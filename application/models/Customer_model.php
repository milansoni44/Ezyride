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

	public function deactivate($id = NULL){
		$this->db->where('id',$id);
		if($this->db->update('customers',array('status'=>0))){
			return true;
		}
		return false;
	}

	public function activate($id = NULL){
		$this->db->where('id',$id);
		if($this->db->update('customers',array('status'=>1))){
			return true;
		}
		return false;
	}
}