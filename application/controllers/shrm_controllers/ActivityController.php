<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ActivityController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/Activity', 'activity');
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
        $activities = $this->activity->getAllActivities();

        // Encrypt IDs for frontend use
        foreach ($activities as &$activity) {
            $activity['encrypted_id'] = $this->encryptId($activity['id']);
        }

        $data['activities'] = $activities;
        $this->load->view('shrm_views/pages/activity/index', $data);
    }

    public function get_activity()
    {
        try {
            $encryptedId = $this->input->post('activity_id', true);

            if (empty($encryptedId)) {
                echo json_encode(['success' => false, 'message' => 'Activity ID is required']);
                return;
            }

            // Decrypt the ID
            $activityId = $this->decryptId($encryptedId);
            if ($activityId === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid activity ID']);
                return;
            }

            $activity = $this->activity->getActivityById($activityId);

            if (empty($activity)) {
                echo json_encode(['success' => false, 'message' => 'Activity not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $activity
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store()
    {
        try {
            $this->form_validation->set_rules('name', 'Activity Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');

            if ($this->form_validation->run() == false) {
                $data['activities'] = $this->activity->getAllActivities();
                $data['validation_errors'] = validation_errors();
                $this->load->view('shrm_views/pages/activity/index', $data);
                return;
            }

            $name = $this->input->post('name', true);
            $status = $this->input->post('status', true);

            // Check if activity name already exists
            $isCheck = $this->activity->checkActivityExists($name);
            if (!empty($isCheck)) {
                $this->session->set_flashdata('error', 'Activity with this name already exists!');
                redirect('activity');
            }

            $data = [
                'name' => $name,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->insert('activities', $data);
            $this->session->set_flashdata('success', 'Activity created successfully!');
            redirect('activity');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('activity');
        }
    }

    public function update()
    {
        try {
            $this->form_validation->set_rules('name', 'Activity Name', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required');
            $this->form_validation->set_rules('activity_id', 'Activity ID', 'required');

            if ($this->form_validation->run() == false) {
                $data['activities'] = $this->activity->getAllActivities();
                $data['validation_errors'] = validation_errors();
                $this->load->view('shrm_views/pages/activity/index', $data);
                return;
            }

            $encryptedId = $this->input->post('activity_id', true);
            $name = $this->input->post('name', true);
            $status = $this->input->post('status', true);

            // Decrypt the ID
            $activityId = $this->decryptId($encryptedId);
            if ($activityId === null) {
                $this->session->set_flashdata('error', 'Invalid activity ID!');
                redirect('activity');
                return;
            }

            // Check if activity exists
            $activity = $this->activity->getActivityById($activityId);
            if (empty($activity)) {
                $this->session->set_flashdata('error', 'Activity not found!');
                redirect('activity');
                return;
            }

            // Check if activity name already exists (excluding current activity)
            $isCheck = $this->activity->checkActivityExistsExcept($name, $activityId);
            if (!empty($isCheck)) {
                $this->session->set_flashdata('error', 'Activity with this name already exists!');
                redirect('activity');
                return;
            }

            $data = [
                'name' => $name,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $activityId);
            $this->shrm->update('activities', $data);

            $this->session->set_flashdata('success', 'Activity updated successfully!');
            redirect('activity');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('activity');
        }
    }

    public function delete()
    {
        try {
            $encryptedId = $this->input->post('activity_id', true);

            if (empty($encryptedId)) {
                $this->session->set_flashdata('error', 'Activity ID is required!');
                redirect('activity');
                return;
            }

            // Decrypt the ID
            $activityId = $this->decryptId($encryptedId);
            if ($activityId === null) {
                $this->session->set_flashdata('error', 'Invalid activity ID!');
                redirect('activity');
                return;
            }

            // Check if activity exists
            $activity = $this->activity->getActivityById($activityId);
            if (empty($activity)) {
                $this->session->set_flashdata('error', 'Activity not found!');
                redirect('activity');
                return;
            }

            // Soft delete - update deleted_at timestamp
            $data = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $activityId);
            $this->shrm->update('activities', $data);

            $this->session->set_flashdata('success', 'Activity deleted successfully!');
            redirect('activity');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('activity');
        }
    }

    public function restore()
    {
        try {
            $encryptedId = $this->input->post('activity_id', true);

            if (empty($encryptedId)) {
                $this->session->set_flashdata('error', 'Activity ID is required!');
                redirect('activity');
                return;
            }

            // Decrypt the ID
            $activityId = $this->decryptId($encryptedId);
            if ($activityId === null) {
                $this->session->set_flashdata('error', 'Invalid activity ID!');
                redirect('activity');
                return;
            }

            // Restore activity - set deleted_at to NULL
            $data = [
                'deleted_at' => NULL,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->shrm->where('id', $activityId);
            $this->shrm->update('activities', $data);

            $this->session->set_flashdata('success', 'Activity restored successfully!');
            redirect('activity');

        } catch (\Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('activity');
        }
    }
}