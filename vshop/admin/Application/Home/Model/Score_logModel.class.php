<?php 
namespace Home\Model;
use Think\Model;
class Score_logModel extends CommonModel {
	// 定义自动写入和更新的时间戳字段
	protected $autoCreateTimestamps = array('create_time');
	protected $autoUpdateTimestamps = array('update_time');
	// 自动验证设置
	protected $_validate	 =	 array(
		array('member_id','require','会员必须！'),
		array('score','require','积分必须！'),
		array('desc','require','内容必须！'),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1','ADD'),
		array('create_time','time',self::MODEL_INSERT,'function'),
	);

}
?>