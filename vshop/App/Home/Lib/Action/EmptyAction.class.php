<?php 
class EmptyAction extends Action {


//前期执行
  public function _initialize(){
	$usecssjs = '
	<link href="'.__ROOT__.'/Public/css/index.css" rel="stylesheet" type="text/css" />
	<link href="'.__ROOT__.'/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
	$this->assign('usecssjs',$usecssjs);
	$this->assign('title','错误页面');
	$this->display('Public:404');
  }

}
?>