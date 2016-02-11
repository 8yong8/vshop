<?php 
namespace Home\Controller;
use Think\Controller;
class RedEnvelopeIntegralController extends CommonController {

	function _before_index(){
	  $_REQUEST ['listRows'] = 50;
	
	}


  /**
   * 禁用
   */
  public function forbid() {
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$model1 = D('FlagModule');
	$pk = $model->getPk ();
	$id = $_REQUEST [$pk];
	$condition = array ($pk => array ('in', $id ) );
	$list = $model->forbid ( $condition );
	if ($list!==false) {
		$condition1 = array ('fid' => array ('in', $id ) );
		$model1->forbid ( $condition1 );
		$this->assign ( "jumpUrl", __CONTROLLER__ );
		$this->success ( '状态禁用成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 启用
   */
  function resume() {
	//恢复指定记录
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$model1 = D('FlagModule');
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->resume ( $condition )) {
		$condition1 = array ('fid' => array ('in', $id ) );
		$model1->resume ( $condition1 );
		$this->assign ( "jumpUrl", __CONTROLLER__ );
		$this->success ( '状态恢复成功！' );
	} else {
		$this->error ( '状态恢复失败！' );
	}
  }

  /**
   * 添加信息 前置
   */
  function _before_add(){
    if($_POST){
	  if(!$_POST['member_id']){
	    $this->error('会员必须');
	  }
	  $model = M('member');
	  $data['id'] = $_POST['member_id'];
	  $vo = $model->field('id,username')->where($data)->find();
	  $_POST['member_name'] = $vo['username'];
	}
  }

  /**
   * 编辑信息 前置
   */
  function _before_edit(){
    if($_POST){
	  if(!$_POST['member_id']){
	    $this->error('出错');
	  }
	  $model = M('member');
	  $data['id'] = $_POST['member_id'];
	  $vo = $model->field('id,username')->where($data)->find();
	  $_POST['member_name'] = $vo['username'];
	}
  }

  /**
   * 删除
   */
  public function delete() {
	//删除指定记录
	$name = CONTROLLER_NAME;
	$model = M ($name);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
			  $condition1 = array ('fid' => array ('in', explode ( ',', $id ) ) );
			  $model1 = D('flag_module');
			  $model1->where($condition1)->delete();
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