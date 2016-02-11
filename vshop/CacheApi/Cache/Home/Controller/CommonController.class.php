<?php
namespace Home\Controller;
use Think\Controller;
require_once C('PUBLIC_INCLUDE')."function.inc.php";
class CommonController extends Controller {

  /**
   * 初始化验证
   */
  function _initialize() {
	if(!$_GET['nonce_str']){
	  ajaxErrReturn('随机字符串必须');
	}

	if(!$_GET['time_stamp']){
	  ajaxErrReturn('创建时间戳必须');
	}

	if(!IS_POST){
	  ajaxErrReturn('数据必须');
	}

	//判断是否内网ip
	if(!check_ip()){
	  ajaxErrReturn('非内网IP');
	}

	//3分钟有效期
	if($_REQUEST['time_stamp']+180<time()){
	  ajaxErrReturn('签名过期');
	}
	
	$model = D('Api');
	require_once(C('INTERFACE_PATH')."Cache/config.php");
	require_once(C('INTERFACE_PATH')."Cache/lib/core.function.php");
	require_once(C('INTERFACE_PATH')."Cache/lib/md5.function.php");
	require_once(C('INTERFACE_PATH')."Cache/lib/rsa.function.php");
	
	$appid = $_GET['appid'];
	$data['py_name'] = 'cache';
	$data['appid'] = $appid;
	$vo = $model->where($data)->find();
	if(!$vo){
	  ajaxErrReturn('无此应用');
	}
	$appkey = $vo['appkey'];
	//除去待签名参数数组中的空值和签名参数
	$para = $_GET;
	$para['c'] = CONTROLLER_NAME;
	$para['a'] = ACTION_NAME;
	$para_filter = CacheParaFilter($para);
	//对待签名参数数组排序
	$para_sort = CacheArgSort($para_filter);
	$prestr = CacheCreateLinkstring($para_sort);
	$timestamp = $_GET['time_stamp'];
	$sign = $_POST['sign'];
	switch ($_POST['sign_type']) {
		case 'RSA':
			$result = CacheRsaVerify($prestr, $config['public_key_path'], $sign);	
			break;
		case 'MD5':
			$result = CacheMd5Sign($appid, $appkey, $prestr, $timestamp, $sign);	
			break;
		case 2:
			$result = CacheMd5Sign($appid, $appkey, $prestr, $timestamp, $sign);	
			break;
	}	

	//dump($result);exit;
	if(!$result){
	  ajaxErrReturn('验证失败');
	}
  }

  /**
   * 设置缓存
   */
  protected function SetCache($name,$value){
	  $expire = $_POST['expire'] ? $_POST['expire'] : C('DATA_CACHE_TIME');
	  if(C('DATA_CACHE_TYPE')=='Memcache'){
		$expire = $expire==-1 ? 0 : $expire;
		$name = $_POST['module'].':'.$_POST['name'];
	  }
	  $expire = $_POST['expire'] ? $_POST['expire'] : 0;
	  $dir = $_POST['dir'] ? $_POST['dir'] : '';
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  $options['filename'] = $name;
	  /*
	  $cache = new \Think\Cache();
	  $cache =$cache->connect(C('DATA_CACHE_TYPE'),$options);
	  $cache->getInstance();
	  $result = $cache->set($name,$value,$expire);
	  */
	  $result = S($name,$value,$options);
	  return $result;
  }

  /**
   * get通道
   */
  public function GetChannel(){
	  echo json_encode( $this->GetCache($_POST['name'],$_POST['module'],$_POST['dir']));
  }

  /**
   * 获取缓存
   */
  protected function GetCache($name,$module='',$dir=''){
	  $module = $module ? $module : MODULE_NAME;
	  $cache = new \Think\Cache();
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  $options['filename'] = $name;
	  //echo $options['temp'];exit;
	  $cache =$cache->connect(C('DATA_CACHE_TYPE'),$options);
	  $cache->getInstance();
	  $data = $cache->get($name);
	  if($data==false){
		//生成缓存
		$class = $module.'Action';
		$action_name = 'Set'.ucwords($name);
		//echo $action_name;exit;
		$_POST['status'] = 1;
		$_POST['from'] = 'self';
		$model = new $class();
		$data = $model->$action_name();
	  }
	  $result['error_code'] = 0;
	  $result['data'] = $data;
	  return $result;
  }

  /**
   * 删除缓存
   */
  public function Del(){
	  $name  = $_POST['name'];
	  $module = $_POST['module'];
	  $dir = $_POST['dir'] ? $_POST['dir'] : '';
	  $cache = new Cache();
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  $options['filename'] = $name;
	  $cache =$cache->connect(C('DATA_CACHE_TYPE'),$options);
	  $cache->getInstance();
	  $data = $cache->rm($name);
	  $result['error_code'] = 0;
	  $result['data'] = $data;
	  echo json_encode($result);exit;
  }

  /**
   * 方法不存在
   */
  public function _empty(){
	ajaxErrReturn('方法不存在');
  }

}
?>