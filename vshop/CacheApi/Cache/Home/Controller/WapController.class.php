<?php
namespace Home\Controller;
use Think\Controller;
class WapController extends CommonController {


  public function _initialize() {
	parent::_initialize();
	$this->db = D('Position_data');
  }

  /**
   * WAP首页缓存
   */
  function index(){
	  $data['area_type'] = 1;
	  $data['status'] = 1;
	  if($_POST['json_data']){
		$pvs = json_decode($_POST['json_data']);
	  }else{
		$pvs = $this->db->where($data)->order('sort asc,id asc')->select();
	  }
	  if($_POST['from']=='self'){
	    return $pvs;exit;
	  }
	  if($_POST['status'])$msg['data'] = $pvs;
	  $return = $this->SetCache('pvs',$pvs);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  echo json_encode($msg);exit;
  }


}
