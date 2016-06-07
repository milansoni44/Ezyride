<?php

class Customers extends CI_Controller
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
        $this->lang->load('auth');
        $this->load->model('customer_model');
    }
    
    public function index() {
        $this->data['title'] = 'Application Users';
        $this->data['result'] = $this->customer_model->get_customers_detail();
        $this->load->view('customers/index',  $this->data);
    }
}