<?php
namespace Home\Controller;
use Think\Controller;
class FeedbackController extends PublicController {

  /**
   * 评论
   */
  public function index(){
	$model = M('Feedback');
	$data = $this->_search();
	$data['product_id'] = $_GET['product_id'];
	$count = $model->where($data)->count();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('page_size');
	//$page_size = 1;
	$page_count = ceil($count/$page_size);
	$pageno = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->where($data)->limit($offset. ',' . $page_size)->order('id desc')->select();
	foreach($list as $key=>$val){
	  $list[$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
	  $list[$key]['grade'] = ceil($val['grade']);
	}
	if(IS_AJAX){
	  $msg['feedback_list'] = $list;
	  ajaxSucReturn($msg);
	}
	$this->assign('list',$list);
	$this->assign('page_count',$page_count);
	$this->assign('headerTitle','商品评价');
	$this->assign('headerKeywords','商品评价');
	$this->assign('headerDescription','商品评价');
	$this->assign('wx_title','商品评价');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }

  /**
   * 搜索条件
   */
  public function _search(){
	$data = array();
	if ($_GET['grade']){
		switch ($_GET['grade'])
		{
			case 1:
				$data['grade'] = array('elt',1);
				break;
			case 2:
				$data['grade'] = 3;
				break;
			case 3:
				$data['grade'] = array('gt',3);
				break;
			default:
				break;
		}
		$this->assign('grade',$_GET['grade']);
	}
	return $data;
  }

}
?>