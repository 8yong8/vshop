<?php 
namespace Home\Model;
use Think\Model;
class CategoryModel extends CommonModel {

	// 定义自动写入和更新的时间戳字段
	protected $autoCreateTimestamps = array('create_time');
	protected $autoUpdateTimestamps = array('update_time');
	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
		array('email','email','邮箱格式错误！',2),
		array('content','require','内容必须'),
		array('verify','require','验证码必须！'),
		array('verify','CheckVerify','验证码错误',0,'callback'),
		array('title','','标题已经存在',0,'unique','add'),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		);

	public function CheckVerify() {
		return md5($_POST['verify']) == $_SESSION['verify'];
	}
}
?>