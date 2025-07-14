<?php

class Holiday extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db_ihrms = $this->load->database('ihrms', TRUE);
    }


    public function getHolidaysByType($year, $type)
    {
        return $this->db_ihrms
            ->where('holiday_list_year', $year)
            ->where('holiday_list_type', $type)
            ->where('holiday_list_is_deleted', 'N')
            ->order_by('holiday_list_date', 'ASC')
            ->get('holiday_list')
            ->result();
    }
}