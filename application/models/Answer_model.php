<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Answer_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-----------------------------------------SELECT----------------------------------------------------------------


    //----------------------DASHBOARD---------------------------------


    //getting answer of a question
    public function getAnswerOfQuestion($question_id){
        $query = $this->db
            ->from('question_answer')
            ->where('question_id', $question_id)
            ->get();

        return $query->row_array();
    }

    //Getting doctor recording - if doctor answered by audio
    public function getDoctorRecording($question_id){
        $query = $this->db
            ->select('answer_id')
            ->from('question_answer')
            ->where('question_id', $question_id)
            ->where('faq', 0)
            ->where('answer_text', null)
            ->get();

        return $query->row_array();

    }

    //Getting doctor answer - text
    public function getDoctorAnswerText($question_id){
        $query = $this->db
            ->select('answer_text')
            ->from('question_answer')
            ->where('question_id', $question_id)
            ->get();

        return $query->row_array();

    }

    public function getDoctorName($question_id){
        $query = $this->db->query('SELECT doctor.doc_fname from doctor where doctor.doc_id IN (Select doctor_id from question_answer where question_id ='. $question_id . ')');
        return $query->row_array();
    }

    public function getAnswerTranscription($question_id){
        $query = $this->db->query('SELECT urdu,english,roman_urdu from answer_transcription where answer_id IN (Select answer_id from question_answer where question_id ='. $question_id.' AND trans_upload_status =1)');
        return $query->row_array();
    }

    public function getAnswerTranscriptionUsingAnswer($answer_id){
        $query = $this->db->query('SELECT urdu,english,roman_urdu from answer_transcription where answer_id = ' . $answer_id);
        return $query->row_array();
    }

    //Getting FAQ file id
    public function getFaqFile($question_id){
        $query = $this->db
            ->select('faq')
            ->from('question_answer')
            ->where('question_id', $question_id)
            ->get();
        return $query->row_array();
    }

    //getting answers
    public function getAnswersLimit($limit, $start){
        $query = $this->db
            ->limit($limit, $start)
            ->from('question_answer')
            ->where('doctor_id !=', 0)
            ->order_by('time_answered', 'DESC')
            ->get();

        return $query->result_array();
    }

    //Getting total count of user question
    public function getAnswerTotal()
    {
        $this->db->from('question_answer');
        $this->db->where('doctor_id !=',0);
        return $this->db->count_all_results();
    }

    //Getting answer transcription
    public function getAnswerTranscriptionByAnswer($answer_id)
    {
        $query = $this->db
            ->from('answer_transcription')
            ->where('answer_id',$answer_id)
            ->get();
        return $query->row_array();
    }

    //----------------------ANDROID------------------------------------

    public function getDoctorResponse($doctor_id, $answer_id)
    {
        $query = $this->db
            ->from('doc_answer_feedback')
            ->where('doctor_id', $doctor_id)
            ->where('answer_id', $answer_id)
            ->get();

        return $query->row_array();
    }

    public function getAnswerText($answer_id)
    {
        $query = $this->db
            ->select('answer_text')
            ->from('question_answer')
            ->where('answer_id', $answer_id)
            ->get();

        return $query->row_array();
    }

    //-----------------------------------------INSERT---------------------------------------------------------------


    //----------------------DASHBOARD---------------------------------

    //----------------------ANDROID------------------------------------
    public function insertAnswer($answer_data)
    {
        if($this->db->insert('question_answer', $answer_data))
        {
            return $this->db->insert_id();
        }
        return false;
    }

    public function insertDoctorResponse($response_data)
    {
        if($this->db->insert('doc_answer_feedback', $response_data))
        {
            return true;
        }
        return false;
    }

    public function insertAnswerTranscription($answer_transcription_array){
        $this->db->insert('answer_transcription', $answer_transcription_array);
    }

    //-----------------------------------------UPDATE----------------------------------------------------------------


    //----------------------DASHBOARD---------------------------------

    public function updateAnswer($question_id, $answer_array){
        $this->db->where('question_id', $question_id);
        $this->db->update('question_answer', $answer_array);
        return true;
    }

    public function updateAnswerApprove($answer_id, $answer_array)
    {
        $this->db->where('answer_id',$answer_id);
        $this->db->update('question_answer',$answer_array);
        return true;
    }

    public function updateAnswerTranscription($answer_id, $answer_transcription_array){
        $this->db->where('answer_id', $answer_id);
        $this->db->update('answer_transcription',$answer_transcription_array);
        return true;
    }

    //----------------------ANDROID------------------------------------
    public function updateDoctorResponse($doctor_id, $answer_id, $response_array)
    {
        $this->db->where('doctor_id',$doctor_id);
        $this->db->where('answer_id',$answer_id);
        $this->db->update('doc_answer_feedback',$response_array);
        return true;
    }
}