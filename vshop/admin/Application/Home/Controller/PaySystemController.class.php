<?php 
namespace Home\Controller;
use Think\Controller;
class PaySystemController extends CommonController {
  
  public function _before_index(){
	 
  }

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['pid']!=""){
      $data['product_id'] = $_GET['pid'];
	  $this->assign("pid",$_GET['pid']);
	}
	if($_GET['title']!=""){
      $data['title'] = array('like','%'.$_GET['title'].'%');
	  $this->assign("title",       $_GET['title']);
	}
	return $data;
  }

  /**
   * 添加信息 前置
   */
 function _before_add(){
   if($_POST){
	 if(!$_POST['product_id']){
	   $this->error('产品必须选择!');
	 }
	 $array = explode('-',$_POST['time']);
	 $_POST['y'] = $array[0];
	 $_POST['m'] = $array[1];
	 $_POST['d'] = $array[2];
   }else{
     if($_GET['pid']){
	   $model = M('Product');
	   $data['id'] = $_GET['pid'];
	   $vo = $model->field('id,name,member_name,realname')->where($data)->find();
	   $this->assign('vo',$vo);
	 }
   }
 }

  /**
   * 编辑信息 前置
   */
  function _before_edit(){
	if($_POST){
	  if(!$_POST['product_id']){
	    $this->error('产品必须选择!');
	  }
	  $array = explode('-',$_POST['time']);
	  $_POST['y'] = $array[0];
	  $_POST['m'] = $array[1];
	  $_POST['d'] = $array[2];
	}
  }


}
?>