<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Story_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-----------------------------------------SELECT----------------------------------------------------------------


    //Get all stories within limit for dashboard - pagination
    public function getStoriesLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('user_story');
        $this->db->where('record_status',1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getStoriesLimitNew($limit, $start) {

       // $query = $this->db->query("SELECT * FROM `user_story` WHERE record_status = 1 and approve_status = 0 and disapproved_reason = '' order by user_story_id DESC limit'= '".$limit."' offset '".$start."' ");
          $query = $this->db->query('SELECT * FROM `user_story` WHERE record_status = 1 and approve_status = 0 and approved_by is NULL order by user_story_id DESC limit  '.$limit.' offset '.$start.' ');
        return $query->result_array();
    }
     public function getStoriesLimitApproved($limit, $start) {

          $query = $this->db->query('SELECT * FROM `user_story` WHERE record_status = 1 and approve_status = 1 and approved_by != "Null" order by user_story_id DESC limit  '.$limit.' offset '.$start.' ');
        return $query->result_array();
    }
    public function getStoriesLimitApprovedOrderBy($limit, $start, $order_by) {


            $query = $this->db->query('SELECT *,

(select count(*) from response where `response`.content_id = `user_story`.user_story_id  and `response`.response = "like" and `response`.response_type= "story") 
as response_like,

(select count(*)  from response where `response`.content_id = `user_story`.user_story_id and `response`.response = "dislike" and `response`.response_type= "story") as response_dislike,

(select count(*)  from response where `response`.content_id = `user_story`.user_story_id and `response`.response = "report" and `response`.response_type= "story")
 as response_inappropriate,

(select count(*) from logs where `logs`.content_id = `user_story`.user_story_id and `logs`.action_id = 89) as no_of_listens,

(select count(*) from comment where `comment`.story_id = `user_story`.user_story_id and `comment`.record_status = 1 and `comment`.approve_status= 1 ) as 
story_comments 

FROM user_story WHERE record_status = 1 and approve_status = 1 and approved_by != "Null" '.$order_by.' limit  '.$limit.' offset '.$start.' ');

        return $query->result_array();
    }
     public function getStoriesLimitRejected($limit, $start) {

          $query = $this->db->query('SELECT * FROM `user_story` WHERE record_status = 1 and approve_status = 2  and approved_by != "Null" order by user_story_id DESC limit  '.$limit.' offset '.$start.' ');
        return $query->result_array();
    }
    public function getApprovedStoryLikes($story_id) {

          $query = $this->db->query('select count(*) as response_like from response where content_id = '.$story_id.' and response = "like" and response_type= "story" ');
        return $query->result_array();
    }
    public function getApprovedStoryLikesOrderBy($story_id,$order_by) {

          $query = $this->db->query('select count(*) as response_like from response where content_id = '.$story_id.' and response = "like" and response_type= "story" '.$order_by.' ');
        return $query->result_array();
    }
     public function getApprovedStoryDislikes($story_id) {

          $query = $this->db->query('select count(*) as response_dislike from response where content_id = '.$story_id.' and response = "dislike" and response_type= "story" ');
        return $query->result_array();
    }
     public function getApprovedStoryComments($story_id) {

          $query = $this->db->query('select count(*) as story_comments from comment where story_id = '.$story_id.' and record_status = 1 and approve_status= 1 ');
        return $query->result_array();
    }
      public function getNoOfListensOfStory($story_id) {

          $query = $this->db->query('select count(*) as no_of_listens from logs where content_id = '.$story_id.' and action_id = 89');
        return $query->result_array();
    }
     public function getNoOfInappropriateStory($story_id) {

          $query = $this->db->query('select count(*) as no_of_inappropriate from response where content_id = '.$story_id.' and response = "report" and response_type= "story" ');
        return $query->result_array();
    }
   



     public function getStoriesTranscriptionLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('user_story');
        $this->db->where('record_status',1);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function getUserStoriesLimit($limit, $start, $user_id)
    {
        $this->db->limit($limit, $start);
        $this->db->from('user_story');
        $this->db->where('record_status',1);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

     public function getUserFromStory($story_id)
    {
        $this->db->select('user_id');
        $this->db->from('user_story');
        $this->db->where('user_story_id',$story_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    //Getting total count of user question
    public function getStoryTotal()
    {
        $this->db->from('user_story');
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }
    public function getStoryTotalNew() {
        $query = $this->db->query('SELECT count(*) as new_total  FROM `user_story` WHERE record_status = 1 and approve_status = 0 and approved_by is NULL ');
        return $query->row_array();
    }
     public function getStoryTotalApproved() {
        $query = $this->db->query('SELECT count(*) as approved_total  FROM `user_story` WHERE record_status = 1 and approve_status = 1 and approved_by != "Null" ');
        return $query->row_array();
    }
     public function getStoryTotalRejected() {
        $query = $this->db->query('SELECT count(*) as rejected_total  FROM `user_story` WHERE record_status = 1 and approve_status = 2 and approved_by != "Null" ');
        return $query->row_array();
    }

    //Getting total count of user question
    public function getUserStoryTotal($user_id)
    {
        $this->db->from('user_story');
        $this->db->where('user_id', $user_id);
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }


    //Get all feedback within limit for dashboard - pagination
    public function getCommentsLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('comment');
        $this->db->where('record_status',1);
        $this->db->order_by('time_recorded', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCommentsTranscriptionLimit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('comment');
        $this->db->where('record_status',1);
        $query = $this->db->get();
        return $query->result_array();
    }


    //Getting total count of user question
    public function getCommentTotal()
    {
        $this->db->from('comment');
        $this->db->where('record_status',1);
        return $this->db->count_all_results();
    }

    //Getting story transcription
    public function getStoryTranscription($user_story_id)
    {
        $query = $this->db
            ->from('story_transcription')
            ->where('user_story_id',$user_story_id)
            ->get();
        return $query->row_array();
    }

    //Getting story transcription
    public function getCommentTranscription($comment_id)
    {
        $query = $this->db
            ->from('comment_story_transcription')
            ->where('comment_id',$comment_id)
            ->get();
        return $query->row_array();
    }
    //-----------------------------------------INSERT----------------------------------------------------------------
    public function insertStoryTranscription($story_transcription_array){
        $this->db->insert('story_transcription', $story_transcription_array);
    }

    public function insertCommentTranscription($comment_transcription_array){
        $this->db->insert('comment_story_transcription', $comment_transcription_array);
    }

    //-----------------------------------------UPDATE----------------------------------------------------------------

    //Updating story
    public function updateStory($story_id,$story_update_array)
    {
        $this->db->where('user_story_id',$story_id);
        $this->db->update('user_story',$story_update_array);
    }
    public function updateStoryTranscription($story_id, $story_transcription_array){

        $this->db->where('user_story_id',$story_id);
        $this->db->update('story_transcription',$story_transcription_array);
    }

    //Updating comment
    public function updateComment($comment_id,$comment_update_array){
        $this->db->where('comment_id',$comment_id);
        $this->db->update('comment',$comment_update_array);
    }


    public function updateCommentTranscription($comment_id, $comment_transcription_array){
        $this->db->where('comment_id', $comment_id);
        $this->db->update('comment_story_transcription',$comment_transcription_array);
        return true;
    }


}