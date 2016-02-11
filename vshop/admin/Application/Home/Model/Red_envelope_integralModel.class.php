<?php 
namespace Home\Model;
use Think\Model;
class Red_envelope_integralModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('member_id','require','会员必须！'),
		array('content','require','交易内容必须填写！'),
	);
}
?>