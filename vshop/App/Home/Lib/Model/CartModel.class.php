<?php 

import('@.Model.CommonModel');
class CartModel extends CommonModel {
	// 定义自动写入和更新的时间戳字段
	protected $autoCreateTimestamps = array('create_time');
	protected $autoUpdateTimestamps = array('update_time');
	// 自动验证设置
	protected $_validate	 =	 array(
		//array('item_id','require','产品必须！',1,'',1),
		//array('item_id','number','产品ID必须数字！',1,'',1),
		array('num','require','数量必须！',1,'',1),
		array('num','number','数量必须数字！',1,'',1),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		//array('status','1','ADD'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);

}
?>