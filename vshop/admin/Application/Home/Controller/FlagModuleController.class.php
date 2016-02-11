<?php 
namespace Home\Controller;
use Think\Controller;
class FlagModuleController extends CommonController {

  public function _before_index(){
	$_REQUEST ['listRows'] = 50;
	$this->get_flag_node();
  }
/*
//首页
  public function index(){
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$this->display();
  }
*/

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['fid']!=""){
	  $data['fid'] = $_GET['fid'];
	  $this->assign("fid",$_GET['fid']);
	}
	if($_GET['moudel_name']!=""){
      $data['moudel_name'] = $_GET['moudel_name'];
	  $this->assign("moudel_name",$_GET['moudel_name']);
	}
	if($_GET['webset']!=""){
      $data['webset'] = $_GET['webset'];
	  $this->assign("webset",$_GET['webset']);
	}
	return $data;
  }

  public function _before_add(){
	$this->get_flag_node();
  }

  /**
   * 添加信息
   */
  function add(){
	if($_POST){
		$model = D('FlagModule');
		$node_ids = $_POST['nodes'];
		$flags = $_POST['flags'];
		$flags_data = $this->get_flags();
		foreach($flags_data as $key=>$val){
		  $key  = $val['id'];
		  $all_flags[$key]['name'] = $val['name'];
		  $all_flags[$key]['id'] = $val['id'];
		}
		$node_model = M('node');
		$node_data['id'] = array('in',$node_ids);
		$nodes = $node_model->where($node_data)->select();
		//echo $node_model->getlastsql();exit;
		//dump($all_flags);
		foreach($nodes as $node){
		  foreach($flags as $flag){
			  $data['moudel_name'] = $node['name'];
			  $data['fid'] = $flag;
			  $count = $model->where($data)->count();
			  if($count==0){
				$data['fname'] = $all_flags[$flag]['name'];
				$data['moudel_title'] = $node['title'];
				$data['create_time'] = time();
				$model->add($data);
				//echo $model->getlastsql();exit;
				unset($data['fname']);
				unset($data['moudel_title']);
				unset($data['create_time']);
			  }
		  }
		}
		$this->success ('操作完成!');
		exit;
	}
    $this->display();
  }

  /**
   * 生成缓存
   */
  function GiveCache(){
	return;
	$k = 0;
	$model = D('FlagModule');
	  if(!file_exists(C('DATA_CACHE_PATH').'/flag/'))mk_dir(C('DATA_CACHE_PATH').'/flag/');
	  $n_data['status'] = 1;
	  $nodes = $model->field('moudel_name')->where($n_data)->group('moudel_name')->select();
	  //dump($nodes);exit;
	  if($nodes){
	    foreach($nodes as $n_val){
		  if(!file_exists(C('DATA_CACHE_PATH').'/flag/'.$n_val['moudel_name']))mk_dir(C('DATA_CACHE_PATH').'/flag/'.$n_val['moudel_name']);
		  $f_data['a.status'] = 1;
		  $f_data['a.moudel_name'] = $n_val['moudel_name'];
		  $flags = $model->field('a.*,b.keep')->table('`'.C('DB_PREFIX').'flag_module` as a')->join('`'.C('DB_PREFIX').'flag` as b on a.fid=b.id')->where($f_data)->order('a.id asc')->select();
		  foreach($flags as $flag){
			 $fid = $flag['fid'];
		     $list[$fid] = $flag;
			 $list[$fid]['name'] = $flag['fname'];
			 $list[$fid]['id'] = $flag['fid'];
		  }
		  //echo $model->getlastsql();exit;
		  F('list',$list,C('DATA_CACHE_PATH').'/flag/'.$n_val['moudel_name'].'/');
		  unset($list);
		}
	  }
	$this->assign('jumpUrl',__CONTROLLER__);
	$this->success ('操作完成!');
  }

  /**
   * 节点属性
   */
  public function get_flag_node(){
	//节点
    //$nodes = include C('DATA_CACHE_PATH').'node/list.php';
	$model = M('node');
	$data['status'] = 1;
	$data['name'] = array('in',array('Article','Product','Shoplist','Advert'));
	$nodes = $model->where($data)->select();
	$this->assign('nodes',$nodes);
	//属性
    $flags = $this->get_flags();
	$this->assign('flags',$flags);
  }

}
?>