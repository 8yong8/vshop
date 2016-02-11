<?php
class FilesAction extends CommonAction {

  protected function _list($model, $map, $sortBy = '', $asc = false) {
	//排序字段 默认为主键名
	if (isset ( $_REQUEST ['_order'] )) {
		$order = $_REQUEST ['_order'];
	} else {
		$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if (isset ( $_REQUEST ['_sort'] )) {
		$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
	} else {
		$sort = $asc ? 'asc' : 'desc';
	}
	if (isset ( $_REQUEST ['fileds'] )) {
		$fileds = $_REQUEST ['fileds'];
	} else {
		$fileds = '*';
	}
	$count = $model->where($map)->count();
	//echo $model->getlastsql();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
	$page_count = ceil($count/$page_size);
	$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
	$offset = ($pageno - 1) * $page_size;
	//$map = '(type=2) or (find_in_set('.$this->user['id'].',users))';
	$voList = $model->field($fileds)->where($map)->order( "`" . $order . "` " . $sort)->limit($offset. ',' . $page_size)->select();
	//echo $model->getlastsql();
	foreach($voList as $key=>$val){
	   $users = explode(',',$val['users']);
	   if($val['type']==1){
		 $k  = array_search($this->user['id'],$users);
		 if($k===false){
		   $voList[$key]['access'] = 0;
		 }else{
		   $voList[$key]['access'] = $this->check_access($val['id'],1);
		 }
	   }else{
		 $voList[$key]['access'] = $this->check_access($val['id'],1);
	   }
	   
	  //$status = $this->check_access($val);
	}
	$list['count'] = $count;
	$list['page_count'] = $page_count;
	$list['data'] = $voList;
	//dump($list);exit;
	echo  json_encode($list);exit;
  }

  protected function _search($name = '') {
	//$map = array ();
	$map = '(type=2) or (find_in_set('.$this->user['id'].',users))';
	/*if($_REQUEST['type']){
	  $map['type'] = $_REQUEST['type'];
	}
	if($_REQUEST['user_id']){
	  $map['_string'] = "find_in_set('".$_REQUEST['user_id']."',users)";
	}
	*/
	if($_REQUEST['month']){
	  //$map['_string'] = "find_in_set('".$_REQUEST['user_id']."',users)";
	  $btime = mktime(0,0,0,$_REQUEST['month'],1,$_GET['year']);
	  $etime = mktime(24,0,0,$_REQUEST['month'],31,$_GET['year']);
	  //$map['create_time'] = array('between',array($btime,$etime));
	  $map .= " AND (create_time between $btime and $etime)";
	}else if($_REQUEST['year']){
	  //$map['_string'] = "find_in_set('".$_REQUEST['user_id']."',users)";
	  $btime = mktime(0,0,0,1,1,$_GET['year']);
	  $etime = mktime(24,0,0,12,31,$_GET['year']);
	  //$map['create_time'] = array('between',array($btime,$etime));
	  $map .= " AND (create_time between $btime and $etime)";	
	}
	return $map;
  }

  Private function check_access($fid,$access=1){
    $model = M('Files_user');
    $data['user_id'] = $this->user['id'];
	$data['fid'] = $fid;
	$vo = $model->where($data)->find();
	if(!$vo){
	  return $access;
	}else{
	  if($vo['status']!=2){
	    return 2;
	  }else{
	    return $access;
	  }
	}
  }

  public function show(){
	$model = M('Files');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
		$msg['status'] = 0;
		$msg['notice'] = '不存在';
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;	
	}
	/*
	$vo['create_time'] = date('Y-m-d H:i:s',$vo['create_time']);
	if($vo['sent_time']){
	  $vo['sent_time'] = date('Y-m-d H:i:s',$vo['sent_time']);
	}else{
	  $vo['sent_time'] = '--';
	}
	*/
	$vo['look_status'] = 1;//默认已查看
	//历史记录
	$model = M('Files_user');
	$mu_data['fid'] = $_GET['id'];
	//$mu_data['status'] = 2;
	$users = $model->where($mu_data)->order('id asc')->select();
	$vo['user_status'] = 0;//是否参与人员
	$vo['access'] = 0;
	foreach($users as $k=>$user){
	  //判断是否有传阅
	  if($user['user_id']==$this->user['id']){
	    $vo['user_status'] = 1;//默认有权限查看
		$vo['access'] = 2;//参与人可看可批
		/*
		if($user['status']!=2){
		  $vo['access'] = 2;//没批阅,到我批阅
		}else{
		  $vo['access'] = 1;//有权限查看
		}
		*/
		if($user['status']==0){
		  $vo['look_status'] = 0;
		}
		//break;
	  }

	}
	//归档判断
	$gd_data['fid'] = $_GET['id'];
	$gd_data['status'] = array('lt',2);
	$gd_count = $model->where($gd_data)->count();
	if($gd_count>0){
	  $vo['gd_status'] = 0;//未归档
	}else{
	  $vo['gd_status'] = 1;//已归档
	}
	if($vo['type']==2 && $vo['access'] == 0){
		/*$msg['status'] = 0;
		$msg['notice'] = '你没有权限查看该文档';
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;*/
		$vo['access'] = 1;//公开文档都有权限查看
	}
	$vo['user_data'] = $users;
	$model = M('attachments');
	$a_data['source'] = 'Files';
	$a_data['sourceid'] = $_GET['id'];
	$files = $model->field('aid,title,filepath')->where($a_data)->select();
	$vo['file_data'] = $files;
	//dump($vo);
	$this->lookup(MODULE_NAME,$_GET['id']);
	echo  json_encode($vo);exit;     
  }

