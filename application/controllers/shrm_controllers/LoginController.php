<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LoginController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shrm_models/User', 'User');
    }

    public function index(): void
    {
        try {
            // If user is already logged in, redirect to dashboard
            if ($this->session->userdata('logged_in')) {
                redirect('shrm/dashboard');
            }

            $this->load->view('shrm_views/auth/login');
        } catch (Exception $e) {
            log_message('error', 'Error loading login view: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Unable to load login page. Please try again later.');
            redirect('shrm/login');
        }
    }

    public function authenticate()
    {
        try {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('shrm/login');
                return;
            }

            $email = $this->input->post('email', TRUE);
            $password = md5($this->input->post('password', TRUE));

            // Try first (shrm) DB
            $user = $this->User->get_user_by_email($email);

            if ($user && $user->password === $password) {
                // Found in shrm DB
                $this->session->set_userdata([
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $user->name ?? $user->email,
                    'logged_in' => true,
                    'role' => $user->role,
                    'ro_id' => $user->reporting_officer_id,
                    'category' => $user->category,
                ]);
                $this->session->set_flashdata('success', 'Login successful! Welcome back.');
                redirect('shrm/dashboard');
            } else {

                $alt_user = $this->db->get_where('user', ['e_mail' => $email])->row();

                if ($alt_user && $alt_user->password === $password) {
                    // Try to get extra user data (if stored separately)
                    $user_data = $this->db->get_where('user_details', ['user_id' => $alt_user->id]);

                    $user_data_row = $user_data && $user_data->num_rows() > 0 ? $user_data->row() : null;

                    // Set session
                    $this->session->set_userdata('show_birthday_modal', true);
                    $this->session->set_userdata('user_id', $alt_user->id);
                    $this->session->set_userdata('name', $alt_user->name);
                    $this->session->set_userdata('e_mail', $alt_user->e_mail);
                    $this->session->set_userdata('password', $alt_user->password);
                    $this->session->set_userdata('payroll_no', $alt_user->payroll_no);
                    $this->session->set_userdata('role', $alt_user->role);
                    $this->session->set_userdata('category', $alt_user->category);
                    $this->session->set_userdata('requisition', $alt_user->requisition);
                    $this->session->set_userdata('user_type', $alt_user->user_type);
                    $this->session->set_userdata('designation', $user_data_row->designation ?? '');
                    $this->session->set_userdata('user_gender', $alt_user->user_gender);
                    $this->session->set_userdata('mobile_no', $alt_user->mobile_no);
                    $this->session->set_userdata('is_credit_society', $alt_user->is_credit_society);

                    $this->session->set_flashdata('success', 'Login successful.');
                    redirect('shrm/dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password.');
                    redirect('shrm/login');
                }

            }
        } catch (Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Login failed due to a system error.');
            redirect('shrm/login');
        }
    }

    public function logout()
    {
        try {
            $this->session->sess_destroy();
            $this->session->set_flashdata('success', 'You have been logged out successfully.');
            redirect('shrm/login');
        } catch (Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Logout failed due to a system error.');
            redirect('shrm/login');
        }
    }
}