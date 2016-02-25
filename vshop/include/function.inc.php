<?php

/**
 * 获取缓存
 * @param  string $name 名称 模块:缓存名
 * @param  string $data 参数 主要包含 dir路径,获取缓存方式at
 * @return mixed
 */
function getCache($string,$data) {
	  $ar = explode(':',$string);
	  if(count($ar)>1){
		$module = $ar[0];
		$name = $ar[1];
	  }else{
		$module = CONTROLLER_NAME;
		$name = $ar[0];  
	  }
	  //集群部署
	  if(C('WEB_DEPLOY_TYPE')==1 && C('DATA_CACHE_TYPE')=='File'){
		GLOBAL $config;
		if(!$config){
			//载入配置
			require_once(C('INTERFACE_PATH')."Cache/config.php");
			require_once(C('INTERFACE_PATH')."Cache/ApiCache.class.php");
		}
		$cache = new ApiCache($config);
		$para['c'] = 'Local';
		$para['a'] = $data['at'] ? $data['at'] : 'Get';
		$para['name'] = $name;
		$data['name'] = $name;
		$dir = $data['dir'] ? $data['dir'] : '';
		$data['dir'] = $dir ? $module.'/'.$dir : $module;
		$data['module'] = $module;
		$result = json_decode($cache->get($para,$data),true);
		if($result['error_code']==0){
		  return $result['data'];
		}else{
		  return false;
		}
	  }else{
		  $dir = $module;
		  $options['temp'] = C('DATA_CACHE_PATH').$dir;
		  $options['filename'] = $name;
		  $data = S($name,'',$options);
		  return $data;
	  }
}

/**
 * 设置缓存
 * @param  string	$file	缓存名 模块:缓存名
 * @param  mixed	$value  缓存值 WEB_DEPLOY_TYPE配置集群部署可为空
 * @param  mixed	$expire 缓存有效时间
 * @param  array	$data   包含 status 返回数据 0:无需 1:需要 dir:缓存路径 
                            expire: 缓存有效时间 at:缓存方式
 * @return mixed
 */
function setCache($string, $value = NULL, $expire = 0, $data = array()) {
	$ar = explode(':',$string);
	if(count($ar)>1){
		$module = $ar[0];
		$name = $ar[1];
	}else{
		$module = CONTROLLER_NAME;
		$name = $ar[0];  
	}
	//集群部署
	if(C('WEB_DEPLOY_TYPE')==1 && C('DATA_CACHE_TYPE')=='File'){
		GLOBAL $config;
		if(!$config){
			//载入配置
			require_once(C('INTERFACE_PATH')."Cache/config.php");
			require_once(C('INTERFACE_PATH')."Cache/ApiCache.class.php");
		}
		$cache = new ApiCache($config);
		$para['c'] = $module;
		$para['a'] = 'Set'.ucwords($name);
		$para['name'] = $name;
		$data['name'] = $name;
		$data['dir'] = $data['dir'] ? $module.'/'.$data['dir'] : $module;
		$data['module'] = $module;
		//dump($data);//exit;
		$result = json_decode($cache->set($para,$data),true);
	}else{
		$dir = $module;
		$options['temp'] = C('DATA_CACHE_PATH').$dir;
		$options['filename'] = $name;
		$result = S($name,$value,$options);
	}
	if($result['error']==0){
	  return true;
	}else{
	  return false;
	}
}

//删除缓存文件
function delCache($string,$dir='') {
	  $ar = explode(':',$string);
	  if(count($ar)>1){
		$module = $ar[0];
		$name = $ar[1];
	  }else{
		$module = MODULE_NAME;
		$name = $ar[0];  
	  }
	  //集群部署
	  if(C('WEB_DEPLOY_TYPE')==1 && C('DATA_CACHE_TYPE')=='File'){
		GLOBAL $config;
		if(!$config){
			//载入配置
			require_once(C('INTERFACE_PATH')."Cache/config.php");
			require_once(C('INTERFACE_PATH')."Cache/ApiCache.class.php");
		}
		$cache = new ApiCache($config);
		$para['c'] = 'Local';
		$para['a'] = 'Del';
		$para['name'] = $name;
		$data['name'] = $name;
		$data['dir'] = $data['dir'] ? $module.'/'.$data['dir'] : $module;
		$data['module'] = $module;
		$result = json_decode($cache->rm($para,$data),true);
		if($result['error_code']==0){
		  return true;
		}else{
		  return false;
		}
	  }else{
		  $dir = $data['dir'] ? $data['dir'] : $module;
		  $expire = $data['expire'];
		  $options['temp'] = C('DATA_CACHE_PATH').$dir;
		  $options['filename'] = $name;
		  $cache = new Cache();
		  $cache = $cache->connect(C('DATA_CACHE_TYPE'),$options);
		  $cache->getInstance();
		  $data = $cache->rm($name);
		  return $data;
	  }
}


