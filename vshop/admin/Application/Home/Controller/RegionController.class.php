<?php
namespace Home\Controller;
use Think\Controller;
class RegionController extends CommonController {

  public $up_fields = array('sort','name'); //可修改字段

  public function _initialize() {
	parent::_initialize();
	$this->db = D(CONTROLLER_NAME);
  }

  /**
   * 列表
   */
  public function index() {
	$data['pid'] = 1;
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$this->assign('list',$list);
	$this->display();
  }

  /**
   * 添加信息
   */
  public function add(){
    if(IS_POST){
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if($_POST['pid'])$this->assign('jumpUrl',__CONTROLLER__.'/add/id/'.$_POST['pid']);
		if (false === $this->db->create ()) {
		  $this->assign('error',$this->db->getError ());
		  $this->display('Public:error2');	
		}
	  //保存当前数据对象
	  $result = $this->db->add ();
	  //echo $this->db->getlastsql();exit;
	  if($result){
		  $message = '添加成功';
		  $this->assign('message',$message);
		  $this->display('Public:success2');	  
	  }else{
		  $this->assign('error','添加失败');
		  $this->display('Public:error2');	  
	  }
	}else{
	  if($_GET['id']){
	    $data['id'] = $_GET['id'];
		$vo = $this->db->where($data)->find();
		$this->assign('vo',$vo);
	  }
	  $this->display();
	}
  }

  /**
   * 编辑信息
   */
  public function edit(){
    if(IS_POST){
	  $wdata['id'] = $_POST['id'];
	  $sdata['area_name'] = $_POST['area_name'];
	  $sdata['sort'] = $_POST['sort'];
	  $result = $this->db->where($wdata)->save($sdata);
	  if($result){
		  $message = '编辑成功<script>setTimeout("art.dialog.close()", 1000 );var obj = $(artDialog.open.origin.document);obj.find("#row_'.$_POST['id'].'").find(".tree-title").html("'.$_POST['area_name'].'");obj.find("#row_'.$_POST['id'].'").find("td").eq(1).html("'.$_POST['sort'].'");</script>';
		  $this->assign('message',$message);
		  $this->display('Public:success2');	  
	  }else{
		  $this->assign('error','编辑失败');
		  $this->display('Public:error2');	  
	  }
	}else{
	  $wdata['id'] = $_GET['id'];
	  $vo = $this->db->where($wdata)->find();
	  $this->assign('vo',$vo);
	  $this->display();
	}
  }

  /**
   * 市列表
   */
  public function city_child(){
	$data['pid'] = $_POST['pid'];
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$this->assign('list',$list);
	$this->assign('pid',$_POST['pid']);
	$this->display();
  }

  /**
   * 地区列表
   */
  public function county_child(){
	$p_data['id'] = $_POST['pid'];
	$parent = $this->db->where($p_data)->find();
	$this->assign('parent',$parent);
	$data['pid'] = $_POST['pid'];
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$this->assign('list',$list);
	$this->assign('pid',$_POST['pid']);
	$this->display();
  }	
  
  /**
   * 判断是否有子节点
   */
  function has_child($id){
	$rows = $this->db->where(array('pid'=>$id))->count();
	return $rows > 0 ? true : false;
  }

  /**
  * 删除地域
  */
  public function ajax_del(){
	$id=intval($_POST['id']);
	if($id>0){
		if(!$this->has_child($id)){
			$this->db->where('id='.$id)->delete();
			//$this->db->build_cache();
			$msg['callback'] = "remove('row_".$id."');";
			$msg['notice'] = '恭喜你，删除区域成功！';
			ajaxSucReturn($msg); 
		}else{
			ajaxErrReturn('请先删除子地区！'); 
		}
		
	}else{
	   ajaxErrReturn('非法操作，请联系管理员！'); 
	}
  }

	function GiveCache(){
	  $js = 'var provinces = new Array();
var cities = new Array();
var counties = new Array();
';
	  $model = M('Region');
	  $data['area_type'] = 1;
	  $pvs = $model->where($data)->order('sort asc,id asc')->select();
	  foreach($pvs as $key=>$pv){
	    $js .= 'provinces['.$key.'] = new Array("'.$pv['id'].'", "'.$pv['area_name'].'");
';
		$pv_id = $pv['id'];
		$provinces[$pv_id] = $pv;
	  }
	  $data['area_type'] = 2;
	  $cities = $model->where($data)->order('sort asc,id asc')->select();
	  foreach($cities as $key=>$ct){
	    $js .= 'cities['.$key.'] = new Array("'.$ct['pid'].'", "'.$ct['id'].'", "'.$ct['area_name'].'");
';
		$ct_id = $ct['id'];
		$citys[$ct_id] = $ct;
	  }
	  $data['area_type'] = 3;
	  $counties = $model->where($data)->order('sort asc,id asc')->select();
	  foreach($counties as $key=>$county){
	    $js .= 'counties['.$key.'] = new Array("'.$county['pid'].'", "'.$county['id'].'", "'.$county['area_name'].'");
';
		$county_id = $county['id'];
		$countys[$county_id] = $county;
	  }
	  $js .= 'provincecount = '.count($pvs).';
citycount = '.count($cities).';
countycount = '.count($counties).';
';
	  $js_mb = file_get_contents('./js_mb');
	  $js .= $js_mb;
	  file_put_contents('./Public/js/region.js',$js);
	  setCache('pvs',$provinces);
	  setCache('cities',$citys);
	  setCache('counties',$countys);
	  $this->success('缓存完毕！');
	}

  /**
   * 区域省市等信息信息
   */
  public function get_area_list($level = 0, $area_id = 0) {
		$sqlmap = array();
		if ($level > 0) {
			$sqlmap['pid'] = $area_id;
		} else {
			$provinces = M('Zone')->getFieldById($area_id, 'pv_ids');
			$provinces = explode(',', $provinces);
			$sqlmap['id'] = array("IN", $provinces);
		}
		$result = M('Region')->where($sqlmap)->order("sort ASC")->select();
		echo json_encode($result);
  }


}
