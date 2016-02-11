<?php 

import('@.Model.CommonModel');
class MemberModel extends CommonModel {
	// 定义自动写入和更新的时间戳字段
	protected $autoCreateTimestamps = array('create_time');
	protected $autoUpdateTimestamps = array('update_time');
	// 自动验证设置
	protected $_validate	 =	 array(
		array('tel','require','手机号码必须！',1,'',1),
		array('tel','validateMobile','手机格式错误！',1,'function',1),//添加必须验证
		array('tel','','帐号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
		array('code','check_code','验证码错误！',0,'callback',1),
		array('code','require','验证码必须！',1,'',1),
		//array('username','require','用户名必须！'),
		array('password','require','密码必须！'),
		//array('password','checkPwd','密码格式不正确',0,'function'),
		//array('email','email','邮箱格式错误！',2),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		);

	public function check_code($code){
	  $model = M('member_verify');
	  $data['type'] = 'reg';
	  $data['tel'] = $_POST['tel'];
	  $vo = $model->field('code,status')->where($data)->find();
	  if($vo['status']==1){
		$this->error = '验证码已被使用';
	    return false;
	  }
	  if($vo['code']!=$code){
	    return false;
	  }
	  return true;

	}

}
?>