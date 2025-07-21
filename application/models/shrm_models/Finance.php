<?php

class Finance extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->shrm = $this->load->database('shrm', TRUE);
    }

    public function getAllFinance()
    {
        $this->shrm->select('fs.*, us.name as username');
        $this->shrm->from('finances fs');
        $this->shrm->join('users us', 'fs.user_id = us.id AND us.status = "Y"', 'inner');

        // Get session data
        $role = $this->session->userdata('role');
        $category = $this->session->userdata('category');
        $userId = $this->session->userdata('user_id');

        // Apply conditional filtering
        if (
            !(
                ($role === 'e' && $category === 'e') ||
                ($role === 'admin') ||
                ($role === 'employee' && $category === 'admin')
            )
        ) {
            // Show only current user's finance records
            $this->shrm->where('fs.user_id', $userId);
        }

        return $this->shrm->get()->result_array();
    }

	//get other document
	public function getAllOtherDocumnet(){
		$this->shrm->select('od.*, us.name as username');
		$this->shrm->from('other_documents od');
		$this->shrm->join('users us', 'od.user_id = us.id AND us.status = "Y"');
		// Get session data
		$role = $this->session->userdata('role');
		$category = $this->session->userdata('category');
		$userId = $this->session->userdata('user_id');

		// Apply conditional filtering
		if (
			!(
				($role === 'e' && $category === 'e') ||
				($role === 'admin') ||
				($role === 'employee' && $category === 'admin')
			)
		) {
			// Show only current user's finance records
			$this->shrm->where('od.user_id', $userId);
		}

		return $this->shrm->get()->result_array();

	}


    public function checkFinance($userId, $month)
    {
        return $this->shrm->get_where('finances', [
            'user_id' => $userId,
            'month_year' => $month
        ])->result_array();
    }
	public function checkOtherFinance($userId){
		return $this->shrm->get_where('other_documents', [
			'user_id' => $userId,
		])->result_array();
	}

}
