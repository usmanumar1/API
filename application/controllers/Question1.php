<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('form_validation');
        $this->load->helper(array('form'));

        $this->load->model('Question_model');
        $this->load->model('Answer_model');
        $this->load->model('Log_model');
    }

    //-------------------------------------------------DASHBOARD------------------------------------------------------//
    //Getting all questions
    public function getAllQuestions()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $questions = $this->Question_model->getQuestionsLimit($limit, $start);
            $question_total = $this->Question_model->getQuestionTotal();

            //Getting all FAQ from table
            $faq = $this->Question_model->getAllFaq();

            foreach ($questions as $key => $question)
            {
                //Getting answer information if it was a new question
                if($question['question_action'] == 1){
                    //If new question was answered
                    if($question['doc_answer_status'] == 1){
                        //If doctor answered by audio, then getting the recording
                        $answer_file_id = $this->Answer_model->getDoctorRecording($question['question_id']);
                        if($answer_file_id){
                            $questions[$key]['answer_id'] = $answer_file_id['answer_id'];
                        }

                        //Getting doctor name
                        $doctor_name = $this->Answer_model->getDoctorName($question['question_id']);
                        $questions[$key]['doctor_name'] = $doctor_name['doc_fname'];

                        //Getting answer transcription if uploaded
                        $answer_transcription = $this->Answer_model->getAnswerTranscription($question['question_id']);
                        if($answer_transcription){
                            $questions[$key]['answer_transcription'] = 1;
                            $questions[$key]['answer_roman_urdu'] = $answer_transcription['roman_urdu'];
                            $questions[$key]['answer_urdu'] = $answer_transcription['urdu'];
                            $questions[$key]['answer_english'] = $answer_transcription['english'];
                        }

                    }
                }

                //Getting FAQ wav file if a FAQ
                if($question['question_action'] == 2){
                    $faq_file = $this->Answer_model->getFaqFile($question['question_id']);
                    $questions[$key]['faq'] = $faq_file['faq'];
                }

                //Getting question transcriptions if uploaded
                if($question['trans_upload_status'] == 1){
                    $question_transcription = $this->Question_model->getQuestionTranscription($question['question_id']);
                    $questions[$key]['question_roman_urdu'] = $question_transcription['roman_urdu'];
                    $questions[$key]['question_english'] = $question_transcription['english'];
                    $questions[$key]['question_urdu'] = $question_transcription['urdu'];
                }

                //Getting timing if question was answered
                if($question['answer_status'] == 1){
                    //getting time when answer was approved
                    $answer = $this->Answer_model->getAnswerOfQuestion($question['question_id']);
                    $questions[$key]['time_approved_answer'] = $answer['time_approved'];

                    $date1 = new DateTime($question['time_recorded']);
                    $date2 = new DateTime($answer['time_approved']);
                    $diff = $date2->diff($date1);

                    $diff =  $diff->format('%a Day and %h hours');
                    $questions[$key]['time_duration'] = $diff;

                }

                 //Getting count
                $forward_count = $this->Question_model->getForwardQuestionTotal($question['question_id']);
                $doctor_like_count = $this->Question_model->getDoctorLikeQuestionTotal($question['question_id']);
                $doctor_dislike_count = $this->Question_model->getDoctorDisLikeQuestionTotal($question['question_id']);
                $doctor_report_count = $this->Question_model->getDoctorReportQuestionTotal($question['question_id']);
                $user_like_count = $this->Question_model->getUserLikeQuestionTotal($question['question_id']);
                $user_dislike_count = $this->Question_model->getUserDisLikeQuestionTotal($question['question_id']);
                $user_report_count = $this->Question_model->getUserReportQuestionTotal($question['question_id']);


                $questions[$key]['forward_count'] = $forward_count;
                $questions[$key]['doctor_like_count'] = $doctor_like_count['COUNT(*)'];
                $questions[$key]['doctor_dislike_count'] = $doctor_dislike_count['COUNT(*)'];
                $questions[$key]['doctor_report_count'] = $doctor_report_count['COUNT(*)'];
                $questions[$key]['user_like_count'] = $user_like_count;
                $questions[$key]['user_dislike_count'] = $user_dislike_count;
                $questions[$key]['user_report_count'] = $user_report_count;

                //Getting block user status
                $blocked_user = $this->Log_model->getBlockedUser($question['user_id']);
                if($blocked_user){
                    if($blocked_user['status'] == 0){
                        $questions[$key]['block_status'] = 0;
                    }
                    else{
                        $questions[$key]['block_status'] = 1;
                    }
                }
                else{
                    $questions[$key]['block_status'] = 0;

                }
            }

            echo json_encode(array("status" => "success","questions" => $questions, "question_total" => $question_total, "faq" => $faq));
            return;
        }
    }

    //Getting all questions with transcription
    public function getAllQuestionsTranscription()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $questions = $this->Question_model->getQuestionTranscriptionLimit($limit, $start);
            $question_total = $this->Question_model->getQuestionTotal();

            foreach ($questions as $key => $question)
            {
                //Getting answer information if it was a new question
                if($question['question_action'] == 1){
                    //If new question was answered
                    if($question['doc_answer_status'] == 1){
                        //Getting answer if of each question
                        $answer_question = $this->Answer_model->getAnswerOfQuestion($question['question_id']);
                        $questions[$key]['answer_id'] = $answer_question['answer_id'];

                        if($answer_question['answer_text'] == NULL){
                            $questions[$key]['answer_file_id'] = $answer_question['answer_id'];
                        }
                        else{
                            $questions[$key]['answer_text'] = $answer_question['answer_text'];
                        }

                        //Getting answer transcription if uploaded
                        $answer_transcription = $this->Answer_model->getAnswerTranscription($question['question_id']);
                        if($answer_transcription){
                            $questions[$key]['answer_transcription'] = 1;
                            $questions[$key]['time_transcription'] = $answer_question['time_trans_uploaded'];
                            $questions[$key]['answer_roman_urdu'] = $answer_transcription['roman_urdu'];
                            $questions[$key]['answer_urdu'] = $answer_transcription['urdu'];
                            $questions[$key]['answer_english'] = $answer_transcription['english'];

                            $date1 = new DateTime($answer_question['time_answered']);
                            $date2 = new DateTime($answer_question['time_trans_uploaded']);
                            $diff = $date2->diff($date1);

                            $diff =  $diff->format('%a Day and %h hours');
                            $questions[$key]['answer_duration'] = $diff;
                        }
                    }
                }

                //Getting question transcriptions if uploaded
                if($question['trans_upload_status'] == 1){
                    $question_transcription = $this->Question_model->getQuestionTranscription($question['question_id']);
                    $questions[$key]['question_roman_urdu'] = $question_transcription['roman_urdu'];
                    $questions[$key]['question_english'] = $question_transcription['english'];
                    $questions[$key]['question_urdu'] = $question_transcription['urdu'];

                    $date1 = new DateTime($question['time_recorded']);
                    $date2 = new DateTime($question['time_trans_uploaded']);
                    $diff = $date2->diff($date1);

                    $diff =  $diff->format('%a Day and %h hours');
                    $questions[$key]['question_duration'] = $diff;
                }

            }

            echo json_encode(array("status" => "success","questions" => $questions, "question_total" => $question_total));
            return;
        }
    }


    //Update status of question
    public function updateQuestionStatus()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            date_default_timezone_set("Asia/Karachi");

            $question_id = $this->input->get('question_id');
            $question_action = $this->input->get('question_action');
            $admin_id = $this->input->get('admin_id');

            $question_action = rtrim($question_action);

            $question_action_id = 0;
            if($question_action == 'New Question') {
                $question_action_id = 1;
            }
            elseif($question_action == 'FAQ') {
                $question_action_id = 2;
            }
            elseif($question_action == "Ignore") {
                $question_action_id = 3;
            }
            elseif($question_action == "Irrelevant") {
                $question_action_id = 4;
            }
            elseif($question_action == "Inappropriate") {
                $question_action_id = 5;
            }
            elseif($question_action == "Inaudible") {
                $question_action_id = 6;
            }
            elseif($question_action == "Unresponsive") {
                $question_action_id = 7;
            }
            elseif($question_action == "Please Select:") {
                echo "error";
                return;
            }

            //Only getting question question type and public status in case of new Question
            if($question_action_id == 1){
                $question_type= $this->input->get('question_type');
                $question_public = $this->input->get('question_public');
            }
            else{
                $question_type = 0;
                $question_public = 0;
            }

            $question_update_array = array(
                'question_action' => $question_action_id,
                'question_type' => $question_type,
                'approved_by' => $admin_id,
                'approve_status' => 1,
                'time_approved' => date('Y:m:d H:i:s'),
                'question_public_admin' => $question_public
            );

            if($this->Question_model->updateQuestion($question_id, $question_update_array))
            {
                //If question = faq,or irrelevant etc then create new answer row
                if($question_action_id != 1){
                    $answer_data = array(
                        'question_id' => $question_id,
                        'approve_status' => 1,
                        'time_approved' => date("Y-m-d H:i:s")
                    );

                    if($question_action_id == 2){
                        $faq_file = $this->input->get('faq_file');
                        $answer_data['faq'] = $faq_file;
                    }

                    //Checking whether answer exists
                    //If exists, then update. else insert
                    $answer = $this->Answer_model->getAnswerOfQuestion($question_id);
                    if($answer){
                        //updating answer row
                        $this->Answer_model->updateAnswer($question_id,$answer_data);
                    }
                    else{
                        //Creating new answer row for FAQ
                        $this->Answer_model->insertAnswer($answer_data);
                    }

                    //Updating answer_check in question table
                    $this->Question_model->updateIsAnswered($question_id);
                    $url = "";
                    if ($question_action_id == 2) {
                        $faq_file = $this->input->get('faq_file');
                        $url = "http://127.0.0.1/MVP/Scripts/createAnswerRequest.php?question_id=$question_id&answer_id=$faq_file&type=FAQ";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                        $result = curl_exec($ch);
                        curl_close($ch);
                    } else if ($question_action_id == 6) {
                        $url = "http://127.0.0.1/MVP/Scripts/createAnswerRequest.php?question_id=$question_id&answer_id=-1&type=Inaudible";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                        $result = curl_exec($ch);
                        curl_close($ch);
                    }

                }

                echo $url;
                return;
            }
            echo "error";
            return;
        }
    }

    //updating question note
    public function saveQuestionNote(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            if (isset($_GET["question_id"]) && isset($_GET["question_note"])) {
                $question_id = $this->input->get("question_id");
                $question_note = $this->input->get("question_note");

                $this->Question_model->updateQuestion($question_id, array("question_note" => $question_note));
                echo "success";
                return;

            }
            echo "error";
            return;
        }

    }

    //Updating question transcription
    public function saveQuestionTranscription(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            if (isset($_GET["question_id"]) && isset($_GET["question_urdu"]) && isset($_GET["question_roman_urdu"]) && isset($_GET["question_english"])) {
                date_default_timezone_set("Asia/Karachi");

                $question_id = $this->input->get("question_id");
                $question_urdu = $this->input->get("question_urdu");
                $question_roman_urdu = $this->input->get("question_roman_urdu");
                $question_english = $this->input->get("question_english");

                $question_transcription_array  = array(
                    'urdu' => $question_urdu,
                    'roman_urdu' => $question_roman_urdu,
                    'english' => $question_english,
                );

                //If question transcription doesn't exit, then insert
                //else update
                $transcription = $this->Question_model->getQuestionTranscription($question_id);
                if($transcription){
                    $this->Question_model->updateQuestionTranscription($question_id, $question_transcription_array);
                }
                else{
                    $question_transcription_array['question_id'] = $question_id;
                    $this->Question_model->insertQuestionTranscription($question_transcription_array);
                }

                //Updating question trans status
                $question_array = array('trans_upload_status' => 1, "time_trans_uploaded" => date('Y-m-d H:i:s'));
                $this->Question_model->updateQuestion($question_id, $question_array);

                echo "success";
                return;

            }
            echo "error";
            return;
        }
    }

    

}