<?php

class Car_detail extends CI_Controller
{
     function __construct() {
        parent::__construct();
        
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation','upload'));
        $this->load->helper(array('url', 'language'));
        if(!$this->ion_auth->logged_in())
        {
            redirect('auth');
        }
        
		$this->lang->load('auth');
        $this->load->model('Car_detail_model');
        
    }
	
	public function valid_upload_attach()
	{
		$config['upload_path'] = './assets/upload/';
		$config['allowed_types'] = 'gif|jpg|png';

		$this->upload->initialize($config);
		if($_FILES['car_image']['size'] > 0){
			if (!$this->upload->do_upload('car_image'))
			{
				$this->form_validation->set_message('valid_upload_attach', $this->upload->display_errors());
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return true;
		}       
	}
    
    public function index() {
        $this->data['title'] = 'Car Details';
        $this->data['result'] = $this->Car_detail_model->car_detail();
        $this->load->view('car/index',$this->data);
    }

    public function edit($id = NULL){
		$this->data['title'] = "Update Car";
		//validation
		$this->form_validation->set_rules('car_image', 'Car Image', 'trim|callback_valid_upload_attach');
		
		if($this->form_validation->run() == true){
			if($_FILES['car_image']['size'] > 0){
				$uploadData = $this->upload->data();
				$image_name = $uploadData["file_name"];
			}else{
				$image_name = $this->input->post('e_car_image');
			}
			
			$data = array(
				'user_id'=>$this->input->post('user'),
				'car_no'=>$this->input->post('car_no'),
				'car_model'=>$this->input->post('car_model'),
				'car_layout'=>$this->input->post('car_layout'),
				'car_image'=>$image_name,
				'ac_availability'=>$this->input->post('ac_availibility'),
				'music_system'=>$this->input->post('music_system'),
				'air_bag'=>$this->input->post('air_bag'),
				'seat_belt'=>$this->input->post('seat_belt'),
				'updation_time'=>date('Y-m-d H:m:s'),
			);
			// echo '<pre>';
			// print_r($data);exit;
			$result = $this->Car_detail_model->update_car($data,$id);
			
			if($result){
				$this->session->set_flashdata('success', 'Car updated successfully.');
				redirect("car_detail", 'refresh');
			}
		}else{
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['car'] = $this->Car_detail_model->get_car_by_id($id);
			$this->data['customers'] = $this->Car_detail_model->get_customers();
			$this->load->view('car/edit',$this->data);
		}
    }
}