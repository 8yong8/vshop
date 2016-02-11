<?php 
namespace Home\Controller;
use Think\Controller;
class DatasController extends CommonController{

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Datas');
	$this->rdb = D('Record');
	$this->odb = D('Order');
	$this->oddb = D('order_detail');
	$this->mdb = D('Member');
	$this->mcdb = D('Member_coupon');
	$this->cdb = D('Collect');
	$this->pdb = D('Product');
	$this->adb = D('Article');
	$datas_menu = array(
	  0 => array(
		'name'=>'销售数据',
		'action'=>'index',
	  ),
	  1 => array(
		'name'=>'订单数据',
		'action'=>'order',
	  ),
	  2 => array(
		'name'=>'会员数据',
		'action'=>'member',
	  ),
	  3 => array(
		'name'=>'其他数据',
		'action'=>'other',
	  ),		
	); 
	$this->assign('datas_menu',$datas_menu);
  }

  /**
   * 默认页
   */
  public function index(){
	  if($_GET['uid']){
		$data['member_id'] = $_GET['uid'];
		$this->assign('uid',$_GET['uid']);
	  }
	  if($_GET['btime'] && $_GET['etime']){
		$data['create_time'] = array(array('gt',strtotime($_GET['btime'])),array('lt',strtotime($_GET['etime'])+86400));
		$this->assign('btime',$_GET['btime']);
		$this->assign('etime',$_GET['etime']);
		$days = ceil((strtotime($_GET['etime'])-strtotime($_GET['btime']))/86400);
	  }elseif($_GET['btime']){
		$data['create_time'] = array('gt',strtotime($_GET['btime']));
		$this->assign('btime',$_GET['btime']);
		$mdata['create_time'] = array('gt',strtotime($_GET['btime']));
		$days = ceil((strtotime($_GET['etime'])-time())/86400);
	  }elseif($_GET['etime']){
		$data['create_time'] = array('lt',strtotime($_GET['etime'])+86400);
		$this->assign('etime',$_GET['etime']);
		$days = 100;
	  }else{
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  }
	  if($days && $days>30*2){
		$data['type'] = 3;//月数据
	  }else if($days && $days>10){
	    $data['type'] = 2;//周数据
	  }else{
		$data['type'] = 1;//天数据	    
	  }
	  if($_GET['days']==7){
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
	  }else if($_GET['days']==30){
		//30天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*30;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }else if($_GET['days']==31){
		//本月
		$btime = mktime(0,0,0,date('m'),1,date('Y'));
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }
	  $btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-86400;
	  $etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
	  //昨日收入
	  $data3['pay_time'] = array(array('gt',$btime-86400),array('lt',$etime-86400));
	  $y = $this->db->where($data3)->find();
	  $yday = unserialize($y['info']);
	  $data2['pay_time'] = array(array('gt',$btime),array('lt',$etime));
	  //今日总交易额
	  $real_amount = $this->rdb->where($data2)->sum('amount');
	  $real_amount = $real_amount ? $real_amount : '0.00';
	  $this->assign('t_real_amount',$real_amount);
	  //$real_amount = 1723;
	  if($yday){
	    if($real_amount > $yday['real_amount']){
		  $sell_gain = ceil(($real_amount-$yday['real_amount'])/$yday['real_amount']*100);
		}else{
		  $sell_gain = '-'.ceil(($yday['real_amount']-$real_amount)/$yday['real_amount']*100);
		}
	  }else{
	    $sell_gain = 0;
	  }
	  $this->assign('sell_gain',$sell_gain);
	  //今日订单总数
	  unset($data2);
	  $data2['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  $t_total_order = $this->odb->where($data2)->count();
	  $t_total_order = $t_total_order ? $t_total_order : '0';
	  $this->assign('t_total_order',$t_total_order);
	  //今日已支付订单数
	  $data2['pay_status'] = 1;
	  $pay_order = $this->odb->where($data2)->count();
	  $pay_order = $pay_order ? $pay_order : '0';
	  $this->assign('t_pay_order',$pay_order);
	  //数据
	  //$count = $this->db->where($data)->count();exit;
	  $list = $this->db->where($data)->order('id asc')->select();
	  //echo $this->db->getlastsql();exit;
	  $count = count($list);
	  $times = '';
	  $pay_order = 0;
	  $pay_amount = 0;
	  foreach($list as $key=>$val){
		if($key==0){
		  $subtext = date('Y.m.d',$val['create_time']).' ~ ';
		}
		$array = unserialize($val['info']);
		$list[$key] = array_merge($list[$key],$array);
		$pay_order += $array['pay_order'];
		$pay_amount += $array['real_amount'];
		$add_data[0][] = $array['real_amount'];
		$add_data[1][] = $array['xf_amount'];
		$add_data[2][] = $array['cz_amount'];
		$add_data[3][] = $array['tx_amount'];
		$add_data[4][] = $array['tk_amount'];
		if($key+1!=$count){
		  $times .= "'".date('Y-m-d',$val['create_time'])."',";
		}else{
		  $times .= "'".date('Y-m-d',$val['create_time'])."'";
		}
		if($key+1==$count){
		  $subtext .= date('Y.m.d',$val['create_time']);
		}
	  }
	  $this->assign('pay_order',$pay_order);
	  $this->assign('pay_amount',$pay_amount);
	  $this->assign('list',$list);
	  $this->assign('times',$times);//x坐标
	  $this->assign('subtext',$subtext);//副标题
	  $names[] = '销售额';
	  $names[] = '消费';
	  $names[] = '充值';
	  $names[] = '提现';
	  $names[] = '退款';
	  $datastr = '';
	  $names_str = '';
	  $count = count($names);
	  foreach($names as $key=>$name){
		$datastr .= '{';
		$datastr .= "name:'".$name."',";
		$datastr .= "type:'bar',";
		//$datastr .= "stack: '总量',";
		$datastr .= 'itemStyle: {normal: {label: {show: true,textStyle: {color: "black"}}}},';
		$datastr .= "data: [".implode(',',$add_data[$key])."]";
		if($key+1!=$count){
		  $datastr .= '},';
		  $names_str .= "'".$name."',";
		}else{
		  $datastr .= '}';
		  $names_str .= "'".$name."'";
		}
		$this->assign('names',$names_str);
	  }
	  $this->assign('datastr',$datastr);
	  $this->display();
  }

  /**
   * 订单数据
   */
  public function order(){
	  $model = D('Order');
	  if($_GET['seller_id']){
		$data['user_id'] = $_GET['uid'];
		$this->assign('uid',$_GET['uid']);
	  }
	  $data['type'] = 1;//天数据
	  if($_GET['btime'] && $_GET['etime']){
		$data['create_time'] = array(array('gt',strtotime($_GET['btime'])),array('lt',strtotime($_GET['etime'])+86400));
		$this->assign('btime',$_GET['btime']);
		$this->assign('etime',$_GET['etime']);
	  }elseif($_GET['btime']){
		$data['create_time'] = array('gt',strtotime($_GET['btime']));
		$this->assign('btime',$_GET['btime']);
		$mdata['create_time'] = array('gt',strtotime($_GET['btime']));
	  }elseif($_GET['etime']){
		$data['create_time'] = array('lt',strtotime($_GET['etime'])+86400);
		$this->assign('etime',$_GET['etime']);
	  }else{
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  }
	  $btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-86400;
	  $etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
	  //昨日收入
	  $data3['pay_time'] = array(array('gt',$btime-86400),array('lt',$etime-86400));
	  $y = $this->db->where($data3)->find();
	  $yday = unserialize($y['info']);
	  $data2['pay_time'] = array(array('gt',$btime),array('lt',$etime));
	  //今日总交易额
	  $real_amount = $this->rdb->where($data2)->sum('amount');
	  $real_amount = $real_amount ? $real_amount : '0.00';
	  $this->assign('t_real_amount',$real_amount);
	  //$real_amount = 1723;
	  if($yday){
	    if($real_amount > $yday['real_amount']){
		  $sell_gain = ceil(($real_amount-$yday['real_amount'])/$yday['real_amount']*100);
		}else{
		  $sell_gain = '-'.ceil(($yday['real_amount']-$real_amount)/$yday['real_amount']*100);
		}
	  }else{
	    $sell_gain = 0;
	  }
	  $this->assign('sell_gain',$sell_gain);
	  //今日订单总数
	  $t_total_order = $this->odb->where($data2)->count();
	  $t_total_order = $t_total_order ? $t_total_order : '0';
	  $this->assign('t_total_order',$t_total_order);
	  //今日已支付订单数
	  $data2['pay_status'] = 1;
	  $pay_order = $this->odb->where($data2)->count();
	  $pay_order = $pay_order ? $pay_order : '0';
	  $this->assign('t_pay_order',$pay_order);
	  //数据
	  $list = $this->db->where($data)->order('id asc')->select();
	  $count = count($list);
	  $times = '';
	  $pay_order = 0;
	  $pay_amount = 0;
	  $total_order = 0;
	  $pay_order = 0;
	  $confirm_order = 0;
	  $cancel_order = 0;
	  $refund_order = 0;
	  foreach($list as $key=>$val){
		if($key==0){
		  $subtext = date('Y.m.d',$val['create_time']).' ~ ';
		}
		$array = unserialize($val['info']);
		$list[$key] = array_merge($list[$key],$array);
		$total_order += $array['total_order'];
		$pay_order += $array['pay_order'];
		$confirm_order += $array['confirm_order'];
		$cancel_order += $array['cancel_order'];
		$refund_order += $array['refund_order'];
		$add_data[0][] = $array['total_order'];
		//$add_data[1][] = $array['pay_order'];
		$add_data[1][] = $array['confirm_order'];
		$add_data[2][] = $array['cancel_order'];
		$add_data[3][] = $array['refund_order'];
		if($key+1!=$count){
		  $times .= "'".date('Y-m-d',$val['create_time'])."',";
		}else{
		  $times .= "'".date('Y-m-d',$val['create_time'])."'";
		}
		if($key+1==$count){
		  $subtext .= date('Y.m.d',$val['create_time']);
		}
	  }
	  $this->assign('total_order',$total_order);
	  $this->assign('pay_order',$pay_order);
	  $this->assign('confirm_order',$confirm_order);
	  $this->assign('cancel_order',$cancel_order);
	  $this->assign('refund_order',$refund_order);
	  $this->assign('list',$list);
	  $this->assign('times',$times);//x坐标
	  $this->assign('subtext',$subtext);//副标题
	  $names[] = '总订单';
	  //$names[] = '已支付订单';
	  $names[] = '已确认订单';
	  $names[] = '取消订单';
	  $names[] = '退款订单';
	  $datastr = '';
	  $names_str = '';
	  $count = count($names);
	  foreach($names as $key=>$name){
		$datastr .= '{';
		$datastr .= "name:'".$name."',";
		$datastr .= "type:'bar',";
		//$datastr .= "stack: '总量',";
		$datastr .= 'itemStyle: {normal: {label: {show: true,textStyle: {color: "black"}}}},';
		$datastr .= "data: [".implode(',',$add_data[$key])."]";
		if($key+1!=$count){
		  $datastr .= '},';
		  $names_str .= "'".$name."',";
		}else{
		  $datastr .= '}';
		  $names_str .= "'".$name."'";
		}
		$this->assign('names',$names_str);
	  }
	  $this->assign('datastr',$datastr);
	  $this->display();
  }

  /**
   * 会员数据
   */
  public function member(){
	  $model = D('Member');
	  $data['type'] = 1;//天数据
	  if($_GET['btime'] && $_GET['etime']){
		$data['create_time'] = array(array('gt',strtotime($_GET['btime'])),array('lt',strtotime($_GET['etime'])+86400));
		$this->assign('btime',$_GET['btime']);
		$this->assign('etime',$_GET['etime']);
	  }elseif($_GET['btime']){
		$data['create_time'] = array('gt',strtotime($_GET['btime']));
		$this->assign('btime',$_GET['btime']);
		$mdata['create_time'] = array('gt',strtotime($_GET['btime']));
	  }elseif($_GET['etime']){
		$data['create_time'] = array('lt',strtotime($_GET['etime'])+86400);
		$this->assign('etime',$_GET['etime']);
	  }else{
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  }
	  if($days && $days>30*2){
		$data['type'] = 3;//月数据
	  }else if($days && $days>10){
	    //$data['type'] = 2;//周数据
	  }else{
		$data['type'] = 1;//天数据	    
	  }
	  if($_GET['days']==7){
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
	  }else if($_GET['days']==30){
		//30天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*30;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }else if($_GET['days']==31){
		//本月
		$btime = mktime(0,0,0,date('m'),1,date('Y'));
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }
	  $btime = mktime(0,0,0,date('m'),date('d'),date('Y'));
	  $etime = mktime(24,0,0,date('m'),date('d'),date('Y'));
	  //前天注册总数
	  $data3['create_time'] = array(array('gt',$btime-86400),array('lt',$etime-86400));
	  $y = $this->db->where($data3)->find();
	  $yday = unserialize($y['info']);
	  //今日注册会员总数
	  $data2['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  $t_reg = $this->mdb->where($data2)->count();
	  $t_reg = $t_reg ? $t_reg : '0';
	  $this->assign('t_reg',$t_reg);
	  //dump($yday);exit;
	  if($yday){
	    if($t_reg > $yday['reg_member']){
		  $sell_gain = ceil(($t_reg-$yday['reg_member'])/$yday['reg_member']*100);
		}else{
		  $sell_gain = '-'.ceil(($yday['reg_member']-$t_reg)/$yday['reg_member']*100);
		}
		$y_reg_member = $yday['reg_member'];
	  }else{
	    $sell_gain = 100;
		$y_reg_member = 0;
	  }
	  $this->assign('y_reg_member',$y_reg_member);//昨日注册会员总数
	  $this->assign('sell_gain',$sell_gain);
	  $data2['from'] = 'app';
	  $t_reg_app = $this->mdb->where($data2)->count();
	  //echo $this->mdb->getlastsql();exit;
	  $this->assign('t_reg_app',$t_reg_app);
	  $data2['from'] = 'wap';
	  $t_reg_wap = $this->mdb->where($data2)->count();
	  $this->assign('t_reg_wap',$t_reg_wap);
	  $data2['from'] = 'pc';
	  $t_reg_pc = $this->mdb->where($data2)->count();
	  $this->assign('t_reg_pc',$t_reg_pc);
	  
	  //数据
	  $list = $this->db->where($data)->order('id asc')->select();
	  //echo $this->db->getlastsql();
	  //dump($list);exit;
	  $count = count($list);
	  $times = '';
	  foreach($list as $key=>$val){
		if($key==0){
		  $subtext = date('Y.m.d',$val['create_time']).' ~ ';
		}
		$array = unserialize($val['info']);
		$list[$key] = array_merge($list[$key],$array);
		$reg_member += $array['reg_member'];
		$reg_app += $array['reg_app'];
		$reg_wap += $array['reg_wap'];
		$reg_pc += $array['reg_pc'];
		if($key+1!=$count){
		  $times .= "'".date('Y-m-d',$val['create_time'])."',";
		}else{
		  $times .= "'".date('Y-m-d',$val['create_time'])."'";
		}
		if($key+1==$count){
		  $subtext .= date('Y.m.d',$val['create_time']);
		}
	  }
	  $this->assign('reg_member',$reg_member);
	  $this->assign('reg_app',$reg_app);
	  $this->assign('reg_wap',$reg_wap);
	  $this->assign('reg_pc',$reg_pc);
	  $this->assign('list',$list);
	  $this->assign('times',$times);//x坐标
	  $this->assign('subtext',$subtext);//副标题
	  //$names[] = '注册会员总数';
	  $names[] = 'APP来源';
	  $names[] = 'WAP来源';
	  $names[] = 'PC来源';
	  $values[] = $reg_app;
	  $values[] = $reg_wap;
	  $values[] = $reg_pc;
	  $datastr = '';
	  $names_str = '';
	  $count = count($names);
	  $datastr .= "{name:'注册会员',";
	  $datastr .= "type:'pie',";
	  $datastr .= "radius : '55%',";
	  $datastr .= " data:[";
	  foreach($names as $key=>$name){
		$datastr .= '{';
		$datastr .= "value:'".$values[$key]."',";
		$datastr .= "name:'".$name."'";

		if($key+1!=$count){
		  $datastr .= '},';
		  $names_str .= "'".$name."',";
		}else{
		  $datastr .= '}';
		  $names_str .= "'".$name."'";
		}
		$this->assign('names',$names_str);
	  }
	  $datastr .= "]}";
	  $this->assign('datastr',$datastr);
	  $this->display();
  }

  /**
   * 其他数据
   */
  public function other(){
	  if($_GET['seller_id']){
		$data['user_id'] = $_GET['uid'];
		$this->assign('uid',$_GET['uid']);
	  }
	  $data['type'] = 1;//天数据
	  if($_GET['btime'] && $_GET['etime']){
		$data['create_time'] = array(array('gt',strtotime($_GET['btime'])),array('lt',strtotime($_GET['etime'])+86400));
		$this->assign('btime',$_GET['btime']);
		$this->assign('etime',$_GET['etime']);
	  }elseif($_GET['btime']){
		$data['create_time'] = array('gt',strtotime($_GET['btime']));
		$this->assign('btime',$_GET['btime']);
		$mdata['create_time'] = array('gt',strtotime($_GET['btime']));
	  }elseif($_GET['etime']){
		$data['create_time'] = array('lt',strtotime($_GET['etime'])+86400);
		$this->assign('etime',$_GET['etime']);
	  }else{
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  }
	  if($days && $days>30*2){
		$data['type'] = 3;//月数据
	  }else if($days && $days>10){
	    //$data['type'] = 2;//周数据
	  }else{
		$data['type'] = 1;//天数据	    
	  }
	  if($_GET['days']==7){
		//7天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*7;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
	  }else if($_GET['days']==30){
		//30天
		$btime = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24*30;
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }else if($_GET['days']==31){
		//本月
		$btime = mktime(0,0,0,date('m'),1,date('Y'));
		$etime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
		$this->assign('days',$_GET['days']);
		//$data['type'] = 2;//周数据
	  }
	  $btime = mktime(0,0,0,date('m'),date('d'),date('Y'));
	  $etime = mktime(24,0,0,date('m'),date('d'),date('Y'));
	  $data2['create_time'] = array(array('gt',$btime),array('lt',$etime));
	  //今日产品数
	  $t_prod_count = $this->pdb->where($data2)->count();
	  $this->assign('t_prod_count',$t_prod_count);
	  //今日文章数
	  $model = M('Article');
	  $t_art_count = $this->adb->where($data2)->count();
	  $this->assign('t_art_count',$t_art_count);
	  //今日优惠券使用数量
	  $data3['status'] = 1;
	  $data3['use_time'] = array(array('gt',$btime),array('lt',$etime));
	  $t_coupon_use = $this->mcdb->where($data3)->count();
	  $this->assign('t_coupon_use',$t_coupon_use);
	  //收藏数
	  $t_collect_count = $this->cdb->where($data2)->count();
	  $this->assign('t_collect_count',$t_collect_count);
	  //数据
	  $list = $this->db->where($data)->order('id asc')->select();
	  $count = count($list);
	  $times = '';
	  foreach($list as $key=>$val){
		if($key==0){
		  $subtext = date('Y.m.d',$val['create_time']).' ~ ';
		}
		$array = unserialize($val['info']);
		$list[$key] = array_merge($list[$key],$array);
		$prod_count += $array['prod_count'];
		$art_count += $array['art_count'];
		$coupon_use += $array['coupon_use'];
		$collect_count += $array['collect_count'];
		$add_data[0][] = $array['total_order'];
		$add_data[1][] = $array['confirm_order'];
		$add_data[2][] = $array['cancel_order'];
		$add_data[3][] = $array['refund_order'];
		if($key+1!=$count){
		  $times .= "'".date('Y-m-d',$val['create_time'])."',";
		}else{
		  $times .= "'".date('Y-m-d',$val['create_time'])."'";
		}
		if($key+1==$count){
		  $subtext .= date('Y.m.d',$val['create_time']);
		}
	  }
	  $this->assign('prod_count',$prod_count);
	  $this->assign('art_count',$art_count);
	  $this->assign('coupon_use',$coupon_use);
	  $this->assign('collect_count',$collect_count);
	  $this->assign('list',$list);
	  $this->assign('times',$times);//x坐标
	  $this->assign('subtext',$subtext);//副标题
	  $names[] = '产品新增数';
	  $names[] = '文章新增数';
	  $names[] = '优惠券使用数';
	  $names[] = '收藏数';
	  $datastr = '';
	  $names_str = '';
	  $count = count($names);
	  foreach($names as $key=>$name){
		$datastr .= '{';
		$datastr .= "name:'".$name."',";
		$datastr .= "type:'bar',";
		//$datastr .= "stack: '总量',";
		$datastr .= 'itemStyle: {normal: {label: {show: true,textStyle: {color: "black"}}}},';
		$datastr .= "data: [".implode(',',$add_data[$key])."]";
		if($key+1!=$count){
		  $datastr .= '},';
		  $names_str .= "'".$name."',";
		}else{
		  $datastr .= '}';
		  $names_str .= "'".$name."'";
		}
		$this->assign('names',$names_str);
	  }
	  $this->assign('datastr',$datastr);
	  $this->display();
  }

  /**
   * 搜索
   */
  public function search(){

  
  }

  /**
   * 分成产看
   */
  function look_cp(){
    $model = M('member');
	$data['utype'] = 3;
	if($_GET['id']){
	  $data['id'] = $_GET['id'];
	  $this->assign('id',$_GET['id']);
	}
	if($_GET['username']){
	  $data['username'] = $_GET['username'];
	  $this->assign('username',$_GET['username']);
	}
	//默认本月
	if(!$_GET['btime'] && !$_GET['etime']){
	  $_GET['btime'] = date('Y-m-1');
	  $_GET['etime'] = date('Y-m-'.date('t'));
	}
	//dump($_GET);exit;
	if($_GET['btime'] && $_GET['etime']){
	  $odata['pay_time'] = array(array('gt',strtotime($_GET['btime'])),array('lt',strtotime($_GET['etime'])+86400));
	  $this->assign('btime',$_GET['btime']);
	  $this->assign('etime',$_GET['etime']);
	}elseif($_GET['btime']){
	  $odata['pay_time'] = array('gt',strtotime($_GET['btime']));
	  $this->assign('btime',$_GET['btime']);
	}elseif($_GET['etime']){
	  $odata['pay_time'] = array('lt',strtotime($_GET['etime'])+86400);
	  $this->assign('etime',$_GET['etime']);
	}
	$count = $model->where($data)->count();
	//echo $model->getlastsql();
	$this->assign('count',$count);
	if($count>0){
	  //创建分页对象
	  $listRows = '20';
	  if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	  }
	  $p = new \My\Page($count,$listRows);

	  //$list = $model->field('id,utype,username,realname,create_time,status')->where($data)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select();
	  $list = $model->table('`'.C('DB_PREFIX').'member` as a')->join('`'.C('DB_PREFIX').'member_wallet` as b on a.id=b.member_id')->field('a.id,a.utype,a.username,a.realname,a.create_time,a.status,b.balance,b.frozen')->where($data)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $model->getlastsql();
	  //分页跳转的时候保证查询条件
	  foreach($map as $key=>$val) {
		if(is_array($val)) {
			foreach ($val as $t){
				$p->parameter	.= $key.'[]='.urlencode($t)."&";
			}
		}else{
			$p->parameter   .=   "$key=".urlencode($val)."&";        
		}
	  }
	  //分页显示
	  $page       = $p->Show();
	}
	//dump($list);exit;
	if($list){
	  $model = M('order');
	  foreach($list as $key=>$val){
		$odata['agent_id'] = $val['id'];
		$odata['status'] = 2;
		$total = $model->field('sum(total_price) as much')->where($odata)->find();
	    $list[$key]['much'] = $total['much'] ? $total['much'] : 0;
	  }
	}
	//dump($list);
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	$this->assign('list',       $list);
	$this->assign('sort',       $sort);
	$this->assign('order',      $order);
	$this->assign('sortImg',    $sortImg);
	$this->assign('sortType',   $sortAlt);
	$this->assign("page",       $page);
	$this->display();
  }

  /**
   * 分成详情
   */
  public function look_cp_detail(){
    $model = M('order');
	$map['agent_id'] = $_GET['member_id'];
	$this->_list($model,$map);
    $this->display();
  }

  /**
   * 数据模式
   */
  public function dp(){
	//exit;
    $model = M('Datas_detail');
	$fields = $model->getDbFields ();
	$model2 = M('Datas');
	$time = time();
	for($i=800;$i>0;$i--){
		$now_time = $time-60*60*24*$i;
		$data['real_amount'] = rand(120,4888.5);
		$data['xf_amount'] = rand(120,4888.5);
		$data['cz_amount'] = rand(120,4888.5);
		$data['tx_amount'] = rand(120,4888.5);
		$data['tk_amount'] = rand(120,4888.5);
		$data['total_order'] = rand(15,500);
		$data['pay_order'] = rand(22,88);
		$data['cancel_order'] = rand(10,48);
		$data['confirm_order'] = rand(20,488);
		$data['refund_order'] = rand(1,18);
		$data['reg_member'] = rand(100,4800);
		$data['reg_app'] = rand(150,1800);
		$data['reg_wap'] = rand(100,888);
		$data['reg_pc'] = rand(55,480);
		$data['art_count'] = rand(5,80);
		$data['prod_count'] = rand(5,80);
		$data['fdb_count'] = rand(5,80);
		$data['collect_count'] = rand(1,40);
		$data['coupon_rcv'] = rand(1,40);
		$data['coupon_use'] = rand(1,40);
		$data['create_time'] = $now_time;
		$model->add($data);
		$data2['type'] = 1;
		unset($data['create_time']);
		$data2['info'] = serialize($data);
		$data2['create_time'] = $now_time;
		$model2->add($data2);
		//周数据
		if(date('N',$now_time)==7){
		   unset($fields['id']);
		   unset($fields['create_time']);
		   //所有字和
		   $field_str = '';
		   foreach($fields as $field){
			 if($field=='id' || $field=='create_time'){
			   continue;
			 }
		     $field_str .= 'SUM(  `'.$field.'` ) AS '.$field.',';
		   }
		   $field_str = substr($field_str,0,-1);
		   $btime = mktime(0,0,0,date('m',$now_time-86400*6),1,date('Y',$now_time-86400*6));
		   $etime = mktime(24,0,0,date('m',$now_time),date('t',$now_time),date('Y',$now_time));
		   $data3['create_time'] = array(array('gt',$btime),array('lt',$etime));
		   $vo = $model->field($field_str)->where($data3)->find();
		   $data4['type'] = 2;
		   $data4['info'] = serialize($vo);
		   $data4['create_time'] = $now_time;
		   $model2->add($data4);		
		}
		//月数据
		if(date('j',$now_time)==date('t',$now_time)){
		   unset($fields['id']);
		   unset($fields['create_time']);
		   //所有字和
		   $field_str = '';
		   foreach($fields as $field){
			 if($field=='id' || $field=='create_time'){
			   continue;
			 }
		     $field_str .= 'SUM(  `'.$field.'` ) AS '.$field.',';
		   }
		   $field_str = substr($field_str,0,-1);
		   $btime = mktime(0,0,0,date('m',$now_time),1,date('Y',$now_time));
		   $etime = mktime(24,0,0,date('m',$now_time),date('t',$now_time),date('Y',$now_time));
		   $data3['create_time'] = array(array('gt',$btime),array('lt',$etime));
		   $vo = $model->field($field_str)->where($data3)->find();
		   $data4['type'] = 3;
		   $data4['info'] = serialize($vo);
		   $data4['create_time'] = $now_time;
		   $model2->add($data4);
		}
	}
	echo 'ok';

  }

} 
?>