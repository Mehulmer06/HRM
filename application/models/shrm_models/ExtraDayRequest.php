<?php

class ExtraDayRequest extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function get_pending_by_ro($ro_id)
    {
        return $this->shrm
            ->select('edr.*, u.name as employee_name, u.employee_id')
            ->from('extra_day_requests edr')
            ->join('users u', 'u.id = edr.user_id')
            ->where('edr.deleted_at IS NULL')
            ->where('edr.status', 'pending')
            ->where('u.reporting_officer_id', $ro_id)
            ->order_by('edr.created_at', 'DESC')
            ->get()
            ->result();
    }
    public function check_existing_date($user_id, $work_date, $exclude_id = null)
    {
        $this->shrm->where('user_id', $user_id);
        $this->shrm->where('work_date', $work_date);
        $this->shrm->where('deleted_at IS NULL', null, false); // ignore soft-deleted

        if ($exclude_id) {
            $this->shrm->where('id !=', $exclude_id);
        }

        $query = $this->shrm->get('extra_day_requests');
        return $query->num_rows() > 0;
    }


    public function get_approved_by_ro($ro_id)
    {
        return $this->shrm
            ->select('edr.*, u.name as employee_name, u.employee_id')
            ->from('extra_day_requests edr')
            ->join('users u', 'u.id = edr.user_id')
            ->where('edr.deleted_at IS NULL')
            ->where('edr.status !=', 'pending')
            ->where('u.reporting_officer_id', $ro_id) // Filter by employees under this RO
            ->order_by('edr.created_at', 'DESC')
            ->get()
            ->result();
    }

// Method to update request status (for approve/reject functionality)
    public function update_request_status($request_id, $status, $remarks, $ro_id)
    {
        // First verify that this RO has authority over this request
        $request = $this->shrm
            ->select('edr.id')
            ->from('extra_day_requests edr')
            ->join('users u', 'u.id = edr.user_id')
            ->where('edr.id', $request_id)
            ->where('u.reporting_officer_id', $ro_id)
            ->where('edr.deleted_at IS NULL')
            ->get()
            ->row();

        if (!$request) {
            return false; // RO doesn't have authority over this request
        }

        $data = array(
            'status' => $status,
            'ro_remark' => $remarks,
            'approved_by' => $ro_id,
            'approved_at' => date('Y-m-d H:i:s')
        );

        return $this->shrm
            ->where('id', $request_id)
            ->update('extra_day_requests', $data);
    }

// Method to get single request details for a specific RO
    public function get_request_by_id($request_id, $ro_id)
    {
        return $this->shrm
            ->select('edr.*, u.name as employee_name, u.employee_id')
            ->from('extra_day_requests edr')
            ->join('users u', 'u.id = edr.user_id')
            ->where('edr.id', $request_id)
            ->where('u.reporting_officer_id', $ro_id)
            ->where('edr.deleted_at IS NULL')
            ->get()
            ->row();
    }

// Method to get all requests for a specific employee under this RO
    public function get_employee_requests($employee_id, $ro_id)
    {
        return $this->shrm
            ->select('edr.*, u.name as employee_name, u.employee_id')
            ->from('extra_day_requests edr')
            ->join('users u', 'u.id = edr.user_id')
            ->where('edr.user_id', $employee_id)
            ->where('u.reporting_officer_id', $ro_id)
            ->where('edr.deleted_at IS NULL')
            ->order_by('edr.created_at', 'DESC')
            ->get()
            ->result();
    }


    public function get_by_user($userId)
    {
        return $this->shrm
            ->select('extra_day_requests.*, users.name as employee_name, users.employee_id')
            ->join('users', 'users.id = extra_day_requests.user_id', 'left')
            ->where('extra_day_requests.user_id', $userId)
            ->where('extra_day_requests.deleted_at IS NULL')
            ->order_by('extra_day_requests.id', 'DESC')
            ->get('extra_day_requests')
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->shrm
            ->select('extra_day_requests.*, users.name as employee_name')
            ->join('users', 'users.id = extra_day_requests.user_id', 'left')
            ->where('extra_day_requests.id', $id)
            ->where('extra_day_requests.deleted_at IS NULL')
            ->get('extra_day_requests')
            ->row();
    }


    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->shrm->insert('extra_day_requests', $data);
    }

    public function update($id, $data)
    {
        return $this->shrm->where('id', $id)->update('extra_day_requests', $data);
    }

    // In ExtraDayRequest model
    public function delete($id)
    {
        return $this->shrm->where('id', $id)->update('extra_day_requests', [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }


}
