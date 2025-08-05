<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectStaffController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->load->model('shrm_models/ProjectStaff', 'ProjectStaff');
        $this->load->model('shrm_models/User', 'User');
        $this->load->model('shrm_models/ProjectStaff', 'ProjectStaff');
        $this->load->model('shrm_models/Project', 'Project');
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->library('upload');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['users'] = $this->ProjectStaff->getUserList();

        $this->load->view('shrm_views/pages/project_staff/index', $data);
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
        $data['projects'] = $this->Project->getActiveProjects();
        $this->load->view('shrm_views/pages/project_staff/create', $data);
    }

    public function store()
    {
        try {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('role', 'Role', 'required');
            $this->form_validation->set_rules('sub_role', 'Sub Role', 'required');
            $this->form_validation->set_rules('reporting_officer', 'Reporting Officer', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
                return $this->load->view('shrm_views/pages/project_staff/create', $data);
            }

            $input = $this->input->post();


            $this->shrm->trans_begin();
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;
//            $password = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()', 8)), 0, 8);
            $password = '12345678';

            $userData = [
                'employee_id' => $input['employee_id'] ?? '',
                'name' => ucwords(strtolower($input['name'])),
                'email' => $input['email'],
                'dob' => $input['dob'],
                'password' => md5($password),
                'address' => $input['address'],
                'professional_email' => $input['professional_email'],
                'department' => $input['department'],
                'role' => $input['role'],
                'category' => $input['sub_role'],
                'gender' => $input['gender'],
                'pan_number' => $input['pan'],
                'ro_flag' => $input['ro_flag'],
                'phone' => $input['mobile'],
                'reporting_officer_id' => $reportingOfficerId,
                'reporting_officer_name' => $reportingOfficerName,
                'reporting_officer_designation' => $reportingOfficerDesignation,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'Y'
            ];

            if (!empty($_FILES['photo']['name'])) {
                $photoUploadPath = FCPATH . 'uploads/photo/';
                if (!is_dir($photoUploadPath)) {
                    mkdir($photoUploadPath, 0755, true);
                }

                $photoConfig = [
                    'upload_path' => $photoUploadPath,
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

            // === SIGNATURE UPLOAD ===
            if (!empty($_FILES['signature']['name'])) {
                $signatureUploadPath = FCPATH . 'uploads/signature/';
                if (!is_dir($signatureUploadPath)) {
                    mkdir($signatureUploadPath, 0755, true);
                }

                $signatureConfig = [
                    'upload_path' => $signatureUploadPath,
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

            // === INSERT USER ===
            $userId = $this->User->insertUser($userData);
            if (!$userId) {
                throw new Exception('Failed to insert user.');
            }

            // === OFFER LETTER UPLOAD ===
            if (isset($_FILES['offer_latter']) && !empty($_FILES['offer_latter']['name'])) {
                $offerLetterUploadPath = FCPATH . 'uploads/offer_latter/';
                if (!is_dir($offerLetterUploadPath)) {
                    mkdir($offerLetterUploadPath, 0755, true);
                }

                if (!is_writable($offerLetterUploadPath)) {
                    throw new Exception('Upload directory is not writable: ' . $offerLetterUploadPath);
                }

                $offerLetterConfig = [
                    'upload_path' => $offerLetterUploadPath,
                    'allowed_types' => 'jpg|jpeg|png|pdf',
                    'encrypt_name' => TRUE
                ];

                $this->upload->initialize($offerLetterConfig);

                if ($this->upload->do_upload('offer_latter')) {
                    $offerLetterData = $this->upload->data();
                    $filename = $offerLetterData['file_name'];

                    // Save offer letter filename to DB (if needed)
                    $this->User->saveOfferLetter($userId, $filename); // optional
                } else {
                    throw new Exception('Offer letter upload failed: ' . strip_tags($this->upload->display_errors()));
                }
            }
            if (!empty($input['designation']) && !empty($input['contract_months'])) {
                $contractData = [
                    'user_id' => $userId,
                    'designation' => $input['designation'],
					'organization'=>$input['organization'],
                    'join_date' => $input['join_date'],
                    'end_date' => $input['end_date'],
                    'contract_month' => $input['contract_months'],
                    'salary' => $input['salary'],
                    'project_name' => $input['project_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'location' => $input['location'],
                    'offer_latter' => $filename ?? '',
                    'status' => 'active'
                ];
                $contractResult = $this->ProjectStaff->insertContract($contractData);
                if (!$contractResult) {
                    throw new Exception('Failed to insert contract details.');
                }
            }
            $assets = [
                'user_id' => $userId,
                'sitting_location' => $input['sitting_location'],
                'asset_detail' => $input['assets'],
				'ip_address' => $input['ip_address'],
				'connection_type' => $input['connection_type'],
				'antivirus' => $input['antivirus'],
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'Y'
            ];
            $assets = $this->ProjectStaff->insertAssets($assets);

            // Guest House
            if (!empty($input['guest_join_date'])) {
                $quarter = [
                    'user_id' => $userId,
                    'guest_join_date' => $input['guest_join_date'],
                    'guest_status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),

                ];
                $quarters = $this->ProjectStaff->insertQuarter($quarter);
            }
            //Mail
            $template = "shrm_views/pages/email/send_email";
            $input['subject'] = 'IHRMS Login Credentials';
            $input['reportingOfficerName'] = $reportingOfficerName;
            $input['reportingOfficerDesignation'] = $reportingOfficerDesignation;
            $input['password'] = $password;

//            $this->sendEmial($input, $template);

            $this->shrm->trans_commit();
            $this->session->set_flashdata('success', 'User and contract created successfully.');
        } catch (\Exception $e) {
            $this->shrm->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }

        redirect('project-staff');
    }

    public function edit($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if ($id === null) {
                $this->session->set_flashdata('error', 'Invalid user ID.');
                redirect('project-staff');
                return;
            }

            $data['user'] = $this->User->getUserById($id);
            if (!$data['user']) {
                $this->session->set_flashdata('error', 'Error: ' . 'USer not found.');
                redirect('project-staff');
            }
            $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
            $data['projects'] = $this->Project->getActiveProjects();
            $data['assets'] = $this->ProjectStaff->get_assets_by_id($id);
            $data['selectedReportingOfficer'] = $this->ProjectStaff->getSelectedReportingOfficer($id);
            $data['contract'] = $this->User->getContractByUserId($id);
            $this->load->view('shrm_views/pages/project_staff/edit', $data);
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }
    }

    public function update($userId)
    {
        try {
            // Form validation
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('role', 'Role', 'required');
            $this->form_validation->set_rules('sub_role', 'Sub Role', 'required');
            $this->form_validation->set_rules('reporting_officer', 'Reporting Officer', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                return redirect('project-staff/edit/' . $userId);
            }

            $input = $this->input->post();
            $input = $this->security->xss_clean($input);

            $this->shrm->trans_begin();

            // Parse reporting officer data
            $reportingParts = explode(",'.',", $input['reporting_officer']);
            $reportingOfficerId = isset($reportingParts[0]) ? trim($reportingParts[0]) : null;
            $reportingOfficerName = isset($reportingParts[1]) ? trim($reportingParts[1]) : null;
            $reportingOfficerDesignation = isset($reportingParts[2]) ? trim($reportingParts[2]) : null;

            // Prepare user data
            $userData = [
                'employee_id' => $input['employee_id'] ?? '',
                'name' => ucwords(strtolower($input['name'])),
                'email' => $input['email'],
                'dob' => $input['dob'] ?? null,
                'address' => $input['address'] ?? '',
                'department' => $input['department'] ?? '',
                'role' => $input['role'],
                'category' => $input['sub_role'],
                'professional_email' => $input['professional_email'] ?? '',
                'gender' => $input['gender'] ?? '',
                'pan_number' => $input['pan'] ?? '',
                'phone' => $input['mobile'] ?? '',
                'ro_flag' => $input['ro_flag'] ?? '',
                'reporting_officer_id' => $reportingOfficerId,
                'reporting_officer_name' => $reportingOfficerName,
                'reporting_officer_designation' => $reportingOfficerDesignation,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle password update
            if (!empty($input['password'])) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
                if ($this->form_validation->run() == FALSE) {
                    $this->session->set_flashdata('error', validation_errors());
                    return redirect('project-staff/edit/' . $userId);
                }
                $userData['password'] = md5($input['password']);
            }

            // Handle photo upload
            if (!empty($_FILES['photo']['name'])) {
                $photoUploadPath = FCPATH . 'uploads/photo/';
                if (!is_dir($photoUploadPath)) {
                    mkdir($photoUploadPath, 0755, true);
                }

                $photoConfig = [
                    'upload_path' => $photoUploadPath,
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

            // Handle signature upload
            if (!empty($_FILES['signature']['name'])) {
                $signatureUploadPath = FCPATH . 'uploads/signature/';
                if (!is_dir($signatureUploadPath)) {
                    mkdir($signatureUploadPath, 0755, true);
                }

                $signatureConfig = [
                    'upload_path' => $signatureUploadPath,
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

            // Update user
            $userUpdate = $this->User->updateUser($userId, $userData);
            if (!$userUpdate) {
                throw new Exception('Failed to update user.');
            }

            // Handle assets data
            $checkAssets = $this->ProjectStaff->checkAssete($userId);
            $assetsData = [
                'user_id' => $userId,
                'sitting_location' => $input['sitting_location'] ?? '',
                'asset_detail' => $input['assets'] ?? '',
				'ip_address' => $input['ip_address']??'',
				'connection_type' => $input['connection_type']??'',
				'antivirus' => $input['antivirus']??'',
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 'Y'
            ];

            if (!empty($checkAssets)) {
                $assetsResult = $this->ProjectStaff->updateAssets($userId, $assetsData);
            } else {
                $assetsData['created_at'] = date('Y-m-d H:i:s');
                $assetsResult = $this->ProjectStaff->insertAssets($assetsData);
            }

            if (!$assetsResult) {
                throw new Exception('Failed to save asset details.');
            }
            $this->shrm->trans_commit();
            $this->session->set_flashdata('success', 'User updated successfully.');

        } catch (\Exception $e) {
            $this->shrm->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }

        redirect('project-staff');
    }


    public function show($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if ($id === null) {
                $this->session->set_flashdata('error', 'Invalid user ID.');
                redirect('project-staff');
                return;
            }
            $data['user'] = $this->User->getUserById($id);
            if (!$data['user']) {
                $this->session->set_flashdata('error', 'Error: User not found.');
                redirect('project-staff');
            }

            $data['contract'] = $this->User->getContractByUserId($id);
			$data['experienceCalculation'] = $this->User->experienceCalculation($id);
			//print_r($data['experienceCalculation']);exit;
            $data['projects'] = $this->Project->getActiveProjects();
            $data['contractList'] = $this->ProjectStaff->getContractDetails($id);
            $data['quarters'] = $this->ProjectStaff->getQuartersDetails($id);
			$data['assetsDetails'] = $this->User->getAssetsByUserId($id);
            $this->load->view('shrm_views/pages/project_staff/show', $data);
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }
    }

    public function renewContract($id)
    {
        $encId = $this->encryptId($id);
        try {
			$this->form_validation->set_rules('organization', 'organization', 'required');
            $this->form_validation->set_rules('modal_designation', 'Designation', 'required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('contract_months', 'Contract Duration', 'required|integer|greater_than[0]');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('salary', 'Salary', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('location', 'Location', 'required');
            $this->form_validation->set_rules('project_name', 'project name', 'required');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('project-staff/renewal-contract/' . $encId);
                return;
            }

            $designation = $this->input->post('modal_designation');
			$organization = $this->input->post('organization');
            $start_date = $this->input->post('start_date');
            $contract_months = (int)$this->input->post('contract_months');
            $end_date = $this->input->post('end_date');
			$contract_id = $this->input->post('contract_id');

			if(empty($contract_id)){
				$this->shrm->where('user_id', $id);
				$this->shrm->where('status !=', 'complete');
				$this->shrm->update('contract_details', ['status' => 'complete']);
			}

            $filename = '';
            if (isset($_FILES['offer_latter']) && !empty($_FILES['offer_latter']['name'])) {
                $uploadPath = FCPATH . 'uploads/offer_latter/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Check if directory is writable
                if (!is_writable($uploadPath)) {
                    throw new Exception('Upload directory is not writable: ' . $uploadPath);
                }

                $offerLetterConfig = [
                    'upload_path' => $uploadPath,
                    'allowed_types' => 'jpg|jpeg|png|pdf',
                    'encrypt_name' => TRUE
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
            $data = [
                'user_id' => $id,
                'designation' => $designation,
				'organization' =>$organization,
                'join_date' => $start_date,
                'end_date' => $end_date,
                'contract_month' => $contract_months,
                'salary' => $this->input->post('salary'),
                'location' => $this->input->post('location'),
                'project_name' => $this->input->post('project_name'),
//                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
			if (!empty($filename)) {
				$data['offer_latter'] = $filename;
			}

			if(!empty($contract_id)){
				$this->shrm->where('id', $contract_id);
				$this->shrm->update('contract_details', $data);
				$this->session->set_flashdata('success', 'Contract updated successfully.');
			}else{
                $data['status'] = 'active';
				$this->shrm->insert('contract_details', $data);
				$this->session->set_flashdata('success', 'New contract added successfully.');
			}
            redirect('project-staff/show/' . $encId);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            redirect('project-staff');
        }
    }

    public function sendEmial($data, $template)
    {

        $this->load->config('email');
        $this->load->library('email');

        $this->email->from('no-reply@myapp.com', 'MyApp');
        $this->email->to($data['email']);

        $this->email->subject($data['subject']);

        $message = $this->load->view($template, $data, TRUE);
        $this->email->message($message);

        if ($this->email->send()) {
            echo 'Email sent successfully!';
        } else {
            echo 'Email failed.<br>';
            echo $this->email->print_debugger();
        }
    }

    public function renewQuarter($userId)
    {
        try {
            $encId = $this->encryptId($userId);
            $this->form_validation->set_rules('guest_join_date', 'join date', 'required');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('project-staff/renewal-quarter/' . $encId);
                return;
            }
            $this->shrm->where('user_id', $userId);
            $this->shrm->where('guest_status !=', 'inactive');
            $this->shrm->update('guesthouses', [
                'guest_end_date' => date('Y-m-d H:i:s'),
                'guest_status' => 'inactive',
            ]);

            // Guest House
            $input = $this->input->post();
            $quarter = [
                'user_id' => $userId,
                'guest_join_date' => $input['guest_join_date'],
                'guest_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),

            ];
            $quarters = $this->ProjectStaff->insertQuarter($quarter);
            $this->session->set_flashdata('success', 'New Quarter Assign successfully.');
            redirect('project-staff/show/' . $encId);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', ' Internal Server error.');
            redirect('project-staff');
        }

    }

    public function editQuarter($quarter_id)
    {
        try {
            $this->form_validation->set_rules('guest_end_date', 'End Date', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('project-staff/view/' . $this->input->post('user_id'));
            }

            // Optional: ensure it's active
            $quarter = $this->shrm->get_where('guesthouses', ['id' => $quarter_id])->row();
            if (!$quarter || strtolower($quarter->guest_status) !== 'active') {
                $this->session->set_flashdata('error', 'Only active quarters can be edited.');
                redirect('project-staff/view/' . $quarter->user_id);
            }

            $data = [
                'guest_end_date' => $this->input->post('guest_end_date'),
                'guest_status' => 'inactive',
            ];

            $this->shrm->where('id', $quarter_id)->update('guesthouses', $data);
            $this->session->set_flashdata('success', 'Quarter updated successfully.');
            redirect('project-staff/show/' . $quarter->user_id);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', ' Internal Server error.');
            redirect('project-staff');
        }
    }

    protected function decryptId(string $encodedId): ?int
    {
        $secretMultiplier = 15395;

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
        $secretMultiplier = 15395;
        return base_convert($id * $secretMultiplier, 10, 36);
    }
}
