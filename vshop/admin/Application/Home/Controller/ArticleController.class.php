<?php 
namespace Home\Controller;
use Think\Controller;
class ArticleController extends CommonController {

  public function _initialize() {
    parent::_initialize();
  }

  /**
   * 列表页
   */
  public function index(){
	$model = D('Article');
	$where = $this->_search();//获得查询条件
	if(isset($_GET['_order'])) {
		$order = $_GET['_order'];
	}else {
		$order = !empty($sortBy)? $sortBy: $model->getPk();
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
	  $listRows = 1;
	  $p = new \My\Page($count,$listRows);
	  $list = $model->field('id,title,cname,create_time,update_time,status')->where($where)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->select();
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

	//dump($page);exit;
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	$this->Predecessor();  //分类
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
   * 查询条件
   */
  public function _search(){
	$data = ' 1 ';
	if($_GET['id']!=""){
      //$data['a.id'] = $_GET['id'];
	  $data .=' and id='.$_GET['id'].'';
	  $this->assign("id",$_GET['id']);
	}
	if($_GET['title']!=""){
      //$data['title'] = array('like','%'.$_GET['title'].'%');
	  $data .=" and title like '".'%'.$_GET['title'].'%'."'";
	  $this->assign("title",       $_GET['title']);
	}
	if($_GET['cid']!=""){
      //$data['cid'] = array('eq',$_GET['cid']);
	  $data .=" and cid='".$_GET['cid']."'";
	  $this->assign("cid",$_GET['cid']);
	}
	if($_GET['creater']!=""){
      //$data['creater'] = $_GET['creater'];
	  $data .=" and createname='".$_GET['creater']."'";
	  $this->assign("creater",$_GET['creater']);
	}
	if($_GET['flags']!=""){
	  $flags = $_GET['flags'];
      //$data['flags'] = "find_in_set( $flags, `flags` )";NOT LIKE
	  //$data['flags'] = array('exp',"find_in_set( $flags, `flags` )");
	  //$data['flags'] = array('or',array('like',"%$flags"),array('eq',"$flags"));
	  $data .=" and find_in_set( $flags, `flags` )";
	  $this->assign("flagid",$_GET['flags']);
	}
	return $data;
  }

  /**
   * 分类信息
   */
  public function gettype(){
	$webset = $_POST['webset'];
	$list = include C('DATA_CACHE_PATH')."/column/column_".$webset.".php";
	$tree = new \My\Tree($list);
	$list = $tree->get_tree('0');
	foreach($list as $k=>$v){
	  $list[$k]['name'] = iconv("GBK", "UTF-8", $v['name']);
	}
	echo json_encode($list);
  }

  /**
   * 文章添加页面
   */
  public function add(){
	if(IS_POST){
		$_POST['keyword'] = str_ireplace("，", ",", $_POST['keyword']);
		$_POST['show_time'] = $_POST['update_time'] = $_POST['create_time'] = time();
		$_POST['pv_count'] = rand(1,20);
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['createname'] = $_SESSION['nickname'];
		if(!$_POST['shorttitle'])$_POST['shorttitle'] = $_POST['title'];
		//缩略图处理
		if($this->checkFileUp()){
		  $this->upload();
		}
		$name = CONTROLLER_NAME;
		$hash = $_POST['hash'];
		//属性处理
		if($_POST['flags']){
		  $flags = $_POST['flags'];
		  $flagstr = '';
		  foreach($_POST['flags'] as $flag){
			$flagstr .= '<'.$flag.'>';
		  }
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		$cmodel = D('classify');
		//分类名处理
		$cdata['id'] = $_POST['cid'];
		$_SESSION['art_cid'] = $_POST['cid'];
		$cc = $cmodel->where($cdata)->find();
		$_POST['cname'] = $cc['title'];
		//顶级分类处理
		$topdata['id'] = $cc['pid'];
		$top = $cmodel->where($topdata)->find();
		$_POST['top_cid'] = $top['id'] ? $top['id'] : $_POST['cid'];
		$_POST['top_cname'] = $top['name'];
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$aid = $model->add ();
		if ($aid!==false) { //保存成功
		  //记录操作
		  $this->history($aid);
			//相关属性表修改
			$amodel = D('Flag_list');
			if($flags){
				$data1['source'] = $name;
				$data1['sourceid'] = $aid;
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  $amodel->add($data1);
				  //echo $amodel->getlastsql();exit;
				}
			}
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	  exit;
	}
	$hash = md5($_SESSION[C('USER_AUTH_KEY')].time());
	$this->assign('imgurl',C('IMG_URL'));
	$this->assign('art_cid',$_SESSION['art_cid']);
	$this->assign('hash',$hash);
	$this->Predecessor();
	$this->display();
  }

  /**
   * 编辑页面
   */
  public function edit(){
	$name = CONTROLLER_NAME;
	$model = D($name);
	if(IS_POST){
		$_POST['keyword'] = str_ireplace("，", ",", $_POST['keyword']);
		$_POST['update_time'] = time();
		$hash = $_POST['hash'];
		if($_POST['flags']){
		  $flags = $_POST['flags'];
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $almodel = D('flag_list');
		  $aldata['source'] = $name;
		  $aldata['sourceid'] = $_POST['id'];
		  $almodel->where($aldata)->delete();	  
		  $_POST['flags'] = '';
		}
		//缩略图处理
		if($this->checkFileUp()){
		  $this->upload();
		}
		$cmodel = D('classify');
		//分类名处理
		$cdata['id'] = $_POST['cid'];
		$cc = $cmodel->where($cdata)->find();
		$_POST['cname'] = $cc['name'];
		//顶级分类处理
		$topdata['id'] = $cc['pid'];
		$top = $cmodel->where($topdata)->find();
		$_POST['top_cid'] = $top['id'] ? $top['id'] : $_POST['cid'];
		$_POST['top_cname'] = $top['name'];
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list = $model->save ();
		if($list){
		  //操作记录
		  $this->history($_POST['id']);
			//相关属性表修改
			$amodel = D('flag_list');
			$wdata['source'] = $name;
			$wdata['sourceid'] = $_POST['id'];
			$flagslist = $amodel->field('fid')->where($wdata)->select();
			foreach($flagslist as $v){
			  $key = array_search($v['fid'],$flags);
			  if($key!==false){
				//记录已经存在的
				$k_fids[] = $v['fid'];
			  }else{
				//要删除的
				$d_fids[] = $v['fid'];
			  }
			  $oldflags[] = $v['fid'];
			}

			if($d_fids){
			  $wdata['fid'] = array('in',$d_fids);
			  $amodel->where($wdata)->delete();
			}
			if($flags){
				$data1['sourceid'] = $_POST['id'];
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['source'] = $name;
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  if($oldflags){
					$key = array_search($fid,$oldflags);
					//已存在信息修改
					if($key!==false){
					  $wdata['fid'] = $fid;
					  unset($data1['sort']);
					  $amodel->where($wdata)->save($data1);
					  continue;
					}
				  }
				  $amodel->add($data1);
				  //echo $amodel->getlastsql();exit;
				}
			}else{
			  $amodel = D('flag_list');
			  $del_data['source'] = $name;
			  $del_data['sourceid'] = $_POST['id'];
			  $flagslist = $amodel->where($del_data)->delete();
			}
		  $this->success ('编辑成功!');
		} else {
		  //错误提示
		  $this->error ('编辑失败!');
		}
		exit;
	}
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
	  $this->error('出错!');
	}
	$this->assign('vo',$vo);
	$this->assign('imgurl',C('IMG_URL'));
	$hash = md5($_SESSION[C('USER_AUTH_KEY')].time());
	$this->assign('hash',$hash);
	$this->Predecessor();
	$this->display();
  }


  /**
   * 删除
   */
  public function foreverdelete() {
	//删除指定记录
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$this->assign('jumpUrl',__MODULE__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		//$id = $_REQUEST [$pk];
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
				$ids =  explode ( ',', $id );
				foreach($ids as $id){
				  //缓存处理
				  $this->history($id);
				}					
				//删除相关属性文章并记录
				$model = D('flag_list');
				$afdata['source'] = $name;
				$afdata['sourceid'] = array('in',explode ( ',', $id ));
				$model->where($afdata)->delete();
				//CsDataUpAttr($ids,'article_index,article_zl_index',array('status'),0);
				$this->success ('删除成功！');
			} else {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }

  /**
   * 通过
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
		  //缓存处理
		  $this->history($id);
		}
		//CsDataUpAttr($ids,'article_index,article_zl_index',array('status'),1);
		$model->query("UPDATE `".C('DB_PREFIX')."art_flag_list` SET `status` = '1' WHERE aid='".$_GET['id']."'");
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态恢复成功！' );
	} else {
		$this->error ( '状态恢复失败！' );
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
		  //记录操作
		  $this->history($id);
		}
		//CsDataUpAttr($ids,'article_index,article_zl_index',array('status'),0);
		$model->query("UPDATE `".C('DB_PREFIX')."art_flag_list` SET `status` = '0' WHERE aid='".$_GET['id']."'");
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态禁用成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 公用信息
   */
  public function Predecessor(){
	//$flags = $this->get_flags();
	$flags = $this->get_moudel_flags();
	$this->assign('imgurl',C('IMG_URL'));
	$this->assign('flags',$flags);
	$model = D('Classify');
	$data['status'] = 1;
	$list = $model->where($data)->order('sort asc,id desc')->select();
	$tree = new \My\Tree($list);
	$list = $tree->get_tree('0');
	$this->assign('types',$list);
  }

  /**
   * 预览跳转
   */
  public function look(){
    $model = D('Article');
	$data['id'] = $_GET['id'];
	$vo = $model->field('url')->where($data)->find();
	header("location:".$vo['url']."");
  }

}
?>