<?php

class Car_detail_model extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function car_detail() {
		$this->db->select('*');
		$q = $this->db->get('car_details');
		if($q->num_rows() > 0)
		{
			return $q->result();
		}
		else
		{
		return false;
		}
	}
	
	public function get_customers(){
		return $this->db->get('customers')->result();
	}
	
	public function get_car_by_id($id = NULL){
		return $this->db->get_where('car_details',array('id'=>$id))->row();
	}
	
	public function update_car($data = array(),$id = NULL){
		$this->db->where('id',$id);
		if($this->db->update('car_details',$data)){
			return true;
		}
		return false;
	}
        
}