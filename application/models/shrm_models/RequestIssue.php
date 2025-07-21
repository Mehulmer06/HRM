<?php

class RequestIssue extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getRequestIssue()
    {
        // First get request issues with session-based filtering
        $this->shrm->select('ri.*');
        $this->shrm->from('request_issues ri');
        $this->shrm->where('ri.deleted_at IS NULL', null, false);
        $this->shrm->where('ri.status', 'in_progress');

        // Check session user's category and role
        $session_category = $this->session->userdata('category');
        $session_role = $this->session->userdata('role');
        $session_user_id = $this->session->userdata('user_id');

        if (($session_category == 'e' && $session_role == 'e') || $session_category == 'a') {
            // Filter to show only records where user_id matches session user with '00' prefix
            $prefixed_user_id = '00' . $session_user_id;
            $this->shrm->where('ri.user_id', $prefixed_user_id);
            $this->shrm->or_where('ri.submitted_to', $prefixed_user_id);
        }else {
            // For other users, show records where user_id OR submitted_to matches session user_id
            $this->shrm->group_start();
            $this->shrm->where('ri.user_id', $session_user_id);
            $this->shrm->or_where('ri.submitted_to', $session_user_id);
            $this->shrm->group_end();
        }

        $query = $this->shrm->get();
        $request_issues = $query->result();

        $result = [];

        foreach ($request_issues as $issue) {
            // Handle user_id (requester)
            if (strpos($issue->user_id, '00') === 0) {
                $actualUserId = substr($issue->user_id, 2);

                $user_query = $this->db->select('*')
                    ->from('user')
                    ->where('id', $actualUserId)
                    ->get();

                $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
            } else {
                $user_query = $this->shrm->select('*')
                    ->from('users')
                    ->where('id', $issue->user_id)
                    ->get();

                $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
            }

            // Handle submitted_to (check for '00' prefix too)
            $submitted_user = null;
            if (!empty($issue->submitted_to)) {
                if (strpos($issue->submitted_to, '00') === 0) {
                    $actualSubmittedId = substr($issue->submitted_to, 2);

                    $submitted_query = $this->db->select('name, e_mail as email, department, role')
                        ->from('user')
                        ->where('id', $actualSubmittedId)
                        ->get();
                } else {
                    $submitted_query = $this->shrm->select('name, email, department, role')
                        ->from('users')
                        ->where('id', $issue->submitted_to)
                        ->get();
                }

                $submitted_user = ($submitted_query && $submitted_query->num_rows() > 0) ? $submitted_query->row_array() : null;
            }

            // Add user info
            $issue->user_name = $user['name'] ?? null;
            $issue->email = $user['email'] ?? $user['e_mail'] ?? null;
            $issue->department = $user['department'] ?? null;
            $issue->role = $user['role'] ?? null;

            // Add submitted_to info
            $issue->submitted_to_name = $submitted_user['name'] ?? null;
            $issue->submitted_to_email = $submitted_user['email'] ?? null;
            $issue->submitted_to_department = $submitted_user['department'] ?? null;
            $issue->submitted_to_role = $submitted_user['role'] ?? null;

            $result[] = $issue;
        }

        return $result;
    }


    public function getRequestIssueColse()
    {
        // First get request issues with session-based filtering
        $this->shrm->select('ri.*');
        $this->shrm->from('request_issues ri');
        $this->shrm->where('ri.deleted_at IS NULL', null, false);
        $this->shrm->where('ri.status', 'close');

        // Check session user's category and role
        $session_category = $this->session->userdata('category');
        $session_role = $this->session->userdata('role');
        $session_user_id = $this->session->userdata('user_id');

        if ($session_category == 'e' && $session_role == 'e') {
            // Filter to show only records where user_id matches session user with '00' prefix
            $prefixed_user_id = '00' . $session_user_id;
            $this->shrm->where('ri.user_id', $prefixed_user_id);
        } else {
            // For other users, show records where user_id OR submitted_to matches session user_id
            $this->shrm->group_start();
            $this->shrm->where('ri.user_id', $session_user_id);
            $this->shrm->or_where('ri.submitted_to', $session_user_id);
            $this->shrm->group_end();
        }

        $query = $this->shrm->get();
        $request_issues = $query->result();

        $result = [];

        foreach ($request_issues as $issue) {
            // Handle user_id (requester)
            if (strpos($issue->user_id, '00') === 0) {
                // Remove '00' prefix for lookup
                $actualUserId = substr($issue->user_id, 2);

                // Fetch user details from 'user' table
                $user_query = $this->db->select('name, email, department, role')
                    ->from('user')
                    ->where('id', $actualUserId)
                    ->get();

                $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
            } else {
                // Fetch user details from 'users' table
                $user_query = $this->shrm->select('name, email, department, role')
                    ->from('users')
                    ->where('id', $issue->user_id)
                    ->get();

                $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
            }

            // Handle submitted_to (always from shrm users table)
            $submitted_user = null;
            if (!empty($issue->submitted_to)) {
                $submitted_query = $this->shrm->select('name, email, department, role')
                    ->from('users')
                    ->where('id', $issue->submitted_to)
                    ->get();

                $submitted_user = ($submitted_query && $submitted_query->num_rows() > 0) ? $submitted_query->row_array() : null;
            }

            // Add user data to issue object
            $issue->user_name = $user['name'] ?? null;
            $issue->email = $user['email'] ?? $user['e_mail'] ?? null;
            $issue->department = $user['department'] ?? null;
            $issue->role = $user['role'] ?? null;

            // Add submitted_to user data
            $issue->submitted_to_name = $submitted_user['name'] ?? null;
            $issue->submitted_to_email = $submitted_user['email'] ?? null;
            $issue->submitted_to_department = $submitted_user['department'] ?? null;
            $issue->submitted_to_role = $submitted_user['role'] ?? null;

            $result[] = $issue;
        }

        return $result;
    }


    public function getById($id)
    {
        // 1. Get the request issue
        $this->shrm->select('ri.*');
        $this->shrm->from('request_issues ri');
        $this->shrm->where('ri.id', $id);
        $this->shrm->where('ri.deleted_at IS NULL', null, false);
        $query = $this->shrm->get();
        $issue = $query->row();

        if (!$issue) {
            return null;
        }

        // 2. Fetch requester (user_id)
        if (strpos($issue->user_id, '00') === 0) {
            $actualUserId = substr($issue->user_id, 2);
            $user_query = $this->db->select('*')->from('user')->where('id', $actualUserId)->get();
            $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
        } else {
            $user_query = $this->shrm->select('name, email, department, role')->from('users')->where('id', $issue->user_id)->get();
            $user = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
        }

        // 3. Fetch submitted_to (dynamic table based on prefix)
        $submitted_user = null;
        if (!empty($issue->submitted_to)) {
            if (strpos($issue->submitted_to, '00') === 0) {
                $actualSubmittedId = substr($issue->submitted_to, 2);
                $submitted_query = $this->db
                    ->select('*')
                    ->from('user')
                    ->where('id', $actualSubmittedId)
                    ->get();
            } else {
                $submitted_query = $this->shrm
                    ->select('name, email, department, role')
                    ->from('users')
                    ->where('id', $issue->submitted_to)
                    ->get();
            }

            $submitted_user = ($submitted_query && $submitted_query->num_rows() > 0) ? $submitted_query->row_array() : null;
        }

        // 4. Add user (requester) info to issue
        $issue->user_name = $user['name'] ?? null;
        $issue->user_email = $user['email'] ?? $user['e_mail'] ?? null;
        $issue->user_department = $user['department'] ?? null;
        $issue->user_role = $user['role'] ?? null;

        // 5. Add submitted_to info
        $issue->submitted_to_name = $submitted_user['name'] ?? null;
        $issue->submitted_to_email = $submitted_user['email'] ?? $submitted_user['e_mail'] ?? null;
        $issue->submitted_to_department = $submitted_user['department'] ?? null;
        $issue->submitted_to_role = $submitted_user['role'] ?? null;

        return $issue;
    }



    public function commentInsert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->shrm->insert('request_comments', $data);
    }

    public function get_by_request($request_id)
    {
        // First get comments without joining users table
        $this->shrm->select('request_comments.*');
        $this->shrm->from('request_comments');
        $this->shrm->where('request_comments.request_id', $request_id);
        $this->shrm->where('request_comments.status', 'Y');
        $this->shrm->order_by('request_comments.created_at', 'DESC');
        $query = $this->shrm->get();

        $result = [];
        foreach ($query->result() as $row) {
            $user_name = null;

            // Check if user_id has '00' prefix
            if (strpos($row->user_id, '00') === 0) {
                // Remove '00' prefix for lookup
                $actualUserId = substr($row->user_id, 2);

                // Fetch user details from 'user' table using db connection
                $user_query = $this->db->select('name')
                    ->from('user')
                    ->where('id', $actualUserId)
                    ->get();

                $user_data = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
                $user_name = $user_data['name'] ?? null;
            } else {
                // Fetch user details from 'users' table using shrm connection
                $user_query = $this->shrm->select('name')
                    ->from('users')
                    ->where('id', $row->user_id)
                    ->get();

                $user_data = ($user_query && $user_query->num_rows() > 0) ? $user_query->row_array() : null;
                $user_name = $user_data['name'] ?? null;
            }

            $result[] = [
                'user_name' => $user_name,
                'comment' => $row->comment,
                'created_at' => date('Y-m-d H:i', strtotime($row->created_at))
            ];
        }

        return $result;
    }

    public function updateStatus($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('request_issues', $data);
    }
}


