<?php
namespace Home\Controller;
use Think\Controller;
//支付回调
class AliPaymentController extends Controller{

  protected $alipay_config; //支付宝配置
  protected $configs;       //网站配置
  protected $data;         //数据
  protected $remark_id;     //回调记录
  protected $payment_company = '支付宝';
  protected $payment_channel = 'app';//APP取渠道

  public function _initialize(){
	require_once(C('INTERFACE_PATH')."aliwappay/alipay.config.php");
	require_once(C('INTERFACE_PATH')."aliwappay/lib/alipay_notify.class.php");
    $this->alipay_config = $alipay_config;
	$_POST = unserialize('a:22:{s:12:"payment_type";s:1:"1";s:7:"subject";s:31:"美鞋家订单-201527691097773";s:8:"trade_no";s:28:"2015100400001000250061485978";s:11:"buyer_email";s:14:"8yong8@163.com";s:10:"gmt_create";s:19:"2015-10-04 14:52:06";s:11:"notify_type";s:17:"trade_status_sync";s:8:"quantity";s:1:"1";s:12:"out_trade_no";s:15:"201527691097773";s:9:"seller_id";s:16:"2088911540905383";s:11:"notify_time";s:19:"2015-10-04 14:52:07";s:4:"body";s:31:"美鞋家订单-201527691097773";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:19:"is_total_fee_adjust";s:1:"N";s:9:"total_fee";s:4:"0.01";s:11:"gmt_payment";s:19:"2015-10-04 14:52:07";s:12:"seller_email";s:15:"mxjmdsh@163.com";s:5:"price";s:4:"0.01";s:8:"buyer_id";s:16:"2088002040040257";s:9:"notify_id";s:34:"be0f89c09b433ddf6a4d02596b50415d3e";s:10:"use_coupon";s:1:"N";s:9:"sign_type";s:3:"RSA";s:4:"sign";s:172:"cpWqetmR0wy2Hr6mdvKaRehv+83qHTdQaZan14QoGo4nXggyNvDrHwlsvDRHo/aOWPszDaRcNaDhR0QHTp31DDmXMvJVvSzNTFYF1FkfCr84Le3cpuEW8wgultgdCIeKjzzFQx9yDiq/W2dE5az0EbKf49t+jhci6UdbOioZ13Y=";}');
	//dump($_POST);exit;
	//配置
	//$configs = include_once C('PUBLIC_CACHE')."config/list.php";
	//$this->configs = $configs;
	//dump($alipay_config);exit;
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyNotify();
	//dump($verify_result);exit;
	$pnmodel = M('Pay_notify');
	if($verify_result && $_POST['trade_status'] == 'TRADE_SUCCESS'){
	  //记录淘宝传值信息
	  $pn_data['order_id'] = $_POST['out_trade_no'];//我方订单
	  $remark = $pnmodel->field('id')->where($pn_data)->find();
	  if($remark){
		$this->remark_id = $remark['id'];
	  }else{
		$pn_data['rid'] = 0;
		$pn_data['info'] = serialize($_POST);
		$pn_data['rstatus'] = 0;
		$pn_data['trade_no'] = $_POST['trade_no'];//支付宝订单
		$pn_data['notice'] = '验证通过';
		$pn_data['create_time'] = time();
		$this->remark_id = $pnmodel->add($pn_data);
	  }
	}else{
	  //记录淘宝传值信息
	  $pn_data['order_id'] = $_POST['out_trade_no'];//我方订单
	  $count = $pnmodel->where($pn_data)->count();
	  if($count>0){
		echo "fail";exit;
	  }else{
		$pn_data['rid'] = 0;
		$pn_data['info'] = serialize($_POST);
		$pn_data['rstatus'] = 0;
		$pn_data['trade_no'] = $_POST['trade_no'];//支付宝订单
		$pn_data['notice'] = '验证不通过';
		$pn_data['create_time'] = time();
		$this->remark_id = $pnmodel->add($pn_data);
	  }
	  echo "fail";exit;
	}
  }

