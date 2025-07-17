<?php

class Leave extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getLeaves($status = null)
    {
        $this->db->select('lr.*, u.name');
        $this->db->from('leave_requests lr');
        $this->db->join('users u', 'u.id = lr.user_id');

        if ($status === 'cancelled') {
            // ✅ Get leaves that have cancellation requests (any status)
            $this->db->where('EXISTS (
            SELECT 1 FROM leave_cancellation_requests lcr 
            WHERE lcr.leave_request_id = lr.id
        )');
        } elseif ($status) {
            $this->db->where('lr.status', $status);
        }

        $this->db->order_by('lr.id', 'DESC');
        return $this->db->get()->result();
    }

    public function insertLeave($data, $days)
    {
        $this->db->insert('leave_requests', $data);
        $leave_request_id = $this->db->insert_id();

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

            $this->db->insert('leave_request_days', $insert);
        }

        return $leave_request_id;
    }

    public function getLeaveBalance($user_id)
    {
        $balance = [];
        $current_year = date('Y');

        // ✅ Total CL Granted for the year
        $this->db->select('COUNT(*) as total_cl');
        $this->db->from('cl_grants');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_granted', 1);
        $this->db->where('YEAR(cl_month)', $current_year);
        $total_cl = $this->db->get()->row();
        $balance['total_cl'] = $total_cl ? (int)$total_cl->total_cl : 0;

        // ✅ Used CL (exclude canceled days)
        $this->db->select('COUNT(*) as used_cl');
        $this->db->from('leave_request_days lrd');
        $this->db->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->db->where('lr.user_id', $user_id);
        $this->db->where('lr.status', 'approved');
        $this->db->where('lrd.leave_type', 'CL');
        $this->db->where('lrd.cl_grant_id IS NOT NULL');
        $this->db->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->db->where('YEAR(lr.created_at)', $current_year);
        $used_cl = $this->db->get()->row();
        $balance['used_cl'] = $used_cl ? (int)$used_cl->used_cl : 0;

        // ✅ Available Extra Days
        $this->db->select('COUNT(*) as available_extra');
        $this->db->from('extra_day_requests');
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 'approved');
        $this->db->where('is_used', 'n');
        $this->db->where('(expired_at IS NULL OR expired_at > CURDATE())');
        $this->db->where('YEAR(work_date)', $current_year);
        $extra = $this->db->get()->row();
        $balance['available_extra'] = $extra ? (int)$extra->available_extra : 0;

        // ✅ Used Extra Days (exclude canceled)
        $this->db->select('COUNT(*) as used_extra');
        $this->db->from('leave_request_days lrd');
        $this->db->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->db->where('lr.user_id', $user_id);
        $this->db->where('lr.status', 'approved');
        $this->db->where('lrd.leave_type', 'Extra');
        $this->db->where('lrd.extra_day_id IS NOT NULL');
        $this->db->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->db->where('YEAR(lr.created_at)', $current_year);
        $used_extra = $this->db->get()->row();
        $balance['used_extra'] = $used_extra ? (int)$used_extra->used_extra : 0;

        // ✅ Used Paid Leaves (exclude canceled)
        $this->db->select('COUNT(*) as used_paid');
        $this->db->from('leave_request_days lrd');
        $this->db->join('leave_requests lr', 'lr.id = lrd.leave_request_id');
        $this->db->where('lr.user_id', $user_id);
        $this->db->where('lr.status', 'approved');
        $this->db->where('lrd.leave_type', 'Paid');
        $this->db->where('lrd.is_canceled !=', 'y'); // ⬅️ exclude canceled
        $this->db->where('YEAR(lr.created_at)', $current_year);
        $used_paid = $this->db->get()->row();
        $balance['used_paid'] = $used_paid ? (int)$used_paid->used_paid : 0;

        // ✅ Remaining CL
        $balance['remaining_cl'] = $balance['total_cl'] - $balance['used_cl'];

        return $balance;
    }


    public function getAvailableExtra($userId)
    {
        return $this->db->where([
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
        return $this->db->where([
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
        return $this->db->where('id', $id)
            ->update('extra_day_requests', ['is_used' => 'y']);
    }

    public function markCLUsed($id)
    {
        return $this->db->where('id', $id)
            ->update('cl_grants', ['is_used' => 'y']);
    }

    public function getById($id)
    {
        // Get leave request with user name
        $this->db->select('lr.*, u.name');
        $this->db->from('leave_requests lr');
        $this->db->join('users u', 'u.id = lr.user_id', 'left');
        $this->db->where('lr.id', $id);
        $leave = $this->db->get()->row();

        // Get leave_request_days entries for this request
        $this->db->from('leave_request_days');
        $this->db->where('leave_request_id', $id);
        $this->db->where('is_canceled !=', 'y');
        $days = $this->db->get()->result();

        return (object)[
            'leave' => $leave,
            'days' => $days
        ];
    }

    public function revertUsedDays($leaveId)
    {
        // Get used leave days
        $days = $this->db->get_where('leave_request_days', ['leave_request_id' => $leaveId])->result();

        foreach ($days as $day) {
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->db->where('id', $day->cl_grant_id)
                    ->update('cl_grants', ['is_used' => 'n']);
            }

            if ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->db->where('id', $day->extra_day_id)
                    ->update('extra_day_requests', ['is_used' => 'n']);
            }
        }
    }

    // ✅ GET PENDING CANCELLATION REQUESTS FOR RO DASHBOARD
    public function getPendingCancellationRequests()
    {
        $this->db->select('
            lcr.id as cancel_id,
            lcr.leave_request_id,
            lcr.leave_request_day_id,
            lcr.requested_at,
            lcr.remarks as cancel_remarks,
            lr.start_date,
            lr.end_date,
            lr.total_days,
            lr.reason as leave_reason,
            u.name as requester_name,
            lrd.leave_date,
            lrd.day_type,
            lrd.half_type,
            lrd.leave_type,
            CASE 
                WHEN lcr.leave_request_day_id IS NULL THEN "Full Leave Cancellation"
                ELSE CONCAT("Partial - ", lrd.leave_date, " (", lrd.day_type, " day)")
            END as cancellation_type
        ');
        $this->db->from('leave_cancellation_requests lcr');
        $this->db->join('leave_requests lr', 'lr.id = lcr.leave_request_id');
        $this->db->join('users u', 'u.id = lcr.requested_by');
        $this->db->join('leave_request_days lrd', 'lrd.id = lcr.leave_request_day_id', 'left');
        $this->db->where('lcr.status', 'pending');
        $this->db->order_by('lcr.requested_at', 'ASC');

        return $this->db->get()->result();
    }

    // ✅ GET CANCELLATION HISTORY
    public function getCancellationHistory($userId = null)
    {
        $this->db->select('lcr.*, lr.start_date, lr.end_date, lr.total_days, u.name as requester_name, ro.name as ro_name, lrd.leave_date, lrd.day_type, lrd.half_type');
        $this->db->from('leave_cancellation_requests lcr');
        $this->db->join('leave_requests lr', 'lr.id = lcr.leave_request_id');
        $this->db->join('users u', 'u.id = lcr.requested_by');
        $this->db->join('users ro', 'ro.id = lcr.ro_id', 'left');
        $this->db->join('leave_request_days lrd', 'lrd.id = lcr.leave_request_day_id', 'left');

        if ($userId) {
            $this->db->where('lcr.requested_by', $userId);
        }

        $this->db->where_in('lcr.status', ['approved', 'rejected']);
        $this->db->order_by('lcr.ro_action_at', 'DESC');
        return $this->db->get()->result();
    }

    // ✅ GET LEAVE WITH CANCELLATION STATUS
    public function getLeaveWithCancellationStatus($leaveId)
    {
        $this->db->select('lr.*, u.name, 
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "pending") as has_pending_cancellation');
        $this->db->from('leave_requests lr');
        $this->db->join('users u', 'u.id = lr.user_id');
        $this->db->where('lr.id', $leaveId);
        return $this->db->get()->row();
    }

    // ✅ CHECK IF CANCELLATION EXISTS
    public function hasPendingCancellation($leaveId, $dayId = null)
    {
        $this->db->where('leave_request_id', $leaveId);
        $this->db->where('status', 'pending');

        if ($dayId) {
            $this->db->where('leave_request_day_id', $dayId);
        } else {
            $this->db->where('leave_request_day_id IS NULL');
        }

        return $this->db->get('leave_cancellation_requests')->num_rows() > 0;
    }

    // ✅ GET CANCELLATION REQUEST BY ID
    public function getCancellationById($cancelId)
    {
        $this->db->select('
            lcr.*,
            lr.start_date,
            lr.end_date,
            lr.total_days,
            lr.reason as leave_reason,
            u.name as requester_name,
            lrd.leave_date,
            lrd.day_type,
            lrd.half_type,
            lrd.leave_type,
            CASE 
                WHEN lcr.leave_request_day_id IS NULL THEN "Full Leave Cancellation"
                ELSE CONCAT("Partial - ", lrd.leave_date, " (", lrd.day_type, " day)")
            END as cancellation_type
        ');
        $this->db->from('leave_cancellation_requests lcr');
        $this->db->join('leave_requests lr', 'lr.id = lcr.leave_request_id');
        $this->db->join('users u', 'u.id = lcr.requested_by');
        $this->db->join('leave_request_days lrd', 'lrd.id = lcr.leave_request_day_id', 'left');
        $this->db->where('lcr.id', $cancelId);
        return $this->db->get()->row();
    }

    // ✅ UPDATE LEAVE STATUS
    public function updateLeaveStatus($leaveId, $status)
    {
        return $this->db->where('id', $leaveId)
            ->update('leave_requests', ['status' => $status]);
    }

    // ✅ GET USER LEAVES WITH CANCELLATION INFO
    public function getUserLeavesWithCancellation($userId)
    {
        $this->db->select('lr.*, 
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "pending") as pending_cancellations,
                          (SELECT COUNT(*) FROM leave_cancellation_requests lcr 
                           WHERE lcr.leave_request_id = lr.id AND lcr.status = "approved") as approved_cancellations');
        $this->db->from('leave_requests lr');
        $this->db->where('lr.user_id', $userId);
        $this->db->order_by('lr.id', 'DESC');
        return $this->db->get()->result();
    }

    // ✅ UPDATE CANCELLATION REQUEST STATUS
    public function updateCancellationStatus($cancelId, $status, $roId, $remark)
    {
        return $this->db->where('id', $cancelId)->update('leave_cancellation_requests', [
            'status' => $status,
            'ro_id' => $roId,
            'ro_action_at' => date('Y-m-d H:i:s'),
            'ro_remark' => $remark
        ]);
    }

    // ✅ RESTORE CL/EXTRA WHEN CANCELLATION IS APPROVED
    public function restoreLeaveDay($dayId)
    {
        $day = $this->db->get_where('leave_request_days', ['id' => $dayId])->row();

        if ($day) {
            if ($day->leave_type === 'CL' && $day->cl_grant_id) {
                $this->db->where('id', $day->cl_grant_id)->update('cl_grants', ['is_used' => 'n']);
            } elseif ($day->leave_type === 'Extra' && $day->extra_day_id) {
                $this->db->where('id', $day->extra_day_id)->update('extra_day_requests', ['is_used' => 'n']);
            }
        }
    }

    // ✅ DELETE SPECIFIC LEAVE DAY
    public function cancelLeaveDay($dayId)
    {
        return $this->db->where('id', $dayId)->update('leave_request_days', ['is_canceled' => 'y']);
    }
    public function cancelAllLeaveDays($leaveId)
    {
        return $this->db->where('leave_request_id', $leaveId)->update('leave_request_days', ['is_canceled' => 'y']);
    }

    // ✅ RECALCULATE LEAVE TOTALS AFTER PARTIAL CANCELLATION
    public function recalculateLeaveTotal($leaveId)
    {
        $this->db->where('leave_request_id', $leaveId);
        $this->db->where('is_canceled !=', 'y');
        $remainingDays = $this->db->get('leave_request_days')->result();

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

        return $this->db->where('id', $leaveId)->update('leave_requests', [
            'total_days' => $totalDays,
            'cl_used' => $clUsed,
            'extra_used' => $extraUsed,
            'paid_used' => $paidUsed,
            'status' => $status
        ]);
    }
    public function getCanceledDays($leaveId)
    {
        $this->db->select('
        lrd.leave_date,
        lrd.day_type,
        lrd.half_type,
        lrd.leave_type,
        lcr.ro_remark as remarks,
        lcr.ro_action_at as canceled_at,
        lcr.status as cancel_status
    ');
        $this->db->from('leave_request_days lrd');
        $this->db->join('leave_cancellation_requests lcr', 'lcr.leave_request_day_id = lrd.id', 'left');
        $this->db->where('lrd.leave_request_id', $leaveId);
        $this->db->where('lrd.is_canceled', 'y');
        $this->db->where('lcr.status', 'approved');
        $this->db->order_by('lrd.leave_date', 'ASC');
        return $this->db->get()->result();
    }


    /**
     * Check if user already has leave application for date range
     */
    public function checkExistingLeave($userId, $startDate, $endDate, $excludeLeaveId = null)
    {
        $this->db->select('lr.id, lr.start_date, lr.end_date, lr.status');
        $this->db->from('leave_requests lr');
        $this->db->where('lr.user_id', $userId);
        $this->db->where('lr.status !=', 'cancelled');
        $this->db->where('lr.status !=', 'rejected');

        // Exclude current leave if updating
        if ($excludeLeaveId) {
            $this->db->where('lr.id !=', $excludeLeaveId);
        }

        // Check for overlapping dates
        $this->db->group_start();
        $this->db->where('lr.start_date <=', $endDate);
        $this->db->where('lr.end_date >=', $startDate);
        $this->db->group_end();

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Check if user already has half-day leave for specific date and time period
     */
    public function checkExistingHalfDayLeave($userId, $date, $timePeriod, $excludeLeaveId = null)
    {
        $this->db->select('lr.id, lrd.leave_date, lrd.half_type, lr.status');
        $this->db->from('leave_requests lr');
        $this->db->join('leave_request_days lrd', 'lr.id = lrd.leave_request_id');
        $this->db->where('lr.user_id', $userId);
        $this->db->where('lr.status !=', 'cancelled');
        $this->db->where('lr.status !=', 'rejected');
        $this->db->where('lrd.leave_date', $date);

        // Exclude current leave if updating
        if ($excludeLeaveId) {
            $this->db->where('lr.id !=', $excludeLeaveId);
        }

        // Check for same time period or full day leave on same date
        $this->db->group_start();
        $this->db->where('lrd.half_type', $timePeriod);
        $this->db->or_where('lrd.day_type', 'full');
        $this->db->group_end();

        $query = $this->db->get();
        return $query->row();
    }
}