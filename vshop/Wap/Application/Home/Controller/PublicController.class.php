<?php 
namespace Home\Controller;
use Think\Controller;
class PublicController extends BaseController {

  protected $configs = array();    //项目配置
  protected $shopcartnum = 0;//购物车商品数量
  protected $user = '';       //会员信息
  protected $login_url = '';       //登录页面
  protected $register_url = '';    //注册页面
  protected $from_url = '';    //本页

  /**
   * 验证码
   */
  public function verify(){
	//3.2.1  中的生成 验证码 图片的方法        
	$Verify = new \Think\Verify();
	// 设置验证码字符为纯数字
	$Verify->codeSet = '0123456789'; 
	$Verify->length   = 4;
    $Verify->imageH    =  50;// 验证码图片高度
    $Verify->imageW    =  200;// 验证码图片宽度
	$Verify->entry();  
  }

  /**
   * 注册页面
   */
  public function register(){
	if($_GET['from_url']){
	  session('from_url',base64_decode($_GET['from_url']));
	}
	$token =  md5(microtime(TRUE));
	session('reg_token',$token);
	$this->assign('token',$token);
	$parent = $this->parent();
	if($parent){
	  if($parent['mobile']){
	    $parent['account'] = $parent['mobile'];
	  }else if($parent['username']){
	    $parent['account'] = $parent['username'];
	  }else if($parent['nickname']){
	    $parent['account'] = $parent['nickname'];
	  }
	}
	$this->assign('parent',$parent);
	$title = '注册页- '.C('site_name');
	$keywords = '注册页';
	$description = '注册页';
	$this->assign('headerTitle',$title);
	$this->assign('headerKeywords',$keywords);
	$this->assign('headerDescription',$description);
    $this->display();
  }

  /**
   * 用户验证
   */
  public function check_username(){
	$model = M('Member');
	$data['tel'] = $_POST['tel'];
	$count = $model->where($data)->count();
	return $count;
  }

// 检查邮箱
  public function check_email() {

  }

  /**
   * 注册处理
   */
  public function reg_do(){
	//3.2.1 的 验证码 检验方法
	$verify = $_POST['verifycode'] ;
	if(!$this->check_verify($verify)){
		ajaxErrReturn('图形验证码不对');
	} 
	/*
	if($_SESSION['verify']!=md5($_POST['verifycode'])){
	  ajaxErrReturn('图形验证码不对');
	}
	*/
	//$token =  md5(microtime(TRUE));
	$mv_model = M('Member_verify');
	$mv_data['mobile'] = $_POST['mobile'];
	$mv_data['type'] = 'reg';
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
	if($mv['code'] != $_POST['code']){
	  $mv_model->where($mv_data)->setInc('verify_num',1);//验证次数+1
	  //ajaxErrReturn($mv_model->getlastsql());
	  ajaxErrReturn('短信验证码不对');
	}
	$model = M('Member');
	$data['mobile'] = $_POST['mobile'];
	$count = $model->where($data)->count();
	if($count>0){
	  ajaxErrReturn('该手机已经被注册');
	}
	$salt = rand_string(6,-1);
	$_POST['salt'] = $salt;
	$_POST['create_time'] = time();
	$_POST['utype'] = 1;
	$_POST['from'] = 'wap';
	$_POST['status'] = 1;
	$_POST['password'] = md5($_POST['password'].$salt.$salt[1]);
	if (false === $model->create ()) {
		ajaxErrReturn( $model->getError () );
	}
	//保存当前数据对象
	$result = $model->add ();
	if($result){
	  $mv_sdata['status'] = 1;
	  $mv_model->where($mv_data)->save($mv_sdata);//验证码失效
	  $vo = $_POST;
	  $vo['id'] = $result;
	  $member_msg = serialize($vo);
	  $member_msg = authcode($member_msg,'ENCODE');
	  session('member_msg',$member_msg);
	  if($_SESSION['from_url']){
	    $from_url = $_SESSION['from_url'];
	  }else{
	    $from_url = __APP__;
	  }
	  $msg['notice'] = '注册成功';
	  $msg['gourl'] = $from_url;
	  ajaxSucReturn($msg);
	}else{
	  ajaxErrReturn( $model->getlastsql() );
	}
  }

