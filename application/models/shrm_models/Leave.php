<?php

class Leave extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getLeaves($status = null)
    {
        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if ($status === 'cancelled') {
            // For cancelled leaves, we need to show only leaves that have approved cancellations
            $this->shrm->select('lr.*, u.name as user_name, lr.reporting_officer_id, "approved" as cancellation_status');
            $this->shrm->from('leave_requests lr');
            $this->shrm->join('users u', 'u.id = lr.user_id');
            $this->shrm->join('leave_cancellation_requests lcr', 'lcr.leave_request_id = lr.id');
            $this->shrm->where('lcr.status', 'approved');
            $this->shrm->group_by('lr.id'); // ✅ This prevents duplicate entries
        } else {
            // For other statuses, use LEFT JOIN to avoid duplicates
            $this->shrm->select('lr.*, u.name as user_name, lr.reporting_officer_id, 
                            COALESCE(MAX(lcr.status), "none") as cancellation_status');
            $this->shrm->from('leave_requests lr');
            $this->shrm->join('users u', 'u.id = lr.user_id');
            $this->shrm->join('leave_cancellation_requests lcr', 'lcr.leave_request_id = lr.id', 'left');

            if ($status) {
                $this->shrm->where('lr.status', $status);
            }

            $this->shrm->group_by('lr.id'); // ✅ This prevents duplicate entries
        }

        // Apply role-based filters
        if ($role === 'e' && $category === 'e') {
            // Reporting Officer sees only their team
            $this->shrm->where('lr.reporting_officer_id', $userId);
        } elseif ($role === 'employee' && $category !== 'admin') {
            // Regular employee sees their own leaves
            $this->shrm->where('lr.user_id', $userId);
        } elseif ($role === 'employee' && $category === 'admin') {
            // Admin employee sees all approved leaves only
            if ($status !== 'cancelled') {
                $this->shrm->where('lr.status', 'approved');
            }
        }

        $this->shrm->order_by('lr.id', 'DESC');
        $leaveRequests = $this->shrm->get()->result();

        // Fetch reporting officer names from external DB
        $officerIds = array_filter(array_unique(array_column($leaveRequests, 'reporting_officer_id')));
        $officerMap = [];

        if (!empty($officerIds)) {
            $officers = $this->db
                ->select('id, name')
                ->from('user')
                ->where_in('id', $officerIds)
                ->get()
                ->result();

            foreach ($officers as $officer) {
                $officerMap[$officer->id] = $officer->name;
            }
        }

        foreach ($leaveRequests as &$lr) {
            $lr->reporting_officer_name = $officerMap[$lr->reporting_officer_id] ?? null;
        }

        return $leaveRequests;
    }


    public function insertLeave($data, $days)
    {
        $this->shrm->insert('leave_requests', $data);
        $leave_request_id = $this->shrm->insert_id();

        foreach ($days as $day) {
            $insert = [
                'leave_request_id' => $leave_request_id,
                'leave_date' => $day['leave_date'],
                'leave_type' => $day['leave_type'],
                'day_type' => $day['day_type'],
                'half_type' => isset($day['half_type']) ? $day['half_type'] : null,
                'source_reference' => isset($day['source_reference']) ? $day['source_reference'] : null,
                'cl_grant_id' => isset($day['cl_grant_id']) ? $day['cl_grant_id'] : null,
                'extra_day_id' => isset($day['extra_day_id']) ? $day['extra_day_id'] : null
            ];

            $this->shrm->insert('leave_request_days', $insert);
        }

        return $leave_request_id;
    }

    public function getLeaveBalance($user_id)
    {
        $balance = [];
        $current_year = date('Y');

        // ✅ Total CL Granted for the year
        $this->shrm->select('COUNT(*) as total_cl');
        $this->shrm->from('cl_grants');
        $this->shrm->where('user_id', $user_id);
        $this->shrm->where('is_granted', 1);
        $this->shrm->where('YEAR(cl_month)', $current_year);
        $total_cl = $this->shrm->get()->row();
        $balance['total_cl'] = $total_cl ? (int)$total_cl->total_cl : 0;

        // ✅ Used CL (exclude canceled days) - Calculate based on day_type
        $this->shrm->select('SUM(CASE WHEN lrd.day_type = "half" THEN 0.5 ELSE 1 END) as used_cl');
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->shrm->where('lr.user_id', $user_id);
        $this->shrm->where('lr.status', 'approved');
        $this->shrm->where('lrd.leave_type', 'CL');
        $this->shrm->where('lrd.cl_grant_id IS NOT NULL');
        $this->shrm->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->shrm->where('YEAR(lr.created_at)', $current_year);
        $used_cl = $this->shrm->get()->row();
        $balance['used_cl'] = $used_cl ? (float)$used_cl->used_cl : 0;

        // ✅ Available Extra Days
        $this->shrm->select('COUNT(*) as available_extra');
        $this->shrm->from('extra_day_requests');
        $this->shrm->where('user_id', $user_id);
        $this->shrm->where('status', 'approved');
        $this->shrm->where('is_used', 'n');
        $this->shrm->where('(expired_at IS NULL OR expired_at > CURDATE())');
        $this->shrm->where('YEAR(work_date)', $current_year);
        $extra = $this->shrm->get()->row();
        $balance['available_extra'] = $extra ? (int)$extra->available_extra : 0;

        // ✅ Used Extra Days (exclude canceled) - Calculate based on day_type
        $this->shrm->select('SUM(CASE WHEN lrd.day_type = "half" THEN 0.5 ELSE 1 END) as used_extra');
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->shrm->where('lr.user_id', $user_id);
        $this->shrm->where('lr.status', 'approved');
        $this->shrm->where('lrd.leave_type', 'Extra');
        $this->shrm->where('lrd.extra_day_id IS NOT NULL');
        $this->shrm->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->shrm->where('YEAR(lr.created_at)', $current_year);
        $used_extra = $this->shrm->get()->row();
        $balance['used_extra'] = $used_extra ? (float)$used_extra->used_extra : 0;

        // ✅ Used Paid Leaves (exclude canceled) - Calculate based on day_type
        $this->shrm->select('SUM(CASE WHEN lrd.day_type = "half" THEN 0.5 ELSE 1 END) as used_paid');
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->shrm->where('lr.user_id', $user_id);
        $this->shrm->where('lr.status', 'approved');
        $this->shrm->where('lrd.leave_type', 'Paid');
        $this->shrm->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->shrm->where('YEAR(lr.created_at)', $current_year);
        $used_paid = $this->shrm->get()->row();
        $balance['used_paid'] = $used_paid ? (float)$used_paid->used_paid : 0;

        // ✅ Remaining CL
        $balance['remaining_cl'] = $balance['total_cl'] - $balance['used_cl'];

        return $balance;
    }


    public function getAvailableExtra($userId)
    {
        return $this->shrm->where([
            'user_id' => $userId,
            'status' => 'approved',
            'is_used' => 'n'
        ])
            ->order_by('work_date', 'ASC')
            ->get('extra_day_requests')
            ->result();
    }

    public function getAvailableCL($userId)
    {
        return $this->shrm->where([
            'user_id' => $userId,
            'is_granted' => 1,
            'is_used' => 'n'
        ])
            ->order_by('cl_month', 'ASC')
            ->get('cl_grants')
            ->result();
    }

    public function markExtraUsed($id)
    {
        return $this->shrm->where('id', $id)
            ->update('extra_day_requests', ['is_used' => 'y']);
    }

    public function markCLUsed($id)
    {
        return $this->shrm->where('id', $id)
            ->update('cl_grants', ['is_used' => 'y']);
    }

    public function getById($id)
    {
        // Get leave request with user name
        $this->shrm->select('lr.*, u.name');
        $this->shrm->from('leave_requests lr');
        $this->shrm->join('users u', 'u.id = lr.user_id', 'left');
        $this->shrm->where('lr.id', $id);
        $leave = $this->shrm->get()->row();

        // Get leave_request_days entries for this request
        $this->shrm->from('leave_request_days');
        $this->shrm->where('leave_request_id', $id);
        $this->shrm->where('is_canceled !=', 'y');
        $days = $this->shrm->get()->result();

        return (object)[
            'leave' => $leave,
            'days' => $days
        ];
    }
    public function getByIdLeavePDF($id)
    {
        // 1) Get leave request with user info
        $this->shrm->select('lr.*, u.name, u.employee_id, u.signature, u.reporting_officer_name,u.department');
        $this->shrm->from('leave_requests lr');
        $this->shrm->join('users u', 'u.id = lr.user_id', 'left');
        $this->shrm->where('lr.id', $id);
        $leave = $this->shrm->get()->row();

        // 2) Get leave_request_days entries (excluding cancelled)
        $this->shrm->from('leave_request_days');
        $this->shrm->where('leave_request_id', $id);
        $this->shrm->where('is_canceled !=', 'y');
        $days = $this->shrm->get()->result();

        // 3) Get the latest contract_details for this user
        $this->shrm->select('designation, join_date, end_date, contract_month, project_name, salary, location, offer_latter, status');
        $this->shrm->from('contract_details');
        $this->shrm->where('user_id', $leave->user_id);
        // you can choose to order by created_at or id; here we use created_at desc:
        $this->shrm->order_by('created_at', 'DESC');
        $this->shrm->limit(1);
        $contract = $this->shrm->get()->row();

        // 4) Return everything in one object
        return (object)[
            'leave'    => $leave,
            'days'     => $days,
            'contract' => $contract
        ];
    }


    public function revertUsedDays($leaveId)
    {
        // Get used leave days
        $days = $this->shrm->get_where('leave_request_days', ['leave_request_id' => $leaveId])->result();

        foreach ($days as $day) {
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->shrm->where('id', $day->cl_grant_id)
                    ->update('cl_grants', ['is_used' => 'n']);
            }

            if ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->shrm->where('id', $day->extra_day_id)
                    ->update('extra_day_requests', ['is_used' => 'n']);
            }
        }
    }

    // ✅ GET PENDING CANCELLATION REQUESTS FOR RO DASHBOARD
    public function getPendingCancellationRequests()
    {
        $query = "
        SELECT 
            MIN(lcr.id) as cancel_id,
            lcr.leave_request_id,
            MIN(lcr.requested_at) as requested_at,
            MIN(lcr.remarks) as cancel_remarks,
            lr.start_date,
            lr.end_date,
            lr.total_days,
            lr.reason as leave_reason,
            u.name as requester_name,
            COUNT(lcr.id) as cancellation_count,
            GROUP_CONCAT(DISTINCT lrd.leave_date ORDER BY lrd.leave_date ASC) as cancelled_dates,
            GROUP_CONCAT(DISTINCT CONCAT(lrd.leave_date, ' (', lrd.day_type, ' day', 
                CASE WHEN lrd.half_type IS NOT NULL THEN CONCAT(' - ', lrd.half_type) ELSE '' END, ')')
                ORDER BY lrd.leave_date ASC SEPARATOR ', ') as cancelled_days_detail,
            CASE 
                WHEN COUNT(lcr.id) = 1 AND MIN(lcr.leave_request_day_id) IS NULL THEN 'Full Leave Cancellation'
                WHEN COUNT(lcr.id) = 1 AND MIN(lcr.leave_request_day_id) IS NOT NULL THEN 'Partial - Single Day'
                ELSE CONCAT('Partial - ', COUNT(lcr.id), ' Days')
            END as cancellation_type
        FROM leave_cancellation_requests lcr
        JOIN leave_requests lr ON lr.id = lcr.leave_request_id
        JOIN users u ON u.id = lcr.requested_by
        LEFT JOIN leave_request_days lrd ON lrd.id = lcr.leave_request_day_id
        WHERE lcr.status = 'pending'
        GROUP BY lcr.leave_request_id
        ORDER BY MIN(lcr.requested_at) ASC
    ";

        return $this->shrm->query($query)->result();
    }


    // ✅ GET CANCELLATION HISTORY
    public function getCancellationHistory($userId = null)
    {
        $this->shrm->select('lcr.*, lr.start_date, lr.end_date, lr.total_days, u.name as requester_name, ro.name as ro_name, lrd.leave_date, lrd.day_type, lrd.half_type');
        $this->shrm->from('leave_cancellation_requests lcr');
        $this->shrm->join('leave_requests lr', 'lr.id = lcr.leave_request_id');
        $this->shrm->join('users u', 'u.id = lcr.requested_by');
        $this->shrm->join('users ro', 'ro.id = lcr.ro_id', 'left');
        $this->shrm->join('leave_request_days lrd', 'lrd.id = lcr.leave_request_day_id', 'left');

        if ($userId) {
            $this->shrm->where('lcr.requested_by', $userId);
        }

        $this->shrm->where_in('lcr.status', ['approved', 'rejected']);
        $this->shrm->order_by('lcr.ro_action_at', 'DESC');
        return $this->shrm->get()->result();
    }

    // ✅ GET LEAVE WITH CANCELLATION STATUS
    public function getLeaveWithCancellationStatus($leaveId)
    {
        $this->shrm->select('lr.*, u.name, 
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "pending") as has_pending_cancellation');
        $this->shrm->from('leave_requests lr');
        $this->shrm->join('users u', 'u.id = lr.user_id');
        $this->shrm->where('lr.id', $leaveId);
        return $this->shrm->get()->row();
    }

    // ✅ CHECK IF CANCELLATION EXISTS
    public function hasPendingCancellation($leaveId, $dayId = null)
    {
        $this->shrm->where('leave_request_id', $leaveId);
        $this->shrm->where('status', 'pending');

        if ($dayId) {
            $this->shrm->where('leave_request_day_id', $dayId);
        } else {
            $this->shrm->where('leave_request_day_id IS NULL');
        }

        return $this->shrm->get('leave_cancellation_requests')->num_rows() > 0;
    }

    // ✅ GET CANCELLATION REQUEST BY ID
    public function getCancellationById($cancelId)
    {
        // First get the leave_request_id from the cancel_id
        $this->shrm->select('leave_request_id');
        $this->shrm->from('leave_cancellation_requests');
        $this->shrm->where('id', $cancelId);
        $cancelRequest = $this->shrm->get()->row();

        if (!$cancelRequest) {
            return null;
        }

        // Now get all cancellation requests for this leave_request_id using Active Record
        $this->shrm->select('
        lcr.leave_request_id,
        MIN(lcr.requested_at) as requested_at,
        MIN(lcr.remarks) as remarks,
        lr.start_date,
        lr.end_date,
        lr.total_days,
        lr.reason as leave_reason,
        u.name as requester_name,
        COUNT(lcr.id) as total_cancellations,
        GROUP_CONCAT(DISTINCT lrd.leave_date ORDER BY lrd.leave_date ASC) as all_cancelled_dates,
        CASE 
            WHEN COUNT(lcr.id) = 1 AND MIN(lcr.leave_request_day_id) IS NULL THEN "Full Leave Cancellation"
            WHEN COUNT(lcr.id) = 1 AND MIN(lcr.leave_request_day_id) IS NOT NULL THEN "Partial - Single Day"
            ELSE CONCAT("Partial - ", COUNT(lcr.id), " Days")
        END as cancellation_type
    ', FALSE); // FALSE parameter prevents CI from escaping the SQL functions

        $this->shrm->from('leave_cancellation_requests lcr');
        $this->shrm->join('leave_requests lr', 'lr.id = lcr.leave_request_id');
        $this->shrm->join('users u', 'u.id = lcr.requested_by');
        $this->shrm->join('leave_request_days lrd', 'lrd.id = lcr.leave_request_day_id', 'left');
        $this->shrm->where('lcr.leave_request_id', $cancelRequest->leave_request_id);
        $this->shrm->where('lcr.status', 'pending');
        $this->shrm->group_by('lcr.leave_request_id');

        return $this->shrm->get()->row();
    }


    public function getCancellationIdsByLeaveRequest($leaveRequestId)
    {
        $this->shrm->select('id');
        $this->shrm->from('leave_cancellation_requests');
        $this->shrm->where('leave_request_id', $leaveRequestId);
        $this->shrm->where('status', 'pending');
        $result = $this->shrm->get()->result();

        return array_column($result, 'id');
    }

    // ✅ UPDATE LEAVE STATUS
    public function updateLeaveStatus($leaveId, $status)
    {
        return $this->shrm->where('id', $leaveId)
            ->update('leave_requests', ['status' => $status]);
    }

    // ✅ GET USER LEAVES WITH CANCELLATION INFO
    public function getUserLeavesWithCancellation($userId)
    {
        $this->shrm->select('lr.*, 
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "pending") as pending_cancellations,
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "approved") as approved_cancellations');
        $this->shrm->from('leave_requests lr');
        $this->shrm->where('lr.user_id', $userId);
        $this->shrm->order_by('lr.id', 'DESC');
        return $this->shrm->get()->result();
    }

    // ✅ UPDATE CANCELLATION REQUEST STATUS
    public function updateCancellationStatus($cancelId, $status, $roId, $remark)
    {
        return $this->shrm->where('id', $cancelId)->update('leave_cancellation_requests', [
            'status' => $status,
            'ro_id' => $roId,
            'ro_action_at' => date('Y-m-d H:i:s'),
            'ro_remark' => $remark
        ]);
    }

    // ✅ RESTORE CL/EXTRA WHEN CANCELLATION IS APPROVED
    public function restoreLeaveDay($dayId)
    {
        $day = $this->shrm->get_where('leave_request_days', ['id' => $dayId])->row();

        if ($day) {
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->shrm->where('id', $day->cl_grant_id)->update('cl_grants', ['is_used' => 'n']);
            } elseif ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->shrm->where('id', $day->extra_day_id)->update('extra_day_requests', ['is_used' => 'n']);
            }
        }
    }

    // ✅ DELETE SPECIFIC LEAVE DAY
    public function cancelLeaveDay($dayId)
    {
        return $this->shrm->where('id', $dayId)->update('leave_request_days', ['is_canceled' => 'y']);
    }

    public function cancelAllLeaveDays($leaveId)
    {
        return $this->shrm->where('leave_request_id', $leaveId)->update('leave_request_days', ['is_canceled' => 'y']);
    }

    // ✅ RECALCULATE LEAVE TOTALS AFTER PARTIAL CANCELLATION
    public function recalculateLeaveTotal($leaveId)
    {
        $this->shrm->where('leave_request_id', $leaveId);
        $this->shrm->where('is_canceled !=', 'y');
        $remainingDays = $this->shrm->get('leave_request_days')->result();

        $totalDays = 0;
        $clUsed = 0;
        $extraUsed = 0;
        $paidUsed = 0;

        foreach ($remainingDays as $day) {
            $dayCount = ($day->day_type === 'half') ? 0.5 : 1;
            $totalDays += $dayCount;

            if ($day->leave_type === 'CL') {
                $clUsed += $dayCount;
            } elseif ($day->leave_type === 'Extra') {
                $extraUsed += $dayCount;
            } else {
                $paidUsed += $dayCount;
            }
        }

        // If no days remaining, mark as cancelled
        $status = ($totalDays == 0) ? 'cancelled' : 'approved';

        return $this->shrm->where('id', $leaveId)->update('leave_requests', [
            'total_days' => $totalDays,
            'cl_used' => $clUsed,
            'extra_used' => $extraUsed,
            'paid_used' => $paidUsed,
            'status' => $status
        ]);
    }

    public function getCanceledDays($leaveId)
    {
        $this->shrm->select('
        lrd.leave_date,
        lrd.day_type,
        lrd.half_type,
        lrd.leave_type,
        lcr.ro_remark as remarks,
        lcr.ro_action_at as canceled_at,
        lcr.status as cancel_status
    ');
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_cancellation_requests lcr', 'lcr.leave_request_day_id = lrd.id', 'left');
        $this->shrm->where('lrd.leave_request_id', $leaveId);
        $this->shrm->where('lrd.is_canceled', 'y');
        $this->shrm->where('lcr.status', 'approved');
        $this->shrm->order_by('lrd.leave_date', 'ASC');
        return $this->shrm->get()->result();
    }


    /**
     * Check if user already has leave application for date range
     */
    public function checkExistingLeave($userId, $startDate, $endDate, $excludeLeaveId = null)
    {
        $this->shrm->select('lr.id, lr.start_date, lr.end_date, lr.status');
        $this->shrm->from('leave_requests lr');
        $this->shrm->where('lr.user_id', $userId);
        $this->shrm->where('lr.status !=', 'cancelled');
        $this->shrm->where('lr.status !=', 'rejected');

        // Exclude current leave if updating
        if ($excludeLeaveId) {
            $this->shrm->where('lr.id !=', $excludeLeaveId);
        }

        // Check for overlapping dates
        $this->shrm->group_start();
        $this->shrm->where('lr.start_date <=', $endDate);
        $this->shrm->where('lr.end_date >=', $startDate);
        $this->shrm->group_end();

        $query = $this->shrm->get();
        return $query->row();
    }

    /**
     * Check if user already has half-day leave for specific date and time period
     */
    public function checkExistingHalfDayLeave($userId, $date, $timePeriod, $excludeLeaveId = null)
    {
        $this->shrm->select('lr.id, lrd.leave_date, lrd.half_type, lr.status');
        $this->shrm->from('leave_requests lr');
        $this->shrm->join('leave_request_days lrd', 'lr.id = lrd.leave_request_id');
        $this->shrm->where('lr.user_id', $userId);
        $this->shrm->where('lr.status !=', 'cancelled');
        $this->shrm->where('lr.status !=', 'rejected');
        $this->shrm->where('lrd.leave_date', $date);

        // Exclude current leave if updating
        if ($excludeLeaveId) {
            $this->shrm->where('lr.id !=', $excludeLeaveId);
        }

        // Check for same time period or full day leave on same date
        $this->shrm->group_start();
        $this->shrm->where('lrd.half_type', $timePeriod);
        $this->shrm->or_where('lrd.day_type', 'full');
        $this->shrm->group_end();

        $query = $this->shrm->get();
        return $query->row();
    }


    public function get_todays_out_of_office()
    {
        $today = date('Y-m-d');
        $ro_id = '';
        if($this->session->userdata('role') === 'employee'){
            $ro_id = $this->session->userdata('ro_id');
        }else{
            $ro_id = $this->session->userdata('user_id');
        }

        $this->shrm->select('u.employee_id, u.name, lrd.leave_date, lrd.day_type, lrd.half_type, cd.designation');
        $this->shrm->distinct();
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->shrm->join('users u', 'u.id = lr.user_id');
        $this->shrm->join('contract_details cd', 'cd.user_id = u.id', 'left');

        $this->shrm->where('lr.status', 'approved');
        $this->shrm->where('lrd.is_canceled', 'n');
        $this->shrm->where('u.status', 'Y');
        $this->shrm->where('lr.reporting_officer_id', $ro_id);

        // Check for approved cancellation requests
        $this->shrm->where('NOT EXISTS (SELECT 1 FROM leave_cancellation_requests lcr 
WHERE lcr.leave_request_id = lr.id 
AND lcr.status = "approved" 
AND (lcr.leave_request_day_id IS NULL OR lcr.leave_request_day_id = lrd.id))');

        $this->shrm->order_by('u.name', 'ASC');
        $this->shrm->order_by('lrd.leave_date', 'ASC');

        $query = $this->shrm->get();
        $results = $query->result_array();

        $processed_results = [];
        foreach ($results as $row) {
            $key = $row['employee_id'];
            if (!isset($processed_results[$key])) {
                $processed_results[$key] = [
                    'employee_id' => $row['employee_id'],
                    'name' => $row['name'],
                    'total_days' => 0, // Initialize to 0, will calculate below
                    'nature' => 'Leave',
                    'designation' => $row['designation'],
                    'dates' => []
                ];
            }

            $leave_details = ($row['day_type'] == 'full') ? 'Full Day' : 'Half Day (' . ucfirst(str_replace('_', ' ', $row['half_type'])) . ')';

            // Check if this date already exists to prevent duplicates
            $date_exists = false;
            foreach ($processed_results[$key]['dates'] as $existing_date) {
                if ($existing_date['leave_date'] == $row['leave_date']) {
                    $date_exists = true;
                    break;
                }
            }

            // Only add if date doesn't already exist
            if (!$date_exists) {
                $processed_results[$key]['dates'][] = [
                    'leave_date' => $row['leave_date'],
                    'leave_details' => $leave_details
                ];

                // Calculate total days: full day = 1, half day = 0.5
                $day_value = ($row['day_type'] == 'full') ? 1 : 0.5;
                $processed_results[$key]['total_days'] += $day_value;
            }
        }

        // Format total_days to show .00 for whole numbers
        foreach ($processed_results as &$result) {
            $result['total_days'] = number_format($result['total_days'], 2);
        }

        return array_values($processed_results);
    }

// Fixed get_upcoming_leave function
    public function get_upcoming_leave()
    {
        $today = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+30 days'));

        // Get all upcoming leave days for employees
        $this->shrm->select('u.employee_id, u.name, lr.start_date, lrd.leave_date, lrd.day_type, lrd.half_type, cd.designation');
        $this->shrm->distinct();
        $this->shrm->from('leave_request_days lrd');
        $this->shrm->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->shrm->join('users u', 'u.id = lr.user_id');
        $this->shrm->join('contract_details cd', 'cd.user_id = u.id', 'left');

        $this->shrm->where('lr.status', 'approved');
        $this->shrm->where('lrd.is_canceled', 'n');
        $this->shrm->where('u.status', 'Y');
        $this->shrm->where('lr.reporting_officer_id', $this->session->userdata('user_id'));
        $this->shrm->where('lrd.leave_date >', $today);
        $this->shrm->where('lrd.leave_date <=', $end_date);

        // Check for approved cancellation requests
        $this->shrm->where('NOT EXISTS (SELECT 1 FROM leave_cancellation_requests lcr 
WHERE lcr.leave_request_id = lr.id 
AND lcr.status = "approved" 
AND (lcr.leave_request_day_id IS NULL OR lcr.leave_request_day_id = lrd.id))');

        $this->shrm->order_by('lr.start_date', 'ASC');
        $this->shrm->order_by('u.name', 'ASC');
        $this->shrm->order_by('lrd.leave_date', 'ASC');

        $query = $this->shrm->get();
        $results = $query->result_array();

        $processed_results = [];
        foreach ($results as $row) {
            $key = $row['employee_id'] . '_' . $row['start_date'];

            if (!isset($processed_results[$key])) {
                $processed_results[$key] = [
                    'employee_id' => $row['employee_id'],
                    'name' => $row['name'],
                    'total_days' => 0, // Initialize to 0, will calculate below
                    'nature' => 'Leave',
                    'designation' => $row['designation'],
                    'start_date' => $row['start_date'],
                    'dates' => []
                ];
            }

            $leave_details = ($row['day_type'] == 'full') ? 'Full Day' : 'Half Day (' . ucfirst(str_replace('_', ' ', $row['half_type'])) . ')';

            // Check if this date already exists to prevent duplicates
            $date_exists = false;
            foreach ($processed_results[$key]['dates'] as $existing_date) {
                if ($existing_date['leave_date'] == $row['leave_date']) {
                    $date_exists = true;
                    break;
                }
            }

            // Only add if date doesn't already exist
            if (!$date_exists) {
                $processed_results[$key]['dates'][] = [
                    'leave_date' => $row['leave_date'],
                    'leave_details' => $leave_details
                ];

                // Calculate total days: full day = 1, half day = 0.5
                $day_value = ($row['day_type'] == 'full') ? 1 : 0.5;
                $processed_results[$key]['total_days'] += $day_value;
            }
        }

        // Format total_days to show .00 for whole numbers
        foreach ($processed_results as &$result) {
            $result['total_days'] = number_format($result['total_days'], 2);
        }

        return array_values($processed_results);
    }
}