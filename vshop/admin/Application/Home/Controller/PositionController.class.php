<?php 
namespace Home\Controller;
use Think\Controller;
class PositionController extends CommonController {

  public $up_fields = array('sort','name'); //可修改字段

  public function _initialize() {
	parent::_initialize();
	$this->db = D(CONTROLLER_NAME);
	$this->ddb = D('PositionData');
  }

  /**
   * 列表
   */
  public function index() {
	$data['pid'] = $_GET['pid'] ? $_GET['pid'] : 0;
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$this->assign('list',$list);
	if($_GET['pid']){
		$data2['id'] = $_GET['pid'];
		$parent = $this->db->where($data2)->find();
		$this->assign('parent',$parent);
	}
	$this->display();
  }

  /**
   * 添加 前置
   */
  public function _before_add(){
    if(!IS_POST){
	  if($_GET['id']){
	    $data['id'] = $_GET['id'];
		$parent = $this->db->where($data)->find();
		$this->assign('parent',$parent);
		$this->assign('pid',$_GET['id']);
		$this->Predecessor();
	  }
	  
	}else{
		
	}
  }

  /**
   * 编辑 前置
   */
  public function _before_edit(){
	if(!IS_POST){
	  $this->Predecessor();
	}else{
  
	}
  }

  /**
   * 子节点数量更新
   */
  protected function _after_add($id){
    $data['id'] = $id;
	$vo = $this->db->field('id,pid')->where($data)->find();
	if($vo && $vo['pid']){
	  $data2['pid'] = $vo['pid'];
	  $num = $this->db->where($data2)->count();
	  $wdata['id'] = $vo['pid'];
	  $sdata['child_num'] = $num;
	  $this->db->where($wdata)->save($sdata);
	}
  }

  /**
   * 公用
   */
  public function Predecessor(){
	$data['status'] = 1;
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$tree = new \My\Tree($list);
	$tree->name_field = 'title';
	$list = $tree->get_tree('0');
	//dump($list);exit;
	$this->assign('list',$list);
  }

