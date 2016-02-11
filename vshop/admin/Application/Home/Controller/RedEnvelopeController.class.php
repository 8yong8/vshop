<?php 
namespace Home\Controller;
use Think\Controller;
class RedEnvelopeController extends CommonController {

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
		$model->getlastsql();exit;
		$this->error ( '状态恢复失败！' );
	}
  }

  /**
   * 编辑信息
   */
  function edit() {
	  $name = CONTROLLER_NAME;
	  $model = D ( $name );
	if($_POST){
	  $model1 = D('FlagModule');
	  $w_data['fid'] = $_POST['id'];
	  $_POST['update_time'] = time();
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $list=$model->save ();
	  if (false !== $list) {
		//成功提示
		if($_POST['status']==0){
		  //删除
		  $model1->where($w_data)->delete();
		}else{
		  $s_data['fname'] = $_POST['name'];
		  $model1->where($w_data)->save($s_data);
		}
		//$this->history($_POST['id']);
		//$this->GiveCache();
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	}else{
	  $name = CONTROLLER_NAME;
	  $model = M ( $name );
	  $id = $_REQUEST [$model->getPk ()];
	  $vo = $model->getById ( $id );
	  $this->assign ( 'vo', $vo );
	  $this->display();	
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