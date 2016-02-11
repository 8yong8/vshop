<?php 
namespace Home\Controller;
use Think\Controller;
class ClassifyController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Classify');
  }

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['name']!=""){
      $data['name'] = array('like','%'.$_GET['name'].'%');
	  $this->assign("name",       $_GET['name']);
	}
	if($_GET['pid']!=""){
      $data['pid'] = $_GET['pid'];
	  $this->assign("pid",$_GET['pid']);
	}else{
	  $data['pid'] = 0;
	}
	return $data;
  }

  /**
   * 添加信息 前置
   */
  public function _before_add(){
    if(!$_POST){
	  $model = D('classify');
	  $data['status'] = 1;
	  $list = $model->where($data)->order('sort asc,id desc')->select();
	  $tree = new \My\Tree($list);
	  $list = $tree->get_tree('0');
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
	    $name=CONTROLLER_NAME;
	    $model = D ($name);
		$pdata['id'] = $_POST['pid'];
		$vo = $model->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	}
  }

  /**
   * 编辑信息 前置
   */
  public function _before_edit(){
	if(!$_POST){
	  $model = D('Classify');
	  $data['status'] = 1;
	  $list = $model->where($data)->order('sort asc,id desc')->select();
	  $tree = new \My\Tree($list);
	  $list = $tree->get_tree('0');
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
	    $name=CONTROLLER_NAME;
	    $model = D ($name);
		$pdata['id'] = $_POST['pid'];
		$vo = $model->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	}
  }

  /**
   * 获取列表信息
   */
  public function lists(){
	$model = M('Classify');
	$data = array();
    if($_POST['pid']){
	  $data['pid'] = $_POST['pid'];
	}
    $list = $model->where($data)->select();
	$tree = new \My\Tree($list);
	$list = $tree->get_tree('0');
	if(IS_AJAX){
	  echo json_encode($list);
	}else{
	  return $list;
	}
  }

}
?>