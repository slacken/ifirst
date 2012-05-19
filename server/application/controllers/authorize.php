<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authorize extends CI_Controller {
	public $succeed;
	public $data;
	
	public function __construct(){
		parent::__construct();
		$this->succeed = FALSE;
		$this->data = array();
	}
	
	/**
	 * 用户登录，更新token并返回
	 * */
	public function login(){
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		if($name && $password){
			$this->load->model('user');
			$user = $this->user->login($name,$password);
			if($user === FALSE){
				$this->succeed = FALSE;
				$this->data['error_info'] = "错误的用户名或密码";
			}else{
				$this->succeed = TRUE;
				$this->data = $user ;
			}
		}else{
			$this->succeed = FALSE;
			$this->data['error_info'] = "未输入用户名或密码";
		}
	}
	
	/**
	 * 用户注册
	 * */
	public function register(){
		
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$email = $this->input->post('email');
		if($name && $password && is_email($email)){
			
			$this->load->model('user');
			$user = $this->user->register($name,$email,$password);
			if($user === FALSE){
				$this->succeed = FALSE;
				$this->data['error_info'] = "重复的用户名或邮箱";
			}else{
				$this->succeed = TRUE;
				$this->data = $user ;
			}
		}else{
			$this->succeed = FALSE;
			$this->data['error_info'] = "未完整填写或者格式有误";
		}
	}
	
	/**
	 * 输出数据
	 * */
	public function __destruct(){
		$this->data['succeed'] = $this->succeed;
		if(!$this->succeed){
			$this->output->enable_profiler(TRUE);
		}
		echo json_encode($this->data);
		
	}
	
	
}
