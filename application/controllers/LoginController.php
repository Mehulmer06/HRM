<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LoginController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        try {
            // If user is already logged in, redirect to dashboard
            if ($this->session->userdata('logged_in')) {
                redirect('dashboard');
            }

            $this->load->view('auth/login');
        } catch (Exception $e) {
            log_message('error', 'Error loading login view: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Unable to load login page. Please try again later.');
            redirect('login');
        }
    }

    public function authenticate(): void
    {
        try {
            // Set validation rules
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('login');
                return;
            }

            $email = $this->input->post('email', TRUE); // XSS filtering
            $password = md5($this->input->post('password', TRUE));

            // Get user by email
            $user = $this->User->get_user_by_email($email);

            if ($user && $user->password === $password) {
                // Login successful
                $this->session->set_userdata([
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $user->name ?? $user->email,
                    'logged_in' => true
                ]);

                $this->session->set_flashdata('success', 'Login successful! Welcome back.');
                redirect('dashboard');
            } else {
                // Login failed
                $this->session->set_flashdata('error', 'Invalid email or password.');
                redirect('login');
            }
        } catch (Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Login failed due to a system error. Please try again.');
            redirect('login');
        }
    }

    public function logout(): void
    {
        try {
            $this->session->sess_destroy();
            $this->session->set_flashdata('success', 'You have been logged out successfully.');
            redirect('login');
        } catch (Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Logout failed due to a system error.');
            redirect('login');
        }
    }
}