//路径
function get_dir($id) {
	$id = abs(intval($id));
	$id = str_pad($id, 10, "0", STR_PAD_LEFT);
	$dir1 = substr($id, 0, 3);
	$dir2 = substr($id, 3, 2);
	$dir3 = substr($id, 5, 2);
	$dir4 = substr($id, 7, 2);
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.$dir4.'/'.substr($id, -1);
}

//cookie加密解密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 3;	//note 随机密钥长度 取值 0-32;
				//note 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				//note 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				//note 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : C('sign'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

//获取客户端ip
function _get_ip(){
	if (isset($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], "unknown")) 
		$ip = $_SERVER['HTTP_CLIENT_IP']; 
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], "unknown")) 
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	else if (isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
	else if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
	else $ip = ""; 
	return ($ip);
}

//获取ip + 地址
function _get_ip_dizhi(){
	$opts = array(
		'http'=>array(
		'method'=>"GET",
		'timeout'=>5,)
	);		
	$context = stream_context_create($opts); 
	$ipmac=_get_ip();
	//$ipmac = '60.181.30.236';
	if(strpos($ipmac,"127.0.0.") === true)return '';
	$url_ip='http://ip.taobao.com/service/getIpInfo.php?ip='.$ipmac;
	$str = @file_get_contents($url_ip, false, $context);
	if(!$str) return false;
	$json=json_decode($str,true);
	//dump($json);exit;
	if($json['code']==0){
		$ipcity= $json['data']['region'].$json['data']['city'];
		$ip= $ipcity.','.$ipmac;
		$data['province'] = $json['data']['region'];
		$data['city'] = $json['data']['city'];
		$data['district'] = $json['data']['county'];
		$data['ip'] = $ipmac;
	}else{
		$ip="";
		$data['ip'] = false;
	}
	return $data;
}

//IP来源地址
function ipfrom($myip){
  require_once C('PUBLIC_INCLUDE')."qqwry.class.php";
  $ip = new IpLocation;
  $datas = $ip->getlocation($myip);
  $pv = $datas['province'];
  $city = $datas['city'];
  return iconv("GBK", "UTF-8",$pv.'/'.$city);
}

/**
 * 百度经纬度获取地理位置
 * @param $lng 经度
 * @param $lat 纬度
 */
function lan_lat_from($lat,$lng){
  $str =  file_get_contents('http://api.map.baidu.com/geocoder/v2/?location='.$lat.','.$lng.'&output=json&&ak='.C('baidu_map_key'));
  $array = json_decode($str,true);
  return $array;
}

/**
 * GPS经纬度转百度经纬度
 * @param $lng 经度
 * @param $lat 纬度
 */
