<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NoteController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Note', 'Note');
    }

    public function index()
    {
        $data = [
            'open_notes' => $this->Note->get_notes_by_status('open'),
            'closed_notes' => $this->Note->get_notes_by_status('closed'),
            'deleted_notes' => $this->Note->get_notes_by_status('deleted'),
            'draft_notes' => $this->Note->get_notes_by_status('draft')
        ];

        $this->load->view('pages/note/index', $data);
    }

    public function create()
    {
        $this->load->view('pages/note/create');
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('submitted_to', 'Submitted To', 'required');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $data['validation_errors'] = validation_errors(); // Optional
            $this->load->view('pages/note/create', $data);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $action = $this->input->post('action'); // 'submit' or 'draft'
        $status = ($action === 'submit') ? 'open' : 'draft';

        $noteData = [
            'user_id' => $user_id,
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description'),
            'submitted_to' => $this->input->post('submitted_to'),
            'status' => $status
        ];

        $attachments = $_FILES['attachments'];
        $attachmentTitles = $this->input->post('attachment_titles');

        $this->db->trans_start();

        $note_id = $this->Note->create_note($noteData);

        if (!empty($attachments['name'][0])) {
            $uploadPath = './uploads/note_attachments/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            for ($i = 0; $i < count($attachments['name']); $i++) {
                $_FILES['single_file']['name'] = $attachments['name'][$i];
                $_FILES['single_file']['type'] = $attachments['type'][$i];
                $_FILES['single_file']['tmp_name'] = $attachments['tmp_name'][$i];
                $_FILES['single_file']['error'] = $attachments['error'][$i];
                $_FILES['single_file']['size'] = $attachments['size'][$i];

                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|xlsx|xls';
                $config['encrypt_name'] = true;
                $config['max_size'] = 10240;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $uploadData = $this->upload->data();
                    $this->Note->add_attachment($note_id, [
                        'title' => $attachmentTitles[$i],
                        'document' => 'uploads/note_attachments/' . $uploadData['file_name']
                    ]);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Note ' . ($status === 'draft' ? 'saved as draft.' : 'submitted successfully.'));
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
        }

        redirect('note');
    }

    public function edit($id)
    {
        $note = $this->Note->getNoteById($id);
        if (!$note) {
            show_404();
        }
        $data['note'] = $note;

        $this->load->view('pages/note/edit', $data);
    }

    public function update($id)
    {
        $note = $this->Note->getNoteById($id);
        if (!$note) {
            show_404();
        }

        $this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('submitted_to', 'Submitted To', 'required');

        $this->form_validation->set_message([
            'required' => 'Please enter {field}',
            'min_length' => '{field} must be at least {param} characters long',
            'max_length' => '{field} cannot exceed {param} characters'
        ]);

        if ($this->form_validation->run() === FALSE) {
            $data['note'] = $note;
            $data['validation_errors'] = validation_errors();
            $this->load->view('pages/note/edit', $data);
            return;
        }

        $action = $this->input->post('action'); // 'submit' or 'draft'
        $status = ($action === 'submit') ? 'open' : 'draft';

        $noteData = [
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description'),
            'submitted_to' => $this->input->post('submitted_to'),
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $attachments = $_FILES['attachments'];
        $attachmentTitles = $this->input->post('attachment_titles');
        $keepAttachments = $this->input->post('keep_attachments') ?: [];
        $removeAttachments = $this->input->post('remove_attachments') ?: [];

        $this->db->trans_start();

        $this->Note->update_note($id, $noteData);

        // Soft-delete removed attachments
        foreach ($removeAttachments as $attachmentId) {
            $this->Note->remove_attachment($attachmentId);
        }

        // Handle new attachments
        if (!empty($attachments['name'][0])) {
            $uploadPath = './uploads/note_attachments/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            for ($i = 0; $i < count($attachments['name']); $i++) {
                if (!empty($attachments['name'][$i])) {
                    $_FILES['file']['name'] = $attachments['name'][$i];
                    $_FILES['file']['type'] = $attachments['type'][$i];
                    $_FILES['file']['tmp_name'] = $attachments['tmp_name'][$i];
                    $_FILES['file']['error'] = $attachments['error'][$i];
                    $_FILES['file']['size'] = $attachments['size'][$i];

                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|xlsx|xls';
                    $config['encrypt_name'] = true;
                    $config['max_size'] = 10240;

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file')) {
                        $uploadData = $this->upload->data();
                        $this->Note->add_attachment($id, [
                            'title' => $attachmentTitles[$i] ?? $uploadData['orig_name'],
                            'document' => 'uploads/note_attachments/' . $uploadData['file_name']
                        ]);
                    } else {
                        $this->session->set_flashdata('error', 'File upload failed: ' . $this->upload->display_errors('', ''));
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $msg = ($status === 'draft') ? 'Note updated and saved as draft.' : 'Note updated successfully.';
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Something went wrong while updating the note. Please try again.');
        }

        redirect('note');
    }

    public function remove_attachment()
    {
        $id = $this->input->post('id');

        if ($id && is_numeric($id)) {
            $removed = $this->Note->remove_attachment($id);
            echo json_encode(['success' => $removed ? true : false]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function close()
    {
        $noteId = $this->input->post('note_id');
        $remarks = $this->input->post('note_close_remarks');
        $userId = $this->session->userdata('user_id');

        if (!$noteId || !$userId) {
            echo json_encode(['success' => false]);
            return;
        }

        $updated = $this->Note->closeNote($noteId, $userId, $remarks);
        echo json_encode(['success' => $updated]);
    }


    public function delete()
    {
        $id = $this->input->post('id');
        if (!$id) return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Invalid ID']));

        $this->db->where('id', $id)->update('notes', [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'message' => 'Note deleted successfully']));
    }

    public function view($id)
    {
        $note = $this->Note->getNoteById($id);
        if (!$note) {
            show_404();
        }

        $data = [
            'note' => $note,
        ];

        $this->load->view('pages/note/view', $data);
    }

    public function get_discussions()
    {
        $note_id = $this->input->post('note_id');
        $discussions = $this->Note->getDiscussions($note_id);

        echo json_encode([
            'success' => true,
            'data' => $discussions // 👈 Make sure the key is 'data'
        ]);
    }


    public function add_discussion()
    {
        $note_id = $this->input->post('note_id');
        $remarks = $this->input->post('remarks');
        $user_id = $this->session->userdata('user_id');

        if (!$note_id || !$remarks || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        $filePath = null;

        // Handle file upload if file exists
        if (!empty($_FILES['document']['name'])) {

            $upload_dir = FCPATH . 'uploads/note_discussions/';

            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $config['upload_path'] = $upload_dir;
            $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|xlsx|xls';
            $config['encrypt_name'] = true;
            $config['max_size'] = 10240;

            // Clear any existing upload library instance
            if (isset($this->upload)) {
                $this->upload->initialize($config);
            } else {
                $this->load->library('upload', $config);
            }

            if ($this->upload->do_upload('document')) {
                $uploadData = $this->upload->data();
                $filePath = 'uploads/note_discussions/' . $uploadData['file_name'];
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $this->upload->display_errors('', '')
                ]);
                return;
            }
        }

        // Insert discussion record
        $data = [
            'note_id' => $note_id,
            'user_id' => $user_id,
            'remarks' => $remarks,
            'document' => $filePath,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $inserted = $this->db->insert('note_discussions', $data);

        if ($inserted) {
            $discussions = $this->Note->getDiscussions($note_id);
            echo json_encode(['success' => true, 'data' => $discussions]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save discussion.']);
        }
    }


    public function take_action()
    {

        $note_id = $this->input->post('note_id');
        $role = $this->input->post('role'); // e.g., 'ro', 'admin', 'vishwambi'
        $is_approved = $this->input->post('is_approved'); // 1 = approve, 0 = reject
        $remarks = $this->input->post('remarks');
        $user_id = $this->session->userdata('user_id');

        if ($is_approved === null || $note_id === null || $role === null) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Invalid request data.'
            ]));
        }

        $data = [];
        $timestamp = date('Y-m-d H:i:s');

        switch ($role) {
            case 'ro':
                $data = [
                    'is_approved_ro' => $is_approved,
                    'approved_by_ro_id' => $user_id,
                    'approved_by_ro_date' => $timestamp,
                    'ro_remarks' => $remarks,
                ];
                break;
            case 'admin':
                $data = [
                    'is_approved_admin' => $is_approved,
                    'approved_by_admin_id' => $user_id,
                    'approved_by_admin_date' => $timestamp,
                    'admin_remarks' => $remarks,
                ];
                break;
            case 'vishwambi':
                $data = [
                    'is_approved_vishwambi' => $is_approved,
                    'approved_by_vishwambi_id' => $user_id,
                    'approved_by_vishwambi_date' => $timestamp,
                    'vishwambi_remarks' => $remarks,
                ];
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid role']);
                return;
        }

        $this->load->model('Note');
        $success = $this->Note->updateNoteById($note_id, $data);

        echo json_encode(['success' => $success]);
    }

}