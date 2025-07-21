<?php

class CasualLeave extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function get_all_grants()
    {
        return $this->shrm->select('cg.*, u.name, DATE_FORMAT(cg.cl_month, "%M %Y") as month_year_display')
            ->from('cl_grants cg')
            ->join('users u', 'u.id = cg.user_id')
            ->order_by('cg.cl_month', 'DESC')
            ->order_by('u.name', 'ASC')
            ->get()
            ->result();
    }

    public function get_grant_by_id($id)
    {
        return $this->shrm->get_where('cl_grants', ['id' => $id])->row();
    }

    public function insert_grant($data)
    {
        return $this->shrm->insert('cl_grants', $data);
    }

    public function update_grant($id, $data)
    {
        return $this->shrm->update('cl_grants', $data, ['id' => $id]);
    }

    public function delete_grant($id)
    {
        return $this->shrm->delete('cl_grants', ['id' => $id]);
    }

    // New method to check if CL already exists for a user and month
    public function check_existing_grant($user_id, $cl_month)
    {
        return $this->shrm->get_where('cl_grants', [
            'user_id' => $user_id,
            'cl_month' => $cl_month  // cl_month is now in YYYY-MM-01 format
        ])->row();
    }

    // Get grants by user
    public function get_grants_by_user($user_id)
    {
        return $this->shrm->select('cg.*, u.name')
            ->from('cl_grants cg')
            ->join('users u', 'u.id = cg.user_id')
            ->where('cg.user_id', $user_id)
            ->order_by('cg.granted_at', 'DESC')
            ->get()
            ->result();
    }

    // Get grants by month
    public function get_grants_by_month($cl_month)
    {
        return $this->shrm->select('cg.*, u.name')
            ->from('cl_grants cg')
            ->join('users u', 'u.id = cg.user_id')
            ->where('cg.cl_month', $cl_month)
            ->order_by('cg.granted_at', 'DESC')
            ->get()
            ->result();
    }


}