<?php
namespace Home\Controller;
use Think\Controller;
//支付回调
class WxPaymentController extends Controller{

  protected $pay_config;   //支付配置
  protected $site_configs; //网站配置  
  protected $data;         //数据
  protected $remark_id;    //记录
  protected $payment_company = '微信支付';//支付公司
  protected $payment_channel = 'WxAppPay';//APP支付

  public function _initialize(){
	require_once C('INTERFACE_PATH')."Wxpay/lib/WxPay.Api.php";
	require_once C('INTERFACE_PATH')."Wxpay/unit/WxPay.JsApiPay.php";
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	$xml = '<xml>
	<return_code><![CDATA[SUCCESS]]></return_code>
	<return_msg><![CDATA[OK]]></return_msg>
	<appid><![CDATA[wx2421b1c4370ec43b]]></appid> <mch_id><![CDATA[10000100]]></mch_id> <device_info><![CDATA[1000]]></device_info> <nonce_str><![CDATA[FvYSnPuFFPkAr77M]]></nonce_str> <sign><![CDATA[63238039D6E43634297CF2A6EB5F3B72]]></sign>
	<result_code><![CDATA[SUCCESS]]></result_code> <openid><![CDATA[oUpF8uN95-Ptaags6E_roPHg7AG0]]></openid>
	<is_subscribe><![CDATA[Y]]></is_subscribe> <trade_type><![CDATA[JSAPI]]></trade_type> <bank_type><![CDATA[CCB_CREDIT]]></bank_type>
	<total_fee>100</total_fee>
	<coupon_fee>0</coupon_fee>
	<fee_type><![CDATA[CNY]]></fee_type> <transaction_id><![CDATA[1008450740201407220000058756]]></transaction_id>
	<out_trade_no><![CDATA[20150519975154104261,20150529101555658541]]></out_trade_no>
	<attach><![CDATA[pay_type|2]]></attach> <time_end><![CDATA[20140722160655]]></time_end>
	</xml>';
	/*
	$xml = '<xml>
	<return_code><![CDATA[SUCCESS]]></return_code>
	<return_msg><![CDATA[OK]]></return_msg>
	<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
	<mch_id><![CDATA[10000100]]></mch_id>
	<device_info><![CDATA[1000]]></device_info>
	<nonce_str><![CDATA[sthBJ9QyUG6vkrjJ]]></nonce_str>
	<sign><![CDATA[6277A96D7875D4FF23AA7B6A4C3046AB]]></sign>
	<result_code><![CDATA[FAIL]]></result_code>
	<err_code><![CDATA[PAYERROR]]></err_code>
	<err_code_des><![CDATA[支付错误]]></err_code_des>
	</xml> ';
	*/
	$this->data = WxPayResults::FromXml($xml);
	//dump($this->data);exit;
	$checkSign = WxPayResults::Init2($xml);
	if($checkSign){
	    $transaction_id = $this->data['transaction_id'];
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$order = WxPayApi::orderQuery($input);
		if($order['result_code']=='SUCCESS'){
		   $order_status = 1;
		   $error_msg = '订单验证成功';
		}else{
		   $order_status = 0;
		   $error_msg = serialize($order);
		}
	}else{
	  $error_msg = '签名错误';
	}
	//配置
	if($checkSign !== FALSE){
	  //记录传值信息
	  $rrmodel = M('pay_notify');
	  $rrdata['type'] = 2;
	  $rrdata['rid'] = 0;
	  $rrdata['info'] = $xml;
	  $rrdata['error_msg'] = $error_msg;
	  $rrdata['rstatus'] = 0;
	  $rrdata['order_id'] = $this->data['transaction_id'];//微信订单
	  $rrdata['oid'] = $this->data['out_trade_no'];//我方订单
	  $rrdata['create_time'] = time();
	  $remark_id = $rrmodel->add($rrdata);
	  $this->remark_id = $remark_id;
	  if($order_status==0){
	    //订单支付失败
		//exit;
	  }
	  //echo $rrmodel->getlastsql();exit;
	}else{
		$returnXml = $notify->returnXml();
	    //Common_util_pub::postXmlSSLCurl($returnXml,'localhost');
		//dump($returnXml);
		$rrmodel = M('pay_notify');
		$rrdata['type'] = 2;
		$rrdata['rid'] = 0;
		$rrdata['info'] = $xml;
		$rrdata['error_msg'] = $error_msg;
		$rrdata['rstatus'] = 0;
		$rrdata['order_id'] = $this->data['transaction_id'];//微信订单
		$rrdata['oid'] = $this->data['out_trade_no'];//我方订单
		$rrdata['create_time'] = time();
		$rrmodel->add($rrdata);
		//exit;
	}
	$this->ReplyNotify();
  }

