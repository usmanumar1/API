<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Question_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //-----------------------------------------SELECT----------------------------------------------------------------
    //----------------------DASHBOARD---------------------------------
    //Get all feedback within limit for dashboard - pagination
    public function getQuestionsLimit($limit, $start) {
        $this->db->limit($limit, $start);
        $this->db->from('question');    
        $this->db->where('question.record_status', 1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getQuestionsLimitNew($limit, $start) {

        $query = $this->db->query("SELECT * FROM `question` WHERE record_status = 1 and question_action = 0 and doc_answer_status = 0 limit $limit offset $start ");
        return $query->result_array();
    }

    public function getQuestionsLimitAssigned($limit, $start) {
        $query = $this->db->query("SELECT * FROM `question` WHERE record_status = 1 and question_action = 1 and doc_answer_status = 0 order by time_approved desc limit $limit offset $start ");
        return $query->result_array();
    }

    public function getQuestionsLimitAnswered($limit, $start) {

        $query = $this->db->query("SELECT * FROM `question_answer` as qa inner join doctor as d on qa.doctor_id = d.doc_id inner join question as q on q.question_id = qa.question_id where q.answer_status =0 And q.doc_answer_status = 1 and qa.approve_status = 0 and q.doc_answer_status = 1 order by time_answered desc limit $limit offset $start ");
        return $query->result_array();
    }
     public function getQuestionsLimitUploaded($limit, $start) {

        $query = $this->db->query("SELECT * FROM `question_answer` as qa inner join doctor as d on qa.doctor_id = d.doc_id inner join question as q on q.question_id = qa.question_id where q.answer_status =1 And qa.approve_status = 1 And q.doc_answer_status = 1 group by q.question_id order by qa.time_approved desc limit $limit offset $start  ");
        return $query->result_array();
    }
      public function getQuestionsUploadedTime($limit, $start) {

        $query = $this->db->query("SELECT time_approved, answer_id, question_id,time_answered FROM `question_answer` where approve_status = 1 and faq = 0 ");
        return $query->result_array();
    }
     public function getQuestionsLimitFaq($limit, $start) {

        $query = $this->db->query("SELECT * FROM `question` as q left join question_answer as qa on q.question_id = qa.question_id where q.question_action = 2 And qa.faq !=0 order by q.time_approved desc limit $limit offset $start ");
        return $query->result_array();
    }
         public function getQuestionsLimitIgnoredEtc($limit, $start) {
        $query = $this->db->query("SELECT * FROM `question` where question_action not IN (0,1,2) and record_status=1 order by time_approved desc limit $limit offset $start ");
        return $query->result_array();
    }
     public function getAnsweredQuestions($limit, $start) {
//        $this->db->limit($limit, $start);
//        $this->db->from('question');
//        $this->db->where('question.record_status',1);
//        $this->db->order_by('time_recorded', 'DESC');

        $query = $this->db->query("SELECT * FROM `question` WHERE record_status = 1 and answer_status = 1 limit $limit offset $start ");
        return $query->result_array();
    }

    public function getQuestionTranscriptionLimit($limit, $start) {
        $this->db->limit($limit, $start);
        $this->db->from('question');
        $this->db->where('question.record_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUserQuestionsLimit($limit, $start, $user_id) {
        $this->db->limit($limit, $start);
        $this->db->from('question');
        $this->db->where('question.record_status', 1);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getQuestion($question_id) {
        $this->db->from('question');
        $this->db->where('question_id', $question_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getUserFromQuestion($question_id) {
        $this->db->select('user_id');
        $this->db->from('question');
        $this->db->where('question_id', $question_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    //Getting total count of user question
    public function getQuestionTotal() {
        $this->db->from('question');
        $this->db->where('record_status', 1);
        return $this->db->count_all_results();
    }

    public function getQuestionTotalNew() {
        $query = $this->db->query('SELECT count(*) as new_total  FROM `question` WHERE record_status = 1 and question_action = 0');
        return $query->row_array();
    }
    public function getTotalFaqAssigned() {
        $query = $this->db->query('select faq, count(*) as faqCount FROM question_answer where faq != 0 GROUP BY faq');
        return $query->result_array();
    }

    public function getQuestionTotalAssigned() {
        $query = $this->db->query('SELECT count(*) as assigned_total  FROM `question` WHERE record_status = 1 and question_action = 1 and doc_answer_status = 0');
        return $query->row_array();
    }

    public function getQuestionTotalAnswered() {
        $query = $this->db->query('SELECT count(*) as answered_total FROM `question_answer` as qa inner join doctor as d on qa.doctor_id = d.doc_id inner join question as q on q.question_id = qa.question_id where  q.record_status = 1 and  q.approve_status = 1 and  q.answer_status = 0 And q.doc_answer_status = 1 and qa.approve_status = 0
');
        return $query->row_array();
    }
     public function getQuestionTotalUploaded() {
        $query = $this->db->query('SELECT count(*) as uploaded_total FROM `question_answer` as qa inner join doctor as d on qa.doctor_id = d.doc_id inner join question as q on q.question_id = qa.question_id where q.answer_status =1 And qa.approve_status = 1 and q.doc_answer_status=1');
        return $query->row_array();
    }
     public function getQuestionTotalFaq() {
        $query = $this->db->query('SELECT count(*) as faq_total FROM `question` as q left join question_answer as qa on q.question_id = qa.question_id where q.question_action = 2 And qa.faq != 0');
        return $query->row_array();
    }
  public function getQuestionTotalIgnoredEtc() {
        $query = $this->db->query('SELECT count(*) as ignoredEtc_total FROM `question` where question_action not IN (0,1,2) and record_status=1');
        return $query->row_array();
    }
    //Getting total count of user question
    public function getUserQuestionTotal($user_id) {
        $this->db->from('question');
        $this->db->where('record_status', 1);
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results();
    }

    //Getting question forward total
    public function getForwardQuestionTotal($question_id) {
        $this->db->from('forward');
        $this->db->where('file_id', $question_id);
        $this->db->where('type', "Question");
        return $this->db->count_all_results();
    }

    //Getting user question like total
    public function getUserLikeQuestionTotal($question_id) {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "like");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }

    //Getting user question dislike total
    public function getUserDisLikeQuestionTotal($question_id) {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "dislike");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }

    //Getting user question report total
    public function getUserReportQuestionTotal($question_id) {
        $this->db->from('response');
        $this->db->where('content_id', $question_id);
        $this->db->where('response', "report");
        $this->db->where('response_type', "question");
        return $this->db->count_all_results();
    }

    //Getting like question total
    public function getDoctorLikeQuestionTotal($question_id) {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 1 and answer_id IN (SELECT answer_id from question_answer where question_id = ' . $question_id . ')');
        return $query->row_array();
    }

    //Getting dislike question total
    public function getDoctorDisLikeQuestionTotal($question_id) {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 2 and answer_id IN (SELECT answer_id from question_answer where question_id = ' . $question_id . ')');
        return $query->row_array();
    }

    //Getting dislike question total
    public function getDoctorReportQuestionTotal($question_id) {
        $query = $this->db->query('SELECT COUNT(*) from doc_answer_feedback where response_type = 3 and answer_id IN (SELECT answer_id from question_answer where question_id = ' . $question_id . ')');
        return $query->row_array();
    }

    //Getting question transcription
    public function getQuestionTranscription($question_id) {
        $query = $this->db
                ->from('question_transcription')
                ->where('question_id', $question_id)
                ->get();
        return $query->row_array();
    }

    public function getAllFaq() {
        $query = $this->db
                ->from('faq')
                ->get();
        return $query->result_array();
    }

    //-----------------------------------------INSERT----------------------------------------------------------------
    public function insertQuestionTranscription($question_transcription_array) {
        $this->db->insert('question_transcription', $question_transcription_array);
    }

    //-----------------------------------------UPDATE----------------------------------------------------------------
    //----------------------DASHBOARD------------------------------------

    public function updateQuestion($question_id, $question_array) {
        $this->db->where('question_id', $question_id);
        $this->db->update('question', $question_array);
        return true;
    }

    public function updateQuestionTranscription($question_id, $question_transcription_array) {
        $this->db->where('question_id', $question_id);
        $this->db->update('question_transcription', $question_transcription_array);
        return true;
    }

    //----------------------ANDROID------------------------------------
    //Updating answer_check of a question
    public function updateIsAnswered($question_id) {
        if ($this->db->query('UPDATE question SET answer_status = 1 WHERE question_id =' . $question_id)) {
            return true;
        }
        return false;
    }

}
