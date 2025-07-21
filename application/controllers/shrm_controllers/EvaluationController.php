<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EvaluationController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/Evaluation', 'Evaluation');
        $this->load->model('shrm_models/User', 'User');
    }

    public function index()
    {
        $data['evaluations'] = $this->Evaluation->getAllEvaluations();
        $this->load->view('shrm_views/pages/evaluation/index', $data);
    }

    public function create()
    {
        $data['users'] = $this->User->get_users_with_latest_contract();
        $this->load->view('shrm_views/pages/evaluation/create', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required|in_list[routine,urgent,addon,support]');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $data['users'] = $this->User->get_users_with_latest_contract();
            $data['validation_errors'] = validation_errors(); // optional
            $this->load->view('shrm_views/pages/evaluation/create', $data);
            return;
        }

        $category = $this->input->post('category');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $assignedUsers = $this->input->post('assign_users');

        // Assume current logged-in user is the reporting officer
        $reportingOfficerId = $this->session->userdata('user_id');
        $reportingOfficerName = $this->session->userdata('name');

        // Insert evaluation
        $evaluationData = [
            'title' => $title,
            'description' => $description,
            'reporting_officer_id' => $reportingOfficerId,
            'reporting_officer_name' => $reportingOfficerName,
            'category' => $category,
            'status' => 'pending'
        ];

        if (!empty($_FILES['attachment']['name'])) {
            $upload_dir = './uploads/evaluations/';
            $full_path = FCPATH . 'uploads/evaluations/';

            // Create directory if it doesn't exist
            if (!is_dir($full_path)) {
                if (!mkdir($full_path, 0755, true)) {
                    $data['users'] = $this->User->get_users_with_latest_contract();
                    $data['upload_error'] = 'Failed to create upload directory. Please check permissions.';
                    $this->load->view('shrm_views/pages/evaluation/create', $data);
                    return;
                }
            }

            // Verify directory is writable
            if (!is_writable($full_path)) {
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['upload_error'] = 'Upload directory is not writable. Please check permissions.';
                $this->load->view('shrm_views/pages/evaluation/create', $data);
                return;
            }

            $config['upload_path'] = $upload_dir; // Use relative path
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['encrypt_name'] = true;
            $config['max_size'] = 10240; // 10MB in KB
            $config['remove_spaces'] = true;

            // Initialize upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            // Changed from 'document' to 'attachment' to match the form field name
            if ($this->upload->do_upload('attachment')) {
                $uploadData = $this->upload->data();
                $filePath = 'uploads/evaluations/' . $uploadData['file_name'];
                $evaluationData['attachment'] = $filePath;
            } else {
                // Handle upload error
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['upload_error'] = $this->upload->display_errors();
                $this->load->view('shrm_views/pages/evaluation/create', $data);
                return;
            }
        }

        $evaluationId = $this->Evaluation->insertEvaluation($evaluationData);

        if ($evaluationId && !empty($assignedUsers)) {
            foreach ($assignedUsers as $userId) {
                $this->Evaluation->assignUserToEvaluation($evaluationId, $userId);
            }

            // Set success message
            $this->session->set_flashdata('success', 'Work progress evaluation created successfully!');
        } else {
            // Set error message
            $this->session->set_flashdata('error', 'Failed to create work progress evaluation. Please try again.');
        }

        redirect('work-progress'); // redirect to evaluation list
    }


    public function edit($id)
    {
        $originalId = $this->decryptId($id);
        $evaluation = $this->Evaluation->getById($originalId);
        if (!$evaluation) {
            show_404();
        }

        $assigned = $this->Evaluation->getAssignedUserIds($originalId);
        $data = [
            'evaluation' => $evaluation,
            'assigned_user_ids' => array_column($assigned, 'user_id'),
            'users' => $this->User->get_users_with_latest_contract()
        ];

        $this->load->view('shrm_views/pages/evaluation/edit', $data);
    }


    public function update($id)
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required|in_list[routine,urgent,addon,support]');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $evaluation = $this->Evaluation->getById($id);
            if (!$evaluation) show_404();

            $data['evaluation'] = $evaluation;
            $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
            $data['users'] = $this->User->get_users_with_latest_contract();
            $data['validation_errors'] = validation_errors();
            $this->load->view('shrm_views/pages/evaluation/edit', $data);
            return;
        }

        // Get existing evaluation data
        $evaluation = $this->Evaluation->getById($id);
        if (!$evaluation) show_404();

        // Prepare update data
        $updateData = [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'category' => $this->input->post('category'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle attachment removal
        if ($this->input->post('remove_attachment') == '1') {
            // Delete old file if exists
            if (!empty($evaluation->attachment) && file_exists(FCPATH . $evaluation->attachment)) {
                unlink(FCPATH . $evaluation->attachment);
            }
            $updateData['attachment'] = null;
            $updateData['attachment_original_name'] = null;
        }

        // Handle new attachment upload
        if (!empty($_FILES['attachment']['name'])) {
            // Use relative path from the application root
            $upload_dir = './uploads/evaluations/';
            $full_path = FCPATH . 'uploads/evaluations/';

            // Create directory if it doesn't exist
            if (!is_dir($full_path)) {
                if (!mkdir($full_path, 0755, true)) {
                    $evaluation = $this->Evaluation->getById($id);
                    $data['evaluation'] = $evaluation;
                    $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
                    $data['users'] = $this->User->get_users_with_latest_contract();
                    $data['upload_error'] = 'Failed to create upload directory. Please check permissions.';
                    $this->load->view('shrm_views/pages/evaluation/edit', $data);
                    return;
                }
            }


            $config['upload_path'] = $upload_dir;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['encrypt_name'] = true;
            $config['max_size'] = 10240; // 10MB in KB
            $config['remove_spaces'] = true;

            // Initialize upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('attachment')) {
                // Delete old file if exists
                if (!empty($evaluation->attachment) && file_exists(FCPATH . $evaluation->attachment)) {
                    unlink(FCPATH . $evaluation->attachment);
                }

                // Save new file info
                $uploadData = $this->upload->data();
                $filePath = 'uploads/evaluations/' . $uploadData['file_name'];
                $updateData['attachment'] = $filePath;
            } else {
                // Handle upload error
                $evaluation = $this->Evaluation->getById($id);
                $data['evaluation'] = $evaluation;
                $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['upload_error'] = $this->upload->display_errors();
                $this->load->view('shrm_views/pages/evaluation/edit', $data);
                return;
            }
        }

        // Update the evaluation
        $this->Evaluation->update($id, $updateData);

        // Update assigned users
        $this->Evaluation->clearAssignedUsers($id);
        foreach ($this->input->post('assign_users') as $userId) {
            $this->Evaluation->assignUserToEvaluation($id, $userId);
        }

        // Set success message
        $this->session->set_flashdata('success', 'Work progress evaluation updated successfully!');

        redirect('work-progress');
    }

    // 3) in shrm_controllers/EvaluationController.php
    public function show($encodedId)
    {
        $originalId = $this->decryptId($encodedId);
        if ($originalId === null) {
            show_404();
            return;
        }

        $this->load->model('Evaluation');
        $evaluation = $this->Evaluation->getById($originalId)
            ?: show_404();

        $data = [
            'evaluation' => $evaluation,
            'assigned_users' => $this->Evaluation->getAssignedUsers($originalId),
            'evaluationComments' => $this->Evaluation->getCommentsByEvaluation($originalId),
        ];

        $this->load->view('shrm_views/pages/evaluation/show', $data);
    }


    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $allowedStatuses = ['pending', 'in_progress', 'completed', 'on_hold'];

        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false]);
            return;
        }

        $updated = $this->Evaluation->updateStatus($id, ['status' => $status]);
        echo json_encode(['success' => $updated]);
    }

    public function add_comment()
    {
        $evalId = $this->input->post('evaluation_id');
        $comment = trim($this->input->post('comment'));
        $userId = $this->session->userdata('user_id');

        // Check session role and category, add '00' prefix if both equal 'e'
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if ($role === 'e' && $category === 'e') {
            $userId = '00' . $userId;
        }

        if ($evalId && $comment && $userId) {
            $this->shrm->insert('evaluation_comments', [
                'evaluation_id' => $evalId,
                'user_id' => $userId,
                'comment' => $comment,
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }


    public function comments($id)
    {
        $originalId = $this->decryptId($id);
        $data['evaluation'] = $this->Evaluation->getById($originalId);
        $data['comments'] = $this->Evaluation->getCommentsByEvaluation($originalId);
        $assigned = $this->Evaluation->getAssignedUsers($originalId);
        $data['assigned_users'] = $assigned;
        $this->load->view('shrm_views/pages/evaluation/comments', $data);
    }

    public function get_comments()
    {
        $evaluation_id = $this->input->get('evaluation_id');

        if (empty($evaluation_id)) {
            echo json_encode(['success' => false, 'message' => 'Evaluation ID is required']);
            return;
        }

        $comments = $this->Evaluation->getCommentsByEvaluation($evaluation_id);

        echo json_encode(['success' => true, 'comments' => $comments]);
    }


    protected function decryptId(string $encodedId): ?int
    {
        $secretMultiplier = 15394;

        // base-36 → decimal string
        $decimalString = base_convert($encodedId, 36, 10);

        // must be all digits and divisible by our multiplier
        if (!ctype_digit($decimalString) || ((int)$decimalString) % $secretMultiplier !== 0) {
            return null;
        }

        // recover original ID
        return intdiv((int)$decimalString, $secretMultiplier);
    }
}