  //充值
  public function recharge(){
	//商户订单号
	$out_trade_no = $this->data['out_trade_no'];
	//交易号
	$trade_no = $this->data['transaction_id'];
	//交易状态
	$trade_status = $this->data['result_code'];
	if($trade_status=='SUCCESS'){
	  //买家支付宝账号
	  $rmodel = D('record');
	  $rdata['order_id'] = $out_trade_no;
	  $vo = $rmodel->field('id,member_id,member_name,realname,status')->where($rdata)->find();
	  $time = time();
	  if(!$vo){
		$this->update_remark('充值订单不存在');
	  }
	  //已支付
	  if($vo['status']==1){
		$this->update_remark('已支付');
	  }
	  //参数
	  $paramstr = $this->data['attach'];
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
		  $balance = $wallet_vo['balance']+$this->data['total_fee'];
		  $frozen = $wallet_vo['frozen'];
		  $wallet_sdata['balance'] = $balance;
		  $wallet_sdata['update_time'] = $time;
		  if($result){
			  $result = $wallet_model->where($wallet_data)->save($wallet_sdata);
		  }else{
		    $this->update_remark('用户钱包修改失败');
		  }
		}else{
		  $frozen = 0;
		  $wallet_sdata['balance'] = $balance;
		  $wallet_sdata['frozen'] = $frozen;
		  $wallet_sdata['member_id'] = $vo['member_id'];
		  $wallet_sdata['update_time'] = $time;
		  if($result){
			$result = $wallet_model->add($wallet_sdata);
		  }else{
		    $this->update_remark('用户钱包添加失败');
		  }
		}
	  }else{
	    $this->update_remark('非充值订单');
	  }
	  //echo $balance;
	  //订单状态修改
	  $sdata['status'] = 1;
	  $sdata['pay_order_id'] = $trade_no;
	  $sdata['balance'] = $balance;
	  //$sdata['frozen'] = $frozen;
	  $sdata['pay_time'] = $this->data['time_end'];
	  $result = $rmodel->where($rdata)->save($sdata);
	  if($result){
	    $rmodel->commit();
		$rstatus = 1;
	  }else{
	    $rmodel->rollback();
		$this->update_remark('订单修改失败');
	  }
	  //记录传值信息
	  $rrmodel = M('pay_notify');
	  $rrwdata['order_id'] = $this->data['transaction_id'];
	  $rrdata['rid'] = $vo['id'];
	  $rrdata['rstatus'] = 1;
	  //$rrdata['create_time'] = $time;
	  $rrmodel->where($rrwdata)->save($rrdata);
	  $this->ReplyNotify();
	}else{
	  $this->update_remark('非充值订单');
	}
  }

