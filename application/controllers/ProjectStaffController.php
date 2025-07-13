<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectStaffController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProjectStaff', 'ProjectStaff');
    }

    public function index()
    {
        $data['users'] = $this->ProjectStaff->getUserList();
        $this->load->view('pages/project_staff/index', $data);
    }
    public function toggle_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $result = $this->ProjectStaff->updateUser($id, ['status' => $status]);

        echo json_encode(['success' => $result]);
    }

    public function create()
    {
        $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
        $this->load->view('pages/project_staff/create', $data);
    }
    public function store()
    {

        try {
            $this->load->library(['upload', 'form_validation']);

            // 1. Form Validation Rules
            $this->form_validation->set_rules('employee_id', 'Employee id', 'required');
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
            $this->form_validation->set_rules('gender', 'Gender', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric');
            $this->form_validation->set_rules('pan', 'PAN Number', 'required');
            $this->form_validation->set_rules('address', 'Address', 'required');
            $this->form_validation->set_rules('department', 'Department', 'required');
            $this->form_validation->set_rules('role', 'Role', 'required');
            $this->form_validation->set_rules('sub_role', 'Sub Role', 'required');
            $this->form_validation->set_rules('reporting_officer', 'Reporting Officer', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            $this->form_validation->set_rules('designation', 'Designation', 'required');
            $this->form_validation->set_rules('join_date', 'Join Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('contract_months', 'Contract Months', 'required|numeric');
            $this->form_validation->set_rules('salary', 'Salary', 'required|numeric');
            $this->form_validation->set_rules('project_name', 'Project Name', 'required');
            $this->form_validation->set_rules('location', 'Location', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
                return $this->load->view('pages/project_staff/create', $data);
                // return redirect('project-staff/create');
            }

            $input = $this->input->post();

            // 2. Start DB Transaction
            $this->db->trans_begin();
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;

            // 3. Prepare User Data
            $userData = [
                'employee_id' => $input['employee_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'dob' => $input['dob'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'address' => $input['address'],
                'department' => $input['department'],
                'role' => $input['role'],
                'category' => $input['sub_role'],
                'gender' => $input['gender'],
                'pan_number' => $input['pan'],
                'phone' => $input['mobile'],
                'reporting_officer_id' => $reportingOfficerId,
                'reporting_officer_name' => $reportingOfficerName,
                'reporting_officer_designation' => $reportingOfficerDesignation,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'Y'
            ];

            // 4. Handle Photo Upload
            if (!empty($_FILES['photo']['name'])) {
                $photoConfig = [
                    'upload_path' => './application/assets/photo/',
                    'allowed_types' => 'jpg|jpeg|png',
                    'encrypt_name' => TRUE
                ];
                $this->upload->initialize($photoConfig);
                if ($this->upload->do_upload('photo')) {
                    $photoData = $this->upload->data();
                    $userData['photo'] = $photoData['file_name'];
                } else {
                    throw new Exception('Photo upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }

            // 5. Handle Signature Upload
            if (!empty($_FILES['signature']['name'])) {
                $signatureConfig = [
                    'upload_path' => './application/assets/signature/',
                    'allowed_types' => 'jpg|jpeg|png',
                    'encrypt_name' => TRUE
                ];
                $this->upload->initialize($signatureConfig);
                if ($this->upload->do_upload('signature')) {
                    $signatureData = $this->upload->data();
                    $userData['signature'] = $signatureData['file_name'];
                } else {
                    throw new Exception('Signature upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }

            // 6. Insert into Users Table
            $userId = $this->User->insertUser($userData);
            if (!$userId) {
                throw new Exception('Failed to insert user.');
            }

            // 7. Prepare Contract Data
            $contractData = [
                'user_id' => $userId,
                'designation' => $input['designation'],
                'join_date' => $input['join_date'],
                'end_date' => $input['end_date'],
                'contract_month' => $input['contract_months'],
                'salary' => $input['salary'],
                'project_name' => $input['project_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'location' => $input['location'],
                'status' => 'Y'
            ];

            // 8. Insert into Contract Table
            $contractResult = $this->ProjectStaff->insertContract($contractData);
            if (!$contractResult) {
                throw new Exception('Failed to insert contract details.');
            }

            // 9. Commit Transaction
            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'User and contract created successfully.');
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }

        redirect('project-staff');
    }
    public function edit($id)
    {
        try {
            $data['user'] = $this->User->getUserById($id);
            if (!$data['user']) {
                $this->session->set_flashdata('error', 'Error: ' . 'USer not found.');
                redirect('project-staff');
            }
            $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
            $data['selectedReportingOfficer'] = $this->ProjectStaff->getSelectedReportingOfficer($id);
            $data['contract'] = $this->User->getContractByUserId($id);

            $this->load->view('pages/project_staff/edit', $data);
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }
    }
    public function update($userId)
    {
        try {
            $this->load->library(['upload', 'form_validation']);

            // 1. Form Validation Rules
            $this->form_validation->set_rules('employee_id', 'Employee id', 'required');
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
            $this->form_validation->set_rules('gender', 'Gender', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric');
            $this->form_validation->set_rules('pan', 'PAN Number', 'required');
            $this->form_validation->set_rules('address', 'Address', 'required');
            $this->form_validation->set_rules('department', 'Department', 'required');
            $this->form_validation->set_rules('role', 'Role', 'required');
            $this->form_validation->set_rules('sub_role', 'Sub Role', 'required');
            $this->form_validation->set_rules('reporting_officer', 'Reporting Officer', 'required');
            $this->form_validation->set_rules('designation', 'Designation', 'required');
            $this->form_validation->set_rules('join_date', 'Join Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('contract_months', 'Contract Months', 'required|numeric');
            $this->form_validation->set_rules('salary', 'Salary', 'required|numeric');
            $this->form_validation->set_rules('project_name', 'Project Name', 'required');
            $this->form_validation->set_rules('location', 'Location', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                return redirect('project-staff/edit/' . $userId);
            }

            $input = $this->input->post();

            // 2. Start DB Transaction
            $this->db->trans_begin();
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;

            // 3. Prepare User Data
            $userData = [
                'employee_id' => $input['employee_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'dob' => $input['dob'],
                'address' => $input['address'],
                'department' => $input['department'],
                'role' => $input['role'],
                'category' => $input['sub_role'],
                'gender' => $input['gender'],
                'pan_number' => $input['pan'],
                'phone' => $input['mobile'],
                'reporting_officer_id' => $reportingOfficerId,
                'reporting_officer_name' => $reportingOfficerName,
                'reporting_officer_designation' => $reportingOfficerDesignation,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Optional password update
            if (!empty($input['password'])) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
                if ($this->form_validation->run() == FALSE) {
                    $this->session->set_flashdata('error', validation_errors());
                    return redirect('project-staff/edit/' . $userId);
                }
                $userData['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            // 4. Handle Photo Upload
            if (!empty($_FILES['photo']['name'])) {
                $photoConfig = [
                    'upload_path' => './application/assets/photo/',
                    'allowed_types' => 'jpg|jpeg|png',
                    'encrypt_name' => TRUE
                ];
                $this->upload->initialize($photoConfig);
                if ($this->upload->do_upload('photo')) {
                    $photoData = $this->upload->data();
                    $userData['photo'] = $photoData['file_name'];
                } else {
                    throw new Exception('Photo upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }

            // 5. Handle Signature Upload
            if (!empty($_FILES['signature']['name'])) {
                $signatureConfig = [
                    'upload_path' => './application/assets/signature/',
                    'allowed_types' => 'jpg|jpeg|png',
                    'encrypt_name' => TRUE
                ];
                $this->upload->initialize($signatureConfig);
                if ($this->upload->do_upload('signature')) {
                    $signatureData = $this->upload->data();
                    $userData['signature'] = $signatureData['file_name'];
                } else {
                    throw new Exception('Signature upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }

            // 6. Update User Table
            $userUpdate = $this->User->updateUser($userId, $userData);
            if (!$userUpdate) {
                throw new Exception('Failed to update user.');
            }

            // 7. Prepare Contract Data
            $contractData = [
                'designation' => $input['designation'],
                'join_date' => $input['join_date'],
                'end_date' => $input['end_date'],
                'contract_month' => $input['contract_months'],
                'salary' => $input['salary'],
                'project_name' => $input['project_name'],
                'updated_at' => date('Y-m-d H:i:s'),
                'location' => $input['location']
            ];

            // 8. Update Contract Table
            $contractResult = $this->ProjectStaff->updateContract($userId, $contractData);
            if (!$contractResult) {
                throw new Exception('Failed to update contract details.');
            }

            // 9. Commit Transaction
            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'User and contract updated successfully.');
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }

        redirect('project-staff');
    }





}