function gps_to_baidu($lat,$lng){
  $str =  file_get_contents('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$lng.'&y='.$lat);
  $array = json_decode($str,true);
  return $array;
}

//生成订单号
function build_order_no($id = 0){
    //$mstr = str_pad(substr($mid,-1,6),6,0,STR_PAD_LEFT);
    /*
    $mstr = str_pad(mt_rand(0,9999),4,0,STR_PAD_LEFT);
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).$mstr;
    */
    $his = substr(time(), -5);
    $msec = substr(microtime(), 2, 5);
    $str = substr($his,0,2).substr($msec,0,2).substr($msec,2,1).substr($his,2,2).substr($msec,3,2).substr($his,4,1);
    $orderSn = date('y') . date('m') . date('d') . $str;
    return $orderSn;
}

//拼音处理
function GetPinyin($str,$ishead=0,$isclose=1){
    global $pinyins;
    $restr = "";
    $str = trim($str);
	$str = iconv("UTF-8", "GBK",$str);
    $slen = strlen($str);
    if($slen<2) return $str;
    if(count($pinyins)==0){
        $fp = fopen(C('PUBLIC_INCLUDE')."pinyin.db","r");
        while(!feof($fp)){
            $line = trim(fgets($fp));
            $pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
        }
        fclose($fp);
    }
    for($i=0;$i<$slen;$i++){
        if(ord($str[$i])>0x80)
        {
            $c = $str[$i].$str[$i+1];
            $i++;
            if(isset($pinyins[$c])){
                if($ishead==0) $restr .= $pinyins[$c];
                else $restr .= $pinyins[$c][0];
            }else $restr .= "-";
        }else if( eregi("[a-z0-9]",$str[$i]) ){    $restr .= $str[$i]; }
        else{ $restr .= "-";  }
    }
    if($isclose==0) unset($pinyins);
    return $restr;
}


/*
*   生成购买的云购码
*	num 		@生成个数
*	sid		    @商品id
*	ret_data	@返回信息
*/
function pay_get_shop_codes($num = 1,$sid = null){
   $model = M('shopcodes_2015');
   $data['s_id'] = $sid;
   $data['s_len'] = array('gt',0);
   $codes_one = $model->where($data)->order('s_cid desc')->find();
   if(!$codes_one){
     return false;
   }
   $codes_arr[$codes_one['s_cid']] = $codes_one;
   $codes_count_len = $codes_arr[$codes_one['s_cid']]['s_len'];
   if($codes_count_len < $num && $codes_one['s_cid'] > 1){
		for($i=$codes_one['s_cid']-1;$i>=1;$i--){
			$data['s_cid'] = $i;
			$codes_arr[$i] = $model->where($data)->order('s_cid desc')->find();
			$codes_count_len += $codes_arr[$i]['s_len'];			
			if($codes_count_len > $num)  break;
		}
   }
   if($codes_count_len < $user_num) $user_num = $codes_count_len;
   $ret_data['codes'] = array();
   $ret_data['code_len'] = 0;
   foreach($codes_arr as $icodes){			
		$u_num = $num;			
		$icodes['s_codes'] = unserialize($icodes['s_codes']);	
		$code_tmp_arr = array_slice($icodes['s_codes'],0,$u_num);
		//$ret_data['codes'] .= implode(',',$code_tmp_arr);
		$ret_data['codes'] = array_merge($ret_data['codes'],$code_tmp_arr);
		$code_tmp_arr_len = count($code_tmp_arr);
		$icodes['s_codes'] = array_slice($icodes['s_codes'],$u_num,count($icodes['s_codes']));
		//var_dump($icodes);exit;
		$icode_sub = count($icodes['s_codes']);		
		$icodes['s_codes'] = serialize($icodes['s_codes']);
		if(!$icode_sub){
			$sdata['s_len'] = 0;
			$sdata['s_codes'] = '';
			$wdata['id'] = $icodes['id'];
			$result = $model->where($wdata)->save($sdata);
			if(!$result)return false;
		}else{		
			$sdata['s_len'] = $icode_sub;
			$sdata['s_codes'] = $icodes['s_codes'];
			$wdata['id'] = $icodes['id'];
			$result = $model->where($wdata)->save($sdata);
			if(!$result)return false;
		}
		$ret_data['code_len'] += $code_tmp_arr_len;
		$num  = $num - $code_tmp_arr_len;
   }
   return $ret_data;
}

//手机验证
function validateMobile($mobile){
	return preg_match('/^1[3458][0-9]{9}$/',$mobile) ? true : false;
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = 1, $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 4 :
			$chars = 'abcdefghijklmnopqrstuvwxyz1234567890' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		$strlen = mb_strlen($addChars,'utf-8');
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= mb_substr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 ,'utf-8');
		}
	}
	return $str;
}