  /**
   * 登录页面
   */
  public function login(){
	$title = '登录页面';
	$keywords = '';
	$description = '';
	if($_GET['from_url'])$_SESSION['from_url']=$_GET['from_url'];
	$this->assign('headerTitle',$title);
	$this->assign('headerKeywords',$keywords);
	$this->assign('headerDescription',$description);
    $this->display();
  }

  /**
   * 登录检测
   */
  public function checkLogin(){
	//3.2.1 的 验证码 检验方法
	$verify = $_POST['verifycode'] ;
	if(!$this->check_verify($verify)){
		ajaxErrReturn('图形验证码不对');
	}
	$model = D('Member');
	$data['mobile'] = $_POST['account'];
	$vo = $model->field('id,logo,password,email,utype,username,realname,create_time,salt')->where($data)->find();
	if(!$vo){
	  ajaxErrReturn('用户不存在');	  
	}
	if($vo['password']!=md5($_POST['password'].$vo['salt'].$vo['salt'][1])){
	  ajaxErrReturn('密码错误');
	}
	$sdata['last_login_time'] = time();
	$sdata['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
	$model->where($data)->save($sdata);
	$member_msg = serialize($vo);
	$member_msg = authcode($member_msg,'ENCODE');
	session('member_msg',$member_msg);
	if($_SESSION['from_url']){
	  //$from_url = base64_decode($_SESSION['from_url']);
	  $from_url = $_SESSION['from_url'];
	}else{
	  $from_url = __APP__;
	}
	$msg['notice'] = '登录成功';
	$msg['gourl'] = $from_url;
	ajaxSucReturn($msg);
  }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串	  
	public function check_verify($code, $id = ''){
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}

  /**
   * 退出
   */
  public function logout(){
	if(session('member_msg')) {
	  session('member_msg',null);
	  $msg = '<script>';
	  $msg .= "window.location.href='".$_SERVER['HTTP_REFERER']."'";
	  $msg .= '</script>';
	  echo $msg;exit;	
	}else {
	  $this->error('已经登出！');
	}
  }

  /**
   * 评论信息
   */
  public function comment($source='Goods',$sourceid){
	$model = M('comment');
	$pl_data['source'] = $source;
	$pl_data['sourceid'] = $sourceid;
	$list = $model->where($pl_data)->limit(10)->order('id desc')->select();
	if($_POST['ajax']){
	  $json = json_encode($list);
	  echo $json;exit;
	}
	return $list;  
  }

  /**
   * 添加评论
   */
  public function comment_add(){
	if(md5($_POST['verify'])!=$_SESSION['verify']){
		$msg['status'] = 0;
		$msg['notice'] = '验证码错误！';
		echo  json_encode($msg);exit;
	}
	if(!$this->user){
		$msg['status'] = 0;
		$msg['notice'] = '请先登录！';
		echo  json_encode($msg);exit;
	}
	if(!$_POST['message']){
		$msg['status'] = 0;
		$msg['notice'] = '内容不能为空！';
		echo  json_encode($msg);exit;
	}
	$model = D ('comment');
	//$btime = mktime(0,0,0,date('m'),1,date('Y'));
	//$etime = mktime(24,0,0,date('m'),date('t'),date('Y'));
	$data['member_id'] = $this->user['id'];
	$data['source'] = $_POST['source'];
	$data['sourceid'] = $_POST['sourceid'];
	//$data['create_time'] = array('between',array($btime,$etime));
	$count = $model->where($data)->count();
	if($count>0){
		$msg['status'] = 0;
		$msg['notice'] = '已经评论过！';
		echo  json_encode($msg);exit;
	}
	$_POST['create_time'] = time();
	$_POST['ip'] = $_SERVER['REMOTE_ADDR'];
	$_POST['logo'] = $this->user['logo'];
	$_POST['member_id'] = $this->user['id'];
	$_POST['member_name'] = $this->user['username'];
	if (false === $model->create ()) {
		$msg['status'] = 0;
		$msg['notice'] = '出错！';
		echo  json_encode($msg);exit;
	}
	//保存当前数据对象
	$list = $model->add ();
	if ($list!==false) { //保存成功
		$msg['status'] = 1;
		$msg['notice'] = '评论成功！';
		$model = M('weight');
		unset($data['member_id']);
		$model->where($data)->setInc('comment_count');
		$this->update_weight($_POST['source'],$_POST['sourceid']);
		echo  json_encode($msg);exit;
	} else {
		$msg['status'] = 0;
		$msg['notice'] = '评论失败！';
		echo  json_encode($msg);exit;
	}
  
  }

  /**
   * 赞
   */
  public function like(){
	if(!$this->user){
		$msg['status'] = 0;
		$msg['notice'] = '请先登录！';
		echo  json_encode($msg);exit;
	}
	$model = D ('like');
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
	$vo = $pmodel->field('id,product_id,product_name,lit_pic,price')->where($pdata)->find();
	$_POST['product_id'] = $vo['product_id'];
	$_POST['content'] = serialize($vo);
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
		$model = M('weight');
		unset($data['member_id']);
		$model->where($data)->setInc('like_count');
		$this->update_weight($_POST['source'],$_POST['sourceid']);
		echo  json_encode($msg);exit;
	} else {
		$msg['status'] = 0;
		$msg['notice'] = '赞失败！';
		echo  json_encode($msg);exit;
	}
  }

