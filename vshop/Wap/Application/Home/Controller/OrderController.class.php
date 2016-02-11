<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends CommonController {

  /**
   * 列表页
   */
  public function index(){
	$model = D('Order');
	$od_model = D('OrderDetail');
	$where = $this->_search();//获得查询条件
	//dump($where);
	if(isset($_GET['_order'])) {
		$order = $_GET['_order'];
	}else {
		$order = !empty($sortBy)? $sortBy: $model->getPk();
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if(isset($_GET['_sort'])) {
		$sort = $_GET['_sort']?'asc':'desc';
	}else {
		$sort = $asc?'asc':'desc';
	}
	if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	}else{
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 10;
	}
	$count = $model->where($where)->count();
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if($count>0){
	  import("@.ORG.Util.Page");
	  //创建分页对象
	  //$listRows = 1;
	  $p = new Page($count,$listRows);
	  $list = $model->field('id,order_sn,payment_mode,actual_paid,create_time,is_rate,pay_status,delivery_status,status')->where($where)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->select();
	  foreach($list as $val){
	    $order_ids[] = $val['id'];
		$id = $val['id'];
		if($val['status']==1){
		  if($val['pay_status']==0){
		    $val['status_name'] = '待支付';
			$val['state'] = 0;
		  }else{
		    if($val['delivery_status']==0){
			  $val['status_name'] = '待发货';
			  $val['state'] = 1;
			}else{
			  $val['status_name'] = '已发货';
			  $val['state'] = 2;
			}
		  }
		}else if($val['status']==2){
		  $val['status_name'] = '完成交易';
		  if($val['is_rate']==0){
		    $val['status_name'] = '待评价';
		  }
		  $val['state'] = 3;
		}else if($val['status']==-1){
		  $val['status_name'] = '订单已关闭';
		}else{
		  $val['status_name'] = '订单未确认';
		}
		$list2[$id] = $val;
	  }
	  //dump($list2);exit;
	  $od_data['order_id'] = array('in',$order_ids);
	  $items = $od_model->field('id,order_id,order_sn,product_name,spec,lit_pic,price,num,refund_status,status')->where($od_data)->order('id desc')->select();
	  foreach($items as $val){
		$id = $val['order_id'];
		//组装详细单号
	    $list2[$id]['items'][] = $val;
		$list2[$id]['item_count']++;
	  }
	  //分页显示
	  $page       = $p->Show();
	}
	//dump($list2);exit;
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	$this->assign('list',$list2);
	$this->assign('sort',$sort);
	$this->assign('order',$order);
	$this->assign("page",$page);
	$this->assign('headerTitle','订单列表页');
	$this->assign('headerKeywords','订单列表页');
	$this->assign('headerDescription','订单列表页');
	$this->assign('wx_title','订单列表页');
	$this->assign('wx_desc',C('wx_desc'));
    $this->display();
  }

  /**
    +----------------------------------------------------------
	* 根据表单生成查询条件
	* 进行列表过滤
    +----------------------------------------------------------
	* @access protected
    +----------------------------------------------------------
	* @param string $name 数据对象名称
    +----------------------------------------------------------
	* @return HashMap
    +----------------------------------------------------------
	* @throws ThinkExecption
    +----------------------------------------------------------
	*/
  protected function _search(){
    $data = array();
	$data['member_id'] = $this->user['id'];
	$data['is_display'] = 1;
	if($_GET['status']){
	  switch ($_GET['status']) {
		case 1:
			$data['pay_status'] = 0;
			break;
		case 2:
			$data['pay_status'] = 1;
			$data['delivery_status'] = 0;
			break;
		case 3:
			$data['pay_status'] = 1;
			$data['delivery_status'] = 1;
			$data['status'] = 1;
			break;
		case 4:
			$data['is_rate'] = 0;
			$data['status'] = 2;
			break;
		case 5:
			$data['is_refund'] = 1;
			//$data['status'] = 2;
			break;
		default:
			$data['pay_status'] = 0;
	  }
	  $this->assign('status',$_GET['status']);
	}
	if($_GET['order_sn']){
	  $data['order_sn'] = $_GET['order_sn'];
	  $this->assign('order_sn',$_GET['order_sn']);
	}
	return $data;
  }

  /**
   * 订单确认页
   */
  public function confirm(){
	//地址
	$model = M('memberAddress');
	$addr_data['member_id'] = $this->user['id'];
	if($_GET['consignee_id']){
	  $addr_data['id'] = $_GET['consignee_id'];
	}else{
	  $addr_data['default'] = 1;
	}
	$consignee = $model->where($addr_data)->order('id desc')->find();
	//产品
	$list = $this->products();
	$vo['total_fee'] = $list['total_fee'];
	$vo['data'] = $list['data'];
	$vo['consignee'] = $consignee;
	//总价
	$total_fee = $vo['total_fee'];
	//优惠券
	$coupons  = $this->member_coupon($total_fee,$this->user['id']);
	$this->assign('coupons',$coupons);
	//促销信息
	$promotions  = get_promotion($list['data']);
	if($promotions){
	  $total_fee -= $promotions[0]['award_value'];
	}
	$this->assign('promotions',$promotions);
	//邮费
	//无地址则读取注册地址
	if(!$consignee){
	  $shipping_fee = shipping_fee($list['data'],$this->user['ct_id']);
	}else{
	  $shipping_fee = shipping_fee($list['data'],$consignee['ct_id']);
	}
	$this->assign('shipping_fee',$shipping_fee);
	$total_fee += $shipping_fee;
	$this->assign('total_fee',$total_fee);
	//发票税额
	if($this->configs['tax_rate']){
	  $this->assign('tax_rate',$this->configs['tax_rate']/100);
	}
	$this->assign('vo',$vo);
	$this->assign('headerTitle','订单确认页');
	$this->assign('headerKeywords','订单确认页');
	$this->assign('headerDescription','订单确认页');
	$this->assign('wx_title','订单确认页');
	$this->assign('wx_desc',C('wx_desc'));
	$this->display();
  }

  /**
   * 产品信息
   */
  protected function products(){
	$model = M('Product');
	$pi_model = M('ProductItem');
	$c_model = M('Cart');
	//产品
	if($_REQUEST['product_id']){
		if($result){
		    if(IS_AJAX){
			  ajaxErrReturn('功能关闭');
			}
			//错误提示
			$this->error('功能关闭');
		}
		$data['id'] = $_REQUEST['product_id'];
		$vo = $model->field('id,name,sn,lit_pic,price,product_type,seller_id,seller_name,seller_realname,stock')->where($data)->find();
		if(!$vo){
		    if(IS_AJAX){
			  ajaxErrReturn('商品不存在');
			}
			//错误提示
			$this->error('商品不存在');
		}
		$vo['num'] = $_REQUEST['num'] ? $_REQUEST['num'] : 1;
		$total_fee = $vo['total_fee'] = $vo['num']*$vo['price'];
		/*
		$result = check_goods_type_specifications($vo['product_type']);
		if($result){
		    if(IS_AJAX){
			  ajaxErrReturn('请选择此商品的规格');
			}
			//错误提示
			$this->error('请选择此商品的规格');
		}
		*/
		$vo['product_id'] = $vo['id'];
		$vo['item_id'] = 0;
		$list[] = $vo;
		$this->assign('product_id',$vo['product_id']);
		$this->assign('item_num',$vo['num']);
	}else if($_REQUEST['item_id']){
		$data['a.id'] = $_REQUEST['item_id'];
		$vo = $pi_model->alias('a')->join('`'.C('DB_PREFIX').'product` as b on a.product_id=b.id')->field('a.*,b.name,b.lit_pic,b.price,b.seller_id,seller_name,seller_realname,b.stock,b.nw,b.is_free_shipping,seller_name,seller_realname')->where($data)->find();
		//echo $model->getlastsql();
		if(!$vo){
		    if(IS_AJAX){
			  ajaxErrReturn('商品不存在！');
			}
			$this->error('商品不存在！');
		}
		$vo['num'] = $_REQUEST['num'] ? $_REQUEST['num'] : 1;
		$num = $vo['num']>$vo['stock'] ? $vo['stock'] : $vo['num'];//超过库存
		$total_fee =$vo['total_fee'] = $num*$vo['price'];
		$vo['item_id'] = $vo['id'];
		$list[] = $vo;
		$this->assign('item_id',$vo['id']);
		$this->assign('num',$vo['num']);
	}else if($_REQUEST['cart_ids']){
		$data['a.member_id'] = $this->user['id'];
		$data['a.id'] = array('in',$_REQUEST['cart_ids']);
		$list = $c_model->alias('a')->join('`'.C('DB_PREFIX').'product` as b on a.product_id=b.id')->join('`'.C('DB_PREFIX').'product_item` as c on a.item_id=c.id')->field('a.*,b.name,b.lit_pic,b.price,b.seller_id,seller_name,seller_realname,b.stock,b.nw,b.is_free_shipping,c.spec,c.price as item_price,c.stock as item_stock')->where($data)->order('id desc')->select();
		//echo $model->getlastsql();exit;
		if(!$list){
		    if(IS_AJAX){
			  ajaxErrReturn('商品不存在！');
			}
			$this->error('商品不存在！');
		}
		$total_fee = 0;
		$carts = '';
		foreach($list as $key=>$val){
			$spec = '';
			if($val['item_id']){
				$list[$key]['stock'] = $val['stock'] = $val['item_stock'];
				$price = $list[$key]['price'] = $val['item_price'];
			}else{
				$price = $val['price'];
			}
			$list[$key]['num'] = $num = $val['num']>$val['stock'] ? $val['stock'] : $val['num'];//超过库存
			$list[$key]['total_fee'] = $price*$num;
			$total_fee += $price*$num;
			unset($list[$key]['product_attr']);
			unset($list[$key]['attr_name']);
			unset($list[$key]['product_attr_value']);
			unset($list[$key]['item_price']);
			unset($list[$key]['item_stock']);
			$carts .= $val['id'].',';
		}
		$this->assign('cart_ids',trim($carts,','));
	}else{
		if(IS_AJAX){
		  ajaxErrReturn('商品不存在！');
		}
		$this->error('商品不存在！');
	}
	$msg['data'] = $list;
	$msg['total_fee'] = $total_fee;
	return $msg;
  }

  /**
   * 优惠券信息
   */
  protected function member_coupon($total_fee,$member_id){
	  $model = M('MemberCoupon');
	  $data['a.member_id'] = $member_id;
	  $data['a.deadline'] = array('gt',time());
	  $data['a.status'] = 0;
	  $data['b.status'] = 1;
	  $data['b.limt'] = array('elt',$total_fee);
	  //$data['b.btime'] = array('lt',time());
	  //$data['b.etime'] = array('gt',time());
	  $coupons = $model->alias('a')->join('`'.C('DB_PREFIX').'coupon` as b on a.coupon_id=b.id')->field('a.id,a.coupon_id,a.deadline,b.value,b.info')->where($data)->group('b.id')->where($data)->select();
	  //echo $model->getlastsql();
	  return $coupons;
  }

  /**
   * 订单生成
   */
  public function create(){
	//F('update',$_POST,'../');
	//dump($_POST);exit;
    //数据模拟
    $_REQUEST['cart_ids'] = '21,14';
    $_REQUEST['consignee_id'] = 1;
    $_POST['tax_title'] = '';
    $_POST['tax_content'] = '';

	$time = time();
	if(!$_REQUEST['cart_ids'] && (!$_REQUEST['item_id'] || !$_REQUEST['num']) && (!$_REQUEST['product_id'] || !$_REQUEST['num'])){
		ajaxErrReturn('商品不存在！');
	}
	if(!$_REQUEST['consignee_id']){
		ajaxErrReturn('收件人地址必须');		  
	}
	$logs_model = M('Logs');
	$pl_model = M('PmList');
	//收件人信息
	$admodel = M('MemberAddress');
	$ad_data['id'] = $_REQUEST['consignee_id'];
	$ad_data['member_id'] = $this->user['id'];
	$address = $admodel->where($ad_data)->find();
	if(!$address){
		ajaxErrReturn('收件人地址必须');		  
	}
	$list = $this->products();
	$total_fee = 0;
	//组装,以商家为单位
	foreach($list['data'] as $key=>$val){
	  $seller_id = $val['seller_id'];
	  $id = $val['id'];
	  //$k = array_search($id,$_POST['ids']);
	  if($val['stock']<1){
		ajaxErrReturn($val['name'].'已无库存');
	  }
	  $num = $val['num']>$val['stock'] ? $val['stock'] : $val['num'];//超过库存
	  $list2[$seller_id]['seller_name'] = $val['seller_name'];
	  $list2[$seller_id]['seller_realname'] = $val['seller_realname'];
	  $list2[$seller_id]['num'] = $num;
	  $list2[$seller_id]['total_num'] += $num;
	  $val['amount'] = $num*$val['price'];
	  //$list2[$member_id]['amount'] = $num*$val['price'];
	  $val['module'] = $val['item_id'] ? 'Product_item' : 'Product';
	  $val['num'] = $num;
	  $list2[$seller_id]['list'][] = $val;
	  $list2[$seller_id]['total_fee'] += $val['amount'];
	  $total_fee += $num*$val['price'];
	  //购买记录
	  /*
	  $logs_data['msg'] = '<span>'.name_hide($this->user['username']).'</span>下单购买作品'.$val['product_name'];
	  $logs_data['create_time'] = $time;
	  $logs_model->add($logs_data);
	  */
	}
	//优惠券
	if($_POST['coupon_user_id']){
	  $mc_model = M('MemberCoupon');
	  $data['a.id'] = array('in',explode(',',$_POST['coupon_user_id']));
	  //$data['a.id'] = $_POST['coupon_user_id'];
	  $coupons = $mc_model->alias('a')->join('`'.C('DB_PREFIX').'coupon` as b on a.coupon_id=b.id')->field('a.id,a.member_id,a.coupon_id,a.deadline,a.status,b.title,b.value,b.info,b.status as c_status,b.seller_id')->group('b.id')->where($data)->select();
	  //组装已商家为单位优惠券
	  foreach($coupons as $key=>$val){
		  $seller_id = $val['seller_id'];
		  $coupons2[$seller_id] = $val;
		  if($val['deadline']<time()){
			ajaxErrReturn($val['title'].'优惠券已过期');
		  }
		  if($val['member_id']!=$this->user['id']){
			ajaxErrReturn($val['title'].'非法优惠券');
		  }
		  if($val['status']==1){
			ajaxErrReturn($val['title'].'此优惠券已使用');
		  }
	  }
	}
	//促销
	if($_POST['sp_id']){
	  $op_model = M('OrderPromotion');
	  $op_data['id'] = array('in',explode(',',$_POST['sp_id']));
	  $proms = $op_model->where($op_data)->select();
	  //组装已商家为单位优惠券
	  foreach($proms as $key=>$val){
		  $seller_id = $val['seller_id'];
		  $proms2[$seller_id] = $val;
		  if($val['btime']>time()){
			ajaxErrReturn($val['title'].'促销活动还未开始');
		  }
		  if($val['etime']<time()){
			ajaxErrReturn($val['title'].'促销活动已结束');
		  }
	  }
	}
	//生成订单
	$model = M('Order');
	$mem_model = M('Member');
	$od_model = M('OrderDetail');
	$time = time();
	$model->startTrans();//启用事务
	$mo_sn = '';
	if(count($list2)>1){
		$mo_sn = build_order_no($this->user['id']);
	}
	foreach($list2 as $seller_id=>$v){
	  if($seller_id!=0){
		//$mdata['id'] = $seller_id;
		//$user = $mem_model->field('id,pid')->where($mdata)->find();
		//$add_order['agent_id'] = $user['pid'];//代理商
		$add_order['seller_id'] = $seller_id;
	  }else{
		$add_order['agent_id'] = 0;
	  }
	  //订单号
	  $order_sn = $add_order['order_sn'] = build_order_no($this->user['id']);
	  if($mo_sn)$add_order['mo_sn'] = $mo_sn;
	  //商品总价
	  $add_order['total_fee'] = $v['total_fee'];
	  //邮费处理
	  $add_order['shipping_fee'] = shipping_fee($v['list'],$address);
	  $add_order['type'] = 1;
	  $add_order['title'] = '购买商品';
	  //优惠券处理
	  if($_POST['coupon_user_id'] && $coupons2[$seller_id]){
	    $add_order['coupons_fee'] = $coupons2[$seller_id]['value'];
	  }else{
	    $add_order['coupons_fee'] = 0;
	  }
	  //促销价格
	  if($_POST['sp_id'] && $proms2[$seller_id]){
		$options['prom_id'] = $proms2[$seller_id]['id'];
		$prom = get_promotion($v['list'],$options);
		if($prom){
		  $add_order['discount_fee'] = $prom[0]['award_value'];
		}
	  }else{
	    $add_order['discount_fee'] = 0;
	  }
	  //实付价格 = 总价 - 优惠券价 - 促销价 + 快递费
	  $add_order['actual_paid'] = $add_order['total_fee']-$add_order['coupons_fee']-$add_order['discount_fee']+$add_order['shipping_fee'];
	  //发票税额
	  if($this->configs['tax_rate'] && $_POST['tax']){
		$add_order['tax_fee'] = $add_order['actual_paid']*$this->configs['tax_rate']/100;
	  }else{
	    $add_order['tax_fee'] = 0;
	  }
	  $add_order['tax_title'] = $_POST['tax_title'];
	  $add_order['tax_content'] = $_POST['tax_content'];
	  //总价 + 发票税
	  $add_order['actual_paid'] = $add_order['actual_paid']+$add_order['tax_fee'];
	  $add_order['bond'] = 0;
	  $add_order['total_num'] = $v['total_num'];
	  $add_order['member_id'] = $this->user['id'];
	  $add_order['member_name'] = $this->user['username'];
	  //$add_order['realname'] = $this->user['realname'];
	  $add_order['seller_id'] = $seller_id;
	  $add_order['recipient'] = $address['name'];
	  $add_order['pv_id'] = $address['pv_id'];
	  $add_order['ct_id'] = $address['ct_id'];
	  $add_order['dist_id'] = $address['dist_id'];
	  $add_order['address'] = $address['province'].$address['city'].$address['district'].$address['addr'];
	  $add_order['zip_code'] = $address['zip_code'];
	  $add_order['mobile'] = $address['mobile'];
	  $add_order['memo'] = $_REQUEST['memo'] ? $_REQUEST['memo'] : '';
	  $add_order['ip'] = $_SERVER['REMOTE_ADDR'];
	  $add_order['create_time'] = $time;
	  $add_order['order_time'] = $this->configs['order_expired']!=0 ? $time+3600*$this->configs['order_expired'] : 0;
	  $add_order['remark'] = $_POST['remark'.$mid] ? $_POST['remark'.$mid] : '';
	  $oid = $model->add($add_order);
	  //if(!$out_trade_no)$out_trade_no=$oid;
	  if($oid){
		$result = true;
	    if($_POST['coupon_user_id'] && $coupons2[$seller_id]){
		  //优惠券状态修改
		  $mc_wdata['id'] = $coupons2[$seller_id]['id'];
		  $mc_sdata['status'] = 1;
		  $mc_sdata['order_id'] = $oid;
		  $mc_sdata['use_time'] = $time;
		  $result = $mc_model->where($mc_wdata)->save($mc_sdata);
	    }
		if(!$result){
			$result = $model->rollback();
			ajaxErrReturn('优惠券无法使用');
			exit;
		}
	    //促销信息记录
	    if($_POST['sp_id'] && $proms2[$seller_id]){
			$pl_data['order_id'] = $oid;
			//$pl_data['info'] = serialize($prom);
			$pl_data['pm_type'] = 'Order';
			$pl_data['pm_id'] = $proms2[$seller_id]['id'];
			$pl_data['create_time'] = $time;
			$result = $op_model->add($op_data);
	    }
		if(!$result){
			$result = $model->rollback();
			ajaxErrReturn('促销信息有误');
			exit;
		}
		//订单产品详情
		foreach($v['list'] as $goods){
		  $od_data['member_id'] = $this->user['id'];
		  $od_data['seller_id'] = $seller_id;
		  $od_data['order_id'] = $oid;
		  $od_data['order_sn'] = $order_sn;
		  $od_data['source'] = $goods['item_id'] ? 'Product_item' : 'Product';
		  $gid = $goods['id'];
		  $od_data['sourceid'] = $gid;
		  $od_data['product_id'] = $goods['product_id'];
		  $od_data['item_id'] = $goods['item_id'];
		  $od_data['product_name'] = $goods['name'];
		  $od_data['spec'] = $goods['spec'] ? $goods['spec'] : '';
		  //$od_data['product_name'] = $goods['product_name'];
		  $share_id  = $_SESSION['share'][$gid]['share_id'];
		  if($share_id){
			if($share_id==$this->user['id']){
			  $share_id = 0;//不能自己分享给自己
			}
		  }else{
			$share_id = 0;
		  }
		  $od_data['share_id'] = $share_id;
		  $od_data['lit_pic'] = $goods['lit_pic'];
		  $od_data['price'] = $goods['price'];
		  $od_data['num'] = $goods['num'];
		  $od_data['create_time'] = $time;
		  $md_id = $od_model->add($od_data);
		  //echo $od_model->getlastsql();exit;
		  if(!$md_id){
			$model->rollback();
			ajaxErrReturn('详情订单生成失败');
			exit;
		  }
		}	  
	  }else{
		$model->rollback();
		ajaxErrReturn('订单生成失败');
		exit;
	  }
	}
	//减去库存
	if($this->configs['site_inventorysetup']==1){
	  stock_update($list['data']);
	}
	$model->commit();
	$msg['error_code'] = 0;
	$msg['notice'] = '订单生成成功';
	//清除购物车数据
	if($_POST['cart_ids']){
	  //CartAction::delete();
      //$data = R('Cart/delete');
	}
	if($mo_sn){
		$msg['mo_sn'] = $mo_sn;
		$gourl = U('Order/beforpay',array('mo_sn',$mo_sn));
	}else{
		$msg['order_sn'] = $order_sn;
		$gourl = U('Order/beforpay',array('order_sn',$order_sn));
	}
	$msg['gourl'] = $gourl;
	ajaxSucReturn($msg);
  }

  /**
   *  合并订单号
   */
  public function merge(){
	$model = D('Order');
	//订单号
	$mo_sn = build_order_no($this->user['id']);
	$ids = explode(',',$_REQUEST['order_ids']);
	$wdata['member_id'] = $this->user['id'];
	$wdata['order_id'] = array('in',$ids);
	$sdata['mo_sn'] = $mo_sn;
	$result = $model->where($wdata)->save($sdata);
	if(!$result){
		ajaxErrReturn('订单生成失败');
	}
	$msg['error_code'] = 0;
	$msg['notice'] = '订单生成成功';
	$msg['out_trade_no'] = $mo_sn;
	ajaxSucReturn($msg);
  }

  /**
   *  支付前处理
   */
  public function beforepay(){
	//是否微信支付
	//dump($_GET);exit;
	if(!isWeixin()){
		if($_GET['order_sn']){
			header("Location: ".__ROOT__."/Order/pay?order_sn=".$_GET['order_sn']);
		}
		if($_GET['mo_sn']){
			header("Location: ".__ROOT__."/Order/pay?mo_sn=".$_GET['mo_sn']);
		}			
		exit;
	}
	include C('INTERFACE_PATH')."wxwappay/lib/WxPay.Api.php";
	include C('INTERFACE_PATH')."wxwappay/unit/WxPay.JsApiPay.php";
	//获取用户openid
	$tools = new JsApiPay();
	$openId = $tools->GetOpenid();
	cookie('wx_real_openid',authcode($openId,'ENCODE'));
	$this->assign('order_sn',$_GET['order_sn']);
	$this->assign('mo_sn',$_GET['mo_sn']);
  }

  /**
   *  收银台
   */
  public function pay(){
	//订单信息
	$model = M('Order');
	if($_REQUEST['order_sn']){
		$data['order_sn'] = $_REQUEST['order_sn'];
		$out_trade_no = $_REQUEST['order_sn'];
		//单一订单支付
		$notify_url = C('SITE_URL') . '/index.php/Wx_Payment/notify';
	}
	if($_REQUEST['mo_sn']){
		$data['mo_sn'] = $_REQUEST['mo_sn'];
		$out_trade_no = $_REQUEST['mo_sn'];
		//多订单合并支付
		$notify_url = C('SITE_URL') . '/index.php/Wx_Payment/notify_merge';
	}
	$data['member_id'] = $this->user['id'];
	$orders = $model->field('id,title,order_sn,total_fee,actual_paid,status')->where($data)->select();
	//echo $model->getlastsql();dump($orders);exit;
	if(!$orders){
		$this->error('订单不存在');
	}
	$total_fee = 0;
	$title = '';
	foreach($orders as $key=>$order){
		$total_fee += $order['total_fee'];
		$titles[] = $order['title'];
		if($order['status']!=1 || $order['pay_status']!=0){
			$this->error('订单：'.$order['order_id'].'状态错误');
		}
	}
	$body = implode(',',$titles);
	//支付接口请求
	/*
	require_once(C('INTERFACE_PATH')."Api/config.php");
	require_once(C('INTERFACE_PATH')."Api/Api.class.php");
	$api = new Api($config);
	$para['m'] = 'Payment';
	$para['a'] = 'wxWapPay';
	if($_REQUEST['order_sn'])$data['order_sn'] = $_REQUEST['order_sn'];
	if($_REQUEST['mo_sn'])$data['mo_sn'] = $_REQUEST['mo_sn'];
	$data['openid'] = 'oO8Umt19WO-Ci88IrRm2ywXgr9OM';
	$result = json_decode($api->get($para,$data),true);
	dump($result);exit;
	*/
	//如果是微信浏览器直接可支付
	$isWeixin = isWeixin();
	if($isWeixin){
		$openId = cookie('wx_real_openid');
		$openId = authcode($openId);
		$body = $body;
		$total_fee = $total_fee*100;
		$total_fee = (string)$total_fee;
		require_once C('INTERFACE_PATH')."wxwappay/lib/WxPay.Api.php";	
		require_once C('INTERFACE_PATH')."wxwappay/unit/WxPay.JsApiPay.php";
		$tools = new JsApiPay();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		//$input->SetGoods_tag("test");
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$payOrder = WxPayApi::unifiedOrder($input);
		$jsApiParameters = $tools->GetJsApiParameters($payOrder);
		$this->assign('jsApiParameters',$jsApiParameters);		
	}
	$this->assign('isWeixin',$isWeixin);
	$this->assign('order',$order);
	$this->assign('headerTitle','订单支付页');
	$this->assign('headerKeywords','订单支付页');
	$this->assign('headerDescription','订单支付页');
	$this->assign('wx_title','订单支付页');
	$this->assign('wx_desc',C('wx_desc'));
	$this->display();
  }

  /**
   *  支付宝支付
   */
  public function alipay(){
	if(!$_REQUEST['order_sn']  && !$_REQUEST['mo_sn']){
		$this->error('订单号必须');
	}
	$model = M('Order');
	if($_REQUEST['order_sn']){
		$data['order_sn'] = $_REQUEST['order_sn'];
		$out_trade_no = $_REQUEST['order_sn'];
		//单一订单支付
		$notify_url = C('SITE_URL') . '/index.php/Ali_Payment/notify';
		$return_url = C('SITE_URL') . '/index.php/Order/index?order_sn='.$_REQUEST['order_sn'];
		$body .= '订单支付：'.$_REQUEST['order_sn'].'';
	}
	if($_REQUEST['mo_sn']){
		$data['mo_sn'] = $_REQUEST['mo_sn'];
		$out_trade_no = $_REQUEST['mo_sn'];
		//多订单合并支付
		$notify_url = C('SITE_URL') . '/index.php/Ali_Payment/notify_merge';
		$return_url = C('SITE_URL') . '/index.php/Order/index?mo_sn='.$_REQUEST['mo_sn'];
		$body .= '订单合并支付：'.$_REQUEST['mo_sn'].'';
	}
	$data['member_id'] = $this->user['id'];
	$orders = $model->field('id,seller_id,bond,type,actual_paid,order_sn,order_time,member_id,member_name,pay_status,status')->where($data)->select();
	if(!$orders){
		$this->error('订单不存在');
	}
	$total_fee = 0;
	$title = '';
	foreach($orders as $key=>$order){
		$total_fee += $order['actual_paid'];
		$titles[] = $order['title'];
		if($order['pay_status']>0){
			$this->error($order['order_sn'].'订单状态错误');
		}
	}

	//支付接口请求
	require_once(C('INTERFACE_PATH')."Api/config.php");
	require_once(C('INTERFACE_PATH')."Api/Api.class.php");
	$api = new Api($config);
	$para['m'] = 'Payment';
	$para['a'] = 'aliWapPay';
	if($_REQUEST['order_sn'])$data['order_sn'] = $_REQUEST['order_sn'];
	if($_REQUEST['mo_sn'])$data['mo_sn'] = $_REQUEST['mo_sn'];
	//$html = $api->buildRequestForm($para,$data);
	//echo $html;exit;
	//dump($html);exit;
	$result = json_decode($api->get($para,$data),true);
	//dump($result);exit;
	if($result['error_code']==0){
	  header("Location:".$result['pay_url']);exit;
	}else{
	  $this->error($result['notice']);
	}

	/*
	//支付宝
	include C('INTERFACE_PATH')."aliwappay/lib/alipay_submit.class.php";
	//支付信息
	//$pay_configs = include C('PUBLIC_CACHE').'/config/pay.php';
	$pay_configs = getCache('Config:pay');
	$alipay_config = unserialize($pay_configs['ali']['content']);
	$alipay_config['seller_id']	= $alipay_config['seller_email'];
	$alipay_config['sign_type'] = strtoupper('RSA');
	$alipay_config['input_charset'] = strtolower('utf-8');
	$alipay_config['cacert'] = C('INTERFACE_PATH').'aliwappay/cacert.pem';
	$alipay_config['private_key_path'] = C('INTERFACE_PATH').'aliwappay/key/rsa_private_key.pem';
	$alipay_config['ali_public_key_path'] = C('INTERFACE_PATH').'aliwappay/key/alipay_public_key.pem';
	$alipay_config['transport'] = 'http';
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$payment_type = "1";
	$exter_invoke_ip = $_SERVER['REMOTE_ADDR'];
	$subject = $body;
	$parameter = array(
			"service" => "alipay.wap.create.direct.pay.by.user",
			"partner" => trim($alipay_config['partner']),
			"seller_id" => trim($alipay_config['seller_id']),
			"sign_type" => 'RSA',
			"payment_type"	=> $payment_type,
			"notify_url"	=> $notify_url,
			"return_url"	=> $return_url,
			"out_trade_no"	=> $out_trade_no,
			"subject"	=> $subject,
			"total_fee"	=> $total_fee,
			"notify_url"	=> $notify_url,
			"return_url"	=> C('WAP_URL').'/index.php/Order',
			//"show_url"	=> $show_url,
			"body"	=> $subject,
			//"it_b_pay"	=> $it_b_pay,
			//"extern_token"	=> $extern_token,
			"extra_common_param"=>'pay_type|2,payment_channel|wap',  //参数
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
	);
	$para = 'https://mapi.alipay.com/gateway.do?'.$alipaySubmit->buildRequestParaToString($parameter);
	header("Location:".$para);exit;
	//$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
	//echo $html_text;exit;
	*/
  }

  /**
   *  余额支付
   */
  public function balance_pay(){
	if(!$_POST['mo_sn'] && !$_POST['order_sn']){
	  ajaxErrReturn('订单不存在');
	}
	if($this->user['password']!=md5($_POST['password'].$this->user['salt'].$this->user['salt'][1])){
	  ajaxErrReturn('密码错误');
	}
	$model = M('Order');
	$od_model = M('OrderDetail');
	$wallet_model = M('MemberWallet');
	$time = time();
	//$trade_no = build_order_no($this->user['id']);
	$model->startTrans();//启用事务
	if($_POST['mo_sn']){
	  $data['mo_sn'] = $_POST['mo_sn'];
	}else{
	  $data['order_sn'] = $_POST['order_sn'];
	}
	$data['member_id'] = $this->user['id'];
	//$data['pay_status'] = 0;
	$list = $model->field('id,seller_id,bond,type,actual_paid,order_sn,order_time,member_id,member_name,pay_status,status')->where($data)->select();
	if(!$list){
	  ajaxErrReturn('订单不存在');
	}
	foreach($list as $vo){
	  $total_fee += $vo['actual_paid'];
	}
	if($this->user['balance']<$total_fee){
	  ajaxErrReturn('可用余额不足');
	}
	$totalprice = 0 ;
	foreach($list as $vo){
		$totalprice += $vo['actual_paid'];
		if(!$vo){
		  ajaxErrReturn($vo['order_sn'].'订单不存在');
		}
		if($vo['order_time'] && time()>$vo['order_time']){
		  ajaxErrReturn($vo['order_sn'].'订单已过期');
		}
		if($vo['type']>2){
		  ajaxErrReturn($vo['order_sn'].'订单有误');
		}
		if($vo['pay_status']>0){
		  ajaxErrReturn($vo['order_sn'].'订单已支付');
		}
		//$time = time();
		$trade_no = build_order_no($this->user['id']);
		$subject = '购买商品:';
		$body = '购买商品';
		//订单状态修改
		$wdata['id'] = $vo['id'];
		$sdata['payment_company'] = '网站余额';
		$sdata['payment_channel'] = 'wxwap';
		$sdata['pay_status'] = 1;
		$sdata['pay_order_id'] = $trade_no;
		$sdata['pay_time'] = $time;
		$result = $model->where($wdata)->save($sdata);
		if(!$result){
		  $model->rollback();
		  ajaxErrReturn('支付失败');
		}
		//修改余额
		$wallet_data['member_id'] = $vo['member_id'];
		$result = $wallet_model->where($wallet_data)->setDec('balance',$vo['actual_paid']);
		if(!$result){
		  $model->rollback();
		  ajaxErrReturn('支付失败');	
		}
		$wl_data['title'] = '完成支付，订单号：'.$vo['order_sn'];
		$content = '共支付'.$vo['actual_paid'];
		//记录买家财务账单
		unset($rdata);
		$rmodel = M('Record');
		$rdata['member_id'] = $vo['member_id'];
		$wallet = $wallet_model->where($wallet_data)->find();
		$rdata['member_name'] = $vo['member_name'];
		$rdata['realname'] = $vo['realname'];
		$rdata['order_sn'] = $vo['order_sn'];
		$rdata['pay_type'] = 2;
		$rdata['payment_mode'] = 1;
		$rdata['payment_company'] = $this->configs['company_name'];
        $rdata['payment_channel'] = '网站余额';
		$rdata['pay_order_sn'] = $trade_no;
		$rdata['buyer'] = $vo['member_name'];
		$rdata['content'] = $content;
		$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
		$rdata['amount'] = $vo['actual_paid'];
		$rdata['create_time'] = $time;
		$rdata['status'] = 1;
		$rdata['pay_time'] = time();
		if($result)$result = $rmodel->add($rdata);
		//支付完成后处理
		$result = after_pay($vo);
		if(!$result){
		  $model->rollback();
		  ajaxErrReturn('支付失败');		
		}
		$log['order_sn'] = $vo['order_sn'];
		$log['utype'] = 1;
		$log['user_id'] = $this->user['id'];
		$log['user_name'] = $this->user['username'];
		$log['msg'] = '账户余额支付';
		$log['action'] = '完成支付';
		$log['create_time'] = time();
		$log['ip'] = _get_ip();
		order_log($log);
	}
	if($result){
		$model->commit();
		$msg['notice'] = '支付成功';
		if($_REQUEST['order_id']){
		  $gourl = U('Order/paycomplet',array('order_id'=>$_REQUEST['order_id']));
		}else{
		  $gourl = U('Order/paycomplet',array('mo_sn'=>$_REQUEST['mo_sn']));
		}
		$msg['gourl'] = $gourl;
		ajaxSucReturn($msg);
	}else{
		$model->rollback();
		ajaxErrReturn('支付失败');
	}
  }

  /**
   *  支付完成页面
   */
  public function paycomplet(){
	if(!$_REQUEST['order_sn']  && !$_REQUEST['mo_sn']){
		$this->error('订单号必须');
	}
	$model = M('order');
	if($_REQUEST['order_sn']){
		$data['order_sn'] = $_REQUEST['order_sn'];
		$out_trade_no = $_REQUEST['order_sn'];
		//单一订单支付
		$notify_url = C('SITE_URL') . '/index.php/Ali_Payment/notify';
		$return_url = C('SITE_URL') . '/index.php/Order/index?order_id='.$_REQUEST['order_id'];
	}
	if($_REQUEST['mo_sn']){
		$data['mo_sn'] = $_REQUEST['mo_sn'];
		$out_trade_no = $_REQUEST['mo_sn'];
		//多订单合并支付
		$notify_url = C('SITE_URL') . '/index.php/Ali_Payment/notify_merge';
		$return_url = C('SITE_URL') . '/index.php/Order/index?mo_sn='.$_REQUEST['mo_sn'];
	}
	$data['member_id'] = $this->user['id'];
	$orders = $model->field('id,title,order_sn,actual_paid,payment_channel,pay_status,status')->where($data)->select();
	//echo $model->getlastsql();
	//dump($orders);exit;
	if(!$orders){
		$this->error('订单不存在');
	}
	$total_fee = 0;
	$title = '';
	foreach($orders as $key=>$order){
		$total_fee += $order['actual_paid'];
		$titles[] = $order['title'];
		if($order['pay_status']==0){
			$this->error($order['order_sn'].'订单支付失败');
		}
	}
	$order = $orders[0];
	if($order['payment_channel']=='balance'){
		$order['payment'] = '余额支付';
	}else if($order['payment_channel']=='wxapp'){
		$order['payment'] = '微信app支付';
	}else if($order['payment_channel']=='wxwap'){
		$order['payment'] = '微信WAP支付';
	}else if($order['payment_channel']=='aliapp'){
		$order['payment'] = '支付宝APP支付';
	}else if($order['payment_channel']=='aliwap'){
		$order['payment'] = '支付宝WAP支付';
	}else if($order['payment_channel']=='aliweb'){
		$order['payment'] = '支付宝PC端支付';
	}
	$this->assign('order',$order);
	$this->assign('headerTitle','支付完成');
	$this->assign('headerKeywords','支付完成');
	$this->assign('headerDescription','订单支付完成');
	$this->assign('wx_title','支付完成');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }

  /**
   * 申请退款
   */
  public function refund(){
	$model = M('OrderDetail');
	$sn = $_REQUEST['order_sn'];
	$ar = explode('_',$sn);
	$order_sn = $ar[0];
	$od_id = $ar[1];
	$data['a.order_sn'] = $order_sn;
	$count = $model->where($data)->count();
	$data['a.id'] = $od_id-8000;
	$vo = $model->field('a.*,b.actual_paid,b.actual_refund,b.pay_status,b.discount_fee,b.coupons_fee,b.confirm_time,b.delivery_status')->alias('a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->where($data)->find();
	if(!$vo){
	  $this->error('订单不存在');
	}
	//超过七天无理由退货
	if($vo['confirm_time'] && $vo['confirm_time']+60*60*24*7<time()){
	  if(IS_AJAX){
	    ajaxErrReturn('超过七天无理由退货');
	  }
	  $this->error('超过七天无理由退货');
	}
	//已申请退款或未支付不可申请
	if($vo['refund_status']!=0 || $vo['pay_status']!=1){
	  if(IS_AJAX){
	    ajaxErrReturn('状态错误');
	  }
	  $this->error('状态错误');
	}
	if($count==1){
	  $vo['pay_amount'] = $vo['actual_paid'];
	  $vo['pay_msg'] = '';
	}else{
	  $info = refund_pay($vo);
	  $vo['pay_amount'] = $info['pay_amount'];
	  $vo['pay_msg'] = $info['msg'];
	}
	//信息提交处理
    if(IS_POST){
	  $wdata['id'] = $vo['id'];
	  if($vo['delivery_status']>0){
		//已经发货,需退货
	    $sdata['refund_status'] = 1;
		$ot_data['action'] = '申请退款，待退货';
	  }else{
		//未发货,无需退货
	    $sdata['refund_status'] = 4;
		$ot_data['action'] = '申请退款，无需退货';
	  }
	  $sdata['refund_reason'] = $_POST['refund_reason'];
	  $sdata['refund_memo'] = $vo['pay_msg'];
	  $sdata['refund_fee'] = $vo['pay_amount'];
	  $model->startTrans();//启用事务
	  $result = $model->where($wdata)->save($sdata);
	  if(!$result){
	    $model->rollback();
		ajaxErrReturn('申请失败');
	  }
	  //订单状态修改
	  $o_model = M('Order');
	  $o_wdata['id'] = $vo['order_id'];
	  $o_wdata['is_refund'] = 1;
	  if($count==1){
		  $result = $o_model->where($o_wdata)->setInc('actual_refund',$vo['pay_amount']);
	  }else{
		  if($info['discount_fee'])$o_sdata['discount_fee'] = 0;
		  if($info['coupons_fee'])$o_sdata['coupons_fee'] = 0;
		  $o_sdata['actual_refund'] = $vo['actual_refund']+$vo['pay_amount'];
		  $result = $o_model->where($o_wdata)->save($o_sdata);
	  }
	  //echo $o_model->getlastsql();exit;
	  if(!$result){
	    $model->rollback();
		ajaxErrReturn('申请失败');
	  }
	  $model->commit();
	  //记录订单日志
	  $ot_data['order_sn'] = $vo['order_sn'];
	  $ot_data['utype'] = 2;
	  $ot_data['user_id'] = $this->user['id'];
	  $ot_data['user_name'] = $this->user['username'];
	  $ot_data['msg'] = '商品:'.$vo['product_name'].' '.$vo['spec'].' * '.$vo['num'].' 退款金额:'.$vo['pay_amount'].';'.$info['msg'];
	  //$ot_data['action'] = '申请退款';
	  $ot_data['create_time'] = time();
	  $ot_data['issystem'] = 0;
	  $ot_data['ip'] = _get_ip();
	  order_log($ot_data);
	  $msg['notice'] = '申请成功';
	  $msg['gourl'] = U('Order/index',array('order_sn'=>$vo['order_sn']));
	  ajaxSucReturn($msg);
	}
	$this->assign('vo',$vo);
	$this->assign('headerTitle','退款申请');
	$this->assign('headerKeywords','退款申请');
	$this->assign('headerDescription','退款申请');
	$this->assign('wx_title','退款申请');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }

  /**
   * 退款订单发货
   */
  public function return_item(){
	$model = M('OrderDetail');
	$sn = $_REQUEST['order_sn'];
	$ar = explode('_',$sn);
	$order_sn = $ar[0];
	$od_id = $ar[1];
	$data['a.order_sn'] = $order_sn;
	$count = $model->where($data)->count();
	$data['a.id'] = $od_id-8000;
	$vo = $model->field('a.*,b.actual_paid,b.pay_status,b.discount_fee,b.coupons_fee,b.confirm_time,create_time')->alias('a')->join('`'.C('DB_PREFIX').'order` as b on a.order_id=b.id')->where($data)->find();
	if(!$vo){
	  $this->error('订单不存在');
	}
	//已申请退款或未支付不可申请
	if($vo['refund_status']!=2){
	  if(IS_AJAX){
	    ajaxErrReturn('状态错误');
	  }
	  $this->error('状态错误');
	}
	if($count==1){
	  $vo['pay_amount'] = $vo['actual_paid'];
	  $vo['pay_msg'] = '';
	}else{
	  $info = refund_pay($vo);
	  $vo['pay_amount'] = $info['pay_amount'];
	  $vo['pay_msg'] = $info['msg'];
	}
	//快递公司
	$s_model = M('Shipping');
    if(IS_POST){
	  $d_model = M('OrderDelivery');
	  $d_data['order_sn'] = $vo['order_sn'];
	  $d_data['item_id'] = $vo['id'];
	  $d_data['item_name'] = $vo['product_name'];
	  $d_data['type'] = 2;
	  $d_data['shipping_id'] = $_POST['shipping_id'];
	  if($_POST['shipping_id']==0){
	    $d_data['shipping_company'] = $_POST['shipping_company'];
	  }else{
	    $s_data['id'] = $_POST['shipping_id'];
		$shipping = $s_model->where($s_data)->find();
		$d_data['shipping_company'] = $shipping['name'];
		$d_data['shipping_code'] = $shipping['code'];
	  }
	  $d_data['shipping_no'] = $_POST['shipping_no'];
	  $d_data['memo'] = $_POST['memo'];
	  $delivery_id = $d_model->add($d_data);
	  $wdata['id'] = $vo['id'];
	  $sdata['refund_status'] = 4;
	  $sdata['delivery_id'] = $delivery_id;
	  //$model->startTrans();//启用事务
	  $result = $model->where($wdata)->save($sdata);
	  if(!$result){
		ajaxErrReturn('提交失败');
	  }
	  //记录订单日志
	  $ot_data['order_sn'] = $vo['order_sn'];
	  $ot_data['utype'] = 2;
	  $ot_data['user_id'] = $this->user['id'];
	  $ot_data['user_name'] = $this->user['username'];
	  $ot_data['msg'] = '商品:'.$vo['product_name'].' '.$vo['spec'].' * '.$vo['num'].' 退款金额:'.$vo['pay_amount'];
	  $ot_data['action'] = '退款快递填写';
	  $ot_data['create_time'] = time();
	  $ot_data['issystem'] = 0;
	  $ot_data['ip'] = _get_ip();
	  order_log($ot_data);
	  $msg['notice'] = '提交成功';
	  $msg['gourl'] = U('Order/index',array('order_sn'=>$vo['order_sn']));
	  ajaxSucReturn($msg);
	}
	$s_data['status'] = 1;
	$shippings = $s_model->where($s_data)->select();
	$this->assign('shippings',$shippings);
	$this->assign('vo',$vo);
	$this->assign('headerTitle','退款快递填写');
	$this->assign('headerKeywords','退款快递填写');
	$this->assign('headerDescription','退款快递填写');
	$this->assign('wx_title','退款快递填写');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }

  /**
   * 确认收货
   */
  public function finish(){
	$model = M('Order');
	$od_model = M('OrderDetail');
	$wdata['member_id'] = $this->user['id'];
	$wdata['order_sn'] = $_REQUEST['order_sn'];
	$vo = $model->field('id,pay_status,delivery_status')->where($wdata)->find();
	if($vo['pay_status']!=1){
	  ajaxErrReturn('未支付');
	}
	if($vo['delivery_status']!=1){
	  ajaxErrReturn('未发货');
	}
	$model->startTrans();//启用事务
	$sdata['status'] = 2;
	$sdata['delivery_status'] = 2;
	$sdata['confirm_time'] = time();
	$result = $model->where($wdata)->save($sdata);
	if($result){
	  $od_wdata['order_id'] = $vo['id'];
	  $od_sdata['status'] = 2;
	  $result = $od_model->where($od_wdata)->save($od_sdata);
	}
	if($result){
	  $model->commit();
	  ajaxSucReturn('操作成功');
	}else{
	  $model->rollback();
	  ajaxErrReturn('操作失败');
	}  
  }

  /**
   * 评价
   */
  public function feedback(){
	if(IS_POST){
	  //ajaxSucReturn('操作成功');
	  $model = M('Feedback');
	  $or_model = M('Order');
	  $od_model = M('OrderDetail');
	  $count_data['order_id'] = $_POST['id'];
	  $count_data['is_rate'] = 0;
	  $count = $od_model->where($count_data)->count();
	  if($count==0){
	    ajaxErrReturn('订单已评价');
	  }
	  $i = 0;
	  foreach($_POST['item_id'] as $key=>$item_id){
		unset($data);
		$data['item_id'] = $item_id;
		$f_count = $model->where($data)->count();
		if($f_count>0){
		  ajaxErrReturn('商品已评价');
		}
	    $data['member_id'] = $this->user['id'];
	    $data['member_name'] = $this->user['username'];
	    $data['order_id'] = $_POST['id'];
		$data['product_id'] = $_POST['product_id'][$key];
		$data['content'] = $_POST['content'][$key] ? $_POST['content'][$key] : '好评';
		$data['grade'] = $_POST['grade'][$key];
		$data['create_time'] = time();
		$data['status'] = 1;
		$result = $model->add($data);
		if($result){
		  $wdata['id'] = $od_id;
		  $sdata['is_rate'] = 1;
		  $od_model->where($wdata)->save($sdata);
		  $i++;
		}
	  }
	  if($count==$i){
		$wdata['id'] = $_POST['id'];
		$sdata['is_rate'] = 1;
		$or_model->where($wdata)->save($sdata);
	  }
	  $msg['notice'] = '评价成功';
	  $msg['gourl'] = U('Order/index');
	  ajaxSucReturn($msg);
	}
	$model = M('Order');
	$wdata['order_sn'] = $_REQUEST['order_sn'];
	$vo = $model->field('id,order_sn,is_rate')->where($wdata)->find();
	if(!$vo){
	  $this->error('订单不存在');
	}
	if($vo['is_rate']==1){
	  //$this->error('订单已评价');
	}
	$this->assign('vo',$vo);
	$model = M('OrderDetail');
	$wdata['a.member_id'] = $this->user['id'];
	$list = $model->field('a.price,a.num,a.product_name,a.lit_pic,a.is_rate,b.grade,b.content')->alias('a')->join('`'.C('DB_PREFIX').'feedback` as b on a.id=b.item_id')->where($wdata)->select();
	$this->assign('list',$list);
	$this->assign('headerTitle','订单评价');
	$this->assign('headerKeywords','订单评价');
	$this->assign('headerDescription','订单评价');
	$this->assign('wx_title','订单评价');
	$this->assign('wx_desc',C('wx_desc'));
	$this->display();
  }

  /**
   * 关闭订单
   */
  public function cancel(){
	$model = M('Order');
	$wdata['member_id'] = $this->user['id'];
	$wdata['order_sn'] = $_REQUEST['order_sn'];
	$sdata['status'] = -1;
	$result = $model->where($wdata)->save($sdata);
	if($result){
	  ajaxSucReturn('操作成功');
	}else{
	  //ajaxErrReturn($model->getlastsql());
	  ajaxErrReturn('操作失败');
	}
  }

  /**
   * 删除订单
   */
  public function delete(){
	$model = M('Order');
	$wdata['member_id'] = $this->user['id'];
	$wdata['order_sn'] = $_REQUEST['order_sn'];
	$sdata['is_display'] = 0;
	$result = $model->where($wdata)->save($sdata);
	if($result){
	  ajaxSucReturn('操作成功');
	}else{
	  ajaxErrReturn($model->getlastsql());
	  ajaxErrReturn('操作失败');
	}
  }

}