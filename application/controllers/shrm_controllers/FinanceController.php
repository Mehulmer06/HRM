<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FinanceController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/Finance', 'finance');
        $this->load->model('shrm_models/User', 'User');
        $this->load->library('upload');
    }

    public function index()
    {
        $data['users'] = $this->User->get_users_with_latest_contract();
        $data['finances'] = $this->finance->getAllFinance();
        $this->load->view('shrm_views/pages/finance/index', $data);
    }

    public function store()
    {
        try {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('month', 'Month', 'required');
            $this->form_validation->set_rules('document', 'Document', 'callback_document_required');

            if ($this->form_validation->run() == false) {
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['validation_errors'] = validation_errors();
                $this->load->view('shrm_views/pages/finance/index', $data);
                return;
            }

            // File upload configuration
            $upload_path = FCPATH . 'upload/salary_slip/';

// Create directory if it doesn't exist
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true); // Creates directory recursively
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|txt';
            $config['max_size'] = 10240;
            $config['encrypt_name'] = true;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('document')) {
                throw new Exception(strip_tags($this->upload->display_errors()));
            }

            $uploadData = $this->upload->data();
            $fileName = $uploadData['file_name'];
            $userId = $this->input->post('username', true);
            $month = $this->input->post('month', true);
            $data = [
                'user_id' => $userId,
                'month_year' => $month,
                'salary_slip' => $fileName,
                'status' => 'Y',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $isCheck = $this->finance->checkFinance($userId, $month);
            if (!empty($isCheck)) {
                $this->session->set_flashdata('error', 'This user already has an uploaded salary slip for the selected month and year!');
                redirect('shrm/finance');
            }
            $this->shrm->insert('finances', $data);
            $this->session->set_flashdata('success', 'Finance submitted successfully!');
            redirect('shrm/finance');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('shrm/finance');
        }
    }

    public function document_required()
    {
        if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
            return true;
        } else {
            $this->form_validation->set_message('document_required', 'The Document field is required.');
            return false;
        }
    }


}