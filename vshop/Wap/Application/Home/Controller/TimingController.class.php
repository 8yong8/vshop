<?php 
namespace Home\Controller;
use Think\Controller;
class TimingController extends Controller {

  protected $configs = array();    //项目配置

//前期执行
  public function _initialize(){
	if($_GET['sign']!=C('sign')){
	  echo 'fail';exit;
	}
	//配置
	$configs = getCache('Config:list');
	$this->configs = $configs;
	//公用函数
	include_once C('PUBLIC_INCLUDE')."function.inc.php";
  }

  //检测拍卖
  public function check_auction(){
    $time = time();
	$model = M('Auction');
	$lmodel = M('AuctionLog');
	$mwmodel = M('MemberWallet');
	$mwlmodel = M('MemberWalletLog');
	//$data['status'] = array('gt',0);
	$data['status'] = 1;
	$data['endtime'] = array('elt',$time);
	$list = $model->where($data)->select();
	$model->startTrans();//启用事务
	foreach($list as $val){
	  $wdata['id'] = $val['id'];
	  //流拍
	  if(!$val['auctionuid']){
		$sdata['status'] = -1;
		$result = $model->where($wdata)->save($sdata);
		//退回押金
		$mwl_data['oid'] = $val['id'];
		$mwl_data['pay_type'] = 1;
		$mwllist = $mwlmodel->where($mwl_data)->select();
		foreach($mwllist as $mwlval){
		  $mw_wdata['member_id'] = $mwlval['member_id'];
		  //区分低价者得和正常拍卖
		  if($val['type']==2){
			$lmodel['aid'] = $val['id'];
			$lmodel['member_id'] = $mwlval['member_id'];
		    $count = $lmodel->where($lmodel)->count();
		  }else{
			  $result = $mwmodel->where($mw_wdata)->setInc('balance',$val['bond']);
			  if($result)$result = $mwmodel->where($mw_wdata)->setDec('frozen',$val['bond']);
			  $mwl_add_data['title'] = '订金退回';
			  $mwl_add_data['member_id'] = $mwlval['member_id'];
			  $mwl_add_data['pay'] = $val['bond'];
			  $mwl_add_data['oid'] = $val['id'];
			  $mwl_add_data['pay_type'] = 2;
			  $mwl_add_data['content'] = '订金退回,拍品：'.$val['product_name'].'，退回订金'.$val['bond'];
			  $mwl_add_data['create_time'] = time();
			  //记录退回订金信息
			  if($result)$result = $mwlmodel->add($mwl_add_data);
			  if(!$result){
				$model->rollback();
				break;
			  }		  
		  }

		}
		$model->commit();
	  }else{
		$sdata['status'] = 2;
		$result = $model->where($wdata)->save($sdata);
		//除竞拍赢家,退回押金;
		$mwl_data['oid'] = $val['id'];
		$mwl_data['pay_type'] = 1;
		$mwl_data['member_id'] = array('neq',$val['auctionuid']);
		$mwllist = $mwlmodel->where($mwl_data)->select();
		//区分低价者得和正常拍卖
		foreach($mwllist as $mwlval){
		  $mw_wdata['member_id'] = $mwlval['member_id'];
		  $result = $mwmodel->where($mw_wdata)->setInc('balance',$val['bond']);
		  if($result)$result = $mwmodel->where($mw_wdata)->setDec('frozen',$val['bond']);
		  $mwl_add_data['title'] = '订金退回';
		  $mwl_add_data['member_id'] = $mwlval['member_id'];
		  $mwl_add_data['pay'] = $val['bond'];
		  $mwl_add_data['oid'] = $val['id'];
		  $mwl_add_data['pay_type'] = 2;
		  $mwl_add_data['content'] = '订金退回,拍品：'.$val['product_name'].'，退回订金'.$val['bond'];
		  $mwl_add_data['create_time'] = $val['endtime'];
		  //记录退回订金信息
		  if($result)$result = $mwlmodel->add($mwl_add_data);
		  if(!$result){
		    $model->rollback();
			break;
		  }
		}
		//生成订单
		$mmodel = M('member');
		$mids[] = $val['auctionuid'];
		if($val['member_id'])$mids[] = $val['member_id'];
		$mdata['id'] = array('in',$mids);
		$member = $mmodel->field('id,pid,realname')->where($mdata)->select();
		foreach($member as $mem){
		  $mid = $mem['id'];
		  $member2[$mid] = $mem;
		}
		$omodel = M('order');
		$o_add['type'] = 1;
		$o_add['source'] = 'Auction';
		$o_add['sourceid'] = $val['id'];
		$o_add['title'] = '竞拍成功!艺术品:'.$val['product_name'];
		$goods[] = $val;
		$o_add['goods'] = serialize($goods);
		$o_add['order_id'] = build_order_no($val['auctionuid']);
		$o_add['total_price'] = $val['realprice'];
		$o_add['bond'] = $val['bond'];
		$o_add['total_num'] = 1;
		$o_add['member_id'] = $val['auctionuid'];
		$o_add['member_name'] = $val['auctionuname'];
		$o_add['realname'] = $member2[$val['auctionuid']]['realname'];
		$o_add['user_id'] = $val['member_id'];
		$o_add['agent_id'] = $member2[$val['member_id']]['pid'] ? $member2[$val['member_id']]['pid'] : 0;
		$o_add['create_time'] = $val['endtime'];
		$o_add['order_time'] = $val['endtime']+86400*$this->configs['order_expired'];
		$oid = $omodel->add($o_add);
		if(!$oid){
		  $model->rollback();
		  break;
		}
		$almodel = M('auction_log');
		$al_data['aid'] = $val['id'];
		$al_data['member_id'] = $val['auctionuid'];
		$al_vo = $model->where($al_data)->find();
		$odmodel = M('order_detail');
		$od_add['oid'] = $oid;
		$od_add['member_id'] = $val['auctionuid'];
		$od_add['user_id'] = $val['member_id'];
		$od_add['source'] = 'Auction';
		$od_add['sourceid'] = $val['id'];
		$od_add['product_id'] = $val['product_id'];
		$od_add['product_name'] = $val['product_name'];
		$od_add['share_id'] = $al_vo['share_id'] ? $al_vo['share_id'] : 0;
		$od_add['lit_pic'] = $val['lit_pic'];
		$od_add['price'] = $val['realprice'];
		$od_add['num'] = 1;
		$od_add['is_buyback'] = $val['is_buyback'];
		$od_add['is_dm'] = $val['is_dm'];
		$od_add['create_time'] = $val['endtime'];
		$od_id = $odmodel->add($od_add);
		if(!$od_id){
		  $model->rollback();
		  break;
		}
		$model->commit();
	  }
	}
	//循环结束
  }

