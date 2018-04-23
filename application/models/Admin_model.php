<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Getting USER for login
    public function getLoginAdmin($data)
    {
        $query = $this->db
            ->where('admin_name',  $data['admin_name'] )
            ->where('password', $data['password'])
            ->get('admin');

        if ( $query->num_rows() > 0 )
        {
            $row = $query->row_array();
            return $row;
        }
        else
        {
            return false;
        }
    }
    public function getAdminRole($data) {
        $query = $this->db
                ->select('admin_role')
                ->where('admin_name', $data['admin_name'])
                ->get('admin');
        if($query->num_rows() > 0){
            $row=$query->row_array();
            return $row;
        } 
        else{
            return false;
        }
    }

    public function getAdminName($admin_id)
    {
        $query = $this->db
            ->select('admin_name')
            ->where('admin_id', $admin_id)
            ->get('admin');

        return $query->row_array();
    }
}