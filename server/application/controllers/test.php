<?php
class Test extends CI_Controller{
	
	
	public function index(){
		$this->load->model('user');
		echo $this->user->hash('admin');
	
	}
}