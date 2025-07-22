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

//    public function getAllEvaluations()
//    {
//        $userId = $this->session->userdata('user_id');
//        $role = $this->session->userdata('role');
//        $category = $this->session->userdata('category');
//
//        $this->shrm->select('e.id,e.category, e.title, e.created_at, e.status, GROUP_CONCAT(DISTINCT u.name) as assigned_users');
//        $this->shrm->from('evaluations e');
//        $this->shrm->join('evaluation_users eu', 'eu.evaluation_id = e.id', 'left');
//        $this->shrm->join('users u', 'u.id = eu.user_id', 'left');
//        $this->shrm->where('e.deleted_at IS NULL');
//
//        if ($role === 'e' && $category === 'e') {
//            $this->shrm->where('e.reporting_officer_id', $userId);
//        } else if ($role === 'employee') {
//            // Only get evaluations where the current employee is assigned, l̥
//            $this->shrm->where('e.id IN (SELECT evaluation_id FROM evaluation_users WHERE user_id = ' . $userId . ')', NULL, FALSE);
//        }
//
//        $this->shrm->group_by('e.id');
//        $this->shrm->order_by('e.created_at', 'desc');
//
//        return $this->shrm->get()->result();
//    }

    public function getAllEvaluations($filters = array())
    {
        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        $this->shrm->select('e.id, e.category, e.title, e.project_id, e.activity_id, e.created_at, e.status, 
                        GROUP_CONCAT(DISTINCT CONCAT(u.id, ":", u.name) SEPARATOR "|") as assigned_users, 
                        a.name as activity_name, 
                        p.project_name as project_name');
        $this->shrm->from('evaluations e');
        $this->shrm->join('evaluation_users eu', 'eu.evaluation_id = e.id', 'left');
        $this->shrm->join('users u', 'u.id = eu.user_id', 'left');
        $this->shrm->join('activities a', 'a.id = e.activity_id', 'left');
        $this->shrm->join('projects p', 'p.id = e.project_id', 'left');
        $this->shrm->where('e.deleted_at IS NULL');

        // Role-based filtering
        if ($role === 'e' && $category === 'e') {
            $this->shrm->where('e.reporting_officer_id', $userId);

            // Apply dynamic filters only for this role/category combination
            if (!empty($filters['filter_project'])) {
                $this->shrm->where('e.project_id', $filters['filter_project']);
            }
            if (!empty($filters['filter_activity'])) {
                $this->shrm->where('e.activity_id', $filters['filter_activity']);
            }
            if (!empty($filters['filter_priority'])) {
                $this->shrm->where('e.category', $filters['filter_priority']);
            }
            if (!empty($filters['filter_status'])) {
                $this->shrm->where('e.status', $filters['filter_status']);
            }

            // Date range filtering
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $this->shrm->where('DATE(e.created_at) >=', $filters['start_date']);
                $this->shrm->where('DATE(e.created_at) <=', $filters['end_date']);
            }
        } else if ($role === 'employee') {
            // Only get evaluations where the current employee is assigned
            $this->shrm->where('e.id IN (SELECT evaluation_id FROM evaluation_users WHERE user_id = ' . $userId . ')', NULL, FALSE);
        }

        $this->shrm->group_by('e.id');
        $this->shrm->order_by('e.created_at', 'desc');

        return $this->shrm->get()->result();
    }


    public function getById($id)
    {
        return $this->shrm
            ->select('evaluations.*, projects.project_name, activities.name')
            ->from('evaluations')
            ->join('projects', 'projects.id = evaluations.project_id', 'left')
            ->join('activities', 'activities.id = evaluations.activity_id', 'left')
            ->where(['evaluations.id' => $id, 'evaluations.deleted_at' => null])
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


    public function getUserCounts($user_id)
    {
        // Category counts
        $this->shrm->select('
        COUNT(DISTINCT e.id) as total_count,
        SUM(CASE WHEN e.category = "routine" THEN 1 ELSE 0 END) as routine_count,
        SUM(CASE WHEN e.category = "other" THEN 1 ELSE 0 END) as other_count,
        SUM(CASE WHEN e.category = "urgent" THEN 1 ELSE 0 END) as urgent_count,
        SUM(CASE WHEN e.category = "addon" THEN 1 ELSE 0 END) as addon_count,
        SUM(CASE WHEN e.category = "support" THEN 1 ELSE 0 END) as support_count,
        SUM(CASE WHEN e.status = "pending" THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN e.status = "in_progress" THEN 1 ELSE 0 END) as in_progress_count,
        SUM(CASE WHEN e.status = "completed" THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN e.status = "on_hold" THEN 1 ELSE 0 END) as on_hold_count
    ');
        $this->shrm->from('evaluations e');
        $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
        $this->shrm->where('eu.user_id', $user_id);
        $this->shrm->where('e.deleted_at IS NULL');
        $this->shrm->where('eu.deleted_at IS NULL');

        $counts = $this->shrm->get()->row_array();

        // Activity counts
        $this->shrm->select('a.name as activity_name, COUNT(DISTINCT e.id) as count');
        $this->shrm->from('evaluations e');
        $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
        $this->shrm->join('activities a', 'e.activity_id = a.id', 'left');
        $this->shrm->where('eu.user_id', $user_id);
        $this->shrm->where('e.deleted_at IS NULL');
        $this->shrm->where('eu.deleted_at IS NULL');
        $this->shrm->group_by('e.activity_id');
        $this->shrm->order_by('count', 'DESC');
        $activity_counts = $this->shrm->get()->result_array();

        // Project counts
        $this->shrm->select('p.id as project_id, p.project_name, COUNT(DISTINCT e.id) as count');
        $this->shrm->from('evaluations e');
        $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
        $this->shrm->join('projects p', 'e.project_id = p.id', 'left');
        $this->shrm->where('eu.user_id', $user_id);
        $this->shrm->where('e.deleted_at IS NULL');
        $this->shrm->where('eu.deleted_at IS NULL');
        $this->shrm->group_by('e.project_id');
        $this->shrm->order_by('count', 'DESC');
        $project_counts = $this->shrm->get()->result_array();

        // Enhanced: Get project-wise category and activity breakdown
        $project_details = [];
        foreach ($project_counts as $project) {
            if ($project['project_id']) {
                // Get category breakdown for this project
                $this->shrm->select('
                e.category,
                COUNT(DISTINCT e.id) as count,
                SUM(CASE WHEN e.status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN e.status = "in_progress" THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN e.status = "completed" THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN e.status = "on_hold" THEN 1 ELSE 0 END) as on_hold_count
            ');
                $this->shrm->from('evaluations e');
                $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
                $this->shrm->where('eu.user_id', $user_id);
                $this->shrm->where('e.project_id', $project['project_id']);
                $this->shrm->where('e.deleted_at IS NULL');
                $this->shrm->where('eu.deleted_at IS NULL');
                $this->shrm->group_by('e.category');
                $category_breakdown = $this->shrm->get()->result_array();

                // Get activity breakdown for this project
                $this->shrm->select('a.name as activity_name, COUNT(DISTINCT e.id) as count');
                $this->shrm->from('evaluations e');
                $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
                $this->shrm->join('activities a', 'e.activity_id = a.id', 'left');
                $this->shrm->where('eu.user_id', $user_id);
                $this->shrm->where('e.project_id', $project['project_id']);
                $this->shrm->where('e.deleted_at IS NULL');
                $this->shrm->where('eu.deleted_at IS NULL');
                $this->shrm->group_by('e.activity_id');
                $this->shrm->order_by('count', 'DESC');
                $activity_breakdown = $this->shrm->get()->result_array();

                $project_details[$project['project_id']] = [
                    'project_name' => $project['project_name'],
                    'total_count' => $project['count'],
                    'category_breakdown' => $category_breakdown,
                    'activity_breakdown' => $activity_breakdown
                ];
            }
        }

        return [
            'total_count' => (int)$counts['total_count'],
            'category_counts' => [
                'routine' => (int)$counts['routine_count'],
                'other' => (int)$counts['other_count'],
                'urgent' => (int)$counts['urgent_count'],
                'addon' => (int)$counts['addon_count'],
                'support' => (int)$counts['support_count']
            ],
            'status_counts' => [
                'pending' => (int)$counts['pending_count'],
                'in_progress' => (int)$counts['in_progress_count'],
                'completed' => (int)$counts['completed_count'],
                'on_hold' => (int)$counts['on_hold_count']
            ],
            'activity_counts' => $activity_counts,
            'project_counts' => $project_counts,
            'project_details' => $project_details // Enhanced project data
        ];
    }


    public function getUnreadCommentsCount($userId)
    {
        try {
            // Get evaluations where user is assigned
            $this->shrm->select('e.id');
            $this->shrm->from('evaluations e');
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
            $this->shrm->where('eu.user_id', str_replace('00', '', $userId)); // Remove 00 prefix for evaluation_users table
            $this->shrm->where('e.deleted_at IS NULL');
            $evaluationIds = $this->shrm->get()->result_array();

            if (empty($evaluationIds)) {
                return 0;
            }

            $evalIds = array_column($evaluationIds, 'id');

            // Get all comments on these evaluations (excluding user's own comments)
            $this->shrm->select('ec.id');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $userId);
            $allComments = $this->shrm->get()->result_array();

            if (empty($allComments)) {
                return 0;
            }

            $commentIds = array_column($allComments, 'id');

            // Get read comments
            $this->shrm->select('comment_id');
            $this->shrm->from('comment_reads');
            $this->shrm->where('user_id', $userId);
            $this->shrm->where_in('comment_id', $commentIds);
            $readComments = $this->shrm->get()->result_array();

            $readCommentIds = array_column($readComments, 'comment_id');

            // Count unread comments
            $unreadCount = count($commentIds) - count($readCommentIds);

            return max(0, $unreadCount);

        } catch (Exception $e) {
            log_message('error', 'Error in getUnreadCommentsCount: ' . $e->getMessage());
            return 0;
        }
    }

    public function getRecentNotifications($userId, $limit = 10)
    {
        try {
            // Get evaluations where user is assigned (remove 00 prefix for evaluation_users lookup)
            $lookupUserId = str_replace('00', '', $userId);

            $this->shrm->select('e.id, e.title');
            $this->shrm->from('evaluations e');
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
            $this->shrm->where('eu.user_id', $lookupUserId);
            $this->shrm->where('e.deleted_at IS NULL');
            $evaluations = $this->shrm->get()->result_array();

            if (empty($evaluations)) {
                return [];
            }

            $evalIds = array_column($evaluations, 'id');
            $evalTitles = array_column($evaluations, 'title', 'id');

            // Get recent comments (excluding user's own comments)
            $this->shrm->select('ec.*');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $userId);
            $this->shrm->order_by('ec.created_at', 'DESC');
            $this->shrm->limit($limit);

            $comments = $this->shrm->get()->result_array();

            // Process each comment
            foreach ($comments as &$comment) {
                // Check if this comment has been read by the user
                $this->shrm->select('id');
                $this->shrm->from('comment_reads');
                $this->shrm->where('comment_id', $comment['id']);
                $this->shrm->where('user_id', $userId);
                $isRead = $this->shrm->get()->row();

                $comment['is_unread'] = $isRead ? 0 : 1;
                $comment['evaluation_title'] = $evalTitles[$comment['evaluation_id']] ?? 'Unknown Evaluation';

                // Get commenter name
                if (strpos($comment['user_id'], '00') === 0) {
                    // Admin user - look in main user table
                    $actualUserId = substr($comment['user_id'], 2);
                    $user = $this->db->select('name')
                        ->from('user')
                        ->where('id', $actualUserId)
                        ->get()
                        ->row_array();
                } else {
                    // Regular employee - look in shrm users table
                    $user = $this->shrm->select('name')
                        ->from('users')
                        ->where('id', $comment['user_id'])
                        ->get()
                        ->row_array();
                }

                $comment['commenter_name'] = $user ? $user['name'] : 'Unknown User';
            }

            return $comments;

        } catch (Exception $e) {
            log_message('error', 'Error in getRecentNotifications: ' . $e->getMessage());
            return [];
        }
    }

    public function markNotificationAsRead($commentId, $userId)
    {
        try {
            // Check if already marked as read
            $existing = $this->shrm->where(['comment_id' => $commentId, 'user_id' => $userId])
                ->get('comment_reads')
                ->row();

            if (!$existing) {
                $data = [
                    'comment_id' => $commentId,
                    'user_id' => $userId,
                    'read_at' => date('Y-m-d H:i:s')
                ];
                return $this->shrm->insert('comment_reads', $data);
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error in markNotificationAsRead: ' . $e->getMessage());
            return false;
        }
    }

    public function markAllNotificationsAsRead($userId)
    {
        try {
            // Get evaluations where user is assigned
            $lookupUserId = str_replace('00', '', $userId);

            $this->shrm->select('e.id');
            $this->shrm->from('evaluations e');
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id');
            $this->shrm->where('eu.user_id', $lookupUserId);
            $this->shrm->where('e.deleted_at IS NULL');
            $evaluationIds = $this->shrm->get()->result_array();

            if (empty($evaluationIds)) {
                return true;
            }

            $evalIds = array_column($evaluationIds, 'id');

            // Get unread comments
            $this->shrm->select('ec.id');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $userId);

            // Exclude already read comments
            $this->shrm->where('ec.id NOT IN (
            SELECT comment_id FROM comment_reads WHERE user_id = "' . $this->shrm->escape_str($userId) . '"
        )', NULL, FALSE);

            $unreadComments = $this->shrm->get()->result_array();

            // Mark all as read
            foreach ($unreadComments as $comment) {
                $this->markNotificationAsRead($comment['id'], $userId);
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error in markAllNotificationsAsRead: ' . $e->getMessage());
            return false;
        }
    }
}
