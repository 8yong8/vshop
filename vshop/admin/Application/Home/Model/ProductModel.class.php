<?php 
namespace Home\Model;
use Think\Model;
class ProductModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','名称必须填写！'),
		array('artist_id','require','艺术家必须填写！'),
	);
}
?>