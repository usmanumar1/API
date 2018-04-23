<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getUsersLimit($limit, $start){
        $this->db->limit($limit, $start);
        $this->db->distinct('functionlog.userid');
        $this->db->select('functionlog.userid');
        $this->db->from('functionlog');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting total count of users
    public function getUserTotal()
    {
        $this->db->from('functionlog');
        $this->db->group_by('functionlog.userid');
        return $this->db->count_all_results();
    }

    public function getUserQuestionTotal($user_id){
        $this->db->from('question');
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results();
    }

    public function getUserFeedbackTotal($user_id){
        $this->db->from('main_feedback');
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results();
    }

    public function getUserForwardTotal($user_id){
        $this->db->from('forward');
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results();
    }


    public function getUserInappropriateTotal($user_id){
        $this->db->from('question');
        $this->db->where('user_id', $user_id);
        $this->db->where('question_action', 5);
        return $this->db->count_all_results();
    }

    public function getUserCallTotal($user_id){
       $query = $this->db->query('SELECT COUNT(DISTINCT(callid)) FROM `functionlog` where userid = '.$user_id);
        return $query->row_array();
    }

    public function getBlockedUser($user_id){
        $query = $this->db
            ->where('user_id', $user_id)
            ->get('blocked_user');
        return $query->row_array();
    }

    public function updateBlockedUser($user_id, $blocked_array){
        $this->db->where('user_id', $user_id);
        $this->db->update('blocked_user',$blocked_array);
        return true;
    }

    public function insertBlockUser($blocked_array){
        $this->db->insert('blocked_user', $blocked_array);
    }

}