<?php
class OrderAction extends CommonAction {

	public function _initialize(){
		parent::_initialize();
	}

	//订单列表
	public function index(){
		$model = D(MODULE_NAME.' as a');
		$where = $this->_search();//获得查询条件
		if(isset($_GET['_order'])) {
			$order = $_GET['_order'];
		}else {
			$order = !empty($sortBy)? $sortBy: $model->getPk();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		//$_GET['_sort'] = 0;
		if(isset($_GET['_sort'])) {
			$sort = $_GET['_sort']?'asc':'desc';
		}else {
			$sort = $asc?'asc':'desc';
		}
		$count = $model->where($where)->count();
		//echo $model->getlastsql();
		$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
		$page_count = ceil($count/$page_size);
		if($count>0){
		  import("@.ORG.Util.Page");
		  //创建分页对象
		  if(!empty($_GET['listRows'])) {
			$page_size  =  $_GET['listRows'];
		  }
		  $p = new Page($count,$page_size);
		  //$list = $model->field('id,title,order_id,payment_mode,total_fee,bond,site_pay,other_pay,,create_time,status')->where($where)->order( "`" . $order . "` " . $sort)->limit($p->firstRow.','.$p->listRows)->select();

		  $voList = $model->table('`'.C('DB_PREFIX').'order` as a')->join('`'.C('DB_PREFIX').'order_detail` as b on a.id=b.order_id')->join('`'.C('DB_PREFIX').'product_item` as c on b.item_id=c.id')->field('a.id,a.title,a.order_id,a.payment_mode,a.payment_company,a.payment_channel,a.total_fee,a.bond,a.site_pay,a.other_pay,a.create_time,a.shipping_status,a.status,b.product_id,b.product_name,b.item_id,b.lit_pic,b.price,b.num,b.status as item_status,c.attr_name,c.product_attr_value,c.price as item_price')->where($where)->order( "`" . $order . "` " . $sort)->limit($p->firstRow.','.$p->listRows)->select();
		}

		foreach($voList as $key=>$val){
			$spec = '';
			if($val['item_id']){
				$names = explode(';',$val['attr_name']);
				$values = explode(';',$val['product_attr_value']);
				foreach($names as $k=>$name){
					$spec .= $name.':'.$values[$k].' ';
				}
				$voList[$key]['spec'] = $spec;
				$price = $voList[$key]['price'] = $val['item_price'];
			}else{
				$price = $val['price'];
			}
			//$total_fee += $price*$val['num'];
			unset($voList[$key]['product_attr']);
			unset($voList[$key]['attr_name']);
			unset($voList[$key]['product_attr_value']);
			unset($voList[$key]['item_price']);
			$oid = $val['order_id'];
			$lists[$oid]['order_id'] = $val['order_id'];
			$lists[$oid]['payment_mode'] = $val['payment_mode']==1 ? '在线支付' : '到付（线下）';
			$lists[$oid]['payment_company'] = $val['payment_company'];
			$lists[$oid]['payment_channel'] = $val['payment_channel'];
			$lists[$oid]['total_fee'] = $val['total_fee'];
			$lists[$oid]['bond'] = $val['bond'];
			$lists[$oid]['site_pay'] = $val['site_pay'];
			$lists[$oid]['other_pay'] = $val['other_pay'];
			$lists[$oid]['shipping_status'] = $val['shipping_status'];
			$lists[$oid]['status'] = $val['status'];
			$lists[$oid]['create_time'] = $val['create_time'];
			unset($voList[$key]['order_id']);
			unset($voList[$key]['payment_mode']);
			unset($voList[$key]['payment_company']);
			unset($voList[$key]['payment_channel']);
			unset($voList[$key]['total_fee']);
			unset($voList[$key]['bond']);
			unset($voList[$key]['site_pay']);
			unset($voList[$key]['other_pay']);
			unset($voList[$key]['shipping_status']);
			unset($voList[$key]['status']);
			unset($voList[$key]['create_time']);
			$lists[$oid]['items'][] = $voList[$key];
		}
		//dump($lists);exit;
		//列表排序显示
		$sortImg    = $sort ;                                   //排序图标
		$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
		$sort       = $sort == 'desc'? 1:0;                     //排序方式
		//赋值
		$list['count'] = $count;
		$list['page_count'] = $page_count;
		$list['count'] = $count;
		$list['sortAlt'] = $sortAlt;
		$list['_sort'] = $sort;
		$list['_order'] = $order;
		//$list['total_fee'] = $total_fee;
		$list['data'] = $lists;
		//dump($list);
		ajaxSucReturn($list);
	}

	//订单详情
	public function detail(){
	  $model = M('Order');
	  $data['order_sn'] = $_POST['order_sn'];
	  $vo = $model->field('id,order_sn,type,actual_paid,totle_fee,bond,shipping_fee,insure_fee,discount_fee,coupons_fee,score_fee,balance_fee,seller_id,recipient,address,zip_code,mobile,memo,create_time,order_time,pay_time,confirm_time,is_refund,is_comment,delivery_status,pay_status,status')->where($data)->find();
	  if(!$vo){
		//错误提示
		ajaxErrReturn('订单不存在');
	  }
	  //订单列表
	  $od_model = M('OrderDetail');
	  $od_data['order_id'] = $vo['id'];
	  $list = $model->field('product_id,product_name,item_id,spec,price,num,memo,status')->where($od_data)->select();
	  $vo['details'] = $list;
      ajaxSucReturn($vo);
	  //echo  json_encode($vo);exit;
	}

    //搜素条件
	public function _search(){
		$map['a.member_id'] = $this->user['id'];
		$map['a.is_display'] = 1;
		if($_GET['order_id']){
			$map['a.order_id'] = $this->user['order_id'];
		}
		if($_GET['status'] || $_GET['status']==='0'){
			$map['a.status'] = $this->user['status'];
		}
		return $map;
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
        $vo['coupons'] = $coupons;
        //促销信息
        $promotions  = get_promotion($list['data']);
        if($promotions){
          $total_fee -= $promotions[0]['award_value'];
        }
        $vo['promotions'] = $promotions;
        //邮费
        //无地址则读取注册地址
        if(!$consignee){
          $shipping_fee = shipping_fee($list['data'],$this->user['ct_id']);
        }else{
          $shipping_fee = shipping_fee($list['data'],$consignee['ct_id']);
        }
        $vo['shipping_fee'] = $shipping_fee;
        $total_fee += $shipping_fee;
        //发票税额
        if($this->configs['tax_rate']){
          $vo['tax_rate'] = $this->configs['tax_rate']/100;
        }
        $vo['total_fee'] = $total_fee;
        $vo['data'] = $list['data'];
        $vo['consignee'] = $consignee;
        echo  json_encode($vo);exit;
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

	//订单生成
	public function create(){
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

	//合并订单号
	public function merge(){
		$model = D('Order');
		//订单号
		$mo_id = build_order_no($this->user['id']);
		$ids = explode(',',$_REQUEST['order_ids']);
		$wdata['member_id'] = $this->user['id'];
		$wdata['order_id'] = array('in',$ids);
		$sdata['mo_id'] = $mo_id;
		$result = $model->where($wdata)->save($sdata);
		if(!$result){
			ajaxErrReturn('订单生成失败');
		}
		$msg['error_code'] = 0;
		$msg['notice'] = '订单生成成功';
		$msg['out_trade_no'] = $mo_id;
		ajaxSucReturn($msg);
	}


	//支付宝支付
	public function alipay(){
		if(!$_REQUEST['order_id'] && $_REQUEST['mo_id']){
			ajaxErrReturn('订单号必须');
		}
		$model = M('order');
		if($_REQUEST['order_id']){
			$data['order_id'] = $_REQUEST['order_id'];
			$out_trade_no = $_REQUEST['order_id'];
			//单一订单支付
			$msg['notify_url'] = C('SITE_URL') . '/index.php/Ali_Payment/notify';
		}
		if($_REQUEST['mo_id']){
			$data['mo_id'] = $_REQUEST['mo_id'];
			$out_trade_no = $_REQUEST['mo_id'];
			//多订单合并支付
			$msg['notify_url'] = C('SITE_URL') . '/index.php/Ali_Payment/notify_merge';
		}
		$data['member_id'] = $this->user['id'];
		$orders = $model->field('id,title,order_id,actual_paid,status')->where($data)->select();
		if(!$orders){
			ajaxErrReturn('订单不存在');
		}
		$total_fee = 0;
		$title = '';
		foreach($orders as $key=>$order){
			$total_fee += $order['actual_paid'];
			$titles[] = $order['title'];
			if($order['pay_status']!=0){
				ajaxErrReturn($order['order_id'].'订单状态错误');
			}
		}
		$body = implode(',',$titles);
		$msg['total_fee'] = $total_fee;
		$msg['out_trade_no'] = $out_trade_no;
		$msg['body'] = $body;
		$msg['partner_id'] = '2088911540905383';
		$msg['seller_id'] = 'mxjmdsh@163.com';
		$msg['private_key'] = 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBALGo8VdoGO71vL65
6AJRHsTLsqWSWh2DSwFqF27aEKGiRg082tncO5B0yLisLgcYrPKOZhc3KHzo4k2z
K1J3kvX6g85XeTtLdeT3HOe5R0Za/Q7uFBWxvC2vF4ybhmNgj03sk8W2vhLmZdSf
8tCl2g7I5xL5ryP5Brsmb/8rSFvvAgMBAAECgYEAhZZSOvY0YWJ4FTX3Bd73cuT0
JNnCVxTFTn/2tMzV2qQJZqTOryiRxVJ7J5mYVo+wZAa9L1GzaRE4MVK7DZyJ7Jko
NA0d91Uca6yF8yv2/oQ50e4pzI+K2lLM8gJ5/ojDj2VmdrGq9SYtzXdvD15IONW5
iiJD6tsq4xKCN4OeUZECQQDa/ugExSDrbO2m28j2gsdYirm+2E2MLF70xhybLr8W
ThMtPSJyIhYOGRvoEOKCoo2mfGqHv+9kRIWMXYEhRonXAkEAz635iuWTt6tUNDgq
bIABAX/YCgi/msG8jCOf5BpQ1xvvNAb5K5z8bbnyRtQCRsXder0SHvYFPp+G48aw
JmnrqQJBAK95qy4PSrZ53N9jot6rodH3rqgWJ+UWtKuLhuZtiZ30x3brUouDdoqr
YeoMhYNJfxkU/kNx7v83zI7RaaPhIpcCQGux2yLk9FoddXfy3EURh/QAGMbzTHDz
IFRHsQV3hA5YhQ2kxBeSu/AmqfWCwX9z4ethGoGFsKiNz0RU50m0SakCQD2hOII/
OeZn0Dn5PT6peHVW8YpTj66m/10uel9LNaFiR5U2GBOFs/nxthafoOoxemgWXtZV
IzEC4DyWMn7RPrw=';
		$msg['autoclose'] = '';
		$msg['error_code'] = 0;
		ajaxSucReturn($msg);
	}


	//微信支付
	public function wxpay(){
		//ajaxErrReturn('订单号必须');exit;
		if(!$_REQUEST['order_id'] && $_REQUEST['mo_id']){
			ajaxErrReturn('订单号必须');
		}
		$model = M('order');
		if($_REQUEST['order_id']){
			$data['order_id'] = $_REQUEST['order_id'];
			$out_trade_no = $_REQUEST['order_id'];
		}
		if($_GET['mo_id']){
			$data['mo_id'] = $_REQUEST['mo_id'];
			$out_trade_no = $_REQUEST['mo_id'];
		}
		$data['member_id'] = $this->user['id'];
		$orders = $model->field('id,title,order_id,actual_paid,status')->where($data)->select();
		if(!$orders){
			ajaxErrReturn('订单不存在');
		}
		$total_fee = 0;
		$title = '';
		foreach($orders as $key=>$order){
			$total_fee += $order['actual_paid'];
			$titles[] = $order['title'];
			if($order['status']!=0){
				ajaxErrReturn($order['order_id'].'订单状态错误');
			}
		}
		$body = implode(',',$titles);
		$body = $body;
		$total_fee = $total_fee*100;
		$total_fee = (string)$total_fee;
		require_once "../wxpay/lib/WxPay.Api.php";		
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);
		//$input->SetAttach("test");
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));

		if($_REQUEST['order_id']){
			//单一订单支付
			$input->SetNotify_url(C('SITE_URL') . '/index.php/Wx_Payment/notify');
		}
		if($_REQUEST['mo_id']){
			//多订单合并支付
			$input->SetNotify_url(C('SITE_URL') . '/index.php/Wx_Payment/notify_merge');
		}
		//$input->SetNotify_url(C('SITE_URL') . '/index.php/Wx_Payment/notify_merge');


		$input->SetTrade_type("APP");
		//$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);
		$msg['appid'] = $order['appid'];
		$msg['partnerid'] = $order['mch_id'];
		$msg['prepayid'] = $order['prepay_id'];
		$msg['package'] = 'Sign=WXPay';
		$msg['noncestr'] = $order['nonce_str'];
		$msg['timestamp'] = time();
		//$this->json['sign'] = $order['sign'];
		//再次生成签名
		ksort($msg);
		//$string = $aa->ToUrlParams($this->json);
		$buff = "";
		foreach ($msg as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		$buff = trim($buff, "&");
		$string = $buff;
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".WxPayConfig::KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		$msg['sign'] = $result;
		//dump($msg);
		ajaxSucReturn($msg);
	}

	//余额支付
	public function balance_pay(){
		$time = time();
		if(!$_REQUEST['order_id'] && $_REQUEST['mo_id']){
			ajaxErrReturn('订单号必须');
		}
		$model = M('Order');
        $od_model = M('OrderDetail');
		if($_REQUEST['order_id']){
			$data['order_id'] = $_REQUEST['order_id'];
			$out_trade_no = $_REQUEST['order_id'];
		}
		if($_GET['mo_id']){
			$data['mo_id'] = $_REQUEST['mo_id'];
			$out_trade_no = $_REQUEST['mo_id'];
		}
		$data['member_id'] = $this->user['id'];
		$orders = $model->field('id,title,order_id,actual_paid,status')->where($data)->select();
		if(!$orders){
			ajaxErrReturn('订单不存在');
		}
		$total_fee = 0;
		foreach($orders as $vo){
		  $total_fee += $vo['actual_paid'];
		}
		if($this->user['balance']<$total_fee){
		  ajaxErrReturn('可用余额不足');
		}
		$po_model = M('PayNotify');
		foreach($orders as $vo){
		  $po_data['order_id'] = $vo['order_id'];
		  $po_data['out_trade_no'] = $vo['order_id'];
		  $po_data['info'] = serialize($vo);
		  $po_data['notice'] = '支付回调';
		  $po_data['create_time'] = $time;
		  $po_model->add($po_data);
		}
		$model->startTrans();//启用事务
		foreach($orders as $vo){
			$order_id = $vo['id'];
			if(!$vo){
			  $this->pay_notice($order_id,'订单不存在');
			  ajaxErrReturn('订单不存在!');
			}
			if($vo['order_time'] && time()>$vo['order_time']){
			  $this->pay_notice($order_id,'订单已过期');
			  ajaxErrReturn('订单已过期!');
			}
			if($vo['pay_status']>0){
			  $this->pay_notice($order_id,'已支付!');
			  ajaxErrReturn('已支付!');
			}
			$trade_no = build_order_no($this->user['id']);
			//$goods = unserialize($vo['goods']);
            $od_data['order_id'] = $vo['id'];
            $goods = $od_model->field('product_name')->where($od_data)->select();
			$subject = '购买商品:';
			$body = '购买商品';
			foreach($goods as $g){
			  $subject .= $g['product_name'].',';
			  $body .= $g['product_name'].',';
			}
			$subject = substr($subject,0,-1);
			$body = substr($subject,0,-1);
			$body .= ',共消费'.$total_fee;
			//订单状态修改
			$wdata['id'] = $vo['id'];
			$sdata['pay_status'] = 1;
			$sdata['order_id_third'] = $trade_no;
			$sdata['pay_time'] = $time;
			$result = $model->where($wdata)->save($sdata);
			if(!$result){
			  $model->rollback();
			  $this->pay_notice($order_id,'订单状态修改失败');
			  ajaxErrReturn('支付失败!');		
			}
			//修改余额
			$wallet_data['member_id'] = $vo['member_id'];
			$result = $wallet_model->where($wallet_data)->setDec('balance',$vo['actual_paid']);
			if(!$result){
			  $model->rollback();
			  $this->pay_notice($order_id,'用户账号余额修改失败');
			  ajaxErrReturn('支付失败!');		
			}
			$content = '共支付'.$total_fee;
			//记录买家财务账单
			unset($rdata);
			$rmodel = M('Record');
			$rdata['member_id'] = $vo['member_id'];
			$wallet = $wallet_model->where($wallet_data)->find();
			$rdata['member_name'] = $vo['member_name'];
			$rdata['realname'] = $vo['realname'];
			$rdata['order_id'] = $vo['order_id'];
			$rdata['pay_type'] = 2;
			$rdata['payment_mode'] = 1;
			$rdata['payment_company'] = $this->configs['company_name'];
            $rdata['payment_channel'] = '网站余额';
			$rdata['pay_order_sn'] = $trade_no;
			$rdata['buyer'] = $vo['member_name'];
			$rdata['content'] = $content;
			$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
			$rdata['pay'] = $vo['actual_paid'];
			$rdata['create_time'] = $time;
			$rdata['status'] = 1;
			$rdata['pay_time'] = time();
			if($result){
			  $result = $rmodel->add($rdata);
			}else{
			  $model->rollback();
			  $this->pay_notice($order_id,'买家财务账单记录失败');
			  ajaxErrReturn('支付失败!');			
			}
			if(!$result){
			  $model->rollback();
			  ajaxErrReturn('支付失败!');		
			}
		}
		if($result){
			$model->commit();
            $msg['notice'] = '完成支付';
			ajaxSucReturn($msg);
		}else{
			$model->rollback();
			ajaxErrReturn ('支付失败!');
		}
	}


  //记录支付节点
  private function pay_notice($order_id,$notice){
	$model = M('Pay_notify');
	$wdata['order_id'] = $order_id;
	$sdata['notice'] = $notice;
	$model->where($order_id)->save($notice);
  }

  //库存修改
  private function update_stock($product_id,$item_id,$num){
	$model = M('Product');
	$data['id'] = $product_id;
	$model->where($data)->setDec('stock',$num);
	if($item_id){
	  $pi_model = M('Product_item');
	  $pi_data['id'] = $item_id;
	  $pi_model->where($pi_data)->setDec('stock',$num);
	}
  }

  //确认订单


}