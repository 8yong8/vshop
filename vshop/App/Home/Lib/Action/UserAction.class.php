<?php
//好友
class UserAction extends CommonAction{

  public function _before_index(){
    $_REQUEST ['fileds'] = 'id,bm_id,logo,bm_name,position,account,nickname,py_name,bm_py_name,tel,baiduUserId';
	/*dump(MODULE_NAME);dump(ACTION_NAME);
	$array['module'] = MODULE_NAME;
	$array['action'] = ACTION_NAME;
	$array['id'] = 1;
	echo json_encode($array);exit;*/
  }

   function index() {
	$model = M('User');
	//$_REQUEST['nickname'] = '明';
	$map = $this->_search();
	$map['id'] = array('gt',1);
	//排序字段 默认为主键名
	if ($_REQUEST ['_order']) {
		$order = $_REQUEST ['_order'];
	} else {
		$order = 'py_name';
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if ($_REQUEST ['_sort']) {
		$sort = $_REQUEST ['_sort'] ? $_REQUEST ['_sort'] : 'desc';
	} else {
		$sort = 'asc';
	}
	if (isset ( $_REQUEST ['fileds'] )) {
		$fileds = $_REQUEST ['fileds'];
	} else {
		$fileds = '*';
	}
	$count = $model->where($map)->count();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
	$page_count = ceil($count/$page_size);
	$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$voList = $model->field($fileds)->where($map)->order( "`" . $order . "` " . $sort)->limit($offset. ',' . $page_size)->select();
	//echo $model->getlastsql();
	foreach($voList as $user){
	  if($order=='py_name'){
	    $key =  ucfirst(substr($user['py_name'],0,1));
		$voList2[$key]['show_name'] = $key;
	  }else{
	    $key = $user['bm_py_name'];
		$voList2[$key]['show_name'] = $user['bm_name'];
	  }
	  $voList2[$key]['show_users'][] = $user;
	}
	foreach($voList2 as $user_msg){
	  $voList3[] = $user_msg;
	}
	$list['count'] = $count;
	$list['page_count'] = $page_count;
	$list['data'] = $voList3;
	//dump($list);
	echo  json_encode($list);exit;
  }

  //列表
  public function lists(){
	$model = M('User');
	$map = $this->_search();
	$count = $model->where($map)->count();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
	$page_count = ceil($count/$page_size);
	$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$voList = $model->field("id,bm_id,bm_name,account,nickname,py_name,bm_py_name,tel,baiduUserId")->where($map)->order("py_name asc")->limit($offset. ',' . $page_size)->select();
	$list['count'] = $count;
	$list['page_count'] = $page_count;
	$list['data'] = $voList;
	echo  json_encode($list);exit;  
  }

  public function update($thum = 0,$width=360,$height=360){
    $model = M('user');
	if($_FILES['logo']['name']){
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1024*1024*10;
		//设置上传文件类型
		$upload->allowExts  = array('jpg','gif','png','jpeg');
		$upload->saveRule = 'uniqid';
		$path = $upload->savePath =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';
		mk_dir($upload->savePath);
		$upload->upload();
		$info = $upload->getUploadFileInfo();
		if($info){
		  $file = $info[0];
		  $ar = getimagesize($path.$file['savename']);
		  if($file['size']>1024*100 || $ar[0]>$width || $ar[1]>$height){
			  $full_blitfilename = $path.$file['savename'];
			  //100KB
			  ImageResize($full_blitfilename,C('UP_IMG_MAX_WIDTH'),C('UP_IMG_MAX_HEIGHT')); 
		  }
		  $sdata['logo'] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$file['savename'];
		}
	}
	  $data['id'] = $this->user['id'];
	  $sdata['baiduUserId'] = $_POST['baiduUserId'];
	  if($_POST['password'])$sdata['password'] = md5($_POST['password']);
	  $result = $model->where($data)->save($sdata);
	  if($result){
		$msg['status'] = 1;
		$msg['notice'] = '修改成功';
		$msg['error_code'] = 1000;
		echo  json_encode($msg);exit;		  
	  }else{
		$msg['status'] = 0;
		$msg['notice'] = '修改失败';
		$msg['error_code'] = 1003;
		echo  json_encode($msg);exit;	  
	  }
  }

  //获取用户信息
  function get_user_msg(){
	echo  json_encode($this->user);exit;
  }

  function get_user_other(){
	//传阅
    $model = M('files_user');
	$data['user_id'] = $this->user['id'];
	$data['status'] = array('lt',2);
	$files_count = $model->where($data)->count();
	$msg['files_count'] = $files_count;
	//会议
    $model = M('meeting_user');
	$data['status'] = 0;
	$meeting_count = $model->where($data)->count();
	$msg['meeting_count'] = $meeting_count;
	//假条
	$data['status'] = 3;
	$jt_count = $model->where($data)->count();
	$msg['jt_count'] = $jt_count;
	//附件
    $model = M('attachments');
	$at_data['user_id'] = $this->user['id'];
	$at_count = $model->where($at_data)->count();
	$msg['at_count'] = $at_count;
	//dump($msg);
	echo json_encode($msg);
  }

  //退出
  function logout(){
	$model = M('user');
	//file_put_contents('./1.txt',$this->user['id'].'/'.$_POST['token']);
	if($this->user){
	  $wdata['id'] = $this->user['id'];
	  $sdata['baiduUserId'] = '';
	  $result = $model->where($wdata)->save($sdata);//百度id清空
	  if($result){
	    $msg['status'] = 1;
	    $msg['error_code'] = 1000;
	    $msg['notice'] = '退出成功';
		echo  json_encode($msg);exit;		  
	  }else{
	    $msg['status'] = 0;
	    $msg['error_code'] = 8002;
	    $msg['notice'] = '退出失败';
		echo  json_encode($msg);exit;	  
	  }
	}else{
	    $msg['status'] = 0;
	    $msg['error_code'] = 1001;
	    $msg['notice'] = '用户不存在';
		echo  json_encode($msg);exit;	
	}
	echo  json_encode($msg);exit;
  }

}
?>