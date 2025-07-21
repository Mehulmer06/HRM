<?php

class Note extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function get_notes_by_status($status)
    {
        $userId   = $this->session->userdata('user_id');
        $role     = $this->session->userdata('role');
        $category = $this->session->userdata('category');

        // Step 1: get notes from shrm
        $this->shrm->select('
        n.id,
        n.user_id,
        n.title,
        n.description,
        n.submitted_to,
        n.status,
        n.is_approved_ro,
        n.is_approved_admin,
        n.is_approved_vishwambi,
        n.ro_remarks,
        n.admin_remarks,
        n.vishwambi_remarks,
        n.status_closed_by,
        n.approved_by_ro_id,
        n.note_close_remarks,
        n.created_at,
        n.updated_at,
        n.deleted_at,
        u.name as user_name,
        u.email as user_email
    ');
        $this->shrm->from('notes n');
        $this->shrm->join('users u', 'n.user_id = u.id', 'left');

        // Role-based filters
        if ($role === 'e' && $category === 'e') {
            $this->shrm->where('n.reporting_officer_id', $userId);
            $this->shrm->where_in('n.submitted_to', ['ro', 'ro_admin', 'ro_admin_vishwambi', 'ro_vishwambi']);
        } elseif ($role === 'admin' || ($role === 'employee' && $category == 'admin')) {
            $this->shrm->group_start();
            $this->shrm->where_in('n.submitted_to', ['ro_admin', 'ro_admin_vishwambi']);
            $this->shrm->group_end();
        } elseif ($role === 'viswambi' || ($role === 'viswambi' && $category === 'admin')) {
            $this->shrm->group_start();
            $this->shrm->where_in('n.submitted_to', ['ro_admin_vishwambi', 'ro_vishwambi']);
            $this->shrm->group_end();
        }

        // Employee (non-admin) sees only their own notes
        if ($role === 'employee' && $category !== 'admin') {
            $this->shrm->where('n.user_id', $userId);
        }

        // Status-based filtering
        switch ($status) {
            case 'open':
                $this->shrm->where('n.status', 'open');
                $this->shrm->where('n.deleted_at IS NULL');
                break;
            case 'closed':
                $this->shrm->where('n.status', 'closed');
                $this->shrm->where('n.deleted_at IS NULL');
                break;
            case 'deleted':
                $this->shrm->where('n.deleted_at IS NOT NULL');
                $this->shrm->where('n.user_id', $userId); // Only creator can view deleted
                break;
            case 'draft':
                $this->shrm->where('n.status', 'draft');
                $this->shrm->where('n.user_id', $userId); // Only creator can view deleted
                $this->shrm->where('n.deleted_at IS NULL');
                break;
        }

        $this->shrm->order_by('n.id', 'DESC');
        $notes = $this->shrm->get()->result();

        // Step 2: fetch approver/closer names from other DB
        $ro_ids     = array_filter(array_unique(array_column($notes, 'approved_by_ro_id')));
        $closer_ids = array_filter(array_unique(array_column($notes, 'status_closed_by')));

        $user_ids = array_unique(array_merge($ro_ids, $closer_ids));

        $user_map = [];
        if (!empty($user_ids)) {
            $users = $this->db
                ->select('id, name')
                ->from('user')
                ->where_in('id', $user_ids)
                ->get()
                ->result();

            foreach ($users as $user) {
                $user_map[$user->id] = $user->name;
            }
        }

        // Step 3: attach names to notes
        foreach ($notes as &$note) {
            $note->approved_by_ro_name = isset($user_map[$note->approved_by_ro_id]) ? $user_map[$note->approved_by_ro_id] : null;
            $note->closed_by_name      = isset($user_map[$note->status_closed_by]) ? $user_map[$note->status_closed_by] : null;
        }

        return $notes;
    }





    public function create_note($data)
    {
        $this->shrm->insert('notes', $data);
        return $this->shrm->insert_id();
    }

    public function add_attachment($note_id, $data)
    {
        $data['note_id'] = $note_id;
        $this->shrm->insert('note_documents', $data);
    }

    public function getNoteById($id)
    {
        // Get the note with user join
        $this->shrm->select('n.*, u.name as user_name, u.email as user_email');
        $this->shrm->from('notes n');
        $this->shrm->join('users u', 'u.id = n.user_id', 'left');
        $this->shrm->where('n.id', $id);
        // $this->shrm->where('n.deleted_at', null);
        $note = $this->shrm->get()->row();

        if ($note) {
            // Get attachments
            $note->attachments = $this->shrm
                ->where('note_id', $id)
                ->where('deleted_at', null)
                ->get('note_documents')
                ->result();

            // Get discussions
            $note->discussions = $this->shrm
                ->where('note_id', $id)
                ->where('deleted_at', null)
                ->get('note_discussions')
                ->result();
        }

        return $note;
    }


    public function update_note($id, $data)
    {
        $this->shrm->where('id', $id);
        return $this->shrm->update('notes', $data);
    }


    public function remove_attachment($attachment_id)
    {
        // Soft delete the attachment
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $this->shrm->where('id', $attachment_id);
        return $this->shrm->update('note_documents', $data);
    }

    public function closeNote($noteId, $userId, $remarks = null)
    {
        $this->shrm->where('id', $noteId);
        return $this->shrm->update('notes', [
            'status' => 'closed',
            'status_closed_by' => $userId,
            'note_close_remarks' => $remarks
        ]);
    }

    public function getDiscussions($note_id)
    {
        // Step 1: Fetch discussions from shrm
        $this->shrm->select('d.*');
        $this->shrm->from('note_discussions d');
        $this->shrm->where('d.note_id', $note_id);
        $this->shrm->where('d.deleted_at IS NULL', null, false);
        $this->shrm->order_by('d.created_at', 'desc');
        $discussions = $this->shrm->get()->result_array();

        // Step 2: Attach user info based on user_id prefix
        foreach ($discussions as &$discussion) {
            $user_id = $discussion['user_id'];

            if (strpos($user_id, '00') === 0) {
                // Remove prefix and fetch from default DB's `user` table
                $actualUserId = substr($user_id, 2);
                $user = $this->db->select('name')
                    ->from('user')
                    ->where('id', $actualUserId)
                    ->get()
                    ->row_array();
            } else {
                // Fetch from shrm DB's `users` table
                $user = $this->shrm->select('name')
                    ->from('users')
                    ->where('id', $user_id)
                    ->get()
                    ->row_array();
            }

            $discussion['user_name'] = $user['name'] ?? 'Unknown';
        }

        return $discussions;
    }


    public function updateNoteById($note_id, $data)
    {
        $this->shrm->where('id', $note_id);
        return $this->shrm->update('notes', $data);
    }
}
