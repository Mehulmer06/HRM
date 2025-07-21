<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/Project', 'project');
        $this->load->model('shrm_models/User', 'User');
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

    protected function encryptId(int $id): string
    {
        $secretMultiplier = 15394;
        return base_convert($id * $secretMultiplier, 10, 36);
    }

    public function index()
    {
        $projects = $this->project->getAllProjects();

        // Encrypt IDs for frontend use
        foreach ($projects as &$project) {
            $project['encrypted_id'] = $this->encryptId($project['id']);
        }

        $data['projects'] = $projects;
        $this->load->view('shrm_views/pages/project/index', $data);
    }

    public function get_project()
    {
        try {
            $encryptedId = $this->input->post('project_id', true);

            if (empty($encryptedId)) {
                echo json_encode(['success' => false, 'message' => 'Project ID is required']);
                return;
            }

            // Decrypt the ID
            $projectId = $this->decryptId($encryptedId);
            if ($projectId === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
                return;
            }

            $project = $this->project->getProjectById($projectId);

            if (empty($project)) {
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $project
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store()
    {
        try {
            $this->form_validation->set_rules('project_name', 'Project Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run() == false) {
                $data['projects'] = $this->project->getAllProjects();
                $data['validation_errors'] = validation_errors();
                $this->load->view('shrm_views/pages/project/index', $data);
                return;
            }

            $project_name = $this->input->post('project_name', true);
            $status = $this->input->post('status', true);

            // Check if project name already exists
            $isCheck = $this->project->checkProjectExists($project_name);
            if (!empty($isCheck)) {
                $this->session->set_flashdata('error', 'Project with this name already exists!');
                redirect('project');
            }

            $data = [
                'project_name' => $project_name,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->insert('projects', $data);
            $this->session->set_flashdata('success', 'Project created successfully!');
            redirect('project');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('project');
        }
    }

    public function update()
    {
        try {
            $this->form_validation->set_rules('project_name', 'Project Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');
            $this->form_validation->set_rules('project_id', 'Project ID', 'required');

            if ($this->form_validation->run() == false) {
                $data['projects'] = $this->project->getAllProjects();
                $data['validation_errors'] = validation_errors();
                $this->load->view('shrm_views/pages/project/index', $data);
                return;
            }

            $encryptedId = $this->input->post('project_id', true);
            $project_name = $this->input->post('project_name', true);
            $status = $this->input->post('status', true);

            // Decrypt the ID
            $projectId = $this->decryptId($encryptedId);
            if ($projectId === null) {
                $this->session->set_flashdata('error', 'Invalid project ID!');
                redirect('project');
                return;
            }

            // Check if project exists
            $project = $this->project->getProjectById($projectId);
            if (empty($project)) {
                $this->session->set_flashdata('error', 'Project not found!');
                redirect('project');
                return;
            }

            // Check if project name already exists (excluding current project)
            $isCheck = $this->project->checkProjectExistsExcept($project_name, $projectId);
            if (!empty($isCheck)) {
                $this->session->set_flashdata('error', 'Project with this name already exists!');
                redirect('project');
                return;
            }

            $data = [
                'project_name' => $project_name,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $projectId);
            $this->shrm->update('projects', $data);

            $this->session->set_flashdata('success', 'Project updated successfully!');
            redirect('project');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('project');
        }
    }

    public function delete()
    {
        try {
            $encryptedId = $this->input->post('project_id', true);

            if (empty($encryptedId)) {
                $this->session->set_flashdata('error', 'Project ID is required!');
                redirect('project');
                return;
            }

            // Decrypt the ID
            $projectId = $this->decryptId($encryptedId);
            if ($projectId === null) {
                $this->session->set_flashdata('error', 'Invalid project ID!');
                redirect('project');
                return;
            }

            // Check if project exists
            $project = $this->project->getProjectById($projectId);
            if (empty($project)) {
                $this->session->set_flashdata('error', 'Project not found!');
                redirect('project');
                return;
            }

            // Soft delete - update deleted_at timestamp
            $data = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $projectId);
            $this->shrm->update('projects', $data);

            $this->session->set_flashdata('success', 'Project deleted successfully!');
            redirect('project');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('project');
        }
    }

    public function restore()
    {
        try {
            $encryptedId = $this->input->post('project_id', true);

            if (empty($encryptedId)) {
                $this->session->set_flashdata('error', 'Project ID is required!');
                redirect('project');
                return;
            }

            // Decrypt the ID
            $projectId = $this->decryptId($encryptedId);
            if ($projectId === null) {
                $this->session->set_flashdata('error', 'Invalid project ID!');
                redirect('project');
                return;
            }

            // Restore project - set deleted_at to NULL
            $data = [
                'deleted_at' => NULL,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $projectId);
            $this->shrm->update('projects', $data);

            $this->session->set_flashdata('success', 'Project restored successfully!');
            redirect('project');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('project');
        }
    }
}