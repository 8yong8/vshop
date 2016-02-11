<?php 
namespace Home\Controller;
use Think\Controller;
class ProductTypeController extends CommonController {

  public function _before_add(){
    if($_POST){
	
	}
  }

  /**
   * 编辑 前置
   */
  public function _before_edit(){
	if($_POST){
	  $model = M('Attribute');
	  $data['cat_id'] = $_POST['id'];
	  $sdata['cat_name'] = $_POST['name'];
	  $model->where($data)->save($sdata);
	}
  }

  /**
   * 排序
   */
  public function sort(){
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	$b_data['pid'] = 0;
	$blist = $model->where($b_data)->order('sort asc,id asc')->select();
	$this->assign('blist',$blist);
	if($_GET['pid']){
	  $data['pid'] = $_GET['pid'];
	  $count = $model->where($data)->count();
	  //创建分页对象
	  $listRows = '20';
	  $pageno = $_GET['p'] ? $_GET['p'] : 1;
	  $offset = ($pageno-1)*$page_size;
	  $p = new \My\Page($count,$listRows);
	  $list = $model->where($data)->order('sort asc,id asc')->limit($p->firstRow.','.$p->listRows)->select();
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
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	foreach($list1 as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }
	  $wdata['id'] = $ar[0];
	  $data['sort'] = $ar[1];
	  $result = $model->where($wdata)->save($data);
	}
	//$this->GiveCache();
	$this->success ('排序完成!');
  }

  /**
   * 默认设置
   */
  public function is_select(){
	$name = CONTROLLER_NAME;
	$model = D ( $name );
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

  public function GiveCache(){
  
  
  }

}
?>