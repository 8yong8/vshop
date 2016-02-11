<?php

class ArticleAction extends Action{
  public function _initialize(){
	//parent::_initialize();
	//$this->assign('model','Article');
  }

  public function index(){
	$model = M('Article');
	$data['status'] = 1;
	$count = $model->where($data)->count();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
	$page_count = ceil($count/$page_size);
	$pageno = 1;
	$offset = ($pageno - 1) * $page_size;
	$voList = $model->field('id,title,content,litpic,create_time')->where($data)->limit($offset. ',' . $page_size)->select();
	foreach($voList as $key=>$val){
	  $voList[$key]['content'] = str_cut(htmlspecialchars_decode($val['content']),45);
	}
	$list['count'] = $count;
	$list['page_count'] = $page_count;
	$list['data'] = $voList;
	echo  json_encode($list);exit;
  }

  //展示页面
  public function show(){
    $model = M('Article');
	$data['id'] = $_GET['id'];
	$vo = $model->field('id,title,content,source,author,litpic,create_time')->where($data)->find();
	$this->lookup(MODULE_NAME,$_GET['id']);
	echo json_encode($vo);
  }

  protected function lookup($source,$sourceid){
	$token = $_REQUEST['token'];
	if(!$token){
	  return;
	}
	$model = M('user_token');
	$data['a.token'] = $token;
	$data['b.status'] = 1;
	$user = $model->field('b.*')->table('`'.C('DB_PREFIX').'user_token` as a')->join('`'.C('DB_PREFIX').'user` as b on a.uid=b.id')->where($data)->find();
    $model  = M('lookup');
    $data['source'] = $source;
	$data['user_id'] = $user['id'];
	$data['sourceid'] = $sourceid;
	$data['user_name'] = $user['nickname'];
	$data['create_time'] = time();
	$model->add($data);
  }

}
?>