<?php
namespace Home\Controller;
use Think\Controller;
class ZoneController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Zone');
  }

  //生成缓存
  function SetList(){
	  $model = D('Zone');
	  $wdata['status'] = 1;
	  $list = $model->where($wdata)->select();
	  foreach($list as $array){
	    $data[$array['id']] = $array;
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

}
