<?php

class Evaluation extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserList()
    {
        $query = $this->db->get('users');
        return $query->result();
    }


    public function insertEvaluation($data)
    {
        $this->db->insert('evaluations', $data);
        return $this->db->insert_id();
    }

    public function assignUserToEvaluation($evaluationId, $userId)
    {
        $this->db->insert('evaluation_users', [
            'evaluation_id' => $evaluationId,
            'user_id' => $userId
        ]);
    }

    public function getAllEvaluations()
    {
        $this->db->select('e.id, e.title, e.created_at, e.status, GROUP_CONCAT(u.name) as assigned_users');
        $this->db->from('evaluations e');
        $this->db->join('evaluation_users eu', 'eu.evaluation_id = e.id');
        $this->db->join('users u', 'u.id = eu.user_id');
        $this->db->where('e.deleted_at IS NULL');
        $this->db->group_by('e.id');
        $this->db->order_by('e.created_at', 'desc');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        return $this->db
            ->from('evaluations')
            ->where(['id' => $id, 'deleted_at' => null])
            ->get()
            ->row();
    }

    public function getAssignedUserIds($evaluation_id)
    {
        return $this->db
            ->select('user_id')
            ->from('evaluation_users')
            ->where('evaluation_id', $evaluation_id)
            ->get()
            ->result_array();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('evaluations', $data);
    }
    public function clearAssignedUsers($evaluation_id) {
        $this->db->where('evaluation_id', $evaluation_id);
        $this->db->delete('evaluation_users');
    }
    public function getAssignedUsers($evaluation_id)
    {
        return $this->db
            ->select('u.id, u.name,u.email')
            ->from('evaluation_users eu')
            ->join('users u', 'u.id = eu.user_id')
            ->where('eu.evaluation_id', $evaluation_id)
            ->get()
            ->result();
    }
    public function updateStatus($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('evaluations', $data);
    }

    public function getCommentsByEvaluation($evaluationId)
    {
        return $this->db
            ->select('ec.*, u.name as user_name')
            ->from('evaluation_comments ec')
            ->join('users u', 'u.id = ec.user_id')
            ->where('ec.evaluation_id', $evaluationId)
            ->order_by('ec.created_at', 'ASC')
            ->get()
            ->result();
    }

}