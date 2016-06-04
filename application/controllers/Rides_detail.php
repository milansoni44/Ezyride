<?php

class Rides_detail extends CI_Controller
{
    function __construct() {
        parent::__construct();
        
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        if(!$this->ion_auth->logged_in())
        {
            redirect('auth');
        }
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->model('Rides_detail_model');
    }
    
    public function index() {
        $this->data['title'] = 'Rides Detail';
        $this->data['result'] = $this->Rides_detail_model->rides_details();
        $this->load->view('rides_detail/index',  $this->data);
    }
}