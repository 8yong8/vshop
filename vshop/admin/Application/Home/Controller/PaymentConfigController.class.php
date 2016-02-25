<?php 
namespace Home\Controller;
use Think\Controller;
class PaymentConfigController extends CommonController {

  /**
   * 列表前置
   */
  function _before_index(){
	$_REQUEST ['listRows'] = 50;
  }

  /**
   * 添加信息 前置
   */
  function _before_add(){
    if($_POST){
	  foreach($_POST as $key=>$val){
	    if($val==''){
		  $this->error($key.'必须填写');
		}
	  }
	  $content = $_POST;
	  unset($content['interface']);
	  unset($content['account_nickname']);
	  unset($content['status']);
	  if($_POST['pay_name']=='支付宝'){
	    $_POST['pay_class'] = 'ali';
	  }else if($_POST['pay_name']=='微信支付'){
	    $_POST['pay_class'] = 'wx';
	  }else if($_POST['pay_name']=='微信APP支付'){
	    $_POST['pay_class'] = 'wxapp';
	  }
	  $_POST['content'] = serialize($content);
	}
  }

  /**
   * 添加信息后置
   */
  function _after_add(){
	if($_POST){
	  $this->GiveCache();
	}
  }

  /**
   * 编辑信息前置
   */
  function _before_edit(){
    if($_POST){
	  foreach($_POST as $key=>$val){
	    if($val==''){
		  $this->error($key.'必须填写');
		}
	  }
	  $content = $_POST;
	  unset($content['interface']);
	  unset($content['account_nickname']);
	  unset($content['status']);
	  $_POST['content'] = serialize($content);
	  if($_POST['pay_name']=='支付宝'){
	    $_POST['pay_class'] = 'ali';
	  }else if($_POST['pay_name']=='微信支付'){
	    $_POST['pay_class'] = 'wx';
	  }else if($_POST['pay_name']=='微信APP支付'){
	    $_POST['pay_class'] = 'wxapp';
	  }
	}
  }