// 自动转换字符集 支持数组转换
function auto_charset($fContents,$from,$to){
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if(is_string($fContents) ) {
        if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            return iconv($from,$to,$fContents);
        }else{
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
            $_key =     auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
            if($key != $_key )
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else{
        return $fContents;
    }
}

// 循环创建目录
function mk_dir($dir, $mode = 0775) {

    if (is_dir($dir) || @mkdir($dir, $mode)){
		@chmod($dir,$mode);
        return true;
	}
    if (!mk_dir(dirname($dir), $mode)){
        return false;
	}
	$result = @mkdir($dir, $mode);
	@chmod($dir,$mode);
    return $result;
}


//字符截取
function str_cut($string, $length,$tags=true ,$dot = '...') {
	$coding = C("DEFAULT_CHARSET");
	if($tags)$string = strip_tags($string);
    $string = str_replace(array('', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strlen = mb_strlen($string,$coding);
    if ($strlen <= $length){
	  return $string;
	}
	$dotlen = mb_strlen($dot,$coding);
	$len = $length - $dotlen;
	$strcut = mb_substr($string,0,$len,$coding);
    return $strcut . $dot;
}

//时间戳转换
function toDate($time,$format='Y-m-d H:i:s') {
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date($format,$time);
}

//发送短信
function sent_msm($tel,$content){
	$sn = C('sms_id');
	$pwd = C('sms_pw');
	//要post的数据 
	$argv = array( 
		 'sn'=>$sn, ////替换成您自己的序列号
		 'pwd'=>strtoupper(md5($sn.$pwd)), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
		 'mobile'=>$tel,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
		 'content'=>$content,//短信内容
		 'ext'=>'',		
		 'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
		 'msgfmt'=>'',
		 'rrid'=>''
	); 
	foreach ($argv as $key=>$value) { 
	  if ($flag!=0) { 
		 $params .= "&"; 
		 $flag = 1; 
	  } 
	 $params.= $key."="; $params.= urlencode($value); 
	 $flag = 1; 
	}
	$length = strlen($params); 
	//创建socket连接 
	$fp = fsockopen("sdk.entinfo.cn",8061,$errno,$errstr,10) or exit($errstr."--->".$errno); 
	//构造post请求的头 
	$header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n"; 
	$header .= "Host:sdk.entinfo.cn\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
	$header .= "Content-Length: ".$length."\r\n"; 
	$header .= "Connection: Close\r\n\r\n"; 
	//添加post的字符串 
	$header .= $params."\r\n"; 
	//发送post的数据 
	fputs($fp,$header); 
	$inheader = 1; 
	while (!feof($fp)) { 
		$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
		if ($inheader && ($line == "\n" || $line == "\r\n")) { 
				 $inheader = 0; 
		 } 
		 if ($inheader == 0) { 
				// echo $line; 
		 } 
	} 
	$line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
	$line=str_replace("</string>","",$line);
	$result=explode("-",$line);
	if(count($result)>1){
		return false;
	}else{
		return true;
	}

}

//Token生成
function create_token($mid,$salt){
	$token = md5(base64_encode($mid.$salt.microtime()));
	return $token;
}

//Token保存
function set_token($member,$token){
	$model = M('Member_token');
	$t_data['member_id'] = $member['id'];
	$t_vo = $model->field('token')->where($t_data)->find();
	if($t_vo){
		if($t_vo['token']!=$token){
		  $t_sdata['token'] = $token;
		}
		$t_sdata['update_time'] = time();
		$model->where($t_data)->save($t_sdata);
	}else{
		$t_data['token'] = $token;
		$t_data['update_time'] = time();
		$model->add($t_data); 
	}	  
}

//获得缩略图
function get_thumb($url,$size){
   $basename = basename($url);
   $url = str_replace($basename,$size.'_'.$basename,$url);
   return $url;
}

//推送消息
function push_msg($baiduUserId,$mb_system,$MsgContent,$custom_content){
	require_once ( C('SITE_ROOT')."baiduapi/Channel.class.php" );
	if(!$baiduUserId || !$MsgContent){
	  //$thi->error('出错了！',1);
	  return false;
	}
	$_POST['MESSAGE_TYPE'] = 1;
	if($mb_system==1){
	  $status = $this->pushMessage_android($baiduUserId,$MsgContent,$custom_content);
	  if($status==0){
		$status = $this->pushMessage_ios($baiduUserId,$MsgContent,$custom_content);
		if($status){
		  $sdata['mb_system'] = 2;
		  $model->where($data)->save($sdata);
		}
	  }
	}else{
	  $status = pushMessage_ios($baiduUserId,$MsgContent,$custom_content);
	  if($status==0){
		$status = pushMessage_android($baiduUserId,$MsgContent,$custom_content);
		if($status){
		  $sdata['mb_system'] = 1;
		  $model->where($data)->save($sdata);
		}
	  }
	}
	return $status;
}

//推送消息
function push_msg2($uid,$MsgContent,$custom_content){
	$MsgContent = $_POST['MsgContent'];
	$custom_content = $_POST['custom_content'];
	require_once ( C('SITE_ROOT')."baiduapi/Channel.class.php" );
	$model = $model ? $model : MODULE_NAME;
	$action = $action ? $action : ACTION_NAME;
	$model = M('user');
	$data['id'] = $uid;
	$vo = $model->where('id,nickname,baiduUserId,mb_system')->where($data)->find();
	$baiduUserId = $vo['baiduUserId'];
	$mb_system = $vo['mb_system'];
	if(!$baiduUserId || !$MsgContent){
	  //$thi->error('出错了！',1);
	  return false;
	}
	$_POST['MESSAGE_TYPE'] = 1;
	if($mb_system==1){
	  $status = $this->pushMessage_android($baiduUserId,$MsgContent,$custom_content);
	  if($status==0){
		$status = $this->pushMessage_ios($baiduUserId,$MsgContent,$custom_content);
		if($status){
		  $sdata['mb_system'] = 2;
		  $model->where($data)->save($sdata);
		}
	  }
	}else{
	  $status = pushMessage_ios($baiduUserId,$MsgContent,$custom_content);
	  if($status==0){
		$status = pushMessage_android($baiduUserId,$MsgContent,$custom_content);
		if($status){
		  $sdata['mb_system'] = 1;
		  $model->where($data)->save($sdata);
		}
	  }
	}
	return $status;
}

//推送android设备消息
function pushMessage_android ($user_id,$MsgContent,$custom_content){
	$apiKey = C('baidu_apiKey2');
	$secretKey = C("baidu_secretKey2");
	//$MsgContent = '这是测试';
	$channel = new Channel ( $apiKey, $secretKey ) ;
	//推送消息到某个user，设置push_type = 1; 
	//推送消息到一个tag中的全部user，设置push_type = 2;
	//推送消息到该app中的全部user，设置push_type = 3;
	$push_type = 1; //推送单播消息
	$optional[Channel::USER_ID] = $user_id; //如果推送单播消息，需要指定user
	//optional[Channel::TAG_NAME] = "xxxx";  //如果推送tag消息，需要指定tag_name

	//指定发到android设备
	$optional[Channel::DEVICE_TYPE] = 3;
	//指定消息类型为通知
	$optional[Channel::MESSAGE_TYPE] = 0;
	//通知类型的内容必须按指定内容发送，示例如下：
	$message = '{ 
			"title": "最新消息",
			"description": "'.$MsgContent.'",
			"notification_basic_style":7,
			"custom_content": '.$custom_content.',
			"open_type":1
		}';
	
	$message_key = "msg_key";
	$ret = $channel->pushMessage ( $push_type, $message, $message_key, $optional ) ;
	if ( false === $ret )
	{
		return 0;
	}
	else
	{
		return 1;
	}
}

//推送ios设备消息
function pushMessage_ios ($user_id,$MsgContent,$custom_content){
	$custom_content = substr($custom_content,1,-1);
	$apiKey = C('baidu_apiKey');
	$secretKey = C("baidu_secretKey");
	$channel = new Channel ( $apiKey, $secretKey ) ;

	$push_type = 1; //推送单播消息
	$optional[Channel::USER_ID] = $user_id; //如果推送单播消息，需要指定user

	//指定发到ios设备
	$optional[Channel::DEVICE_TYPE] = 4;
	//指定消息类型为通知
	$optional[Channel::MESSAGE_TYPE] = $_POST['MESSAGE_TYPE'];
	//如果ios应用当前部署状态为开发状态，指定DEPLOY_STATUS为1，默认是生产状态，值为2.
	//旧版本曾采用不同的域名区分部署状态，仍然支持。
	$optional[Channel::DEPLOY_STATUS] = 2;
	//通知类型的内容必须按指定内容发送，示例如下：
	$message = '{ 
		"title": "最新消息",
		"description": "'.$MsgContent.'",
		"aps":{
			"alert":"'.$MsgContent.'",
			"sound":"",
			"badge":0
		},
		'.$custom_content.'
	}';
	
	$message_key = "msg_key";
	$ret = $channel->pushMessage ( $push_type, $message, $message_key, $optional ) ;
	if ( false === $ret ){
		return 0;
	}
	else{
		return 1;
	}
}

/**
 * 远程获取数据，POST模式
 * 注意：
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @param $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * return 远程输出的数据
 */
function httpPOST($url, $para, $input_charset = '') {

	if (trim($input_charset) != '') {
		$url = $url."_input_charset=".$input_charset;
	}
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl,CURLOPT_POST,true); // post传输数据
	curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);
	return $responseText;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * @param $url 指定URL完整路径地址
 * return 远程输出的数据
 */
