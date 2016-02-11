<?php 
namespace Home\Controller;
use Think\Controller;
class ClassController extends CommonController {

  /**
   * 列表 前置
   */
  function _before_index(){
	$_REQUEST ['listRows'] = 50;
  }

  /**
   * 编辑 前置
   */
  public function _before_edit(){
    $model = M('class_node');
	$wdata['cid'] = $_POST['id'];
	$sdata['cname'] = $_POST['name'];
	$model->where($wdata)->save($sdata);
  }

  /**
   * 排版
   */
  function typeset(){
    $model = D('Class_node');
    $data['cid'] = $_GET['id'];
	//已选中节点
	$list1 = $model->where($data)->select();
	foreach($list1 as $val){
	  $list[] = $val['nid'];
	}
	$this->assign('list',$list);
	//已被选节点
	$sld_data['cid'] = array('neq',$_GET['id']);
	$list2 = $model->where($sld_data)->select();
	foreach($list2 as $val){
	  $sld_ids[] = $val['nid'];
	}
	$this->assign('sld_ids',$sld_ids);
	//dump(in_array(178,$sld_ids));dump($sld_ids);exit;
	//所有节点
	$nmodel = D('Node');
	$ndata['status'] = 1;
	$ndata['level'] = 2;
	$nlist = $nmodel->where($ndata)->order('sort asc')->select();
	foreach($nlist as $key=>$val){
	  $id = $val['id'];
	  if(in_array($id,$sld_ids)){
	    $nlist[$key]['disabled'] = 1;//不能选择
	  }
	}
	//dump($nlist);exit;
	$this->assign('nlist',$nlist);	
	//所有版块
	$cmodel = D('Class');
	$clist = $cmodel->order('id desc')->select();
	$this->assign('clist',$clist);
	$this->display();
  }

  /**
   * 排版保存
   */
  function savelayout(){
    $model = D('Class_node');
	$data['cid'] = $_POST['cid'];
	$result = $model->where($data)->delete();
	$array = explode(',',$_POST['ids']);
	$clist = include './cache/class/list.php';
	$cname = $clist[$_POST['cid']]['name'];
	$nlist = include './cache/node/list.php';
	foreach($array as $val){
	  $data['nid'] = $val;
	  $data['cname'] = $cname;
	  $data['nname'] = 	$nlist[$val]['title'];
	  $result = $model->add($data);
	}
	if($result){
	  //$this->GiveCache(0);
	  echo "保存成功";
	}else{
	  echo "保存失败";
	}
  }

  /**
   * 排序页面
   */
  public function sort(){
	$model = D('Class');
	$count = $model->where($data)->count();
	//创建分页对象
	$listRows = '20';
	$p = new \My\Page($count,$listRows);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno-1)*$page_size;
	$p = new Page($count,$listRows);
	$list = $model->field('*')->where($data)->order('sort asc,id desc')->limit($p->firstRow.','.$p->listRows)->select();
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
	$model = D('Class');
	foreach($list1 as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }
	  $wdata['id'] = $ar[0];
	  $data['sort'] = $ar[1];
	  $result = $model->where($wdata)->save($data);
	}
	$this->GiveCache(0);
	$this->success ('排序完成!');
  }

  /**
   * 生成缓存
   */
  function GiveCache($status=1){
	  $model = D('Class');
	  $list = $model->where($wdata)->order('sort asc,id desc')->select();
	  foreach($list as $array){
	    $data[$array['id']] = $array;
	  }
	  mk_dir('./cache/class/');
	  F('list',$data,'./cache/class/');
	  $nlist = include './cache/node/list.php' ;
	  $cnmodel = D('Class_node');
	  foreach($nlist as $val){
	    if($val['name']!='Index' && $val['name']!='Public'){
		  $data1['a.nid'] = $val['id'];
		  $caches = $cnmodel->field('a.*,b.sort')->table('`137_class_node` as a ')->join('`137_class` as b on a.cid=b.id')->where($data1)->find();
		  F('np_'.$val['id'],$caches,'./cache/class/');
		}
	  }
	  if($status==1){
	    $this->success ('生成缓存完毕!');
	  }
  }

}
?>