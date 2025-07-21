<?php

class Activity extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getAllActivities()
    {
        $this->shrm->select('*');
        $this->shrm->from('activities');
        $this->shrm->order_by('created_at', 'DESC');
        return $this->shrm->get()->result_array();
    }

    public function getActiveActivities()
    {
        $this->shrm->select('*');
        $this->shrm->from('activities');
        $this->shrm->where('status', 'active');
        $this->shrm->where('deleted_at IS NULL');
        $this->shrm->order_by('name', 'ASC');
        return $this->shrm->get()->result_array();
    }

    public function getActivityById($id)
    {
        return $this->shrm->get_where('activities', ['id' => $id])->row_array();
    }

    public function checkActivityExists($name)
    {
        $this->shrm->where('LOWER(name)', strtolower($name));
        $this->shrm->where('deleted_at IS NULL');
        return $this->shrm->get('activities')->result_array();
    }

    public function checkActivityExistsExcept($name, $exceptId)
    {
        $this->shrm->where('LOWER(name)', strtolower($name));
        $this->shrm->where('id !=', $exceptId);
        $this->shrm->where('deleted_at IS NULL');
        return $this->shrm->get('activities')->result_array();
    }

    public function createActivity($data)
    {
        return $this->shrm->insert('activities', $data);
    }

    public function updateActivity($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('activities', $data);
    }

    public function deleteActivity($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->shrm->where('id', $id);
        return $this->shrm->update('activities', $data);
    }

    public function restoreActivity($id)
    {
        $data = [
            'deleted_at' => NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->shrm->where('id', $id);
        return $this->shrm->update('activities', $data);
    }

    public function getActivityStats()
    {
        // Total activities
        $this->shrm->where('deleted_at IS NULL');
        $total = $this->shrm->count_all_results('activities');

        // Active activities
        $this->shrm->where('status', 'active');
        $this->shrm->where('deleted_at IS NULL');
        $active = $this->shrm->count_all_results('activities');

        // Inactive activities
        $this->shrm->where('status', 'inactive');
        $this->shrm->where('deleted_at IS NULL');
        $inactive = $this->shrm->count_all_results('activities');

        // Deleted activities
        $this->shrm->where('deleted_at IS NOT NULL');
        $deleted = $this->shrm->count_all_results('activities');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'deleted' => $deleted
        ];
    }
}