<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProjectStaffController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProjectStaff','ProjectStaff');
    }

    public function index()
    {
        $data['users'] = $this->ProjectStaff->getUserList();
        $this->load->view('pages/project_staff/index', $data);
    }
    public function toggle_status() {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $result = $this->ProjectStaff->updateUser($id, ['status' => $status]);

        echo json_encode(['success' => $result]);
    }

    public function create()
    {
        $data['reportingOfficers'] = $this->ProjectStaff->getReportingOfficers();
        $this->load->view('pages/project_staff/create',$data);
    }


}