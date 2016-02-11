<?php 
class PublicAction extends Action {
  
	//前置执行
	public function _initialize(){
		include_once C('PUBLIC_INCLUDE')."function.inc.php";
	}
  
	//空页面
	public function _empty(){
		$msg['status'] = 0;
		$msg['error_code'] = 8001;
		$msg['notice'] = '出错';
		echo json_encode($msg);
	}


	//注册
	public function register(){
		$model = D('Member'); 
		if (false === $model->create ()) {
			//错误提示
			$msg['error_code'] = 8002;
			$msg['notice'] = $model->getError ();
			echo json_encode($msg);exit;
		}
		$data['tel'] = $_POST['tel'];
		$data['salt'] = $salt = rand_string(6,-1);
		$psw = md5($_POST['password'].$salt.$salt[1]);
		$data['password'] = $psw;
		$mid = $model->add($data);
		if($mid){
			$msg['error_code'] = 0;
			$msg['notice'] = '注册成功';
			//生成token
			$token = create_token($mid,$salt);
			$vo['id'] = $mid;
			$vo['salt'] = $salt;
			//存储token
			set_token($vo,$token);
			$msg['token'] = $token;
			echo json_encode($msg);exit;		  
		}else{
			$msg['error_code'] = 8002;
			$msg['notice'] = '注册失败';
			echo json_encode($msg);exit;
		}
	}


	//登录检测
	public function checkLogin(){
		if($_SESSION['verify'] != md5($_POST['verify'])){
		  //$this->error('验证码错误!');
		}
		$model = M('Member');
		$data['mobile'] = $_POST['mobile'];
		$vo = $model->field('id,mobile,salt,password')->where($data)->find();
		if(!$_POST['baiduUserId'] || $_POST['baiduUserId']=='(null)'){
			$msg['error_code'] = 1001;
			$msg['notice'] = '百度ID不存在';
			echo  json_encode($msg);exit;	
		}
		if(!$vo){
			$msg['error_code'] = 1001;
			$msg['notice'] = '用户不存在';
			echo  json_encode($msg);exit;
		}
		if($vo['password']!=md5($_POST['password'].$vo['salt'].$vo['salt'][1])){
			$msg['notice'] = '密码错误';
			$msg['error_code'] = 8002;
			echo  json_encode($msg);exit;	
		}
		//清除其他百度id
		if($vo['baiduUserId']!=$_POST['baiduUserId'] && $vo['baiduUserId']){
		  //file_put_contents('./1.txt',$vo['baiduUserId'].'/'.$_POST['baiduUserId']);
		  //踢出
		  $sent_array['module'] = 'Public';
		  $sent_array['action'] = 'logout';
		  $sent_array['id'] = 0;
		  $custom_content = json_encode($sent_array);
		  $MsgContent = '在其他地方登陆';
		  push_msg($vo['baiduUserId'],$vo['mb_system'],$MsgContent,$custom_content);
		}
		$_data['id'] = $vo['id'];
		$_sdata['last_login_ip'] = _get_ip();
		$_sdata['login_count'] = $vo['login_count']+1;
		$_sdata['last_login_time'] = time();
		$_sdata['baiduUserId'] = $_POST['baiduUserId'];
		$_sdata['mb_system'] = $_POST['mb_system'] ? $_POST['mb_system'] : 2;
		$model->where($_data)->save($_sdata);
		//list($usec, $sec) = explode(' ', microtime());
		//echo ceil($usec*1000000);exit;
		//重新生成token
		$token = create_token($vo['id'],$vo['salt']);
		//存储token
		set_token($vo,$token);
		$vo['error_code'] = 0;
		$vo['token'] = $token;
		$vo['baiduUserId'] = $_POST['baiduUserId'];
		unset($vo['password']);
		echo  json_encode($vo);exit;
	}


	//注册短信请求
	public function reg_sms(){
		$tel = $_POST['tel'];
		if(!validateMobile($tel)){
			$msg['error_code'] = 8002;
			$msg['notice'] = '手机号码不对！';
			echo  json_encode($msg);exit;	  
		}
		$code = str_pad(rand(0,9999),4,0,STR_PAD_LEFT);
		$content = '验证码：'.$code.'，请勿将验证码泄漏给其他人【夜猫圈】';
		$model = M('member_verify');
		$data['tel'] = $tel;
		$v_vo = $model->where($data)->find();
		if($v_vo['status']==1){
			$msg['error_code'] = 8002;
			$msg['notice'] = '用户已验证';
			echo  json_encode($msg);exit;	  
		}
		if($v_vo){
			if(time()-$v_vo['update']<60){
				$msg['error_code'] = 8002;
				$msg['notice'] = '请稍后再发';
				echo  json_encode($msg);exit;	  
			}else{
			  if($v_vo['m']==date('m') && $v_vo['d']==date('d')){
				if($v_vo['sent_num']>4){
				  $msg['error_code'] = 80002;
				  $msg['notice'] = '每天最多发送5次,请明天再来';
				  echo  json_encode($msg);exit;				  
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
		  $sdata['member_id'] = 0;
		  $sdata['code'] = $code;
		  $sdata['tel'] = $tel;
		  $sdata['y'] = date('Y');
		  $sdata['m'] = date('m');
		  $sdata['d'] = date('d');
		  $sdata['sent_num'] = 1;
		  $sdata['msg'] = $content;
		  $sdata['ip'] = _get_ip();
		  $sdata['status'] = 0;
		  $sdata['update_time'] = time();
		  $v_vo['id'] = $model->add($sdata);
		}
		$result = sent_msm($tel,$content);
		if($result){
		  $msg['error_code'] = 0;
		  $msg['notice'] = '发送成功';
		  $msg['id'] = $code;
		  echo  json_encode($msg);exit;
		}else{
		  $msg['error_code'] = 2001;
		  $msg['notice'] = '发送失败';
		  echo  json_encode($msg);exit;	
		}
	}


	//定制执行判断是否推送
	function timing(){
		$model = M('files_user');
		$data['call_time'] = array('neq',0);
		$data['call_sent_status'] = 0;
		//$data['baiduUserId'] = array('neq','');
		$data['a.status'] = 0;
		$list = $model->field('a.*,b.baiduUserId')->table('`zy_files_user` as a')->join('`zy_user` as b on a.user_id=b.id')->where($data)->select();
		//$list = $model->where($data)->select();
		//echo $model->getlastsql();dump($list);exit;
		if(!$list){
		  return false;
		}
		foreach($list as $user){
		  if($user['baiduUserId']){
			//推送
			$sent_array['module'] = 'Files';
			$sent_array['action'] = 'show';
			$sent_array['id'] = $user['fid'];
			$custom_content = json_encode($sent_array);
			$result = push_msg2($user['user_id'],$user['title'],$custom_content);
			if($result){
			  $sdata['sent_status'] = 1;
			  $sdata['call_sent_status'] = 1;
			  $sdata['sent_time'] = time();
			  $wdata['id'] = $user['id'];
			  $model->where($wdata)->save($sdata);
			}else{
			  $error[] = $user;
			}
		  }else{
			$error[] = $user;
		  }
		}
		if($error){
			F('files_error',$error,'./');
		}else{
		  unlink('./files_error.php');
		}
		//dump($list);
	}


}
?>