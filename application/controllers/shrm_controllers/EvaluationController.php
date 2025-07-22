<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EvaluationController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('shrm/login');
        }
        $this->shrm = $this->load->database('shrm', TRUE);
        $this->load->model('shrm_models/Evaluation', 'Evaluation');
        $this->load->model('shrm_models/User', 'User');
        $this->load->model('shrm_models/Project', 'Project');
        $this->load->model('shrm_models/Activity', 'Activity');
    }

    public function index()
    {
        if ($this->session->userdata('role') === 'employee') {
            $user_id = $this->session->userdata('user_id');
            $data['counts'] = $this->Evaluation->getUserCounts($user_id);
        }

        $data['activity'] = $this->Activity->getActiveActivities();

        // Get filter parameters for dynamic filtering
        $filters = array(
            'filter_project' => $this->input->get('filter_project'),
            'filter_activity' => $this->input->get('filter_activity'),
            'filter_priority' => $this->input->get('filter_priority'),
            'filter_status' => $this->input->get('filter_status'),
            'start_date' => $this->input->get('start_date'),
            'end_date' => $this->input->get('end_date')
        );

        // Pass filters to the model
        $data['evaluations'] = $this->Evaluation->getAllEvaluations($filters);

        // Get projects and activities for filter dropdowns (only for role 'e' with category 'e')
        if ($this->session->userdata('role') === 'e' && $this->session->userdata('category') === 'e') {
            $data['projects'] = $this->Project->getActiveProjects(); // You might need to create this method
            $data['activities'] = $this->Activity->getActiveActivities();
        }

        $this->load->view('shrm_views/pages/evaluation/index', $data);
    }


    public function create()
    {
        $data['users'] = $this->User->get_users_with_latest_contract();
        $data['projects'] = $this->Project->getActiveProjects();
        $data['activity'] = $this->Activity->getActiveActivities();
        $this->load->view('shrm_views/pages/evaluation/create', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required|in_list[routine,urgent,addon,support]');
        $this->form_validation->set_rules('project_id', 'Project', 'required');
        $this->form_validation->set_rules('activity_id', 'Activity', 'required');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $data['users'] = $this->User->get_users_with_latest_contract();
            $data['projects'] = $this->Project->getActiveProjects();
            $data['activity'] = $this->Activity->getActiveActivities();
            $data['validation_errors'] = validation_errors(); // optional
            $this->load->view('shrm_views/pages/evaluation/create', $data);
            return;
        }

        $category = $this->input->post('category');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $assignedUsers = $this->input->post('assign_users');

        // Assume current logged-in user is the reporting officer
        $reportingOfficerId = $this->session->userdata('user_id');
        $reportingOfficerName = $this->session->userdata('name');
        $projectId = $this->input->post('project_id');
        $activityId = $this->input->post('activity_id');
        // Insert evaluation
        $evaluationData = [
            'title' => $title,
            'description' => $description,
            'reporting_officer_id' => $reportingOfficerId,
            'reporting_officer_name' => $reportingOfficerName,
            'category' => $category,
            'project_id' => $projectId,
            'activity_id' => $activityId,
            'status' => 'pending'
        ];

        if (!empty($_FILES['attachment']['name'])) {
            $upload_dir = './uploads/evaluations/';
            $full_path = FCPATH . 'uploads/evaluations/';

            // Create directory if it doesn't exist
            if (!is_dir($full_path)) {
                if (!mkdir($full_path, 0755, true)) {
                    $data['users'] = $this->User->get_users_with_latest_contract();
                    $data['projects'] = $this->Project->getActiveProjects();
                    $data['activity'] = $this->Activity->getActiveActivities();
                    $data['upload_error'] = 'Failed to create upload directory. Please check permissions.';
                    $this->load->view('shrm_views/pages/evaluation/create', $data);
                    return;
                }
            }

            // Verify directory is writable
            if (!is_writable($full_path)) {
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['projects'] = $this->Project->getActiveProjects();
                $data['activity'] = $this->Activity->getActiveActivities();
                $data['upload_error'] = 'Upload directory is not writable. Please check permissions.';
                $this->load->view('shrm_views/pages/evaluation/create', $data);
                return;
            }

            $config['upload_path'] = $upload_dir; // Use relative path
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['encrypt_name'] = true;
            $config['max_size'] = 10240; // 10MB in KB
            $config['remove_spaces'] = true;

            // Initialize upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            // Changed from 'document' to 'attachment' to match the form field name
            if ($this->upload->do_upload('attachment')) {
                $uploadData = $this->upload->data();
                $filePath = 'uploads/evaluations/' . $uploadData['file_name'];
                $evaluationData['attachment'] = $filePath;
            } else {
                // Handle upload error
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['projects'] = $this->Project->getActiveProjects();
                $data['activity'] = $this->Activity->getActiveActivities();
                $data['upload_error'] = $this->upload->display_errors();
                $this->load->view('shrm_views/pages/evaluation/create', $data);
                return;
            }
        }

        $evaluationId = $this->Evaluation->insertEvaluation($evaluationData);

        if ($evaluationId && !empty($assignedUsers)) {
            foreach ($assignedUsers as $userId) {
                $this->Evaluation->assignUserToEvaluation($evaluationId, $userId);
            }

            // Set success message
            $this->session->set_flashdata('success', 'Work progress evaluation created successfully!');
        } else {
            // Set error message
            $this->session->set_flashdata('error', 'Failed to create work progress evaluation. Please try again.');
        }

        redirect('work-progress'); // redirect to evaluation list
    }


    public function edit($id)
    {
        $originalId = $this->decryptId($id);
        $evaluation = $this->Evaluation->getById($originalId);
        if (!$evaluation) {
            show_404();
        }

        $assigned = $this->Evaluation->getAssignedUserIds($originalId);
        $data = [
            'evaluation' => $evaluation,
            'assigned_user_ids' => array_column($assigned, 'user_id'),
            'users' => $this->User->get_users_with_latest_contract(),
            'projects' => $this->Project->getActiveProjects(),
            'activity' => $this->Activity->getActiveActivities(),
        ];
        $this->load->view('shrm_views/pages/evaluation/edit', $data);
    }


    public function update($id)
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_users[]', 'Assigned Users', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required|in_list[routine,urgent,addon,support]');
        $this->form_validation->set_rules('project_id', 'Project', 'required');
        $this->form_validation->set_rules('activity_id', 'Activity', 'required');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $evaluation = $this->Evaluation->getById($id);
            if (!$evaluation) show_404();

            $data['evaluation'] = $evaluation;
            $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
            $data['users'] = $this->User->get_users_with_latest_contract();
            $data['validation_errors'] = validation_errors();
            $this->load->view('shrm_views/pages/evaluation/edit', $data);
            return;
        }

        // Get existing evaluation data
        $evaluation = $this->Evaluation->getById($id);
        if (!$evaluation) show_404();

        // Prepare update data
        $updateData = [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'category' => $this->input->post('category'),
            'project_id' => $this->input->post('project_id'),
            'activity_id' => $this->input->post('activity_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle attachment removal
        if ($this->input->post('remove_attachment') == '1') {
            // Delete old file if exists
            if (!empty($evaluation->attachment) && file_exists(FCPATH . $evaluation->attachment)) {
                unlink(FCPATH . $evaluation->attachment);
            }
            $updateData['attachment'] = null;
            $updateData['attachment_original_name'] = null;
        }

        // Handle new attachment upload
        if (!empty($_FILES['attachment']['name'])) {
            // Use relative path from the application root
            $upload_dir = './uploads/evaluations/';
            $full_path = FCPATH . 'uploads/evaluations/';

            // Create directory if it doesn't exist
            if (!is_dir($full_path)) {
                if (!mkdir($full_path, 0755, true)) {
                    $evaluation = $this->Evaluation->getById($id);
                    $data['evaluation'] = $evaluation;
                    $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
                    $data['users'] = $this->User->get_users_with_latest_contract();
                    $data['upload_error'] = 'Failed to create upload directory. Please check permissions.';
                    $this->load->view('shrm_views/pages/evaluation/edit', $data);
                    return;
                }
            }


            $config['upload_path'] = $upload_dir;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['encrypt_name'] = true;
            $config['max_size'] = 10240; // 10MB in KB
            $config['remove_spaces'] = true;

            // Initialize upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('attachment')) {
                // Delete old file if exists
                if (!empty($evaluation->attachment) && file_exists(FCPATH . $evaluation->attachment)) {
                    unlink(FCPATH . $evaluation->attachment);
                }

                // Save new file info
                $uploadData = $this->upload->data();
                $filePath = 'uploads/evaluations/' . $uploadData['file_name'];
                $updateData['attachment'] = $filePath;
            } else {
                // Handle upload error
                $evaluation = $this->Evaluation->getById($id);
                $data['evaluation'] = $evaluation;
                $data['assigned_user_ids'] = array_column($this->Evaluation->getAssignedUserIds($id), 'user_id');
                $data['users'] = $this->User->get_users_with_latest_contract();
                $data['upload_error'] = $this->upload->display_errors();
                $this->load->view('shrm_views/pages/evaluation/edit', $data);
                return;
            }
        }

        // Update the evaluation
        $this->Evaluation->update($id, $updateData);

        // Update assigned users
        $this->Evaluation->clearAssignedUsers($id);
        foreach ($this->input->post('assign_users') as $userId) {
            $this->Evaluation->assignUserToEvaluation($id, $userId);
        }

        // Set success message
        $this->session->set_flashdata('success', 'Work progress evaluation updated successfully!');

        redirect('work-progress');
    }

    public function show($encodedId)
    {
        $originalId = $this->decryptId($encodedId);
        if ($originalId === null) {
            show_404();
            return;
        }

        $this->load->model('Evaluation');
        $evaluation = $this->Evaluation->getById($originalId)
            ?: show_404();

        $data = [
            'evaluation' => $evaluation,
            'assigned_users' => $this->Evaluation->getAssignedUsers($originalId),
            'evaluationComments' => $this->Evaluation->getCommentsByEvaluation($originalId),
        ];

        $this->load->view('shrm_views/pages/evaluation/show', $data);
    }


    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $allowedStatuses = ['pending', 'in_progress', 'completed', 'on_hold'];

        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false]);
            return;
        }

        $updated = $this->Evaluation->updateStatus($id, ['status' => $status]);
        echo json_encode(['success' => $updated]);
    }

    public function add_comment()
    {
        $evalId = $this->input->post('evaluation_id');
        $comment = trim($this->input->post('comment'));
        $userId = $this->session->userdata('user_id');

        // Check session role and category, add '00' prefix if both equal 'e'
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if ($role === 'e' && $category === 'e') {
            $userId = '00' . $userId;
        }

        if ($evalId && $comment && $userId) {
            $this->shrm->insert('evaluation_comments', [
                'evaluation_id' => $evalId,
                'user_id' => $userId,
                'comment' => $comment,
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }


    public function comments($id)
    {
        $originalId = $this->decryptId($id);
        $data['evaluation'] = $this->Evaluation->getById($originalId);
        $data['comments'] = $this->Evaluation->getCommentsByEvaluation($originalId);
        $assigned = $this->Evaluation->getAssignedUsers($originalId);
        $data['assigned_users'] = $assigned;
        $this->load->view('shrm_views/pages/evaluation/comments', $data);
    }

    public function get_comments()
    {
        $evaluation_id = $this->input->get('evaluation_id');

        if (empty($evaluation_id)) {
            echo json_encode(['success' => false, 'message' => 'Evaluation ID is required']);
            return;
        }

        $comments = $this->Evaluation->getCommentsByEvaluation($evaluation_id);

        echo json_encode(['success' => true, 'comments' => $comments]);
    }


    protected function decryptId(string $encodedId): ?int
    {
        $secretMultiplier = 15394;

        // base-36 → decimal string
        $decimalString = base_convert($encodedId, 36, 10);

        // must be all digits and divisible by our multiplier
        if (!ctype_digit($decimalString) || ((int)$decimalString) % $secretMultiplier !== 0) {
            return null;
        }

        // recover original ID
        return intdiv((int)$decimalString, $secretMultiplier);
    }


    public function report($id)
    {
        $originalId = $this->decryptId($id);
        $data['counts'] = $this->Evaluation->getUserCounts($originalId);
        if (!$data['counts']) {
            show_404();
        }

        $this->load->view('shrm_views/pages/report/evaluation_report', $data);
    }


    public function get_notifications()
    {
        header('Content-Type: application/json');

        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        // User ID formatting based on role
        if ($role === 'e' && $category === 'e') {
            $commentUserId = '00' . $userId; // For comments table
            $evalUserId = $userId; // For evaluation_users table (unsigned int)
        } else {
            $commentUserId = $userId;
            $evalUserId = $userId;
        }

        try {
            // Get evaluations where user is assigned OR user is reporting officer
            $this->shrm->select('e.id, e.title, e.reporting_officer_id');
            $this->shrm->from('evaluations e');
            $this->shrm->group_start(); // Start grouping conditions
            // Either user is assigned to evaluation
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id', 'left');
            $this->shrm->where('eu.user_id', $evalUserId);
            $this->shrm->where('eu.deleted_at IS NULL');
            // OR user is the reporting officer
            $this->shrm->or_where('e.reporting_officer_id', $evalUserId);
            $this->shrm->group_end(); // End grouping
            $this->shrm->where('e.deleted_at IS NULL');
            $this->shrm->group_by('e.id'); // Group to avoid duplicates
            $evaluations = $this->shrm->get()->result_array();

            if (empty($evaluations)) {
                echo json_encode(['success' => true, 'notifications' => []]);
                return;
            }

            $evalIds = array_column($evaluations, 'id');
            $evalTitles = array_column($evaluations, 'title', 'id');

            // Get ONLY UNREAD comments on these evaluations (exclude user's own comments)
            $this->shrm->select('ec.id, ec.evaluation_id, ec.user_id, ec.comment, ec.created_at');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $commentUserId);

            // IMPORTANT: Only get comments that are NOT read by current user
            $this->shrm->where('ec.id NOT IN (
            SELECT comment_id FROM comment_reads 
            WHERE user_id = "' . $this->shrm->escape_str($commentUserId) . '"
        )', NULL, FALSE);

            $this->shrm->order_by('ec.created_at', 'DESC');
            $this->shrm->limit(20);
            $comments = $this->shrm->get()->result_array();

            $notifications = [];
            foreach ($comments as $comment) {
                // Get commenter name
                $commenterName = 'Unknown User';
                if (strpos($comment['user_id'], '00') === 0) {
                    // Admin user - remove 00 prefix and check main user table
                    $actualUserId = substr($comment['user_id'], 2);
                    $user = $this->db->select('name')->from('user')->where('id', $actualUserId)->get()->row();
                    $commenterName = $user ? $user->name : 'Admin User';
                } else {
                    // Regular employee - check shrm users table
                    $user = $this->shrm->select('name')->from('users')->where('id', $comment['user_id'])->get()->row();
                    $commenterName = $user ? $user->name : 'Employee';
                }

                $notifications[] = [
                    'id' => $comment['id'],
                    'evaluation_id' => $comment['evaluation_id'],
                    'evaluation_title' => $evalTitles[$comment['evaluation_id']] ?? 'Unknown Evaluation',
                    'comment_preview' => substr(strip_tags($comment['comment']), 0, 100) . '...',
                    'commenter_name' => $commenterName,
                    'created_at' => $comment['created_at'],
                    'is_unread' => 1 // All returned notifications are unread
                ];
            }

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'total' => count($notifications),
                'debug_info' => [
                    'user_id' => $userId,
                    'comment_user_id' => $commentUserId,
                    'eval_user_id' => $evalUserId,
                    'evaluations_found' => count($evaluations),
                    'role' => $role,
                    'category' => $category,
                    'only_unread' => true
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ]);
        }
    }


    public function get_notification_count()
    {
        header('Content-Type: application/json');

        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if (!$userId) {
            echo json_encode(['count' => 0]);
            return;
        }

        // User ID formatting
        if ($role === 'e' && $category === 'e') {
            $commentUserId = '00' . $userId;
            $evalUserId = $userId;
        } else {
            $commentUserId = $userId;
            $evalUserId = $userId;
        }

        try {
            // Get evaluations where user is assigned OR user is reporting officer
            $this->shrm->select('e.id');
            $this->shrm->from('evaluations e');
            $this->shrm->group_start(); // Start grouping conditions
            // Either user is assigned to evaluation
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id', 'left');
            $this->shrm->where('eu.user_id', $evalUserId);
            $this->shrm->where('eu.deleted_at IS NULL');
            // OR user is the reporting officer
            $this->shrm->or_where('e.reporting_officer_id', $evalUserId);
            $this->shrm->group_end(); // End grouping
            $this->shrm->where('e.deleted_at IS NULL');
            $this->shrm->group_by('e.id'); // Group to avoid duplicates
            $evaluations = $this->shrm->get()->result_array();

            if (empty($evaluations)) {
                echo json_encode(['count' => 0]);
                return;
            }

            $evalIds = array_column($evaluations, 'id');

            // Get all comments on user's evaluations (not by user)
            $this->shrm->select('ec.id');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $commentUserId);
            $allComments = $this->shrm->get()->result_array();

            if (empty($allComments)) {
                echo json_encode(['count' => 0]);
                return;
            }

            $commentIds = array_column($allComments, 'id');

            // Get read comments
            $this->shrm->select('comment_id');
            $this->shrm->from('comment_reads');
            $this->shrm->where('user_id', $commentUserId);
            $this->shrm->where_in('comment_id', $commentIds);
            $readComments = $this->shrm->get()->result_array();

            $readCommentIds = array_column($readComments, 'comment_id');
            $unreadCount = count($commentIds) - count($readCommentIds);

            echo json_encode([
                'count' => max(0, $unreadCount),
                'debug_info' => [
                    'user_id' => $userId,
                    'evaluations_found' => count($evaluations),
                    'total_comments' => count($allComments),
                    'read_comments' => count($readCommentIds),
                    'unread_comments' => $unreadCount
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
        }
    }


    public function mark_notification_read()
    {
        header('Content-Type: application/json');

        $commentId = $this->input->post('comment_id');
        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if (!$userId || !$commentId) {
            echo json_encode(['success' => false, 'message' => 'Missing required data']);
            return;
        }

        // User ID formatting
        if ($role === 'e' && $category === 'e') {
            $commentUserId = '00' . $userId;
        } else {
            $commentUserId = $userId;
        }

        try {
            // Check if already marked as read
            $this->shrm->select('id');
            $this->shrm->from('comment_reads');
            $this->shrm->where('comment_id', $commentId);
            $this->shrm->where('user_id', $commentUserId);
            $exists = $this->shrm->get()->row();

            if (!$exists) {
                // Insert read record
                $this->shrm->insert('comment_reads', [
                    'comment_id' => $commentId,
                    'user_id' => $commentUserId,
                    'read_at' => date('Y-m-d H:i:s')
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Notification marked as read']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function mark_all_notifications_read()
    {
        header('Content-Type: application/json');

        $userId = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        // User ID formatting
        if ($role === 'e' && $category === 'e') {
            $commentUserId = '00' . $userId;
            $evalUserId = $userId;
        } else {
            $commentUserId = $userId;
            $evalUserId = $userId;
        }

        try {
            // Get evaluations where user is assigned OR user is reporting officer
            $this->shrm->select('e.id');
            $this->shrm->from('evaluations e');
            $this->shrm->group_start();
            $this->shrm->join('evaluation_users eu', 'e.id = eu.evaluation_id', 'left');
            $this->shrm->where('eu.user_id', $evalUserId);
            $this->shrm->where('eu.deleted_at IS NULL');
            $this->shrm->or_where('e.reporting_officer_id', $evalUserId);
            $this->shrm->group_end();
            $this->shrm->where('e.deleted_at IS NULL');
            $this->shrm->group_by('e.id');
            $evaluations = $this->shrm->get()->result_array();

            if (empty($evaluations)) {
                echo json_encode(['success' => true, 'message' => 'No evaluations found']);
                return;
            }

            $evalIds = array_column($evaluations, 'id');

            // Get all unread comments
            $this->shrm->select('ec.id');
            $this->shrm->from('evaluation_comments ec');
            $this->shrm->where_in('ec.evaluation_id', $evalIds);
            $this->shrm->where('ec.user_id !=', $commentUserId);
            $this->shrm->where('ec.id NOT IN (
            SELECT comment_id FROM comment_reads WHERE user_id = "' . $this->shrm->escape_str($commentUserId) . '"
        )', NULL, FALSE);
            $unreadComments = $this->shrm->get()->result_array();

            // Mark all as read
            $markedCount = 0;
            foreach ($unreadComments as $comment) {
                $this->shrm->insert('comment_reads', [
                    'comment_id' => $comment['id'],
                    'user_id' => $commentUserId,
                    'read_at' => date('Y-m-d H:i:s')
                ]);
                $markedCount++;
            }

            echo json_encode([
                'success' => true,
                'marked_count' => $markedCount,
                'message' => "Marked {$markedCount} notifications as read"
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