  //充值
  public function recharge(){
	//dump($_POST);exit;
	$alipayNotify = new AlipayNotify($this->alipay_config);
	$verify_result = $alipayNotify->verifyNotify();
	$time = time();
	if($verify_result && $_POST['trade_status'] == 'TRADE_SUCCESS') {
	  //商户订单号
	  $out_trade_no = $_POST['out_trade_no'];
	  //支付宝交易号
	  $trade_no = $_POST['trade_no'];
	  //交易状态
	  $trade_status = $_POST['trade_status'];
	  //买家支付宝账号
	  $buyer_email = $_POST['buyer_email'];
	  $rmodel = D('record');
	  $rdata['order_id'] = $out_trade_no;
	  $vo = $rmodel->field('id,member_id,member_name,realname,status')->where($rdata)->find();
	  if(!$vo){
		echo "success";exit;
	  }
	  //已支付
	  if($vo['status']==1){
		echo "success";exit;
	  }
	  //参数
	  $paramstr = $_POST['extra_common_param'];
	  if($paramstr){
		$arr = explode(',',$paramstr);
		foreach($arr as $val){
		  $ar = explode('|',$val);
		  $key = $ar[0];
		  $v = $ar[1];
		  $$key = $v;
		}
	  }
	  $rmodel->startTrans();//启用事务
	  $result = true;
	  //充值,修改用户余额
	  $wallet_model = M('member_wallet');
	  if($pay_type==1){
		//查找用户钱包
		$wallet_data['member_id'] = $vo['member_id'];
	    $wallet_vo = $wallet_model->where($wallet_data)->find();
		if($wallet_vo){
		  $balance = $wallet_vo['balance']+$_POST['total_fee'];
		  if($t==1){
		    $balance = $wallet_vo['balance']+$_POST['total_fee'];
			$frozen = $wallet_vo['frozen'];
		  }else{
		    $balance = $wallet_vo['balance'];
			$frozen = $wallet_vo['frozen']+$_POST['total_fee'];
		    $mwlmodel = M('member_wallet_log');
		    $mwl_data['title'] = $_POST['body'];
		    $mwl_data['member_id'] = $vo['member_id'];
		    $mwl_data['aid'] = $aid ? $aid : 0;
		    $mwl_data['rid'] = $vo['id'];
		    $mwl_data['pay_type'] = 1;//冻结
		    $mwl_data['pay'] = $_POST['total_fee'];
		    $mwl_data['content'] = '保证金';
		    $mwl_data['create_time'] = $time;
		    $result = $mwlmodel->add($mwl_data);
		  }
		  $wallet_sdata['balance'] = $balance;
		  $wallet_sdata['update_time'] = $time;
		  if($result)$result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		}else{
		  if($t==1){
		    $balance = $_POST['total_fee'];
			$frozen = 0;
		  }else{
		    $balance = 0;
			$frozen = $_POST['total_fee'];
		    $mwlmodel = M('member_wallet_log');
		    $mwl_data['title'] = $_POST['body'];
		    $mwl_data['member_id'] = $vo['member_id'];
		    $mwl_data['oid'] = $oid ? $oid : 0;
		    $mwl_data['rid'] = $vo['id'];
		    $mwl_data['pay_type'] = 1;//冻结
		    $mwl_data['pay'] = $_POST['total_fee'];
		    $mwl_data['content'] = '保证金';
		    $mwl_data['create_time'] = $time;
		    $result = $mwlmodel->add($mwl_data);
		  }
		  $wallet_sdata['balance'] = $balance;
		  $wallet_sdata['frozen'] = $frozen;
		  $wallet_sdata['member_id'] = $vo['member_id'];
		  $wallet_sdata['update_time'] = $time;
		  if($result)$result = $wallet_model->add($wallet_sdata);
		}
	  }else{
	    echo "success";exit;
	  }

	  //echo $balance;
	  //订单状态修改
	  $sdata['status'] = 1;
	  $sdata['pay_order_id'] = $trade_no;
	  $sdata['balance'] = $balance;
	  //$sdata['frozen'] = $frozen;
	  $sdata['pay_time'] = $time;
	  $result2 = $rmodel->where($rdata)->save($sdata);
	  //echo $rmodel->getlastsql();
	  if($result && $result2){
	    $rmodel->commit();
		$rstatus = 1;
	  }else{
	    $rmodel->rollback();
		$rstatus = 0;
	    //记录淘宝传值信息
	    $rrmodel = M('pay_notify');
	    $rrdata['rid'] = $vo['id'];
	    $rrdata['info'] = serialize($_POST);
	    $rrdata['rstatus'] = $rstatus;
	    $rrdata['create_time'] = $time;
	    $rrmodel->add($rrdata);
		echo "success";exit;
	  }
	  //记录淘宝传值信息
	  $rrmodel = M('pay_notify');
	  $rrwdata['order_id'] = $_POST['trade_no'];
	  $rrdata['rid'] = $vo['id'];
	  $rrdata['rstatus'] = $rstatus;
	  $rrdata['create_time'] = $time;
	  $rrmodel->where($rrwdata)->save($rrdata);
	  echo "success";
	}else{
	  echo "fail";
	}
  
  }

//阿里支付,单一订单
  public function notify(){
	$time = time();
	if($_POST['trade_status'] == 'TRADE_SUCCESS') {
	  //商户订单号
	  $out_trade_no = $_POST['out_trade_no'];
	  //支付宝交易号
	  $trade_no = $_POST['trade_no'];
	  //交易状态
	  $trade_status = $_POST['trade_status'];
	  //买家支付宝账号
	  $buyer_email = $_POST['buyer_email'];
      $model = M('Order');
      $data['order_sn'] = $out_trade_no;
	  $vo = $model->field('id,type,order_sn,bond,member_id,member_name,realname,seller_id,title,actual_paid,pay_status,status')->where($data)->find();
	  //echo $model->getlastsql();
	  //判断购买订单是否存在
	  if(!$vo){
		$this->update_remark('订单不存在');
	    //echo "success";exit;
	  }else{
	    //支付判断
	    if($vo['pay_status']>0){
		  $this->update_remark('订单已支付');
	    }
		//价格判断
	    if($vo['actual_paid']!=$_POST['total_fee']){
		  $this->update_remark('支付价格错误');
	    }
	    $model->startTrans();//启用事务
		//传参数存在
		if($_POST['extra_common_param']){
		  $paramstr = $_POST['extra_common_param'];
		  if($paramstr){
			$arr = explode(',',$paramstr);
			foreach($arr as $val){
			  $ar = explode('|',$val);
			  $key = $ar[0];
			  $v = $ar[1];
			  $$key = $v;
			}
		  }
		}
		//支付渠道
		if($payment_channel){
			$this->payment_channel = $payment_channel;
		}
	    //订单状态修改
	    $sdata['pay_status'] = 1;
	    $sdata['pay_order_id'] = $trade_no;
		$sdata['payment_mode'] = 1;
		$sdata['payment_company'] = $this->payment_company;
		$sdata['payment_channel'] = $this->payment_channel;
	    $sdata['pay_time'] = $time;
	    $result = $model->where($data)->save($sdata);

		//修改详细订单状态 (废弃,制至支付完成后处理)
		/*
		if($result){
		  $odmodel = M('Order_detail');
		  $od_data['oid'] = $vo['id'];
		  $od_sdata['status'] = 1;
		  $od_sdata['pay_time'] = $time;
		  $result = $odmodel->where($od_data)->save($od_sdata);
		  unset($od_data);
		}else{
			$this->update_remark('订单状态修改失败');
		}
		*/
	  }
	  dump($vo);exit;
	  //买家账户记录
	  $wallet_model = M('Member_wallet');
	  $rdata['member_id'] = $vo['member_id'];
	  $wallet = $wallet_model->where($rdata)->find();
	  $rmodel = D('Record');
	  $rdata['pay_type'] = $pay_type ? $pay_type : 2;
	  $rdata['member_name'] = $vo['member_name'];
	  //$rdata['realname'] = $vo['realname'];
	  $rdata['payment_mode'] = 1;
	  $rdata['payment_company'] = $payment_company ? $payment_company : 'alipay';
	  $rdata['order_id'] = $vo['order_id'];
	  $rdata['pay_order_id'] = $trade_no;
	  $rdata['buyer'] = $buyer_email;
	  $rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
	  $rdata['content'] = $_POST['body'] ? $_POST['body'] : '';
	  $rdata['pay'] = $_POST['total_fee'];
	  $rdata['create_time'] = $time;
	  $rdata['status'] = 1;
	  $rdata['pay_time'] = $_POST['gmt_payment'] ? strtotime($_POST['gmt_payment']) : $time;
	  $rid = $rmodel->add($rdata);
	  //拍卖,扣除买家保证金
	  if($vo['bond']){
		  $wallet_data2['member_id'] = $vo['member_id'];
		  if($result)$result=$wallet_model->where($wallet_data2)->setDec('frozen',$vo['bond']);
		  //记录变化
		  $wlmodel = M('member_wallet_log');
		  $wl_data['title'] = '扣除保证金并完成支付，订单号：'.$vo['order_id'];
		  $wl_data['member_id'] = $vo['member_id'];
		  $wl_data['oid'] = $vo['id'];
		  $wl_data['pay_type'] = 3;
		  $wl_data['pay'] = $vo['bond'];
		  $wl_data['content'] = '扣除保证金并完成支付，订单号：<a href="'.__APP__.'/Orders/index/order_sn/'.$vo['order_sn'].'">'.$vo['order_sn'].'</a>';
		  $wl_data['create_time'] = time();
		  if($result)$result=$wlmodel->add($wl_data);
	  }
	  //卖家资金记录
	  if($vo['seller_id']){
	  
	  }
	  if($result){
		//支付完成后
	    $result = after_pay($vo);
	  }
	  if($result && $rid){
	    $model->commit();
		$rstatus = 1;
	  }else{
	    $model->rollback();
	    //记录淘宝传值信息
	    $this->update_remark('支付失败');
	  }
	  $log['order_sn'] = $vo['order_sn'];
	  $log['utype'] = 1;
	  $log['user_id'] = $vo['member_id'];
	  $log['user_name'] = $vo['member_name'];
	  $log['msg'] = '支付宝支付';
	  $log['action'] = '完成支付';
	  $log['create_time'] = $time;
	  $log['ip'] = _get_ip();
	  order_log($log);
	  //记录淘宝传值信息
	  $this->update_remark('支付完成');
	  echo "success";
	}else{
	  echo "fail";
	}
  }

