<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends CommonController {

  /**
   * 会员默认页
   */
  public function index(){
	//待付款
	$model = M('Order');
	$data['member_id'] = $this->user['id'];
	$data['pay_status'] = 0;
	$dfk_count = $model->where($data)->count();
	$this->assign('dfk_count',$dfk_count);
	//待发货
	$data['pay_status'] = 1;
	$data['delivery_status'] = 0;
	$dfh_count = $model->where($data)->count();
	$this->assign('dfh_count',$dfh_count);
	//待确认
	$data['pay_status'] = 1;
	$data['delivery_status'] = 1;
	$data['status'] = 1;
	$dqr_count = $model->where($data)->count();
	//echo $model->getlastsql();
	$this->assign('dqr_count',$dqr_count);
	//待评价
	unset($data['delivery_status']);
	unset($data['pay_status']);
	$data['is_rate'] = 0;
	$data['status'] = 2;
	$dpj_count = $model->where($data)->count();
	$this->assign('dpj_count',$dpj_count);
	//待退款
	unset($data['is_rate']);
	$data['is_refund'] = 1;
	$data['status'] = 2;
	$dtk_count = $model->where($data)->count();
	$this->assign('dtk_count',$dtk_count);
	$this->assign('headerTitle','会员后台');
	$this->assign('headerKeywords','会员后台');
	$this->assign('headerDescription','会员后台');
	$this->assign('wx_title','会员后台');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }

  /**
   * 个人信息编辑
   */
  public function edit(){
	if(IS_POST){
	  if($this->checkFileUp()){
		  $this->upload();
	  }
	  $model = M('Member');
	  $wdata['id'] = $this->user['id'];
	  $result = false;
	  if($_POST['nickname']!=$this->user['nickname']){
	    $sdata['nickname'] = $_POST['nickname'];
		$result = true;
	  }
	  if($_POST['logo']){
		$sdata['logo'] = $_POST['logo'];
		$result = true;
	  }
	  if($result)$result = $model->where($wdata)->save($sdata);

	  if($_POST['sex']!=$this->user['sex']){
	    $model = M('Member_msg');
		$ms_wdata['member_id'] = $this->user['id'];
		$ms_sdata['sex'] = $_POST['sex'];
		$result = $model->where($ms_wdata)->save($ms_sdata);
	  }
	  //echo $model->getlastsql();
	  //dump($result);exit;
	  if($result){
		$this->assign('headerTitle','修改成功');
	    $this->success('修改成功');
	  }else{
		$this->assign('headerTitle','修改失败');
		$this->error('修改失败');
	  }
	}
	$this->assign('headerTitle','个人资料');
	$this->assign('headerKeywords','个人资料');
	$this->assign('headerDescription','个人资料');
	$this->assign('wx_title','个人资料');
	$this->assign('wx_desc','微信分享');
	$this->display();  
  }

  /**
   * 手机绑定
   */
  public function bindingmb(){
	if(IS_POST){
	  if($this->user['mobile']){
	    ajaxErrReturn('手机已绑定');
	  }
	  $mv_model = M('Member_verify');
	  $mv_data['mobile'] = $_POST['mobile'];
	  $mv_data['type'] = 'bdmb';
	  $mv = $mv_model->where($mv_data)->find();
	  if(!$mv){
	    ajaxErrReturn('短信未发送，请重试');
	  }
	  if($mv['status']==1){
	    ajaxErrReturn('短信验证码已使用');
	  }
	  if($mv['verify_num']>10){
	    ajaxErrReturn('短信验证码已失效');
	  }
	  $model = M('Member');
	  $wdata['id'] = $this->user['id'];
	  $salt = $this->user['salt'];
	  $sdata['mobile'] = $_POST['mobile'];
	  //判断手机号码是否存在
	  $count = $model->where($sdata)->count();
	  if($count>0){
	    ajaxErrReturn('手机号码已被注册');
	  }
	  $sdata['password'] = md5($_POST['password'].$salt.$salt[1]);
	  $result = $model->where($wdata)->save($sdata);
	  if($result){
	    $mv_sdata['status'] = 1;
	    $mv_model->where($mv_data)->save($mv_sdata);//验证码失效
		$msg['notice'] = '绑定成功';
		$msg['gourl'] = $_SESSION['self_url'];
	    ajaxSucReturn($msg);
	  }else{
	    ajaxErrReturn('绑定失败');
	  }
	}
	$_SESSION['self_url'] = $_GET['redirectURL'] ? $_GET['redirectURL'] : __APP__.'/Member';
	//dump($_SESSION['self_url']);
	$this->assign('headerTitle','绑定手机');
	$this->assign('headerKeywords','绑定手机');
	$this->assign('headerDescription','绑定手机');
	$this->assign('wx_title','绑定手机');
	$this->assign('wx_desc','微信分享');  
    $this->display();
  }

  /**
   * 密码设置
   */
  public function set_psw(){
  
  
  }

  /**
   * 我的二维码
   */
  public function qrcode(){
	$r = base64_encode($this->user['id']);
	$qrcode_url = C('WAP_URL') . "/Public/register?r={$r}";
	//echo $qrcode_url;exit;
	$this->assign('qrcode_url',$qrcode_url);
	$this->assign('headerTitle','我的二维码');
	$this->assign('headerKeywords','我的二维码');
	$this->assign('headerDescription','我的二维码');
	$this->assign('wx_title','我的二维码');
	$this->assign('wx_desc','微信分享');
    $this->display();
  }

}
?>