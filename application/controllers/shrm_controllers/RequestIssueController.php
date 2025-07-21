<?php

class RequestIssueController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->load->model('shrm_models/RequestIssue', 'requestIssue');
        $this->load->model('shrm_models/User', 'User');
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function index()
    {
        try {
            $data['requestData'] = $this->requestIssue->getRequestIssue();
            $data['closeData'] = $this->requestIssue->getRequestIssueColse();
            $data['users'] = $this->User->get_users_with_latest_contract_request();

            $this->load->view('shrm_views/pages/request_issue/index', $data);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('dashboard');
        }
    }

    public function store()
    {
        try {
            $this->load->library('upload');
            // Validation Rules
            $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[100]');
            $this->form_validation->set_rules('submitted_to', 'Submitted To', 'required');
            $this->form_validation->set_rules('description', 'Description', 'required|min_length[10]|max_length[1000]');

            if ($this->form_validation->run() == false) {
                $data['requestData'] = $this->requestIssue->getRequestIssue();
                $data['closeData'] = $this->requestIssue->getRequestIssueColse();
                $data['users'] = $this->User->get_users_with_latest_contract_request();
                $this->load->view('shrm_views/pages/request_issue/index', $data);
                return;
            }

            // Handle file upload if exists
            $fileName = null;
            if (!empty($_FILES['document']['name'])) {
                $uploadPath = FCPATH . 'uploads/request_issue/';

                // Create folder if it doesn't exist
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = 10240; // 10 MB
                $config['encrypt_name'] = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('document')) {
                    throw new Exception(strip_tags($this->upload->display_errors()));
                }

                $uploadData = $this->upload->data();
                $fileName = $uploadData['file_name'];
            }

            // Get session values
            $user_id = $this->session->userdata('user_id');
            $role = $this->session->userdata('role');
            $category = $this->session->userdata('category');

            // Prefix user_id if both role and category are 'e'
            if (($role === 'e' && $category === 'e') || $role === 'a') {
                $user_id = '00' . $user_id;
            }

            // Prepare insert data
            $data = [
                'user_id' => $user_id,
                'title' => $this->input->post('title', true),
                'submitted_to' => $this->input->post('submitted_to', true),
                'description' => $this->input->post('description', false), // allow HTML
                'document' => $fileName,
                'request_date' => date('Y-m-d H:i:s'),
                'status' => 'in_progress',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert into DB
            $this->shrm->insert('request_issues', $data);

            $this->session->set_flashdata('success', 'Request submitted successfully!');
            redirect('request-issue');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());

            // reload the form view with existing data if exception
            $data['requestData'] = $this->requestIssue->getRequestIssue();
            $data['closeData'] = $this->requestIssue->getRequestIssueColse();
            $data['users'] = $this->User->get_users_with_latest_contract_request();
            $this->load->view('shrm_views/pages/request_issue/index', $data);
        }
    }


    public function show($id)
    {
        $evaluation = $this->requestIssue->getById($id);
        if (!$evaluation) {
            show_404();
        }

        // Get comments for this request
        $comments = $this->requestIssue->get_by_request($id);

        $data['evaluation'] = $evaluation;
        $data['comments'] = $comments;
        $this->load->view('shrm_views/pages/request_issue/show', $data);
    }


    public function commentStore()
    {
        try {
            $request_id = $this->input->post('request_id');
            $comment = $this->input->post('comment');

            // Validate inputs
            if (empty($request_id) || empty(trim($comment))) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
                return;
            }

            // Check if request exists
            $this->shrm->where('id', $request_id);
            $this->shrm->where('deleted_at IS NULL', null, false);
            $request_exists = $this->shrm->get('request_issues')->num_rows() > 0;

            if (!$request_exists) {
                echo json_encode(['status' => 'error', 'message' => 'Request not found']);
                return;
            }

            // Get session data
            $session_user_id = $this->session->userdata('user_id');
            $session_category = $this->session->userdata('category');
            $session_role = $this->session->userdata('role');

            // Determine user_id for comment based on category and role
            if (($session_role === 'e' && $session_category === 'e') || $session_role === 'a') {
                $comment_user_id = '00' . $session_user_id;
            } else {
                $comment_user_id = $session_user_id;
            }

            $data = [
                'request_id' => $request_id,
                'comment' => trim($comment),
                'user_id' => $comment_user_id,
                'status' => 'Y',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->requestIssue->commentInsert($data);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Comment added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    public function fetch($id)
    {
        try {
            // Validate ID
            if (empty($id) || !is_numeric($id)) {
                echo json_encode(['comments' => []]);
                return;
            }

            // Check if request exists
            $this->shrm->where('id', $id);
            $this->shrm->where('deleted_at IS NULL', null, false);
            $request_exists = $this->shrm->get('request_issues')->num_rows() > 0;

            if (!$request_exists) {
                echo json_encode(['comments' => []]);
                return;
            }

            $comments = $this->requestIssue->get_by_request($id);
            echo json_encode(['comments' => $comments]);

        } catch (Exception $e) {
            echo json_encode(['comments' => [], 'error' => $e->getMessage()]);
        }
    }


    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $allowedStatuses = ['in_progress', 'close'];

        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false]);
            return;
        }

        $updated = $this->requestIssue->updateStatus($id, ['status' => $status]);
        echo json_encode(['success' => $updated]);
    }
}
