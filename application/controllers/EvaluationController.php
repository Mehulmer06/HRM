<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EvaluationController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Evaluation', 'Evaluation');
    }

    public function index()
    {
        $data['evaluations'] = $this->Evaluation->getAllEvaluations();
        $this->load->view('pages/evaluation/index', $data);
    }

    public function create()
    {
        $data['users'] = $this->Evaluation->getUserList();
        $this->load->view('pages/evaluation/create', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $data['users'] = $this->Evaluation->getUserList();
            $data['validation_errors'] = validation_errors(); // optional
            $this->load->view('pages/evaluation/create', $data);
            return;
        }

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
            'reporting_officer_id' => 8,
            'reporting_officer_name' => 'Dr Abhishek Kumar',
            'status' => 'pending'
        ];

        $evaluationId = $this->Evaluation->insertEvaluation($evaluationData);

        if ($evaluationId && !empty($assignedUsers)) {
            foreach ($assignedUsers as $userId) {
                $this->Evaluation->assignUserToEvaluation($evaluationId, $userId);
            }
        }

        redirect('evaluation'); // redirect to evaluation list
    }


    public function edit($id)
    {
        $evaluation = $this->Evaluation->getById($id);
        if (!$evaluation) {
            show_404();
        }

        $assigned = $this->Evaluation->getAssignedUserIds($id);
        $data = [
            'evaluation' => $evaluation,
            'assigned_user_ids' => array_column($assigned, 'user_id'),
            'users' => $this->Evaluation->getUserList()
        ];

        $this->load->view('pages/evaluation/edit', $data);
    }


    public function update($id)
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');

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
            $data['users'] = $this->Evaluation->getUserList();
            $data['validation_errors'] = validation_errors();
            $this->load->view('pages/evaluation/edit', $data);
            return;
        }

        $this->Evaluation->update($id, [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'status' => 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->Evaluation->clearAssignedUsers($id);
        foreach ($this->input->post('assign_users') as $userId) {
            $this->Evaluation->assignUserToEvaluation($id, $userId);
        }

        redirect('evaluation');
    }

    public function show($id)
    {
        $evaluation = $this->Evaluation->getById($id);
        if (!$evaluation) {
            show_404();
        }

        $assigned = $this->Evaluation->getAssignedUsers($id); // array of user objects

        $data['evaluation'] = $evaluation;
        $data['assigned_users'] = $assigned;
        $this->load->view('pages/evaluation/show', $data);
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

        if ($evalId && $comment && $userId) {
            $this->db->insert('evaluation_comments', [
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
        $data['evaluation'] = $this->Evaluation->getById($id);
        $data['comments'] = $this->Evaluation->getCommentsByEvaluation($id);
        $assigned = $this->Evaluation->getAssignedUsers($id);
        $data['assigned_users'] = $assigned;
        $this->load->view('pages/evaluation/comments', $data);
    }
    public function get_comments() {
        $evaluation_id = $this->input->get('evaluation_id');

        if (empty($evaluation_id)) {
            echo json_encode(['success' => false, 'message' => 'Evaluation ID is required']);
            return;
        }

        $comments = $this->Evaluation->getCommentsByEvaluation($evaluation_id);

        echo json_encode(['success' => true, 'comments' => $comments]);
    }

}