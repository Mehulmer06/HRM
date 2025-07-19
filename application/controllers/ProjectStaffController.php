<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectStaffController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProjectStaff', 'ProjectStaff');
        $this->load->library('upload');
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

            $this->form_validation->set_rules('employee_id', 'Employee id', 'required');
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
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

            }

            $input = $this->input->post();


            $this->db->trans_begin();
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;

            $userData = [
                'employee_id' => (int)$input['employee_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'dob' => $input['dob'],
                'password' => md5($input['password']),
                'address' => $input['address'],
				'professional_email' =>$input['professional_email'],
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

            if (!empty($_FILES['photo']['name'])) {
                $photoConfig = [
                    'upload_path' => './upload/photo/',
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
            if (!empty($_FILES['signature']['name'])) {
                $signatureConfig = [
                    'upload_path' => './upload/signature/',
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
            $userId = $this->User->insertUser($userData);
            if (!$userId) {
                throw new Exception('Failed to insert user.');
            }
			if (!empty($_FILES['offer_latter']['name'])) {
				$offerLetterConfig = [
					'upload_path'   => './upload/offer_latter/',
					'allowed_types' => 'jpg|jpeg|png|pdf',
					'encrypt_name'  => TRUE
				];

				$this->load->library('upload');
				$this->upload->initialize($offerLetterConfig);

				if ($this->upload->do_upload('offer_latter')) {
					$offerLetterData = $this->upload->data();
					$filename = $offerLetterData['file_name'];
				} else {
					throw new Exception('Offer letter upload failed: ' . strip_tags($this->upload->display_errors()));
				}
			} else {
				$filename = null;
			}
			$contractData = [
				'user_id'        => $userId,
				'designation'    => $input['designation'],
				'join_date'      => $input['join_date'],
				'end_date'       => $input['end_date'],
				'contract_month' => $input['contract_months'],
				'salary'         => $input['salary'],
				'project_name'   => $input['project_name'],
				'created_at'     => date('Y-m-d H:i:s'),
				'location'       => $input['location'],
				'offer_latter'   => $filename,
				'status'         => 'active'
			];
            $contractResult = $this->ProjectStaff->insertContract($contractData);
            if (!$contractResult) {
                throw new Exception('Failed to insert contract details.');
            }
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


            $this->db->trans_begin();
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;


            $userData = [
                'employee_id' => $input['employee_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'dob' => $input['dob'],
                'address' => $input['address'],
				'professional_email' =>$input['professional_email'],
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


            if (!empty($input['password'])) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
                if ($this->form_validation->run() == FALSE) {
                    $this->session->set_flashdata('error', validation_errors());
                    return redirect('project-staff/edit/' . $userId);
                }
                $userData['password'] = md5($input['password']);
            }


            if (!empty($_FILES['photo']['name'])) {
                $photoConfig = [
                    'upload_path' => 'upload/photo/',
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


            if (!empty($_FILES['signature']['name'])) {
                $signatureConfig = [
                    'upload_path' => 'upload/signature/',
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


            $userUpdate = $this->User->updateUser($userId, $userData);
            if (!$userUpdate) {
                throw new Exception('Failed to update user.');
            }

			$filename = null;

			if (isset($_FILES['offer_latter']) && !empty($_FILES['offer_latter']['name'])) {
				$offerLetterConfig = [
					'upload_path'   => './upload/offer_latter/',
					'allowed_types' => 'jpg|jpeg|png|pdf',
					'encrypt_name'  => TRUE
				];

				$this->load->library('upload');
				$this->upload->initialize($offerLetterConfig);

				if ($this->upload->do_upload('offer_latter')) {
					$offerLetterData = $this->upload->data();
					$filename = $offerLetterData['file_name'];
				} else {
					throw new Exception('Offer letter upload failed: ' . strip_tags($this->upload->display_errors()));
				}
			}

			$contractData = [
				'designation' => $input['designation'],
				'join_date' => $input['join_date'],
				'end_date' => $input['end_date'],
				'contract_month' => $input['contract_months'],
				'salary' => $input['salary'],
				'project_name' => $input['project_name'],
				'updated_at' => date('Y-m-d H:i:s'),
				'location' => $input['location'],
			];
			if ($filename !== null) {
				$contractData['offer_latter'] = $filename;
			}

			$contractResult = $this->ProjectStaff->updateContract($userId, $contractData);
			if (!$contractResult) {
				throw new Exception('Failed to update contract details.');
			}

			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'User and contract updated successfully.');
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
		}

        redirect('project-staff');
    }
    public function show($id)
    {
        try {
            $data['user'] = $this->User->getUserById($id);
            if (!$data['user']) {
                $this->session->set_flashdata('error', 'Error: User not found.');
                redirect('project-staff');
            }

            $data['contract'] = $this->User->getContractByUserId($id);
            if (!$data['contract']) {
                $this->session->set_flashdata('error', 'Error: Contract details not found.');
                redirect('project-staff');
            }
            $data['contractList'] = $this->ProjectStaff->getContractDetails($id);
            $this->load->view('pages/project_staff/show', $data);
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }

    }
    public function renewContract($id)
    {
        try {
            $this->form_validation->set_rules('modal_designation', 'Designation', 'required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('contract_months', 'Contract Duration', 'required|integer|greater_than[0]');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('salary', 'Salary', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('location', 'Location', 'required');
            $this->form_validation->set_rules('project', 'Project', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,pending,completed]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('project-staff/renewal-contract/' . $id);
                return;
            }

            $this->db->where('user_id', $id);
            $this->db->where('status !=', 'complete');
            $this->db->update('contract_details', ['status' => 'complete']);


            $designation = $this->input->post('modal_designation');
            $start_date = $this->input->post('start_date');
            $contract_months = (int) $this->input->post('contract_months');
            $end_date = $this->input->post('end_date');

			if (!empty($_FILES['offer_latter']['name'])) {
				$offerLetterConfig = [
					'upload_path'   => './upload/offer_latter/',
					'allowed_types' => 'jpg|jpeg|png|pdf',
					'encrypt_name'  => TRUE
				];

				$this->load->library('upload');
				$this->upload->initialize($offerLetterConfig);

				if ($this->upload->do_upload('offer_latter')) {
					$offerLetterData = $this->upload->data();
					$filename = $offerLetterData['file_name'];
				} else {
					throw new Exception('Offer letter upload failed: ' . strip_tags($this->upload->display_errors()));
				}
			} else {
				$filename = null;
			}
            $data = [
                'user_id' => $id,
                'designation' => $designation,
                'join_date' => $start_date,
                'end_date' => $end_date,
                'contract_month' => $contract_months,
                'salary' => $this->input->post('salary'),
                'location' => $this->input->post('location'),
                'project_name' => $this->input->post('project'),
				'offer_latter' => $filename,
                'status' => $this->input->post('status'),
                'created_at' => date('Y-m-d H:i:s')
            ];


            $this->db->insert('contract_details', $data);

            $this->session->set_flashdata('success', 'New contract added successfully.');
            redirect('project-staff/show/' . $id);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }
    }
}
