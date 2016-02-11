<?php 
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller {

  /**
   * 前期执行
   */
  public function _initialize(){
	$this->assign('jumpUrl',__ROOT__);
	$this->assign('usecssjs',$usecssjs);
	$this->assign('waitSecond',3);
	$this->assign('error','您访问的页面不存在！');
	$title = '404错误- '.C('site_name');
	$keywords = '404错误';
	$description = '404错误';
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
	$this->display('Public:404');
	exit;
  }

}
?>