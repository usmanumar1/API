<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->model('Feedback_model');
    }

    //Getting all feedback
    public function getAllFeedback()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $feedbacks = $this->Feedback_model->getFeedbacksLimit($limit, $start);
            $feedback_total = $this->Feedback_model->getFeedbackTotal();

            foreach ($feedbacks as $key => $feedback){
                //Getting feedback transcriptions if uploaded
                if($feedback['trans_upload_status'] == 1) {
                    $feedback_transcription = $this->Feedback_model->getFeedbackTranscription($feedback['feedback_id']);
                    $feedbacks[$key]['feedback_roman_urdu'] = $feedback_transcription['roman_urdu'];
                    $feedbacks[$key]['feedback_english'] = $feedback_transcription['english'];
                    $feedbacks[$key]['feedback_urdu'] = $feedback_transcription['urdu'];
                }
            }


            echo json_encode(array("status" => "success","feedbacks" => $feedbacks, "feedback_total" => $feedback_total));
            return;
        }
    }

    //Getting all stories with transcription
    public function getAllFeedbackTranscription()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;

            $feedbacks = $this->Feedback_model->getFeedbacksTranscriptionLimit($limit, $start);
            $feedback_total = $this->Feedback_model->getFeedbackTotal();

            foreach ($feedbacks as $key => $feedback){
                //Getting feedback transcriptions if uploaded
                if($feedback['trans_upload_status'] == 1) {
                    $feedback_transcription = $this->Feedback_model->getFeedbackTranscription($feedback['feedback_id']);
                    $feedbacks[$key]['feedback_roman_urdu'] = $feedback_transcription['roman_urdu'];
                    $feedbacks[$key]['feedback_english'] = $feedback_transcription['english'];
                    $feedbacks[$key]['feedback_urdu'] = $feedback_transcription['urdu'];

                    $date1 = new DateTime($feedback['time_recorded']);
                    $date2 = new DateTime($feedback['time_trans_uploaded']);
                    $diff = $date2->diff($date1);

                    $diff = $diff->format('%a Day and %h hours');
                    $feedbacks[$key]['feedback_duration'] = $diff;

                }
            }

            echo json_encode(array("status" => "success","feedbacks" => $feedbacks, "feedback_total" => $feedback_total));
            return;
        }
    }

    //Updating feedback transcription
    public function saveFeedbackTranscription(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            if (isset($_GET["feedback_id"]) && isset($_GET["feedback_urdu"]) && isset($_GET["feedback_roman_urdu"]) && isset($_GET["feedback_english"])) {
                date_default_timezone_set("Asia/Karachi");

                $feedback_id = $this->input->get("feedback_id");
                $feedback_urdu = $this->input->get("feedback_urdu");
                $feedback_roman_urdu = $this->input->get("feedback_roman_urdu");
                $feedback_english = $this->input->get("feedback_english");

                $feedback_transcription_array  = array(
                    'urdu' => $feedback_urdu,
                    'roman_urdu' => $feedback_roman_urdu,
                    'english' => $feedback_english,
                );

                //If feedback transcription doesn't exit, then insert
                //else update
                $transcription = $this->Feedback_model->getFeedbackTranscription($feedback_id);
                if($transcription){
                    $this->Feedback_model->updateFeedbackTranscription($feedback_id, $feedback_transcription_array);
                    echo "success";
                    return;
                }
                else{
                    $feedback_transcription_array['feedback_id'] = $feedback_id;
                    $this->Feedback_model->insertFeedbackTranscription($feedback_transcription_array);
                    //Updating feedback trans status
                    $feedback_array = array('trans_upload_status' => 1, "time_trans_uploaded" => date('Y-m-d H:i:s'));
                    $this->Feedback_model->updateFeedback($feedback_id, $feedback_array);

                    echo "success";
                    return;
                }



            }
            echo "error";
            return;
        }
    }

    public function updateFeedbackStatus(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $feedback_id = $this->input->get('feedback_id');
            $status = $this->input->get('approved');
            $feedback_array = array('status' => $status);
            if($this->Feedback_model->updateFeedback($feedback_id, $feedback_array)){
                echo "success";
                return;
            }
            echo "error";

        }
    }

}