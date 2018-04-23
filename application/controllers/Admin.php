<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->model('Admin_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    //-------------------------------------------------DASHBOARD------------------------------------------------------//
    public function loginAdmin()
    {
        if($this->input->server('REQUEST_METHOD')== "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $admin_data = array(
                'admin_name' => $data->admin_name,
                'password' => $data->password
            );

            //Getting User
            $admin = $this->Admin_model->getLoginAdmin($admin_data);
            $admin_role = $this->Admin_model->getAdminRole($admin_data);
            if(!($admin)) {
                echo json_encode(array('status' => "error in db"));
                return;
            }
            else {
                echo json_encode(array('status' => "success", "admin" => $admin,"admin_role"=>$admin_role));
                return;
            }
        }
    }


    //-------------------------------------------------DASHBOARD------------------------------------------------------//


}