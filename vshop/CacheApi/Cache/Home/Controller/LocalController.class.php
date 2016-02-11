<?php
namespace Home\Controller;
use Think\Controller;
class LocalController extends CommonController {
  
  /**
   * 设置缓存
   */
  public function Set(){
	  $name  = $_POST['name'];
	  $value  = json_decode($_POST['json_data'],true);
	  $expire = $_POST['expire'] ? $_POST['expire'] : C('DATA_CACHE_TIME');
	  if(C('DATA_CACHE_TYPE')=='Memcache'){
		$expire = $expire==-1 ? 0 : $expire;
		$name = $_POST['module'].':'.$_POST['name'];
	  }
	  $expire = $_POST['expire'] ? $_POST['expire'] : 0;
	  $dir = $_POST['dir'] ? $_POST['dir'] : '';
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  //创建目录
	  if(C('DATA_CACHE_TYPE')=='File')mk_dir(C('DATA_CACHE_PATH').$dir);
	  $options['filename'] = $name;
	  $cache = new Cache();
	  $cache =$cache->connect(C('DATA_CACHE_TYPE'),$options);
	  $cache->getInstance();
	  $result = $cache->set($name,$value,$expire);
	  if($_POST['from']=='self'){
	    return;
	  }
	  if($result){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  echo json_encode($msg);exit;
  }

  /**
   * 获取缓存
   */
  public function Get(){
	  $name  = $_POST['name'];
	  $module = $_POST['module'];
	  $dir = $_POST['dir'] ? $_POST['dir'] : '';
	  /*
	  $cache = new \Think\Cache();
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  $options['filename'] = $name;
	  $cache =$cache->connect(C('DATA_CACHE_TYPE'),$options);
	  $cache->getInstance();
	  $data = $cache->get($name);
	  */
	  $options['temp'] = C('DATA_CACHE_PATH').$dir;
	  $options['filename'] = $name;
	  $data = S($name,'',$options);

	  if($data==false){
		//生成缓存
		$class = $module.'Action';
		$action_name = 'Set'.ucwords($name);
		//echo $action_name;exit;
		$_POST['status'] = 1;
		$_POST['from'] = 'self';
		$model = new $class();
		$data = $model->$action_name();
		$_POST['from'] = 'self';
		$_POST['json_data'] = json_encode($data);
		$this->Set();
	  }
	  $result['error_code'] = 0;
	  $result['data'] = $data;
	  echo json_encode($result);exit;
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