<?php 
namespace Home\Controller;
use Think\Controller;
class ShaidanController extends CommonController {

	function _before_index(){
	  $_REQUEST ['listRows'] = 50;
	
	}


  /**
   * 禁用
   */
  public function forbid() {
	$name=CONTROLLER_NAME;
	$model = D ($name);
    $data['id'] = array('in',$_GET['id']);
	$sdata['status'] = -1;
	$list = $model->where($data)->save($sdata);
	if (false !== $list) {
		//成功提示
		$this->success ('编辑成功!');
	} else {
		//错误提示
		$this->error ('编辑失败!');
	}
  }

  /**
   * 通过
   */
  function resume() {
	//恢复指定记录
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$re_model = M('red_envelope');
	$mem_model = M('Member');
	$mem_data['id'] = $val['member_id'];
	$member = $mem_model->field('id,pid')->where($mem_data)->find();

	$data['id'] = array('in',$_GET['id']);
	$data['status'] = 0;
	$list = $model->where($data)->select();
	foreach($list as $key=>$val){
	  $model->startTrans();//启用事务
	  $w_data['id'] = $val['id'];
	  $s_data['status'] = 1;
	  $result = $model->where($w_data)->save($s_data);
	  //dump($result);
	  if(!$result){
		$model->rollback();
	    continue;
	  }
	  //添加一元红包
	  $add_data['title'] = '晒单';
	  $add_data['pay'] = 1;
	  $add_data['member_id'] = $val['member_id'];
	  $add_data['member_name'] = $val['member_name'];
	  $add_data['source'] = $name;
	  $add_data['sourceid'] = $val['id'];
	  $add_data['create_time'] = time();
	  $id = $re_model->add($add_data);
	  //dump($id);
	  //查看上级
	  $mem_data['id'] = $val['member_id'];
	  $member = $mem_model->field('id,pid')->where($mem_data)->find();
	  if($member['pid']){
	    $mem_data['id'] = $member['pid'];
		$member = $mem_model->field('id,pid,username as member_name')->where($mem_data)->find();
		//dump($member);exit;
	    $add_data['member_id'] = $member['id'];
	    $add_data['member_name'] = $member['member_name'];
		$add_data['from_id'] = $val['member_id'];
		$add_data['from_name'] = $val['member_name'];
		$add_data['pay'] = 0.05;
		$id = $re_model->add($add_data);
		  if($member['pid']){
			$mem_data['id'] = $member['pid'];
			$member = $mem_model->field('id,pid,username as member_name')->where($mem_data)->find();
			//dump($member);exit;
			$add_data['member_id'] = $member['id'];
			$add_data['member_name'] = $member['member_name'];
			$add_data['from_id'] = $val['member_id'];
			$add_data['from_name'] = $val['member_name'];
			$add_data['pay'] = 0.03;
			$id = $re_model->add($add_data);
			//echo $re_model->getlastsql();exit;
		  }
	  }
	  //dump($id);
	  if(!$id){
		$model->rollback();
	    continue;
	  }else{
	    $model->commit();
	  }
	}
	$this->success ( '审核完成' );
  }

  /**
   * 编辑信息
   */
  function edit() {
	  
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	if($_POST){
		//echo 111;exit;
	  $data['id'] = $_POST['id'];
	  $vo = $model->where($data)->find();
	  if($_POST['status']==1){
	    $this->resume();
	  }else if ($_POST['status']==0){
	    $this->forbid();
	  }
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $list=$model->save ();
	  if (false !== $list) {
		//成功提示
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	}else{
	  $name=CONTROLLER_NAME;
	  $model = M ( $name );
	  $id = $_REQUEST [$model->getPk ()];
	  $vo = $model->getById ( $id );
	  $this->assign ( 'vo', $vo );
	  $this->display();	
	}

  }

  /**
   * 删除信息
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
	//$this->GiveCache();
	//Flag_module::GiveCache();
  }

  /**
   * 查看信息
   */
  function look(){
    $model = M('shaidan');
    $data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	$this->assign('vo',$vo);
	$this->display();
  }




}
?>