//多订单支付
  public function notify(){
	$time = time();
	//商户订单号
	$out_trade_no = $this->data['out_trade_no'];
	//交易号
	$trade_no = $this->data['transaction_id'];
	//交易状态
	$trade_status = $this->data['result_code'];
	//openid
	$outAccount = $this->data['openid'];
	//交易金额
	$totalFee = $this->data['total_fee'];
	//dump($this->data);exit;
	if($trade_status=='SUCCESS'){
	  //买家支付宝账号
	  //$array_ids = explode(',',$this->data['out_trade_no']);
	  $model = M('order');
	  //$olist_data['order_id'] = $out_trade_no;
	  //$vo = $model->field('id,order_id,type,sourceid,total_fee,total_num,bond,other_pay,member_id,member_name,realname,user_id,title,goods,status')->where($olist_data)->order('id asc')->find();
	  $odmodel = M('order_detail');
	  $rmodel = M('record');
	  $wallet_model = M('member_wallet');
	  $wlmodel = M('member_wallet_log');
	  $model->startTrans();//启用事务
	  if($this->data['attach']){
		$paramstr = $this->data['attach'];
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
	  if($payment_channel){
		$this->payment_channel = $payment_channel;
	  }

		$data['order_id'] = $out_trade_no;
		$vo = $model->field('id,order_id,type,sourceid,actual_paid,bond,other_pay,member_id,member_name,user_id,title,pay_status,status')->where($data)->find();

		//判断购买订单是否存在
		//已支付
		if($vo['pay_status']>0){
		  $model->rollback();
		  $this->update_remark('已支付');
		  exit;
		}
	    $payAmount = $vo['actual_paid']*100;
	    $payAmount = number_format($payAmount,2,'.','');
	    if($payAmount!=$totalFee){
		  $this->update_remark('价格不正确');
		  exit ;
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
		  exit;			
		}
		unset($rdata);
		//买家账户记录
		$rdata['member_id'] = $vo['member_id'];
		$wallet = $wallet_model->where($rdata)->find();
		$rdata['pay_type'] = $pay_type ? $pay_type : 2;
		$rdata['member_name'] = $vo['member_name'];
		$rdata['realname'] = $vo['realname'];
		$rdata['payment_mode'] = '1';
		$rdata['payment_company'] = $this->payment_company;
		$rdata['order_id'] = $vo['order_id'];
		$rdata['pay_order_id'] = $trade_no;
		$rdata['buyer'] = $vo['realname'] ? $vo['realname'] : '' ;
		$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
		$rdata['content'] = $this->data['body'] ? $this->data['body'] : '';
		$rdata['content'] .= '共支付:'.$vo['total_fee'].';微信支付'.$this->data['total_fee'];
		if($vo['bond']){
			$rdata['content'] .= ';保证金支付'.$vo['bond'];
		}
		if($vo['other_pay']){
			$rdata['content'] .= ';其他支付'.$vo['other_pay'];
		}
		$rdata['pay'] = $this->data['total_fee'];
		$rdata['create_time'] = $time;
		$rdata['status'] = 1;
		$rdata['pay_time'] = $this->data['gmt_payment'] ? strtotime($this->data['gmt_payment']) : $time;
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
		if($vo['balance_fee']){
			  $wallet_data2['member_id'] = $vo['member_id'];
			  if($result)$result=$wallet_model->where($wallet_data2)->setDec('frozen',$vo['bond']);
			  //记录变化
			  $wl_data['title'] = '扣除冻结资金并完成支付，订单号：'.$vo['order_sn'];
			  $wl_data['member_id'] = $vo['member_id'];
			  $wl_data['oid'] = $vo['id'];
			  $wl_data['pay_type'] = 3;
			  $wl_data['pay'] = $vo['bond'];
			  $wl_data['content'] = '扣除冻结资金并完成支付，订单号：<a href="'.C('MEMBER_SITE_URL').'/index.php/Orders/index/order_sn/'.$vo['order_sn'].'">'.$vo['order_id'].'</a>;扣除保证金:'.$vo['bond'];
			  $wl_data['create_time'] = time();
			  $result=$wlmodel->add($wl_data);
			  if(!$result){
				  $model->rollback();
				  $this->update_remark('冻结资金操作失败');
				  exit;	
			  }
		}
		if($vo['type']!=2){
			//产品减库存
			$od_data['oid'] = $vo['id'];
			$list = $odmodel->field('sourceid,num')->where($od_data)->select();
			$od_sdata['trade_status'] = 1;
			$result = $odmodel->where($od_data)->save($od_sdata);
			//echo $odmodel->getlastsql();
			$goods_model = M('goods');
			foreach($list as $val){
			  $this->stock_minus($val['sourceid'],$val['num']);
			}
			if(!$result){
				$model->rollback();
				$this->update_remark('减库存操作失败');
				exit;	
			}
		}else{
			$result = $this->order_do_yg($vo);
		}
		  //dump($result);dump($rid);
		if($result){
			//支付成功
		}else{
			$model->rollback();
			$this->update_remark('支付失败');
		}

	  $model->commit();
	  $rstatus = 1;
	  //记录传值信息
	  $rrmodel = M('pay_notify');
	  $rrwdata['order_id'] = $trade_no;
	  //$rrdata['rid'] = $rid ? $rid : 0;
	  $rrdata['oid'] = $this->data['out_trade_no'];
	  $rrdata['rstatus'] = $rstatus;
	  $rrdata['create_time'] = $time;
	  $rrmodel->where($rrwdata)->save($rrdata);
	  $this->ReplyNotify();
	}else{
	  $this->update_remark('支付失败');
	}
  }

//多订单支付
  public function notify_merge(){
	$time = time();
	//商户订单号
	$out_trade_no = $this->data['out_trade_no'];
	//交易号
	$trade_no = $this->data['transaction_id'];
	//交易状态
	$trade_status = $this->data['result_code'];
	//dump($this->data);exit;
	if($trade_status=='SUCCESS'){
	  //$array_ids = explode(',',$this->data['out_trade_no']);
	  $model = M('order');
	  $olist_data['mo_id'] = $out_trade_no;
	  $orderlist = $model->field('id,order_id,type,sourceid,total_fee,total_num,bond,other_pay,member_id,member_name,realname,user_id,title,goods,status')->where($olist_data)->order('id asc')->select();
	  $odmodel = M('order_detail');
	  $rmodel = M('record');
	  $wallet_model = M('member_wallet');
	  $wlmodel = M('member_wallet_log');
	  $model->startTrans();//启用事务
	  if($this->data['attach']){
		$paramstr = $this->data['attach'];
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
	  if($payment_channel){
		$this->payment_channel = $payment_channel;
	  }
	  foreach($orderlist as $vo){
		/*
		$data['order_id'] = $order_id;
		$vo = $model->field('id,order_id,type,sourceid,total_fee,total_num,bond,other_pay,member_id,member_name,realname,user_id,title,goods,status')->where($data)->find();
		*/
		//判断购买订单是否存在
		//已支付
		if($vo['status']>0){
		  $model->rollback();
		  $this->update_remark('已支付');
		  exit;
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
		  exit;			
		}
		unset($rdata);
		//买家账户记录
		$rdata['member_id'] = $vo['member_id'];
		$wallet = $wallet_model->where($rdata)->find();
		$rdata['pay_type'] = $pay_type ? $pay_type : 2;
		$rdata['member_name'] = $vo['member_name'];
		$rdata['realname'] = $vo['realname'];
		$rdata['payment_mode'] = '1';
		$rdata['payment_company'] = $this->payment_company;
		$rdata['order_id'] = $vo['order_id'];
		$rdata['pay_order_id'] = $trade_no;
		$rdata['buyer'] = $vo['realname'] ? $vo['realname'] : '' ;
		$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
		$rdata['content'] = $this->data['body'] ? $this->data['body'] : '';
		$rdata['content'] .= '共支付:'.$vo['total_fee'].';微信支付'.$this->data['total_fee'];
		if($vo['bond']){
			$rdata['content'] .= ';保证金支付'.$vo['bond'];
		}
		if($vo['other_pay']){
			$rdata['content'] .= ';其他支付'.$vo['other_pay'];
		}
		$rdata['pay'] = $this->data['total_fee'];
		$rdata['create_time'] = $time;
		$rdata['status'] = 1;
		$rdata['pay_time'] = $this->data['gmt_payment'] ? strtotime($this->data['gmt_payment']) : $time;
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
		if($vo['type']!=2){
			//产品减库存
			$od_data['oid'] = $vo['id'];
			$list = $odmodel->field('sourceid,num')->where($od_data)->select();
			$od_sdata['trade_status'] = 1;
			$result = $odmodel->where($od_data)->save($od_sdata);
			//echo $odmodel->getlastsql();
			$goods_model = M('goods');
			foreach($list as $val){
			  $this->stock_minus($val['sourceid'],$val['num']);
			}
			//dump($result);exit;
			if(!$result){
				$model->rollback();
				$this->update_remark('减库存操作失败');
				exit;	
			}
		}else{
			$result = $this->order_do_yg($vo);
		}
		  //dump($result);dump($rid);
		if($result){
			//支付成功
		}else{
			$model->rollback();
			$this->update_remark('支付失败');
		}
	  }
	  $model->commit();
	  $rstatus = 1;
	  //记录淘宝传值信息
	  $rrmodel = M('pay_notify');
	  $rrwdata['order_id'] = $trade_no;
	  //$rrdata['rid'] = $rid ? $rid : 0;
	  $rrdata['oid'] = $this->data['out_trade_no'];
	  $rrdata['rstatus'] = $rstatus;
	  $rrdata['create_time'] = $time;
	  $rrmodel->where($rrwdata)->save($rrdata);
	  $this->ReplyNotify();
	}else{
	  $this->update_remark('支付失败');
	}
  }

  //减库存
  function stock_minus($id,$num){
    $model = M('product');
	$data['id'] = $id;
    $model->where($data)->setDec('stock',$num);
  }

  //云购
  public function order_do_yg($vo){
	include C('PUBLIC_INCLUDE')."function.inc.php";
	//获得云码
	$codes = pay_get_shop_codes($vo['total_num'],$vo['sourceid']);
	if($codes['code_len']<$s_num){
	  $s_num = $codes['code_len'];
	}
	$model = M('shoplist');
	if($codes==false || $codes['code_len']==0){
	  $model->rollback();
	  $this->update_remark('云码不存在');
	  exit;
	}
	$data['id'] = $vo['sourceid'];
	$shop = $model->where($data)->find();
	//记录购买云码
	$mgrmodel = M('go_record');
	$mgr_data['member_id'] = $vo['member_id'];
	$mgr_data['member_name'] = $vo['member_name'];
	$mgr_data['order_id'] = $vo['id'];
	$mgr_data['shopid'] = $vo['sourceid'];
	$mgr_data['shopname'] = $shop['name'];
	$mgr_data['shopqishu'] = $shop['qishu'];
	$mgr_data['goucode'] = implode(',',$codes['codes']);
	$mgr_data['code_num'] = $codes['code_len'];
	$timearr = explode(' ',microtime());
	$mgr_data['ms'] = substr($timearr[0],2,3);
	$mgr_data['create_time'] = $timearr[1];
	$result = $mgrmodel->add($mgr_data);
	//echo $mgrmodel->getlastsql();
	if(!$result){
	  $model->rollback();
	  $this->update_remark('会员云码记录失败');
	  exit;
	}
	return $result;
  }

  //记录错误节点
  protected function update_remark($error_msg){
    $model = M('pay_notify');
	$wdata['id'] = $this->remark_id;
    $sdata['error_msg'] = $error_msg;
	$model->where($wdata)->save($sdata);
	$this->ReplyNotify();
  }

  //退款
  public function refund(){
  
  
  }

  //返回信息
  protected function ReplyNotify(){
    $values['return_code'] = 'SUCCESS';
	$values['return_msg'] = 'OK';
	$returnXml = "<xml>";
	foreach ($values as $key=>$val)
	{
		if (is_numeric($val)){
			$returnXml.="<".$key.">".$val."</".$key.">";
		}else{
			$returnXml.="<".$key."><![CDATA[".$val."]]></".$key.">";
		}
	}
	$returnXml.="</xml>";
	echo $returnXml;
	exit;
  }


}
?>