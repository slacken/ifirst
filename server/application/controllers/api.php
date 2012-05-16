<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	/**
	 * 是否成功
	 * */
	public $succeed;
	
	public $data;//返回的数据
	
	public $time;//当前时间
	/**
	 * 当前用户实例
	 * */
	public $user;
	
	public $error_type = array(
		'no_token'=>1,
		'invalid_token'=>2,
		'token_expires'=>3,
		'out_limit'=>4,
		'no_action'=>5,
		'unpermissed'=>6,
		'buzz_no_content'=>7,
		'param_error'=>8,
		'wrong_data'=>9,
		'no_record'=>10
	);
	
	/**
	 * 定义存取权限
	 * */
	public $actions = array(
		'user'=>array('info'=>'student'),
		'score'=>array('get'=>'teacher','modify'=>'teacher'),
		'class'=>array('list'=>array('admin','student'),'add'=>'admin','apply'=>'student','apply_list'=>'teacher','permit'=>'teacher','reject'=>'teacher'),
		'buzz'=>array('add'=>'student','list'=>'student'),
		'teacher'=>array('info'=>'teacher'),
		'test'=>array('index'=>array('student','teacher','admin'))
	);
	
	/**
	 * 测试API
	 * */
	public function test($action,$param=array()){
		
		$this->user['type'] = 5;
		$this->user['class_id'] = 2;
		
		$this->teacher('info');
		$this->output->enable_profiler(TRUE);
	}
	public function teacher($action,$param=array()){
		switch ($action){
			case 'info':{//老师用来获取概要班级信息
				//申请列表
				$this->data['apply'] = $this->db->where('apply.class_id',$this->user['class_id'])
												->where('user.type',1)//限学生
												->join('user','user.id = apply.user_id','left')
												->select('apply.id,user.name,user.email')
												->get('apply')
												->result_array();
				
				//成员列表
				$this->data['member'] = $this->db->where('user.class_id',$this->user['class_id'])
												->where('user.type',1)//限学生
												->select('user.name,user.email,user.id,score.gpa')
												->order_by('score.gpa','desc')
												->join('score','score.user_id = user.id')
												->get('user')
												->result_array();
				$this->succeed = TRUE;
			}break;
		}
	}
	/**
	 * 用户相关信息
	 * */
	public function user($action,$param=array()){
		switch ($action){
			case 'info':{//学生获取自己的全部信息
				$this->data['email'] = $this->user['email'];
				$this->data['name'] = $this->user['name'];
				$this->data['id'] = $this->user['id'];
				//然后获取学生的分数
				$score = $this->db->where('user_id',$this->user['id'])->get('score')->row_array();
				
				assert(!empty($score));
				
				$this->data['scores'] = unserialize($score['scores']);//分数：array(array('name','credit','mark';)
				//计算绩点和班级排名
				$this->data['rank'] = $this->db->where('class_id',$this->user['class_id'])
											->where('gpa >',$score['gpa'])
											->count_all('score') + 1;
				//计算绩点
				$this->data['credit'] = 0;//暂时这样啦
			}break;
			default:{
				$this->succeed = FALSE ;
				$this->data['error'] = $this->error_type['param_error'];
				$this->data['error_info'] = "参数有误";
			}
		}
	}
	/**
	 * 分数相关信息
	 * */
	public function score($action,$param=array()){
		switch ($action){
			case 'get':{//获取某个学生的分数信息
				$id = $this->input->get('id');
				if($id){
					$score = $this->db->where('score.class_id',$this->user['class_id'])
									->where('score.id',intval($id))
									->join('user','user.id = score.user_id')
									->select('score.scores,score.id,user.name,user.email')
									->get('score')
									->row_array();
					if(empty($score)){
						$this->succeed = FALSE ;
						$this->data['error'] = $this->error_type['param_error'];
						$this->data['error_info'] = "参数有误";
					}else{
						//返回数据
						$this->data['scores'] = unserialize($score['scores']);
						$this->data['name'] = $score['name'];
						$this->data['email'] = $score['email'];
						$this->data['id'] = $score['id'];
						$this->succeed = TRUE;
					}
				}
				else{
					$this->succeed = FALSE ;
					$this->data['error'] = $this->error_type['param_error'];
					$this->data['error_info'] = "缺少参数";
				}
			}break;
			case 'modify':{//老师修改学生的成绩
				$scores = $this->input->post('scores');
				$id = $this->input->post('id');
				if(is_numeric($id) && $scores && is_array($scores)){
					$score = $this->db->where('class_id',$this->user['class_id'])
									->where('id',$id)
									->get('score')
									->row_array();
					if(empty($score)){
						$this->succeed = FALSE;
						$this->data['error'] = $this->error_type['no_record'];
						$this->data['error_info'] = "不存在该记录";
					}else{
						$score['scores'] = unserialize($score['scores']);
						if(count($score['scores']) != count($scores)){
							$this->succeed = FALSE;
							$this->data['error'] = $this->error_type['param_error'];
							$this->data['error_info'] = "参数有误";
						}else{
							foreach ($score['scores'] as $k=> $s){
								$score['scores'][$k]['mark'] = $scores[$k];
							}
							$this->db->where('id',$id)->update('score',array('scores'=>serialize($score['scores'])));
							$this->succeed = TRUE;
						}
					}
				}else{
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['param_error'];
					$this->data['error_info'] = "参数有误";
				}
			}break;
		}
	}
	/**
	 * 班级相关信息
	 * */
	public function clas($action,$param=array()){
		switch($action){
			case 'list':{//获取班级列表
				$this->data['classes'] = $this->db->select('id,name')->get('class')->result_array();
				
				$this->succeed = TRUE;
			}break;
			case 'apply':{//申请加入班级
				$class_id = $this->input->get('class');
				if(is_numeric($class_id)){
					$apply = array(
						'user_id'=>$this->user['id'],
						'class_id'=>intval($class_id)
					);
					$this->db->insert('apply',$apply);
					$this->succeed = TRUE;
				}else{
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['param_error'];
					$this->data['error_info'] = "参数有误";
				}
			}break;
			case 'apply_list':{//获取申请列表
				$this->data['list'] = $this->db->where('apply.class_id',$this->user['class_id'])
											->join('user','user.id = apply.user_id','left')
											->select('apply.id,apply.user_id,user.name')
											->get('apply')
											->result_array();
				//
			}break;
			case 'permit':{//允许加入
				//老师的行为
				$apply_id = $this->input->get('id');
				if($apply_id){
					$apply_id = intval($apply_id);
					$apply = $this->db->where('id',$apply_id)->get('apply')->row_array();
					if(!empty($apply) && $this->user['class_id'] == $apply['class_id']){
						$this->db->where('id',$apply['user_id'])->update('user',array('class_id'=>$apply['class_id']));
						//然后把申请记录删掉（还可以生成通知，暂无）
						$this->db->where('id',$apply_id)->delete('apply');
						
						//然后是生成分数记录
						$class = $this->db->where('id',$apply['class_id'])->get('class')->row_array();
						assert(!empty($class));
						$courses = unserialize($class['courses']);
						
						$score = array(
							'user_id'=>$apply['user_id'],
							'class_id'=>$apply['class_id'],
							'gpa'=>0,
							'scores'=>''//格式是array(array('name','credit','mark';))
						);
						$score_temp = array();
						foreach ($courses as $c){
							$course_temp[] = array_merge($c,array('mark'=>0));
						}
						$score['scores'] = serialize($score_temp);
						$this->db->insert('score',$score);
						$this->succeed = TRUE;
					}else{
						$this->succeed = FALSE;
						$this->data['error'] = $this->error_type['param_error'];
						$this->data['error_info'] = "没有这样的一次提交记录";
					}
				}else{
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['param_error'];
					$this->data['error_info'] = "参数有误";
				}
			}break;
			case 'reject':{//拒绝加入
				$apply_id = $this->input->get('id');
				if($apply_id){
					$apply_id = intval($apply_id);
					$this->db->where('id',$apply_id)->where('class_id',$this->user['class_id'])->delete('apply');
					$this->succeed = TRUE;
				}else{
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['param_error'];
					$this->data['error_info'] = "参数有误";
				}
			}break;
			case 'add':{//增加一个班级
				//包括什么
				if($this->input->post('name') && $this->input->post('teachers') && $this->input->post('courses')){
					$teacher_ids = explode(',', clean_string($this->input->post('teachers')));
					$course_temp = explode(',', clean_string($this->input->post('courses')));
					$courses = array();
					if(!empty($course_temp)){
						$this->succeed = TRUE;
						$course_temp = array_unique($course_temp);
						
						foreach ($course_temp as $c){
							$match = array();
							if(preg_match('/^([\S]*?)\(([0-9\.]{1,3})\)/', $c,$match) !== 1){
								$this->succeed = FALSE;
								$this->data['error'] = $this->error_type['wrong_data'];
								$this->data['error_info'] = "数据格式错误";
								break;
							}
							$match[] = array('name'=>$match[1],'credit'=>$match[2]);
						}
						foreach ($teacher_ids as $k=> $t){
							if(!is_numeric($t)){
								$this->succeed = FALSE;
								$this->data['error'] = $this->error_type['wrong_data'];
								$this->data['error_info'] = "数据格式错误";
								break;
							}else{
								$teacher_ids[$k] = intval($t);
							}
						}
						$teacher_ids = array_unique($teacher_ids);
						if($this->succeed){//数据格式正确
							//生成班级，然后更新老师的class_id
							$class = array(
								'name'=>clean_string($this->input->post('name')),
								'courses'=>serialize($courses)
							);
							$this->db->insert('class',$class);
							$id = $this->db->insert_id();
							$this->db->where_in('id',$teacher_ids)->update('user',array('class_id'=>$id));
							
							$this->succeed = TRUE;
						}
					}else{
						$this->succeed = FALSE;
						$this->data['error'] = $this->error_type['wrong_data'];
						$this->data['error_info'] = "数据格式错误";
					}
				}
			}break;
			
		}
	}
	
	public function buzz($action,$param=array()){
		switch ($action){
			case 'add':{
				if($this->input->post('content')){
					$buzz = array(
						'content'=>clean_text($this->input->post('content')),
						'pubtime'=>$this->time,
						'user_id'=>$this->user['id'],
						'class_id'=>$this->user['class_id']
					);
					$this->db->insert('buzz',$buzz);
					$this->succeed = TRUE;
				}else{
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['buzz_no_content'];
					$this->data['error_info'] = "没有输入内容";
				}
			}break;
			case 'list':{
				$page = 1;
				$per_page = 10;
				$today_start = $this->time - $this->time%(24*3600);
				if($this->input->get('page'))$page = intval($this->input->get('page'));
				$this->data['buzz'] = $this->db->where('buzz.class_id',$this->user['class_id'])
											->where('pubtime >',$today_start)
											->limit($per_page,($page-1)*$per_page)
											->order_by('buzz.id','desc')
											->join('user','user.id = buzz.user_id','left')
											->select('content,name')
											->get('buzz')
											->result_array();
				$this->data['more'] = (count($this->data['buzz']) == $per_page);//是否还有更多
				$this->succeed = TRUE;
			}break;
			default:{
				$this->succeed = FALSE;
				$this->data['error'] = $this->error_type['param_error'];
				$this->data['error_info'] = "参数错误！";
			}
		}
	}
	
	/**
	 * API控制器
	 * */
	public function _remap($name,$param = array()){
		
		
		$this->time = time();
		$this->data = array();
		$this->succeed = TRUE;
		$this->user = array();
		
		if(!$this->input->get('token')){
			$this->succeed = FALSE;
			$this->data['error'] = $this->error_type['no_token'];
			$this->data['error_info'] = "未提供存取标识";
		}else{
			$token = clean_string($this->input->get('token'));
			$access = $this->db->where('value',$token)->get('token')->row_array();
			
			//var_dump($access);
			
			if(empty($access)){
				$this->succeed = FALSE;
				$this->data['error'] = $this->error_type['invalid_token'];
				$this->data['error_info'] = "不合法的存取标识";
			}elseif ($this->time - $access['stamp'] > $this->config->item('token_expire')){
				$this->succeed = FALSE;
				$this->data['error'] = $this->error_type['token_expires'];
				$this->data['error_info'] = "存取标识过期了";
			}elseif($access['count'] > $this->config->item('token_limit')){
				
				$this->succeed = FALSE;
				$this->data['error'] = $this->error_type['out_limit'];
				$this->data['error_info'] = "存取过于频繁";
				
				//然后做出一些处理
				
			}else{

				if(empty($param))$param[0] = 'index';
				
				$action = $param[0];
				
				//是否提供此API
				if(!key_exists($name, $this->actions) || !isset($this->actions[$name][$action])){
					
					$this->succeed = FALSE;
					$this->data['error'] = $this->error_type['no_action'];
					$this->data['error_info'] = "不存在此API";
				}else{
					
					//查询用户
					$this->user = $this->db->where('id',$access['user_id'])->get('user')->row_array();
					//var_dump($this->user);
					assert(!empty($this->user));//应该不为空的
					
					$this->load->model('user','userModel');
					
					$type_name = array_flip($this->userModel->type);
					
					if(($type_name[$this->user['type']] === $this->actions[$name][$action]) || (is_array($this->actions[$name][$action]) && in_array($type_name[$this->user['type']], $this->actions[$name][$action]))){
						
						if($name == 'class')$name = 'clas';
						//here we go...
						$this->$name($action,array_shift($param));
						
					}else{
						$this->succeed = FALSE;
						$this->data['error'] = $this->error_type['unpermissed'];
						$this->data['error_info'] = "无权访问此API";
					}
				}
			}
		}
		$this->data['succeed'] = $this->succeed;
		
		echo json_encode($this->data);//json_encode
		//var_dump($this->data);
	}
}
