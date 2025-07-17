<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ExtraDayRequestController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ExtraDayRequest', 'ExtraDayRequest');
        $this->load->model('Holiday', 'Holiday');
    }

    public function index()
    {
        $userId = $this->session->userdata('user_id');

        $data['requests'] = $this->ExtraDayRequest->get_by_user($userId);
        $data['public_holidays'] = $this->Holiday->getHolidaysByType(2025, 'fixed');
        $holidayDates = array_map(function ($h) {
            return $h->holiday_list_date;
        }, $data['public_holidays']);
        $data['holiday_dates_js'] = json_encode($holidayDates);

        $this->load->view('pages/leave/extra-day/index', $data);
    }


    public function create()
    {
        $data = $this->input->post();
        $data['user_id'] = $this->session->userdata('user_id');
        $data['created_at'] = date('Y-m-d H:i:s');

        $requestedDate = $data['work_date'] ?? null;

        // Validate: date required
        if (!$requestedDate) {
            $this->session->set_flashdata('error', 'Date is required.');
            redirect('extra-day-requests');
            return;
        }

        // Check day of week
        $dayOfWeek = date('w', strtotime($requestedDate)); // 0 = Sunday, 6 = Saturday

        // Get holidays for the requested year
        $year = date('Y', strtotime($requestedDate));
        $publicHolidays = $this->Holiday->getHolidaysByType($year, 'fixed');
        $holidayDates = array_map(function ($h) {
            return $h->holiday_list_date;
        }, $publicHolidays);

        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
        $isHoliday = in_array($requestedDate, $holidayDates);

        if (!$isWeekend && !$isHoliday) {
            $this->session->set_flashdata('error', 'Only Saturdays, Sundays, or Public Holidays are allowed.');
            redirect('extra-day-requests');
            return;
        }

        // ✅ Valid request
        $this->ExtraDayRequest->create($data);
        $this->session->set_flashdata('success', 'Extra Day Request submitted successfully.');
        redirect('extra-day-requests');
    }


    public function get_request($id)
    {
        $data = $this->ExtraDayRequest->get_by_id($id);
        echo json_encode($data);
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->ExtraDayRequest->update($id, $post);
        redirect('ExtraDayRequestController/index');
    }

    public function delete()
    {
        $id = $this->input->post('id');
        $this->ExtraDayRequest->delete($id);
        redirect('ExtraDayRequestController/index');
    }

    public function roIndex()
    {
//        $user_id = $this->session->userdata('user_id');
        $user_id = 8;
        $data['pending'] = $this->ExtraDayRequest->get_pending_by_ro($user_id);
        $data['approved'] = $this->ExtraDayRequest->get_approved_by_ro($user_id);
        $this->load->view('pages/leave/ro-extra-day/index', $data);
    }

    public function action_request()
    {
        $request_id = $this->input->post('request_id');
        $remarks = $this->input->post('remarks');
        $action = $this->input->post('action'); // 'approved' or 'rejected'
        $ro_id = 8;
//        $ro_id = $this->session->userdata('user_id');

        // Validate action
        if (!in_array($action, ['approved', 'rejected'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            return;
        }

        // Validate required fields
        if (empty($request_id) || empty($remarks) || empty($action)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $result = $this->ExtraDayRequest->update_request_status($request_id, $action, $remarks, $ro_id);

        if ($result) {
            $message = $action === 'approved' ? 'Request approved successfully' : 'Request rejected successfully';
            echo json_encode(['status' => 'success', 'message' => $message]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to ' . $action . ' request']);
        }
    }


}
