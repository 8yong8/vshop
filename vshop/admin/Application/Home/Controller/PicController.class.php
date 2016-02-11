<?php
namespace Home\Controller;
use Think\Controller;
class PicController extends CommonController {

  /**
   * 列表信息
   */
  public function index(){
	$name = CONTROLLER_NAME;
    $model = D($name);
	$map = $this->_search ();
	//分页查询数据
	if($_GET['projectname']){
	  $map['137_designer_project.name'] = $_GET['projectname'];
	  $this->assign('albumname',$_GET['albumname']);
	}
	if($_GET['uid']){
	  $map['137_pic.uid'] = $_GET['uid'];
	  $this->assign('uid',$_GET['uid']);
	}
	if($_GET['username']){
	  $map['137_pic.username'] = $_GET['username'];
	  $this->assign('username',$_GET['username']);
	}
	$count = $model->join('137_designer_project on 137_pic.albumid=137_designer_project.albumid')->where ( $map )->count ( 'picid' );
	if ($count > 0) {
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : 'picid';
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//创建分页对象
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '20';
		}
		$p = new \My\Page ( $count, $listRows );
		$voList = $model->join('137_designer_project on 137_pic.albumid=137_designer_project.albumid')->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->findAll();
		//echo $model->getlastsql();
		//分页跳转的时候保证查询条件
		foreach ( $map as $key => $val ) {
			if (! is_array ( $val )) {
				$p->parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		//分页显示
		$page = $p->show ();
		//列表排序显示
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示
		$this->assign ( 'list', $voList );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( "page", $page );
	}
	cookie( '_currentUrl_', __SELF__ );
	$this->display();	
  }

  /**
   * 删除
   */
  function foreverdelete(){
	//删除指定记录
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$list = $model->where($condition)->findall();
			foreach($list as $val){
			  unlink($val['filepath']);
			}
			if (false !== $model->where ( $condition )->delete ()) {
				$this->success ('删除成功！');
			} else {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
	$this->forward ();  
  }

}
?>