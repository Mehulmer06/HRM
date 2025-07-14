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
    public function updateUser($id, $data)
    {
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
    public function insertContract($data)
    {
        return $this->db->insert('contract_details', $data);
    }
    public function getSelectedReportingOfficer($user_id)
    {
        $this->db->select('reporting_officer_id');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get()->row();

        return $query ? $query->reporting_officer_id : '';
    }
    public function updateContract($userId, $data)
    {
        $this->db->where('user_id', $userId);
        return $this->db->update('contract_details', $data);
    }
    public function updateContractStatus($userId, $data)
    {
        $this->db->where('user_id', $userId);
        return $this->db->update('contract_details', ['status' => 'complete']);
    }
    public function getContractDetails($userId)
    {
        $this->db->select('*');
        $this->db->from('contract_details');
        $this->db->where('user_id', $userId);
        $query = $this->db->get();
        return $query->result_array();
    }


}