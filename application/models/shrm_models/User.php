<?php

class User extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function get_user_by_email($email)
    {
        $query = $this->shrm->get_where('users', ['email' => $email]);
        return $query->row();
    }

    public function insertUser($data)
    {
        $this->shrm->insert('users', $data);
        return $this->shrm->insert_id();
    }

    public function getUserById($id)
    {
        $query = $this->shrm->get_where('users', ['id' => $id]);
        return $query->row_array();
    }

    public function getContractByUserId($userId)
    {
        $this->shrm->select('*');
        $this->shrm->from('contract_details');
        $this->shrm->where('status', 'active');
        $this->shrm->where('user_id', $userId);
        $query = $this->shrm->get();
        return $query->row_array();
    }

    public function updateUser($userId, $data)
    {
        $this->shrm->where('id', $userId);
        return $this->shrm->update('users', $data);
    }

    public function get_users_with_latest_contract()
    {
        // 1) build a sub-query that gets the latest join_date per user
        $latest = $this->shrm
            ->select('user_id, MAX(join_date) AS max_join_date')
            ->from('contract_details')
            ->where('status','active')
            ->group_by('user_id')
            ->get_compiled_select();

        // 2) join users → latest → full contract_details
        $this->shrm
            ->select('u.*, cd.designation, cd.join_date, cd.end_date, cd.salary, cd.location, cd.status')
            ->from('users u')
            ->where('u.role', 'employee')
            ->join("($latest) AS l", 'l.user_id = u.id', 'left')
            ->join('contract_details cd',
                'cd.user_id = u.id AND cd.join_date = l.max_join_date',
                'left');

        return $this->shrm->get()->result();
    }

    public function get_users_with_latest_contract_request()
    {
        $session_user_id = $this->session->userdata('user_id');

        // === Part 1: Get latest contract details (same as before) ===
        $latest = $this->shrm
            ->select('user_id, MAX(join_date) AS max_join_date')
            ->from('contract_details')
            ->where('status', 'active')
            ->group_by('user_id')
            ->get_compiled_select();

        $contract_query = $this->shrm
            ->select('u.*, cd.designation, cd.join_date, cd.end_date, cd.salary, cd.location, cd.status')
            ->from('users u')
            ->join("($latest) AS l", 'l.user_id = u.id', 'left')
            ->join('contract_details cd', 'cd.user_id = u.id AND cd.join_date = l.max_join_date', 'left')
            ->where('u.id !=', $session_user_id)
            ->get();

        $contract_users = $contract_query->result();

        // === Part 2: Fetch specific admin user from another connection/table ===
        $admin_user = $this->db
            ->select('*')
            ->from('user')
            ->where('role', 'a')
            ->where('category', 'a')
            ->like('e_mail', 'adminofficer')
            ->where('is_active', 'y')
            ->where('is_deleted', 'n')
            ->limit(1)
            ->get()
            ->row();

        // === Combine and return both ===
        return [
            'contract_users' => $contract_users,
            'admin_user' => $admin_user
        ];
    }





    public function getAllUser()
    {
        $this->shrm->select('*');
        $this->shrm->from('users');
        $this->shrm->where('status', 'Y');
        $query = $this->shrm->get();
        return $query->result();
    }

    public function get_phone_by_user_id($userId)
    {
        $this->shrm->where('id', $userId);
        $this->shrm->where('status', 'Y');
        $query = $this->shrm->get('users');
        return $query->row();

    }

    public function get_user_by_password($user_id)
    {
        return $this->shrm->get_where('users', ['id' => $user_id])->row();
    }

    public function update_password($user_id, $hashed_password)
    {
        return $this->shrm->update('users', ['password' => $hashed_password], ['id' => $user_id]);
    }

    public function get_by_user($user_id)
    {
        return $this->shrm->select('us.*')
            ->from('users us')
            ->where('us.id', $user_id)
            ->get()
            ->row();
    }

    public function contract_history($user_id)
    {
        return $this->shrm->select('*')
            ->from('contract_details')
            ->where('user_id', $user_id)
            ->where('status', 'complete')
            ->get()
            ->result(); // use ->result() to return all completed contracts
    }


}
