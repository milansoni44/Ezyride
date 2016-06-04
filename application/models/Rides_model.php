<?php

class Rides_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function rides_detail() {
        $this->db->select('*');
        $q = $this->db->get('rides');
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }

}
