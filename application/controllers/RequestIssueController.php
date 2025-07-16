<?php

class RequestIssueController extends CI_Controller
{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('RequestIssue','requestIssue');
		}
		public function index(){
			try {
				$data['requestData'] = $this->requestIssue->getRequestIssue();
				$data['closeData'] = $this->requestIssue->getRequestIssueColse();
				$this->load->view('pages/request_issue/index',$data);
			}catch (Exception $e){
				$this->session->set_flashdata('error', $e->getMessage());
				redirect('dashboard');
			}

		}
	public function store()
	{
		try {

			$this->form_validation->set_rules('title', 'Title', 'required|min_length[5]|max_length[100]');
			$this->form_validation->set_rules('submitted_to', 'Submitted To', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required|min_length[20]|max_length[1000]');

			if ($this->form_validation->run() == false) {
				throw new Exception('Validation failed. Please correct the errors.');
			}


			$fileName = null;
			if (!empty($_FILES['document']['name'])) {
				$config['upload_path']   = './upload/request_issue/';
				$config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|txt';
				$config['max_size']      = 10240;
				$config['encrypt_name']  = true;

				$this->upload->initialize($config);

				if (!$this->upload->do_upload('document')) {
					throw new Exception(strip_tags($this->upload->display_errors()));
				}

				$uploadData = $this->upload->data();
				$fileName = $uploadData['file_name'];
			}


			$data = [
				'user_id'      => $this->session->userdata('user_id'),
				'title'        => $this->input->post('title', true),
				'submitted_to' => $this->input->post('submitted_to', true),
				'description'  => $this->input->post('description', false), // allow HTML
				'document'     => $fileName,
				'request_date'   => date('Y-m-d H:i:s'),
				'status'       => 'in_progress',
				'created_at'   => date('Y-m-d H:i:s')

			];

			$this->db->insert('request_issues', $data);

			$this->session->set_flashdata('success', 'Request submitted successfully!');
			redirect('request-issue');

		} catch (Exception $e) {
			$this->session->set_flashdata('error', $e->getMessage());
			$this->load->view('request_form');
		}
	}
	public function show($id)
	{
		$evaluation = $this->requestIssue->getById($id);
		if (!$evaluation) {
			show_404();
		}
		$data['evaluation'] = $evaluation;
		$this->load->view('pages/request_issue/show', $data);
	}
	public function commentStore()
	{
		$request_id = $this->input->post('request_id');
		$comment = $this->input->post('comment');

		$data = [
			'request_id' => $request_id,
			'comment' => $comment,
			'user_id' => $this->session->userdata('user_id'),
			'status' => 'Y',
			'created_at' => date('Y-m-d H:i:s')
		];
		$this->requestIssue->commentInsert($data);
		echo json_encode(['status' => 'success']);
	}
	public function fetch($id)
	{
		$comments = $this->requestIssue->get_by_request($id);
		echo json_encode(['comments' => $comments]);
	}
	public function update_status()
	{
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$allowedStatuses = [ 'in_progress', 'close'];

		if (!in_array($status, $allowedStatuses)) {
			echo json_encode(['success' => false]);
			return;
		}

		$updated = $this->requestIssue->updateStatus($id, ['status' => $status]);
		echo json_encode(['success' => $updated]);
	}



}
?>
