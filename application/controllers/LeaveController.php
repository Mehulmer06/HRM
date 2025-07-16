<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Leave');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        $data['pending_leaves'] = $this->Leave->getLeaves('pending');
        $data['approved_leaves'] = $this->Leave->getLeaves('approved');
        $data['rejected_leaves'] = $this->Leave->getLeaves('rejected');
        $data['cancelled_leaves'] = $this->Leave->getLeaves('cancelled');
        $data['leave_balance'] = $this->Leave->getLeaveBalance($user_id);

        // ✅ ADD PENDING CANCELLATION REQUESTS FOR RO

            $data['pending_cancellations'] = $this->Leave->getPendingCancellationRequests();




        $this->load->view('pages/leave/index', $data);
    }

    public function applyLeave()
    {
        $this->load->model('Leave');
        $userId = $this->session->userdata('user_id');
        $leaveType = $this->input->post('leave_type');

        $leaveData = [
            'user_id' => $userId,
            'reason' => $this->input->post('reason'),
            'address' => $this->input->post('address'),
            'attachment' => '',
            'status' => 'pending'
        ];

        $days = [];
        $totalDays = 0;
        $extraUsed = 0;
        $clUsed = 0;
        $paidUsed = 0;

        // File upload handling
        if (!empty($_FILES['attachment']['name'])) {
            $config['upload_path'] = './uploads/leave_attachments/';
            $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
            $config['max_size'] = 5120;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $leaveData['attachment'] = $this->upload->data('file_name');
            }
        }

        if ($leaveType == 'fullday') {
            $leaveData['start_date'] = $this->input->post('start_date');
            $leaveData['end_date'] = $this->input->post('end_date');

            $start = strtotime($leaveData['start_date']);
            $end = strtotime($leaveData['end_date']);

            // Fetch available extra and CL
            $extra = $this->Leave->getAvailableExtra($userId);
            $cl = $this->Leave->getAvailableCL($userId);

            for ($date = $start; $date <= $end; $date += 86400) {
                $entry = [
                    'leave_date' => date('Y-m-d', $date),
                    'day_type' => 'full'
                ];

                if (!empty($extra)) {
                    $e = array_shift($extra);
                    $entry['leave_type'] = 'Extra';
                    $entry['extra_day_id'] = $e->id;
                    $entry['source_reference'] = $e->work_date;
                    $extraUsed++;
                    $this->Leave->markExtraUsed($e->id);
                } elseif (!empty($cl)) {
                    $c = array_shift($cl);
                    $entry['leave_type'] = 'CL';
                    $entry['cl_grant_id'] = $c->id;
                    $entry['source_reference'] = $c->cl_month;
                    $clUsed++;
                    $this->Leave->markCLUsed($c->id);
                } else {
                    $entry['leave_type'] = 'Paid';
                    $paidUsed++;
                }

                $days[] = $entry;
                $totalDays++;
            }
        } elseif ($leaveType == 'halfday') {
            $leaveData['start_date'] = $this->input->post('half_day_date');
            $leaveData['end_date'] = $this->input->post('half_day_date');
            $totalDays = 0.5;

            $entry = [
                'leave_date' => $this->input->post('half_day_date'),
                'day_type' => 'half',
                'half_type' => $this->input->post('time_period')
            ];

            $extra = $this->Leave->getAvailableExtra($userId);
            $cl = $this->Leave->getAvailableCL($userId);

            if (!empty($extra)) {
                $e = array_shift($extra);
                $entry['leave_type'] = 'Extra';
                $entry['extra_day_id'] = $e->id;
                $entry['source_reference'] = $e->work_date;
                $extraUsed += 0.5;
                $this->Leave->markExtraUsed($e->id);
            } elseif (!empty($cl)) {
                $c = array_shift($cl);
                $entry['leave_type'] = 'CL';
                $entry['cl_grant_id'] = $c->id;
                $entry['source_reference'] = $c->cl_month;
                $clUsed += 0.5;
                $this->Leave->markCLUsed($c->id);
            } else {
                $entry['leave_type'] = 'Paid';
                $paidUsed += 0.5;
            }

            $days[] = $entry;
        }

        // Finalize
        $leaveData['total_days'] = $totalDays;
        $leaveData['cl_used'] = $clUsed;
        $leaveData['extra_used'] = $extraUsed;
        $leaveData['paid_used'] = $paidUsed;
        $this->Leave->insertLeave($leaveData, $days);

        $this->session->set_flashdata('success', 'Leave request submitted successfully.');
        redirect('leave');
    }

    // ✅ UPDATED get_by_id method in LeaveController.php
    public function get_by_id($id)
    {
        $this->load->model('Leave');
        $result = $this->Leave->getById($id);

        if ($result && $result->leave) {
            // ✅ Get canceled days using model method
            $canceled_days = $this->Leave->getCanceledDays($id);

            // ✅ Format canceled days for display
            $formatted_cancelled_days = [];
            foreach ($canceled_days as $day) {
                $formatted_cancelled_days[] = [
                    'date' => $day->leave_date,
                    'day_type' => $day->day_type,
                    'half_type' => $day->half_type,
                    'leave_type' => $day->leave_type,
                    'remarks' => $day->remarks,
                    'canceled_at' => $day->canceled_at
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => $result, // leave + active days only
                'cancelled_days' => $formatted_cancelled_days
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Leave not found.'
            ]);
        }
    }



    public function updateLeave()
    {
        $userId = $this->session->userdata('user_id');
        $leaveId = $this->input->post('leave_id');
        $leaveType = $this->input->post('leave_type');

        // Fetch existing record
        $existing = $this->db->get_where('leave_requests', ['id' => $leaveId])->row();
        if (!$existing || $existing->user_id != $userId) {
            $this->session->set_flashdata('error', 'Invalid leave request.');
            redirect('leave');
        }

        // Build new data
        $leaveData = [
            'reason' => $this->input->post('reason'),
            'address' => $this->input->post('address'),
            'edit_reason' => $this->input->post('edit_reason'),
            'status' => 'pending',
            'attachment' => $existing->attachment
        ];

        // Handle new file
        if (!empty($_FILES['attachment']['name'])) {
            $config['upload_path'] = './uploads/leave_attachments/';
            $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
            $config['max_size'] = 5120;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $leaveData['attachment'] = $this->upload->data('file_name');
            }
        }

        $days = [];
        $totalDays = 0;
        $extraUsed = 0;
        $clUsed = 0;
        $paidUsed = 0;

        $extra = $this->Leave->getAvailableExtra($userId);
        $cl = $this->Leave->getAvailableCL($userId);

        if ($leaveType === 'fullday') {
            $leaveData['start_date'] = $this->input->post('start_date');
            $leaveData['end_date'] = $this->input->post('end_date');

            $start = strtotime($leaveData['start_date']);
            $end = strtotime($leaveData['end_date']);

            for ($date = $start; $date <= $end; $date += 86400) {
                $entry = [
                    'leave_date' => date('Y-m-d', $date),
                    'day_type' => 'full'
                ];

                if (!empty($extra)) {
                    $e = array_shift($extra);
                    $entry['leave_type'] = 'Extra';
                    $entry['extra_day_id'] = $e->id;
                    $entry['source_reference'] = $e->work_date;
                    $extraUsed++;
                    $this->Leave->markExtraUsed($e->id);
                } elseif (!empty($cl)) {
                    $c = array_shift($cl);
                    $entry['leave_type'] = 'CL';
                    $entry['cl_grant_id'] = $c->id;
                    $entry['source_reference'] = $c->cl_month;
                    $clUsed++;
                    $this->Leave->markCLUsed($c->id);
                } else {
                    $entry['leave_type'] = 'Paid';
                    $paidUsed++;
                }

                $days[] = $entry;
                $totalDays++;
            }
        } elseif ($leaveType === 'halfday') {
            $leaveData['start_date'] = $this->input->post('half_day_date');
            $leaveData['end_date'] = $this->input->post('half_day_date');
            $totalDays = 0.5;

            $entry = [
                'leave_date' => $this->input->post('half_day_date'),
                'day_type' => 'half',
                'half_type' => $this->input->post('time_period')
            ];

            if (!empty($extra)) {
                $e = array_shift($extra);
                $entry['leave_type'] = 'Extra';
                $entry['extra_day_id'] = $e->id;
                $entry['source_reference'] = $e->work_date;
                $extraUsed += 0.5;
                $this->Leave->markExtraUsed($e->id);
            } elseif (!empty($cl)) {
                $c = array_shift($cl);
                $entry['leave_type'] = 'CL';
                $entry['cl_grant_id'] = $c->id;
                $entry['source_reference'] = $c->cl_month;
                $clUsed += 0.5;
                $this->Leave->markCLUsed($c->id);
            } else {
                $entry['leave_type'] = 'Paid';
                $paidUsed += 0.5;
            }

            $days[] = $entry;
        }

        $leaveData['total_days'] = $totalDays;
        $leaveData['cl_used'] = $clUsed;
        $leaveData['extra_used'] = $extraUsed;
        $leaveData['paid_used'] = $paidUsed;

        // Update request
        $this->db->where('id', $leaveId)->update('leave_requests', $leaveData);

        // Delete old days and insert new
        $this->db->delete('leave_request_days', ['leave_request_id' => $leaveId]);
        foreach ($days as $day) {
            $day['leave_request_id'] = $leaveId;
            $this->db->insert('leave_request_days', $day);
        }

        $this->session->set_flashdata('success', 'Leave updated successfully.');
        redirect('leave');
    }

    public function take_action()
    {
        $this->load->model('Leave');
        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $leaveId = $this->input->post('leave_id');
        $action = $this->input->post('action'); // approved or rejected
        $remark = $this->input->post('remark');

        if (!$leaveId || !in_array($action, ['approved', 'rejected'])) {
            $this->session->set_flashdata('error', 'Invalid action.');
            redirect('leave');
        }

        $leave = $this->db->get_where('leave_requests', ['id' => $leaveId])->row();
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave not found.');
            redirect('leave');
        }

        // Revert CL/Extra usage if rejected
        if ($action === 'rejected') {
            $this->Leave->revertUsedDays($leaveId);
        }

        // Update leave status
        $updateData = [
            'status' => $action,
            'action_by' => $userId,
            'action_at' => date('Y-m-d H:i:s'),
            'action_remark' => $remark
        ];

        $this->db->where('id', $leaveId)->update('leave_requests', $updateData);

        $this->session->set_flashdata('success', 'Leave has been ' . $action . ' successfully.');
        redirect('leave');
    }

    public function delete($id)
    {
        $userId = $this->session->userdata('user_id');
        $leave = $this->db->get_where('leave_requests', ['id' => $id])->row();

        if (!$leave || $leave->user_id != $userId || $leave->status != 'pending') {
            $this->session->set_flashdata('error', 'Invalid or unauthorized delete request.');
            redirect('leave');
        }

        $days = $this->db->get_where('leave_request_days', ['leave_request_id' => $id])->result();

        foreach ($days as $d) {
            if ($d->leave_type == 'CL' && $d->cl_grant_id) {
                $this->db->set('is_used', 'n')->where('id', $d->cl_grant_id)->update('cl_grants');
            } elseif ($d->leave_type == 'Extra' && $d->extra_day_id) {
                $this->db->set('is_used', 'n')->where('id', $d->extra_day_id)->update('extra_day_requests');
            }
        }

        $this->db->delete('leave_request_days', ['leave_request_id' => $id]);
        $this->db->delete('leave_requests', ['id' => $id]);

        $this->session->set_flashdata('success', 'Leave deleted successfully.');
        redirect('leave');
    }

    // ✅ USER CANCEL REQUEST
    public function cancel()
    {
        $userId = $this->session->userdata('user_id');
        $leaveId = $this->input->post('leave_id');
        $cancelType = $this->input->post('cancel_type'); // full or partial
        $selectedDays = $this->input->post('cancel_days') ?? [];
        $remarks = $this->input->post('remarks') ?? '';

        // Validate leave exists and belongs to user
        $existing = $this->db->get_where('leave_requests', ['id' => $leaveId])->row();
        if (!$existing || $existing->user_id != $userId) {
            $this->session->set_flashdata('error', 'Invalid leave request.');
            redirect('leave');
        }

        // Only allow cancellation for approved leaves
        if ($existing->status !== 'approved') {
            $this->session->set_flashdata('error', 'Only approved leaves can be cancelled.');
            redirect('leave');
        }

        if ($cancelType === 'full') {
            // Check if full cancellation already requested
            $alreadyRequested = $this->db->get_where('leave_cancellation_requests', [
                'leave_request_id' => $leaveId,
                'leave_request_day_id' => null,
                'status' => 'pending'
            ])->num_rows();

            if ($alreadyRequested > 0) {
                $this->session->set_flashdata('error', 'Full cancellation already requested for this leave.');
                redirect('leave');
            }

            // Insert full cancellation request
            $this->db->insert('leave_cancellation_requests', [
                'leave_request_id' => $leaveId,
                'leave_request_day_id' => null, // null means full cancellation
                'requested_by' => $userId,
                'requested_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'remarks' => $remarks
            ]);

        } elseif ($cancelType === 'partial' && !empty($selectedDays)) {
            // Insert partial cancellation requests for selected days
            foreach ($selectedDays as $dayId) {
                // Check if this specific day already has a pending cancellation
                $exists = $this->db->get_where('leave_cancellation_requests', [
                    'leave_request_id' => $leaveId,
                    'leave_request_day_id' => $dayId,
                    'status' => 'pending'
                ])->num_rows();

                if ($exists == 0) {
                    // Get day info for restore references
                    $day = $this->db->get_where('leave_request_days', ['id' => $dayId])->row();

                    $this->db->insert('leave_cancellation_requests', [
                        'leave_request_id' => $leaveId,
                        'leave_request_day_id' => $dayId,
                        'requested_by' => $userId,
                        'requested_at' => date('Y-m-d H:i:s'),
                        'status' => 'pending',
                        'restore_to' => $day ? $day->leave_type : null,
                        'restore_reference' => $day ? $day->source_reference : null,
                        'remarks' => $remarks
                    ]);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Please select at least one day for partial cancellation.');
            redirect('leave');
        }

        $this->session->set_flashdata('success', 'Leave cancellation request submitted successfully. Awaiting RO approval.');
        redirect('leave');
    }

    // ✅ RO ACTION ON CANCELLATION REQUEST
    public function cancel_action()
    {
        $cancelId = $this->input->post('cancel_id');
        $action = $this->input->post('action'); // approved or rejected
        $remark = $this->input->post('remark');
        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        // Check if user is RO or Admin
//        if (!in_array($role, ['RO', 'Admin'])) {
//            $this->session->set_flashdata('error', 'Unauthorized access.');
//            redirect('leave');
//        }

        if (!in_array($action, ['approved', 'rejected'])) {
            $this->session->set_flashdata('error', 'Invalid action.');
            redirect('leave');
        }

        $cancelRequest = $this->db->get_where('leave_cancellation_requests', ['id' => $cancelId])->row();
        if (!$cancelRequest || $cancelRequest->status !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid cancellation request.');
            redirect('leave');
        }

        // If approved, process the cancellation
        if ($action === 'approved') {
            $this->processCancellationApproval($cancelRequest);
        }

        // Update cancellation request status
        $this->Leave->updateCancellationStatus($cancelId, $action, $userId, $remark);

        $this->session->set_flashdata('success', 'Cancellation request ' . $action . ' successfully.');
        redirect('leave');
    }

    // ✅ GET CANCELLATION DETAILS FOR RO ACTION
    public function get_cancellation_details($cancelId)
    {
        $role = $this->session->userdata('role');

        // Check if user is RO or Admin
//        if (!in_array($role, ['RO', 'Admin'])) {
//            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
//            return;
//        }

        $cancellation = $this->Leave->getCancellationById($cancelId);

        if ($cancellation) {
            echo json_encode([
                'success' => true,
                'data' => $cancellation
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Cancellation request not found'
            ]);
        }
    }

    // ✅ PRIVATE METHOD TO PROCESS CANCELLATION APPROVAL
    private function processCancellationApproval($cancelRequest)
    {
        if ($cancelRequest->leave_request_day_id === null) {
            // Full cancellation - cancel entire leave
            $this->cancelFullLeave($cancelRequest->leave_request_id);
        } else {
            // Partial cancellation - cancel specific day
            $this->cancelPartialLeave($cancelRequest->leave_request_id, $cancelRequest->leave_request_day_id);
        }
    }

    // ✅ CANCEL FULL LEAVE
    private function cancelFullLeave($leaveId)
    {
        $days = $this->db->get_where('leave_request_days', ['leave_request_id' => $leaveId])->result();

        foreach ($days as $day) {
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->db->where('id', $day->cl_grant_id)->update('cl_grants', ['is_used' => 'n']);
            } elseif ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->db->where('id', $day->extra_day_id)->update('extra_day_requests', ['is_used' => 'n']);
            }
        }

        $this->Leave->cancelAllLeaveDays($leaveId); // mark all days as is_canceled = 'y'

        $this->db->where('id', $leaveId)->update('leave_requests', [
            'status' => 'cancelled',
            'total_days' => 0,
            'cl_used' => 0,
            'extra_used' => 0,
            'paid_used' => 0
        ]);
    }


    // ✅ CANCEL PARTIAL LEAVE
    private function cancelPartialLeave($leaveId, $dayId)
    {
        // Get the specific day
        $day = $this->db->get_where('leave_request_days', ['id' => $dayId])->row();

        if ($day) {
            // Restore CL or Extra if applicable
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->db->where('id', $day->cl_grant_id)->update('cl_grants', ['is_used' => 'n']);
            } elseif ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->db->where('id', $day->extra_day_id)->update('extra_day_requests', ['is_used' => 'n']);
            }

            // ✅ SOFT CANCEL instead of delete
            $this->Leave->cancelLeaveDay($dayId);

            // Recalculate leave totals
            $this->Leave->recalculateLeaveTotal($leaveId);
        }
    }



}