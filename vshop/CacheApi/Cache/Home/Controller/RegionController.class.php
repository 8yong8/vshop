<?php
namespace Home\Controller;
use Think\Controller;
class RegionController extends CommonController {

  public $up_fields = array('orderindex','name'); //可修改字段

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Region');
  }

  /**
   * 省缓存
   */
  function SetPvs(){
	  $data['area_type'] = 1;
	  $data['status'] = 1;
	  if($_POST['json_data']){
		$pvs = json_decode($_POST['json_data']);
	  }else{
		$list = $this->db->where($data)->order('sort asc,id asc')->select();
		foreach($list as $val){
		  $key = $val['id'];
		  $pvs[$key] = $val;
		}
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

  /**
   * 市缓存
   */
  function SetCities(){
	  $data['area_type'] = 2;
	  $data['status'] = 1;
	  if($_POST['json_data']){
		$cities = json_decode($_POST['json_data']);
	  }else{
		$list = $this->db->where($data)->order('sort asc,id asc')->select();
		foreach($list as $val){
		  $key = $val['id'];
		  $cities[$key] = $val;
		}
	  }
	  if($_POST['from']=='self'){
	    return $cities;exit;
	  }
	  if($_POST['status'])$msg['data'] = $cities;
	  $return = $this->SetCache('cities',$cities);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  echo json_encode($msg);exit;
  }

  /**
   * 区缓存
   */
  function SetCounties(){
	  $data['area_type'] = 3;
	  $data['status'] = 1;
	  if($_POST['json_data']){
		//file_put_contents('../upate.txt',time());
		$counties = json_decode($_POST['json_data']);
	  }else{
		$counties = $this->db->where($data)->order('sort asc,id asc')->select();
		foreach($list as $val){
		  $key = $val['id'];
		  $counties[$key] = $val;
		}
	  }
	  if($_POST['from']=='self'){
	    return $counties;exit;
	  }
	  if($_POST['status'])$msg['data'] = $counties;
	  $return = $this->SetCache('counties',$counties);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  echo json_encode($msg);exit;
  }

}
