<?php
class hook{
		//做用户权限管理，防频繁登录等
		private $model;  
	    private $method;  
	    private $CI;
	    public function __construct(){
	    	$this->CI = & get_instance();
	    	$this->model = $this->CI->uri->rsegments[1];
	    	$this->method = $this->CI->uri->rsegments[2];
	    }
	    
	    
	    /**
	     * 权限检测，增加跳转功能
	     * */
	    public function post_controller_constructor(){
	    	/*
	    	if(!$this->CI->user->normal_quest()){
	    		show_404('error/404');
	    		exit;
	    	}
	    	*/
	    	//请求的默认参数
	    	$request = array(
	    		'valid'=>false,//请求是否合法
	    		'jump'=>false,//是否直接跳转
	    		'url'=>'404',//跳转地址
	    		'back'=>false//是否需要跳回，一般为登录页
	    	);
	    	$current_url = $this->CI->uri->uri_string();
	    	$this->CI->load->config('acl');
	    	$acl_config = config_item('acl');
	    	$type = $this->CI->user->type();//用户类型
	    	if(isset($acl_config[$this->model][$this->method]) ){
	    		if($acl_config[$this->model][$this->method][0] & $type){
	    			$request['valid'] = true;
	    		}else{
	    			//检测跳转URL
	    			if(isset($acl_config[$this->model][$this->method][1])){
	    				$request['jump'] = true;
	    				$request['url'] = $acl_config[$this->model][$this->method][1];
	    				if(isset($acl_config[$this->model][$this->method][2]))$request['back'] = TRUE;
	    			}
	    		}
	    	}elseif (isset($acl_config[$this->model]['default'])){
	    		if($acl_config[$this->model]['default'][0] & $type){
	    			$request['valid'] = true;
	    		}else{
	    			if(isset($acl_config[$this->model]['default'][1])){
	    				$request['jump'] = true;
	    				$request['url'] = $acl_config[$this->model]['default'][1];
	    				if(isset($acl_config[$this->model]['default'][2]))$request['back'] = TRUE;
	    			}
	    		}
	    	}
	    	elseif($acl_config['default'][0] & $type){
	    		$request['valid'] = true;
	    	}
	    	elseif(isset($acl_config['default'][1])){
	    		$request['jump'] = true;
	    		if(isset($acl_config['default'][2]))$request['back'] = TRUE;
	    		$request['url'] = $acl_config['default'][1];
	    	}
	    	
	    	if($request['valid'])return ;//请求合法
	    	elseif($request['jump']){
	    		if($request['back'])headto($request['url'].'?url='.$current_url);
	    		else headto($request['url']);
	    		exit;
	    	}
	    	else{
	    		//headto('/');
	    		show_404();
	    		exit;
	    	}
	    }
}