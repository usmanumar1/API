<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('uploadAudio')) {
    function uploadAudio($answer_id)
    {
        $CI =& get_instance();
        $config['upload_path'] = 'E:\\USMAN\\xammp\\htdocs\\Mvp-Dashboard-New\\Recs\\' ; 
//'D:\\xampp\\htdocs\\MVP\\Recs\\';
        $config['allowed_types'] = '*';
        //[$config['max_size'] = 2048 * 8;
        //$config['encrypt_name'] = TRUE;
        $config['file_name'] = "A". $answer_id;

        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload('audio_path'))
        {
            $status = 0;
            $msg = $CI->upload->display_errors('', '');
            return array($status, $msg);
        }
        else
        {
            $status = 1;
            $data = $CI->upload->data();
            $fileName = $data['file_name'];
            return array($status, $fileName);
        }
    }
}