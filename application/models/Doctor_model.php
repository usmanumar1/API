<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //-----------SELECT-----------------
    //Checking whether password matches
    public function selectDoctorByID($doc_id) {
        $query = $this->db
                ->select()
                ->where('doc_id', $doc_id)
                ->get('doctor');

        return $query->row_array();
    }

    public function selectDoctorNew($doc_id) {
        $this->db->from('doctor');
        $this->db->where('doc_id ', $doc_id);
        $this->db->where('isApproved ', 0);
        $this->db->where('doc_reset_code', '');
        $query = $this->db->get();

        return $query->row_array();
    }
public function selectDoctorByResetCode($code) {
        $query = $this->db
                ->select()
                ->where('doc_reset_code', $code)
                ->get('doctor');

        return $query->row_array();
    }

    public function selectDoctorsNewSignup($limit, $start) {

        $this->db->limit($limit, $start);
        $this->db->from('doctor');
        $this->db->where('doc_reset_code', '');
        $this->db->where('isApproved ', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function countDoctorsNewSignup() {
        $this->db->from('doctor');
//         $this->db->where('doc_reset_code ', '');
        $this->db->where('isApproved ', '0');
        return $this->db->count_all_results();
    }

    //Get all feedback within limit for dashboard - pagination
    public function getDoctorsLimit($limit, $start) {
        $this->db->limit($limit, $start);
        $this->db->from('doctor');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Get all chat responses
    public function getChatResponses($limit, $start, $doctor_id) {
        $this->db->limit($limit, $start);
        $this->db->from('chat_response');
        $this->db->where('doctor_id', $doctor_id);
        $this->db->order_by('response_time', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting total count of user question
    public function getChatResponseTotal($doctor_id) {
        $this->db->from('chat_response');
        $this->db->where('doctor_id', $doctor_id);
        return $this->db->count_all_results();
    }

    //Getting total count of user question
    public function getDoctorTotal() {
        $this->db->from('doctor');
        return $this->db->count_all_results();
    }

    public function getDoctorAnswerTotal($doctor_id) {
        $this->db->from('question_answer');
        $this->db->where('doctor_id', $doctor_id);
        return $this->db->count_all_results();
    }

    public function getDoctorAverageTime($doctor_id) {
        $query = $this->db->query('SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(time_answered))) as result FROM question_answer where doctor_id =' . $doctor_id);
        return $query->row_array();
    }

    public function getResponseById($response_id) {
        $query = $this->db
                ->from('chat_response')
                ->where('response_id', $response_id)
                ->get();
        return $query->row_array();
    }

    public function getTimeLastAnswer($doctor_id) {
        $query = $this->db
                ->from('question_answer')
                ->where('doctor_id', $doctor_id)
                ->order_by('time_answered', 'DESC')
                ->limit(1)
                ->get();
        return $query->row_array();
    }

    //--------------------------INSERT----------------------------------
    public function insertChatResponse($response_chat_array) {
        if ($this->db->insert('chat_response', $response_chat_array)) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        return false;
    }

    //-------------------UPDATE---------------------------------------
    public function updateChatResponse($response_id, $response_array) {
        $this->db->where('response_id', $response_id);
        $this->db->update('chat_response', $response_array);
    }

    public function updateMessageReadStatus($doc_id, $read_status_array) {
        $this->db->where('doc_id', $doc_id);
        $this->db->update('doctor', $read_status_array);
    }

    public function updateNewDoctorIsApproved($doctor_id, $doctor_status_array) {
        $this->db->where('doc_id', $doctor_id);
        $this->db->update('doctor', $doctor_status_array);
        return true;
    }
     public function updateDoctorPassword($doc_id,$new_password_array) {
        $this->db->where('doc_id', $doc_id);
        $this->db->update('doctor', $new_password_array);
        return true;
    }
         public function updateDoctorResetCode($doc_id,$remove_reset_code_array) {
        $this->db->where('doc_id', $doc_id);
        $this->db->update('doctor', $remove_reset_code_array);
        return true;
    }

}
