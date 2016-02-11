<?php 
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class PublicController extends Controller {

	/**
	 * 登录检测
	 */
	public function checkLogin() {
		$User	=	D('User');
		if(empty($_POST['username'])) {
			//iconv("GBK", "UTF-8", '帐号错误');
			$this->loginerror('帐号错误');
			//$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			//$this->error('密码必须！');
           $this->loginerror('密码必须！');
		}
        //3.2.1 的 验证码 检验方法
        $verify = $_POST['verify'] ;
        if(!$this->check_verify($verify)){
            //$this->loginerror('验证码输入错误！');
        }  
        //生成认证条件
        $map            =   array();
        $map["account"]	=	$_POST['username'];
        $map["status"]	=	array('gt',0);
		//权限1
		import ( '@.ORG.RBAC' );
        $authInfo = RBAC::authenticate($map);
		$authInfo = $User->where($map)->find();
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            //$this->error('用户名不存在或已禁用！');
			$this->loginerror('用户名不存在或已禁用！');
        }else {
			if($authInfo['account']	!=  $_POST['username']) {
				//$this->error('帐号错误！');
				$this->loginerror('帐号错误！');
			}
            if($authInfo['password'] != md5($_POST['password'])) {
            	//$this->error('密码错误！');
				$this->loginerror('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
			$_SESSION['account']		=	$authInfo['account'];
            $_SESSION['nickname']		=	$authInfo['nickname'];
			$_SESSION['logo']       =   $authInfo['logo'];
            $_SESSION['lastLoginTime']	=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
			$_SESSION['editorname']     =   $authInfo['editorname'];
            if($authInfo['account']=='admin') {
            	$_SESSION['_administrator_']		=	true;
            }
			$data['id'] = $authInfo['id'];
			$save['last_login_time'] = time();
			$save['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
			$save['login_count'] = $authInfo['login_count']+1;
			$User->where($data)->save($save);
			// 缓存访问权限
            RBAC::saveAccessList();
		   $data['error_code'] = 0;
		   $data['notice'] = '登录成功';
		   echo json_encode($data);exit;
		}
	}

	/**
	 * 错误提示
	 */
    public function loginerror($msg){
	  $data['error_code'] = 8001;
	  $data['notice'] = $msg;
	  echo json_encode($data);exit;
	} 

	/**
	 * 登录页面
	 */
	public function login() {
		//dump($_SESSION);
		unset($_SESSION[C('USER_AUTH_KEY')]);
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display('login');
		}else{
			$this->redirect('Index/index');
		}
	}

	/**
	 * 退出
	 */
    public function logout(){
		//echo __MODULE__.'||'.__CONTROLLER__;exit;
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",__CONTROLLER__.'/login/');
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串	  
	public function check_verify($code, $id = ''){
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}	

    /**
     +----------------------------------------------------------
     * 验证码显示
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function verify(){

        $Verify = new \Think\Verify();
        // 设置验证码字符为纯数字
        $Verify->codeSet = '0123456789';
        $Verify->length   = 4;
        $Verify->entry(); 

        /*
    	$config =    array(
		    'fontSize'	=>	14,// 验证码字体大小
		    'length'	=>	4,// 验证码位数
		    'useNoise'	=>	false,// 关闭验证码杂点
		    'imageW'	=>	'105',//宽度
		    'imageH'	=>	'30',//高度
		    'bg'		=>	array(247,247,247),//背景色
		);
    	$verify = new \Think\Verify($config);
		$verify->fontttf = '4.ttf'; 
		$verify->entry();
        */
    }

    /**
     +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作 
     * 可以在action控制器中重载
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getReturnUrl() 
    {
        return __CONTROLLER__.'?'.C('VAR_MODULE').'='.MODULE_NAME.'&'.C('VAR_ACTION').'='.C('DEFAULT_ACTION');
    }
    

	/**
	 * 用户检测
	 */
	public function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__MODULE__.'/Public/login');
			$this->error('没有登录');
		}
	}

}
?>