  //取得openid
  public function get_code2(){
    $model = M('wx_user');
    $data['token'] = $_GET['token'];
    $wx_msg = $model->where($data)->find();
	$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$wx_msg['appid'].'&secret='.$wx_msg['appsecret'].'&code='.$_GET['code'].'&grant_type=authorization_code';
	$json = httpGet($url);
	$array  = json_decode($json,1);
	$openid = $array['openid'];
	$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$array['access_token'].'&openid='.$openid.'&lang=zh_CN';
	$msg = json_decode(httpGet($url),1);
	$model = M('member_bind');
	$add_data['openid'] = $openid;
	$count = $model->where($add_data)->count();
	if($count==0){
		$add_data['token'] = $_GET['token'];
		$add_data['nickname'] = $msg['nickname'];
		$add_data['sex'] = $msg['sex'];
		$add_data['province'] = $msg['province'];
		$add_data['city'] = $msg['city'];
		$add_data['country'] = $msg['country'];
		$add_data['headimgurl'] = $msg['headimgurl'];
		$add_data['create_time'] = time();
		$model->add($add_data);	
	}
	cookie('openid',$openid);
    $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
	$parse = parse_url($url);
	if(isset($parse['query'])) {
		parse_str($parse['query'],$params);
	}
	$params['a'] = $_GET['do'] ? $_GET['do'] : 'index';
	unset($params['code']);
	unset($params['do']);
	$params['openid'] = $openid;
	$go_url = $parse['path'].'?'.http_build_query($params);
	//echo $go_url;exit;
	header("Location: ".$go_url);
  }

