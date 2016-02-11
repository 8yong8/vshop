<?php
namespace Home\Controller;
use Think\Controller;
class AuctionController extends PublicController {
  public function _initialize(){
	parent::_initialize();
	$usecssjs = '<link href="'.__ROOT__.'/Public/css/index.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/pro_list.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/zc.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/false.css" rel="stylesheet" type="text/css" /><link href="'.__ROOT__.'/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/jquery-1.8.2.min.js"></script><link href="'.__ROOT__.'/Public/js/artDialog/skins/blue.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/jquery.artDialog.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/artDialog/plugins/iframeTools.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/common.js?2015/3/25"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/jquery.countdown.min.js"></script><script type="text/javascript" src="'.__ROOT__.'/Public/js/dropMenu1.js"></script>';
	$this->assign('usecssjs',$usecssjs);
	$this->assign('action_name',MODULE_NAME);
  }

  //首页
  public function index(){
	//主题
	$model = M('Subject');
	$subject_data['source'] = 'Auction';
	$subject_data['status'] = 1;
	$subjects = $model->where($subject_data)->order('orderindex asc,id desc')->limit(3)->select();
	$this->assign('subjects',$subjects);
	//分类
	$model = M('ProductType');
	$data['pid'] = 0;
	$types = $model->where($data)->select();
	$this->assign('types',$types);
	//数据
    $model = M('Auction');
	$data = $this->_search();
	$page = $_GET['p'] ? $_GET['p'] : 1;
	$order = 'create_time desc,id desc';
	if($_GET['_order']){
	  $_order = $_GET['_order'];
	}
	if($_GET['_sort']){
	  $_sort = $_GET['_sort'];
	}
	if($_order && $_sort){
	  $order = $_order.' '.$_sort;
	}
	$count = $model->where($data)->count();
	import("@.ORG.Util.Page");
	$page_size = 12;
	$p = new Page ( $count, $page_size );
	$page_count = ceil($count / $page_size);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->field('id,auctioncount,product_name,subtitle,lit_pic,starttime,endtime')->where($data)->order($order)->limit($offset.','.$page_size)->select();
	foreach($list as $key=>$val){
	  if(time()<$val['starttime']){
	    $list[$key]['endtime'] = $val['starttime'];
	  }
	}
	//dump($list);
	//echo $model->getlastsql();
	$page = $p->show ();
	unset($data);
	//价高数量
	$data['type'] = 1;
	$data['status'] = 1;
	$data['endtime'] = array('gt',time());
	$jg_count = $model->where($data)->count();
	//价低
	$data['type'] = 2;
	$jd_count = $model->where($data)->count();
	//组装URL
	$url  =  preg_replace("/\/_order.+/", "",__SELF__);
	if(ACTION_NAME=='index'){
	  $pos = strpos($url,'index/');
	  if($pos===false){
	    $url = $url.'/index';
	  }
	}
	$this->assign('url',$url);
	$this->assign('jg_count',$jg_count);
	$this->assign('jd_count',$jd_count);
	$this->assign('list', $list);
	$this->assign('p',$pageno);
	$this->assign ("page",$page );
	$this->assign('count',$count);
	$this->assign('page_count', $page_count);
	$title = '艺术品拍卖-'.$this->configs['webname'];
	$keywords = $product['keywords'];
	$description = $product['description'];
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
    $this->display();
  }

