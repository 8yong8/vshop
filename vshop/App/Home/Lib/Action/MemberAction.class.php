<?php
class MemberAction extends CommonAction {

	//列表页
	public function index(){
		$model = M('Order');
		$data['member_id'] = $this->user['id'];
		$data['status'] = 0;
		//待付款
		$dfk_count = $model->where($data)->count();
		//待发货
		$data['status'] = 1;
		$data['shipping_status'] = 0;
		$dfh_count = $model->where($data)->count();
		//待确认
		$data['status'] = 1;
		$data['shipping_status'] = 1;
		$dqr_count = $model->where($data)->count();
		//待评价
		$data['status'] = 2;
		$data['shipping_status'] = 2;
		$dpj_count = $model->where($data)->count();
		//待退款
		unset($data);
		$data['is_refund'] = 1;
		$dtk_count = $model->where($data)->count();
		$list['dfk_count'] = $dfk_count;
		$list['dfh_count'] = $dfh_count;
		$list['dqr_count'] = $dqr_count;
		$list['dpj_count'] = $dpj_count;
		$list['dtk_count'] = $dtk_count;
		$list['user'] = $this->user;
		//dump($list);
		ajaxSucReturn($list);
	}


	//获取用户信息
	public function get_user_msg(){
		ajaxSucReturn($this->user);
	}

    //用户其他信息
	public function get_user_other(){
		//收藏
		$model = M('favorite');
		$data['member_id'] = $this->user['id'];
		$favo_count = $model->where($data)->count();
		$msg['favo_count'] = $favo_count;
		//评价
		$model = M('comment');
		$meeting_count = $model->where($data)->count();
		$msg['meeting_count'] = $meeting_count;
		echo json_encode($msg);
	}

	//添加页面
	public function _before_add(){
		if($_FILES){
		  import("ORG.Net.UploadFile");
		  $upload = new UploadFile();
		  //设置上传文件大小
		  $upload->maxSize  = 3145728 ;
		  //设置上传文件类型
		  $upload->allowExts  = array('jpg','gif','png','jpeg');
		  $upload->saveRule = 'uniqid';
		  $dir = get_dir($_POST['id']);
		  $path = $upload->savePath =  C('IMG_ROOT').$dir.'/'.'avatar/'.date('Y').'/'.date('m').'/'.date('d').'/';
		  mk_dir($upload->savePath);
		  $upload->upload();
		  $info = $upload->getUploadFileInfo();
		  if($info!=""){
			foreach($info as $file){
			  //图片调整大小
			  $imginfo = getimagesize($path.$file['savename']);
			  $width = $imginfo[0];
			  $height = $imginfo[1];
			  if($width>500 || $height>320){
				$fullfilename = $path.$file['savename'];
				ImageResize($fullfilename,500,320);  
			  }
			  $key = $file['key'];
			  $_POST[$key] = C('IMG_URL').$dir.'/'.'avatar/'.date('Y').'/'.date('m').'/'.date('d').'/'.$file['savename'];
			}
		  }
		} 
		if(!$_POST)$this->other_info(1);
	}


	//编辑
	public function edit(){
		$model = D('Member');
		$data['a.id'] = $_GET['id'];
		$vo = $model->table(C('DB_PREFIX').'member as a')->join(C('DB_PREFIX').'member_msg as b on a.id=b.member_id')->where($data)->find();
		$this->other_info($vo['pv_id'],$vo['ct_id']);
		if($vo['utype']==1){
		  $lvs[] = array('name'=>'普通会员','val'=>0);
		  $lvs[] = array('name'=>'VP1','val'=>1);
		  $lvs[] = array('name'=>'VP2','val'=>2);
		  $lvs[] = array('name'=>'VP3','val'=>3);
		  $lvs[] = array('name'=>'VP4','val'=>4);
		  $lvs[] = array('name'=>'VP5','val'=>5);
		  $lvs[] = array('name'=>'钻石会员','val'=>6);
		  $this->assign('lvs',$lvs);
		}else if($vo['utype']==3){
		  $lvs[] = array('name'=>'银牌','val'=>1);
		  $lvs[] = array('name'=>'金牌','val'=>2);
		  $this->assign('lvs',$lvs);
		}
		$this->assign('vo',$vo);
		$this->display();
	}

