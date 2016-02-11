<?php 
namespace Home\Controller;
use Think\Controller;
class WeightController extends CommonController {

  /**
   * 添加信息
   */
  public function add(){
 	$model = D('Weight');
    if($_POST){
	  $_POST['create_time'] = time();
	  $cdata['source'] = $_POST['source'];
	  $cdata['sourceid'] = $_POST['sourceid'];
	  $vo = $model->where($cdata)->find();
	  if($vo){
	    redirect('Weight/edit/id/'.$vo['id']);
	  }
	  $pmodel = M($_POST['source']);
	  $data['id'] = $_POST['sourceid'];
	  $vo = $pmodel->field('product_id')->where($data)->find();
	  $_POST['product_id'] = $vo['product_id'];
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $result = $model->add();
	  //echo $model->getlastsql();exit;
	  if($result){
		$this->history($result);
		$this->success ('新增成功!');
	  }else{
		$this->error ('添加失败!');
	  }
	  exit;
	}
	if($_GET['source'] && $_GET['sourceid']){
	  $cdata['source'] = $_GET['source'];
	  $cdata['sourceid'] = $_GET['sourceid'];
	  $vo = $model->where($cdata)->find();
	  if($vo){
	    redirect(__APP__.'/Weight/edit/id/'.$vo['id']);
	  }
	  //$name = $_GET['source']==1 ? 'auction' : 'goods';
	  $model = D($_GET['source']);
	  $data['id'] = $_GET['sourceid'];
	  $vo = $model->field('id,product_name,member_name,realname')->where($data)->find();
	  //echo $model->getlastsql();
	  $this->assign('vo',$vo);
	  //dump($vo);
	}
	$this->display();
  }

  /**
   * 编辑信息
   */
  public function edit(){
	$model = D('Weight');
    if($_POST){
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	   $list=$model->save();
	  $pmodel = M($_POST['source']);
	  $data['id'] = $_POST['sourceid'];
	  $vo = $pmodel->field('product_id')->where($data)->find();
	  $_POST['product_id'] = $vo['product_id'];
	  if (false !== $list) {
		//成功提示
		$this->history($_POST['id']);
		if($_POST['source']==1){
		  $model = D('auction');
		}else{
		  $model = D('goods');
		}
		$wdata['id'] = $_POST['sourceid'];
		$sdata['qz'] = $_POST['qz'];
		$model->where($wdata)->save($sdata);
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  } 
	}else{
	  $data['id'] = $_GET['id'];
	  $vo = $model->where($data)->find();
	  $this->assign('vo',$vo);
	  $this->display();
	}
  }

  /**
   * 产品信息
   */
  function get_product(){
	if($_POST['source']==1){
	  $model = D('auction');
	}else{
	  $model = D('goods');
	}
	//$data['id'] = $_POST['id'];
	$data['product_name'] = array('like','%'.$_POST['key'].'%');
	$list = $model->field('id,product_name,member_name,realname')->where($data)->select();
	//echo $model->getlastsql();
	echo json_encode($list);
  }

  /**
   * 权重修改
   */
  function update($source,$sourceid){
	//修改权重
    $model = M('weight');
	$data['source'] = $source;
	$data['sourceid'] = $sourceid;
	$vo = $model->where($data)->find();
	$qz = $vo['like_count']*1+$vo['favorite_count']*2+$vo['comment_count']*3+ceil($vo['pv_count']/10);
	$sdata['qz'] = $qz;
	$model->where($data)->save($sdata);
	//修改产品权重
	$model = M($source);
	$pdata['id'] = $sourceid;
	$model->where($pdata)->save($sdata);
  }

}
?>