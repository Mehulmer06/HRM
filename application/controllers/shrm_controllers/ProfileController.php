<?php

class ProfileController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/User', 'User');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $data['profiles'] = $this->User->get_by_user($user_id);
        $data['contract_history'] = $this->User->contract_history($user_id);
        $data['current_contract'] = $this->User->getContractByUserId($user_id);


        return $this->load->view('shrm_views/profile/index', $data);
    }

    public function changePassword()
    {
        $user_id = $this->session->userdata('user_id');
        $data['users'] = $this->User->get_phone_by_user_id($user_id);
        return $this->load->view('shrm_views/profile/change_password', $data);
    }

    public function update_phone()
    {
        try {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $user_id = $this->session->userdata('user_id');

            // Basic phone validation
            if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $this->session->set_flashdata('error', 'Invalid phone number. Please enter 10 to 15 digits.');
                return redirect('change-password');
            }

            // Initialize data with phone
            $userData = ['phone' => $phone];

            // Handle photo upload if provided
            if (!empty($_FILES['photo']['name'])) {
                $uploadDir = FCPATH . './upload/photo/';

                // Create directory if not exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $photoConfig = [
                    'upload_path' => $uploadDir,
                    'allowed_types' => 'jpg|jpeg|png',
                    'encrypt_name' => TRUE
                ];

                $this->load->library('upload', $photoConfig);

                if ($this->upload->do_upload('photo')) {
                    $photoData = $this->upload->data();
                    $filename = $photoData['file_name'];

                    // Add to update only if upload succeeds
                    $userData['photo'] = $filename;
                } else {
                    throw new Exception('Photo upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }

            // Update users table
            $this->shrm->where('id', $user_id);
            $this->shrm->update('users', $userData);

            $this->session->set_flashdata('success', 'Profile updated successfully.');
            return redirect('shrm/dashboard');

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong: ' . $e->getMessage());
            return redirect('change-password');
        }
    }


    public function update_password()
    {
        try {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('current_password', 'Current Password', 'required');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors('<div>', '</div>'));
                redirect('change-password');
            } else {
                $user_id = $this->session->userdata('user_id');
                $current_password = $this->input->post('current_password');
                $new_password = $this->input->post('new_password');

                $this->load->model('User');
                $user = $this->User->get_user_by_password($user_id);
                if ( ! $user || md5($current_password) !== $user->password ) {
                    $this->session->set_flashdata('error', 'Current password is incorrect.');
                    redirect('change-password');
                }

                $md5_password = md5($new_password);
                $this->User->update_password($user_id, $md5_password);

                $this->session->set_flashdata('success', 'Password updated successfully.');
                redirect('shrm/logout');
            }
        } catch (Exception $e) {
            log_message('error', 'Error in update_password: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('change-password');
        }
    }

}


