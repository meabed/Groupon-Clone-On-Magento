<?php

class Users_model extends CI_Model
{

    /**
     * Validate the login's data with the database
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    function validate($username, $password)
    {
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query = $this->db->get('membership');

        if ($query->num_rows == 1) {
            return true;
        }
    }

    public function getMemberByUsername($username)
    {
        $query = $this->db->select('*')->from('membership')->where('username', $username)->get();
        $rows = $query->result_array();
        return ($rows) ? $rows[0] : false;
    }

    /**
     * Serialize the session data stored in the database,
     * store it in a new array and return it to the controller
     *
     * @return array
     */
    function get_db_session_data()
    {
        $query = $this->db->select('user_data')->get('ci_sessions');
        $user = array(); /* array to store the user data we fetch */
        foreach ($query->result() as $row) {
            $udata = unserialize($row->user_data);
            /* put data in array using username as key */
            $user['username'] = $udata['username'];
            $user['is_logged_in'] = $udata['is_logged_in'];
            $data = $this->getMemberByUsername($user['username']);
        }
        return $data;
    }

    /**
     * Store the new user's data into the database
     *
     * @return boolean - check the insert
     */
    function create_member($err = true, $data = array())
    {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $this->db->where('username', $username);
        $query = $this->db->get('membership');
        $this->db->where('email', $email);
        $query2 = $this->db->get('membership');
        if ($query->num_rows > 0 || $query2->num_rows > 0 ) {
            if (!$err) {
                return false;
            }
            echo '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>';
            echo "Username already taken";
            echo '</strong></div>';
        } else {
            $date = date('Y-m-d H:i:s');
            $new_member_insert_data = array(
                'name'       => $this->input->post('name'),
                'email'      => $this->input->post('email'),
                'username'   => $this->input->post('username'),
                'password'   => md5($this->input->post('password')),
                'created_at' => $date,
                'updated_at' => $date,
            );
            $data['password'] = $new_member_insert_data['password'];
            $new_member_insert_data = array_merge($new_member_insert_data, $data);
            $insert = $this->db->insert('membership', $new_member_insert_data);
            return $insert;
        }

    }

    public function updateLoginDateTime()
    {
        $username = $this->input->post('username');
        $data['last_login'] = date('Y-m-d H:i:s');
        $this->db->where('username', $username);
        $this->db->update('membership', $data);
    }
    //create_member
}

