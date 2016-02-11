<?php 
namespace Home\Model;
use Think\Model;
class ConfigModel extends Model {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！',1),
		array('value','require','参数必须！',1),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		array('create_time','time','ADD','function'),
		);


}


?>