  public function files_user_update(){
	//$_POST['fid'] = 2;
	//$_POST['msg'] = '很好';
	//$_POST['user_id'] = '26,27';
    $model = M('files_user');
	//修改自己批复信息
	$data['fid'] = $_POST['fid'];
	$data['user_id'] = $this->user['id'];
	$vo = $model->where($data)->find();
	$s_data['status'] = 2;
	$s_data['msg'] = $_POST['msg'];
	if($_POST['user_id']){
	  $uids = explode(',',$_POST['user_id']);
	}
	if($_POST['user_id']){
	  $uids = explode(',',$_POST['user_id']);
	  $umodel = M('user');
	  foreach($uids as $uid){
		  //判断是否已存在
		  $cdata['fid'] = $_POST['fid'];
		  $cdata['user_id'] = $uid;
		  $count = $model->where($cdata)->count();
		  //echo $model->getlastsql();exit;
		  if($count>0){
		     continue;
		  }
		  $udata['id'] = $uid;
		  $user = $umodel->where($udata)->find();
		  $add_data['fid'] = $vo['fid'];
		  $add_data['title'] = $vo['title'];
		  $add_data['user_id'] = $uid;
		  $add_data['user_name'] = $user['nickname'];
		  $add_data['bm_name'] = $user['bm_name'];
		  $add_data['from_id'] = $this->user['id'];
		  $add_data['from_name'] = $this->user['nickname'];
		  $add_data['from_time'] = time();
		  if($_POST['call_time']){
			$add_data['call_time'] = strtotime($_POST['call_time']);
		  }
		  $result = $model->add($add_data);
		  //推送
		  if($result && $user['baiduUserId']){
			$sent_array['module'] = MODULE_NAME;
			$sent_array['action'] = 'show';
			$sent_array['id'] = $_POST['fid'];
			$custom_content = json_encode($sent_array);
			$result = push_msg($user['baiduUserId'],$user['mb_system'],$vo['title'],$custom_content);
		  }
		  if($user['position']!='普通员工'){
			$s_data['remark'] = '请'.$user['nickname'].'批阅';
		  }else if($this->user['position']=='书记'){
			$s_data['remark'] = mb_substr($this->user['position'],0,1,'utf-8').$user['position'].'分发';	 
		  }else{
			$pos = strpos($user['position'],'主任');
			if($pos!==false){
			  $notice = '分拣';
			}
			$s_data['remark'] = '请'.mb_substr($user['nickname'],0,1,'utf-8').$user['position'].$notice;
		  }
	  }
	  $this->update_file_users($_POST['fid']);
	}else{
	  /*unset($data['user_id']);
	  $data['status'] = array('lt',2);
	  $count = $model->where($data)->count();
	  */
	  if($this->user['position']=='书记'){
	    $s_data['remark'] = '批阅完毕';
	  }else{
	    $s_data['remark'] = mb_substr($this->user['nickname'],0,1,'utf-8').$this->user['position'].'办结';
	  }
	  //echo $model->getlastsql();exit;
	  //$s_data['remark'] = '文书归档';
	}
	$s_data['update_time'] = time();
	$result = $model->where($data)->save($s_data);
	if($result){
		$msg['status'] = 1;
		$msg['notice'] = '提交成功';
		$msg['error_code'] = 1000;
		echo  json_encode($msg);exit;	
	}else{
		$msg['status'] = 0;
		$msg['notice'] = '修改失败';
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;	
	}
  }

