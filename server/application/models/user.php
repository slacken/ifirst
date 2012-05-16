<?php
/**
 * 用户管理模块
 * */
class User extends CI_Model{
	public $table = 'user';
	
	public $type = array(
		'student'=>1,
		'teacher'=>5,
		'admin'=>10
	);
	
	public function hash($password){
		return md5(sha1($password));
	}
	
	public function rand(){
		$n = rand(10e16, 10e20);
		return $this->hash(base_convert($n, 10, 36));
	}
	/**
	 * 用户登录，返回用户的token，有效期，类型
	 * */
	public function login($name,$password){
		$password = $this->hash($password);
		if(is_email($name))$this->db->where('email',$name);
		else $this->db->where('name',$name);
		$user = $this->db->where('password',$password)->get($this->table)->row_array();
		
		if(empty($user))return FALSE;
		
		//每登录一次，就更新一次token
		$token = array(
			'value'=>$this->rand(),
			'stamp'=>time(),
			'count'=>0
		);
		
		$this->db->where('user_id',$user['id'])->update('token',$token);
		$type = array_flip($this->type);
		return array('token'=>$token['value'],'type'=>$type[$user['type']],'expires'=>$this->config->item('token_expire'));
	}
	
	/**
	 * 用户注册
	 * */
	public function register($name,$email,$password){
		$password = $this->hash($password);
		
		if($this->db->where('name',$name)->or_where('email',$email)->get($this->table)->num_rows()>0)
			return FALSE;//已存在用户名或者电子邮件地址
		$user = array(
			'class_id'=>0,//默认为0
			'type'=>$this->type['student'],
			'name'=>$name,
			'password'=>$password,
			'email'=>$email
		);
		
		$this->db->insert($this->table,$user);
		
		$user_id = $this->db->insert_id();
		
		//生成token
		$token = array(
			'user_id'=>$user_id,
			'value'=>$this->rand(),
			'stamp'=>time(),
			'count'=>0
		);
		
		$this->db->insert('token',$token);
		
		return array('token'=>$token['value'],'type'=>'student','expires'=>$this->config->item('token_expire'));
		
	}
}