<?php
class CommonAction extends Action {
  protected $configs = array();    //项目配置
  protected $user = '';            //会员信息

  //前期执行
  public function _initialize(){
	include_once C('PUBLIC_INCLUDE')."function.inc.php";
	if(!$_REQUEST['token']){
	  $token = $_REQUEST['token'] = 'a14782764160fe8905a69b22ccb84eb2';
	}else{
	  $token = $_REQUEST['token'];
	}
	$model = M('member_token');
	$data['a.token'] = $token;
	$data['b.status'] = 1;
	$user = $model->field('b.id,b.username,b.realname,b.mobile,b.province,b.city,b.district,b.pv_id,b.ct_id,b.dist_id,b.logo,b.last_login_time,b.baiduUserId,b.mb_system,b.status,c.balance,c.frozen,c.points')->table('`'.C('DB_PREFIX').'member_token` as a')->join('`'.C('DB_PREFIX').'member` as b on a.member_id=b.id')->join('`'.C('DB_PREFIX').'member_wallet` as c on a.member_id=c.member_id')->where($data)->find();
	//echo $model->getlastsql();exit;
	if($user){
		$user['balance'] = $user['balance'] ? $user['balance'] : 0;
		$user['frozen'] = $user['frozen'] ? $user['frozen'] : 0;
		$user['points'] = $user['points'] ? $user['points'] : 0;
		$this->user = $user;
	}else{
	  if(MODULE_NAME!='Public'){
		ajaxErrReturn('用户不存在',1001);
		/*
		$msg['status'] = 0;
		$msg['info'] = '用户不存在';
		$msg['error_code'] = -1;
		echo  json_encode($msg);exit;
		*/
	  }
	}
	/*
	//会员信息
	$model = M('user');
	$data['id'] = $_POST['user_id'];
	$user = $model->where($data)->find();
	$this->user = $user;
	$time = $_GET['time'] ? $_GET['time'] : $_POST['time'];
	//有效时间3小时
	if($_POST['vsign']==md5($time.C('sign')) && time()>$time+60*60*3 && $user){
	  $this->user = $user;
	}
	*/
  }


	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		//$name=$this->getActionName();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$this->assign($val,$_REQUEST [$val]);
				if($val=='nickname'){
				  $map [$val] = array('like','%'.$_REQUEST [$val].'%');
				}else{
				  $map [$val] = $_REQUEST [$val];
				}
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
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
		$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
		$page_count = ceil($count/$page_size);
		$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
		$offset = ($pageno - 1) * $page_size;
		$voList = $model->field($fileds)->where($map)->order( "`" . $order . "` " . $sort)->limit($offset. ',' . $page_size)->select();
		foreach($voList as $key=>$val){
		  if($val['msg']){
			$voList[$key]['msg'] = str_cut(htmlspecialchars_decode($val['msg']),30);
		  }
		  if($val['content']){
			$voList[$key]['content'] = str_cut(htmlspecialchars_decode($val['content']),30);
		  }
		}
		//echo $model->getlastsql();
		$list['count'] = $count;
		$list['page_count'] = $page_count;
		$list['data'] = $voList;
		ajaxSucReturn($list);
		//echo  json_encode($list);exit;
	}

	//更多信息
	function get_more_msg(){
		$model = M(MODULE_NAME);
		$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
		$pageno = $_POST['p'] ? $_POST['p'] : 1;
		//$pageno = 1;
		$offset = ($pageno - 1) * $page_size;
		$list = $model->where($data)->order('id desc')->limit($offset.','.$page_size)->select();
		foreach($list as $key=>$val){
		  if($val['content'])$list[$key]['content'] = str_cut(htmlspecialchars_decode($val['content']),45);
		}
		ajaxSucReturn($list);
		//echo json_encode($list);
	}

	//历史操作记录
	public function history($id,$action='',$model='',$info=''){
		$m = D('action_log');
		$data['model'] = $model ? $model : $this->getActionName();
		$data['action'] = $action ? $action : ACTION_NAME;
		$data['uid'] = $this->user['id'];
		$data['username'] = $this->user['username'];
		$data['create_time'] = time();
		$data['sourceid'] = $id;
		//记录详细更新内容
		/*
		if(!$info){
		  $map = array ();
		  $model = D($data['model']);
		  foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_POST [$val] ) && $_POST [$val] != '') {
				$this->assign($val,$_POST [$val]);
				$map [$val] = $_POST [$val];
			}
		  }	
		}else{
		  $map = $info;
		}
		$data['info'] = serialize($map);
		*/
		$data['info'] = serialize($_REQUEST);
		$m->add($data);
	}


	//文件上传
	public function up_file(){
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 10241024 ;
		//设置上传文件类型
		$upload->allowExts  = array('jpg','gif','png','jpeg','swf','pdf','doc','text','txt');
		$upload->saveRule = 'uniqid';
		$path = $upload->savePath =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';
		mk_dir($upload->savePath);
		$upload->upload();
		$info = $upload->getUploadFileInfo();
		if($info!=""){
		  foreach($info as $file){
			$key = $file['key'];
			$_POST[$key] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$file['savename'];
		  }
		}
		echo json_encode($_POST[$key]);
	}

	protected function lookup($source,$sourceid){
		$model  = M('lookup');
		$data['source'] = $source;
		$data['user_id'] = $this->user['id'];
		$data['sourceid'] = $sourceid;
		$data['user_name'] = $this->user['nickname'];
		$data['create_time'] = time();
		$model->add($data);
	}

	//空页面
	public function _empty(){
	  $msg['error_code'] = '8001';
	  $msg['notice'] = '访问页面不存在';
	  echo json_encode($msg);exit;
	}

}
?>