<?php

class IndexAction extends PublicAction{

  public function _initialize(){
	  parent::_initialize();
	  $this->assign('top_menu_id',1);
	  //$this->assign('SELF',C('SITE_URL').'/index.php?m=Index&a=index');
  }
  
  public function index(){
	$msg['module'] = 'Meeting';
	$msg['action'] = 'show';
	$msg['id'] = 2;
	echo json_encode($msg);
    $this->display();
  }

  public function _empty(){
	$msg['status'] = 0;
	$msg['error_num'] = 8001;
	$msg['notice'] = '出错';
	echo json_encode($msg);
  }

  public function tuisong(){
	echo time();
	//时间
	$add_time = 60*5;
    $model = M('files_user');
    $data['call_time'] = array('between',array(time()-$add_time,time()));
	$data['status'] = array('neq',2);
	$data['call_sent_status'] = 0;
	//$list = $model->table('')->join()->where()->select();
	//$list = $model->where($data)->select();
	echo $model->getlastsql();
	dump($list);exit;
  
  }

}
?>