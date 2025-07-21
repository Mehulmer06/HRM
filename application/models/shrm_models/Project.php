<?php

class Project extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getAllProjects()
    {
        $this->shrm->select('*');
        $this->shrm->from('projects');
        $this->shrm->order_by('created_at', 'DESC');
        return $this->shrm->get()->result_array();
    }

    public function getActiveProjects()
    {
        $this->shrm->select('*');
        $this->shrm->from('projects');
        $this->shrm->where('status', 'active');
        $this->shrm->where('deleted_at IS NULL');
        $this->shrm->order_by('project_name', 'ASC');
        return $this->shrm->get()->result_array();
    }

    public function getProjectById($id)
    {
        return $this->shrm->get_where('projects', ['id' => $id])->row_array();
    }

    public function checkProjectExists($project_name)
    {
        $this->shrm->where('LOWER(project_name)', strtolower($project_name));
        $this->shrm->where('deleted_at IS NULL');
        return $this->shrm->get('projects')->result_array();
    }

    public function checkProjectExistsExcept($project_name, $exceptId)
    {
        $this->shrm->where('LOWER(project_name)', strtolower($project_name));
        $this->shrm->where('id !=', $exceptId);
        $this->shrm->where('deleted_at IS NULL');
        return $this->shrm->get('projects')->result_array();
    }

    public function createProject($data)
    {
        return $this->shrm->insert('projects', $data);
    }

    public function updateProject($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('projects', $data);
    }

    public function deleteProject($id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->shrm->where('id', $id);
        return $this->shrm->update('projects', $data);
    }

    public function restoreProject($id)
    {
        $data = [
            'deleted_at' => NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->shrm->where('id', $id);
        return $this->shrm->update('projects', $data);
    }

    public function getProjectStats()
    {
        // Total projects
        $this->shrm->where('deleted_at IS NULL');
        $total = $this->shrm->count_all_results('projects');

        // Active projects
        $this->shrm->where('status', 'active');
        $this->shrm->where('deleted_at IS NULL');
        $active = $this->shrm->count_all_results('projects');

        // Inactive projects
        $this->shrm->where('status', 'inactive');
        $this->shrm->where('deleted_at IS NULL');
        $inactive = $this->shrm->count_all_results('projects');

        // Deleted projects
        $this->shrm->where('deleted_at IS NOT NULL');
        $deleted = $this->shrm->count_all_results('projects');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'deleted' => $deleted
        ];
    }

    // Keep the old methods for backward compatibility
    public function insert($table, $data)
    {
        return $this->shrm->insert($table, $data);
    }

    public function update($table, $where, $data)
    {
        return $this->shrm->where($where)->update($table, $data);
    }

    public function get_projects()
    {
        $this->shrm->where('status', 'Y');
        $query = $this->shrm->get('projects');
        return $query->result();
    }
}