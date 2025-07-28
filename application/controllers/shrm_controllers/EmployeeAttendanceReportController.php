<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EmployeeAttendanceReportController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/User', 'User');
    }

    public function index()
    {
        $data['users'] = $this->User->get_users_with_latest_contract();
        $this->load->view('shrm_views/pages/employee-attendance-report/index',$data);
    }
}