  /**
   *  注册短信
   */
  public function reg_sms(){
	$tel = $_POST['mobile'];
	$type = $_POST['type'] ? $_POST['type'] : 'reg';
	if(!validateMobile($tel)){
		$msg['notice'] = '手机号码不对！';
		ajaxErrReturn($msg);	  
	}
	$code = str_pad(rand(0,9999),4,0,STR_PAD_LEFT);
	$content = '验证码：'.$code.'，请勿将验证码泄漏给其他人';
	$model = M('Member_verify');
	$data['mobile'] = $tel;
	$data['type'] = $type;
	$v_vo = $model->where($data)->find();
	if($v_vo['status']==1){
		$msg['notice'] = '短信已验证';
		ajaxErrReturn($msg);	  
	}
	if($v_vo){
		if(time()-$v_vo['update']<60){
			$msg['notice'] = '请稍后再发';
			ajaxSucReturn($msg);  
		}else{
		  if($v_vo['m']==date('m') && $v_vo['d']==date('d')){
		    if($v_vo['sent_num']>4){
			  $msg['notice'] = '每天最多发送5次,请明天再来';
			  ajaxErrReturn($msg);			  
			}
			$sdata['sent_num'] = $v_vo['sent_num']+1; //今天+1
		  }else{
		    $sdata['sent_num'] = 1; //其他重新统计
		  }
		  $sdata['y'] = date('Y');
		  $sdata['m'] = date('m');
		  $sdata['d'] = date('d');
		  $sdata['code'] = $code;
		  $sdata['tel'] = $tel;
		  $sdata['msg'] = $content;
		  $sdata['status'] = 0;
		  $sdata['update_time'] = time();
		  $model->where($data)->save($sdata);
		  //echo $model->getlastsql();exit;
		}	
	}else{
	  $sdata['member_id'] = $this->user['id'] ? $this->user['id'] : 0;
	  $sdata['code'] = $code;
	  $sdata['type'] = $type;
	  $sdata['mobile'] = $tel;
	  $sdata['y'] = date('Y');
	  $sdata['m'] = date('m');
	  $sdata['d'] = date('d');
	  $sdata['sent_num'] = 1;
	  $sdata['msg'] = $content;
	  $sdata['status'] = 0;
	  $sdata['ip'] = _get_ip();
	  $sdata['update_time'] = time();
	  $v_vo['id'] = $model->add($sdata);
	}
	$result = sent_msm($tel,$content);
	$result = true;
	if($result){
	  $msg['notice'] = '发送成功';
	  ajaxSucReturn($msg);
	}else{
	  $msg['notice'] = '发送失败';
	  ajaxErrReturn($msg);
	}
  }

  /**
   *  跳转至微信授权页
   */
  public function wx_auth(){

		//查询用户是否存在

		$openid = 'oO8UmtwFMCiA6_FzuXUGZfDL9sJo';
		$model = M('Member');
		$data['openid'] = $openid;
		$member = $model->field('id,logo,email,username,nickname,realname,province,city,district,create_time,salt')->where($data)->find();

		$member_msg = serialize($member);
		$member_msg = authcode($member_msg,'ENCODE');
		session('member_msg',$member_msg);
		$redirectURL = Cookie( '_redirectURL_');
		$go_url = $redirectURL ? $redirectURL : __APP__.'?wx_login=1';
		header("Location: ".$go_url);
		exit;


	//判断是否有微信帐号
	if(cookie('wx_has')){
	  header("Location: ".__APP__.'/Public/wx_authbase');exit;
	}
	include C('INTERFACE_PATH')."wxwappay/lib/WxPay.Api.php";
	include C('INTERFACE_PATH')."wxwappay/unit/WxPay.JsApiPay.php";
	$url = urlencode(__APP__.'/'.MODULE_NAME.'/'.ACTION_NAME.'/get_code/?&key='.time());	
	$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WxPayConfig::APPID.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=index#wechat_redirect';
	header("Location: ".$url);exit;	
  }

  /**
   *  跳转至微信授权页
   */
  public function wx_authbase(){
	include C('INTERFACE_PATH')."wxwappay/lib/WxPay.Api.php";
	include C('INTERFACE_PATH')."wxwappay/unit/WxPay.JsApiPay.php";
	$url = urlencode(__APP__.'/'.MODULE_NAME.'/get_code/?&key='.time());	
	$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WxPayConfig::APPID.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_base&state=index#wechat_redirect';
	header("Location: ".$url);exit;	
  }

