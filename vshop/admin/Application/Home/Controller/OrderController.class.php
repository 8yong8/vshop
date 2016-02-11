<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends CommonController {

  protected $can_del = 0; //0不可删除 1可删除

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Order');
	$this->oddb = D('Order_detail');
	$this->otdb = D('Order_track');
	$this->rdb = D('Record');
  }

  /**
   * 编辑
   */
  public function edit(){
	  if(IS_POST){	
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$wdata['id'] = $_POST['id'];
		$vo = $model->field('id,order_id,type,total_fee,bond,member_id,member_name,realname,seller_id,title,goods,delivery_status,status')->where($wdata)->find();
		if($vo['status']==2){
		  $this->error('已完成交易订单无法修改!');
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$model->startTrans();//启用事务
		$result = $model->save();
		if (false !== $list) {
		  //完成支付
		  if($vo['status']==0 && $_POST['status']==1){
			$status = $this->pay_complete($vo);
			if($status){
			  $model->commit();
			}else{
			  $model->rollback();
			  $this->error ('编辑失败!');
			}
		  }else{
		    $model->commit();
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
		$id = $_REQUEST [$this->db->getPk ()];
		$vo = $this->db->getById ( $id );
		$prev_data['id'] = array('lt',$id);
		$prev = $this->db->field('id')->where($prev_data)->order('id desc')->find();
		$this->assign('prev',$prev);
		$next_data['id'] = array('gt',$id);
		$next = $this->db->field('id')->where($next_data)->order('id asc')->find();
		$this->assign('next',$next);
		if($vo['seller_id']){
		  $vo['seller'] = get_member_msg($vo['seller_id']);
		}else{
		  $vo['seller']['realname'] = C('company_name');
		  $vo['seller']['member_name'] = C('company_name');
		}
		//网站优惠
		if($vo['discount_fee']>0){
		  $msg = array(
			 'order' => array(
			   1=>'满额优惠金额',
			   2=>'满额打折',
			   3=>'满额送倍数积分',
			   4=>'满额送优惠券',
			   5=>'满额免运费',
			 ),
		  );
		  $model = M('Prom_list');
		  $prom_data['order_id'] = $vo['id'];
		  $prom_list = $model->where($prom_data)->select();
		  foreach($prom_list as $key=>$val){
			$info = unserialize($val['info']);
		    $prom_list[$key]['name'] = $info['name'];
			$prom_list[$key]['info'] = $info['info'];
			$prom_list[$key]['award_type'] = $msg[$val['prom_type']][$info['award_type']];
			$prom_list[$key]['award_value'] = $info['award_value'];
		  }
		  //dump($prom_list);exit;
		  $this->assign('prom_list',$prom_list);
		}
		//优惠券
		if($vo['coupons_fee']>0){
		  $model = M('Member_coupon');
		  $mc_data['order_id'] = $vo['id'];
		  $coupon_list = $model->where($mc_data)->select();
		  foreach($coupon_list as $key=>$val){
			$info = unserialize($val['coupon_info']);
		    $coupon_list[$key]['title'] = $info['title'];
			$coupon_list[$key]['info'] = $info['info'];
			$coupon_list[$key]['award_value'] = $info['award_value'];
		  }
		  $this->assign('coupon_list',$coupon_list);
		}
		$d_data['order_id'] = $vo['id'];
		$items = $this->oddb->where($d_data)->select();
		$this->assign('items',$items);
		//物流公司
		$model = M('Shipping');
		$s_data['status'] = 1;
		$shippings = $model->where($s_data)->select();
		$this->assign('shippings',$shippings);
		//订单物流
		$model = M('Order_delivery');
		$os_data['type'] = 1;
		$os_data['order_id'] = $vo['order_id'];
		$shipping = $model->where($os_data)->find();
		$this->assign('shipping',$shipping);
		$this->assign ( 'vo', $vo );
		$this->display();
	  }	
  }

  /**
   * 完成支付
   */
  function pay_complete($vo){
	switch ($vo['type']) {
		case 1://商城
			return $this->order_do($vo);
			break;
		case 2://云够
			return $this->order_do($vo);
			break;
		case 3://兑换
			//return $this->buyback($vo);
			break;
		case 4:
			return $this->order_do_project($vo);
			break;
	}
  }

  /**
   * 竞拍商城订单处理
   */
  function order_do($vo){
	$time = time();
	$model = M('order');
	$odmodel = M('order_detail');
	$rmodel = M('record');
	$wallet_model = M('member_wallet');
	$wlmodel = M('member_wallet_log');
	if($vo['status']==1){
	  //echo "success";exit;
	  return false;
	}
	//订单状态修改
	$wdata['id'] = $vo['id'];
	$sdata['status'] = 1;
	$sdata['payment_mode'] = 1;
	$sdata['payment_company'] = '支付宝';
	$sdata['pay_order_id'] = $_POST['pay_order_id'];
	$sdata['pay_time'] = $time;
	$result = $model->where($wdata)->save($sdata);
	unset($rdata);
	  //买家账户记录
	  $rdata['member_id'] = $vo['member_id'];
	  $wallet = $wallet_model->where($rdata)->find();
	  $rdata['pay_type'] = 2;
	  $rdata['member_name'] = $vo['member_name'];
	  $rdata['realname'] = $vo['realname'];
	  $rdata['payment_mode'] = $_POST['payment_mode'];
	  $rdata['payment_company'] = $_POST['payment_company'];
	  $rdata['order_id'] = $vo['order_id'];
	  $rdata['pay_order_id'] = $_POST['pay_order_id'];
	  $rdata['buyer'] = $vo['member_name'];
	  $rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
	  $rdata['content'] = '购买产品';
	  //$vo['other_pay']
	  if($vo['bond']){
		$rdata['content'] .= '共支付:'.$vo['total_price'].';其他支付'.$vo['total_price']-$vo['bond'].';保证金支付'.$vo['bond'];
	  }else{
		$rdata['content'] .= '共支付:'.$vo['total_price'];
	  }
	  $rdata['pay'] = $vo['total_price'];
	  $rdata['create_time'] = $time;
	  $rdata['status'] = 1;
	  $rdata['pay_time'] = $time;
	  $rid = $rmodel->add($rdata);
	  //dump($result);dump($rid);exit;
	  //拍卖,扣除买家保证金
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
		  if($result)$result=$wlmodel->add($wl_data);
	  }
	  if($vo['other_pay']){
		  $wallet_data2['member_id'] = $vo['member_id'];
		  if($result)$result=$wallet_model->where($wallet_data2)->setDec('frozen',$vo['other_pay']);
		  //记录变化
		  $wl_data['title'] = '扣除冻结资金并完成支付，订单号：'.$vo['order_id'];
		  $wl_data['member_id'] = $vo['member_id'];
		  $wl_data['oid'] = $vo['id'];
		  $wl_data['pay_type'] = 3;
		  $wl_data['pay'] = $vo['bond'];
		  $wl_data['content'] = '扣除冻结资金并完成支付，订单号：<a href="'.C('MEMBER_SITE_URL').'/index.php/Orders/index/order_id/'.$vo['order_id'].'">'.$vo['order_id'].'</a>;扣除冻结资金:'.$vo['bond'];
		  $wl_data['create_time'] = time();
		  if($result)$result=$wlmodel->add($wl_data);
	  }
	  if($vo['type']==1){
		//产品减库存
		$od_data['oid'] = $vo['id'];
		$list = $odmodel->field('sourceid,num')->where($od_data)->select();
		$od_sdata['trade_status'] = 1;
		if($result)$result=$odmodel->where($od_data)->save($od_sdata);
		$goods_model = M('goods');
		foreach($list as $val){
		  $goods_data['id'] = $val['sourceid'];
		  //$this->stock_minus($val['sourceid'],$val['num']);
		  $goods_model->where($goods_data)->setDec('inventory',$val['num']);
		}
	  }else{
	    $result = $this->order_do_yg($vo);
	  }
	  //dump($result);dump($rid);exit;
	  if($result && $rid){
		//$model->commit();
		return true;
		//支付成功
	  }else{
		//$model->rollback();
		return false;
	  }
  
  }

  /**
   * 云够
   */
  public function order_do_yg($vo){
	//获得云码
	$codes = pay_get_shop_codes($s_num,$vo['sourceid']);
	if($codes['code_len']<$s_num){
	  $s_num = $codes['code_len'];
	}
	if($codes==false || $codes['code_len']==0){
	  $this->error('下单失败');exit;
	}
	$model = M('shoplist');
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
	return $result;
  }

  /**
   * 导出数据
   */
  public function excelDown(){
	$p = $_GET['p'] ? $_GET['p'] : 1;
	$model = D('order');
	$firstRow = ($p-1)*20;
	$oder = $model->field('id,order_id,product_name,count_detail,member_id,create_time,name')->limit($firstRow.',20')->select();
	if(!$oder){
	  $this->error('无数据!');
	}
	/*
	foreach($oder as $key=>$val){
	  $arr['data'][$key][] = $val['id'];
	  $arr['data'][$key][] = $val['order_id'];
	  $arr['data'][$key][] = $val['product_name'];
	  $arr['data'][$key][] = $val['count_detail'];
	  $arr['data'][$key][] = $val['member_id'];
	  $arr['data'][$key][] = date('Y-m-d',$val['create_time']);
	  $arr['data'][$key][] = $val['name'];
	}
	*/
	$fields[] = '编号';
	$fields[] = '订单号';
	$fields[] = '下单商品';
	$fields[] = '数量';
	$fields[] = '总价';
	$fields[] = '会员名称';
	$fields[] = '创建时间';
	excelDown($oder,$fields,'订单数据');
  }

  /**
   * 详情查看
   */
  public function look(){
    $model = M('order');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if($vo['seller_id']){
		$model = M('Member');
		$mdata['id'] = $vo['seller_id'];
		$user_msg = $model->where($mdata)->find();
		$this->assign('user_msg',$user_msg);	
	}
	$goods = '<a href="__APP__/Shoplist/edit/id/{$detail.sourceid}">{$detail.name}</a>&nbsp;&nbsp;';
	if($vo['source']=='Shoplist'){
	  $model = M('shoplist');
	  $shop_data['id'] = $vo['sourceid'];
	  $shop = $model->field('id,name,qishu,q_user_code')->where($shop_data)->find();
	  //$shop = array_pop(unserialize($vo['goods']));
	  $model = M('member_go_record');
	  $data['member_id'] = $vo['member_id'];
	  $data['shopid'] = $vo['sourceid'];
	  $r_vo = $model->where($data)->find();
	  $goods = '<a href="__APP__/Shoplist/edit/id/'.$shop['id'].'">'.$shop['name'].'&nbsp;第('.$shop['qishu'].')期</a>&nbsp;&nbsp;<br>';
	  for($i=0; $i<strlen($r_vo['goucode']); $i++){
		 if($i!=0 && $i%9==0){
		   $goods.= '&nbsp;';
		 }
		 if($i!=0 && $i%45==0){
		   $goods.= '<br/>';
		 }
		 $goods.= $r_vo['goucode'][$i];
	  }
	  if($shop['q_user_code']){
	    $goods = str_ireplace($shop['q_user_code'],'<font color="red">'.$shop['q_user_code'].'</font>',$goods);
	  }
	  //$goods = $r_vo['goucode'];
	  //dump($r_vo['goucode']);exit;
	}else{
	  $d_data['oid'] = $vo['id'];
	  $details = $this->oddb->where($d_data)->select();
	  foreach($details as $val){
	    $goods .= '<a href="__APP__/Goods/edit/id/'.$val['sourceid'].'">'.$val['product_name'].'</a>&nbsp;&nbsp;';
	  }
	  $this->assign('details',$details);
	}
	$this->assign('goods',$goods);
	$this->assign('vo',$vo);
	$this->display();
  }

  /**
   * 价格修改
   */
  public function edit_price(){
    $wdata['order_sn'] = $_POST['order_sn'];
	$sdata['actual_paid'] = $_POST['actual_paid'];
	$result = $this->db->where($wdata)->save($sdata);
	if($result){
	  $msg['action'] = '价格修改';
	  $msg['msg'] = '从「'.$_POST['oldPrice'].'」修改到「'.$_POST['actual_paid'].'」';
	  $this->log_track($vo['order_sn'],$msg);
	  ajaxSucReturn('修改成功');
	}else{
	  ajaxErrReturn('修改失败');
	}
  }

  /**
   * 确认订单
   */
  public function confirm_order(){
	$wdata['id'] = $_POST['id'];
	$vo = $this->db->field('id,order_sn,payment_mode,delivery_status,pay_status,status')->where($wdata)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($model->get_error_msg());
	}
	$sdata[$_POST['field']] = $_POST['val'];
	$result = $this->db->where($wdata)->save($sdata);
	if($result){
	  $this->log_track($vo['order_sn'],'确认订单');
	  $msg['callback'] = '<input type="button" value=" 发 货 "  onclick="deliver('.$_POST['id'].');" class="button">';
	  $msg['notice'] = '修改成功';
	  ajaxSucReturn($msg);
	}else{
	  ajaxErrReturn('修改失败');
	}
  }

  /**
   * 确认付款
   */
  public function payment(){
     
	$wdata['id'] = $_POST['id'];
	$vo = $this->db->field('id,order_sn,payment_mode,delivery_status,pay_status,status')->where($wdata)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	} 
	$sdata[$_POST['field']] = $_POST['val'];
	$result = $this->db->where($wdata)->save($sdata);
	if($result){
	  $od_wdata['order_id'] = $_POST['id'];
	  $od_sdata['status'] = 1;
	  $this->oddb->where($od_wdata)->save($od_sdata);
	  $this->log_track($vo['order_sn'],'确认付款');
      if($vo['payment_mode']==2){
          $msg['callback'] = '<strong>交易完成</strong>';
          $msg['notice'] = '确认付款';      
      }else{
          $msg['callback'] = '<input type="button" value=" 发 货 "  onclick="deliver('.$vo['id'].');" class="button">';
          $msg['notice'] = '确认付款';       
      }
	  ajaxSucReturn($msg);
	}else{
	  //ajaxErrReturn($model->getlastsql());
	  ajaxErrReturn('修改失败');
	}
  }

  /**
   * 确认收货
   */
  public function receipt(){
	$wdata['id'] = $_POST['id'];
	$vo = $this->db->field('id,order_sn,payment_mode,delivery_status,pay_status,status')->where($wdata)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$sdata['delivery_status'] = 2;
	$sdata['pay_status'] = 1;
	$sdata['status'] = 2;
	$result = $this->db->where($wdata)->save($sdata);
	if($result){
	  $od_wdata['order_id'] = $_POST['id'];
	  $od_sdata['status'] = 2;
	  $this->oddb->where($od_wdata)->save($od_sdata);
	  $this->log_track($vo['order_sn'],'订单完成');
	  $msg['callback'] = '<strong>交易完成</strong>';
	  $msg['notice'] = '交易完成';
	  ajaxSucReturn($msg);
	}else{
	  //ajaxErrReturn($model->getlastsql());
	  ajaxErrReturn('交易失败');
	}
  }

  /**
   * 退款申请
   */
  public function apply(){
	$id = $_POST['id'];
	$data2['id'] = $data['a.id']= $id;
	$vo = $this->db->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->field('a.id,a.product_name,a.spec,a.num,a.order_id,a.status,a.refund_status,b.payment_mode,b.delivery_status,b.order_sn')->where($data)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$sdata['refund_status'] = 1;
	$sdata['refund_time'] = time();
	$result = $this->oddb->where($data2)->save($sdata);
	if($result){
	  $log_msg['action'] = '申请退款';
	  $log_msg['msg'] = '货品:'.$vo['product_name'].' 规格:'.$vo['spec'].' 数量:'.$vo['num'];
	  $this->log_track($vo['order_sn'],$log_msg);
	  $owdata['id'] = $vo['order_id'];
	  $osdata['is_refund'] = 1;
	  $this->db->where($owdata)->save($osdata);
	  $msg['obj_id'] = 'item_'.$id;
	  //未发货
	  if($vo['delivery_status']==0){
		$msg['callback'] = '<strong>申请退款</strong>
		<a href="javascript:;" onclick="order_update(\'nd_agree\',\'refund_status\',4,'.$id.');"><font class="red">同意退款(未发货)</font></a>';	  
	  }else{
	  //已发货
	    $msg['callback'] = '<strong>申请退款</strong>
		<a href="javascript:;" onclick="order_update(\'yd_agree\',\'refund_status\',2,'.$id.');"><font class="red">同意退款(已发货)</font></a>';
	  }
	  $msg['notice'] = '申请成功';
	  ajaxSucReturn($msg);
	}else{
	  ajaxErrReturn('申请失败');
	}
  }

  /**
   * 未发货同意退款
   */
  function nd_agree(){
	$id = $_POST['id'];
	$data['a.id']= $id;
	$vo = $this->db->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->field('a.id,a.product_name,a.num,a.order_id,a.status,a.refund_status,b.payment_mode,b.delivery_status,b.order_sn')->where($data)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$this->db->startTrans();//启用事务
	if($vo['payment_mode']==2){
	  /* [到付 && 未发货] */
	  ajaxErrReturn('请关闭订单重新购买');
	}else{
	  /* [已支付 && 未发货] */
	  $sdata['refund_status'] = 2;
	}
	$wdata['id'] = $id;
	$sdata['refund_time'] = time();
	$result = $this->oddb->where($wdata)->save($sdata);
	//ajaxErrReturn($this->oddb->getlastsql());
	if($result){
	  $this->log_track($vo['order_sn'],'同意退款');
	  if($vo['payment_mode']==2){
  
	  }else{

	  }
	  $msg['notice'] = '申请成功';
	  $this->db->commit();
	  ajaxSucReturn($msg);
	}else{
	  $this->db->rollback();
	  ajaxErrReturn('申请失败');
	}  
  }

  /**
   * 已发货同意退款
   */
  function yd_agree(){
	$id = $_POST['id'];
	$data2['id'] = $data['a.id']= $id;
	$vo = $this->db->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->field('a.id,a.product_name,a.num,a.order_id,a.status,a.refund_status,b.payment_mode,b.delivery_status,b.order_sn')->where($data)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$sdata['refund_status'] = 2;
	$result = $this->oddb->where($data2)->save($sdata);
	if($result){
	  $this->log_track($vo['order_sn'],'同意退款');
	  $msg['notice'] = '操作成功';
	  ajaxSucReturn($msg);
	}else{
	  ajaxErrReturn('操作失败');
	}    
  }

  /**
   * 确认退货
   */
  public function return_goods(){
	$id = $_POST['id'];
	$data2['id'] = $data['a.id']= $id;
	$vo = $this->db->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->field('a.id,a.product_name,a.num,a.order_id,a.status,a.refund_status,b.payment_mode,b.delivery_status,b.order_sn')->where($data)->find();
	$result = $this->db->validate($vo);
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$sdata['refund_status'] = 4;
	$result = $this->oddb->where($data2)->save($sdata);
	if($result){
	  $this->log_track($vo['order_sn'],'退货完成，等待退款');
	  $msg['obj_id'] = 'item_'.$id;
	  $msg['callback'] = '<strong>退货完成，等待退款</strong>
		<a href="javascript:;" onclick="order_update(\'return_refund\',\'refund_status\',5,'.$id.');"><font class="red">确认退款</font></a>';
	  $msg['notice'] = '操作成功';
	  ajaxSucReturn($msg);
	}else{
	  ajaxErrReturn('操作失败');
	}    
  
  }

  /**
   * 确认退款
   */
  public function return_refund(){
	$id = $_POST['id'];
	$data2['id'] = $data['a.id']= $id;
	$vo = $this->db->table('`'.C('DB_PREFIX').'order_detail` as a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->field('a.id,a.product_name,a.price,a.num,a.order_id,a.status,a.refund_status,b.payment_mode,payment_company,b.delivery_status,b.order_sn,b.member_id,b.member_name')->where($data)->find();
	$result = $this->db->validate($vo);
	$this->db->startTrans();//启用事务
	if(!$result){
	  ajaxErrReturn($this->db->get_error_msg());
	}
	$sdata['refund_status'] = 5;
	$result = $this->oddb->where($data2)->save($sdata);
	if($result){
	  //记录退款金额
	  $o_data['id'] = $vo['order_id'];
	  //$refund_fee = $vo['price']*$vo['num'];
	  $refund_fee = $this->refund_fee($vo);
	  $result = $this->db->where($o_data)->setDec('actual_refund',$refund_fee);
	  //退款信息记录
	  $model = M('Member_wallet');
	  $mw_wdata['member_id'] = $vo['member_id'];
	  $wallet = $model->where($mw_wdata)->find();
	  $r_data['member_id'] = $vo['member_id'];
	  $r_data['member_name'] = $vo['member_name'];
	  $r_data['payment_mode'] = $_POST['payment_mode'];
	  $r_data['payment_company'] = $_POST['payment_company'];
	  $r_data['order_sn'] = $vo['order_sn'];
	  $r_data['pay_order_sn'] = $_POST['pay_order_sn'] ? $_POST['pay_order_sn'] : build_order_no($vo['member_id']);
	  $r_data['pay_type'] = 5;
	  $r_data['amount'] = $refund_fee;
	  if($vo['payment_company']=='余额支付'){
	    //退款至账户
		$mw_sdata['balance'] = $wallet['balance']+$refund_fee;
		$result = $model->where($mw_wdata)->save($mw_sdata);
		$balance = $wallet['balance']+$refund_fee;
	  }else{
	    $balance = $wallet['balance'];
	  }
	  $r_data['balance'] = $balance;
	  $r_data['ip'] = _get_ip();
	  $r_data['content'] = '退款 商品'.$vo['product_name'].'*'.$vo['num'].'退款';
	  $r_data['create_time'] = time();
	  $r_data['pay_time'] = time();
	  $r_data['status'] = 1;
	  if($result)$result = $this->rdb->add($r_data);
	  if($result){
		$this->db->commit();
	  }else{
	    $this->db->rollback();
		ajaxErrReturn('操作失败');
	  } 
	  //记录订单操作日志
	  $this->log_track($vo['order_sn'],'确认退款');
	  $msg['obj_id'] = 'item_'.$id;
	  $msg['callback'] = '<strong><font color="blue">完成退款</font></strong>';
	  $msg['notice'] = '完成退款';
	  ajaxSucReturn($msg);
	}else{
	  $this->db->rollback();
	  ajaxErrReturn('操作失败');
	}    
  }

  /**
   * 退款金额计算
   */
  protected function refund_fee(){
	$refund_fee = $total_fee = $vo['price']*$vo['num'];
	//减去优惠券
	if($vo['coupons_fee']>0){
	  $refund_fee = $total_fee-$vo['coupons_fee'];
	}
	return $refund_fee;
  }

  /**
   * 物流处理
   */
  public function deliver(){
	  $this->assign('jumpUrl',__CONTROLLER__.'/deliver/id/'.$_REQUEST['id']);
	  if(IS_POST){	
		$wdata['id'] = $_POST['id'];
		$vo = $this->db->field('id,payment_mode,order_sn,type,total_fee,bond,member_id,member_name,seller_id,title,delivery_status,pay_status,status')->where($wdata)->find();
		//dump($vo);echo $this->db->getlastsql();exit;
		$result = $this->db->validate($vo);
		if(!$result){
		  //ajaxErrReturn($model->get_error_msg());
		  $this->assign('error',$this->db->get_error_msg());
		  $this->display('Public:error2');
		  exit;
		}
		//物流处理
		if($_POST['shipping_no']){
			$wl = ShippingController::get_shipping($_POST['shipping_id']);
			$os_model = M('Order_delivery');
			$os_data['type'] = 1;
			$os_data['order_sn'] = $vo['order_sn'];
			$os_data['shipping_id'] = $_POST['shipping_id'];
			$os_data['shipping_company'] = $wl['name'];
			$os_data['shipping_code'] = $wl['code'];
			$os_data['shipping_no'] = $_POST['shipping_no'];
			$os_data['order_id'] = $vo['order_id'];
			$os_data['create_time'] = time();
			$delivery_id = $os_model->add($os_data);
			//echo $os_model->getlastsql();exit;
		}else{
		  $this->assign('error','物流单号必须');
		  $this->display('Public:error2');
		}

		if (false === $this->db->create ()) {
		  $this->assign('error',$this->db->getError ());
		  $this->display('Public:error2');
		}
		// 更新数据
		$wdata['id'] = $_POST['id'];
		$sdata['delivery_status'] = $_POST['delivery_status'];
		$sdata['delivery_id'] = $delivery_id;
		$sdata['delivery_time'] = time();
		$result = $this->db->where($wdata)->save($sdata);
		if (false !== $list) {
		  //成功提示
		  $this->history($_POST['id']);
		  //$this->assign('jumpUrl',__CONTROLLER__.'/deliver/id/'.$_REQUEST['id']);
		  $this->assign('jumpUrl',__CONTROLLER__.'/deliver/suc/1/id/'.$_POST['id']);
		  $message = "发货完成<script>function returnHomepage(){var origin = artDialog.open.origin;var dom = origin.document.getElementById('td_".$vo['id']."');dom.innerHTML = '<input type=\"button\" value=\"确认收货\"  onclick=\'order_update(\"receipt\",\"status\",2,".$vo['id'].");\' class=\"button\">';setTimeout(\"art.dialog.close()\", 500 );}returnHomepage();</script>";
		  //$message = '发货完成';
		  $msg['action'] = '确认发货';
		  $msg['msg'] = $wl['name'].' 物流号:'.$_POST['shipping_no'];
		  $this->log_track($vo['order_sn'],$msg);
		  $this->assign('message',$message);
		  $this->display('Public:success2');
		  //$this->success ('编辑成功!');
		} else {
		  $model->rollback();
		  //错误提示
		  $this->assign('error','发货失败');
		  $this->display('Public:error2');
		}
	  }else{
		$id = $_REQUEST [$this->db->getPk ()];
		$vo = $this->db->getById ( $id );
		if($vo['user_id']){
		  $vo['user'] = get_member_msg($vo['user_id']);
		}else{
		  $vo['user']['realname'] = C('company_name');
		  $vo['user']['member_name'] = C('company_name');
		}
		//订单产品
		$d_data['order_id'] = $vo['id'];
		$details = $this->oddb->where($d_data)->select();
		//echo $model->getlastsql();
		$this->assign('details',$details);
		//物流公司
		$model = M('Shipping');
		$s_data['status'] = 1;
		$shippings = $model->where($s_data)->select();
		$this->assign('shippings',$shippings);
		//订单物流
		$model = M('Order_delivery');
		$os_data['type'] = 1;
		$os_data['order_id'] = $vo['order_id'];
		$shipping = $model->where($os_data)->find();
		$this->assign('shipping',$shipping);
		$this->assign ( 'vo', $vo );
		$callback = '<input type="button" value="确认收货" onclick=\'order_update("receipt","status",2,'.$_POST['id'].')\'; class="button">';
		$this->assign('callback',$callback);
		$this->display();
	  }	
  }

  /**
   * 订单操作日志
   */
  protected function log_track($order_sn,$msg){
	//添加
	if(!is_array($msg)){
	  $data['action'] = $msg;
	}else{
	  $data = $msg;
	}
	$data['order_sn'] = $order_sn;
	$data['ip'] = _get_ip();
	$data['create_time'] = time();
	$data['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
	$data['user_name'] = $_SESSION['nickname'];
	$this->otdb->add($data);
	//echo $this->otdb->getlastsql();exit;
  }


  /**
   * 打印快递信息
   */
  public function print_order($order_id = 0) {
	if ((int)$order_id < 1 ) $this->error('您的订单号有误');
	$vo = $this->db->field('a.*,b.shipping_company,b.shipping_no')->table('`'.C('DB_PREFIX').'order` as a')->join('`'.C('DB_PREFIX').'order_delivery` as b on a.delivery_id=b.id')->where(array('a.id'=>$order_id))->find();
	$this->assign('vo',$vo);
	//详细清单
	$d_data['order_id'] = $vo['id'];
	$items = $this->oddb->where($d_data)->select();
	$this->assign('items',$items);
	$this->assign('head_title','打印订单');
	$this->display();
  }

}

?>
