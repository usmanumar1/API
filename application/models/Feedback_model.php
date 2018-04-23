<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-----------------------------------------SELECT----------------------------------------------------------------


    //Get all feedback within limit for dashboard - pagination
    public function getFeedbacksLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('main_feedback');
        $this->db->where('record_status',1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

     public function getFeedbacksTranscriptionLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('main_feedback');
        $this->db->where('record_status',1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUserFeedbacksLimit($limit, $start, $user_id)
    {
        $this->db->limit($limit, $start);
        $this->db->from('main_feedback');
        $this->db->where('user_id',$user_id);
        $this->db->where('record_status',1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting total count of feedback
    public function getFeedbackTotal()
    {
        $this->db->from('main_feedback');
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }

    //Getting total count of feedback
    public function getUserFeedbackTotal($user_id)
    {
        $this->db->from('main_feedback');
        $this->db->where('user_id', $user_id);
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }


    //Getting story transcription
    public function getFeedbackTranscription($feedback_id)
    {
        $query = $this->db
            ->from('feedback_transcription')
            ->where('feedback_id',$feedback_id)
            ->get();
        return $query->row_array();
    }

    //-----------------------------------------INSERT----------------------------------------------------------------
    public function insertFeedbackTranscription($feedback_transcription_array){
        $this->db->insert('feedback_transcription', $feedback_transcription_array);
    }


    //-----------------------------------------UPDATE---------------------------------------------------------------
    public function updateFeedback($feedback_id, $feedback_array){

        $this->db->where('feedback_id',$feedback_id);
        $this->db->update('main_feedback',$feedback_array);
    }

    public function updateFeedbackTranscription($feedback_id, $feedback_transcription_array){

        $this->db->where('feedback_id',$feedback_id);
        $this->db->update('feedback_transcription',$feedback_transcription_array);
    }

}