  //检测订单
  public function check_order(){
    $model = M('Order');
	$mw_model = M('MemberWallet');
	$mwl_model = M('MemberWalletLog');
	$data['status'] = 0;
    $data['order_time'] = array('lt',time());
    $list = $model->field('id,order_id,order_time,bond,type,member_id')->where($data)->select();
	foreach($list as $val){
	  if($val['order_time']<=time()){
	    if($val['type']==1){
		  //拍卖,订单不支付过期,扣除冻结金
		  $model->startTrans();//启用事务
		  $wdata['id'] = $val['id'];
		  $sdata['status'] = -1;
		  $result = $model->where($wdata)->save($sdata);
		  $wallet_data['member_id'] = $val['member_id'];
		  if($result)$result = $mw_model->where($wallet_data)->setDec('frozen',$val['bond']);
		  if($result){
			$mwl_data['title'] = '拍卖成功,到期未支付,扣除保证金!';
			$mwl_data['member_id'] = $val['member_id'];
			$mwl_data['aid'] = $val['id'];
			$mwl_data['pay_type'] = 3;
			$mwl_data['pay'] = $val['bond'];
			$mwl_data['content'] = '拍卖成功,到期未支付,扣除保证金!订单号:'.$val['order_id'];
			$mwl_data['create_time'] = time();
		    $result = $mwl_model->add($mwl_data);
		  }
		  if($result){
			$model->commit();
		  }else{
		    $model->rollback();
		  }
		}else{
		  $wdata['id'] = $val['id'];
		  $sdata['status'] = -1;
		  $model->where($wdata)->save($sdata);		
		}
	  }
	}
  }