function httpGET($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);
	return $responseText;
}

/**
 *  库存修改
 *  $products 商品
 *  $type 类型 1:减库存 2:加库存
 */
function stock_update($products,$type=1){
  $model = M('Product');
  $model2 = M('Product_item');
  $result = true;
  foreach($products as $product){
	if($result==false){
	  break;
	}
	$data['id'] = $product['product_id'];
	if($type==1){
	  if($product['item_id']){
		$data2['id'] = $product['item_id'];
	    $result = $model2->where($data2)->setDec('stock',$product['num']);
	  }
      $result = $model->where($data)->setDec('stock',$product['num']);
	}else{
	  $result = $model->where($data)->setInc('stock',$product['num']);
	}
  }
  return $result;
}

/**
 *  支付完成后
 *  $order 订单信息
 */
function after_pay($order){
	$configs = getCache('Config:list');
	$od_model = M('Order_detail');
	$mw_model = M('Member_wallet');
	$od_data['order_id'] = $order['id'];
	$list = $od_model->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'product` as b on a.product_id=b.id')->field('a.id,a.product_id,a.item_id,a.num,a.share_id,b.integral')->where($od_data)->select();
	//修改订单详情状态
	$od_sdata['status'] = 1;
	$od_model->where($od_data)->save($od_sdata);
	$result = true;
	//修改库存
	if($configs['site_inventorysetup']==2){
	  //产品减库存
	  $result = stock_update($list);
	}
	//积分处理
	$integral = 0;
	foreach($list as $item){
	  $integral += $item['integral'];
	}
	$mw_data['member_id'] = $order['member_id'];
	if($integral>0){
	  $result = $mw_model->where($mw_data)->setInc('score',$integral);
	  $member['id'] = $order['member_id'];
	  $member['member_name'] = $order['member_name'];
	  score_log($member,'order',$order['id'],$integral,'订单完成支付');
	}
	return $result;
}