  /**
   * 排序页面
   */
  public function sort(){
	$data['position_id'] = $_GET['sortId'];
	$count = $this->ddb->where($data)->count();
	//创建分页对象
	$listRows = '20';
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno-1)*$page_size;
	$p = new \My\Page($count,$listRows);
	$list = $this->ddb->field('*')->where($data)->order('sort asc,id asc')->limit($p->firstRow.','.$p->listRows)->select();
	$page = $p->Show();
	$this->assign('page',$page);
	$this->assign('list',$list);
    $this->display();
  }

  /**
   * 保存排序
   */
  public function saveSort(){
	$fid = $_POST['flagid'];
	$dostr = $_POST['dostr'];
	$list = explode('#',$dostr);
	$y = 1;
	foreach($list as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }else{
	     $ar = explode(':',$val);
		 if($ar[1]==0){
		   $list2[$y] = $val;
		   $y++;
		 }else{
		   $list1[$y] = $val;
		   $y++;
		 }
	  }
	}
	if($list1 && $list2){
	  $list1 = array_merge($list1,$list2);
	}elseif($list2){
	  $list1 = $list2;
	}
	foreach($list1 as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }
	  $wdata['id'] = $ar[0];
	  $data['sort'] = $ar[1];
	  $result = $this->ddb->where($wdata)->save($data);
	}
	$this->success ('排序完成!');
  }
  
  /**
   * 是否有子节点判断
   */
  function has_child($id){
	$rows = $this->db->where(array('pid'=>$id))->count();
	return $rows > 0 ? true : false;
  }

  /**
   * 删除节点
   */
  public function ajax_del(){
	$id=intval($_POST['id']);
	if($id>0){
		if(!$this->has_child($id)){
			$this->db->where('id='.$id)->delete();
			$msg['callback'] = "remove('row_".$id."');";
			$msg['notice'] = '恭喜你，删除区域成功！';
			ajaxSucReturn($msg); 
		}else{
			ajaxErrReturn('请先删除子节点！'); 
		}
	}else{
	   ajaxErrReturn('非法操作，请联系管理员！'); 
	}
  }

  /**
   * 查询条件
   */
  public function _search2(){
	$data = array();
	if($_GET['id']!=""){
      $data['position_id'] = $_GET['id'];
	  $this->assign("id",$_GET['id']);
	}
	if($_GET['title']!=""){
      $data['title'] = array('like','%'.$_GET['title'].'%');
	  $this->assign("title",$_GET['title']);
	}

	return $data;
  }

  /**
   * 内容列表
   */
  public function substance(){
	if(!$_GET['id']){
	  $this->error('推荐位没有');
	}
	$data['id'] = $_GET['id'];
	$parent = $this->db->where($data)->find();
	$this->assign('parent',$parent);
	$where = $this->_search2();//获得查询条件
	if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	}else{
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 20;
	}
	$count = $this->ddb->where($where)->count();
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if($count>0){
	  //创建分页对象
	  //$listRows = 1;
	  $p = new \My\Page($count,$listRows);
	  $list = $this->ddb->field('id,title,data_type,lit_pic,create_time,sort,status')->where($where)->order('sort asc,id desc')->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $this->ddb->getlastsql();
	  //分页跳转的时候保证查询条件
	  foreach($map as $key=>$val) {
		if(is_array($val)) {
			foreach ($val as $t){
				$p->parameter	.= $key.'[]='.urlencode($t)."&";
			}
		}else{
			$p->parameter   .=   "$key=".urlencode($val)."&";        
		}
	  }
	  //分页显示
	  $page = $p->Show();
	}
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	$this->assign('list',       $list);
	$this->assign('sort',       $sort);
	$this->assign('order',      $order);
	$this->assign('sortImg',    $sortImg);
	$this->assign('sortType',   $sortAlt);
	$this->assign("page",       $page);
    $this->display();
  }

  /**
   * 内容添加
   */
  public function sub_add() {
	  if(IS_POST){
		if($_POST['data_type']==''){
		  $this->error('关联必须');
		}
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		$_POST['params'] = serialize($_POST['params']);
		if($this->checkFileUp()){
			$this->upload();
		}
		$_POST['position_name'] = $this->db->where('id='.$_POST['position_id'])->getField('title');
		if (false === $this->ddb->create ()) {
			$this->error ( $this->ddb->getError () );
		}
		//保存当前数据对象
		$list = $this->ddb->add ();
		if ($list!==false) { 
			//保存成功
			$this->update_node($_POST['position_id']);
			$this->assign('jumpUrl',__CONTROLLER__.'/sub_add/id/'.$_POST['position_id']);
		    $this->history($list);
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}	  
	  }else{
		$data['id'] = $_GET['id'];
		$vo = $this->db->where($data)->find();
		$this->assign('vo',$vo);
		$this->Predecessor();
		$this->display ();
	  }
  }

  /**
   * 内容编辑
   */
  public function sub_edit(){
	  if(IS_POST){
		$_POST['update_time'] = time();
		if($this->checkFileUp()){
			$this->upload();
		}
		$_POST['params'] = serialize($_POST['params']);
		if (false === $this->ddb->create ()) {
		  $this->error ( $model->getError () );
		}
		$list = $this->ddb->save ();
		if (false !== $list) {
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
		} else {
		  //错误提示
		  $this->error ('编辑失败!');
		}
	  }else{
		$id = $_REQUEST [$this->ddb->getPk ()];
		$vo = $this->ddb->getById ( $id );
		if($vo['data_type']=='product_list'){
			$types = ProductCategoryController::lists();
		}else if($vo['data_type']=='article_list'){
			$types = ClassifyController::lists();
		}
		$this->assign('types',$types);
		$vo['params'] = unserialize($vo['params']);
		$this->assign ( 'vo', $vo );
		$this->Predecessor();
		$this->display();
	  }
  }

  /**
   * 更新节点数
   */
  protected function update_node($position_id){
	$data['position_id'] = $position_id;
	$count = $this->ddb->where($data)->count();
    $wdata['id'] = $position_id;
	$sdata['child_num'] = $count;
	$this->db->where($wdata)->save($sdata);
  }

  /**
   * 删除节点内容
   */
  public function ajax_sub_del(){
    $data['id'] = $_POST['id'];
	$result = $this->ddb->where($data)->delete();
	if($result){
		$msg['notice'] = '删除成功！';
		ajaxSucReturn($msg); 
	}else{
		ajaxErrReturn('删除失败！'); 
	}  
  }

  /**
   * 启用
   */
  public function sub_resume(){
	$options['id'] = array('in',explode(',',$_GET['id']));
	if(FALSE === $this->ddb->where($options)->setField('status',1)){
		$this->error ('启用失败!');
	}else {
		$this->success ('启用成功!');
	}
  }

  /**
   * 禁用
   */
  public function sub_forbid(){
	$options['id'] = array('in',explode(',',$_GET['id']));
	if(FALSE === $this->ddb->where($options)->setField('status',0)){
		$this->error ('禁用失败!');
	}else {
		$this->success ('禁用成功!');
	}
  }

}
?>