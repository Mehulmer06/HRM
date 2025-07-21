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
            $this->form_validation->set_rules('captcha', 'Captcha', 'required|numeric');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('shrm/login');
                return;
            }

            // Verify captcha first
            $captcha_answer = $this->input->post('captcha', TRUE);
            if (!$this->verify_captcha($captcha_answer)) {
                $this->session->set_flashdata('error', 'Invalid captcha answer. Please try again.');
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

    public function captcha()
    {
        // Generate two random numbers for math operation
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);

        // Random operation (0 = addition, 1 = subtraction)
        $operation = rand(0, 1);

        if ($operation == 0) {
            // Addition
            $question = $num1 . ' + ' . $num2 . ' = ?';
            $answer = $num1 + $num2;
        } else {
            // Subtraction - ensure positive result
            if ($num1 < $num2) {
                $temp = $num1;
                $num1 = $num2;
                $num2 = $temp;
            }
            $question = $num1 . ' - ' . $num2 . ' = ?';
            $answer = $num1 - $num2;
        }

        // Store answer in session
        $this->session->set_userdata('captcha_answer', $answer);

        // Image dimensions
        $width = 150;
        $height = 50;

        // Create image
        $image = imagecreate($width, $height);

        // Define colors
        $bg_color = imagecolorallocate($image, 240, 240, 240); // Light gray background
        $text_color = imagecolorallocate($image, 0, 0, 0);     // Black text

        // Add the math question to image with larger TTF font
        $font_size = 18; // Large TTF font size
        $font_file = FCPATH . 'asset/arial/arial.ttf'; // Path to TTF font file

        // Get text dimensions for centering
        $text_box = imagettfbbox($font_size, 0, $font_file, $question);
        $text_width = $text_box[4] - $text_box[0];
        $text_height = $text_box[1] - $text_box[7];

        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2 + $text_height;

        // Use TTF font for larger, clearer text
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $question);

        // Set header and output image
        header('Content-Type: image/jpeg');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        imagejpeg($image, null, 80);
        imagedestroy($image);
    }

// Helper function to verify captcha
    public function verify_captcha($user_answer)
    {
        $stored_answer = $this->session->userdata('captcha_answer');

        if ($stored_answer && intval($user_answer) === intval($stored_answer)) {
            // Clear the captcha from session after successful verification
            $this->session->unset_userdata('captcha_answer');
            return true;
        }

        return false;
    }
}