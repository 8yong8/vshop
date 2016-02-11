<?php
namespace Home\Controller;
use Think\Controller;
class RecordController extends CommonController {

  /**
   * 搜索条件
   */
  protected function _search($name = '') {
	$map = array ();
	if($_GET['order_id']){
	  $map['order_id'] = $_GET['order_id'];
	  $this->assign('order_id',$_GET['order_id']);
	}
	if($_GET['member_name']){
	  $map['member_name'] = $_GET['member_name'];
	  $this->assign('member_name',$_GET['member_name']);
	}
	if($_GET['pay_type']){
	  $map['pay_type'] = $_GET['pay_type'];
	  $this->assign('pay_type',$_GET['pay_type']);
	}
	if($_GET['name']){
	  $map['name'] = array('like','%'.$_GET['name'].'%');
	  $this->assign('name',$_GET['name']);
	}
	if($_GET['status']){
	  $map['status'] = $_GET['status'];
	  $this->assign('status',$_GET['status']);
	}
	if($_GET['btime'] && $_GET['etime']){
	  //echo date('Y-m-d H:i:s',strtotime($_GET['etime']));exit;
	  $map['create_time'] = array('between',array(strtotime($_GET['btime']),strtotime($_GET['etime'])));
	  $this->assign('btime',$_GET['btime']);
	  $this->assign('etime',$_GET['etime']);
	}
	return $map;
  }
	
  /**
   * 添加记录
   */
  public function add(){
    if($_POST){
	  if(!$_POST['member_id']){
	    $this->error('用户必须!');
	  }
	  $_POST['create_time'] = time();
	  //用户钱包
	  $m_model = M('member');
	  $m_data['a.id'] = $_POST['member_id'];
	  $member = $m_model->table('`'.C('DB_PREFIX').'member` as a')->join('`'.C('DB_PREFIX').'member_wallet` as b on a.id=b.member_id')->field('a.id,a.pid,logo,email,utype,password,username,realname,tel,pv_id,ct_id,province,city,create_time,last_login_time,balance,frozen,b.update_time')->where($m_data)->find();
	  if($member['balance']==null){
	    $member['balance'] = 0;
	  }
	  $_POST['member_name'] = $member['username'];
	  $_POST['realname'] = $member['realname'];
	  $_POST['buyer'] = $member['realname'];
	  if($_POST['pay_type']==1){
		//充值
	    $_POST['balance'] = $member['balance']+$_POST['pay'];
		$_POST['pay_time'] = time();
	  }else if($_POST['pay_type']==2){
		//消费
		if($_GET['payment_mode']==1){
	      if($member['balance']-$_POST['pay']<0){
	        $this->error('余额不足!');exit;
	      }else{
		    $_POST['balance'] = $vo['balance']-$_POST['pay'];
		  }
		}else{
		  //其他途径支付
		  $_POST['balance'] = $member['balance'];
		  $result2 = true;
		}
	    //$_POST['balance'] = $vo['balance']-$_POST['pay'];
	  }else if($_POST['pay_type']==3){
		//提现
	    if($member['balance']-$_POST['pay']<0){
	      $this->error('余额不足!');exit;
	    }else{
		  $_POST['balance'] = $member['balance']-$_POST['pay'];
		}
	  }else if($_POST['pay_type']==5){
		//收入提成
	    $_POST['balance'] = $member['balance']+$_POST['pay'];
	  }
	  $name = CONTROLLER_NAME;
	  $model = D ( $name );
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  $model->startTrans();
	  //添加数据
	  $id = $model->add();
	  if($id){
		if(!$result2){
			//修改用户余额
			$m_model = M('member_wallet');
			$m_wdata['member_id'] = $_POST['member_id'];
			$mcount = $m_model->where($m_wdata)->count();
			if($mcount>0){
			  $m_sdata['balance'] = $_POST['balance'];
			  $result2 = $m_model->where($m_wdata)->save($m_sdata);
			}else{
			  $m_sdata['balance'] = $_POST['balance'];
			  $m_sdata['member_id'] = $_POST['member_id'];
			  $m_sdata['update_time'] = time();
			  $result2 = $m_model->add($m_sdata);
			}
		}
		if($result2){
		  $model->commit();
		}else{
		  $model->rollback();
		  $this->error('失败!');
		}
		$this->history($id);
		$this->success ('新增成功!');
	  }else{
		$model->rollback();
		$this->error ('添加失败!');
	  }
	  exit;	
	}
	$bh = str_pad($_POST['member_id'].time(),15,0,STR_PAD_LEFT);
	$this->assign('bh',$bh);
    $this->display();
  }

