<?php 
namespace Home\Controller;
use Think\Controller;
class ConfigController extends CommonController{

  /**
   * 项目配置缓存
   */
  function SetList(){
	  $model = D('Config');
	  $wdata['status'] = 1;
	  $list = $model->where($wdata)->select();
	  foreach($list as $array){
	    $data[$array['key']] = $array['val'];
		$data[$array['key']] = $array['val'];
	  }
	  if($_POST['from']=='self'){
	    return $data;exit;
	  }
	  $return = $this->SetCache('list',$data);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  if($_POST['status'])$msg['data'] = $data;
	  echo json_encode($msg);exit;
  }

  /**
   * 支付信息缓存
   */
  function SetPay(){
	$model = M ('Payment_config');
	$wdata['status'] = 1;
	$list = $model->where($wdata)->order('id desc')->select();
	foreach($list as $val){
	  $key = $val['pay_class'];
	  $data[$key] = $val;
	}
	  if($_POST['from']=='self'){
	    return $data;exit;
	  }
	  $return = $this->SetCache('pay',$data);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  if($_POST['status'])$msg['data'] = $data;
	  echo json_encode($msg);exit;
  }

} 
?>