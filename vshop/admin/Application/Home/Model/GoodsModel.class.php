<?php 
namespace Home\Model;
use Think\Model;
class GoodsModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('tid','require','分类必须填写！'),
		array('name','require','标题必须填写！'),
	);
	public $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_BOTH,'function'),
	);
}
?>