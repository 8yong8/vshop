<?php
namespace Home\Controller;
use Think\Controller;
class ProjectsController extends PublicController{
  public function _initialize(){
	parent::_initialize();
	$usecssjs = '<link href="'.__ROOT__.'/Public/css/index.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/pro_list.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/zc.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/false.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/jquery-1.8.2.min.js"></script><link href="'.__ROOT__.'/Public/js/artDialog/skins/blue.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/jquery.artDialog.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/plugins/iframeTools.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/jquery.countdown.min.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/common.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/dropMenu1.js"></script>';
	$this->assign('usecssjs',$usecssjs);
	$this->assign('action_name','zhongchou');
  }

  public function index(){
	//主题
	$model = M('Subject');
	$subject_data['source'] = 'Projects';
	$subject_data['status'] = 1;
	$subjects = $model->where($subject_data)->order('orderindex asc,id desc')->limit(3)->select();
	$this->assign('subjects',$subjects);
	//分类
	$model = M('ProductType');
	$data['pid'] = 0;
	$types = $model->where($data)->select();
	$this->assign('types',$types);
	//众筹
    $model = M('projects');
	$data = $this->_search();
	$page = $_GET['p'] ? $_GET['p'] : 1;
	$order = 'id desc';
	$count = $model->where($data)->count();
	import("@.ORG.Util.Page");
	$page_size = 12;
	$p = new Page ( $count, $page_size );
	$page_count = ceil($count / $page_size);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->where($data)->order($order)->limit($offset.','.$page_size)->select();
	//echo $model->getlastsql();
	$page = $p->show ();
	$this->assign('list', $list);
	$this->assign('p',$pageno);
	$this->assign ("page",$page );
	$this->assign('count',$count);
	$this->assign('page_count', $page_count);
	//信息
	$title = '众筹页- '.C('site_name');
	$keywords = '众筹页';
	$description = '众筹页';
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);  
    $this->display();
  }

  public function lists(){
	//分类
	$model = M('ProductType');
	$data['pid'] = 0;
	$types = $model->where($data)->select();
	$this->assign('types',$types);
	//众筹
    $model = M('Projects');
	$data = $this->_search();
	$page = $_GET['p'] ? $_GET['p'] : 1;
	$order = 'id desc';
	$count = $model->where($data)->count();
	import("@.ORG.Util.Page");
	$page_size = 12;
	$p = new Page ( $count, $page_size );
	$page_count = ceil($count / $page_size);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->where($data)->order($order)->limit($offset.','.$page_size)->select();
	//echo $model->getlastsql();
	$page = $p->show ();
	$this->assign('list', $list);
	$this->assign('p',$pageno);
	$this->assign ("page",$page );
	$this->assign('count',$count);
	$this->assign('page_count', $page_count);
	//信息
	$title = '众筹页- '.C('site_name');
	$keywords = '众筹页';
	$description = '众筹页';
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);  
    $this->display();
  }

  //搜索条件
  public function _search(){
	//搜索条件组装
	$del_li = '';
	$root = __URL__.'/lists';
	$url_array = $_GET;
	unset($url_array['_URL_']);
	$map = array ();
	$map['status'] = 1;
	$map['member_id'] = array('gt',0);
	if($_GET['sid']){
	  $map['subject_id'] = $_GET['sid'];
	}

	if($_GET['keyword']){
	  $map['title'] = array('like','%'.$_GET['keyword'].'%');
	  $this->assign('keyword',$_GET['keyword']);
	}

	if($_GET['zt']==1){
	  $map['starttime'] = array('gt',time());
	}else if($_GET['zt']==2){
	  $map['starttime'] = array('lt',time());
	  $map['endtime'] = array('gt',time());
	}else if($_GET['zt']==3){
	  $map['status'] = 2;
	}

	if($_GET['tid']){
	  $map['tid'] = $_GET['tid'];
	  $this->assign('tid',$_GET['tid']);
	  $model = M('ProductType');
	  $data['id'] = $_GET['tid'];
	  $vo = $model->field('pid,name')->where($data)->find();
	  $toptid = $vo['pid'];
	  $tdata['pid'] = $toptid;
	  $tdata['status'] = 1;
	  $childrentypes = $model->where($tdata)->select();
	  $this->assign('childrentypes',$childrentypes);
	  $this->assign('toptid',$toptid);
	  $my_url_array = $url_array;
	  unset($my_url_array['tid']);
	  $url_str = http_build_query($my_url_array);
	  $url_str = str_ireplace(array('&','='),array('/','/'),$url_str);
	  $del_li = '<a href="'.$root.'/'.$url_str.'">'.$vo['name'].'</a>';
	}

	if($_GET['toptid']){
	  $map['toptid'] = $_GET['toptid'];
	  if($_GET['toptid']==1){
	    $this->assign('top_cname','中国国画');
	  }else{
	    $this->assign('top_cname','书法');
	  }
	  $model = M('product_type');
	  $tdata['pid'] = $_GET['toptid'];
	  $tdata['status'] = 1;
	  $childrentypes = $model->where($tdata)->select();
	  $this->assign('childrentypes',$childrentypes);
	  $this->assign('toptid',$_GET['toptid']);
	}
	
	return $map;
  }

  public  function detail(){
    $model = M('Projects');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
	  $this->error('出错');
	}
	$model->where($data)->setInc('look_num');
	$this->assign('vo',$vo);
	$pgmodel = M('Project_goods');
	$pg_data['project_id'] = $_GET['id'];
	$list = $pgmodel->where($pg_data)->select();
	$this->assign('list',$list);
	$title = '众筹详细页- '.C('site_name');
	$keywords = '众筹详细页';
	$description = '众筹详细页';
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
	$this->assign('furl',urlencode(C('SITE_URL').'/index.php/Projects/detail/id/'.$_GET['id']));
	$this->assign('ftitle',$vo['name']);
	$this->assign('fpic',$vo['pic']);
	$this->display();
  }

  //众筹
  public function buystep(){
	$usecssjs = '<link href="'.__ROOT__.'/Public/css/index.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/shopping.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/flat-ui.min.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/false.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/jquery.js"></script><link href="'.__ROOT__.'/Public/js/artDialog/skins/blue.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/jquery.artDialog.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/plugins/iframeTools.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/common.js"></script>';
	$this->assign('usecssjs',$usecssjs);
	//$_POST['ids'] = explode(',',$_POST['ids']);
	//$_POST['nums'] = explode(',',$_POST['nums']);
    if(!$this->user){
	  $this->assign('jumpUrl',$this->login_url);
	  $this->error('请先登录!');
	}
	$model = M('member_address');
	$ad_data['member_id'] = $this->user['id'];
	$addrs = $model->where($ad_data)->order('id desc')->select();
	$this->assign('addrs',$addrs);
	$model = M('projects');
	$p_data['id'] = $_GET['id'];
	$vo = $model->where($p_data)->find();
	$model = M('project_goods');
	$data['id'] = array('in',$_GET['ids']);
	$data['status'] = 1;
	$data['project_id'] = $_GET['id'];
	//$pg_data['id'] = array('in',$_GET['ids']);
	$list = $model->field('*')->where($data)->select();
	if(!$list){
	  $this->error('艺术品已售罄');
	}
	foreach($list as $val){
	  $total_price += $val['price'];
	}
	$this->assign('total_price',$total_price);
	$this->assign('list',$list);
	$this->assign('totleprice',$totleprice);
	$title = '众筹订单确认页- '.C('site_name');
	$keywords = '众筹订单确认页';
	$description = '众筹订单确认页';
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
	$this->display();  
  
  }
  //订单生成
  public function create_order_to_pay(){
	if(!$this->user){
	  $this->assign('jumpUrl',__APP__.'/Public/login/from_url/'.urlencode($_SERVER['HTTP_REFERER']));
	  $this->error('请先登录!');
	}
	if(!$_POST['ids'] || !$_POST['nums'] || !$_POST['address_id']){
	  $this->error('出错！');
	}
	$logs_model = M('Logs');
	$p_model = M('Projects');
	$p_data['id'] = $_POST['id'];
	$vo = $p_model->where($p_data)->find();
	$admodel = M('MemberAddress');
	$ad_data['id'] = $_POST['address_id'];
	$address = $admodel->where($ad_data)->find();
	$model = M('project_goods');
	$data['id'] = array('in',$_POST['ids']);
	$list = $model->field('id,lit_pic,project_id,project_name,product_id,goods_id,product_name,subtitle,price,status')->where($data)->select();
	$totleprice = 0;
	$goods_name = '购买艺术品:';
	//dump($list);exit;
	foreach($list as $key=>$val){
	  $member_id = $val['member_id'];
	  $id = $val['id'];
	  $k = array_search($id,$_POST['ids']);
	  if($val['status']==0){
	    $this->error($val['product_name'].'已被购买');
	  }
	  $num = 1;
	  if($key==0){
	    $goods_name .= $val['product_name'];
	  }else{
	    $goods_name .= ','.$val['product_name'];
	  }
	  $totleprice += $num*$val['price'];
	  //记录购买
	  $logs_data['msg'] = '<span>'.name_hide($this->user['username']).'</span>下单购买众筹作品'.$val['product_name'];
	  $logs_data['create_time'] = time();
	  $logs_model->add($logs_data);
	}
	//生成订单
	$model = M('Order');
	$gmodel = M('Goods');
	$mmodel = M('Member');
	$odmodel = M('OrderDetail');
	$time = time();
	$model->startTrans();//启用事务
	$out_trade_no = '';
	  $add_order['uw_id'] = $vo['member_id'];
	  $add_order['type'] = 4;
	  $add_order['title'] = $goods_name;
	  $add_order['goods'] = serialize($list);
	  //订单号
	  //$bh = str_pad($mid.time(),15,0,STR_PAD_LEFT);
	  //$add_order['order_id'] = 'c'.$bh;
	  $add_order['order_id'] = build_order_no($this->user['id']);
	  if($_POST['paytype'] == 1){
	    $add_order['payment_mode'] = '1';
	    $add_order['payment_company'] = '支付宝';	    
	  }else if($_POST['paytype'] == 0){
	    $add_order['payment_mode'] = '1';
	    $add_order['payment_company'] = C('company_name');		  
	  }
	  $add_order['total_price'] = $totleprice;
	  $add_order['bond'] = 0;
	  $add_order['source'] = 'Projects';
	  $add_order['sourceid'] = $_POST['id'];
	  $add_order['total_num'] = count($list);
	  $add_order['member_id'] = $this->user['id'];
	  $add_order['member_name'] = $this->user['username'];
	  $add_order['realname'] = $this->user['realname'];
	  $add_order['user_id'] = 0;
	  $add_order['address_id'] = $_POST['address_id'];
	  $add_order['recipient'] = $address['name'];
	  $add_order['address'] = $address['address'];
	  $add_order['postcode'] = $address['postcode'];
	  $add_order['tel'] = $address['mobile'];
	  $add_order['remark'] = $_POST['remark'];
	  $add_order['ip'] = $_SERVER['REMOTE_ADDR'];
	  $add_order['create_time'] = $time;
	  $add_order['order_time'] = $time+86400*$this->configs['order_expired'];
	  $add_order['remark'] = $_POST['remark'.$mid] ? $_POST['remark'.$mid] : '';
	  $oid = $model->add($add_order);
	  //提交给支付宝的订单
	  $out_trade_no .= $oid.',';
	  if($oid){
		//订单产品详情
	    foreach($list as $goods){
		  $od_data['member_id'] = $this->user['id'];
		  $od_data['user_id'] = 0;
		  $od_data['oid'] = $oid;
		  $od_data['source'] = 'Project_goods';
		  $gid = $goods['id'];
		  $od_data['sourceid'] = $goods['id'];
		  $od_data['product_id'] = $goods['product_id'];
		  $od_data['product_name'] = $goods['product_name'];
		  //$od_data['product_name'] = $goods['product_name'];
		  $share_id  = $_SESSION['share_id'];
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
		  $od_data['num'] = 1;
		  $od_data['is_buyback'] = 1;
		  $od_data['is_dm'] = 1;
		  $md_id = $odmodel->add($od_data);
		  //echo $odmodel->getlastsql();exit;
		  //减去库存
		  //$result = $gmodel->where('id='.$gid)->setDec('inventory',$goods['num']);
		  if(!$md_id){
			$model->rollback();
			$this->error('订单生成失败!');
			exit;
		  }
	    }	  
	  }else{
	    $model->rollback();
	    $this->error('订单生成失败!');
		exit;
	  }

	$out_trade_no = substr($out_trade_no,0,-1);
	$model->commit();
	if($_POST['paytype'] == 1){
		//支付宝
		require_once("./Alipay/alipay.config.php");
		require_once("./Alipay/lib/alipay_submit.class.php");
		require_once("./Alipay/Alipay.class.php");
		//支付信息
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$payment_type = "1";
		$exter_invoke_ip = $_SERVER['REMOTE_ADDR'];
		//$out_trade_no = $vo['id'];
		$subject = $content = '众筹购买支付!';
		$total_fee = $totleprice;
		$total_fee = 0.01;
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url.'/ali_notify_project',
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $content,
				//"show_url"	=> $show_url,
				//"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
				"extra_common_param"=>'pay_type|2'  //参数
		);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
		echo $html_text;
	}else if($_POST['paytype'] == 0){
	  redirect(__APP__.'/Projects/payment/ids/'.$out_trade_no);
	}
	echo '出错!';exit;
  }


  //余额支付
  public function payment(){
	$usecssjs = '<link href="'.__ROOT__.'/Public/css/index.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/shopping.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/flat-ui.min.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/false.css" rel="stylesheet" type="text/css" />';
	$this->assign('usecssjs',$usecssjs);
	if(!$this->user){
	  $this->assign('jumpUrl',__APP__.'/Public/login/from_url/'.urlencode($_SERVER['HTTP_REFERER']));
	  $this->error('请先登录!');
	}
	if(!$_GET['ids']){
	  $this->error('出错！');
	}
    $ids = explode(',',$_GET['ids']);
	$this->assign('jumpUrl',__ROOT__.'/member/index.php/Orders');
	$model = M('order');
	$goods_model = M('goods');
	$odmodel = M('order_detail');
	$pmodel = M('projects');
	$pgmodel = M('project_goods');
	$wallet_model = M('member_wallet');
	$time = time();
	//$trade_no = build_order_no($this->user['id']);
	$model->startTrans();//启用事务
	$data['id'] = array('in',$_GET['ids']);
	$data['member_id'] = $this->user['id'];
	$data['status'] = 0;
	$data['type'] = 4;
	$list = $model->field('id,user_id,bond,type,sourceid,goods,total_price,order_id,status,order_time,member_id,member_name,realname')->where($data)->select();
	if(count($list)!=count($ids)){
	  $this->error('出错!');
	}
	foreach($list as $vo){
	  $total_price += $vo['total_price'];
	}
	//echo $total_price;
	if($this->user['balance']<$total_price){
	  $this->error('可用余额不足!');
	}
	$totalprice = 0 ;
	foreach($list as $vo){
		$totalprice += $vo['total_price'];
		if(!$vo){
		  $this->error('订单不存在!');
		}
		if($vo['order_time'] && time()>$vo['order_time']){
		  $this->error('订单已过期!');
		}
		if($vo['status']>0){
		  $this->error('已支付!');
		}
		if($vo['type']!=4){
		  $this->error('订单有误!');
		}
		//$time = time();
		$trade_no = build_order_no($this->user['id']);
		$goods = unserialize($vo['goods']);
		$subject = '购买商品:';
		$body = '购买商品';
		foreach($goods as $g){
		  $subject .= $g['product_name'].',';
		  $body .= $g['product_name'].',';
		}
		$subject = substr($subject,0,-1);
		$body = substr($subject,0,-1);
		$body .= ',共消费'.$vo['total_price'];
		//订单状态修改
		$wdata['id'] = $vo['id'];
		$sdata['status'] = 1;
		$sdata['pay_order_id'] = $trade_no;
		$sdata['pay_time'] = $time;
		$result = $model->where($wdata)->save($sdata);
		if(!$result){
	      $model->rollback();
		  $this->error ('支付失败1!');		
		}
		//修改余额
		$wallet_data['member_id'] = $vo['member_id'];
		$result = $wallet_model->where($wallet_data)->setDec('balance',$vo['total_price']);
		if(!$result){
	      $model->rollback();
		  $this->error ('支付失败2!');		
		}
		$wl_data['title'] = '完成支付，订单号：'.$vo['order_id'];
		//$content = '共支付'.$vo['total_price'];
		$content = $body;
		//记录买家财务账单
		unset($rdata);
		$rmodel = M('record');
		$rdata['member_id'] = $vo['member_id'];
		$wallet = $wallet_model->where($wallet_data)->find();
		$rdata['member_name'] = $vo['member_name'];
		$rdata['realname'] = $vo['realname'];
		$rdata['order_id'] = $vo['order_id'];
		$rdata['pay_type'] = 2;
		$rdata['payment_mode'] = '1';
		$rdata['payment_company'] = $this->configs['company_name'];
		$rdata['pay_order_id'] = $trade_no;
		$rdata['buyer'] = $vo['member_name'];
		$rdata['content'] = $content;
		$rdata['balance'] = $wallet['balance'] ? $wallet['balance'] : 0;
		$rdata['pay'] = $vo['total_price'];
		$rdata['create_time'] = $time;
		$rdata['status'] = 1;
		$rdata['pay_time'] = time();
		if($result)$result = $rmodel->add($rdata);
		//修改库存
		//产品减库存
		$od_data['oid'] = $vo['id'];
		$od_list = $odmodel->alias('a')->join('`op_project_goods` as b on a.sourceid=b.id')->field('b.id,b.goods_id')->where($od_data)->select();
		foreach($od_list as $val){
		  //众筹产品状态修改
		  $pg_data['id'] = $val['id'];
		  $pg_sdata['status'] = 0;
		  if($result)$result = $pgmodel->where($pg_data)->save($pg_sdata);
		  //减库存
		  $goods_data['id'] = $val['goods_id'];
		  if($result){
			  $result = $goods_model->where($goods_data)->setDec('inventory',1);
		  }
		  //echo $goods_model->getlastsql();exit;
		}
		//修改众筹信息
		$pdata['id'] = $vo['sourceid'];
		$where_str = 'project_id='.$vo['sourceid'].' AND status=0';
		$goods_count = $pgmodel->where($where_str)->count();
		$psdata['pay_num'] = $goods_count;
		$goods_pay = $pgmodel->field('sum(price) as pay')->where($where_str)->find();
		$psdata['price'] = $goods_pay['pay'];
		if($result)$result=$pmodel->where($pdata)->save($psdata);

		if(!$result){
	      $model->rollback();
		  $this->error ('支付失败3!');		
		}
	}
	if($result){
	    $model->commit();
		//$this->success ('支付成功!');
		$title = '支付页- '.C('site_name');
		$keywords = '支付页';
		$this->assign('totalprice',$totalprice);
		$description = '支付页';
		$this->assign('title', $title);
		$this->assign('keywords', $keywords);
		$this->assign('description', $description);
		$this->assign('totalprice',$totalprice);
		$this->display('goods:order-confirm');
	}else{
	    $model->rollback();
		$this->error ('支付失败4!');
	}
  }

  function like(){
	if(!$this->user){
		$msg['status'] = 0;
		$msg['notice'] = '请先登录！';
		echo  json_encode($msg);exit;
	}
	$model = D (ACTION_NAME);
	$data['member_id'] = $this->user['id'];
	$data['source'] = $_POST['source'];
	$data['sourceid'] = $_POST['sourceid'];
	$count = $model->where($data)->count();
	if($count>0){
		$msg['status'] = 0;
		$msg['notice'] = '已赞！';
		echo  json_encode($msg);exit;
	}
	$_POST['create_time'] = time();
	$_POST['ip'] = $_SERVER['REMOTE_ADDR'];
	$_POST['member_id'] = $this->user['id'];
	$pmodel = M($_POST['source']);
	$pdata['id'] = $_POST['sourceid'];
	$pmodel->where($pdata)->setInc('like_num');
	if (false === $model->create ()) {
		$msg['status'] = 0;
		$msg['notice'] = '出错！';
		echo  json_encode($msg);exit;
	}
	//保存当前数据对象
	$list = $model->add ();
	if ($list!==false) { //保存成功
		$msg['status'] = 1;
		$msg['notice'] = '赞成功！';
		//$model = M('weight');
		//unset($data['member_id']);
		//$this->update_weight($_POST['source'],$_POST['sourceid']);
		echo  json_encode($msg);exit;
	} else {
		$msg['status'] = 0;
		$msg['notice'] = '赞失败！';
		echo  json_encode($msg);exit;
	}
  }

  function support(){
	if(!$this->user){
		$msg['status'] = 0;
		$msg['notice'] = '请先登录！';
		echo  json_encode($msg);exit;
	}
	$model = D (ACTION_NAME);
	$data['member_id'] = $this->user['id'];
	$data['source'] = $_POST['source'];
	$data['sourceid'] = $_POST['sourceid'];
	$count = $model->where($data)->count();
	if($count>0){
		$msg['status'] = 0;
		$msg['notice'] = '已支持！';
		echo  json_encode($msg);exit;
	}
	$_POST['create_time'] = time();
	$_POST['ip'] = $_SERVER['REMOTE_ADDR'];
	$_POST['member_id'] = $this->user['id'];
	$pmodel = M($_POST['source']);
	$pdata['id'] = $_POST['sourceid'];
	$pmodel->where($pdata)->setInc('support_num');
	if (false === $model->create ()) {
		$msg['status'] = 0;
		$msg['notice'] = '出错！';
		echo  json_encode($msg);exit;
	}
	//保存当前数据对象
	$list = $model->add ();
	if ($list!==false) { //保存成功
		$msg['status'] = 1;
		$msg['notice'] = '支持成功！';
		//$model = M('weight');
		//unset($data['member_id']);
		//$this->update_weight($_POST['source'],$_POST['sourceid']);
		echo  json_encode($msg);exit;
	} else {
		$msg['status'] = 0;
		$msg['notice'] = '支持失败！';
		echo  json_encode($msg);exit;
	}
  }

  function check_inventory($id,$num){
    $model = M('goods');
	$data['id'] = $id;
    $vo = $model->field('inventory')->where($data)->find();
	if($num>$vo['inventory']){
	  return false;
	}else{
	  return true;
	}
  }
  
}
?>