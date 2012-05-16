<?php
if ( ! function_exists('strcut')){
	//字符串截取
	function strcut($str_cut,$length=30,$tail=''){
		$length = 3*$length;
	 	$str_cut=strip_tags(trim($str_cut));
		if (strlen($str_cut) > $length){
	  		for($i=0; $i < $length; $i++)
	  			if(ord($str_cut[$i]) > 128)$i+=2;//如果是汉字
	 		$str_cut = substr($str_cut,0,$i).$tail;
		}
		return $str_cut;
	}
}
if ( ! function_exists('site_url'))
{
	function site_url($uri = '')
	{
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}
if ( ! function_exists('rough_time')){
	//时间
	function rough_time($time){
	    $limit = time() - intval($time);
	    if($limit < 60 && $limit > 0){
	        return $limit .'秒钟前';
	    }
	    elseif($limit >= 60 && $limit < 3600){
	        return floor($limit/60) . '分钟前';
	    }
	    elseif($limit >= 3600 && $limit < 86400){
	        return '约'.floor($limit/3600) . '小时前';
	    }
	    elseif($limit >= 86400 and $limit<604800){
	        return floor($limit/86400) . '天前';//一周内
	    }
	    elseif($limit >= 604800){
	        return date('Y-n-j',$time);
	    }elseif ($limit <= 0){
	    	return '刚刚';
	    }
	    else{
	        return '';
	    }
	}
}
if ( ! function_exists('headto')){
	function headto($url = '',$http_response_code = 302){
		if (preg_match('#^(http[s]?|ftp)://#i', $url) !== 1){
			if (empty($url) || $url=='/') {
				$url = '/';
			}else{
				$url='/'.ltrim($url,'/');
			}
		}
		die(header('Location: ' . $url,TRUE,$http_response_code));
	}
}
if ( ! function_exists('js_redirect')){
	//JS跳转
	function js_redirect($url='/',$time=0){
		if (empty($url) || $url=='/') {
			$url = '/';
		}else{
			$url='/'.ltrim($url,'/');
		}
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location.href=\"{$url}\";},1000*{$time});</script>";
	}
}
/*
if ( ! function_exists('clean_text')){
	function clean_text($text){
		$text = str_replace(array('<?php','?>'),'',$text);
		$unTags=array('script','html','object','embed','iframe','frame','body','head','form','applet');
		foreach ($unTags as $tag){
			if( preg_match_all( '/<'.$tag.'[^>]*>([^<]*)<\/'.$tag.'>/iu', $text, $found) ){
                $text = str_replace($found[0],$found[1],$text);
            }
		}
		$text = preg_replace( '/(<('.join('|',$unTags).')(\n|\r|.)*\/>)/iu', '', $text);//单标签
		return $text;
	}
}
*/
//验证是否为图片
function _url_replace_callback($matches){
	if(preg_match('#http://ww[\d]\.sinaimg\.cn/[\w]*/[\w]*\.(jpg|gif)#i',$matches[0]) === 1){
			return "<img src=\"{$matches[0]}\" class=\"outimg\" />";
	}else{
		return "<a href='{$matches[0]}' target='_blank'>{$matches[0]}</a>";
	}
}
if ( ! function_exists('clean_text')){
	function clean_text($text){
		
		$text = nl2br(str_replace(array('<','>','\'','\"'), array('&lt;','&gt;','&#039;','&quot;'), $text));
		
		
		$text = preg_replace_callback('#(http[s]?|ftp)://([\w-]*\.)+([a-z]{2,4})(/[\w-./?%&=]*)?#i', '_url_replace_callback',$text);
				
		return $text;
	}
}
if ( ! function_exists('clean_string')){
	function clean_string($string){
		return htmlspecialchars(trim($string),ENT_QUOTES);
	}
}
if ( ! function_exists('rawtext')){
	function rawtext($string){
		return nl2br(strip_tags($string));
	}
}
if(!function_exists('is_email')){
	function is_email($str){
		return preg_match('/^[\w\.-]*?@([\w]+\.)+([a-z]{2,4})$/i', $str) == 1;
	}
}





