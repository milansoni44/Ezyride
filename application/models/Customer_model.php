<?php

class Customer_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_customers_detail() {
		$this->db->select('*');
		$q = $this->db->get('customers');
		if($q->num_rows() > 0)
		{
			return $q->result();
		}
		else
		{
		return false;
		}
	}

	public function get_customer_by_id($id = NULL){
		return $this->db->get_where('customers',array('id'=>$id))->row();
	}
}