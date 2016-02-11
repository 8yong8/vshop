<?php
namespace Home\Controller;
use Think\Controller;
class MemberCouponController extends CommonController {

  protected $can_del = 0; //0不可删除 1可删除

  public function _initialize() {
	parent::_initialize();
	$this->db = D(CONTROLLER_NAME);
  }

  /**
   * 列表信息
   */
  public function index(){
	$model = D('memberCoupon as a');
	$where = $this->_search();//获得查询条件
	if(isset($_GET['_order'])) {
		$order = 'a.'.$_GET['_order'];
	}else {
		$order = !empty($sortBy)? $sortBy: 'a.id';
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if(isset($_GET['_sort'])) {
		$sort = $_GET['_sort']?'asc':'desc';
	}else {
		$sort = $asc?'asc':'desc';
	}
	if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	}else{
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 20;
	}
	$count = $model->where($where)->count();
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if($count>0){
	  //创建分页对象
	  $p = new \My\Page($count,$listRows);
	  $list = $model->table('`'.C('DB_PREFIX').'member_coupon` as a')->join('`'.C('DB_PREFIX').'coupon` as b on a.coupon_id=b.id')->field('a.id,a.create_time,a.use_time,a.status,a.member_name,b.title,b.btime,b.etime')->where($where)->order('a.id desc')->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $model->getlastsql();
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
	$page       = $p->Show();
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
   * 查询条件
   */
  public function _search(){
	if($_GET['coupon_id']!=""){
      $data['a.coupon_id'] = $_GET['coupon_id'];
	  $this->assign("coupon_id",$_GET['coupon_id']);
	}
	if($_GET['member_id']!=""){
      $data['member_id'] = $_GET['member_id'];
	  $this->assign("member_id",$_GET['member_id']);
	}
	if($_GET['member_name']!=""){
	  $model = M('member');
	  $data['member_name'] = $_GET['member_name'];
	  $member = $model->field('id')->where($data)->find();
      $data['member_id'] = $member['id'];
	  $this->assign("member_name",$_GET['member_name']);
	}
	if($_GET['status']!=""){
      $data['a.status'] = $_GET['status'];
	  $this->assign("status",$_GET['status']);
	}
	if($_GET['btime'] && $_GET['etime']){
	  $data['a.use_time'] = array('between',array(strtotime($_GET['btime']),strtotime($_GET['etime'])));
	  $this->assign('btime',$_GET['btime']);
	  $this->assign('etime',$_GET['etime']);
	}else if($_GET['btime']){
	  $data['a.use_time'] = array('gt',strtotime($_GET['btime']));
	  $this->assign('btime',$_GET['btime']);
	}else if($_GET['etime']){
	  $data['a.use_time'] = array('lt',strtotime($_GET['etime']));
	  $this->assign('etime',$_GET['etime']);
	}
	return $data;
  }

}
?>