<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->library('form_validation');
        $this->load->helper(array('form'));
        $this->load->library('email');


        $this->load->model('Doctor_model');
        $this->load->model('Question_model');
    }

    //----------------------------------------------------------------------------------------------------------------//
    public function getAllDoctors() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $doctors = $this->Doctor_model->getDoctorsLimit($limit, $start);
            $doctor_total = $this->Doctor_model->getDoctorTotal();
            //Getting status of doctor
            //if question answered in last 7 days or not
            foreach ($doctors as $key => $doctor) {
                $status = "Inactive";

                $time = $this->Doctor_model->getTimeLastAnswer($doctor['doc_id']);
                if ($time) {
                    $date1 = new DateTime($time['time_answered']);
                    $date2 = new DateTime(date('Y-m-d H:i:s'));
                    $diff = $date2->diff($date1);

                    $diff = $diff->format('%a');
                    if ($diff < 7) {
                        $status = "active";
                    } else {
                        $status = "Inactive";
                    }
                }

                $doctors[$key]['status'] = $status;
            }


            echo json_encode(array("status" => "success", "doctors" => $doctors, "doctor_total" => $doctor_total));
            return;
        }
    }

    public function getDoctor() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $doctor_id = $this->input->get('doctor_id');
            $doctor = $this->Doctor_model->selectDoctorByID($doctor_id);

            $answer_total = $this->Doctor_model->getDoctorAnswerTotal($doctor_id);
            $average_time = $this->Doctor_model->getDoctorAverageTime($doctor_id);

            $doctor['answer_total'] = $answer_total;
            $doctor['average_time'] = $average_time['result'];
            echo json_encode(array("status" => "success", "doctor" => $doctor));
            return;
        }
    }

    public function getAllChatResponses() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $doctor_id = $this->input->get('doctor_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $responses = $this->Doctor_model->getChatResponses($limit, $start, $doctor_id);
            $response_total = $this->Doctor_model->getChatResponseTotal($doctor_id);

            echo json_encode(array("status" => "success", "responses" => $responses, "response_total" => $response_total));
            return;
        }
    }

    //Inserting a new reply to response
    public function saveResponseReply() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            if (isset($_GET["doctor_id"]) && isset($_GET["response_reply"])) {
                $doctor_id = $this->input->get("doctor_id");
                $response_reply = $this->input->get("response_reply");

                $response_reply_array = array(
                    'response' => $response_reply,
                    'response_type' => "admin",
                    'doctor_id' => $doctor_id
                );

                //Inserting a new response - reply
                $response_reply_id = $this->Doctor_model->insertChatResponse($response_reply_array);
                if ($response_reply_id) {
                    $doctor = $this->Doctor_model->selectDoctorByID($doctor_id);

                    //If doctor is login, then send notification
                    if ($doctor['isLogin'] != 0) {
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $headers = array('Content-Type' => 'application/json', 'authorization' => 'key= AIzaSyBkjUN0B0-_gE0TthUgBGgpCZdEsATQ7-M');
                        $fields = array(
                            'data' => array(
                                "response_reply" => "reply available"
                            ),
                            'to' => $doctor['doc_token']
                        );

                        $response = (Requests::post($url, $headers, json_encode($fields)));
                    }
                }

                echo "success";
                return;
            }
            echo "error";
            return;
        }
    }

    public function updateReadStatus() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $doc_id = $this->input->get('doc_id');

            $read_status = $this->input->get('read_status');
            $read_status_array = array(
                'read_status' => $read_status
            );

            $readStatus = $this->Doctor_model->updateMessageReadStatus($doc_id, $read_status_array);
            echo json_encode(array("status" => "success", "readStatus" => $readStatus));
            return;
        }
    }

    public function getnewDoctorsSignup() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $admin_id = $this->input->get('admin_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start = $data->start;
            $doctors = $this->Doctor_model->selectDoctorsNewSignup($limit, $start);
            $doctor_total = $this->Doctor_model->countDoctorsNewSignup();
            //print_r($doctors);die;

            echo json_encode(array("status" => "success", "doctors" => $doctors, "doctor_total" => $doctor_total));
            return;
        }
    }

    public function updateDoctorApproveStatus() {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $doctor_id = $this->input->get('doctor_id');
            //checking if exists in blocked user
            $newSignupDoctor = $this->Doctor_model->selectDoctorNew($doctor_id);
            $doc_email = $newSignupDoctor['doc_email'];
            $doc_id = $newSignupDoctor['doc_id'];

            //update
            $doctor_array = array('isApproved' => 1);
            if ($newSignupDoctor) {
                $isApproved = $this->Doctor_model->updateNewDoctorIsApproved($doctor_id, $doctor_array);

                if ($isApproved) {
                    $config['protocol'] = 'sendmail';
                    $config['mailtype'] = 'html';
                    $config['wordwrap'] = TRUE;
                    $this->email->initialize($config);
                    $this->email->from('admin@rahemaadevelopment.com', 'Rah-e-Maa Team');
                    $this->email->to(trim($doc_email));
                    $this->email->cc('hunainusman94@gmail.com');
                    $this->email->bcc('hunainusman94@gmail.com');
                    $this->email->subject('Activation Email');
                    $this->email->message('
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Actionable emails e.g. reset password</title>


<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body itemscope itemtype="" style="font-family:Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family:Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family:Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family:Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://58.27.220.110:201/MVP/MVP-Dashboard/Login" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Congratulation!<br>
                  
                  Your account is approved by admin. Now you can login from Rah-e-Maa app.<br>Thanks!
									</td>
								</tr>
                                                                <tr style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; Rah-e-Maa Team<br>
                                                                                Information Technology University<br>
                                                                                W: www.rahemaa.com  
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
						</tr></table></div></div>
		</td>
		
	</tr></table></body>
</html>
');
                    $this->email->send();
                    //If doctor is not login(new account), then send notification
//                    if ($newSignupDoctor['isLogin'] = 0) {
//                        $url = 'https://fcm.googleapis.com/fcm/send';
//                        $headers = array('Content-Type' => 'application/json', 'authorization' => 'key= AIzaSyBkjUN0B0-_gE0TthUgBGgpCZdEsATQ7-M');
//                        $fields = array(
//                            'data' => array(
//                                "response_reply" => "You can login now from rah-e-maa app"
//                            ),
//                            'to' => $doctor['doc_token']
//                        );
//
//                        $response = (Requests::post($url, $headers, json_encode($fields)));
//                    }
                    echo json_encode(array('status' => "success", 'msg' => " Account is activated", "doc_id" => $doc_id));
                    return;
                }
            }
        }
    }

    public function resetPassword() {
        date_default_timezone_set("Asia/Karachi");
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
//            $code = $this->input->get('code');
//            $password = $this->input->get('password');
            $data = json_decode(file_get_contents("php://input"));
                $password = $data->doc_password;
            $code = $data->code;

            $getDoctor = $this->Doctor_model->selectDoctorByResetCode($code);
            $doc_id = $getDoctor['doc_id'];
            $new_password_array = array(
                'doc_password' => $password
            );
            if (isset($getDoctor)) {

                $doc_reset_time = $getDoctor['doc_reset_time'];
                $expiry_time = strtotime($doc_reset_time) + (60 * 60 * 24 * 2);
                $now = time();

                if ($now > $expiry_time) {

                    $remove_reset_code_array = array(
                        'doc_reset_code' => '',
                        'doc_reset_time' => '0000-00-00 00:00:00'
                    );

                    if ($this->Doctor_model->updateDoctorResetCode($doc_id, $remove_reset_code_array)) {


                        echo json_encode(array("status" => "error", "msg" => "Reset Code Link Is Expired"));
                        return;
                    }
                }


//                print_r($doc_id);
//                print_r($password);die;
                if ($this->Doctor_model->updateDoctorPassword($doc_id, $new_password_array)) {
                    $remove_reset_code_array = array(
                        'doc_reset_code' => '',
                        'doc_reset_time' => '0000-00-00 00:00:00'
                    );

                    $this->Doctor_model->updateDoctorResetCode($doc_id, $remove_reset_code_array);
                    echo json_encode(array("status" => "success", "newPassword" => $password));
                    return;
                } else {
                    echo json_encode(array("status" => "error", "msg" => "password not updated"));
                    return;
                }
            }else{
                echo json_encode(array("status" => "error", "msg" => "Invalid/Missing Reset Code"));
                    return;
            }
        }
    }
    
    public function resetPin() {
        date_default_timezone_set("Asia/Karachi");
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
//            $code = $this->input->get('code');
//            $password = $this->input->get('password');
            $data = json_decode(file_get_contents("php://input"));
            $pin = $data->doc_pin;
            $code = $data->code;

            $getDoctor = $this->Doctor_model->selectDoctorByResetCode($code);
            $doc_id = $getDoctor['doc_id'];
            $new_password_array = array(
                'doc_pin' => $pin
            );
            if (isset($getDoctor)) {

                $doc_reset_time = $getDoctor['doc_reset_time'];
                $expiry_time = strtotime($doc_reset_time) + (60 * 60 * 24 * 2);
                $now = time();

                if ($now > $expiry_time) {

                    $remove_reset_code_array = array(
                        'doc_reset_code' => '',
                        'doc_reset_time' => '0000-00-00 00:00:00'
                    );

                    if ($this->Doctor_model->updateDoctorResetCode($doc_id, $remove_reset_code_array)) {


                        echo json_encode(array("status" => "error", "msg" => "Reset Code Link Is Expired"));
                        return;
                    }
                }


//                print_r($doc_id);
//                print_r($password);die;
                if ($this->Doctor_model->updateDoctorPassword($doc_id, $new_password_array)) {
                    $remove_reset_code_array = array(
                        'doc_reset_code' => '',
                        'doc_reset_time' => '0000-00-00 00:00:00'
                    );

                    $this->Doctor_model->updateDoctorPassword($doc_id, $remove_reset_code_array);
                    echo json_encode(array("status" => "success", "newPin" => $pin));
                    return;
                } else {
                    echo json_encode(array("status" => "error", "msg" => "pin not updated"));
                    return;
                }
            }else{
                echo json_encode(array("status" => "error", "msg" => "Invalid/Missing Reset Code"));
                    return;
            }
        }
    }


}
