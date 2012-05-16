<?php
/**
 * 自定义的缓存模块，屏蔽不同缓存类型的差异;负责对数据的encode和decode
 * 缓存命名规范：
 * 1.对于单个数据表查询db_table_domain_value或者db_table_id_value
 * 2.对于某个函数（即复杂查询）fun_name_pname_pvalue
 * 3.待添加
 * */
class Mycache extends CI_Model{
	/**
	 * 缓存类型
	 * */
	private $type;
	
	/**
	 * 缓存是否有效
	 * */
	private $enable;
	
	/**
	 * 连接句柄
	 * */
	private $instance;
	
	/**
	 * 目前可用的缓存
	 * */
	public $cache_availabe = array('sae_memcache','memcached');
	
	public function __construct(){
		parent::__construct();
		$this->type = $this->config->item('cache_type');
		
		$this->init();//初始化
	}
	
	/**
	 * 设置当前缓存是否有效
	 * */
	private function init(){
		if(!in_array($this->type,$this->cache_availabe)){
			$this->enable = FALSE;//默认是无效的
			return ;
		}
		//
		switch ($this->type){
			case 'sae_memcache':{
				if(function_exists('memcache_init')){
					$this->instance = memcache_init();
					if($this->instance != FALSE){
						$this->enable = TRUE;
						return ;
					}
				}
			}break;
			case 'memcached':{
				if(class_exists('Memcached')){
					$this->instance = new Memcached();//貌似要add_server
					$this->enable = TRUE;
					return ;
				}
			}break;
			default:{}
		}
		$this->enable = FALSE;
	}
	
	public function debug(){
		if(!$this->enable)echo ('not enabled.');
		
		if($this->type == 'memcached'){
			var_dump($this->instance->getStats());
		}
	}
	
	/**
	 * 对于特殊的缓存类型，可特别设置
	 * */
	public function set_type($type){
		$this->type = $type;
		$this->init();//重新初始化
	}
	
	/**
	 * 获取,假如不存在则返回FALSE,key可以为字符串或者数组
	 * */
	public function get($key){
		if(!$this->enable)return FALSE;
		
		if($this->type == 'sae_memcache'){
			$result = $this->instance->get($key,MEMCACHE_COMPRESSED);
			
			if($result == FALSE)return FALSE;
			
			return unserialize($result);
		}
		
		return FALSE;//不存在或失败时严格返回FALSE
	}
	
	/**
	 * 设置,$expire为过期时间,0表示永不过期；暂时设置为1个小时
	 * */
	public function set($key,$value,$expire = 3600){
		if(!$this->enable)return ;
		if($this->type == 'sae_memcache'){
			$this->instance->set($key,serialize($value),MEMCACHE_COMPRESSED,$expire);
		}
		return ;
	}
	
	/**
	 * 删除
	 * */
	public function delete($key){
		if(!$this->enable)return ;
		if($this->type == 'sae_memcache')$this->instance->delete($key);
	}
}