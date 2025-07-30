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
        $this->shrm->select('u.*, cd.designation, cd.join_date, cd.end_date, cd.contract_month, cd.project_name, cd.salary, cd.location, cd.status AS contract_status');
        $this->shrm->from('users u');
        $this->shrm->join('(SELECT * FROM contract_details cd1
                        WHERE NOT EXISTS (
                            SELECT 1 FROM contract_details cd2
                            WHERE cd2.user_id = cd1.user_id
                              AND cd2.created_at > cd1.created_at
                        )) cd', 'cd.user_id = u.id', 'left');
        $this->shrm->where('u.deleted_at IS NULL');
        $this->shrm->where('cd.status', 'active'); // Only active contracts
        return $this->shrm->get()->result();
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
        $this->shrm->select('contract_details.*, contract_details.project_name as contractProjectId,contract_details.id as contractId ,projects.*,contract_details.status as status');
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

    public function checkQuarter($userId)
    {
        return $this->shrm->get_where('guesthouses', ['user_id' => $userId])->result();
    }

    public function updateQuarter($userId, $data)
    {
        return $this->shrm->where('user_id', $userId)->update('guesthouses', $data);
    }

    public function insertQuarter($quarter)
    {
        return $this->shrm->insert('guesthouses', $quarter);
    }

    public function getQuartersDetails($userId)
    {
        $this->shrm->select('*');
        $this->shrm->from('guesthouses');
        $this->shrm->where('user_id', $userId);
        $query = $this->shrm->get();
        return $query->result_array();
    }
}
