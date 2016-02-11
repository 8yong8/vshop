<?php
namespace Home\Model;
use Think\Model;
// 商品模型
class Product_itemModel extends CommonModel {
	public $_validate	=	array(
		);

	public $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		//array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>