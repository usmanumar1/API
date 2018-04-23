<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Story extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('form_validation');
        $this->load->helper(array('form'));

        $this->load->model('Story_model');
        $this->load->model('Log_model');
    }

    //Getting all stories
    public function getAllStories() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $stories = $this->Story_model->getStoriesLimit($limit, $start);
            $story_total = $this->Story_model->getStoryTotal();

            foreach ($stories as $key => $story) {
                if ($story["approve_status"] == 1) {
                    $date1 = new DateTime($story['time_recorded']);
                    $date2 = new DateTime($story['time_approved']);
                    $diff = $date2->diff($date1);

                    $diff = $diff->format('%a Day and %h hours');
                    $stories[$key]['time_duration'] = $diff;
                }
                //Getting story transcriptions if uploaded
                if ($story['trans_upload_status'] == 1) {
                    $story_transcription = $this->Story_model->getStoryTranscription($story['user_story_id']);
                    $stories[$key]['story_roman_urdu'] = $story_transcription['roman_urdu'];
                    $stories[$key]['story_english'] = $story_transcription['english'];
                    $stories[$key]['story_urdu'] = $story_transcription['urdu'];
                }

                //Getting block user status
                $blocked_user = $this->Log_model->getBlockedUser($story['user_id']);
                if ($blocked_user) {
                    if ($blocked_user['status'] == 0) {
                        $stories[$key]['block_status'] = 0;
                    } else {
                        $stories[$key]['block_status'] = 1;
                    }
                } else {
                    $stories[$key]['block_status'] = 0;
                }
            }


            echo json_encode(array("status" => "success", "stories" => $stories, "story_total" => $story_total));
            return;
        }
    }

    public function getAllStoriesNew() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $stories = $this->Story_model->getStoriesLimitNew($limit, $start);
            $story_total = $this->Story_model->getStoryTotalNew();
            $story_approved_total = $this->Story_model->getStoryTotalApproved();
            $story_rejected_total = $this->Story_model->getStoryTotalRejected();
        }

        echo json_encode(array("status" => "success", "stories" => $stories,
            "story_total" => $story_total['new_total'],
            "story_approved_total" => $story_approved_total['approved_total'],
            "story_rejected_total" => $story_rejected_total['rejected_total']));

        return;
    }

    public function getAllStoriesApproved() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $order_by = $this->input->get('order_by');
            $stories = $this->Story_model->getStoriesLimitApproved($limit, $start);
            $story_total = $this->Story_model->getStoryTotalNew();

            if (isset($order_by) === "order by time_approved asc" || "order by time_approved desc"
                    || "order by response_like asc"|| "order by response_like desc"
                    || "order by response_dislike asc"|| "order by response_dislike desc"
                    || "order by response_inappropriate asc"|| "order by response_inappropriate desc"
                    || "order by no_of_listens asc"|| "order by no_of_listens desc"
                    || "order by story_comments  asc"|| "order by story_comments  desc"
                    ) 
                
            {

                $stories = $this->Story_model->getStoriesLimitApprovedOrderBy($limit, $start, $order_by);
            }


            foreach ($stories as $key => $story) {

                $story_likes = $this->Story_model->getApprovedStoryLikes($story['user_story_id']);
                $story_dislikes = $this->Story_model->getApprovedStoryDislikes($story['user_story_id']);
                $no_of_comments = $this->Story_model->getApprovedStorycomments($story['user_story_id']);
                $no_of_listens = $this->Story_model->getNoOfListensOfStory($story['user_story_id']);
                $no_of_inappropriate = $this->Story_model->getNoOfInappropriateStory($story['user_story_id']);
                //print_r($story_likes);
                if ($story_likes) {
                    $stories[$key]['story_like'] = $story_likes[0]['response_like'];
                }
                if ($story_dislikes) {
                    $stories[$key]['story_dislike'] = $story_dislikes[0]['response_dislike'];
                }
                if ($no_of_comments) {
                    $stories[$key]['story_comments'] = $no_of_comments[0]['story_comments'];
                }
                if ($no_of_listens) {
                    $stories[$key]['no_of_listens'] = $no_of_listens[0]['no_of_listens'];
                }
                if ($no_of_inappropriate) {
                    $stories[$key]['no_of_inappropriate'] = $no_of_inappropriate[0]['no_of_inappropriate'];
                }
            }



            $story_approved_total = $this->Story_model->getStoryTotalApproved();
            $story_rejected_total = $this->Story_model->getStoryTotalRejected();
        }


        echo json_encode(array("status" => "success", "stories" => $stories,
            "story_total" => $story_total['new_total'],
            "story_approved_total" => $story_approved_total['approved_total'],
            "story_rejected_total" => $story_rejected_total['rejected_total']));
        return;
    }

    public function getAllStoriesRejected() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $stories = $this->Story_model->getStoriesLimitRejected($limit, $start);
            $story_total = $this->Story_model->getStoryTotalNew();
            $story_approved_total = $this->Story_model->getStoryTotalApproved();
            $story_rejected_total = $this->Story_model->getStoryTotalRejected();
        }


        echo json_encode(array("status" => "success", "stories" => $stories,
            "story_total" => $story_total['new_total'],
            "story_approved_total" => $story_approved_total['approved_total'],
            "story_rejected_total" => $story_rejected_total['rejected_total']));
