<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->load->model('shrm_models/Leave', 'Leave');
    }

    public function index()
    {
        try {
            $data['out_of_office'] = $this->Leave->get_todays_out_of_office();
            $data['upcoming_leave'] = $this->Leave->get_upcoming_leave();
//            echo "<pre>";
//            print_r($data);
//            exit();
            $this->load->view('shrm_views/pages/dashboard', $data);
        } catch (Exception $e) {
            log_message('error', 'Dashboard loading error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Unable to load the dashboard. Please try again later.');
            redirect('shrm/login');
        }
    }

    public function test()
    {
        $this->load->view('shrm_views/pages/staff_attendance');
    }
}