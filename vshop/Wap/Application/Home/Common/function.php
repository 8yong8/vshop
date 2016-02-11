<?php 

//状态信息
function rs($value,$str){
  $array = explode('@',$str);
  foreach($array as $arr){
    $val = explode('#',$arr);
	if($val['0']==$value){
	  if(!$val['2']){
		$color = 'black';
	  }else{
	    $color = $val['2']; 
	  }
	  return "<font color='".$color."'>".$val['1']."</font>";
	  break;
	}
  }
}

//隐藏名称
function name_hide($name){
  $leng = mb_strlen($name,'utf8');
  if($leng>2){
    $str = mb_substr($name,0,1,'utf8').'***'.mb_substr($name,$leng-1,$leng,'utf8');
  }else{
    $str = mb_substr($name,0,1,'utf8').'***';
  }
  return $str;
}


/**
 *  URL重新组装
 *  $url 原始URL
 *  $resetField 重置字段
 */
function resetUrl($url,$resetField){
	$fields = explode(',',$resetField);
	$parse = parse_url($url);
	if(isset($parse['query'])) {
		
		parse_str($parse['query'],$params);
		foreach($fields as $field){
		  unset($params[$field]);
		}
		$resetUrl   =  $parse['path'].'?'.http_build_query($params);
	}else{
		if(strpos($url,'?')){
			$resetUrl = $url;
		}else{
			$resetUrl = $url.'?';
		}
		
	}
	return $resetUrl;
}


//判断微信
function isWeixin(){ 
	if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false) {
		return true;
	}	
	return false;
}

//获取访客操作系统
function GetOs(){  
   if(!empty($_SERVER['HTTP_USER_AGENT'])){  
    $OS = $_SERVER['HTTP_USER_AGENT'];  
    if (preg_match('/win/i',$OS)) {  
     $OS = 'Windows';  
    }elseif (preg_match('/mac/i',$OS)) {  
     $OS = 'MAC';  
    }elseif (preg_match('/linux/i',$OS)) {  
     $OS = 'Linux';  
    }elseif (preg_match('/unix/i',$OS)) {  
     $OS = 'Unix';  
    }elseif (preg_match('/bsd/i',$OS)) {  
     $OS = 'BSD';  
    }else {  
     $OS = 'Other';  
    }  
    return $OS;    
   }else{
	 return "获取访客操作系统信息失败！";
   }     
}  

//获得访客浏览器类型  
function GetBrowser(){  
   if(!empty($_SERVER['HTTP_USER_AGENT'])){  
    $br = $_SERVER['HTTP_USER_AGENT'];  
    if (preg_match('/MSIE/i',$br)) {      
               $br = 'MSIE';  
             }elseif (preg_match('/Firefox/i',$br)) {  
     $br = 'Firefox';  
    }elseif (preg_match('/Chrome/i',$br)) {  
     $br = 'Chrome';  
       }elseif (preg_match('/Safari/i',$br)) {  
     $br = 'Safari';  
    }elseif (preg_match('/Opera/i',$br)) {  
        $br = 'Opera';  
    }else {  
        $br = 'Other';  
    }  
    return $br;  
   }else{return "获取浏览器信息失败！";}   
} 

//记录登录信息
function login_log($authInfo){
  $model = M('Login_log');
  $add_data['member_id'] = $authInfo['id'];
  $add_data['member_name'] = $authInfo['username'];
  $add_data['ip'] = $ip;
  $add_data['province'] = $dizhi['province'];
  $add_data['city'] = $dizhi['city'];
  $add_data['district'] = $dizhi['district'];
  $add_data['browser'] = GetBrowser();
  $add_data['os'] = GetOs();
  $model->add($add_data);  
}

/**
 +----------------------------------------------------------
 * 更新产品数据
 +----------------------------------------------------------
 * @param string $field 字段
 * @param string $type  数据类型 1:增加 2:减少
 * @param string $id    数据对象id
 +----------------------------------------------------------
 * @return
 +----------------------------------------------------------
 */
function update_product($field, $type = 1, $id){
	$model = D('Product');
	$data['id'] = $id;
	if($type==1){
	  $result = $model->where($data)->setInc($field);
	}else{
	  $result = $model->where($data)->setDec($field);
	}
	return $result;
}

?>