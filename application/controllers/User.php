<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('form_validation');
        $this->load->helper(array('form'));

        $this->load->model('Log_model');
        $this->load->model('Question_model');
        $this->load->model('Answer_model');
        $this->load->model('Story_model');
        $this->load->model('Feedback_model');
    }

    //Getting all users
    public function getAllUsers()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $users = $this->Log_model->getUsersLimit($limit, $start);
            $user_total = $this->Log_model->getUserTotal();

            echo json_encode(array("status" => "success","users" => $users, "user_total" => $user_total));
            return;
        }
    }

     public function getUser(){
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $user_id = $this->input->get('user_id');
            $user = array();
            
            $user_questions_total = $this->Log_model->getUserQuestionTotal($user_id);
            $user_feedback_total = $this->Log_model->getUserFeedbackTotal($user_id);
            $user_inappropriate_total = $this->Log_model->getUserInappropriateTotal($user_id);
            $user_calls_total = $this->Log_model->getUserCallTotal($user_id);
            $user_forward_total = $this->Log_model->getUserForwardTotal($user_id);

            $blocked_user = $this->Log_model->getBlockedUser($user_id);
            if($blocked_user){
                if($blocked_user['status'] == 0){
                    $user['status'] = "Active";
                }
                else{
                    $user['status'] = "Blocked";
                }
            }
            else{
                $user['status'] = "Active";
            }

            
            $user['user_id'] = $user_id;
            $user['user_question_total'] = $user_questions_total;
            $user['user_feedback_total'] = $user_feedback_total;
            $user['user_inappropriate_total'] = $user_inappropriate_total;
            $user['user_calls_total'] = $user_calls_total['COUNT(DISTINCT(callid))'];
            $user['user_forward_total'] =$user_forward_total;

            echo json_encode(array("status" => "success","user" => $user));
            return;
        }
    }

    //Getting all user questions
    public function getUserQuestions()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $user_id = $this->input->get('user_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;

            $questions = $this->Question_model->getUserQuestionsLimit($limit, $start, $user_id);
            $question_total = $this->Question_model->getUserQuestionTotal($user_id);

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
            }

            echo json_encode(array("status" => "success","questions" => $questions, "question_total" => $question_total));
            return;
        }
    }

    //Getting all stories of user
    public function getUserStories()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $user_id = $this->input->get('user_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $stories = $this->Story_model->getUserStoriesLimit($limit, $start, $user_id);
            $story_total = $this->Story_model->getUserStoryTotal($user_id);

            foreach ($stories as $key => $story){
                if($story["approve_status"] == 1){
                    $date1 = new DateTime($story['time_recorded']);
                    $date2 = new DateTime($story['time_approved']);
                    $diff = $date2->diff($date1);

                    $diff =  $diff->format('%a Day and %h hours');
                    $stories[$key]['time_duration'] = $diff;
                }
                //Getting story transcriptions if uploaded
                if($story['trans_upload_status'] == 1) {
                    $story_transcription = $this->Story_model->getStoryTranscription($story['user_story_id']);
                    $stories[$key]['story_roman_urdu'] = $story_transcription['roman_urdu'];
                    $stories[$key]['story_english'] = $story_transcription['english'];
                    $stories[$key]['story_urdu'] = $story_transcription['urdu'];

                }
            }


            echo json_encode(array("status" => "success","stories" => $stories, "story_total" => $story_total));
            return;
        }
    }

    //Getting user feedback
    public function getUserFeedback()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $user_id = $this->input->get('user_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $feedbacks = $this->Feedback_model->getUserFeedbacksLimit($limit, $start, $user_id);
            $feedback_total = $this->Feedback_model->getUserFeedbackTotal($user_id);

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

    public function updateUserBlockStatus(){
        if($this->input->server('REQUEST_METHOD') == 'GET') {
            $source_id = $this->input->get('source_id');
            $status = $this->input->get('status');
            $type = $this->input->get('type');

            if($type == "Question"){
                $user = $this->Question_model->getUserFromQuestion($source_id);
            }
            else{
                $user = $this->Story_model->getUserFromStory($source_id);

            }
            $user_id = $user['user_id'];

            $blocked_array = array('status' => $status);

            //checking if exists in blocked user
            $blocked_user = $this->Log_model->getBlockedUser($user_id);


            //update
            if($blocked_user){
                $this->Log_model->updateBlockedUser($user_id, $blocked_array);
                echo json_encode(array("status" => "success"));
                return;
            }
            //insert
            $blocked_array['user_id'] = $user_id;
            $this->Log_model->insertBlockUser($blocked_array);
            echo json_encode(array("status" => "success"));
            return;


        }
    }

}