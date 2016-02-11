<?php 
namespace Home\Controller;
use Think\Controller;
class ProductCategoryController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('ProductCategory');
  }

  /**
   * 查询条件
   */
  public function _search(){
    $map = array();
    if($_GET['name']){
	  $map['name'] = array('like','%'.$_GET['name'].'%');
	}
    if($_GET['pid']){
	  $map['pid'] = $_GET['pid'];
	}
    if($_GET['channel']){
	  $map['_string'] = "find_in_set('".$_GET['channel']."',channel)";
	}
	return $map;
  }

  /**
   * 添加信息 前置
   */
  public function _before_add(){
    if(!$_POST){
	  $data['status'] = 1;
	  $list = $this->db->where($data)->order('sort asc,id asc')->select();
	  $tree = new \My\Tree($list);
	  $list = $tree->get_tree('0');
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
		$pdata['id'] = $_POST['pid'];
		$vo = $this->db->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	  $_SESSION['pid'] = $_POST['pid'] ? $_POST['pid'] : 0;
	  $_POST['channel'] = implode(',',$_POST['channel']);
	}
  }

  /**
   * 编辑信息 前置
   */
  public function _before_edit(){
	if(!$_POST){
	  import('@.ORG.Util.Tree');
	  $data['status'] = 1;
	  $list = $this->db->where($data)->order('sort asc,id desc')->select();
	  $tree = new Tree($list);
	  $list = $tree->get_tree('0');
	  //dump($list);exit;
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
		$pdata['id'] = $_POST['pid'];
		$vo = $this->db->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	  $_POST['channel'] = implode(',',$_POST['channel']);
	}
  }

  /**
   * 排序
   */
  public function sort(){
	$b_data['pid'] = 0;
	$blist = $this->db->where($b_data)->order('sort asc,id asc')->select();
	$this->assign('blist',$blist);
	if($_GET['pid']){
	  $data['pid'] = $_GET['pid'];
	  $count = $this->db->where($data)->count();
	  //创建分页对象
	  $listRows = '20';
	  $p = new \My\Page($count,$listRows);
	  $pageno = $_GET['p'] ? $_GET['p'] : 1;
	  $offset = ($pageno-1)*$page_size;
	  $p = new Page($count,$listRows);
	  $list = $this->db->where($data)->order('sort asc,id asc')->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $model->getlastsql();
	  $page = $p->Show();
	  $this->assign('page',$page);
	  $this->assign('list',$list);	
	}
    $this->display();
  }

  /**
   * 保存排序
   */
  public function saveSort(){
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
	$this->GiveCache();
	$this->success ('排序完成!');
  }
  
  /**
   * 生成缓存
   */
  public function GiveCache(){
	  /*
	  $model = D('Product_category');
	  $wdata['status'] = 1;
	  $wdata['_string'] = "find_in_set('商城',channel)";
	  $list = $model->where($wdata)->order('lv asc,sort asc')->select();
	  setCache('sc_list',$list);
	  $wdata['pid'] = 0;
	  $top_cat = $model->where($wdata)->order('sort asc')->select();
	  setCache('sc_list_top',$top_cat);
	  $cdata['status'] = 1;
	  foreach($list as $val){
		$cdata['pid'] = $val['id'];
		$child = $model->where($cdata)->order('sort asc')->select();
		if($child){
		  setCache('sc_list_'.$val['id'],$child);
		}
		setCache('sc_detail_'.$val['id'],$child);
	  }
	  */
	  setCache('sc_list'.$val['id']);
	  if($_GET['status']==1){
	    $this->success ('缓存完成!');
	  }
	  
  }

  /**
   * 获取列表信息
   */
  public function lists(){
	//$model = M('ProductCategory');
	$data = array();
    if($_POST['pid']){
	  $data['pid'] = $_POST['pid'];
	}
    $list = $this->db->where($data)->select();
	import('@.ORG.Util.Tree');
	$tree = new Tree($list);
	$list = $tree->get_tree('0');
	if(IS_AJAX){
	  echo json_encode($list);
	}else{
	  return $list;
	}
  }

}
?>