  /**
   * 编辑信息
   */
  function edit() {
	$name = CONTROLLER_NAME;
	$model = D ($name);
	if($_POST){
	  $_POST['update_time'] = time();
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  $content = $_POST;
	  unset($content['interface']);
	  unset($content['account_nickname']);
	  unset($content['status']);
	  $model->content = serialize($content);
	  // 更新数据
	  $list = $model->save ();
	  if (false !== $list) {
		//成功提示
		$this->history($_POST['id']);
		$this->GiveCache();
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	}else{
	  $name = CONTROLLER_NAME;
	  $model = M ( $name );
	  $id = $_REQUEST [$model->getPk ()];
	  $vo = $model->getById ( $id );
	  $content = unserialize(htmlspecialchars_decode($vo['content']));
	  $this->assign('content',$content);
	  $vo = array_merge($vo,$content);
	  $this->assign ( 'vo', $vo );
	  $this->display($vo['pay_class'].'_edit');	
	}
  }

  /**
   * 生成缓存
   */
  function GiveCache(){
	$name=CONTROLLER_NAME;
	$model = M ( $name );
	$wdata['status'] = 1;
	//$wdata['pay_class'] = 'ali';
	$list = $model->where($wdata)->order('id desc')->select();
	foreach($list as $val){
	  /*
	  if($val['pay_class']=='ali'){
	    $this->alipay_cache($val);
	  }else if($val['pay_class']=='wx'){
	    $this->wxpay_cache($val);
	  }else if($val['pay_class']=='wxapp'){
	    $this->wxapppay_cache($val);
	  }
	  */
	  $key = $val['pay_class'];
	  $list2[$key] = $val;
	}
	//F('pay',$list2,C('DATA_CACHE_PATH').'/config/');
	setCache('Config:pay',$list2);
  }

  /**
   * 支付宝生成缓存
   */
  public function alipay_cache($vo){
	extract(unserialize($vo['content']));
	$str = "<?php 
	"."$"."alipay_config['id']='".$vo['id']."';
	"."$"."pay_config['title']='".$vo['pay_name']."';
	"."$"."alipay_config['partner']='".$partner."';
	"."$"."alipay_config['key']= '".$key."';
	"."$"."alipay_config['sign_type']= strtoupper('MD5');
	"."$"."alipay_config['input_charset']= strtolower('utf-8');
	"."$"."alipay_config['cacert']='../cacert.pem';
	"."$"."alipay_config['transport']='http';
	"."$"."seller_email = '".$seller_email."';
	"."$"."notify_url = '".C('SITE_URL')."/index.php/Ali_Payment';
	"."$"."return_url = '".C('SITE_URL')."/index.php/Ali_Payment/ali_return';
	?>";
	file_put_contents('../Alipay/alipay.config.php',$str);
  }

  /**
   * 微信支付生成缓存
   */
  public function wxpay_cache($vo){
	//header("Content-Type: text/html; charset=utf-8");
	extract(unserialize($vo['content']));
	$str = "<?php 
	"."$"."pay_config['id']='".$vo['id']."';
	"."$"."pay_config['title']='".$vo['pay_name']."';
	"."$"."pay_config['appid']='".$appid."';
	"."$"."pay_config['mchid']='".$mchid."';
	"."$"."pay_config['key']= '".$key."';
	"."$"."pay_config['appsecret']= '".$appsecret."';
	"."$"."pay_config['sslcert_path']='".C('ROOT_SITE_DIR')."cacert20150528/apiclient_cert.pem';
	"."$"."pay_config['sslkey_path']='".C('ROOT_SITE_DIR')."cacert20150528/apiclient_key.pem';
	"."$"."notify_url = '".C('SITE_URL')."/index.php/Wx_Payment';
	"."$"."return_url = '".C('SITE_URL')."/index.php/Wx_Payment/ali_return';
	?>";
	$str = "<?php 
	class WxPayConfig{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = '".$appid."';
	//受理商ID，身份标识
	const MCHID = '".$mchid."';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '".$key."';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '".$appsecret."';
	//=======【curl代理设置】===================================
	/**
	 * 
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认0.0.0.0和0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = '0.0.0.0';
	const CURL_PROXY_PORT = 8080;	

	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '".C('ROOT_SITE_DIR')."Wxpay/cacert20150528/apiclient_cert.pem';
	const SSLKEY_PATH = '".C('ROOT_SITE_DIR')."Wxpay/cacert20150528/apiclient_key.pem';
	
	//=======【上报信息配置】===================================
	/**
	 * 
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	?>";
	//F('wxpay.config',$vo,'../'.'/Wxpay/');
	file_put_contents('../Wxpay/lib/WxPay.Config.php',$str);
  }

  /**
   * 微信app生成缓存
   */
  public function wxapppay_cache($vo){
	//header("Content-Type: text/html; charset=utf-8");
	extract(unserialize($vo['content']));
	$str = "<?php 
	"."$"."pay_config['id']='".$vo['id']."';
	"."$"."pay_config['title']='".$vo['pay_name']."';
	"."$"."pay_config['appid']='".$appid."';
	"."$"."pay_config['mchid']='".$mchid."';
	"."$"."pay_config['key']= '".$key."';
	"."$"."pay_config['appsecret']= '".$appsecret."';
	"."$"."pay_config['sslcert_path']='".C('ROOT_SITE_DIR')."cacert20150528/apiclient_cert.pem';
	"."$"."pay_config['sslkey_path']='".C('ROOT_SITE_DIR')."cacert20150528/apiclient_key.pem';
	"."$"."notify_url = '".C('SITE_URL')."/index.php/Wx_Payment';
	"."$"."return_url = '".C('SITE_URL')."/index.php/Wx_Payment/ali_return';
	?>";
	$str = "<?php 
	class WxPayConfig{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = '".$appid."';
	//受理商ID，身份标识
	const MCHID = '".$mchid."';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '".$key."';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '".$appsecret."';
	//=======【curl代理设置】===================================
	/**
	 * 
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认0.0.0.0和0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = '0.0.0.0';
	const CURL_PROXY_PORT = 8080;	

	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '".C('ROOT_SITE_DIR')."Wxpay/cacert20150528/apiclient_cert.pem';
	const SSLKEY_PATH = '".C('ROOT_SITE_DIR')."Wxpay/cacert20150528/apiclient_key.pem';
	
	//=======【上报信息配置】===================================
	/**
	 * 
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	?>";
	//F('wxpay.config',$vo,'../'.'/Wxpay/');
	file_put_contents('../Wxpay/lib/WxPay.Config.php',$str);
  }

}
?>