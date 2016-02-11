<?php
namespace Home\Controller;
use Think\Controller;
class ShippingController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Shipping');
	$this->srdb = D('Shipping_region');
	$this->sdb = D('Region');
	$_REQUEST['_sort'] = 1;
  }

  /**
   * 添加
   */
  function add(){
    if(IS_POST){
	  if(!$_POST['code'])$_POST['code'] = GetPinyin($_POST['name']);
		$name=CONTROLLER_NAME;
		$model = D ($name);
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list = $model->add ();
		if ($list!==false) { //保存成功
		    $this->history($list);
			  //新增物流配置
			  if($_POST['shipping_region']['add']){
				foreach($_POST['shipping_region']['add']['area_id'] as $key=>$val){
				  $sr_add['region_id'] = $val;
				  $sr_add['weightprice'] = $_POST['shipping_region']['add']['weightprice'][$key];
				  $sr_add['shipping_id'] = $list;
				  $sr_add['shipping_name'] = $_POST['name'];
				  $this->srdb->add($sr_add);
				}
			  }
			$this->up_shipping_region($list);//缓存
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}else{
	  $zone_lists  = ZoneController::lists();
	  $this->assign('zone_lists',$zone_lists);
	  $this->display();
	}
  }

  /**
   * 编辑
   */
  function edit(){
    if(IS_POST){
	  if(!$_POST['code'])$_POST['code'] = GetPinyin($_POST['name']);
	  //编辑物流配置
	  $cache_up = 0;
	  if($_POST['shipping_region']['edit']){
	    foreach($_POST['shipping_region']['edit'] as $id=>$val){
		  if($val['up_status']!=0){
			$sr_wdata['id'] =  $id;
			$sr_sdata['fw_price'] = $val['fw_price'];
			$sr_sdata['aw_price'] = $val['aw_price'];
			$sr_sdata['region_id'] = $val['area_id'];
			$this->srdb->where($sr_wdata)->save($sr_sdata);
			$cache_up = 1;
		  }
		}
	  }
	  //新增物流配置
	  if($_POST['shipping_region']['add']){
	    foreach($_POST['shipping_region']['add']['area_id'] as $key=>$val){
		  $sr_add['region_id'] = $val;
		  $sr_add['fw_price'] = $_POST['shipping_region']['add']['fw_price'][$key];
		  $sr_add['aw_price'] = $_POST['shipping_region']['add']['aw_price'][$key];
		  $sr_add['shipping_id'] = $_POST['id'];
		  $sr_add['shipping_name'] = $_POST['name'];
		  $this->srdb->add($sr_add);
		}
		$cache_up = 1;
	  }
	  if($cache_up){
	    $this->up_shipping_region($_POST['id']);
	  }
	  if (false === $this->db->create ()) {
		$this->error ( $this->db->getError () );
	  }
	  $list = $this->db->save ();
	  if (false !== $list) {
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
	  } else {
		  //错误提示
		  $this->error ('编辑失败!');
	  }
	  
	}else{
	  $data['id'] = $_GET['id'];
	  $vo = $this->db->where($data)->find();
	  $this->assign('vo',$vo);
	  $zone_lists  = ZoneController::lists();
	  $this->assign('zone_lists',$zone_lists);
	  $this->get_shipping_region();
	  $this->display();
	}
  }

  /**
   * 缓存
   */
  function up_shipping_region($shipping_id){
    $data['shipping_id'] = $shipping_id;
	$list = $this->srdb->where($data)->order('sort asc,id asc')->select();
	//dump($list);
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
	/*
	$dir = C('DATA_CACHE_PATH').'/shipping_region/'.$shipping_id.'/';
	mk_dir($dir);
	F('list',$list2,$dir);
	*/
	$options['shipping_id'] = $shipping_id;
	$options['dir'] = $shipping_id.'/';
	setCache('Shipping_region:list',$list2,0,$options);
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

  /**
   * 区域配置信息
   */
  public function get_shipping_region(){
    $data['shipping_id'] = $_GET['id'];
    $srlist = $this->srdb->where($data)->select();
	if($srlist){
		foreach($srlist as $val){
		  if(!$region_conf){
			$region_conf = explode(',',$val['region_id']);
		  }else{
			$region_conf = array_merge($region_conf,explode(',',$val['region_id']));
		  }
		  $region_tid[$val['id']] =  explode(',',$val['region_id']);
		}
		//dump($region_tid);exit;
		$this->assign('region_conf',json_encode(array_unique($region_conf)));
		$this->assign('region_tid',json_encode($region_tid));
		$this->assign('srlist',$srlist);
	}
  }

  /**
   * 导入数据 已废弃
   */
  function import(){
	exit;
	$names = array('顺丰','圆通','中通','申通','汇通','韵达','天天','宅急送','EMS');
	$model = M(CONTROLLER_NAME);
	foreach($names as $name){
	  $data['name'] = $name;
	  $data['code'] = GetPinyin($name);
	  $model->add($data);
	}
	echo 'ok';
  }

  /**
   * 默认设置
   */
  public function is_select(){
	$model = D (CONTROLLER_NAME);
	$data['id'] = array('neq',$_POST['id']);
    $sdata['default'] = 0;
	$model->where($data)->save($sdata);
	$data['id'] = $_POST['id'];
    $sdata['default'] = 1;
	$result = $model->where($data)->save($sdata);
    if($result){
	  $msg['error_code'] = 0;
	  $msg['notice'] = '设置成功';
	}else{
	  $msg['error_code'] = 8002;
	  $msg['notice'] = '设置失败';	
	}
	echo json_encode($msg);exit;
  }

  /**
   * 获取物流信息
   */
  public function get_shipping($id){
    $model = M('Shipping');
	$data['id'] = $id;
	$vo = $model->where($data)->find();
	return $vo;
  }

  /**
   * 快递公司
   */
  public function lists(){
    $model = M('Shipping');
	$data['status'] = 1;
	$list = $model->where($data)->select();
	return $list;
  }

  /**
   * 删除物流配置信息
   */
  public function ajax_del_sr(){
	$data['id'] = $_POST['id'];
	$vo = $this->srdb->where($data)->find();
	$this->up_shipping_region($vo['shipping_id']);
    $result = $this->srdb->where($data)->delete();
	if($result){
	  $this->history($_POST['id'],'delete','Shipping_region');
	  ajaxSucReturn('删除成功！'); 
	}else{
	  ajaxErrReturn('删除失败！');
	}
  }

  /**
   * 删除物流公司 废弃
   */
  public function foreverdelete(){
	exit;
	//删除指定记录
	$model = D (CONTROLLER_NAME);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
				$ids =  explode ( ',', $id );
				//删除缓存文件
				$sr_data['shipping_id'] = array ('in', explode ( ',', $id ) );
				$this->srdb->where($sr_data)->delete();
				foreach($ids as $id){
				  $dir = C('DATA_CACHE_PATH').'/shipping_region/'.$id.'/list.php';
				  unlink($dir);
				  $this->history($id);
				}
				$this->success ('删除成功！');
			} else {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }

}
?>