//            "questionIgnoreEtc_total" => $questionIgnoreEtc_total['ignoredEtc_total'],
//            "questionFaq_total" => $questionFaq_total['faq_total'], "questionUploaded_total" => $questionUploaded_total['uploaded_total'],
//            "questionAnswered_total" => $questionAnswered_total['answered_total'], "questionAssigned_total" => $questionAssigned_total['assigned_total'], "faq" => $faq));
        return;
    }

    //Update status of story
    public function updateStoryStatus() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            date_default_timezone_set("Asia/Karachi");

            $story_id = $this->input->get('story_id');

            $story_status = $this->input->get('story_status');
            $admin_id = $this->input->get('admin_id');
            $tag = $this->input->get('tag');

            $story_status = rtrim($story_status);

            $story_status_id = 0;
            if ($story_status == 'Approved') {
                $story_status_id = 1;
            } elseif ($story_status == 'Rejected') {
                $story_status_id = 2;
            } elseif ($story_status == 'Pending') {
                echo "error";
                return;
            }

            if ($story_status_id == 2) {
                $story_disapproval = $this->input->get('story_disapproval');
            } else {
                $story_disapproval = NULL;
            }

            $story_update_array = array(
                'approve_status' => $story_status_id,
                'tag' => $tag,
                'disapproved_reason' => $story_disapproval,
                'approved_by' => $admin_id,
                'time_approved' => date('Y:m:d H:i:s'),
            );

            if ($this->Story_model->updateStory($story_id, $story_update_array)) {
                echo "success";
                return;
            }
            echo "error";
            return;
        }
    }

    public function updateStoryStatusNew() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            date_default_timezone_set("Asia/Karachi");

            $story_id = $this->input->get('story_id');
            $story_edit = $this->input->get('story_edit');
            $story_status = $this->input->get('story_status');
            $admin_id = $this->input->get('admin_id');
            $tag = $this->input->get('tag');

            $story_status = rtrim($story_status);

            $story_status_id = 0;
            if ($story_status == 'Approved') {
                $story_status_id = 1;
            } elseif ($story_status == 'Rejected' || $story_status == 'Inappropriate' || $story_status == 'Irrelevant' || $story_status == 'Misinformation' || $story_status == 'Inaudible' || $story_status == 'Hang Up' || $story_status == 'Silent' || $story_status == 'Misplaced') {
                $story_status_id = 2;
            } elseif ($story_status == 'Pending') {
                echo "error";
                return;
            }

            if ($story_status_id == 2) {
                $story_disapproval = $story_status;
            } else {
                $story_disapproval = NULL;
            }
            if ($story_edit == 1 && $story_status != "Approved") {
                $story_update_array = array(
                    'approve_status' => $story_status_id,
                    'tag' => "NULL",
                    'disapproved_reason' => $story_disapproval,
                    'approved_by' => $admin_id,
                );
            } else {
                $story_update_array = array(
                    'approve_status' => $story_status_id,
                    'tag' => $tag,
                    'disapproved_reason' => $story_disapproval,
                    'approved_by' => $admin_id,
                    'time_approved' => date('Y:m:d H:i:s'),
                );
            }


            if ($this->Story_model->updateStory($story_id, $story_update_array)) {
                echo "success";
                return;
            }
            echo "error";
            return;
        }
    }

    public function getComments() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $comments = $this->Story_model->getCommentsLimit($limit, $start);
            $comment_total = $this->Story_model->getCommentTotal();

            foreach ($comments as $key => $comment) {
                //Getting comment transcriptions if uploaded
                if ($comment['trans_upload_status'] == 1) {
                    $comment_transcription = $this->Story_model->getCommentTranscription($comment['comment_id']);
                    $comments[$key]['comment_roman_urdu'] = $comment_transcription['roman_urdu'];
                    $comments[$key]['comment_english'] = $comment_transcription['english'];
                    $comments[$key]['comment_urdu'] = $comment_transcription['urdu'];

                    $date1 = new DateTime($comment['time_recorded']);
                    $date2 = new DateTime($comment['time_trans_uploaded']);
                    $diff = $date2->diff($date1);

                    $diff = $diff->format('%a Day and %h hours');
                    $comments[$key]['comment_duration'] = $diff;
                }
            }

            echo json_encode(array("status" => "success", "comments" => $comments, "comment_total" => $comment_total));
            return;
        }
    }

    public function updateCommentStatus() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $comment_id = $this->input->get('comment_id');
            $status = $this->input->get('status');

            $comment_data = array('approve_status' => $status);
            $this->Story_model->updateComment($comment_id, $comment_data);
            echo "Success";
            return;
        }
    }

    //Getting all stories with transcription
    public function getAllStoriesTranscription() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $stories = $this->Story_model->getStoriesTranscriptionLimit($limit, $start);
            $story_total = $this->Story_model->getStoryTotal();

            foreach ($stories as $key => $story) {
                //Getting story transcriptions if uploaded
                if ($story['trans_upload_status'] == 1) {
                    $story_transcription = $this->Story_model->getStoryTranscription($story['user_story_id']);
                    $stories[$key]['story_roman_urdu'] = $story_transcription['roman_urdu'];
                    $stories[$key]['story_english'] = $story_transcription['english'];
                    $stories[$key]['story_urdu'] = $story_transcription['urdu'];

                    $date1 = new DateTime($story['time_recorded']);
                    $date2 = new DateTime($story['time_trans_uploaded']);
                    $diff = $date2->diff($date1);

                    $diff = $diff->format('%a Day and %h hours');
                    $stories[$key]['story_duration'] = $diff;
                }
            }


            echo json_encode(array("status" => "success", "stories" => $stories, "story_total" => $story_total));
            return;
        }
    }

    //Updating story transcription
    public function saveStoryTranscription() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            if (isset($_GET["story_id"]) && isset($_GET["story_urdu"]) && isset($_GET["story_roman_urdu"]) && isset($_GET["story_english"])) {
                date_default_timezone_set("Asia/Karachi");

                $story_id = $this->input->get("story_id");
                $story_urdu = $this->input->get("story_urdu");
                $story_roman_urdu = $this->input->get("story_roman_urdu");
                $story_english = $this->input->get("story_english");

                $story_transcription_array = array(
                    'urdu' => $story_urdu,
                    'roman_urdu' => $story_roman_urdu,
                    'english' => $story_english,
                );

                //If story transcription doesn't exit, then insert
                //else update
                $transcription = $this->Story_model->getStoryTranscription($story_id);
                if ($transcription) {
                    $this->Story_model->updateStoryTranscription($story_id, $story_transcription_array);
                } else {
                    $story_transcription_array['user_story_id'] = $story_id;
                    $this->Story_model->insertStoryTranscription($story_transcription_array);
                }

                //Updating story trans status
                $story_array = array('trans_upload_status' => 1, "time_trans_uploaded" => date('Y-m-d H:i:s'));
                $this->Story_model->updateStory($story_id, $story_array);

                echo "success";
                return;
            }
            echo "error";
            return;
        }
    }

    //Updating comment transcription
    public function saveCommentTranscription() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            if (isset($_GET["comment_id"]) && isset($_GET["comment_urdu"]) && isset($_GET["comment_roman_urdu"]) && isset($_GET["comment_english"])) {
                date_default_timezone_set("Asia/Karachi");

                $comment_id = $this->input->get("comment_id");
                $comment_urdu = $this->input->get("comment_urdu");
                $comment_roman_urdu = $this->input->get("comment_roman_urdu");
                $comment_english = $this->input->get("comment_english");

                $comment_transcription_array = array(
                    'urdu' => $comment_urdu,
                    'roman_urdu' => $comment_roman_urdu,
                    'english' => $comment_english,
                );

                //If comment transcription doesn't exit, then insert
                //else update
                $transcription = $this->Story_model->getCommentTranscription($comment_id);
                if ($transcription) {
                    $this->Story_model->updateCommentTranscription($comment_id, $comment_transcription_array);
                } else {
                    $comment_transcription_array['comment_id'] = $comment_id;
                    $this->Story_model->insertCommentTranscription($comment_transcription_array);
                }

                //Updating comment trans status
                $comment_array = array('trans_upload_status' => 1, "time_trans_uploaded" => date('Y-m-d H:i:s'));
                $this->Story_model->updateComment($comment_id, $comment_array);

                echo "success";
                return;
            }
            echo "error";
            return;
        }
    }

}
