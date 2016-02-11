<?php
namespace Home\Controller;
use Think\Controller;
class CommentController extends CommonController {

  /**
   * 查看
   */
  public function look(){
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
    $this->assign('vo',$vo);
	$this->display();
  }

  /**
   * 查询条件
   */
  public function _search(){

	if($_GET['id']!=""){
      $data['id'] = $_GET['id'];
	  $this->assign("id",$_GET['id']);
	}
	if($_GET['member_name']!=""){
      $data['member_name'] = $_GET['member_name'];
	  $this->assign("member_name",$_GET['member_name']);
	}
	if($_GET['message']!=""){
      $data['message'] = array('like','%'.$_GET['message'].'%');
	  $this->assign("message",       $_GET['message']);
	}
	return $data;
  }

}
?>