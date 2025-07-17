<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CasualLeaveController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CasualLeave');
        $this->load->model('User'); // Add this if you have a User model
        $this->load->library('session');
    }

    public function index()
    {
        $data['grants'] = $this->CasualLeave->get_all_grants();

        // Load users for dropdown - adjust this based on your User model
        $data['users'] = $this->get_all_users(); // You'll need to implement this

        $this->load->view('pages/casual_leave/index', $data);
    }

    public function save()
    {
        // Validation
        $this->form_validation->set_rules('user_id', 'Employee', 'required');
        $this->form_validation->set_rules('cl_month', 'CL Month', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('casual-leave');
            return;
        }

        // Convert month input (YYYY-MM) to full date (YYYY-MM-01)
        $cl_month_input = $this->input->post('cl_month');
        $cl_month_date = $cl_month_input . '-01'; // Convert 2025-07 to 2025-07-01

        $data = [
            'user_id' => $this->input->post('user_id'),
            'cl_month' => $cl_month_date, // Store as full date
            'is_granted' => 1,
            'is_used' => 'n',
        ];

        $id = $this->input->post('id');

        try {
            if ($id) {
                // Update existing grant
                $data['is_granted'] = true;
                $result = $this->CasualLeave->update_grant($id, $data);
                $message = 'CL grant updated successfully!';
            } else {
                // Check if CL already exists for this user and month
                $existing = $this->CasualLeave->check_existing_grant($data['user_id'], $data['cl_month']);
                if ($existing) {
                    $this->session->set_flashdata('error', 'CL already granted for this employee and month!');
                    redirect('casual-leave');
                    return;
                }

                // Insert new grant
                $result = $this->CasualLeave->insert_grant($data);
                $message = 'CL granted successfully!';
            }

            if ($result) {
                $this->session->set_flashdata('success', $message);
            } else {
                $this->session->set_flashdata('error', 'Failed to save CL grant. Please try again.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'An error occurred: ' . $e->getMessage());
        }

        redirect('casual-leave');
    }

    public function get_grant($id)
    {
        $data = $this->CasualLeave->get_grant_by_id($id);

        if ($data) {
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);

        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Grant not found']);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->CasualLeave->delete_grant($id);

            if ($result) {
                $this->session->set_flashdata('success', 'CL grant deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to delete CL grant.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'An error occurred while deleting: ' . $e->getMessage());
        }

        redirect('casual-leave');
    }

    // Helper method to get all users - adjust based on your User model
    private function get_all_users()
    {
        return $this->db->select('id, name')
            ->from('users')
            ->where('status', 'Y') // Adjust based on your user table structure
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }
}