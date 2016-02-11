<?php 
namespace Home\Controller;
use Think\Controller;
class HistoryController extends CommonController {

  /**
   * 列表 前置
   */
  public function _before_index(){
    $model = D('Node');
	$data['level'] = 2;
	$data['status'] = 1;
    $models = $model->where($data)->order('sort asc')->select();
	$data['level'] = 3;
	$actions = $model->where($data)->order('sort asc')->select();
	$this->assign('models',$models);
	$this->assign('actions',$actions);
  }

  /**
   * 查询条件
   */
  public function _search(){
	$map = array ();
	if($_GET['model']!=""){
      $map['model'] = $_GET['model'];
	  $this->assign("model",$_GET['model']);
	}
	if($_GET['action']!=""){
      $map['action'] = $_GET['action'];
	  $this->assign("action",$_GET['action']);
	}
	if($_GET['sourceid']!=""){
      $map['sourceid'] = $_GET['sourceid'];
	  $this->assign("sourceid",$_GET['sourceid']);
	}
	if($_GET['username']!=""){
      $map['username'] = $_GET['username'];
	  $this->assign("username",$_GET['username']);
	}
	//array('between', array('1001', '5000'));
	if($_GET['btime']!="" && $_GET['etime']!=""){
	  $btime = strtotime($_GET['btime']);
	  $etime = strtotime($_GET['etime']);
	  $map['create_time'] = array('between', array($btime, $etime));
	  $this->assign("btime",$_GET['btime']);
	  $this->assign("etime",$_GET['etime']);
	}else if($_GET['btime']!=""){
	  $btime = strtotime($_GET['btime']);
      $map['create_time'] = array('egt',$btime);
	  $this->assign("btime",$_GET['btime']);	
	}else if($_GET['etime']!=""){
	  $etime = strtotime($_GET['etime']);
      $map['create_time'] = array('elt',$etime);
	  $this->assign("etime",$_GET['etime']);	
	}
	return $map;    
  }

  /**
   * 清空数据
   */
  public function clearall(){
    $model = D('');
    $model->query('TRUNCATE TABLE `'.C('DB_PREFIX').'history`');
	$this->assign('jumpUrl',__CONTROLLER__);
	$this->success ('数据已清空!');
  }

}
?>