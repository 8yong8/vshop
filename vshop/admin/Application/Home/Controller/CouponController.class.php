<?php
namespace Home\Controller;
use Think\Controller;
class CouponController extends CommonController {

  public function _before_add(){
    if(IS_POST){
	  $_POST['btime'] = strtotime($_POST['btime']);
	  $_POST['etime'] = strtotime($_POST['etime']);
	}
  }

  public function _before_edit(){
    if(IS_POST){
	  $_POST['btime'] = strtotime($_POST['btime']);
	  $_POST['etime'] = strtotime($_POST['etime']);
	}
  }

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['name']!=""){
      $data['name'] = $_GET['name'];
	  $this->assign("name",$_GET['name']);
	}
	return $data;
  }

  /**
   * 优惠券
   */
  public function lists(){
    $model = M(MODULE_NAME);
	$data['status'] = 1;
	$data['etime'] = array('lt',time());
	$list = $model->where($data)->select(); echo json_encode($list);
	if($_POST['ajax']==1){
	  echo json_encode($list);
	}else{
	  return $list;
	}
  }

}
?>