  /**
   * 编辑记录
   */
  public function edit(){
	  if($_POST){
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$wdata['id'] = $_POST['id'];
		$vo = $model->field('id,member_id,order_id,pay,pay_type,status')->where($wdata)->find();
		$result = true;
		$result2 = true;
		$result3 = true;
		//提现退回
		if($vo['pay_type']==3 && $_POST['status']==-1){
		  $model->startTrans();//启用事务
		  //余额处理,查找用户钱包
		  $wallet_model = M('member_wallet');
		  $wallet_data['member_id'] = $vo['member_id'];
		  $wallet_vo = $wallet_model->where($wallet_data)->find();
		  $wallet_sdata['frozen'] = $wallet_vo['frozen']-$vo['pay'];
		  $wallet_sdata['balance'] = $wallet_vo['balance']+$vo['pay'];
		  $result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		}

		//回购处理确认
		if(($vo['pay_type']==3 || $vo['pay_type']==4) && $vo['status']==0 && $_POST['status']==1){
		  $model->startTrans();//启用事务
		  //余额处理,查找用户钱包
		  $wallet_model = M('member_wallet');
		  $wallet_data['member_id'] = $vo['member_id'];
		  $wallet_vo = $wallet_model->where($wallet_data)->find();
		  $sdata['pay_time'] = time();
		  if(!$wallet_vo){
			$wallet_add['member_id'] = $vo['member_id'];
			$wallet_add['update_time'] = time();
		    $wallet_model->add($wallet_add);
			$wallet_vo['balance'] = 0;
			$wallet_vo['frozen'] = 0;
		  }
		  //充值处理
		  if($vo['pay_type']==1){
			$wallet_sdata['balance'] = $wallet_vo['balance']+$vo['pay'];
			$result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		  }
		  //提现处理
		  if($vo['pay_type']==3){
			$wallet_sdata['frozen'] = $wallet_vo['frozen']-$vo['pay'];
			$result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		  }
		  //回购订单处理
		  if($vo['pay_type']==4){
		    if($wallet_vo){
			  $sdata['balance'] = $wallet_vo['balance']+$vo['pay'];
		      $wallet_sdata['balance'] = $sdata['balance'];
		      $result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		    }else{
		      $wallet_data['balance'] = $sdata['balance'];
		      $result = $wallet_model->add($wallet_data);
			  $sdata['balance'] = $vo['pay'];
		    }
		    $omodel = M('order');
			$odata['order_id'] = $vo['order_id'];
			$vv = $omodel->field('sourceid')->where($odata)->find();
			$osdata['status'] = 1;
			$result2 = $omodel->where($odata)->save($osdata);
		    $odmodel = M('order_detail');
			$oddata['oid'] = $vv['sourceid'];
			$odsdata['status'] = 2;
			$result3 = $odmodel->where($oddata)->save($odsdata);
			//echo $odmodel->getlastsql();exit;
		  }
		}
		//echo $wallet_model->getlastsql();exit;
		$sdata['payment_mode'] = $_POST['payment_mode'];
		$sdata['payment_company'] = $_POST['payment_company'];
		$sdata['pay_order_id'] = $_POST['pay_order_id'];
		if($vo['status']==0)$sdata['status'] = $_POST['status'];
		$sdata['content'] = $_POST['content'];
		$sdata['remark'] = $_POST['remark'];
		// 更新数据
		$list = $model->where($wdata)->save ($sdata);
		//dump($result);dump($list);dump($result2);dump($result3);exit;
		if (false !== $list) {
		  if($result && $result2 && $result3 && $list){
		    $model->commit();
		  }else{
			$model->rollback();
			$this->error ('修改失败!');
		  }
		  //成功提示
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
		} else {
		  $model->rollback();
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
   * 编辑 前置
   */
  public function _before_edit(){
    if($_POST){
	  unset($_POST['pay']);
	}
  }

  /**
   * 创建支付订单前缀 废弃
   */
  public function create_order(){
	$bh = str_pad($_POST['member_id'].time(),15,0,STR_PAD_LEFT);
	switch ($_POST['pay_type']) {
		case 1:
			$before = 'r';
			break;
		case 2:
			$before = 'c';
			break;
		case 3:
			$before = 'w';
			break;
		case 4:
			$before = 'b';
			break;
		case 5:
			$before = 'i';
			break;
	}
	echo $before.$bh;  
  }

  /**
   * 查看信息
   */
  public function look(){
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	$this->assign('vo',$vo);
    $this->display();
  }

}

?>
