<?php
namespace Home\Controller;
use Think\Controller;
class ZoneController extends CommonController {
  
  public function _initialize() {
	parent::_initialize();
	$this->db = D('Zone');
	$this->rdb = D('Region');
	$_REQUEST['_sort'] = 1;
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
   * 添加
   */
  public function add() {
	if (IS_POST) {
		if (is_array($_POST['provinces'])) {
			$_POST['pv_ids'] = implode(',',$_POST['provinces']);
		}
		$list = $this->rdb->where(array('id'=>array('in',$_POST['provinces'])))->select();
		foreach($list as $pv){
		  $pvs[] = $pv['area_name'];
		}
		$data['name'] = $_POST['name'];
		$data['pv_ids'] = $_POST['pv_ids'];
		$data['pv_names'] = implode('，',$pvs);
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
		$region_lists = $this->rdb->where(array('pid' => '1'))->order('sort asc')->select();
		$this->assign('region_lists',$region_lists);
		$this->display();
	}
  }

  /**
   * 编辑
   */
  public function edit() {
	$id = (int) $_GET['id'];
	if (IS_POST) {
		if (is_array($_POST['provinces'])) {
			$_POST['pv_ids'] = implode(',',$_POST['provinces']);
		}
		$list = $this->rdb->where(array('id'=>array('in',$_POST['provinces'])))->select();
		foreach($list as $pv){
		  $pvs[] = $pv['area_name'];
		}
		$wdata['id'] = $_POST['id'];
		$sdata['name'] = $_POST['name'];
		$sdata['pv_ids'] = $_POST['pv_ids'];
		$sdata['pv_names'] = implode('，',$pvs);
		$result = $this->db->where($wdata)->save($sdata);
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
		$regionids = $this->db->where(array('id' => array("NEQ", $id),'status'=>1))->getField('pv_ids', TRUE);
		$regionids = implode(',', $regionids);
		$regionids = explode(',', $regionids);
		$this->assign('regionids',$regionids);
		//此区域信息
		$vo = $this->db->getById($id);
		$this->assign('vo',$vo);
		$provinces = explode(',', $vo['pv_ids']);
		$this->assign('provinces',$provinces);
		//所有省
		$region_lists = $this->rdb->where(array('pid' => '1'))->order('sort asc,id asc')->select();
		$this->assign('region_lists',$region_lists);
		$this->display();
	}
  }

  /**
   * 排序页面
   */
  public function sort(){
	$count = $this->db->where($data)->count();
	//创建分页对象
	$listRows = '20';
	$p = new \My\Page($count,$listRows);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno-1)*$page_size;
	$p = new Page($count,$listRows);
	$list = $this->db->field('*')->where($data)->order('sort asc,id desc')->limit($p->firstRow.','.$p->listRows)->select();
	$page = $p->Show();
	$this->assign('page',$page);
	$this->assign('list',$list);
    $this->display();
  }

  /**
   * 保存排序
   */
  public function saveSort(){
	$fid = $_POST['flagid'];
	$dostr = $_POST['dostr'];
	$list = explode('#',$dostr);
	$y = 1;
	foreach($list as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }else{
	     $ar = explode(':',$val);
		 if($ar[1]==0){
		   $list2[$y] = $val;
		   $y++;
		 }else{
		   $list1[$y] = $val;
		   $y++;
		 }
	  }
	}
	if($list1 && $list2){
	  $list1 = array_merge($list1,$list2);
	}elseif($list2){
	  $list1 = $list2;
	}
	foreach($list1 as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }
	  $wdata['id'] = $ar[0];
	  $data['sort'] = $ar[1];
	  $result = $this->db->where($wdata)->save($data);
	}
	$this->success ('排序完成!');
  }

  /**
   * 区域列表
   */
  public function lists(){
    $model = M('Zone');
	$data['status'] = 1;
	$list = $model->where($data)->select();
	return $list;
  }

  /**
   * 生成缓存
   */
  function GiveCache(){
	  $model = D('Zone');
	  $wdata['status'] = 1;
	  $list = $model->where($wdata)->select();
	  foreach($list as $array){
	    $data[$array['id']] = $array;
	  }
	  /*
	  mk_dir(C('DATA_CACHE_PATH').'Zone/');
	  F('list',$data,C('DATA_CACHE_PATH').'Zone/');
	  */
	  //$options['dir'] = 'Zone/';
	  setCache('Zone:list',$data);
	  $this->assign('jumpUrl',__CONTROLLER__);
	  $this->success ('操作成功!');
  }

}
?>