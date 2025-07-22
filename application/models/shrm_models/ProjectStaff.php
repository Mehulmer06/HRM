<?php

class ProjectStaff extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getUserList()
    {
        $query = $this->shrm->get('users');
        return $query->result();
    }

    /**
     * Update user
     */
    public function updateUser($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('users', $data);
    }

    public function getReportingOfficers()
    {
        $this->db->select('id,name,designation');
        $this->db->where('is_active', 'y');
        $this->db->where('is_deleted', 'n');
        $query = $this->db->get('user');
        return $query->result();
    }

    public function insertContract($data)
    {
        return $this->shrm->insert('contract_details', $data);
    }

    public function insertAssets($assets)
    {
        return $this->shrm->insert('assets', $assets);
    }

    public function getSelectedReportingOfficer($user_id)
    {
        $this->shrm->select('reporting_officer_id');
        $this->shrm->from('users');
        $this->shrm->where('id', $user_id);
        $query = $this->shrm->get()->row();

        return $query ? $query->reporting_officer_id : '';
    }

    public function get_assets_by_id($user_id)
    {
        return $this->shrm
            ->where('user_id', $user_id)
            ->where('status', 'Y')
            ->get('assets')
            ->row_array();
    }

    public function updateContract($userId, $data)
    {
        $this->shrm->where('user_id', $userId);
        return $this->shrm->update('contract_details', $data);
    }

    public function updateAssets($userId, $data)
    {
        $this->shrm->where('user_id', $userId);
        return $this->shrm->update('assets', $data);
    }

    public function updateContractStatus($userId, $data)
    {
        $this->shrm->where('user_id', $userId);
        return $this->shrm->update('contract_details', ['status' => 'complete']);
    }

    public function getContractDetails($userId)
    {
        $this->shrm->select('contract_details.*, projects.*,contract_details.status as status');
        $this->shrm->from('contract_details');
        $this->shrm->join('projects', 'contract_details.project_name = projects.id', 'inner');
        $this->shrm->where('contract_details.user_id', $userId);
       // $this->shrm->where('contract_details.status', 'active');
        $query = $this->shrm->get();
        return $query->result_array();

    }

    public function checkContract($userId)
    {
        return $this->shrm->get_where('contract_details', ['user_id' => $userId])->result();
    }

    public function checkAssete($userId)
    {
        return $this->shrm->get_where('assets', ['user_id' => $userId])->result();
    }


}
