<?php
namespace Home\Controller;
use Think\Controller;
class OrderPromotionController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D(CONTROLLER_NAME);
	$this->pdb = D('Product');
	$this->ppldb = D('ProductPmList');
  }

  /**
   * 添加信息 前置
   */
  public function _before_add(){
    if(IS_POST){
	  $_POST['btime'] = strtotime($_POST['btime']);
	  $_POST['etime'] = strtotime($_POST['etime']);
	}
  }

  /**
   * 编辑信息 前置
   */
  public function _before_edit(){
    if(IS_POST){
	  $_POST['btime'] = strtotime($_POST['btime']);
	  $_POST['etime'] = strtotime($_POST['etime']);
	}
  }

  /**
   * 添加信息
   */
  public function add(){
    if(IS_POST){
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if (false === $this->db->create ()) {
		  $this->error($this->db->getError ());	
		}
	  //保存当前数据对象
	  $result = $this->db->add ();
	  if($result){
		if($_POST['product_id']){
		  $model = M('ProductPmList');
		  $pmodel = M('Product');
		  foreach($_POST['product_id'] as $product_id){
			$add_data['pm_type'] = 'order';
			$add_data['pm_id'] = $result;
			$add_data['product_id'] = $product_id;
			$add_data['info'] = serialize($_POST);
			$add_data['btime'] = $_POST['btime'];
			$add_data['etime'] = $_POST['etime'];
			$this->ppldb->add($add_data);
			//商品参加促销
			$p_wdata['id'] = $product_id;
			$p_sdata['is_prom'] = 1;
			$this->pdb->where($p_wdata)->save($p_sdata);
		  }
		}
		$this->success('添加成功');	
	  }else{	
		  $this->error('添加失败');	
	  }
	}else{
	  $this->display();
	}
  }

  /**
   * 编辑信息
   */
  public function edit(){
	  if(IS_POST){
		//修改
		$wdata['prom_type'] = 'order';
		$wdata['prom_id'] = $_POST['id'];
		$sdata['info'] = serialize($_POST);
		$sdata['btime'] = $_POST['btime'];
		$sdata['etime'] = $_POST['etime'];
		$sdata['status'] = $_POST['status'];
		$this->ppldb->where($wdata)->save($sdata); 
		if($_POST['product_id']){
		  foreach($_POST['product_id'] as $product_id){
			$add_data['pm_type'] = 'order';
			$add_data['pm_id'] = $_POST['id'];
			$add_data['product_id'] = $product_id;
			//添加
			$add_data['info'] = serialize($_POST);
			$add_data['btime'] = $_POST['btime'];
			$add_data['etime'] = $_POST['etime'];
			$add_data['status'] = $_POST['status'];
			$this->ppldb->add($add_data);
			//商品参加促销
			$p_wdata['id'] = $product_id;
			$p_sdata['is_pm'] = 1;
			$this->pdb->where($p_wdata)->save($p_sdata);
		  }

		}
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['update_time'] = time();
		if (false === $model->create ()) {
		  $this->error ( $model->getError () );
		}
		// 更新数据
		$list = $model->save ();
		if (false !== $list) {
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
		} else {
		  //错误提示
		  $this->error ('编辑失败!');
		}
	  }else{
		$id = $_REQUEST [$this->db->getPk ()];
		$vo = $this->db->getById ( $id );
		$this->assign ( 'vo', $vo );
		//参加优惠产品
		$data['a.pm_type'] = 'order';
		$data['a.pm_id'] = $id;
		$list = $this->db->field('a.id,name,subtitle,lit_pic,price,stock')->table('`'.C('DB_PREFIX').'product_pm_list` as a')->join('`'.C('DB_PREFIX').'product` as b on a.product_id=b.id')->where($data)->select();
		$this->assign('list',$list);
		$this->display();
	  }
  }

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['name']!=""){
      $data['name'] = $_GET['name'];
	  $this->assign("name",$_GET['name']);
	}
	return $data;
  }

  /**
   * 产品列表
   */
  public function product_list(){
    $data['prom_type'] = 'order';
	$data['prom_id'] = $_GET['pid'];
	$subQuery = $this->ppldb->field('product_id')->where($data)->select(false);
	$where = 'id not in '.$subQuery.' AND status=1';
	if($_GET['keyword']){
	  $where .= " AND name like '%".$_GET['keyword']."%'";
	  $this->assign('keyword',$_GET['keyword']);
	}
	$count = $this->pdb->where($where)->count();
	//echo $this->pdb->getlastsql();exit;
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if($count>0){
	  //创建分页对象
	  $listRows = 10;
	  $p = new \My\Page($count,$listRows);
	  $list = $this->pdb->field('id,name,subtitle,lit_pic,price,stock')->where($where)->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $this->pdb->getlastsql();exit;
	  $page       = $p->Show();
	  //dump($page);exit;
	  $this->assign('page',$page);
	} 
	$this->assign('list',$list);
    $this->display();
  }

  /**
   * ajax删除
   */
  public function ajax_del() {
	//删除指定记录
	$id = $_REQUEST ['id'];
	if (isset ( $id )) {
		$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
		//被删除促销的产品列表
		$list = $this->ppldb->field('product_id')->where($condition)->group('product_id')->select();
		if (false !== $this->ppldb->where ( $condition )->delete ()) {
			//echo $model->getlastsql();exit;
			$ids =  explode ( ',', $id );
			foreach($ids as $id){
			  $this->history($id);
			}
			$this->check_product_prom($list);
			$msg['callback'] = 'remove("tr_'.$id.'")';
			//$msg['notice'] = '删除成功！';
			ajaxSucReturn($msg);
		} else {
			ajaxErrReturn('删除失败！');
		}
	} else {
		ajaxErrReturn( '非法操作' );
	}
  }

  /**
   * 检查产品是否有促销
   */
  protected function check_product_prom($products){
	$data['etime'] = array('gt',time());
	$data['status'] = 1;
	foreach($products as $product){
	  $ids[] = $product['product_id'];
	  /*
	  $data['product_id'] = $id;
	  $count = $this->ppldb->where($data)->count();
	  if($count>0){
	    
	  }else{
	  
	  }
	  */
	}
	$data['product_id'] = array('in',$ids);
	$list = $this->ppldb->where($data)->group('prom_type')->select();//所有删除产品的促销
	if($list){
		foreach($list as $p){
		  $product_id = $p['product_id'];
		  //判断该产品是否在删除列表,不在删除列表
		  if (!in_array($product_id,$ids)) {
			$wdata['id'] = $product_id;
			$sdata['is_prom'] = 0;
			$this->pdb->where($wdata)->save($sdata);
		  }
		}
	}else{
		$wdata['id'] = array('in',$ids);
		$sdata['is_prom'] = 0;
		$this->pdb->where($wdata)->save($sdata);	
	}
  }

  /**
   * 禁用
   */
  public function forbid() {
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_REQUEST [$pk];
	$condition = array ($pk => array ('in', $id ) );
	$list=$model->forbid ( $condition );
	if ($list!==false) {
		$ids =  explode ( ',', $id );
		foreach($ids as $id){
		  $this->history($id);
			//修改
			$wdata['prom_type'] = 'order';
			$wdata['prom_id'] = $id;
			$sdata['status'] = 0;
			$this->ppldb->where($wdata)->save($sdata); 
		}
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态禁用成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 启用
   */
  function resume() {
	//恢复指定记录
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->resume ( $condition )) {
		$ids =  explode ( ',', $id );
		foreach($ids as $id){
			//修改
			$wdata['prom_type'] = 'order';
			$wdata['prom_id'] = $id;
			$sdata['status'] = 1;
			$this->ppldb->where($wdata)->save($sdata); 
		  $this->history($id);
		}
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态恢复成功！' );
	} else {
		//$model->getlastsql();exit;
		$this->error ( '状态恢复失败！' );
	}
  }

}
?>