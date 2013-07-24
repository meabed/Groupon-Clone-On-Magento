<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        $CI =& get_instance();
        $user = $CI->session->userdata('username');
        $row = $CI->Users_model->getMemberByUsername($user);
        if (($row['username'] == $user) && ($row['active'] == 1) && ($row['is_admin'] == 1)) {
            return $row;
        }
        return false;
    }
}
if (!function_exists('is_auth')) {
    function is_auth()
    {
        $CI =& get_instance();
        $user = $CI->session->userdata('username');
        $id = $CI->session->userdata('id');
        $row = $CI->Users_model->getMemberByUsername($user);
        if (($id == $row['id']) && ($row['username'] == $user) && ($row['active'] == 1)) {
            return $row;
        }else{
            //$CI->session->destroy();
        }
        return false;
    }
}
if (!function_exists('getUID')) {
    function getUID()
    {
        $CI =& get_instance();
        return $id = $CI->session->userdata('id');
    }
}