  //添加
  public function add(){
	$model = D ('Files');
	//file_put_contents('./1.txt',$_POST['content']);
	$_POST = json_decode($_POST['content'],1);
	$_POST['create_time'] = time();
	$_POST['user_id'] = $this->user['id'];
	$_POST['user_name'] = $this->user['nickname'];
	if (false === $model->create ()) {
		$this->error ( $model->getError () );
	}
	//保存当前数据对象
	$list = $model->add ();
	if ($list!==false) { //保存成功
		$this->history($list);
		//附件处理
		if($_POST['uppicarr']){
		  $model = M('attachments');
		  foreach($_POST['uppicarr'] as $key=>$file){
			$add_data['source'] = MODULE_NAME;
			$add_data['sourceid'] = $list;
			$add_data['filepath'] = $file;
			$file_dir = str_replace(C('IMG_URL'),C('IMG_ROOT'),$file);
			$add_data['size'] = filesize($file_dir);
			$add_data['type'] = get_file_type($file_dir);
			$add_data['filename'] = $add_data['title'] = $_POST['uppicname'][$key];
			$add_data['create_time'] = time();
			$add_data['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
			$model->add($add_data);
		  }
		}
		//传阅处理
		if($_POST['user']){
		  $model = M('Files_user');
		  if($_POST['call_time']){
			$add_data['call_time'] = strtotime($_POST['call_time']);
		  }
		  foreach($_POST['user'] as $key=>$user){
			$user_arr = explode(',',$user);
			//推送
			$sent_array['module'] = MODULE_NAME;
			$sent_array['action'] = 'show';
			$sent_array['id'] = $list;
			$custom_content = json_encode($sent_array);
			$result = push_msg2($user_arr[0],$_POST['title'],$custom_content);
			if($result){
			  $add_data['sent_status'] = 1;
			  $add_data['sent_time'] = time();
			}
			$add_data['from_id'] = $this->user['id'];
			$add_data['from_name'] = $this->user['nickname'];
			$add_data['user_id'] = $user_arr[0];
			$ids[] = $user_arr[0];
			$add_data['user_name'] = $user_arr[1];
			$add_data['bm_name'] = $user_arr[2];
			$add_data['fid'] = $list;
			$add_data['title'] = $_POST['title'];
			$model->add($add_data);
		  }
		}
		//$this->update_date($ids,$list);//记录
		if($_POST['type']==1){
		  $this->update_date($user_ids,$list);
		}else{
		  unset($user_ids);
		  $user_ids[] = 0;
		  $this->update_date($user_ids,$list);
		}
		$model->query('OPTIMIZE TABLE `zy_attachments`');
		$msg['status'] = 1;
		$msg['notice'] = '提交成功';
		$msg['error_code'] = 1000;
		echo  json_encode($msg);exit;
	} else {
		//失败提示
		$msg['status'] = 0;
		$msg['notice'] = '修改失败';
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;
	}
  }

  public function edit(){  
	  $model = M('Files');
	  $data['id'] = $_GET['id'];
	  $data['user_id'] = $this->user['id'];
	  $vo = $model->where($data)->find();
	  if(!$vo){
		$msg['status'] = 0;
		$msg['notice'] = '无权限操作';
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;	  
	  }
	  $this->show();exit;
	  //文档
	  $model = M('attachments');
	  $a_data['source'] = 'Files';
	  $a_data['sourceid'] = $_GET['id'];
	  $files = $model->where($a_data)->select();
	  $vo['files_data'] = $files;
	  //传阅
	  $model = M('Files_user');
	  $mu_data['fid'] = $_GET['id'];
	  $users = $model->where($mu_data)->select();
	  $vo['files_data'] = $users;
	  echo json_encode($vo);
  }

  public function update(){
	  //传阅
	  $model = M('Files_user');
	  $mu_data['fid'] = $_POST['id'];
	  $users = $model->where($mu_data)->select();
	  if($_POST['user']){
		if($users){
		  $up_data['fid'] = $_POST['id'];
		  $up_sdata['title'] = $_POST['title'];
	      $model->where($up_data)->save($up_sdata);
		  foreach($_POST['user'] as $key=>$user){
			$user_arr = explode(',',$user);
			$ids[] = $user_arr[0];
		  }
		  foreach($users as $user){
			if(array_search($user['user_id'],$ids)===false){
			  //删除参会人员
			  $del_data['id'] = $user['id'];
			  $model->where($del_data)->delete();
			}
		  }
		  //unset($ids);
		  foreach($users as $user){
			$ids2[] = $user['user_id'];
		  }
		  foreach($_POST['user'] as $user){
			$user_arr = explode(',',$user);
			if(array_search($user_arr[0],$ids2)===false){
			    if($_POST['call_time']){
			      $add_data['call_time'] = strtotime($_POST['call_time']);
			    }
			  //添加
			    $add_data['fid'] = $_POST['id'];
				$add_data['user_id'] = $user_arr[0];
				$add_data['user_name'] = $user_arr[1];
				$add_data['bm_name'] = $user_arr[2];
				$add_data['title'] = $_POST['title'];
				$model->add($add_data);
			}
		  }
		}else{
		  //添加
		  foreach($_POST['user'] as $key=>$user){
			if($_POST['call_time']){
			  $add_data['call_time'] = strtotime($_POST['call_time']);
			}
			$add_data['fid'] = $_POST['id'];
			$user_arr = explode(',',$user);
			$add_data['user_id'] = $user_arr[0];
			$add_data['user_name'] = $user_arr[1];
			$add_data['bm_name'] = $user_arr[2];
			$add_data['title'] = $_POST['title'];
			$model->add($add_data);
		  }
		}
	  }else{
		$del_data['fid'] = $_POST['id'];
	    $model->where($del_data)->delete();
	  }
	  //附件处理
	  $model = M('attachments');
	  $a_data['source'] = MODULE_NAME;
	  $a_data['sourceid'] = $_POST['id'];
	  $model->where($a_data)->delete();
	  //echo $model->getlastsql();exit;
	  if($_POST['uppicarr']){
		  $model = M('attachments');
		  foreach($_POST['uppicarr'] as $key=>$file){
			$add_data['source'] = MODULE_NAME;
			$add_data['sourceid'] = $_POST['id'];
			$add_data['filepath'] = $file;
			$file_dir = str_replace(C('IMG_URL'),C('IMG_ROOT'),$file);
			$add_data['size'] = filesize($file_dir);
			$add_data['type'] = get_file_type($file_dir);
			$add_data['filename'] = $add_data['title'] = $_POST['uppicname'][$key];
			$add_data['create_time'] = time();
			$add_data['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
			$model->add($add_data);
			//echo $model->getlastsql();exit;
		  }
	  }

	  $model = M('Files');
	  $vo_data['id'] = $_POST['id'];
	  $vo = $model->where($vo_data)->find();
	  if($vo['type']==1 && $_POST['type']==1){
	  	  //内部文档变更
		  $this->update_date($ids2,$_POST['id'],'del');
		  $this->update_date($ids,$_POST['id'],'add');	  
	  }else if($vo['type']==1 && $_POST['type']==2){
	  	  //内部文档改外部文档
		  $this->update_date($ids2,$_POST['id'],'del');
		  unset($ids);
		  $ids[] = 0;
		  $this->update_date($ids,$_POST['id'],'add');		  
	  }

	  if($vo['type']==2 && $_POST['type']==1){
	  	  //外部文档改内部文档
		  unset($ids2);
		  $ids2[] = 0;
		  $this->update_date($ids2,$_POST['id'],'del');
		  $this->update_date($ids,$_POST['id'],'add');	  
	  }
	  if (false === $model->create ()) {
		  //$this->error ( $model->getError () );
		$msg['status'] = 0;
		$msg['notice'] = $model->getError();
		$msg['error_code'] = 8002;
		echo  json_encode($msg);exit;	
	  }
	  // 更新数据
	  $list = $model->save ();
	  $this->update_file_users($_POST['id']);
	  $msg['status'] = 1;
	  $msg['notice'] = '编辑成功!';
	  $msg['error_code'] = 8002;
	  echo  json_encode($msg);exit;
	  //$this->success ('编辑成功!');
  }

  Private function update_file_users($id){
    $model = M('Files');
    $data['id'] = $id;
	$fu_model = M('files_user');
	$fu_data['fid'] = $id;
	$list = $fu_model->field('user_id')->where($fu_data)->select();
	foreach($list as $val){
	  $ids[] = $val['user_id'];
	}
	if($ids){
	  $sdata['users'] = implode(',',$ids);
	}else{
	  $sdata['users'] = '';
	}
	$model->where($data)->save($sdata);
  }

  Private function update_date($user_ids,$id,$act='add'){
    $model = M('files_date');
	$fmodel = M('files');
	$fdata['id'] = $id;
	$file = $fmodel->field('create_time')->where($fdata)->find();
	if($act=='add'){
	  foreach($user_ids as $user_id){
		  $data['user_id'] = $user_id;
		  $data['y'] = date('Y',$file['create_time']);
		  $data['m'] = date('m',$file['create_time']);
		  $vo = $model->where($data)->find();
		  if($vo){
			$sdata['num'] = $vo['num']+1;
			$model->where($data)->save($sdata);
		  }else{
			$data['num'] = 1;
			$model->add($data);
		  }
		  unset($data);
	  }
	}else{
	  foreach($user_ids as $user_id){
		  $data['user_id'] = $user_id;
		  $data['y'] = date('Y',$file['create_time']);
		  $data['m'] = date('m',$file['create_time']);
		  $vo = $model->where($data)->find();
		  if($vo){
			if($vo['num']>1){
			  $sdata['num'] = $vo['num']-1;
			  $model->where($data)->save($sdata);
			}else{
			  $model->where($data)->delete();			
			}
		  }
	  }

	}
  }


  function get_year(){
    $model = M('files_date');
	$where = " user_id = ".$this->user['id']." or user_id=0";
    $list = $model->where($where)->order('y desc,m desc')->select();
	//dump($list);
	//echo $model->getlastsql();
	foreach($list as $val){
	  $key = $val['y'];
	  $years[$key]['year'] = $val['y'];
	  if($years[$key]['year']){
	    $val['user_id'] = $this->user['id'];
		$val['y'] = $val['y'];
		$val['m'] = $val['m'];
		$val['num'] = $years[$key]['data'][0]['num']+$val['num'];
		unset($years[$key]['data']);
	  }
	  $years[$key]['data'][] = $val;
	}
	foreach($years as $year){
	  $list2[] = $year;
	}
	//dump($list2);
	echo json_encode($list2);
  }

  function get_month(){
    $model = M('files_date');
	//$data['y'] = $_GET['y'];
	$where = " y=".$_GET['y']." AND (user_id = ".$this->user['id']." or user_id=0)";
    $list = $model->where($where)->group('m')->order('y desc')->select();
	foreach($list as $val){
	  $month[] = $val['m'];
	}
	echo json_encode($month);
  }

}
?>