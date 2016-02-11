<?php
namespace Home\Controller;
use Think\Controller;
class HelpController extends CommonController {

  public $up_fields = array('sort','title'); //可修改字段

  public function _initialize() {
	parent::_initialize();
	$this->db = M('help');
  }

  /**
   * 列表
   */
  public function index(){
	$data['pid'] = 0;
	$list = $this->db->where($data)->order('sort asc,id asc')->select();
	$this->assign('list',$list);
	$this->display();
  }

  /**
   * 子节点列表
   */
  public function help_child(){
	$sqlmap = array();
	$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:0;
	$sqlmap['pid'] = $pid;
	$field = 'id,title,content,keyword,status,sort';
	$list = $this->db->field($field)->where($sqlmap)->order(array('sort'=>'ASC','id'=>'ASC'))->select();
	//echo $this->db->getlastsql();exit;
	foreach ($list as $key => $value) {
		$list[$key]['state'] = $this->has_child($value['id']) ? 'closed' : 'open';
	}
	$this->assign('pid',$pid);
	$this->assign('list',$list);
	$this->display();
	//echo json_encode($data);
  }
	
  /**
   * 是否有子节点
   */
  function has_child($id){
	$rows = $this->db->where(array('pid'=>$id))->count();
	return $rows > 0 ? true : false;
  }

  /**
   * 添加信息
   */
  public function add(){
    if(IS_POST){
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if($_POST['pid'])$this->assign('jumpUrl',__CONTROLLER__.'/add/id/'.$_POST['pid']);
		if (false === $this->db->create ()) {
		  $this->assign('error',$this->db->getError ());
		  $this->display('Public:error2');	
		}
	  //保存当前数据对象
	  $result = $this->db->add ();
	  //echo $this->db->getlastsql();exit;
	  if($result){
		  $message = '添加成功';
		  $this->assign('message',$message);
		  $this->display('Public:success2');	  
	  }else{
		  $this->assign('error','添加失败');
		  $this->display('Public:error2');	  
	  }
	}else{
	  if($_GET['id']){
	    $data['id'] = $_GET['id'];
		$vo = $this->db->where($data)->find();
		$this->assign('vo',$vo);
	  }
	  $this->display();
	}
  }

  /**
   * 编辑信息
   */
  public function edit(){
    if(IS_POST){
	  $wdata['id'] = $_POST['id'];
	  $sdata['title'] = $_POST['title'];
	  $sdata['content'] = $_POST['content'];
	  $sdata['sort'] = $_POST['sort'];
	  $result = $this->db->where($wdata)->save($sdata);
	  //echo $this->db->getlastsql();exit;
	  if($result){
		  $message = '编辑成功<script>setTimeout("art.dialog.close()", 1000 );var obj = $(artDialog.open.origin.document);obj.find("#row_'.$_POST['id'].'").find(".tree-title").html("'.$_POST['title'].'");obj.find("#row_'.$_POST['id'].'").find("td").eq(1).html("'.$_POST['sort'].'");</script>';
		  $this->assign('message',$message);
		  $this->display('Public:success2');	  
	  }else{
		  $this->assign('jumpUrl',__CONTROLLER__.'/edit/id/'.$_POST['id']);
		  $this->assign('error','编辑失败');
		  $this->display('Public:error2');	  
	  }
	}else{
	  $wdata['id'] = $_GET['id'];
	  $vo = $this->db->where($wdata)->find();
	  $this->assign('vo',$vo);
	  $this->display();
	}
  }


}

?>