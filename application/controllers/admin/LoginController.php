<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LoginController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->load->view('admin/login');
    }

    public function authenticate()
    {
        $email = $this->input->post('email');
        $password = md5($this->input->post('password')); // MD5 hash

        $user = $this->User->get_user_by_email($email);

        if ($user && $user->password === $password) {
            // Success: set session
            $this->session->set_userdata([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'logged_in' => true
            ]);
            redirect('dashboard');
        } else {
            // Failure
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }

}