  //首页
  public function lists(){
	//分类
	$model = M('product_type');
	$data['pid'] = 0;
	$types = $model->where($data)->select();
	$this->assign('types',$types);
	//数据
    $model = M('Auction');
	$data = $this->_search();
	$page = $_GET['p'] ? $_GET['p'] : 1;
	if($_GET['order']==1){
	  $order = 'create_time desc,id desc';
	}else if($_GET['order']==2){
	  $order = 'price desc';
	}else if($_GET['order']==2){
	  $order = 'look_num desc';
	}else{
	  $order = 'create_time desc,id desc';
	}
	
	$count = $model->where($data)->count();
	import("@.ORG.Util.Page");
	$page_size = 12;
	$p = new Page ( $count, $page_size );
	$page_count = ceil($count / $page_size);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->field('*')->where($data)->order($order)->limit($offset.','.$page_size)->select();
	$page = $p->show ();
	unset($data);
	//价高数量
	$data['type'] = 1;
	$data['status'] = 1;
	$data['endtime'] = array('gt',time());
	$jg_count = $model->where($data)->count();
	//价低
	$data['type'] = 2;
	$jd_count = $model->where($data)->count();
	//组装URL
	$url  =  preg_replace("/\/_order.+/", "",__SELF__);
	if(ACTION_NAME=='index'){
	  $pos = strpos($url,'index/');
	  if($pos===false){
	    $url = $url.'/index';
	  }
	}
	$this->assign('url',$url);
	$this->assign('jg_count',$jg_count);
	$this->assign('jd_count',$jd_count);
	$this->assign('list', $list);
	$this->assign('p',$pageno);
	$this->assign ("page",$page );
	$this->assign('count',$count);
	$this->assign('page_count', $page_count);
	$title = '艺术品拍卖-'.$this->configs['webname'];
	$keywords = $product['keywords'];
	$description = $product['description'];
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
    $this->display();
  }

  //搜索条件
  public function _search(){
	$map = array ();
	$map['status'] = 1;
	$map['endtime'] = array('gt',time());
	if($_GET['t']){
	  $map['type'] = $_GET['t'];
	  $this->assign('t',$_GET['t']);
	}else{
	  //$map['type'] = $_GET['t'] = 1;
	  //$this->assign('t',$_GET['t']);
	}
	if($_GET['st']){
	  if($_GET['st']==1){
	    $map['starttime'] = array('lt',time());
	  }else{
	    $map['starttime'] = array('gt',time());
	  }
	  $this->assign('st',$_GET['st']);
	}
	if($_GET['sid']){
	  $map['subject_id'] = $_GET['sid'];
	}
	if($_GET['tid']){
	  $map['tid'] = $_GET['tid'];
	  $this->assign('tid',$_GET['tid']);
	  $model = M('product_type');
	  $data['id'] = $_GET['tid'];
	  $vo = $model->where($data)->getField('pid');
	  $toptid = $vo['pid'];
	  $tdata['pid'] = $toptid;
	  $tdata['status'] = 1;
	  $childrentypes = $model->where($tdata)->select();
	  $this->assign('childrentypes',$childrentypes);
	  $this->assign('toptid',$toptid);
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
	  $childrentypes = $model->where($tdata)->select();
	  $this->assign('childrentypes',$childrentypes);
	  $this->assign('toptid',$_GET['toptid']);
	}

	if($_GET['keyword']){
	  $map['product_name'] = array('like','%'.$_GET['keyword'].'%');
	  $this->assign('keyword',$_GET['keyword']);
	}
	if($a){
	
	}
	return $map;
  }

