

var storage = {
	set:function( key , value ){
		window.localStorage.setItem( key , value );
	},
	get:function( key  ){
		return window.localStorage.getItem( key );
	},
	remove:function( key ){
		window.localStorage.removeItem( key );
	}
};


var site = {
	//当前显示的区域的选择器
	$section:null,
	//当前用户的access_token
	token:'',
	type:'',//用户类型,值为student/teacher/admin
	expires:0,//token有效期
	base_url:'http://ifirst.sinaapp.com/server/',//服务器端基网址
	
	//构造函数
	init:function(selector){
		this.token = storage.get("token")|| '';
		this.type = storage.get("type")|| '';
		this.expires = storage.get("expires")|| 0;
		//检查是否登录
		if(selector =="#login" || selector=="#register"){
			if(this.logined()){
				this.go(this.type+".html");
				return ;
			}
		}else{
			if(!this.logined()){
				this.go('index.html');
				return ;
			}
		}
		this.$section = $(selector).removeClass("hide");;
		//this.render();
	},
	
	//页内跳转,这里可以加一点效果
	redirect:function(selector,callback){
		//根据选择器跳转到该section
		this.$section.addClass("hide");
		this.$section = $(selector);
		
		if(callback !== undefined)callback.call();//回调函数
		this.$section.removeClass("hide");
	},
	//页外跳转
	go:function(uri){
		if(uri === undefined)uri = (this.type||'index')+'.html';
		location.href = uri;
	},
	//Ajax获取数据
	get:function(uri,param){
		//
		var temp = ["token="+this.token || ''];
		if(param !== undefined){
			for( var p in param ){
			  temp.push(p + '=' + encodeURIComponent( param[p] || '' ) );
			}
		}
		var retdata;//返回的数据
		
		$.ajax({
			type:'GET',
			url:this.base_url+uri+"?"+temp.join('&'),
			async:false,//很重要
			dataType: 'json',
			success: function(data){
				if(data.succeed)retdata = data;
				else retdata = false;
			}
		});
		return retdata;
	},
	//Ajax提交数据
	post:function(uri,param){
		var retdata = false;
		$.ajax({
			type:'POST',
			url:this.base_url+uri+"?token="+(this.token),
			async:false,//很重要
			data:param,
			dataType: 'json',
			success: function(data){
				if(data.succeed)retdata = data;//作用域
				else console.log(data.error_info);
			}
		});
		return retdata;
	},
	
	//用户登入或注册
	sign:function(name,password,email){
		if(!this.logined()){
			var ret;
			if(email === undefined){
				ret = this.post('authorize/login',{'name':name,'password':password});
			}else{
				ret = this.post('authorize/register',{'name':name,'password':password,'email':email});
			}
			if(ret == false)return false;
			//console.log(ret);
			//存储
			storage.set('token',ret.token);
			storage.set('type',ret.type);
			storage.set('expires',ret.expires);
			this.token = ret.token;
			this.type = ret.type;
			this.expires = ret.expires;
		}
		return true;
	},
	//判断用户是否登录
	logined:function(){
		return  this.token && this.type && this.expires;
	},
	
	//用户登出
	logout:function(){
		storage.remove('token');
		storage.remove('type');
		storage.remove('expires');
		//
		this.go("index.html");
	},
	//一个简单的模板方法
	tmpl:function(data){
		$temp = this.$section.find(".tmpl");
		var html=$temp.html();
		if($.isArray(data)){//假如为数组
			for(k in data){
				for(key in data[k]){
					html = html.replace(new RegExp("\${"+key+"}","g"),data[k][key]);
				}
				//然后插到$temp的前面
				
			}
		}else{
			for(key in data){
				html = html.replace(new RegExp("\${"+key+"}","g"),data[key]);
			}
		}
		this.$section.html(html);
	},
	//加载数据
	render:function(data){
		if(this.$section.hasClass('rendered'))return ;//加载模板
		
		//this.tmpl(data);
		var id = this.$section.attr('id')+'-tmpl';
		var tmpl = ich[id](data);
		
		this.$section.find(".content").append(tmpl);

		this.$section.addClass('rendered').removeClass("hide");
	},
	get_render:function(uri,pamam){
		if(this.$section.hasClass('rendered'))return ;//加载模板
		var result = site.get(uri,pamam);
		if(result!=false){
			this.render(result);
		}else alert('fuck');
	},
	unrender:function(){
		this.$section.removeClass('rendered');
	}
};
//用以加载数据
function render($section){
	
}

var activate = ('createTouch' in document) ? 'touchstart' : 'click';
//公共操作
$(document).ready(function(){
	
});