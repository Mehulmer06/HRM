<?php
class CasualLeave extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_grants()
    {
        return $this->db->select('cg.*, u.name')
            ->from('cl_grants cg')
            ->join('users u', 'u.id = cg.user_id')
            ->order_by('cg.granted_at', 'DESC')
            ->get()
            ->result();
    }

    public function get_grant_by_id($id)
    {
        return $this->db->get_where('cl_grants', ['id' => $id])->row();
    }

    public function insert_grant($data)
    {
        return $this->db->insert('cl_grants', $data);
    }

    public function update_grant($id, $data)
    {
        return $this->db->update('cl_grants', $data, ['id' => $id]);
    }

    public function delete_grant($id)
    {
        return $this->db->delete('cl_grants', ['id' => $id]);
    }

    // New method to check if CL already exists for a user and month
    public function check_existing_grant($user_id, $cl_month)
    {
        return $this->db->get_where('cl_grants', [
            'user_id' => $user_id,
            'cl_month' => $cl_month  // cl_month is now in YYYY-MM-01 format
        ])->row();
    }

    // Get grants by user
    public function get_grants_by_user($user_id)
    {
        return $this->db->select('cg.*, u.name')
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
        return $this->db->select('cg.*, u.name')
            ->from('cl_grants cg')
            ->join('users u', 'u.id = cg.user_id')
            ->where('cg.cl_month', $cl_month)
            ->order_by('cg.granted_at', 'DESC')
            ->get()
            ->result();
    }


}