  //众筹
  function check_project(){
    $time = time();
	$model = M('projects');
	$pgmodel = M('project_goods');
	$rmodel = M('record');
	$mwmodel = M('member_wallet');
	$mwlmodel = M('member_wallet_log');
	$omodel = M('order');
	$odmodel = M('order_detail');
	$logs_model = M('logs');
	$data['status'] = 1;
	$data['endtime'] = array('elt',$time);
	$list = $model->where($data)->select();
	//dump($list);exit;
	foreach($list as $key=>$val){
	  unset($pg_data);
	  $model->startTrans();//启用事务
	  $pg_data['project_id'] = $val['id'];
	  $count = $pgmodel->where($pg_data)->count();
	  $pg_data['status'] = 0;
	  $buy_count = $pgmodel->where($pg_data)->count();
	  //echo $buy_count.'/'.$count;
	  if($buy_count/$count>0.5){
	    //众筹成功
		//修改状态
		$data['id'] = $val['id'];
		$sdata['status'] = 2;
	    $result = $model->where($data)->save($sdata);
		if(!$result){
		  $model->rollback();
		  continue;
		}
		//分成处理
		$get_pay = $val['price']*0.1;
		$mw_data['member_id'] = $val['member_id'];
		$result = $mwmodel->where($mw_data)->setInc('balance',$get_pay);
		if(!$result){
		  $model->rollback();
		  continue;
		}
		//记录至账户
		$mw_data['member_id'] = $val['member_id'];
		$mw_vo = $mwmodel->where($mw_data)->find();
		$pay_order_id = build_order_no($val['member_id']);
		$rdata['member_id'] = $val['member_id'];
		$rdata['member_name'] = $val['member_name'];
		$rdata['realname'] = $val['realname'];
		$rdata['payment_mode'] = '1';
		$rdata['payment_company'] = C('company_name');
		$rdata['pay_order_id'] = $pay_order_id;
		$rdata['order_id'] = '';
		$rdata['pay_type'] = 5;
		$rdata['pay'] = $get_pay;
		$rdata['balance'] = $mw_vo['balance'];
		$rdata['content'] = $val['name'].'众筹成功,返利¥'.$get_pay;
		$rdata['create_time'] = $time;
		$rdata['pay_time'] = $time;
		$rdata['status'] = 1;
		$result = $rmodel->add($rdata);
		//echo $rmodel->getlastsql();
		if(!$result){
		  $model->rollback();
		  continue;
		}
		//订单信息修改
		//关联已支付订单
		$odata['type'] = 4;
		//$odata['source'] = 'Projects';
		$odata['sourceid'] = $val['id'];
		$odata['status'] = 1;
		$osdata['pay_order_id'] = $pay_order_id;
		$result = $omodel->where($odata)->save($osdata);
		//关闭未支付订单
		$odata['status'] = 0;
		$osdata2['status'] = -1;
		if($result)$result = $omodel->where($odata)->save($osdata2);
		//记录
		$logs_data['msg'] = '<span>'.name_hide($vo['member_name']).'</span>   众筹成功,获得返利'.$get_pay;
		$logs_data['create_time'] = time();
		$logs_model->add($logs_data);
		//事务提交
		if($result){
		  $model->commit();
		}else{
		  $model->rollback();
		}
		echo 112233;
	  }else{
		//众筹失败,修改状态
		$data['id'] = $val['id'];
		$sdata['status'] = -1;
	    $result = $model->where($data)->save($sdata);
		if($buy_count==0){
			continue;
		}
		if(!$result){
		  $model->rollback();
		  continue;
		}
		//退款
		//支付成功订单
		$odata['source'] = 'Projects';
		$odata['sourceid'] = $val['id'];
		$odata['status'] = 1;
		$olist = $omodel->where($odata)->select();
		foreach($olist as $oval){
		  //订单信息修改
		  $pay_order_id = build_order_no($oval['member_id']);
		  $owdata['id'] = $oval['id'];
		  $osdata['status'] = -1;
		  $osdata['pay_order_id'] = $pay_order_id;
		  $osdata['remark'] = $val['name'].'众筹失败退款';
		  $omodel->where($owdata)->save($osdata);
		  //详细订单状态修改
		  $od_data['oid'] = $oval['id'];
		  $od_sdata['trade_status'] = 0;
		  $od_sdata['remark'] = '众筹失败';
		  $result = $odmodel->where($od_data)->save($od_sdata);
		  //退款至账户
		  $mw_data['member_id'] = $oval['member_id'];
		  $mw_vo = $mwmodel->where($mw_data)->find();
		  if($result)$result = $mwmodel->where($mw_data)->setInc('balance',$oval['total_price']);
		  if(!$result){
			  $model->rollback();
			  continue;
		  }
		  //退款记录
		  $rdata['member_id'] = $oval['member_id'];
		  $rdata['member_name'] = $oval['member_name'];
		  $rdata['realname'] = $oval['realname'];
		  $rdata['payment_mode'] = '1';
		  $rdata['payment_company'] = C('company_name');
		  $rdata['pay_order_id'] = $pay_order_id;
		  $rdata['order_id'] = $oval['order_id'];
		  $rdata['pay_type'] = 5;
		  $rdata['pay'] = $oval['total_price'];
		  $rdata['balance'] = $mw_vo['balance']+$oval['total_price'];
		  $rdata['content'] = $val['name'].'众筹失败退款¥'.$oval['total_price'];
		  $rdata['create_time'] = $time;
		  $rdata['pay_time'] = $time;
		  $rdata['status'] = 1;
		  $result = $rmodel->add($rdata);
		  if(!$result){
			  $model->rollback();
			  continue;
		  }
		}
		//事务提交
		if($result){
		  //echo 11223;
		  $model->commit();
		}else{
		  $model->rollback();
		}
	  }
	}
  }

  //数据优化
  public function opt(){
	$db_pr = C('DB_PREFIX');
    $model = M('MemberVerify');
	//短信验证码
	$mv_data['staus']  = 1;
	$mv_data['verify_num']  = array('gt',10);
	$mv_data['_logic'] = 'or';
	$model->where($mv_data)->delete();
	$model->query('OPTIMIZE TABLE  `'.$db_pr.'member_verify');//短信验证
	$model->query('OPTIMIZE TABLE  `'.$db_pr.'cart`');//购物车
	$model->query('OPTIMIZE TABLE  `'.$db_pr.'collect`');//收藏夹
    $model = M('Member_token');
	$mt_data['update_time'] = array('lt',time()-60*60*24*7);//7天未登陆
	$model->where($mt_data)->delete();
	$model->query('OPTIMIZE TABLE  `'.$db_pr.'member_token`');//app会员token
  }
}
?>