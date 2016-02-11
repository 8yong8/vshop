<?php 
namespace Home\Controller;
use Think\Controller;
import ( '@.ORG.Cart' );
include C('PUBLIC_INCLUDE')."function.inc.php";
class BaseController extends Controller {

  protected $configs = array();    //项目配置
  protected $shopcartnum = 0;//购物车商品数量
  protected $user = '';       //会员信息
  protected $login_url = '';       //登录页面
  protected $register_url = '';    //注册页面
  protected $from_url = '';    //本页

  /**
   * 前期执行
   */
  public function _initialize(){
    echo 123;exit;
	//配置
	$configs = getCache('Config:list');
	$this->configs = $configs;
	$this->assign('configs',$configs);
    //dump($configs);exit;
	//购物车
	/*
	$shopcart=  session('shopcart');
	$this->shopcartnum = count($shopcart);
	$this->assign('shopcartnum', count($shopcart));
	*/
	//上级
	if($_GET['r']){
	  cookie('r',$_GET['r']);
	}
	//会员信息
	$member_msg = session('member_msg');
	$user = unserialize(authcode($member_msg,'DECODE'));
    $user['id'] = 107;//模拟用户

	if($user){
	  if($user['openid'] && !$user['id']){
		$to_url = C('MEMBER_SITE_URL').'/Member/message';
	    header("location:".$to_url);exit;
	  }
	  $model = D('Member');
	  $data['a.id'] = $user['id'];
	  $db_pre = C('DB_PREFIX');
	  $user = $model->alias('a')->join('`'.$db_pre.'member_wallet` as b on a.id=b.member_id')->join('`'.$db_pre.'member_msg` as c on a.id=c.member_id')->field('a.id,a.pid,a.nickname,logo,lv,lv_name,email,utype,username,realname,mobile,password,salt,pv_id,ct_id,province,city,create_time,last_login_time,balance,frozen,c.sex')->where($data)->find();
	  //echo $model->getlastsql();exit;
	  if($user['balance']==null){
		$data['update_time'] = time();
		$model->add($data);
	    $user['balance'] = 0;
		$user['frozen'] = 0;
		$user['update_time'] = time();
	  }
	  //dump($user);
	  $user['username'] = $user['username'] ? $user['username'] : $user['mobile'];
	  $this->user = $user;
	  $this->assign('user',$user);
	}
	$this->iswx = isWeixin();//是否微信浏览器
	$this->login_url = C('SITE_URL').'/index.php/Public/login';
	$this->register_url = C('SITE_URL').'/index.php/Public/register';
	if(!IS_AJAX){
		if($_SERVER['QUERY_STRING']){
		  $from_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		}else{
		  $from_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];	
		}
		$from_url_except = array('login','register','reg_do','verify','checkLogin','check_username','check_email','get_city');
		if(array_search(ACTION_NAME,$from_url_except)===false && CONTROLLER_NAME!='Public'){
		  $_SESSION['self_url'] = $from_url;
		}	
	}
	
	$this->assign('self_url',$_SESSION['self_url']);
  }

  /**
   * 空方法
   */
  public function _empty(){
	$this->assign('jumpUrl',__ROOT__);
	$this->error('访问页面不存在');
	exit;
  }

  /**
   * 权重修改
   */
  protected function update_weight($source,$sourceid){
	//修改权重
    $model = M('Weight');
	$data['source'] = $source;
	$data['sourceid'] = $sourceid;
	$vo = $model->where($data)->find();
	$qz = $vo['like_count']*1+$vo['favorite_count']*2+$vo['comment_count']*3+ceil($vo['pv_count']/100);
	$sdata['qz'] = $qz;
	$model->where($data)->save($sdata);
	//修改产品权重
	$model = M($source);
	$pdata['id'] = $sourceid;
	$model->where($pdata)->save($sdata);
  }

  /**
   * 微信js分享调用
   */
  public function wxsign(){
	header("Access-Control-Allow-Origin: ".C('WAP_URL'));
	if($_GET['token']){
      $model = M('WxUser');
      $data['token'] = $_GET['token'];
      $wx_msg = $model->where($data)->find();
	  $appid = $wx_msg['appid'];
	  $appsecret = $wx_msg['appsecret'];
	  require_once "jssdk.php";
	  $url = $_GET['url'];
	  $jssdk = new JSSDK($appid,$appsecret,$url);
	  $jssdk->cacheDir = C('PUBLIC_CACHE').'/'.$_GET['token'];
	  $signPackage = $jssdk->GetSignPackage();
	  echo json_encode($signPackage);exit;
	  //dump($signPackage);
	}
  }

}
?>