/**
 +----------------------------------------------------------
 * 记录积分信息
 +----------------------------------------------------------
 * @param array  $member	会员信息
 * @param string $source	来源 如 order:订单 login:登录 register:注册
 * @param string $sourceid	来源对象id 如订单号
 * @param number $num		积分数量
 * @param string $desc		描述
 +----------------------------------------------------------
 * @return
 +----------------------------------------------------------
 */
function score_log($member,$source,$sourceid = 0,$num,$desc){
  $model = M('Score_log');
  $data['member_id'] = $member['id'];
  $data['member_name'] = $member['member_name'];
  $data['source'] = $source;
  $data['sourceid'] = $sourceid;
  $data['num'] = $num;
  $data['desc'] = $desc;
  $data['create_time'] = time();
  $result = $model->add($data);
  return $result;
}

/**
 +----------------------------------------------------------
 * 记录订单操作信息
 +----------------------------------------------------------
 * @param array  $data	数据
 +----------------------------------------------------------
 * @return
 +----------------------------------------------------------
 */
function order_log($data){
  $model = M('Order_track');
  if($data['id']){
    $result = $model->save($data);
  }else{
    $result = $model->add($data);
  }
  return $result;
}
/**
 +----------------------------------------------------------
 * 促销信息
 +----------------------------------------------------------
 * @access protected
 +----------------------------------------------------------
 * @param array   $products 产品数据
 * @param array   $options  过滤条件
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function get_promotion($products,$options = array()){
	$list = array();
	$product_ids = array();
	foreach($products as $product){
	  $product_id = $product['product_id'];
	  if(array_search($product['product_id'], $product_ids)===false){
		  $product_ids[] = $product_id;
	  }
	  //商品总价
	  $_products[$product_id]['total_fee'] += $product['total_fee'];
	}
	//订单促销产品信息
	$model = M('Product_pm_list');
	$data['product_id'] = array('in',$product_ids);
	$time = time();
	$data['btime'] = array('lt',$time);
	$data['etime'] = array('gt',$time);
	$data['status'] = 1;
	if($options)$data = array_merge($data,$options);
	$list = $model->where($data)->select();
	if($list){
	  foreach($list as $key=>$val){
	    $prom_id = $val['pm_id'];
		$info = unserialize($val['info']);
		if(!$list2[$prom_id])$list2[$prom_id] = $info;
		$product_id = $val['product_id'];
		//需要判断是否在该促销里
		$list2[$prom_id]['totle_fee'] += $_products[$product_id]['total_fee'];
	  }
	}
	foreach($list2 as $val){
	  if($val['limt']<=$val['totle_fee']){
	    $proms[] = $val;
	  }
	}
	return $proms;
}

//快递邮费
function shipping_fee($products,$ct_id){
 $cost = 0;//价钱
 $nw = 0;  //重量
 foreach($products as $product){
   if($product['is_free_shipping']==0){
	 $nw += $product['nw'];
   }
 }
 if($nw>0){
	 $configs = getCache('Config:list');
	 $shipping_id = $configs['shipping_id'];
	 $options['shipping_id'] = $shipping_id;
	 $options['dir'] = $shipping_id.'/';
	 $list = getCache('Shipping_region:list',$options);
	 $exp = $list[$ct_id];//快递费用
	 $fw_price = $exp['fw_price'] ? $exp['fw_price'] : 6;//首重
	 $aw_price = $exp['aw_price'] ? $exp['aw_price'] : 3;//增重费用
	 if($nw<=1){
	   $cost = $fw_price;
	 }else{
	   $cost = $fw_price+ceil($nw-1)*$aw_price;
	 }
 }
 return $cost;
}

//退款金额计算
function refund_pay($vo){
  $model = M('Order');
  $od_model = M('Order_detail');
  $info['pay_amount'] = $vo['price']*$vo['num']; //默认退款金额
  $info['msg'] = '';//默认退款信息
  if($vo['discount_fee'] || $vo['coupons_fee']){
	$total_fee = 0;
	$od_data['order_id'] = $vo['order_id'];
	$od_data['refund_status'] = 0;
	$od_data['id'] = array('neq',$vo['id']);
    $list = $od_model->field('price,num')->where($od_data)->select();
	foreach($list as $val){
	  $total_fee += $val['price']*$val['num'];
	}
  }
  
  if($vo['discount_fee']>0){
    $pl_model = M('Pm_list');
	$pl_data['order_id'] = $vo['order_id'];
	$pl_vo = $pl_model->where($pl_data)->find();
	$mdl = $pl_vo['pm_type'];
	$pm_model = M($mdl.'_promotion');
	$pm_data['id'] = $pl_vo['pm_id'];
	$pm = $pm_model->field('award_value,limt')->where($pm_data)->find();
	//当总价满足不了
	if($total_fee<$pm['limt']){
	  $info['discount_fee'] = $pm['award_value'];
	  $info['pay_amount'] -= $pm['award_value'];
	  $info['msg'] .= '促销活动使用条件无法满足，扣除金额'.$pm['award_value'].'<br>';
	}
  }
  if($vo['coupons_fee']>0){
    //$mc_model = M('Member_coupon');
	$mc_data['a.order_id'] = $vo['order_id'];
	$mc_data['a.member_id'] = $vo['member_id'];
	$coupon = $model->table('`'.C('DB_PREFIX').'member_coupon` as a')->join('`'.C('DB_PREFIX').'coupon` as b on a.coupon_id=b.id')->field('value,limt')->where($mc_data)->find();
	//当总价满足不了
	if($total_fee<$coupon['limt']){
	  $info['coupons_fee'] = $coupon['value'];
	  $info['pay_amount'] -= $coupon['value'];
	  $info['msg'] .= '优惠券使用条件无法满足，扣除金额'.$coupon['value'];
	}
  }
  return $info;
}

//ajax 成功信息返回
function ajaxSucReturn($data){
	if(!is_array($data)){
	  $msg['notice'] = $data;
	  $msg['error_code'] = 0;
	}else{
	  $msg = $data;
	  $msg['error_code'] = 0;
	}
	echo json_encode($msg);exit;
}

//ajax 错误信息返回
function ajaxErrReturn($data,$error_code=8002){
	if(!is_array($data)){
	  $msg['notice'] = $data;
	  $msg['error_code'] = (string)$error_code;
	}else{
	  $msg = $data;
	  $msg['error_code'] = (string)$error_code;
	}
	echo json_encode($msg);exit;
}

?>