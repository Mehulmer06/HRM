<?php
class User extends CI_Model
{
    public function get_user_by_email($email)
    {
        $query = $this->db->get_where('users', ['email' => $email]);
        return $query->row();
    }
    public function insertUser($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }
    public function getUserById($id)
    {
        $query = $this->db->get_where('users', ['id' => $id]);
        return $query->row_array();
    }
    public function getContractByUserId($userId)
    {
        $this->db->select('*');
        $this->db->from('contract_details');
        $this->db->where('status', 'active');
        $this->db->where('user_id', $userId);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function updateUser($userId, $data)
    {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }

}
