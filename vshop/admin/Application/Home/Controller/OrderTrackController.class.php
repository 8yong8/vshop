<?php
namespace Home\Controller;
use Think\Controller;
class OrderTrackController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('OrderTrack');
  }

  /**
   * 日志信息
   */
  public function lists(){
	if(!$_GET['order_sn']){
	  $this->error('订单号必须');
	}
	$data['order_sn'] = $_GET['order_sn'];
    $list = $this->db->where($data)->select();
    $this->assign('list',$list);
	$this->display();
  }

}
?>