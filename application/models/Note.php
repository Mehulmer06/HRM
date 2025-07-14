<?php

class Note extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_notes_by_status($status)
    {
        $this->db->select('
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
        n.note_close_remarks,
        n.created_at,
        n.updated_at,
        n.deleted_at,
        u.name as user_name,
        u.email as user_email,
        closer.name as closed_by_name
    ');

        $this->db->from('notes n');
        $this->db->join('users u', 'n.user_id = u.id', 'left'); // submitter
        $this->db->join('users closer', 'n.status_closed_by = closer.id', 'left'); // closing user

        // Handle different status conditions
        switch ($status) {
            case 'open':
                $this->db->where('n.status', 'open');
                $this->db->where('n.deleted_at IS NULL');
                break;

            case 'closed':
                $this->db->where('n.status', 'closed');
                $this->db->where('n.deleted_at IS NULL');
                break;

            case 'deleted':
                $this->db->where('n.deleted_at IS NOT NULL');
                break;

            case 'draft':
                $this->db->where('n.status', 'draft');
                $this->db->where('n.deleted_at IS NULL');
                break;
        }

        $this->db->order_by('n.id', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }


    public function create_note($data)
    {
        $this->db->insert('notes', $data);
        return $this->db->insert_id();
    }

    public function add_attachment($note_id, $data)
    {
        $data['note_id'] = $note_id;
        $this->db->insert('note_documents', $data);
    }

    public function getNoteById($id)
    {
        // Get the note with user join
        $this->db->select('n.*, u.name as user_name, u.email as user_email');
        $this->db->from('notes n');
        $this->db->join('users u', 'u.id = n.user_id', 'left');
        $this->db->where('n.id', $id);
        $this->db->where('n.deleted_at', null);
        $note = $this->db->get()->row();

        if ($note) {
            // Get attachments
            $note->attachments = $this->db
                ->where('note_id', $id)
                ->where('deleted_at', null)
                ->get('note_documents')
                ->result();

            // Get discussions
            $note->discussions = $this->db
                ->where('note_id', $id)
                ->where('deleted_at', null)
                ->get('note_discussions')
                ->result();
        }

        return $note;
    }


    public function update_note($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('notes', $data);
    }


    public function remove_attachment($attachment_id)
    {
        // Soft delete the attachment
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $attachment_id);
        return $this->db->update('note_documents', $data);
    }

    public function closeNote($noteId, $userId, $remarks = null)
    {
        $this->db->where('id', $noteId);
        return $this->db->update('notes', [
            'status' => 'closed',
            'status_closed_by' => $userId,
            'note_close_remarks' => $remarks
        ]);
    }

    public function getDiscussions($note_id)
    {
        $this->db->select('d.*, u.name as user_name');
        $this->db->from('note_discussions d');
        $this->db->join('users u', 'u.id = d.user_id', 'left');
        $this->db->where('d.note_id', $note_id);
        $this->db->where('d.deleted_at IS NULL');
        $this->db->order_by('d.created_at', 'desc');
        return $this->db->get()->result();
    }

    public function updateNoteById($note_id, $data)
    {
        $this->db->where('id', $note_id);
        return $this->db->update('notes', $data);
    }


}