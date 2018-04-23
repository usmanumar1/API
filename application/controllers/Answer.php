<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Answer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('form_validation');
        $this->load->helper(array('audio'));

        $this->load->model('Answer_model');
        $this->load->model('Question_model');
    }
    //Getting all answers
    public function getAllAnswers()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            date_default_timezone_set("Asia/Karachi");

            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $answers = $this->Answer_model->getAnswersLimit($limit, $start);
            $answer_total = $this->Answer_model->getAnswerTotal();

            foreach ($answers as $key => $answer){
                $question_info = $this->Question_model->getQuestion($answer['question_id']);
                $answers[$key]['call_id'] = $question_info['call_id'];
                $answers[$key]['user_id'] = $question_info['user_id'];

                //Getting answer transcription if uploaded
                $answer_transcription = $this->Answer_model->getAnswerTranscriptionUsingAnswer($answer['answer_id']);
                if($answer_transcription){
                    $answers[$key]['answer_transcription'] = 1;
                    $answers[$key]['answer_roman_urdu'] = $answer_transcription['roman_urdu'];
                    $answers[$key]['answer_urdu'] = $answer_transcription['urdu'];
                    $answers[$key]['answer_english'] = $answer_transcription['english'];
                }

                //Getting time difference

                //using time answered
                if($answer['answer_text'] != NULL){
                    $date1 = new DateTime($answer['time_answered']);
                    $date2 = new DateTime($answer['time_approved']);
                    $diff = $date2->diff($date1);

                    $diff =  $diff->format('%a Day and %h hours');
                    $answers[$key]['time_duration'] = $diff;

                }

                //Using time recorded
                else{
                    $date1 = new DateTime($answer['time_trans_uploaded']);
                    $date2 = new DateTime($answer['time_approved']);
                    $diff = $date2->diff($date1);

                    $diff =  $diff->format('%a Day and %h hours');
                    $answers[$key]['time_duration'] = $diff;
                }
            }


            echo json_encode(array("status" => "success","answers" => $answers, "answer_total" => $answer_total));
            return;
        }
    }

    public function uploadAnswerAudio(){
        date_default_timezone_set("Asia/Karachi");

        $answer_id = $this->input->get('answer_id');
        $question_id = $this->input->get('question_id');
        $admin_id = $this->input->get('admin_id');

        //Making time limit infinite
        set_time_limit(0);

        //Validating video and uploading it
        $audio_attributes = uploadAudio($answer_id);
        $audioUploadStatus = $audio_attributes[0];

        //If audioValidation fails, then exit!
        if ($audioUploadStatus == 0) {
            echo $audio_attributes[1];
            return;
        }

        $answer_data = array(
            'approve_status' => 1,
            'time_approved' => date("Y-m-d H:i:s"),
            'approved_by' => $admin_id
        );

        //When voice artist uploads answer, updating answer check in question and answer table
        $this->Answer_model->updateAnswerApprove($answer_id, $answer_data);
        $this->Question_model->updateIsAnswered($question_id);

        $url = "http://127.0.0.1/MVP/Scripts/createAnswerRequest.php?question_id=$question_id&answer_id=$answer_id&type=Q";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        $result = curl_exec($ch);
        curl_close($ch);

        echo "Uploaded";
        return;

    }

    //Updating Answer transcription
    public function saveAnswerTranscription(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            if (isset($_GET["answer_id"]) && isset($_GET["answer_urdu"]) && isset($_GET["answer_roman_urdu"]) && isset($_GET["answer_english"])) {
                date_default_timezone_set("Asia/Karachi");

                $answer_id = $this->input->get("answer_id");
                $answer_urdu = $this->input->get("answer_urdu");
                $answer_roman_urdu = $this->input->get("answer_roman_urdu");
                $answer_english = $this->input->get("answer_english");

                $answer_transcription_array  = array(
                    'urdu' => $answer_urdu,
                    'roman_urdu' => $answer_roman_urdu,
                    'english' => $answer_english,
                );

                //If answer transcription doesn't exit, then insert
                //else update
                $transcription = $this->Answer_model->getAnswerTranscriptionByAnswer($answer_id);
                if($transcription){
                    $this->Answer_model->updateAnswerTranscription($answer_id, $answer_transcription_array);
                }
                else{
                    $answer_transcription_array['answer_id'] = $answer_id;
                    $this->Answer_model->insertAnswerTranscription($answer_transcription_array);
                }

                //Updating answer trans status
                $answer_array = array('trans_upload_status' => 1, "time_trans_uploaded" => date('Y-m-d H:i:s'));
                $this->Answer_model->updateAnswerApprove($answer_id, $answer_array);

                echo "success";
                return;

            }
            echo "error";
            return;
        }
    }
}