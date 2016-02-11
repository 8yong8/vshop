<?php
namespace Home\Controller;
use Think\Controller;
class ShippingRegionController extends CommonController {
  
  public function _initialize() {
	parent::_initialize();
	$this->db = D('ShippingRegion');
	$this->region_db = D('Region');
	//$_REQUEST['_sort'] = 1;
  }

  function _before_add(){
    if(IS_POST){
	  
	}
  }

  function _before_edit(){
    if(IS_POST){
	  
	}
  }

  /**
   * 添加信息
   */
  public function add() {
	if (IS_POST) {
		if (is_array($_POST['provinces'])) {
			$_POST['provinces'] = implode(',',$_POST['provinces']);
		}
		$data['name'] = $_POST['name'];
		$data['provinces'] = $_POST['provinces'];
		$data['create_time'] = time();
		$result = $this->db->add($data);
		$this->assign('jumpUrl',__CONTROLLER__.'/add');
		if (!$result) {
		  $this->assign('error','区域新增失败');
		  $this->display('Public:error2');	 
		} else {
		  $this->assign('message','区域新增成功');
		  $this->display('Public:success2');
		}
	} else {
		//非此区域省
		$regionids = $this->db->where(array('status' => 1))->getField('provinces', TRUE);
		$regionids = implode(',', $regionids);
		$regionids = explode(',', $regionids);
		$this->assign('regionids',$regionids);
		//此区域信息
		$vo = $this->db->getById($id);
		$this->assign('vo',$vo);
		$provinces = explode(',', $vo['provinces']);
		$this->assign('provinces',$provinces);
		//所有省
		$region_lists = $this->region_db->where(array('pid' => '1'))->order('sort asc')->select();
		$this->assign('region_lists',$region_lists);
		$this->display();
	}
  }

  /**
   * 编辑信息
   */
  public function edit() {
	$id = (int) $_GET['id'];
	if (IS_POST) {
		if (is_array($_POST['provinces'])) {
			$_POST['provinces'] = implode(',',$_POST['provinces']);
		}
		$wdata['id'] = $_POST['id'];
		$sdata['name'] = $_POST['name'];
		$sdata['provinces'] = $_POST['provinces'];
		$result = $this->db->add();
		$this->assign('jumpUrl',__CONTROLLER__.'/edit/id/'.$_POST['id']);
		if (!$result) {
		  $this->assign('error','区域编辑失败');
		  $this->display('Public:error2');	 
		} else {
		  $this->assign('message','区域编辑成功');
		  $this->display('Public:success2');
		}
	} else {
		//非此区域省
		$regionids = $this->db->where(array('id' => array("NEQ", $id),'status'=>1))->getField('provinces', TRUE);
		$regionids = implode(',', $regionids);
		$regionids = explode(',', $regionids);
		$this->assign('regionids',$regionids);
		//此区域信息
		$vo = $this->db->getById($id);
		$this->assign('vo',$vo);
		$provinces = explode(',', $vo['provinces']);
		$this->assign('provinces',$provinces);
		//所有省
		$region_lists = $this->region_db->where(array('pid' => '1'))->order('sort asc')->select();
		$this->assign('region_lists',$region_lists);
		$this->display();
	}
  }

  /**
   * 快递公司
   */
  public function lists(){
    $model = M('ShippingRegion');
	$data['status'] = 1;
	$list = $model->where($data)->select();
	return $list;
  }

}
?>