<?php
namespace Home\Controller;
use Think\Controller;
class ShippingRegionController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Shipping');
	$this->srdb = D('ShippingRegion');
	$this->sdb = D('Region');
  }

  /**
   * 快递缓存 按快递公司分
   */
  function SetList(){
    $data['shipping_id'] = $_POST['shipping_id'];
	$list = $this->srdb->where($data)->order('sort asc,id asc')->select();
	foreach($list as $val){
	  $region_ids2 = array();
	  $region_ids = explode(',',$val['region_id']);
	  foreach($region_ids as $region_id){
	     $rids = $this->reset_region($region_id);
		 $region_ids2 = array_merge($region_ids2,$rids);
	  }
	  foreach($region_ids2 as $region_id){
	    $list2[$region_id]['fw_price'] = $val['fw_price'];
		$list2[$region_id]['aw_price'] = $val['aw_price'];
	  }
	}
	if($_POST['from']=='self'){
	    return $list2;exit;
	}
	$return = $this->SetCache('list',$list2);
	if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	}
	echo json_encode($msg);exit;
  }

  /**
   * 重新组装
   */
  protected function reset_region($region_id){
	 //是否是省
	 $data['id'] = $region_id;
	 $data['area_type'] = 1;
	 $vo = $this->sdb->where($data)->find();
	 if($vo){
	   $data2['pid'] = $vo['id'];
	   $list = $this->sdb->where($data2)->select();
	   foreach($list as $val){
	     $region_ids[] = $val['id'];
	   }
	 }else{
	   $region_ids[] = $region_id;
	 }
	 return $region_ids;
  }

}
?>