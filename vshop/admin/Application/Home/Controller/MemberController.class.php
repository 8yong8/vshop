<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends CommonController {

  /**
   * 列表页
   */
  public function index(){
	$model = D('Member');
	$where = $this->_search();//获得查询条件
	if(isset($_GET['_order'])) {
		$order = $_GET['_order'];
	}else {
		$order = !empty($sortBy)? $sortBy: $model->getPk();
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if(isset($_GET['_sort'])) {
		$sort = $_GET['_sort']?'asc':'desc';
	}else {
		$sort = $asc?'asc':'desc';
	}
	$count = $model->where($where)->count();
	$this->assign('count',$count);
	if($count>0){
	  //创建分页对象
	  $listRows = '50';
	  if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	  }
	  $p = new \My\Page($count,$listRows);

	  //$list = $model->field('id,utype,username,realname,balance,create_time,status')->where($where)->order( "`" . $order . "` " . $sort)->limit($p->firstRow.','.$p->listRows)->select();
	  $list = $model->alias('a')->join('`'.C('DB_PREFIX').'member_wallet` as b on a.id=b.member_id')->field('a.id,a.utype,a.username,a.realname,a.create_time,a.status,province,city,district,b.balance,b.frozen,lv_name,bus_lv_name')->where($where)->order( "`" . $order . "` " . $sort)->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $model->getlastsql();
	//分页跳转的时候保证查询条件
	foreach($map as $key=>$val) {
		if(is_array($val)) {
			foreach ($val as $t){
				$p->parameter	.= $key.'[]='.urlencode($t)."&";
			}
		}else{
			$p->parameter   .=   "$key=".urlencode($val)."&";        
		}
	}
	//分页显示
	$page       = $p->Show();
	}
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	//echo json_encode($list);exit;
	$this->assign('list',       $list);
	$this->assign('sort',       $sort);
	$this->assign('order',      $order);
	$this->assign('sortImg',    $sortImg);
	$this->assign('sortType',   $sortAlt);
	$this->assign("page",       $page);
	$this->display();
  }

  /**
   * 搜索条件
   */
  public function _search(){
	if($_GET['id']){
	  $data['id'] = $_GET['id'];
	  $this->assign('id',$_GET['id']);
	}
	if($_GET['utype']){
	  $data['utype'] = $_GET['utype'];
	  $this->assign('utype',$_GET['utype']);
	}
	if($_GET['username']){
	  $data['username'] = $_GET['username'];
	  $this->assign('username',$_GET['username']);
	}
    return $data;
  }

  /**
   * 添加页面 前置执行
   */
  public function _before_add(){
	if($this->checkFileUp()){
	  //$this->upload();
	} 
	if(!$_POST)$this->other_info(1);
  }

  /**
   * 添加会员
   */
  public function add() {
	  if($_POST){
		$salt = rand_string(6,-1);
		$_POST['salt'] = $salt;
		$name=CONTROLLER_NAME;
		$model = D ($name);
		if(!$_POST['password']){
		  $this->error('密码必须!');
		}
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['createname'] = $_SESSION['nickname'];
		$_POST['password'] = md5($_POST['password'].$salt.$salt[1]);
		if($_POST['exp_time'])$_POST['exp_time'] = strtotime($_POST['exp_time'])+60*60*24-1;
	    //普通会员
	    if($_POST['utype']==1){
		  $_POST['bus_lv'] = 0;
		  $_POST['bus_lv_name'] = '';	  
	    }else{
		  $_POST['pass_time'] = time();
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list = $model->add ();
		//echo $model->getlastsql();exit;
		if ($list!==false) { //保存成功
		    $this->history($list);
			$model = D('Member_msg');
			$_POST['member_id'] = $list;
			if (false === $model->create ()) {
			  $this->error ( $model->getError () );
			}
			$model->add();
			$model = D('member_wallet');
			$mw_data['update_time'] = time();
			$mw_data['member_id'] = $list;
			$model->add($mw_data);
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}	  
	  }else{
		  $lvs[] = array('name'=>'普通会员','val'=>0);
		  $lvs[] = array('name'=>'VP1','val'=>1);
		  $lvs[] = array('name'=>'VP2','val'=>2);
		  $lvs[] = array('name'=>'VP3','val'=>3);
		  $lvs[] = array('name'=>'VP4','val'=>4);
		  $lvs[] = array('name'=>'VP5','val'=>5);
		  $lvs[] = array('name'=>'钻石会员','val'=>6);
		  $this->assign('lvs',$lvs);
		  $this->display ();
	  }
  }

  /**
   * 编辑 前置
   */
  public function _before_edit(){
	if($this->checkFileUp()){
	  $config['saveRule'] = '';
	  $config['savePath'] = 'avatar/'.get_dir($_POST['id']).'/';
	  $config['thumbPrefix'] = 'b_,m_,s_';
	  $config['thumbMaxWidth'] = '200,120,48';
	  $config['thumbMaxHeight'] = '200,120,48';
	  $config['saveName'] = 'logo';
	  $config['subName'] = '';
	  $config['saveExt'] = 'jpg';
	  $this->upload($config);
	}
  }

  /**
   * 编辑会员信息
   */
  public function edit(){
    $model = D('Member');
	if($_POST){
		//dump($_POST);exit;
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
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $list = $model->save ();
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
		  $sdata['sex'] = $_POST['sex'];
		  $sdata['intro'] = $_POST['intro'];
		  $result = $model->where($wdata)->save($sdata);		
		}

		$this->history($_POST['id']);
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	  exit;
	}
	$data['a.id'] = $_GET['id'];
    $vo = $model->table(C('DB_PREFIX').'member as a')->join(C('DB_PREFIX').'member_msg as b on a.id=b.member_id')->where($data)->find();
	//dump($vo);exit;
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

	/**
     * 其他信息编辑
     */
	public function head(){
        if(IS_POST){
            $img_dir = str_ireplace(C('IMG_URL'), C('IMG_ROOT'), $_POST['filePath']);
            $img_dir = preg_replace("/\?.*/is", '', $img_dir);
            $image = new \Think\Image(1,$img_dir);
            $b_img = str_ireplace('logo', 'b_logo', $img_dir);
            $image->crop(200,200,$_POST['x'],$_POST['y'])->save($b_img);
            $m_img = str_ireplace('logo', 'm_logo', $img_dir);
            $image->thumb(120, 120)->save($m_img);
            $s_img = str_ireplace('logo', 's_logo', $img_dir);
            $image->thumb(48, 48)->save($s_img);
            if(file_exists($s_img)){
                $this->success('上传成功');
            }else{
                $this->error('上传失败');
            }
            EXIT;
        }else{
            $img_dir =  C('IMG_ROOT').'avatar/'.get_dir($_GET['id']).'/m_logo.jpg';
            if(file_exists($img_dir)){
              $img =  C('IMG_URL').'avatar/'.get_dir($_GET['id']).'/m_logo.jpg';
            }
            $this->assign('img',$img);
        }
        $this->display();
  
	}

    public function head_up(){
	    $config['thumb'] = true;
        $config['thumbPrefix'] = 'Original';  //原图压缩
        $config['thumbMaxWidth'] = '500';
        $config['thumbMaxHeight'] = '500';
        $config['saveRule'] = '';
        $config['savePath'] = 'avatar/'.get_dir($_POST['member_id']).'/';
        $config['saveName'] = 'logo';
        $config['subName'] = '';
        $config['saveExt'] = 'jpg';
        $config['replace'] = true;
        $result = $this->upload($config);
        //dump($result['url']);exit;
        $arr['img'] = $result['url']['profileLogo'].'?'.NOW_TIME;
        echo json_encode($arr);       
    }

  /**
   * 其他信息编辑
   */
  function memmsg_edit(){
    $model = D('member_msg');
	if($_POST){
		//dump($_POST);exit;
	    //图片处理处理
		if($_FILES){
		  foreach($_FILES as $key=>$val){
		    if($val['name']){
			  $this->upload();
			  break;
			}
		  }
		}
	  if($_POST['tid']){
	    $tmodel = M('trade');
		$tdata['id'] = $_POST['tid'];
		//dump($tmodel->where($tdata)->getField('name'));exit;
		$_POST['tname'] = $tmodel->where($tdata)->getField('name');
	  }
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  $data['member_id'] = $_POST['id'];
	  $list = $model->where($data)->save();
	  //echo $model->getlastsql();exit;
	  if(!$list){
		$_POST['member_id'] = $_POST['id'];
	    $model->add($_POST);
	  }
	  if (false !== $list) {
		$this->history($_POST['id'],'edit');
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	  exit;
	}
	$data['a.id'] = $_GET['id'];
	$vo = $model->table('`'.C('DB_PREFIX').'member` as a')->join('`'.C('DB_PREFIX').'member_msg` as b on a.id=b.member_id')->where($data)->find();
	$this->assign('vo',$vo);
	$this->display('memmsg_edit2');
	/*
	if($vo['utype']==1){
	  $this->display('memmsg_edit2');
	}else{
	  $model = M('trade');
	  $t_data['status'] = 1;
	  $tlist = $model->where($t_data)->order('id asc')->select();
	  $this->assign('tlist',$tlist);
	  if($vo['utype']==5){
	    $this->other_info($vo['agent_pv_id'],$vo['agent_ct_id']);
	  }
	  $this->display();
	}*/
  }

  /**
   * 代理商编辑
   */
  public function agent_edit(){
    $model = D('agent');
	if($_POST){
		//dump($_POST);exit;
	    //图片处理处理
		if($_FILES){
		  foreach($_FILES as $key=>$val){
		    if($val['name']){
			  $this->upload();
			  break;
			}
		  }
		}
	  if($_POST['tid']){
	    $tmodel = M('trade');
		$tdata['id'] = $_POST['tid'];
		//dump($tmodel->where($tdata)->getField('name'));exit;
		$_POST['tname'] = $tmodel->where($tdata)->getField('name');
	  }
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  $data['member_id'] = $_POST['id'];
	  $list = $model->where($data)->save();
	  if(!$list){
		$_POST['member_id'] = $_POST['id'];
	    $model->add($_POST);
	  }
	  if (false !== $list) {
		$this->history($_POST['id'],'edit','agent');
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	  exit;
	}
	$data['a.id'] = $_GET['id'];
	$vo = $model->table('`'.C('DB_PREFIX').'member` as a')->join('`'.C('DB_PREFIX').'agent` as b on a.id=b.member_id')->where($data)->find();
	if($vo['utype']!=5){
		$this->error('你不是代理商');
	}
	$this->assign('vo',$vo);
	$model = M('trade');
	$t_data['status'] = 1;
	$tlist = $model->where($t_data)->order('id asc')->select();
	$this->assign('tlist',$tlist);
	$this->other_info($vo['agent_pv_id'],$vo['agent_ct_id']);
	$this->display();
  }

  /**
   * 重置密码
   */
  public function resetPwd(){
    $model = D('Member');
	$id = $_POST['id'];
	if($id<1)return false;
	$vo = $model->field('id,salt,password')->where('id='.$id)->find();
	$data['password'] = md5($_POST['password'].$vo['salt'].$vo['salt'][1]);
	if($data['password']==$vo['password']){
	  $msg['error_code'] = 1;
	  $msg['notice'] = '于原密码一样，密码修改失败';
	  echo json_encode($msg);exit;	
	}
	$result = $model->where('id='.$id)->save($data);
	if($result){
	  $msg['error_code'] = 0;
	  $msg['notice'] = '密码已修改为：'.$_POST['password'];
	  echo json_encode($msg);exit;
	}else{
	  $msg['error_code'] = 1;
	  $msg['notice'] = '密码修改失败';
	  echo json_encode($msg);exit;
	}
	
  }

  /**
   * 检查帐号
   */
  public function checkAccount() {
	$model = M("Member");
	// 检测用户名是否冲突
	$data['username']  =  $_REQUEST['username'];
	$count = $model->where($data)->count();
	//echo $model->getlastsql();exit;
	$ucresult = $count>0 ? '用户已存在!' : '可以注册!';
	echo $ucresult;exit;
  }

  /**
   * 省市信息
   */
  public function other_info($pv_id,$city_id){
	//省
	$model = M('Region');
	$data['area_type'] = 1;
	$pvlist = $model->where($data)->select();
	//echo $model->getlastsql();exit;
	$this->assign('pvlist',$pvlist);
	//dump($pvlist);exit;
	$model = M('Region');
	//市
	$city_data['pid'] = $pv_id;
	//$city_data['class_type'] = 2;
	$ctlist = $model->where($city_data)->select();
	$this->assign('ctlist',$ctlist);
	//区
	$model = M('Region');
	$district_data['pid'] = $city_id;
	$districts = $model->where($district_data)->select();
	$this->assign('districts',$districts);
  }

  /**
   * 根据省获取市信息
   */
  public function get_city(){
	$model = M('Region');
	$where['pid'] = $_POST['pid'];
	$list = $model->where($where)->select();
	$json_ct = json_encode($list);
	echo $json_ct;
  }

  /**
   * 根据市获取区信息
   */
  public function get_district(){
	$model = M('Region');
	$where['pid'] = $_POST['pid'];
	$list = $model->where($where)->select();
	$json_ct = json_encode($list);
	echo $json_ct;
  }

  /**
   * 添加管理员
   */
  public function user_add(){
    $model = M('member');
	$umodel = M('User');
	$data['id'] = $_GET['id'];
	$member = $model->where($data)->find();
	if(!$member){
	  $this->error ('添加失败!');
	}
	$count = $umodel->where($data)->count();
	if($count>0){
	  $sdata['is_ad'] = 1;
	  $model->where($data)->save($sdata);
	  $this->error ('已加入管理员团队!');
	}
	$member['account'] = $member['username'];
	$member['nickname'] = $member['realname'] ? $member['realname'] : $member['username'];
	$member['editorname'] = $member['username'];
	$result = $umodel->add($member);
	if($result){
	  $sdata['is_ad'] = 1;
	  $model->where($data)->save($sdata);
	  $this->success ('添加成功!');
	}else{
	  $this->error ('添加失败!');
	}
  }

  /**
   * 禁用会员
   */
  public function forbid($options,$field='status'){
	$name = CONTROLLER_NAME;
	$model = M($name);
	$data['id'] = array('in',$_GET['id']);
	$sdata['status'] = 0;
	$list = $model->where($data)->save($sdata);
	if ($list!==false) {
		$model = M('artists');
		$art_data['member_id'] = array('in',$_GET['id']);
		$art_data['_string'] = 'member_id>0';
		$art_sdata['status'] = 0;
		$model->where($art_data)->save($art_sdata);
		$this->success ( '操作成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }	

  /**
   * 启用会员
   */
  public function resume(){
    $model = M('Project_apply');
    $data['id'] = array('in',$_GET['id']);
	$list = $model->where($data)->select();
	$sdata['status'] = 1;
	$model->where($data)->save($sdata);
	if ($list!==false) {
			$model = M('artists');
			$art_data['member_id'] = array('in',$_GET['id']);
			$art_data['_string'] = 'member_id>0';
			$art_sdata['status'] = 1;
			$model->where($art_data)->save($art_sdata);
			$this->success ( '操作成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 导入用户 废弃
   */
  public function dao(){
	exit;
    $model = M('Member');
	$data['id'] = array('gt',107);
    $list = $model->table('137home.`137_member`')->where($data)->order('id asc')->limit(10000)->select();
	//echo $model->getlastsql();exit;
	foreach($list as $val){
	  //$add_data['id'] = $val['id'];
	  $add_data['username'] = $val['username'];
	  $add_data['realname'] = $val['realname'];
	  $add_data['mobile'] = $val['tel'];
	  $add_data['password'] = $val['password'];
	  $add_data['create_time'] = $val['create_time'];
	  $model->add($add_data);
	}
  }

  /**
   * 数据处理 废弃
   */
  public function dp(){
	exit;
    $model = M('Member');
	$data['update_time'] = array('lt',time()-86400*5);
	$list = $model->field('id')->where($data)->order('id desc')->limit(500)->select();
	$from = array(
		0 => 'app',	
		1 => 'wap',	
		2 => 'pc',	
	);
	foreach($list as $val){
	  $n = rand(0,2);
	  $wdata['id'] = $val['id'];
	  $sdata['from'] = $from[$n];
	  $sdata['update_time'] = time();
	  $model->where($wdata)->save($sdata);
	}
  
  }

  /**
   * 获取用户信息
   */
  public function get_members(){
    $model = D('member');
	if(is_numeric($_POST['key'])){
	  $map['id'] = $_POST['key'];
	}else{
      $data['realname'] = array('like','%'.$_POST['key'].'%');
	  $data['username'] = array('like','%'.$_POST['key'].'%');
	  $data['_logic'] = 'or';
	  $map['_complex'] = $data;	
	}
	$members = $model->field('id,username,realname,utype')->where($map)->select();
	if(IS_AJAX){
	  echo json_encode($members);
	}else{
	  return $members;
	}
  }

}
?>