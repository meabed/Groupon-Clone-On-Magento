<?php

class User extends CI_Controller
{

    /**
     * Check if the user is logged in, if he's not,
     * send him to the login page
     *
     * @return void
     */
    function index()
    {
        if (is_auth()) {
            redirect('admin/products');
        } else {
            $this->load->view('admin/login');
        }
    }

    /**
     * encript the password
     *
     * @return mixed
     */
    function __encrip_password($password)
    {
        return md5($password);
    }

    /**
     * check the username and the password with the database
     *
     * @return void
     */
    function validate_credentials()
    {

        $this->load->model('Users_model');

        $username = $this->input->post('username');
        $password = $this->__encrip_password($this->input->post('password'));

        $is_valid = $this->Users_model->validate($username, $password);
        $data = $this->Users_model->getMemberByUsername($username);

        if ($is_valid && $data['active']) {
            $this->Users_model->updateLoginDateTime();
            $this->session->set_userdata($data);
            redirect('admin/products');
        } else // incorrect username or password
        {
            $data['message_error'] = true;
            $this->load->view('admin/login', $data);
        }
    }

    /**
     * The method just loads the signup view
     *
     * @return void
     */
    function signup()
    {
        $this->load->view('admin/signup_form');
    }


    /**
     * Create new user and store it in the database
     *
     * @return void
     */
    function create_member()
    {
        $this->load->library('form_validation');

        // field name, error message, validation rules
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/signup_form');
        } else {
            $this->load->model('Users_model');

            if ($query = $this->Users_model->create_member()) {
                $this->load->view('admin/signup_successful');
            } else {
                $this->load->view('admin/signup_form');
            }
        }

    }

    /**
     * Destroy the session, and logout the user.
     *
     * @return void
     */
    function logout()
    {
        $this->session->sess_destroy();
        redirect('admin');
    }

    public function profile()
    {
        if(!is_auth()){
            redirect('admin/login');
        }
        $id = $this->session->userdata('id');

        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            $this->form_validation->set_rules('name', 'name', 'required');
            $this->form_validation->set_error_delimiters(
                '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>'
            );
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            if (is_admin()) {
            $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
            }
            $pass = $this->input->post('password');
            if ($pass) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
            }
            //if the form has passed through the validation
            if ($this->form_validation->run()) {

                if (is_admin()) {
                    $data_to_store = array(
                        'email' => $this->input->post('email'),
                        'active' => $this->input->post('active'),
                        'is_admin' => $this->input->post('is_admin'),
                    );
                }
                $data_to_store['name'] = $this->input->post('name');
                if ($pass) {
                    $data_to_store['password'] = $this->__encrip_password($this->input->post('password'));

                }
                //if the insert has returned true then we show the flash message
                if ($this->vendors_model->update_vendor($id, $data_to_store) == true) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/profile');

            }
            //validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data
        $data['manufacture'] = $this->vendors_model->get_vendor_by_id($id);
        //load the view
        $data['main_content'] = 'admin/users/profile';
        $this->load->view('includes/template', $data);

    }

}