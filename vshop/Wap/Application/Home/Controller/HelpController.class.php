<?php
namespace Home\Controller;
use Think\Controller;
class HelpController extends PublicController {

  /**
   * 列表页
   */
  public function index(){
	$model = D('Help');
	$data['pid'] = 0;
	$data['status'] = 1;
	$list = $model->field('id,title')->where($data)->order('sort asc,id asc')->select();
	if(!$list){
	  $this->error('访问页面不存在');
	}
	$this->assign('list',$list);
	$this->assign('headerTitle','帮助中心');
	$this->assign('headerKeywords','帮助中心');
	$this->assign('headerDescription','帮助中心');
	$this->assign('wx_title','帮助中心');
	$this->assign('wx_desc',C('wx_desc'));
    $this->display();
  }


  /**
   * 列表页
   */
  public function lists(){
	$model = D('Help');
	$p_data['id'] = $_GET['pid'];
	$parent = $model->where($p_data)->find();
	$this->assign('parent',$parent);
	$data['pid'] = $_GET['pid'];
	$data['status'] = 1;
	$list = $model->field('id,title')->where($data)->order('sort asc,id desc')->select();
	if(!$list){
	  $this->error('访问页面不存在');
	}
	$this->assign('list',$list);
	$this->assign('headerTitle','帮助中心 - '.$parent['title']);
	$this->assign('headerKeywords','帮助中心 - '.$parent['title']);
	$this->assign('headerDescription','帮助中心 - '.$parent['title']);
	$this->assign('wx_title','帮助中心 - '.$parent['title']);
	$this->assign('wx_desc',C('wx_desc'));
    $this->display();
  }

  /**
   * 详情页
   */
  public function detail(){
    $model = D('Help');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	$this->assign('vo',$vo);
	$this->assign('headerTitle',$vo['title']);
	$this->assign('headerKeywords',$vo['title']);
	$this->assign('headerDescription',$vo['title']);
	$this->assign('wx_title',$vo['title']);
	$this->assign('wx_desc',C('wx_desc'));
    $this->display();
  }

}
?>