	//更新
	public function update(){
		$model = D('Member');
		$_POST['update_time'] = time();
		if($_POST['exp_time'])$_POST['exp_time'] = strtotime($_POST['exp_time'])+60*60*24-1;
		//普通会员
		if($_POST['utype']==1){
			$_POST['bus_lv'] = 0;
			$_POST['bus_lv_name'] = '';	  
		}else{
			 $data['id'] = $_POST['id'];
			 $vo = $model->field('id,pass_time')->where($data)->find();
			if($vo['pass_time']==0)$_POST['pass_time'] = time();
		}
		if (false === $model->create ()) {
			dump($model->getError ());exit;
			//$this->error ( $model->getError () );
		}
		echo 11;exit;
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$model = D('Member_msg');
			$wdata['member_id'] = $_POST['id'];
			$count = $model->where($wdata)->count();
			if($count==0){
			  $_POST['member_id'] = $_POST['id'];
			  if (false === $model->create ()) {
				$this->error ( $model->getError () );
			  }
			  $model->add();
			}else{
			  $sdata['intro'] = $_POST['intro'];
			  $result = $model->where($wdata)->save($sdata);		
			}
			$this->history($_POST['id']);
			ajaxSucReturn($this->user);
		} else {
			//错误提示
			ajaxErrReturn('提交失败');
		}
	}

	//更新
	public function update2(){
		$model = D('Member');
		$_POST['update_time'] = time();
		if($_POST['exp_time'])$_POST['exp_time'] = strtotime($_POST['exp_time'])+60*60*24-1;
		//普通会员
		if($_POST['utype']==1){
			$_POST['bus_lv'] = 0;
			$_POST['bus_lv_name'] = '';	  
		}else{
			 $data['id'] = $_POST['id'];
			 $vo = $model->field('id,pass_time')->where($data)->find();
			if($vo['pass_time']==0)$_POST['pass_time'] = time();
		}
		$_POST['id'] = $this->user['id'];
		dump($model->create ());
		if (false === $model->create ()) {
			dump($model->getError ());exit;
			//$this->error ( $model->getError () );
		}
		//echo 11;exit;
		// 更新数据
		$list = $model->save ();
		echo $model->getlastsql();exit;
		if (false !== $list) {
			//成功提示
			$model = D('Member_msg');
			$wdata['member_id'] = $_POST['id'];
			$count = $model->where($wdata)->count();
			if($count==0){
			  $_POST['member_id'] = $_POST['id'];
			  if (false === $model->create ()) {
				$this->error ( $model->getError () );
			  }
			  $model->add();
			}else{
			  $sdata['intro'] = $_POST['intro'];
			  $result = $model->where($wdata)->save($sdata);		
			}
			$this->history($_POST['id']);
			ajaxSucReturn($this->user);
		} else {
			//错误提示
			ajaxErrReturn('提交失败');
		}
	}

	//修改
	public function _before_edit(){
		if($_FILES){
		  import("ORG.Net.UploadFile");
		  $upload = new UploadFile();
		  //设置上传文件大小
		  $upload->maxSize  = 3145728 ;
		  //设置上传文件类型
		  $upload->allowExts  = array('jpg','gif','png','jpeg');
		  $upload->saveRule = 'uniqid';
		  $dir = get_dir($_POST['id']);
		  $path = $upload->savePath =  C('IMG_ROOT').'avatar/'.$dir.'/';
		  mk_dir($upload->savePath);
		  $upload->upload();
		  $info = $upload->getUploadFileInfo();
		  if($info!=""){
			foreach($info as $file){
			  //图片调整大小
			  $imginfo = getimagesize($path.$file['savename']);
			  $width = $imginfo[0];
			  $height = $imginfo[1];
				//缩略图2(250*250)
				$full_blitfilename2 = $path.'m.jpg';
				copy($path.$file['savename'],$full_blitfilename2);
				ImageResize($full_blitfilename2,120,120);
				//缩略图2(250*250)
				$full_blitfilename3 = $path.'s.jpg';
				copy($path.$file['savename'],$full_blitfilename3);
				ImageResize($full_blitfilename3,64,64);
				//原图
				$full_blitfilename = $path.'b.jpg';
				rename($path.$file['savename'],$full_blitfilename);
			  $key = $file['key'];
			  $_POST[$key] = C('IMG_URL').'avatar/'.$dir.'/m.jpg';
			}
		  }
		} 
	}

	//重置密码
	public function resetPwd(){
		$model = D('Member');
		$id = $_POST['id'];
		if($id<1)return false;
		$vo = $model->field('id,salt')->where('id='.$id)->find();
		$data['password'] = md5($_POST['password'].$vo['salt'].$vo['salt'][1]);
		$result = $model->where('id='.$id)->save($data);
		if($result){
		  echo 1;
		}else{
		   echo 0;
		}
	}

	// 检查帐号
	public function checkAccount() {
		$model = M("Member");
		// 检测用户名是否冲突
		$data['username']  =  $_REQUEST['username'];
		$count = $model->where($data)->count();
		//echo $model->getlastsql();exit;
		$ucresult = $count>0 ? '用户已存在!' : '可以注册!';
		echo $ucresult;exit;
	}

	//省市信息
	public function other_info($pv_id,$city_id){
		//省
		$model = M('area_city');
		$data['class_type'] = 1;
		$pvlist = $model->where($data)->select();
		//echo $model->getlastsql();exit;
		$this->assign('pvlist',$pvlist);
		//dump($pvlist);exit;
		$model = M('area_city');
		//市
		$city_data['pid'] = $pv_id;
		//$city_data['class_type'] = 2;
		$ctlist = $model->where($city_data)->select();
		$this->assign('ctlist',$ctlist);
		//区
		$model = M('area_city');
		$district_data['pid'] = $city_id;
		$districts = $model->where($district_data)->select();
		$this->assign('districts',$districts);
	}

	//根据省获取市信息
	public function get_city(){
		$model = M('area_city');
		$where['pid'] = $_POST['pid'];
		$list = $model->where($where)->select();
		$json_ct = json_encode($list);
		echo $json_ct;
	}

	//根据市获取区信息
	public function get_district(){
		$model = M('area_city');
		$where['pid'] = $_POST['pid'];
		$list = $model->where($where)->select();
		$json_ct = json_encode($list);
		echo $json_ct;
	}


	public function show(){
		$model = M('Member');
	}

	/**
	 *  我的二维码
	 */
	public function qrcode() 
	{
		$r = base64_encode($this->user['mobile']);
		$url = C('PRO_URL') . "/index.php?m=Public&a=register&r={$r}";
		$IMG_ROOT = C('IMG_ROOT').'avatar/'.get_dir($this->user['id']);
		$IMG_URL = C('IMG_URL').'avatar/'.get_dir($this->user['id']);
		$img_dir = $IMG_ROOT.'/qrcode.png';
		$img_url = $IMG_URL.'/qrcode.png';
		if(!file_exists($img_dir)){
			include C('PUBLIC_INCLUDE').'phpqrcode.php';
			mk_dir($IMG_ROOT);
			QRcode::png($url,$img_dir,'L',8, 2);
		}
		//echo $img_url;exit;
		$data['img_url'] = $img_url;
		ajaxSucReturn($data);
	}

	//退出
	function logout(){
		$model = M('member');
		//file_put_contents('./1.txt',$this->user['id'].'/'.$_POST['token']);
		//dump($_POST);exit;
		if($this->user){
		  $wdata['id'] = $this->user['id'];
		  $sdata['baiduUserId'] = '';
		  $result = $model->where($wdata)->save($sdata);
		  if($result){
			$msg['error_code'] = 0;
			$msg['notice'] = '退出成功';
			echo  json_encode($msg);exit;		  
		  }else{
			$msg['error_code'] = 8003;
			$msg['notice'] = '退出失败';
			echo  json_encode($msg);exit;	  
		  }
		}else{
			$msg['error_code'] = 1001;
			$msg['notice'] = '用户不存在';
			echo  json_encode($msg);exit;	
		}
	}

}
?>