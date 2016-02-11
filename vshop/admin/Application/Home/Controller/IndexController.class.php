<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
  public function _initialize() {
	parent::_initialize();
	$this->rdb = D('Record');
	$this->odb = D('Order');
	$this->oddb = D('order_detail');
	$this->mdb = D('Member');
	$this->pdb = D('Product');
	$this->fdb = D('Feedback');
	$this->cdb = D('Member_coupon');
  }

  /**
   * 首页基本数据
   */
  public function index() {
	$btime = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$etime = time()+1;
	$data2['pay_time'] = array(array('gt',$btime),array('lt',$etime));
	//今日总交易额
	$real_amount = $this->rdb->where($data2)->sum('amount');
	$real_amount = $real_amount ? $real_amount : '0.00';
	$this->assign('real_amount',$real_amount);
	//今日订单总数
	$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$total_order = $this->odb->where($data)->count();
	$total_order = $total_order ? $total_order : '0';
	$this->assign('total_order',$total_order);
	//提现申请
	$model = D('record');
	$rdata['pay_type'] = 3;
	$rdata['status'] = 0;
	$withdraw = $model->where($rdata)->count();
	$this->assign('withdraw',$withdraw);
	//今日注册会员总数
	$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$reg = $this->mdb->where($data)->count();
	$reg = $reg ? $reg : '0';
	$this->assign('reg',$reg);
	//今日评论总数
	$data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$feedback = $this->fdb->where($data)->count();
	$feedback = $feedback ? $feedback : '0';
	$this->assign('feedback',$feedback);

	//待确认订单
	$dqr_data['type'] = 1;
	$dqr_data['status'] = 0;
	$dqr_data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$dqr_count = $this->odb->where($dqr_data)->count();
	$this->assign('dqr_count',$dqr_count);

	//待支付订单
	$dfh_data['type'] = 1;
	$dfh_data['status'] = 1;
	$dfh_data['pay_status'] = 1;
	$dfh_data['delivery_status'] = 0;
	$dfh_data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$dfh_count = $this->odb->where($dfh_data)->count();
	$this->assign('dfh_count',$dfh_count);

	//完成交易订单
	$wc_data['type'] = 1;
	$wc_data['status'] = 1;
	$wc_data['pay_status'] = 1;
	$wc_data['delivery_status'] = 0;
	$wc_data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$wc_count = $this->odb->where($wc_data)->count();
	$this->assign('wc_count',$wc_count);

	//商品上架数量
	$p_data['type'] = 1;
	$p_data['status'] = 1;
	$p_data['pay_status'] = 1;
	$p_data['delivery_status'] = 0;
	$p_count = $this->pdb->where($p_data)->count();
	$this->assign('p_count',$p_count);

	//库存警告
	$jg_data['status'] = 1;
	$jg_data['_string'] = '`stock` <=  `warn_number`';
	$jg_count = $this->pdb->where($jg_data)->count();
	$this->assign('jg_count',$jg_count);

	//优惠券发放数
	$ct_data['status'] = 0;
	$ct_data['create_time'] = array(array('gt',$btime),array('lt',$etime));
	$ct_count = $this->cdb->where($ct_data)->count();
	$this->assign('ct_count',$ct_count);

	//优惠券使用数
	$cu_data['status'] = 1;
	$cu_data['use_time'] = array(array('gt',$btime),array('lt',$etime));
	$cu_count = $this->cdb->where($cu_data)->count();
	$this->assign('cu_count',$cu_count);


	//一元购发货
	$order_data['type'] = 2;
	$order_data['status'] = 1;
	$order_data['is_prize'] = 1;
	$yiyuan_count = $model->where($order_data)->count();
	$this->assign('yiyuan_count',$yiyuan_count);

	//广告待审核
	$model = D('advert');
	$ad_data['status'] = 0;
	$ad_count = $model->where($ad_data)->count();
	$this->assign('ad_count',$ad_count);
	//晒单
	$model = D('shaidan');
	$sd_data['status'] = 0;
	$sd_count = $model->where($sd_data)->count();
	$this->assign('sd_count',$sd_count);
	$this->display();
  }


  /**
   * 密码修改页
   */
  public function uc_sup_infoxg(){
    $this->display();
  }

  /**
   * 密码修改保存
   */
  public function uc_sup_infoxg_save(){
	$model = D('User');
	$data['id'] = $_SESSION[C('USER_AUTH_KEY')];
	$sdata['email'] = $_POST['email'];
	$sdata['editorname'] = $_POST['editorname'];
	if($_POST['password']){
	  $sdata['password'] = md5($_POST['password']);
	}
	if($_FILES){
	  foreach($_FILES as $key=>$val){
		if($val['name']){
		  $this->upload();
		  break;
		}
	  }
	}
	if($_POST['logo']){
		$sdata['logo'] = $_POST['logo'];
	}
	$result = $model->where($data)->save($sdata);
	if (false !== $list) {
	//成功提示
	  $_SESSION['editorname']     =   $_POST['editorname'];
	  $_SESSION['email']     =   $_POST['email'];
	  $_SESSION['logo']     =   $_POST['logo'];
	  $this->success ('编辑成功!');
	} else {
	//错误提示
	  $this->error ('编辑失败!');
	}
  }

  function test(){
	//$cache_data['dir'] = 'abc/';
	setCache('Region:pvs',123,60*60*12,$cache_data);
	//dump(getcache('Region:pvs'));exit;
	//delcache('Region:pvs');
  }
}