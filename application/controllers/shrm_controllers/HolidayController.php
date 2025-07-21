<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HolidayController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->load->model('shrm_models/Holiday', 'Holiday');
    }

    public function index($year = null)
    {
        $year = $year ?? date('Y');
        $data['year'] = $year;
        $data['restricted_holidays'] = $this->Holiday->getHolidaysByType($year, 'rh');
        $data['public_holidays'] = $this->Holiday->getHolidaysByType($year, 'fixed');

        $this->load->view('shrm_views/pages/holiday/index', $data);
    }

}
