<?php
class RequestIssue extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function getRequestIssue(){
		$this->db->select('*');
		$this->db->from('request_issues');
		$this->db->where('deleted_at IS NULL', null, false);
		$this->db->where('status', 'in_progress');
		$query = $this->db->get();
		return $query->result();
	}
	public function getRequestIssueColse(){
		$this->db->select('*');
		$this->db->from('request_issues');
		$this->db->where('deleted_at IS NULL', null, false);
		$this->db->where('status', 'close');
		$query = $this->db->get();
		return $query->result();
	}
	public function getById($id)
	{
		$this->db->select('ri.title, ri.submitted_to, ri.description, ri.document, us.name,ri.status,ri.request_date');
		$this->db->from('request_issues ri');
		$this->db->join('users us', 'ri.user_id = us.id', 'inner');
		$this->db->where('ri.id', $id);
		$this->db->where('ri.deleted_at IS NULL', null, false);
		$query = $this->db->get();
		return $query->row();
	}
	public function commentInsert($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$this->db->insert('request_comments', $data);
	}
	public function get_by_request($request_id)
	{
		$this->db->select('request_comments.*, users.name as user_name');
		$this->db->from('request_comments');
		$this->db->join('users', 'users.id = request_comments.user_id', 'left');
		$this->db->where('request_comments.request_id', $request_id);
		$this->db->where('request_comments.status', 'Y');
		$this->db->order_by('request_comments.created_at', 'DESC');
		$query = $this->db->get();

		$result = [];
		foreach ($query->result() as $row) {
			$result[] = [
				'user_name' => $row->user_name,
				'comment' => $row->comment,
				'created_at' => date('Y-m-d H:i', strtotime($row->created_at))
			];
		}
		return $result;
	}
	public function updateStatus($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update('request_issues', $data);
	}
}
?>
