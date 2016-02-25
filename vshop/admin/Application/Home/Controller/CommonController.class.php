<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class CommonController extends Controller {

  protected $configs;//网站配置	
  protected $can_del = 1; //0不可删除 1可删除

  /**
   * 前期执行
   */
  function _initialize() {
	include C('PUBLIC_INCLUDE')."function.inc.php";
	// 用户权限检查
	if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
		import ( '@.ORG.RBAC' );
		if (! RBAC::AccessDecision ()) {
			//检查认证识别号
			if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
				//跳转到认证网关
				redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
			}
			// 没有权限 抛出错误
			if (C ( 'RBAC_ERROR_PAGE' )) {
				// 定义权限错误页面
				redirect ( C ( 'RBAC_ERROR_PAGE' ) );
			} else {
			
				if (C ( 'GUEST_AUTH_ON' )) {
					$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 提示错误信息
				$this->error ( L ( '_VALID_ACCESS_' ) );
			}
		}
	  //左侧菜单
	  $this->menu();
	}
	$configs = getCache('Config:list');
	C(array_merge(C(''),$configs));
	$this->configs = $configs;
	//$this->configs = C('');
	$this->assign('configs',$configs);
	$hash = md5($_SESSION[C('USER_AUTH_KEY')].time());
	$this->assign('hash',$hash);
	$this->assign('head_title','后台管理中心');
	//include C('INTERFACE_PATH').'dz/config.inc.php';
	//include C('INTERFACE_PATH').'dz/uc_client/client.php';
  }

  /**
   * 列表页
   */
  public function index() {
	//列表过滤器，生成查询Map对象
	$map = $this->_search ();
	if (method_exists ( $this, '_filter' )) {
		$this->_filter ( $map );
	}
	$name = CONTROLLER_NAME;
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
	return __CONTROLLER__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
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
		$name = CONTROLLER_NAME;
	}
	$model = D ( $name );
	$map = array ();
	foreach ( $model->getDbFields () as $key => $val ) {
		if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
			$this->assign($val,$_REQUEST [$val]);
			//dump(C('DB_LIKE_FIELDS'));exit;
			if(C('DB_LIKE_FIELDS')){
			  $like_keys = explode('|',C('DB_LIKE_FIELDS'));
			  if(in_array($val,$like_keys)){
				$map [$val] = array('like','%'.$_REQUEST [$val].'%');
			  }else{
				$map [$val] = $_REQUEST [$val];
			  }
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
	//取得满足条件的记录数
	$count = $model->where ( $map )->count ();
	//echo $model->getlastsql();exit;
	if (! empty ( $_REQUEST ['listRows'] )) {
		$listRows = $_REQUEST ['listRows'];
	} else {
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 20;
	}
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if ($count > 0) {
		//创建分页对象
		$p = new \My\Page ( $count, $listRows );
		//分页查询数据

		$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
		//分页跳转的时候保证查询条件
		foreach ( $map as $key => $val ) {
			if (! is_array ( $val )) {
				$p->parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		//分页显示
		$page = $p->show ();
		//列表排序显示
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示
		$this->assign ( 'list', $voList );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( "page", $page );
	}
	//dump($voList);exit;
	cookie( '_currentUrl_', __SELF__ );
	return;
  }

  /**
   * 添加数据
   */
  public function add() {
	  if(IS_POST){
		$name=CONTROLLER_NAME;
		$model = D ($name);
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if($this->checkFileUp()){
			$this->upload();
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list = $model->add ();
		if ($list!==false) { //保存成功
		    $this->history($list);
			//$this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
			if($_POST['flags']){
			  $this->flags_post($list);
			}
			if(method_exists($this,'_after_add')){
			  $this->_after_add($list);
			}
			if(method_exists($this,'GiveCache')){
			  $this->GiveCache($list);
			}
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}	  
	  }else{
		$this->display ();
	  }
  }

  /**
   * 查看信息
   */
  public function look(){
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	$this->assign('vo',$vo);
    $this->display();
  }

  /**
   * 编辑数据
   */
  public function edit() {
	  if(IS_POST){
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['update_time'] = time();
		if($this->checkFileUp()){
			$this->upload();
		}
		if($_POST['flags']){
		  $this->flags_post();
		}else{
		  $_POST['flags'] = '';
		}
		if (false === $model->create ()) {
		  $this->error ( $model->getError () );
		}
		//dump($_POST);exit;
		// 更新数据
		$list = $model->save ();
		//echo $model->getlastsql();exit;
		if (false !== $list) {
		  //成功提示
		  if(method_exists($this,'GiveCache')){
			$this->GiveCache(0);
		  }
		  //之后执行
		  if(method_exists($this,'_after_edit')){
			$this->_after_edit();
		  }
		  //dump($_POST);exit;
		  $this->history($_POST['id']);
		  $this->success ('编辑成功!');
		} else {
		  //错误提示
		  $this->error ('编辑失败!');
		}
	  }else{
		$name=CONTROLLER_NAME;
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display();
	  }
  }

  /**
   * 检查是否有文件上传
   */
  public function checkFileUp(){
	  foreach($_FILES as $key=>$val){
		if($val['name']){
		  return true;
		}
	  }
	  return false;
  }

  /**
   +----------------------------------------------------------
   * 默认删除操作
   +----------------------------------------------------------
   * @access public
   +----------------------------------------------------------
   * @return string
   +----------------------------------------------------------
   * @throws ThinkExecption
   +----------------------------------------------------------
   */
  public function delete() {
	//删除指定记录
	$name = CONTROLLER_NAME;
	$model = M ($name);
	$this->assign('jumpUrl',__MODULE__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		//$id = $_REQUEST [$pk];
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$list = $model->where ( $condition )->setField ( 'status', - 1 );
			if ($list!==false) {
				$this->history($id);
				$this->success ('删除成功！' );
			} else {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }

  /**
   +----------------------------------------------------------
   * 值重复检查
   * @param Model $a 被检查对象
   * @param HashMap $b 匹配对象
   +----------------------------------------------------------
   * @access public
   +----------------------------------------------------------
   * @return string
   +----------------------------------------------------------
   * @throws FcsException
   +----------------------------------------------------------
   */
  public function key_compare_func($a, $b){
	if(!$a){
	  $data['a'] = $b;
	  return $data;
	}
	foreach($a as $v){
	  $key = array_search($v,$b);
	  if($key===false){
		//无法查到的
		$d_fids[] = $v['fid'];
	  }
	}
	foreach($b as $v){
	  $key = array_search($v,$a);
	  if($key===false){
		//无法查到的
		$a_fids[] = $v['fid'];
	  }
	}
	$data['k'] = array_intersect($a, $b);
	$data['d'] = $d_fids;
	$data['a'] = $a_fids;
	return $data;
  }

  /**
   * flag处理
   */
  public function flags_post($id){
	$name = CONTROLLER_NAME;
	$flagss = $this->get_moudel_flags();
	$sourceid = $id ? $id : $_POST['id'];
	//dump($_POST['flags']);exit;
	if($_POST['flags']){
	  $flags = $_POST['flags'];
	  $amodel = D('flag_list');
	  $wdata['source'] = $name;
	  $wdata['sourceid'] = $sourceid;
	  $list = $amodel->field('fid')->where($wdata)->select();
	  if($list){
	    foreach($list as $val){
	      $flagslist[] = $val['fid'];
	    }
		$pipei = $this->key_compare_func($flagslist,$flags);	  
	  }else{
	    $pipei['a'] = $flags;
	  }
	  //删除
	  if($pipei['d']){
		$d_fids = $pipei['d'];
		$wdata['fid'] = array('in',$d_fids);
		$amodel->where($wdata)->delete();
	  }
	  //添加
	  if($pipei['a']){
		$a_fids = $pipei['a'];
		foreach($a_fids as $fid){
		  $add_data['fid'] = $fid;
		  $add_data['fname'] = $flagss[$fid]['name'];
		  $add_data['source'] = $name;
		  $add_data['sourceid'] = $sourceid;
		  $add_data['create_time'] = time();
		  $amodel->add($add_data);
		  //echo $amodel->getlastsql();exit;
		}
	  }
	  $_POST['flags'] = implode(',',$_POST['flags']);
	}else{
	  //删除
	  $almodel = D('flag_list');
	  $aldata['source'] = $name;
	  $aldata['sourceid'] = $sourceid;
	  $almodel->where($aldata)->delete();	  
	  $_POST['flags'] = '';
	}
  }

  /**
   * 排序页面
   */
  public function sort($fields = 'id,title,sort,b.create_time'){
	$name = CONTROLLER_NAME;
    $amodel = D('flag_list');
	$flags = $this->get_moudel_flags();
	$this->assign('flags',$flags);
	if($_GET['fid']){
	  $this->assign('fid',$_GET['fid']);
	  $data1['fid'] = $data2['fid'] = $_GET['fid'];
	  $data1['source'] = $data2['a.source'] = $name;
	  if($_GET['toptid']){
		$this->assign('toptid',$_GET['toptid']);
		$data1['toptid']=$_GET['toptid'];
	  }
	  $count = $amodel->where($data1)->count();
	  if($count>0){
	    //创建分页对象
	    $listRows = '20';
	    $p = new \My\Page($count,$listRows);
		$pageno = $_GET['p'] ? $_GET['p'] : 1;
		$offset = ($pageno-1)*$page_size;
	    $list = $amodel->table('`'.C('DB_PREFIX').'flag_list` as a')->join('`'.C('DB_PREFIX').strtolower($name).'` as b on a.sourceid=b.id')->field($fields)->where($data2)->order('sort asc,sourceid desc')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $amodel->getlastsql();exit;
		$page = $p->Show();
		$this->assign('page',$page);
		$this->assign('list',$list);
	  }
	}
    $this->display();
  }

  /**
   * 保存排序
   */
  public function saveSort(){
	$fid = $_POST['flagid'];
	$dostr = $_POST['dostr'];
	$list = explode('#',$dostr);
	foreach($list as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }else{
	     $ar = explode(':',$val);
		 if($ar[1]==0){
		   $list2[] = $val;
		 }else{
		   $list1[$ar[1]] = $val;
		 }
	  }
	}
	ksort($list1);
	if($list1 && $list2){
	  $list1 = array_merge($list1,$list2);
	}elseif($list2){
	  $list1 = $list2;
	}
	//$model = D('product_flag_list');
	$name = CONTROLLER_NAME;
    $model = D('flag_list');
	$pmodel = D($name);
	$fmodel = D('Flag');
	$fdata['status'] = 1;
	$flags = $fmodel->where($fdata)->select();
	foreach($list1 as $val){
	  $ar = explode(':',$val);
	  if(!is_numeric($ar[0])){
	    continue;
	  }
	  //dump($ar);exit;
	  $wdata['fid'] = $fid;
	  $mdata['source'] = $wdata['source'] = $name;
	  $mmdata['id'] = $mdata['sourceid'] = $wdata['sourceid'] = $ar[0];
	  $sort = $data['sort'] = $ar[1];
	  if($sort==0){
	    $model->where($wdata)->delete();
	    $fids = $model->field('fid')->where($mdata)->select();
		foreach($fids as $k=>$v){
		  $fidss[] = $v['fid'];
		}
		$save['flags'] = implode(',',$fidss);
		if(!$fids)$save['flags']='';
		if(!$mmdata['id'])continue;
		$pmodel->where($mmdata)->save($save);
		continue;
	  }
	  $result = $model->where($wdata)->save($data);
	  //echo $model->getlastsql();
	  if($result){
	  }else{
		$adata['id'] = $ar[0];
		$vo = $amodel->where($adata)->find();
		$data1['fid'] = $fid;
		$data1['fname'] = $flags[$fid];
		$data1['sort'] = $sort;
		$data1['source'] = $name;
		$data1['sourceid'] = $ar[0];
		$id = $model->add($data1);
	  }
	}
	$keep = $flags[$_POST['flagid']]['keep'];
	$keep = $keep ? $keep : 100;
	//$this->delmoreflag($_POST['flagid'],$keep);
	$this->success ('修改成功!');
  }

  /**
   * 删除记录
   */
  public function foreverdelete() {
	  if($this->can_del){
		//删除指定记录
		$name=CONTROLLER_NAME;
		$model = D ($name);
		$this->assign('jumpUrl',__MODULE__.'/'.$name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			//$id = $_REQUEST [$pk];
			$id = $_REQUEST ['id'];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					$ids =  explode ( ',', $id );
					foreach($ids as $id){
					  $this->history($id);
					}
					if(method_exists($this,'GiveCache')){
					  $this->GiveCache();
					}
					$this->success ('删除成功！');
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	  }else{
	    $this->error('不可删除');
	  }
  }

  /**
   * 清楚记录
   */
  public function clear() {
	//删除指定记录
	$name=CONTROLLER_NAME;
	$model = D ($name);
	if (! empty ( $model )) {
		if (false !== $model->where ( 'status=1' )->delete ()) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( '_DELETE_SUCCESS_' ) );
		} else {
			$this->error ( L ( '_DELETE_FAIL_' ) );
		}
	}
	$this->forward ();
  }

  /**
   +----------------------------------------------------------
   * 默认禁用操作
   *
   +----------------------------------------------------------
   * @access public
   +----------------------------------------------------------
   * @return string
   +----------------------------------------------------------
   * @throws FcsException
   +----------------------------------------------------------
   */
  public function forbid() {
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_REQUEST [$pk];
	$condition = array ($pk => array ('in', $id ) );
	$list=$model->forbid ( $condition );
	if ($list!==false) {
		$ids =  explode ( ',', $id );
		foreach($ids as $id){
		  $this->history($id);
		}
		if(method_exists($this,'GiveCache')){
		  $this->GiveCache();
		}
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态禁用成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 状态批准
   */
  public function checkPass() {
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->checkPass( $condition )) {
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态批准成功！' );
	} else {
		$this->error  (  '状态批准失败！' );
	}
  }

  /**
   * 状态批准
   */
  public function recycle() {
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->recycle ( $condition )) {

		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态还原成功！' );

	} else {
		$this->error   (  '状态还原失败！' );
	}
  }

  /**
   * 带条件状态批准
   */
  public function recycleBin() {
	$map = $this->_search ();
	$map ['status'] = - 1;
	$name=CONTROLLER_NAME;
	$model = D ($name);
	if (! empty ( $model )) {
		$this->_list ( $model, $map );
	}
	$this->display ();
  }

  /**
   +----------------------------------------------------------
   * 默认恢复操作
   *
   +----------------------------------------------------------
   * @access public
   +----------------------------------------------------------
   * @return string
   +----------------------------------------------------------
   * @throws FcsException
   +----------------------------------------------------------
   */
  function resume() {
	//恢复指定记录
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->resume ( $condition )) {
		$ids =  explode ( ',', $id );
		foreach($ids as $id){
		  $this->history($id);
		}
		if(method_exists($this,'GiveCache')){
		  $this->GiveCache();
		}
		$this->assign ( "jumpUrl", $this->getReturnUrl () );
		$this->success ( '状态恢复成功！' );
	} else {
		//$model->getlastsql();exit;
		$this->error ( '状态恢复失败！' );
	}
  }

  /**
   * 菜单信息
   */
  public function menu() {
	//$this->checkUser();
	if(isset($_SESSION[C('USER_AUTH_KEY')])) {
	  if(!$_SESSION[C('SESSION_PREFIX').'menu_all']){
		  $num = ceil($_SESSION[C('USER_AUTH_KEY')]/100);
		  $root_dir = './cache/access/'.$num.'/';
		  if(is_file($root_dir.$_SESSION[C('USER_AUTH_KEY')].'.php')){
			//判断是否需要更新
			$update_time = file_get_contents('./cache/access/update_time.txt');
			//缓存时间对比
			if(filemtime('./cache/access/update_time.txt')>filemtime($root_dir.$_SESSION[C('USER_AUTH_KEY')].'.php')){
			  $menu1 = UserController::GiveCache($_SESSION[C('USER_AUTH_KEY')]);
			}else{
			  $menu1 = include $root_dir.$_SESSION[C('USER_AUTH_KEY')].'.php';
			}
		  }else{
			//import("Home.Controller.UserController");
			$menu1 = UserController::GiveCache($_SESSION[C('USER_AUTH_KEY')]);
			
		  }
		  $_SESSION[C('SESSION_PREFIX').'menu_all'] = $menu1;
	  }else{
		$menu1 = $_SESSION[C('SESSION_PREFIX').'menu_all'];
	  }
	}else{
	  $this->error('出错!');
	}
	//判断大分类是否展开
	//echo MODULE_NAME;
	foreach($menu1 as $key=>$child_munu){
	  foreach($child_munu['nlist'] as $val){
		if($val['name']==CONTROLLER_NAME){
		  $menu1[$key]['show'] = 1;
		  $nlist = $menu1[$key]['nlist'];
		  //$node_title = $val['title'];//节点名称
		  //$board_title = $menu1[$key]['cname'];//板块名称
		  $node_title = "<a href='__ROOT__/index.php?c={$val[name]}{$val[param_str]}' >".$val['title'].'</a>';//节点名称
		  $board_title = "<a href='__ROOT__/index.php?c={$menu1[$key][nlist][0][name]}{$menu1[$key][nlist][0][param_str]}' >".$menu1[$key]['cname'].'</a>';//板块名称
		}			    
	  }
	}
	$this->assign('node_title',$node_title);
	$this->assign('board_title',$board_title);
	$this->assign('left_nlist',$nlist);
	$this->assign('menu1',$menu1);
  }

  /**
   * 用户检测
   */
  protected function checkUser() {
	if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
		//$this->assign('jumpUrl',__MODULE__.'/Public/login');
		//$this->error('没有登录');
	}
  }

  /**
   * flag获取
   */
  protected function get_flags(){
	$model = D('Flag');
	$flags = $model->where('status=1')->order('id desc')->select();
	return $flags;
  }

  /**
   * 根据模块获得属性标签
   */
  protected function get_moudel_flags($moudel){
	$moudel = $moudel ? $moudel : CONTROLLER_NAME;
	$model = D('flag_module');
	$data['moudel_name'] = $moudel;
	$data['status'] = 1;
	$flags = $model->where($data)->select();
	foreach($flags as $key=>$val){
	  $key  = $val['fid'];
	  $flags2[$key]['name'] = $val['fname'];
	  $flags2[$key]['id'] = $val['fid'];
	}
	return $flags2;
  }

  /**
   * 删除多于信息
   */
  protected function delmoreflag($table_name,$fid,$keep,$data=array()){
	if(!$fid){
	  return false;
	}
	$model = D($table_name);
	$data['fid'] = $fid;
	$data['webset'] = $_SESSION['ac_site'];
	$count = $model->where($data)->count();
	//echo $model->getlastsql();exit;
	if($count<=$keep){
	  return false;
	}
	$del_num = $count-$keep>0 ? $count-$keep : 0;
	$model->where($data)->order('sort desc,sourceid asc')->limit($del_num)->delete();
	$sql = 'OPTIMIZE TABLE `137`'.$table_name.'';
	$model->query($sql);
  }

  /**
   * 历史操作记录
   */
  public function history($id,$action='',$model='',$info=''){
	$m = D('History');
	$data['model'] = $model ? $model : CONTROLLER_NAME;
	$data['action'] = $action ? $action : ACTION_NAME;
	$data['uid'] = $_SESSION[C('USER_AUTH_KEY')];
	$data['username'] = $_SESSION['nickname'];
	$data['create_time'] = time();
	$data['sourceid'] = $id;
	//记录详细更新内容
	if(!$info){
	  $map = array ();
	  $model = D($data['model']);
	  $dbFields = $model->getDbFields ();
	  foreach ( $dbFields as $key => $val ) {
		if (isset ( $_POST [$val] ) && $_POST [$val] != '') {
			//$this->assign($val,$_POST [$val]);
			$map [$val] = $_POST [$val];
		}
	  }	
	}else{
	  $map = $info;
	}
	//flags处理
	if(!$dbFields['flags']){
	  unset($_POST['flags']);
	}
	//$data['info'] = serialize($map);
	$data['info'] = serialize($_POST);
	$m->add($data);
  }

    // 文件上传
    protected function upload2($options) {
        import('@.ORG.UploadFile');
        //导入上传类
        $upload = new \UploadFile();
        //设置上传文件大小
        $upload->maxSize            = 3292200;
        //设置上传文件类型
        $upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath           = $options['savePath'] ?  $options['savePath'] : C('IMG_ROOT');
        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb              = $options['thumb'] ?  $options['thumb'] : C('thumb');

        // 设置引用图片类库包路径
        $upload->imageClassPath     = '@.ORG.Image';
        //设置需要生成缩略图的文件后缀
        $upload->thumbPrefix        = $options['thumbPrefix'] ?  $options['thumbPrefix'] : C('thumbPrefix');  //生产2张缩略图
        //设置缩略图最大宽度
        $upload->thumbMaxWidth      = $options['thumbMaxWidth'] ?  $options['thumbMaxWidth'] : C('thumbMaxWidth');
        //设置缩略图最大高度
        $upload->thumbMaxHeight     = $options['thumbMaxHeight'] ?  $options['thumbMaxHeight'] : C('thumbMaxHeight');
        //设置上传文件规则
        $upload->saveRule           = 'uniqid';
        //删除原图
        $upload->thumbRemoveOrigin  = true;
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $info = $upload->getUploadFileInfo();
			//dump($info);exit;
			if($info!=""){
			  $result['data'] = $info;
			  foreach($info as $file){
				$key = $file['key'];
				$_POST[$key] = C('IMG_URL').$file['savename'];
				$result['url'][$key] = $_POST[$key];
				$result['info'][$key] = $file;
			  }
			}else{
			  $result = '';
			}
			/*
            import('@.ORG.Image');
            //给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
            \Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
            $_POST['image'] = $uploadList[0]['savename'];
			*/
        }

        return $result;
    }

  /**
   * 上传图片
   */
  protected function upload($options){
	//$ftp_config = include C("ROOT_SITE_DIR").'config/Ftp_config.php';
	$config['maxSize'] = 1024*1024*2;
	$config['allowExts'] = array('jpg','gif','png','jpeg','swf');
	$config['saveRule'] = 'uniqid';
	$config['rootPath'] = C('IMG_ROOT');
	//$options['rootPath'] = '/git/vshop/img0/'; //FTP相对路径
	$config['savePath'] = '';
	$config['subName'] = date('Y/m/d');
	//$config['driver'] = C('UP_DRIVER');
	$config['thumb'] = C('thumb');
	$config['thumbPrefix'] = C('thumbPrefix');  //生产2张缩略图
	$config['thumbMaxWidth'] = C('thumbMaxWidth');
	$config['thumbMaxHeight'] = C('thumbMaxHeight');
	if($options){
		$config = array_merge($config,$options);
	}
	//$upload = new \Think\Upload($config,'FTP',include C("ROOT_SITE_DIR").'config/Ftp_config.php');
	$upload = new \Think\Upload($config);
	$info = $upload->upload();
	//dump($info);exit;
	if($info){
	  $result['data'] = $info;
	  foreach($info as $file){
		$key = $file['key'];
		$_POST[$key] = C('IMG_URL').$file['savepath'].$file['savename'];
		$result['url'][$key] = $_POST[$key];
		$result['info'][$key] = $file;
	  }
	}else{
	  //dump($upload->getError());exit;
	  $result = '';
	}
	return $result;
  }

  /**
   * 字段值修改
   */
  function field_value_update(){
	if(!$this->up_fields){
	  ajaxErrReturn('请先设置可以修改字段');
	}
	if(!in_array($_POST['field'],$this->up_fields)){
	  ajaxErrReturn('非可修改字段');
	}
	$model = D(CONTROLLER_NAME);
	$data['id'] = $_POST['id'];
	$field = $_POST['field'];
	$vo = $model->where($data)->find();
	$sdata[$field] = $_POST['val'];
	$result = $model->where($data)->save($sdata);    
	if($result){
	  ajaxSucReturn('修改成功');
	}else{
	  ajaxErrReturn('修改失败');
	}

  }

}
?>