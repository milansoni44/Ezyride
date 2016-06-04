<?php

class Home extends CI_Controller
{
   function __construct() {
        parent::__construct();
        
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        if(!$this->ion_auth->logged_in())
        {
            redirect('auth');
        }
    }
        
        public function index() {
            $this->data['title'] = 'Admin Home';
            $this->load->view('index',  $this->data);
        }
}