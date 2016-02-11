<?php 
namespace Home\Model;
use Think\Model;
class NodeModel extends CommonModel{

	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
	);

	// 自动完成
	protected $_auto	 =	 array(
		array('title','update_after',self::MODEL_UPDATE,'callback'),
	);

	//title 修改回调方法
	public function update_after(){
	  $model = M('ClassNode');
	  $wdata['nid'] = $_POST['id'];
	  $sdata['nname'] = $_POST['title'];
	  $model->where($wdata)->save($sdata);
	  return $_POST['title'];
	}
}
?>