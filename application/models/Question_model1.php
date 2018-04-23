<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    //-----------------------------------------SELECT----------------------------------------------------------------


    //----------------------DASHBOARD---------------------------------

    //Get all feedback within limit for dashboard - pagination
    public function getQuestionsLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('question');
        $this->db->where('question.record_status',1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getQuestionTranscriptionLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('question');
        $this->db->where('question.record_status',1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUserQuestionsLimit($limit, $start, $user_id)
    {
        $this->db->limit($limit, $start);
        $this->db->from('question');
        $this->db->where('question.record_status',1);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getQuestion($question_id)
    {
        $this->db->from('question');
        $this->db->where('question_id',$question_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getUserFromQuestion($question_id)
    {
        $this->db->select('user_id');
        $this->db->from('question');
        $this->db->where('question_id',$question_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    //Getting total count of user question
    public function getQuestionTotal()
    {
        $this->db->from('question');
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }

    //Getting total count of user question
    public function getUserQuestionTotal($user_id)
    {
        $this->db->from('question');
        $this->db->where('record_status',1);
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results();
    }

    //Getting question forward total
    public function getForwardQuestionTotal($question_id)
    {
        $this->db->from('forward');
        $this->db->where('file_id', $question_id);
        $this->db->where('type', "Question");
        return $this->db->count_all_results();
    }

    //Getting user question like total
    public function getUserLikeQuestionTotal($question_id)
    {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "like");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }

    //Getting user question dislike total
    public function getUserDisLikeQuestionTotal($question_id)
    {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "dislike");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }

    //Getting user question report total
    public function getUserReportQuestionTotal($question_id)
    {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "report");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }


    //Getting like question total
    public function getDoctorLikeQuestionTotal($question_id)
    {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 1 and answer_id IN (SELECT answer_id from question_answer where question_id = '.$question_id.')');
        return $query->row_array();
    }

    //Getting dislike question total
    public function getDoctorDisLikeQuestionTotal($question_id)
    {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 2 and answer_id IN (SELECT answer_id from question_answer where question_id = '.$question_id.')');
        return $query->row_array();
    }

    //Getting dislike question total
    public function getDoctorReportQuestionTotal($question_id)
    {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 3 and answer_id IN (SELECT answer_id from question_answer where question_id = '.$question_id.')');
        return $query->row_array();
    }

    //Getting question transcription
    public function getQuestionTranscription($question_id)
    {
        $query = $this->db
        ->from('question_transcription')
        ->where('question_id',$question_id)
        ->get();
        return $query->row_array();
    }

    public function getAllFaq(){
        $query = $this->db
            ->from('faq')
            ->get();
        return $query->result_array();
    }

    //-----------------------------------------INSERT----------------------------------------------------------------
    public function insertQuestionTranscription($question_transcription_array){
        $this->db->insert('question_transcription', $question_transcription_array);
    }


    //-----------------------------------------UPDATE----------------------------------------------------------------


    //----------------------DASHBOARD------------------------------------

    public function updateQuestion($question_id, $question_array){
        $this->db->where('question_id', $question_id);
        $this->db->update('question',$question_array);
        return true;
    }

    public function updateQuestionTranscription($question_id, $question_transcription_array){
        $this->db->where('question_id', $question_id);
        $this->db->update('question_transcription',$question_transcription_array);
        return true;
    }
    //----------------------ANDROID------------------------------------

    //getting userid of a question
    public function getUserIdofQuestion($question_id){
        $query = $this->db
            ->select('user_id')
            ->from('question')
            ->where('question_id', $question_id)
            ->get();

        return $query->row_array();
    }

    //Updating answer_check of a question
    public function updateIsAnswered($question_id)
    {
        if($this->db->query('UPDATE question SET answer_status = 1 WHERE question_id ='.$question_id))
        {
            return true;
        }
        return false;
    }
}