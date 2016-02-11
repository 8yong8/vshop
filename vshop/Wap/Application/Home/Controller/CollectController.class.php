<?php
namespace Home\Controller;
use Think\Controller;
class CollectController extends CommonController {
  /**
   * 列表页 前置
   */
  public function _before_index(){
	$this->assign('headerTitle','收藏列表页');
	$this->assign('headerKeywords','收藏列表页');
	$this->assign('headerDescription','收藏列表页');
	$this->assign('wx_title','收藏列表页');
	$this->assign('wx_desc',C('wx_desc'));
  }

  /**
   * 添加收藏
   */
  public function add(){
	$model = D('Collect');
	if($_POST['source']!='Product' && $_POST['source']!='News'){
	  ajaxErrReturn('非添加类型');
	}
	$data['source'] = $_POST['source'];
	$data['sourceid'] = $_POST['sourceid'];
	$data['member_id'] = $this->user['id'];
	$count = $model->where($data)->count();
	if($count>0){
	  ajaxErrReturn('已收藏');
	}
	if($_POST['source']=='Product'){
	  $options['dir'] = get_dir($_POST['sourceid']);
	  $vo = getcache('Product:detail',$options);//产品基本信息
	  $info['id'] = $vo['id'];
	  $info['name'] = $vo['name'];
	  $info['subtitle'] = $vo['subtitle'];
	  $info['lit_pic'] = $vo['lit_pic'];
	  $info['cat_id'] = $vo['cat_id'];
	  $info['cat_name'] = $vo['cat_name'];
	  $info['brand_id'] = $vo['brand_id'];
	  $info['brand_name'] = $vo['brand_name'];
	  $info['market_price'] = $vo['market_price'];
	  $info['price'] = $vo['price'];
	  $info['nw'] = $vo['nw'];
	  $info = serialize($info);
	}
	$data['info'] = $info;
	$data['create_time'] = time();
	$data['ip'] = time();
	$result = $model->add($data);
	if($result){
		if($_POST['source']=='Product'){
		  update_product('collect_num',1,$_POST['sourceid']);
		}
		//权重修改
		$model = M('Weight');
		unset($data['member_id']);
		$model->where($data)->setInc('favorite_count');
		$this->update_weight($_POST['source'],$_POST['sourceid']);

		$msg['error_code'] = 0;
		$msg['id'] = $result;
		$msg['notice'] = '收藏成功';
		ajaxSucReturn($msg); 
	}else{
		ajaxErrReturn('收藏失败');
	}
	exit;
  }

  /**
   * 删除收藏
   */
  public function delete(){
	$model = D('Collect');
	$data['id'] = $_POST['id'];
	$vo = $model->field('id,source,sourceid')->where($data)->find();
	$data['member_id'] = $this->user['id'];
	$result = $model->where($data)->delete();
	if($result){
		$msg['error_code'] = 0;
		$msg['notice'] = '删除成功';
		if($vo['source']=='Product'){
		  update_product('collect_num',2,$vo['sourceid']);
		}
		ajaxSucReturn($msg);  
	}else{
		ajaxErrReturn('删除失败'); 	  
	}
  }

}
?>