  /**
   *  取得授权信息
   */
  public function get_code(){
	include C('INTERFACE_PATH')."wxwappay/lib/WxPay.Api.php";
	include C('INTERFACE_PATH')."wxwappay/unit/WxPay.JsApiPay.php";

	//获取openid
	$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET.'&code='.$_GET['code'].'&grant_type=authorization_code';
	$json = httpGet($url);
	$array  = json_decode($json,1);
	$openid = $array['openid'];

	//获取用户信息
	$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$array['access_token'].'&openid='.$openid.'&lang=zh_CN';
	$msg = json_decode(httpGet($url),1);
	$model = M('Member');
	$time = time();
	if($msg){
		//记录微信注册过
		cookie('wx_has',1,60*60*24*365);
		//查询用户是否存在
		$data['openid'] = $openid;
		$member = $model->field('id,logo,email,username,nickname,realname,province,city,district,create_time,salt')->where($data)->find();
		//用户存在则登录
		if($member){
			$sdata['last_login_time'] = time();
			$sdata['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
			$model->where($data)->save($sdata);
			$member_msg = serialize($member);
			$member_msg = authcode($member_msg,'ENCODE');
			session('member_msg',$member_msg);
		}else{
			$parent = $this->parent();
			$region = $this->getcityid($msg['province'],$msg['city']);
			$data['openid'] = $openid;
			$data['nickname'] = $msg['nickname'];
			$data['from'] = 'wap';
			$salt = rand_string(6,-1);
			$data['salt'] = $salt;
			$data['pid'] = $parent ? $parent['id'] : 0;
			$data['pv_id'] = $region['pv_id'];
			$data['ct_id'] = $region['ct_id'];
			$data['province'] = $msg['province'];
			$data['city'] = $msg['city'];
			$data['logo'] = $msg['headimgurl'];
			$data['last_login_time'] = $time;
			$ip = _get_ip();
			$data['last_login_ip'] = $ip;
			$data['create_time'] = $time;
			$member_id = $model->add($data);
			//用户其他信息
			$model = M('Member_msg');
			$data2['member_id'] = $member_id;
			$data2['sex'] = $msg['sex'];
			$model->add($data2);
			//微信登录
			$member['id'] = $member_id;
			$member['logo'] = $msg['headimgurl'];
			$member['salt'] = $salt;
			$member['ip'] = $ip;
			$member['province'] = $msg['province'];
			$member['city'] = $msg['city'];
			$member['district'] = '';
			$member['create_time'] = $time;
			$member_msg = serialize($member);
			$member_msg = authcode($member_msg,'ENCODE');
			session('member_msg',$member_msg);
		}
		login_log($member);

	}

	//跳转回访问页
	//$origin_action = $_GET['origin_action'] ? $_GET['origin_action'] : 'index';
	//$go_url = __APP__.'?wx_login=1';
	$redirectURL = Cookie( '_redirectURL_');
	$go_url = $redirectURL ? $redirectURL : __APP__.'?wx_login=1';
	header("Location: ".$go_url);
	exit;
  }	

  /**
   *  推荐人
   */
  protected function parent(){
	//获得推荐人
	$base64 = cookie('r');
	if ($base64){
		//推荐人是否存在
		$model = M('Member');
		$data['id'] = base64_decode($base64);
		$referee = $member = $model->field('id,username,nickname,mobile')->where($data)->find();
	}
	return !empty($referee) ? $referee : '';
  }

  /*
   *  获取城市id
   */	
  protected function getcityid($province,$city){
    $model = M('Region');
	$data['area_name'] = array('like','%'.$province.'%');
	$province = $model->where($data)->find();
	$data2['area_name'] = array('like','%'.$city.'%');
	$city = $model->where($data)->find();
	$arr['pv_id'] = $province['id'];
	$arr['ct_id'] = $city['id'];
	return $arr;
  }

  /**
   *  微信JSAPI
   */
  public function wxsign(){
	header("Access-Control-Allow-Origin: ".DOMAIN_NAME);
	require_once "lib/api/wxwebpay/jssdk.php";
	require_once "lib/api/wxwebpay/lib/WxPay.Config.php";
	//来源地址
	$url = $_GET['url'];
	$appid = WxPayConfig::APPID;
	$appsecret = WxPayConfig::APPSECRET;
	$jssdk = new JSSDK($appid,$appsecret,$url);
	$signPackage = $jssdk->GetSignPackage();
	echo json_encode($signPackage);exit;
  }

}
?>