  //阿里支付,多订单支付
  public function notify_merge(){
	$time = time();
	$alipayNotify = new AlipayNotify($this->alipay_config);
	$verify_result = $alipayNotify->verifyNotify();
	if($verify_result && $_POST['trade_status'] == 'TRADE_SUCCESS') {
	  //商户订单号
	  $out_trade_no = $_POST['out_trade_no'];
	  //支付宝交易号
	  $trade_no = $_POST['trade_no'];
	  //交易状态
	  $trade_status = $_POST['trade_status'];
	  //买家支付宝账号
	  $buyer_email = $_POST['buyer_email'];
	  //$array_ids = explode(',',$_POST['out_trade_no']);
	  $model = M('order');
	  $olist_data['mo_id'] = $out_trade_no;
	  $orderlist = $model->field('id,order_id,type,sourceid,total_fee,total_num,bond,other_pay,member_id,member_name,realname,user_id,title,goods,status')->where($olist_data)->order('id asc')->select();
	  $odmodel = M('order_detail');
	  $rmodel = M('record');
	  $wallet_model = M('member_wallet');
	  $wlmodel = M('member_wallet_log');
	  $model->startTrans();//启用事务
	  if($_POST['extra_common_param']){
		$paramstr = $_POST['extra_common_param'];
		if($paramstr){
			$arr = explode(',',$paramstr);
			foreach($arr as $val){
			  $ar = explode('|',$val);
			  $key = $ar[0];
			  $v = $ar[1];
			  $$key = $v;
			}
		}
	  }
	  //支付渠道
	  if($payment_channel){
		$this->payment_channel = $payment_channel;
	  }
	  foreach($orderlist as $vo){
		/*
		$data['id'] = $id;
		$vo = $model->field('id,order_id,type,sourceid,total_fee,total_num,bond,other_pay,member_id,member_name,realname,user_id,title,goods,status')->where($data)->find();
		*/
		//已支付
		if($vo['status']>0){
		  $model->rollback();
		  $this->update_remark('已支付');
		  echo "success";exit;	
		}
		if($vo['type']==2){
		  //云购直接完成交易
		  $sdata['confirm_time'] = $time;
		}
		//订单状态修改
		$wdata['id'] = $vo['id'];
		$sdata['status'] = 1;
		$sdata['payment_mode'] = '1';
		$sdata['payment_company'] = $this->payment_company;
		$sdata['payment_channel'] = $this->payment_channel;
		$sdata['pay_order_id'] = $trade_no;
		$sdata['pay_time'] = $time;
		$result = $model->where($wdata)->save($sdata);
		if(!$result){
		  $model->rollback();
		  $this->update_remark('订单状态修改失败');
		  echo "success";exit;			
		}
		unset($rdata);
		//买家账户记录
		$rdata['member_id'] = $vo['member_id'];
		$wallet = $wallet_model->where($rdata)->find();
		$rdata['pay_type'] = $pay_type ? $pay_type : 2;
		$rdata['member_name'] = $vo['member_name'];
		$rdata['realname'] = $vo['realname'];
		$rdata['payment_mode'] = '1';
		$rdata['payment_company'] = '支付宝';
		$rdata['order_id'] = $vo['order_id'];
		$rdata['pay_order_id'] = $trade_no;
		$rdata['buyer'] = $buyer_email;
		$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
		$rdata['content'] = $_POST['body'] ? $_POST['body'] : '';
		if($vo['bond']){
		    $rdata['content'] .= '共支付:'.$vo['total_fee'].';支付宝支付'.$_POST['total_fee'].';保证金支付'.$vo['bond'];
		}else{
		    $rdata['content'] .= '共支付:'.$vo['total_fee'];
		}
		$rdata['pay'] = $_POST['total_fee'];
		$rdata['create_time'] = $time;
		$rdata['status'] = 1;
		$rdata['pay_time'] = $_POST['gmt_payment'] ? strtotime($_POST['gmt_payment']) : $time;
		$rid = $rmodel->add($rdata);
		if(!$rid){
			  $model->rollback();
			  $this->update_remark('买家账户记录失败');
			  exit;	
		}
		//扣除买家保证金
		if($vo['bond']){
			  $wallet_data2['member_id'] = $vo['member_id'];
			  if($result)$result=$wallet_model->where($wallet_data2)->setDec('frozen',$vo['bond']);
			  //记录变化
			  $wl_data['title'] = '扣除保证金并完成支付，订单号：'.$vo['order_id'];
			  $wl_data['member_id'] = $vo['member_id'];
			  $wl_data['oid'] = $vo['id'];
			  $wl_data['pay_type'] = 3;
			  $wl_data['pay'] = $vo['bond'];
			  $wl_data['content'] = '扣除保证金并完成支付，订单号：<a href="'.C('MEMBER_SITE_URL').'/index.php/Orders/index/order_id/'.$vo['order_id'].'">'.$vo['order_id'].'</a>;扣除保证金:'.$vo['bond'];
			  $wl_data['create_time'] = time();
			  $result=$wlmodel->add($wl_data);
			  if(!$result){
				  $model->rollback();
				  $this->update_remark('保证金操作失败');
				  exit;	
			  }
		}
		//减去冻结资金
		if($vo['other_pay']){
			  $wallet_data2['member_id'] = $vo['member_id'];
			  if($result)$result=$wallet_model->where($wallet_data2)->setDec('frozen',$vo['bond']);
			  //记录变化
			  $wl_data['title'] = '扣除冻结资金并完成支付，订单号：'.$vo['order_id'];
			  $wl_data['member_id'] = $vo['member_id'];
			  $wl_data['oid'] = $vo['id'];
			  $wl_data['pay_type'] = 3;
			  $wl_data['pay'] = $vo['bond'];
			  $wl_data['content'] = '扣除冻结资金并完成支付，订单号：<a href="'.C('MEMBER_SITE_URL').'/index.php/Orders/index/order_id/'.$vo['order_id'].'">'.$vo['order_id'].'</a>;扣除保证金:'.$vo['bond'];
			  $wl_data['create_time'] = time();
			  $result=$wlmodel->add($wl_data);
			  if(!$result){
				  $model->rollback();
				  $this->update_remark('冻结资金操作失败');
				  exit;	
			  }
		}
	    //卖家资金记录
	    if($vo['seller_id']){
	  
	    }
		if($result && $rid){
			//支付成功
			  $log['order_sn'] = $vo['order_sn'];
			  $log['utype'] = 1;
			  $log['user_id'] = $vo['member_id'];
			  $log['user_name'] = $vo['member_name'];
			  $log['msg'] = '支付宝支付';
			  $log['action'] = '完成支付';
			  $log['create_time'] = $time;
			  $log['ip'] = _get_ip();
			  order_log($log);
		  }else{
			$model->rollback();
			echo "success";exit;
			$rstatus = 0;
		}
	  }
	  $model->commit();
	  $rstatus = 1;
	  //记录淘宝传值信息
	  $rrmodel = M('pay_notify');
	  $rrwdata['order_id'] = $_POST['trade_no'];
	  //$rrdata['rid'] = $rid ? $rid : 0;
	  $rrdata['oid'] = $_POST['out_trade_no'];
	  $rrdata['rstatus'] = $rstatus;
	  $rrdata['create_time'] = $time;
	  $rrmodel->where($rrwdata)->save($rrdata);
	  echo "success";
	}else{
	  echo "fail";
	}
  }

  /**
   +----------------------------------------------------------
   * 记录错误节点
   +----------------------------------------------------------
   * @access protected
   +----------------------------------------------------------
   * @param string $error_msg 错误信息
   * @param boolean $status 0：停止执行 1：继续执行
   +----------------------------------------------------------
  */
  protected function update_remark($error_msg,$status=0){
    $model = M('Pay_notify');
	$wdata['id'] = $this->remark_id;
	$sdata['payment_channel'] = $this->payment_channel;
    $sdata['notice'] = $error_msg;
	$model->where($wdata)->save($sdata);
	if($status==0){
		$this->ReplyNotify();		
	}
	return;
  }

  public function ali_return(){
	unset($_SESSION['share']);
	$out_trade_no = $_GET['out_trade_no'];
	header("location:".__ROOT__."/member/index.php/Record/");
  }

  /** 
  * 返回信息
  */ 
  protected function ReplyNotify(){
	 echo "success";exit;
  }

}
?>