<?php

class Evaluation extends CI_Model
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


    public function insertEvaluation($data)
    {
        $this->shrm->insert('evaluations', $data);
        return $this->shrm->insert_id();
    }

    public function assignUserToEvaluation($evaluationId, $userId)
    {
        $this->shrm->insert('evaluation_users', [
            'evaluation_id' => $evaluationId,
            'user_id' => $userId
        ]);
    }

    public function getAllEvaluations()
    {
        $userId   = $this->session->userdata('user_id');
        $role     = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        $this->shrm->select('e.id,e.category, e.title, e.created_at, e.status, GROUP_CONCAT(DISTINCT u.name) as assigned_users');
        $this->shrm->from('evaluations e');
        $this->shrm->join('evaluation_users eu', 'eu.evaluation_id = e.id', 'left');
        $this->shrm->join('users u', 'u.id = eu.user_id', 'left');
        $this->shrm->where('e.deleted_at IS NULL');

        if ($role === 'e' && $category === 'e') {
            $this->shrm->where('e.reporting_officer_id', $userId);
        } else if ($role === 'employee') {
            // Only get evaluations where the current employee is assigned, l̥
            $this->shrm->where('e.id IN (SELECT evaluation_id FROM evaluation_users WHERE user_id = ' . $userId . ')', NULL, FALSE);
        }

        $this->shrm->group_by('e.id');
        $this->shrm->order_by('e.created_at', 'desc');

        return $this->shrm->get()->result();
    }



    public function getById($id)
    {
        return $this->shrm
            ->from('evaluations')
            ->where(['id' => $id, 'deleted_at' => null])
            ->get()
            ->row();
    }

    public function getAssignedUserIds($evaluation_id)
    {
        return $this->shrm
            ->select('user_id')
            ->from('evaluation_users')
            ->where('evaluation_id', $evaluation_id)
            ->get()
            ->result_array();
    }

    public function update($id, $data)
    {
        return $this->shrm
            ->where('id', $id)
            ->update('evaluations', $data);
    }
    public function clearAssignedUsers($evaluation_id)
    {
        $this->shrm->where('evaluation_id', $evaluation_id);
        $this->shrm->delete('evaluation_users');
    }
    public function getAssignedUsers($evaluation_id)
    {
        return $this->shrm
            ->select('u.id, u.name,u.email')
            ->from('evaluation_users eu')
            ->join('users u', 'u.id = eu.user_id')
            ->where('eu.evaluation_id', $evaluation_id)
            ->get()
            ->result();
    }
    public function updateStatus($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('evaluations', $data);
    }

    public function getCommentsByEvaluation($evaluationId)
    {
        $comments = $this->shrm
            ->select('ec.*')
            ->from('evaluation_comments ec')
            ->where('ec.evaluation_id', $evaluationId)
            ->order_by('ec.created_at', 'ASC')
            ->get()
            ->result_array();

        foreach ($comments as &$comment) {
            if (strpos($comment['user_id'], '00') === 0) {
                // Remove '00' prefix for lookup
                $actualUserId = substr($comment['user_id'], 2);

                // Fetch user details from 'user' table
                $user = $this->db->select('name')
                    ->from('user')
                    ->where('id', $actualUserId)
                    ->get()
                    ->row_array();
            } else {
                // Fetch user details from 'users' table
                $user = $this->shrm->select('name')
                    ->from('users')
                    ->where('id', $comment['user_id'])
                    ->get()
                    ->row_array();
            }

            $comment['user_name'] = $user ? $user['name'] : 'Unknown User';
        }

        return $comments;
    }
}
