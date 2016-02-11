<?php
namespace Home\Controller;
use Think\Controller;
class OrderDeliveryController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('OrderDelivery');
	$this->odb = D('Order');
	$this->otdb = D('OrderTrack');
  }

  /**
   * 物流信息
   */
  public function look(){
	$data['id'] = $_GET['id'];
	$vo = $this->db->where($data)->find();
	$this->assign ( 'vo', $vo );
	if(!$vo){
	  $this->error('物流信息不存在');
	}
	$dir = get_dir($vo['id']);
	if(file_exists(C('DATA_CACHE_PATH').'/delivery/'.$dir.'/list.php')){
	    $list = include C('DATA_CACHE_PATH').'/delivery/'.$dir.'/list.php';
	}else{
		include C('PUBLIC_INCLUDE')."kuaidi.class.php";
		$kuaidi = new kuaidi();
		$list = $kuaidi->query($vo['shipping_code'], $vo['shipping_no']);
		//dump($list);exit;
		krsort($list['data']);
		mk_dir(C('DATA_CACHE_PATH').'/delivery/');
		if($list['state']==3){
		  mk_dir(C('DATA_CACHE_PATH').'/delivery/'.$dir.'/');
		  F('list',$list,C('DATA_CACHE_PATH').'/delivery/'.$dir.'/');
		}
	}
	if($list){
	  $list['count'] = count($list['data']);
	}else{
	  $list['message'] = $kuaidi->error;
	}
	$this->assign('list',$list);
	if($_GET['ajax']==1){
	  $this->display('ajax_look');	
	}else{
	  $this->display();
	}
  }

  /**
   * 快递查询
   */
  public function detail(){
	$data['id'] = $_GET['id'];
	$vo = $this->db->where($data)->find();
	$this->assign ( 'vo', $vo );
	if(!$vo){
	  $this->error('物流信息不存在');
	}
	$dir = get_dir($vo['id']);
	if(file_exists(C('DATA_CACHE_PATH').'/delivery/'.$dir.'/list.php')){
	    $list = include C('DATA_CACHE_PATH').'/delivery/'.$dir.'/list.php';
	}else{
		include C('PUBLIC_INCLUDE')."kuaidi.class.php";
		$kuaidi = new kuaidi();
		$list = $kuaidi->query($vo['shipping_code'], $vo['shipping_no']);
		krsort($list['data']);
		mk_dir(C('DATA_CACHE_PATH').'/delivery/');
		if($list['state']==3){
		  mk_dir(C('DATA_CACHE_PATH').'/delivery/'.$dir.'/');
		  F('list',$list,C('DATA_CACHE_PATH').'/delivery/'.$dir.'/');
		}
	}
	if($list){
	  $list['count'] = count($list['data']);
	}else{
	  $list['message'] = $kuaidi->error;
	}
	if(!$list) {
		$this->error($kuaidi->getError());
	} else {
		$result['data'] = $list;
		$result['notice'] = '查询成功';
		ajaxSucReturn($result);
	}
  }

  /**
   * 编辑信息
   */
  public function edit() {
	  if(IS_POST){
		$wl = ShippingController::get_shipping($_POST['shipping_id']);
		//dump($wl);exit;
		$_POST['shipping_code'] = $wl['code'];
		$_POST['shipping_company'] = $wl['name'];
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['update_time'] = time();
		if (false === $model->create ()) {
		  $this->error ( $model->getError () );
		}
		// 更新数据
		$list = $model->save ();
		if (false !== $list) {
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
		} else {
		  //错误提示
		  $this->error ('编辑失败!');
		}
	  }else{
		$shippings = ShippingController::lists();
		$this->assign('shippings',$shippings);
		$data['id'] = $_GET['id'];
		$vo = $this->db->where($data)->find();
		$this->assign ( 'vo', $vo );
		$this->display();
	  }
  }

}
?>