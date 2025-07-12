<?php
class ProjectStaff extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db_ihrms = $this->load->database('ihrms', TRUE);
    }
    public function getUserList()
    {
        $query = $this->db->get('users');
        return $query->result();
    }
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function getReportingOfficers()
    {
        $this->db_ihrms->select('id,name,designation');
        $this->db_ihrms->where('is_active', 'y');
        $this->db_ihrms->where('is_deleted', 'n');
        $query = $this->db_ihrms->get('user');
        return $query->result();
    }
}