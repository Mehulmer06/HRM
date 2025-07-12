<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $this->load->view('pages/dashboard');
        } catch (Exception $e) {
            log_message('error', 'Dashboard loading error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Unable to load the dashboard. Please try again later.');
            redirect('login'); // Or redirect wherever you want user to land after failure
        }
    }
}