  //详情页
  public function detail(){
    $model = M('Auction');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
	  $this->error('不存在!');
	}
	$this->assign('url',__SELF__);
	$this->assign('vo',$vo);
	if($vo['endtime']<time()){
	  $this->assign('hide',1);
	}
	$share_id = $_GET['share_id'] ? $_GET['share_id'] : 0;
	$this->assign('share_id',$share_id);
	//艺术品信息
	$model = M('Product');
	$p_data['id'] = $vo['product_id'];
	$product = $model->where($p_data)->find();
	$this->assign('vo',$vo);
	$this->assign('product',$product);
	//图片
	$model = M('Pic');
	$pic_data['source'] = 'Product';
	$pic_data['sourceid'] = $product['id'];
	$pics = $model->where($pic_data)->select();
	$IMG_URL = C('IMG_URL');
	$IMG_ROOT = C('IMG_ROOT');
	if(!$pics){
	  $pics[0]['img-src'] = $product['logo'];
	  $bpic_dir = str_ireplace($IMG_URL,$IMG_ROOT,$product['logo']).'_yt.jpg';
	  if(file_exists($bpic_dir)){
	    $pics[0]['bimg-src'] = $product['logo'].'_yt.jpg';
	  }else{
	    $pics[0]['bimg-src'] = $product['logo'];
	  }
	}else{
	  foreach($pics as $key=>$val){
	    $pics[$key]['img-src'] = $val['domain'].$val['filepath'].$val['savename'];
		$bpic_dir = str_ireplace($IMG_URL,$IMG_ROOT,$pics[$key]['img-src']).'_yt.jpg';
	    if(file_exists($bpic_dir)){
	      $pics[0]['bimg-src'] = $pics[$key]['img-src'].'_yt.jpg';
	    }else{
	      $pics[0]['bimg-src'] = $pics[$key]['img-src'];
	    }
	  }
	}
	//dump($pics);
	$this->assign('pics',$pics);
	//评论
	$pls = $this->comment('Auction',$_GET['id']);
	$this->assign('pls',$pls);
	//记录浏览
	$model = M('flow');
	$add_data['source'] = 'Auction';
	$add_data['sourceid'] = $_GET['id'];
	$btime = mktime(0,0,0,date('m'),1,date('Y'));
	$etime = mktime(24,0,0,date('m'),date('t'),date('Y'));
	$add_data['create_time'] = array('between',array($btime,$etime));
	$count = $model->where($add_data)->count();
	if($count<300){
	  $add_data['product_id'] = $vo['product_id'];
	  $add_data['create_time'] = time();
	  $add_data['ip'] = $_SERVER['REMOTE_ADDR'];
	  $add_data['member_id'] = $this->user['id'] ? $this->user['id'] : 0;
	  $fid = $model->add($add_data);
	  if($count%100==0)$this->update_weight('Auction',$vo['id']);
	}
	//权重
	$wmodel = M('Weight');
	$w_data['source'] = 'Auction';
	$w_data['sourceid'] = $_GET['id'];
	$weight = $wmodel->where($w_data)->find();
	if(!$weight){
	  $weight['comment_count'] = 0;
	  $weight['pv_count'] = 1;
	  $weight['product_id'] = $vo['product_id'];
	  $weight['like_count'] = 0;
	  $weight['favorite_count'] = 0;
	  $weight['source'] = 'Auction';
	  $weight['sourceid'] = $_GET['id'];
	  $weight['create_time'] = time();
	  $wmodel->add($weight);
	}else{
	  if($fid){
	    $wmodel->where($w_data)->setInc('pv_count',1);
	  }
	}
	$this->assign('weight',$weight);
	//分享
	if($_GET['share_id']){
	  $this->assign('share_id',$_GET['share_id']);
	}else{
	  $this->assign('share_id',0);
	}
	//竞价记录
	$almodel = M('AuctionLog');
	$al_data['aid'] = $_GET['id'];
	$al_count = $almodel->where($al_data)->count();
	$al_list = $almodel->where($al_data)->limit(10)->order('id desc')->select();
	$this->assign('al_list',$al_list);
	$this->assign('al_count',$al_count);
	//保证金
	if($this->user){
	  if($vo['bond']){
	    $mwlmodel = M('member_wallet_log');
	    $mwl_data['member_id'] = $this->user['id'];
	    $mwl_data['pay_type'] = 1;
	    $mwl_data['aid'] = $vo['id'];
	    $mwl_count = $mwlmodel->where($mwl_data)->count();
	    $this->assign('mwl_count',$mwl_count);	  
	  }else{
	    $this->assign('mwl_count',1);
	  }
	}
	//看过的
	$looks = $this->random();
	$this->assign('looks',$looks);
	//价格走势
	$model = M('PaySystem');
	$pay_data['product_id'] = $vo['product_id'];
	$pay_list = $model->where($pay_data)->order('id desc')->select();
	if($pay_list){
	  krsort($pay_list);
	  foreach($pay_list as $val){
		$categories.= "'".$val['m'].'/'.$val['d']."',";
		$pays .= $val['pay'].',';
	  }
	  $categories = substr($categories,0,-1);
	  $pays = substr($pays,0,-1);
	}
	//echo $_SESSION['form_url'];
	//dump($_SESSION);
	$this->assign('categories',$categories);
	$this->assign('pays',$pays);
	$title = $vo['product_name'].'-竞拍- '.C('site_name');
	$keywords = $product['keywords'];
	$description = $product['description'];
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
	if($vo['type']==2){
	  $this->display('low_detail');
	  exit;
	}
	$this->display();
  }

  //余额支付
  public function payment(){
	if(!$this->user){
	  $this->error('请先登录!');
	}
	$model = M('auction');
	$data['id'] = $_POST['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
	  $this->error('拍品不存在!');
	}
	if($vo['endtime']<=time()){
	  $this->error('拍品已结束!');
	}
	$time = time();
	$model->startTrans();//启用事务
	if($_POST['paytype'] == 1){
		  //记录账号
		  $rmodel = M('Record');
		  $rdata['member_id'] = $this->user['id'];
		  $rdata['member_name'] = $this->user['username'];
		  $rdata['realname'] = $this->user['realname'];
		  $rdata['order_id'] = build_order_no($this->user['id']);
		  $rdata['pay_type'] = 2;
		  $rdata['payment_mode'] = '1';
		  $rdata['payment_company'] = '支付宝';
		  $rdata['pay_order_id'] = build_order_no($this->user['id']);
		  $rdata['buyer'] = $this->user['username'];
		  $rdata['content'] = '拍卖订金，支付宝支付;拍品:'.$vo['product_name'];
		  $rdata['balance'] = $this->user['balance'];
		  $rdata['pay'] = $vo['bond'];
		  $rdata['create_time'] = $time;
		  $rdata['status'] = 1;
		  $rdata['ip'] = $_SERVER['REMOTE_ADDR'];
		  $rdata['pay_time'] = time();
		  $result = $rmodel->add($rdata);
		  if($result){
			$model->commit();
		  }else{
			$model->rollback();
			$this->error ('支付失败!');
			exit;
		  }
		//支付宝
		require_once("./Alipay/alipay.config.php");
		require_once("./Alipay/lib/alipay_submit.class.php");
		require_once("./Alipay/Alipay.class.php");
		//支付信息
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$payment_type = "1";
		$exter_invoke_ip = $_SERVER['REMOTE_ADDR'];
		$out_trade_no = $rdata['order_id'];
		$subject = $content = '拍卖订金,拍品:'.$vo['product_name'];
		$total_fee = $vo['bond'];
		$total_fee = 0.01;
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url.'/recharge',
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
				"extra_common_param"=>'pay_type|1,t|2,aid|'.$vo['id']  //参数
		);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
		echo $html_text;
	}else if($_POST['paytype'] == 0){
	  if($this->user['balance']<$vo['bond']){
	    $this->error('余额不足!');
	  }
	  $mwlmodel = M('MemberWalletLog');
	  $mwl_data['member_id'] = $this->user['id'];
	  $mwl_data['pay_type'] = 1;
	  $mwl_data['aid'] = $vo['id'];
	  $mwl_count = $mwlmodel->where($mwl_data)->count();
	  if($mwl_count>0){
	    $this->error('你已交保证金!');
	  }
	  //记录账号
	  $rmodel = M('Record');
	  $rdata['member_id'] = $this->user['id'];
	  $rdata['member_name'] = $this->user['username'];
	  $rdata['realname'] = $this->user['realname'];
	  $rdata['order_id'] = build_order_no($this->user['id']);
	  $rdata['pay_type'] = 2;
	  $rdata['payment_mode'] = '1';
	  $rdata['payment_company'] = $this->configs['company_name'];
	  $rdata['pay_order_id'] = build_order_no($this->user['id']);
	  $rdata['buyer'] = $this->user['username'];
	  $rdata['content'] = '订金,可用余额转入冻结资金.';
	  $rdata['balance'] = $this->user['balance'] - $vo['bond'];
	  $rdata['pay'] = $vo['bond'];
	  $rdata['create_time'] = $time;
	  $rdata['status'] = 1;
	  $rdata['ip'] = $_SERVER['REMOTE_ADDR'];
	  $rdata['pay_time'] = time();
	  $result = $rmodel->add($rdata);
	  //修改金额
	  $mw_model = M('MemberWallet');
	  $mw_data['member_id'] = $this->user['id'];
	  if($result)$result = $mw_model->where($mw_data)->setInc('frozen',$vo['bond']);// 用户冻结资金增加
	  if($result)$result = $mw_model->where($mw_data)->setDec('balance',$vo['bond']);// 可用资金减
	  //添加冻结记录
	  $mwl_data['title'] = '拍卖订金';
	  $mwl_data['pay'] = $vo['bond'];
	  $mwl_data['content'] = '保证金:'.$vo['bond'].';艺术品:'.$vo['product_name'];
	  $mwl_data['create_time'] = time();
	  if($result)$mwlmodel->add($mwl_data);
	  if($result){
		$model->commit();
		$this->success ('支付成功!');
	  }else{
	    $model->rollback();
		$this->error ('支付失败!');		
	  }
	}
  }

  //出价
  public function bid(){
    $model = M('Auction');
	$data['id'] = $_POST['id'];
	$vo = $model->field('id,status,starttime,endtime,product_id,product_name,startprice,addprice,auctioncount')->where($data)->find();
	if(!$vo){
	  $msg['status'] = 0;
	  $msg['notice'] = '拍品不存在！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['starttime']>time()){
	  $msg['status'] = 0;
	  $msg['notice'] = '还未开始拍卖！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['endtime']<=time()){
	  $msg['status'] = 0;
	  $msg['notice'] = '拍卖已结束！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['type']==2){
	  $msg['status'] = 0;
	  $msg['notice'] = '出错！';
	  echo  json_encode($msg);exit;		
	}
	$mwlmodel = M('MemberWalletLog');
	$mwl_data['member_id'] = $this->user['id'];
	$mwl_data['pay_type'] = 1;
	$mwl_data['aid'] = $vo['id'];
	$mwl_count = $mwlmodel->where($mwl_data)->count();
	if($mwl_count==0){
	  $msg['status'] = 0;
	  $msg['notice'] = '请先付保证金！';
	  echo  json_encode($msg);exit;	
	}
	if(($_POST['pay']-$vo['startprice'])%$vo['addprice']!=0 || $_POST['pay']<$vo['startprice']){
	  $msg['status'] = 0;
	  $msg['notice'] = '出价不对！';
	  echo  json_encode($msg);exit;		  
	}
	$al_model = M('AuctionLog');
	$al_data['aid'] = $_POST['id'];
	$al_vo = $al_model->field('member_id,bidprice')->order('id desc')->where($al_data)->find();
	if($al_vo['member_id']==$this->user['id']){
	  $msg['status'] = 0;
	  $msg['notice'] = '你是最高出价者！';
	  echo  json_encode($msg);exit;		
	}
	if($al_vo['bidprice']>=$_POST['pay']){

	  $msg['status'] = 0;
	  $msg['notice'] = '已有人比你出更高价格！';
	  echo  json_encode($msg);exit;
	}
	$model->startTrans();//启用事务
	//添加记录
	$al_add['aid'] = $_POST['id'];
	$al_add['product_id'] = $vo['product_id'];
	$al_add['product_name'] = $vo['product_name'];
	$al_add['member_id'] = $this->user['id'];
	$al_add['member_name'] = $this->user['username'];
	$al_add['realname'] = $this->user['realname'];
	//分享处理
	if($_POST['sid']){
	  $al_add['share_id'] = $_POST['sid']!=$this->user['id'] ? $_POST['sid'] : 0;
	}else{
	  $al_add['share_id'] = $_POST['share_id'] ? $_POST['share_id'] : 0;
	}
	$al_add['ip'] = $_SERVER['REMOTE_ADDR'];
	$al_add['address'] = ipfrom($_SERVER['REMOTE_ADDR']);
	$al_add['bidprice'] = $_POST['pay'];
	$al_add['create_time'] = time();
	$result = $al_model->add($al_add);
	//echo $al_model->getlastsql();exit;
	//修改状态
	$sdata['auctioncount'] = $vo['auctioncount']+1;
	$sdata['realprice'] = $_POST['pay'];
	$sdata['auctionuid'] = $this->user['id'];
	$sdata['auctionuname'] = $this->user['username'];
	if($result)$result = $model->where($data)->save($sdata);
	if($result){
	  $model->commit();
	  $msg['status'] = 1;
	  $msg['notice'] = '出价成功！';
	  echo  json_encode($msg);exit;	
	}else{
	  $model->rollback();
	  $msg['status'] = 0;
	  $msg['notice'] = '出错！';
	  echo  json_encode($msg);exit;	
	}
  }

  //出价
  public function low_bid(){
	if($_POST['pay']<1){
	  $msg['status'] = 0;
	  $msg['notice'] = '出价不能低于1元！';
	  echo  json_encode($msg);exit;	
	}
    $model = M('Auction');
	$lmodel = M('AuctionLog');
	$rmodel = M('record');
	$mmodel = M('Member');
	$mwmodel = M('MemberWallet');
	$mwlmodel = M('MemberWalletLog');
	$data['id'] = $_POST['id'];
	$vo = $model->field('id,status,starttime,endtime,product_id,product_name,startprice,addprice,auctioncount,realprice,bond,user_id')->where($data)->find();
	if(!$vo){
	  $msg['status'] = 0;
	  $msg['notice'] = '拍品不存在！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['starttime']>time()){
	  $msg['status'] = 0;
	  $msg['notice'] = '还未开始拍卖！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['endtime']<=time()){
	  $msg['status'] = 0;
	  $msg['notice'] = '拍卖已结束！';
	  echo  json_encode($msg);exit;		
	}
	if($vo['type']==1){
	  $msg['status'] = 0;
	  $msg['notice'] = '出错！';
	  echo  json_encode($msg);exit;		
	}
	$mwlmodel = M('MemberWalletLog');
	$mwl_data['member_id'] = $this->user['id'];
	$mwl_data['pay_type'] = 1;
	$mwl_data['aid'] = $vo['id'];
	$mwl_count = $mwlmodel->where($mwl_data)->count();
	if($mwl_count==0){
	  $msg['status'] = 0;
	  $msg['notice'] = '请先付保证金！';
	  echo  json_encode($msg);exit;	
	}
	if(($this->user['frozen']+$this->user['balance'])<1){
	  $msg['status'] = 0;
	  $msg['notice'] = '余额不足！';
	  echo  json_encode($msg);exit;
	}
	if($this->user['id']==$vo['auctionuid']){
	  $msg['status'] = 0;
	  $msg['notice'] = '目前您领先,无需再出价！';
	  echo  json_encode($msg);exit;
	}
	$model->startTrans();//启用事务
	//添加记录
	$al_model = M('AuctionLog');
	$al_add['aid'] = $_POST['id'];
	$al_add['product_id'] = $vo['product_id'];
	$al_add['product_name'] = $vo['product_name'];
	$al_add['member_id'] = $this->user['id'];
	$al_add['member_name'] = $this->user['username'];
	$al_add['realname'] = $this->user['realname'];
	$al_add['ip'] = $_SERVER['REMOTE_ADDR'];
	$al_add['address'] = ipfrom($_SERVER['REMOTE_ADDR']);
	$al_add['bidprice'] = $_POST['pay'];
	$al_add['create_time'] = time();
	$result = $al_model->add($al_add);
	//竞拍失败
	if($_POST['pay']>=$vo['realprice']){

	}else{
	  //竞价成功
	  $sdata['auctioncount'] = $vo['auctioncount']+1;
	  $sdata['realprice'] = $_POST['pay'];
	  $sdata['auctionuid'] = $this->user['id'];
	  $sdata['auctionuname'] = $this->user['username'];
	  if($result)$result = $model->where($data)->save($sdata);
	}
	//扣除竞拍金
	  //竞价次数
	  $l_data['aid']  = $_POST['id'];
	  $count = $lmodel->where($l_data)->count();
	  //冻结金还剩多少
	  if($vo['bond']-$count>1){
		$mw_wdata['member_id'] = $this->user['id'];
		if($result)$result=$mwmodel->where($mw_wdata)->setDec('frozen',1);
		$mwl_add_data['title'] = '订金扣除';
		$mwl_add_data['member_id'] = $this->user['id'];
		$mwl_add_data['pay'] = $_POST['pay'];
		$mwl_add_data['aid'] = $_POST['id'];
		$mwl_add_data['pay_type'] = 3;
		$mwl_add_data['content'] = '低价者得,拍品：'.$val['product_name'].'，竞拍失败扣除订金1元';
		$mwl_add_data['create_time'] = time();
		if($result)$result = $mwlmodel->add($mwl_add_data);
	  }else{
		$mw_wdata['member_id'] = $this->user['id'];
	    if($result)$result=$mwmodel->where($mw_wdata)->setDec('balance',1);
		$rdata['member_id'] = $this->user['id'];
		$rdata['member_name'] = $this->user['username'];
		$rdata['realname'] = $this->user['realname'];
		$rdata['pay'] = 1;
		$rdata['balance'] = $this->user['balance']-1;
		$rdata['order_id'] = build_order_no($this->user['id']);
		$rdata['pay_order_id'] = build_order_no($this->user['id']);
		$rdata['payment_company'] = C('company_name');
		$rdata['pay_type'] = 2;
		$rdata['content'] = '低价者得,拍品：'.$val['product_name'].'，竞拍失败扣除1元';
		if($result)$result = $rmodel->add($rdata);
	  }
	  //转钱进入卖家账户
	  if($vo['user_id']){
		  //查找用户钱包
		  $mw_wdata['member_id'] = $vo['user_id'];
	      if($result)$result=$mwmodel->where($mw_wdata)->setInc('balance',1);
		  $m_data['a.id'] = $vo['user_id'];
		  $wallet_vo = $mmodel->alias('a')->join('`'.C('DB_PREFIX').'member_wallet` as b on a.id=b.member_id')->field('a.id,logo,email,utype,password,username,realname,tel,pv_id,ct_id,province,city,create_time,last_login_time,balance,frozen,b.update_time,cp')->where($m_data)->find();
		  $rdata['member_id'] = $wallet_vo['id'];
		  $rdata['member_name'] = $wallet_vo['username'];
		  $rdata['realname'] = $wallet_vo['realname'];
		  $rdata['pay'] = 1;
		  $rdata['balance'] = $wallet_vo['balance']+1;
		  $rdata['order_id'] = build_order_no($this->user['id']);
		  $rdata['pay_order_id'] = build_order_no($this->user['id']);
		  $rdata['payment_company'] = C('company_name');
		  $rdata['pay_type'] = 5;
		  $rdata['content'] = '低价者得,拍品：'.$val['product_name'].'，竞拍收入1元';
		  if($result)$result = $rmodel->add($rdata);
	  }
	  //echo $rmodel->getlastsql();exit;
	if($result){
	  $model->commit();
	  if($_POST['pay']>=$vo['realprice']){
	    $msg['status'] = 1;
	    $msg['notice'] = '很遗憾，您出价太高了！';
	    echo  json_encode($msg);exit;	
	  }else{
	    $msg['status'] = 1;
	    $msg['notice'] = '出价成功，领先！';
	    echo  json_encode($msg);exit;		  
	  }
	}else{
	  $model->rollback();
	  $msg['status'] = 0;
	  $msg['notice'] = '出错！';
	  echo  json_encode($msg);exit;	
	}
  }


}