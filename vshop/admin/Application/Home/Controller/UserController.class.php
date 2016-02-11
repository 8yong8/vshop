<?php 
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController{

  public function _before_index(){
    $_REQUEST ['listRows'] = 50;
  }

  /**
   * 搜索条件
   */
  public function _search(){
	if($_GET['nickname']){
	  $data['nickname'] = array('like','%'.$_GET['nickname'].'%');
	  $this->assign('nickname',$_GET['nickname']);
	}
	if($_GET['webset']){
	  $data['ac_sites'] = array('like','%'.$_GET['webset'].'%');
	  $this->assign('webset',$_GET['webset']);
	}
    return $data;
  }

  /**
   * 添加管理员
   */
  public function add() {
	  //$uid = uc_user_register($_POST['username'], $_POST['password'],$_POST['email']);
	  if($_POST){
		if(!$_POST['password']){
		  $this->error('密码必须!');
		}
		$_POST['username'] = $_POST['account'];
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['createname'] = $_SESSION['nickname'];
		$_POST['password'] = md5($_POST['password']);
		/*
		//会员添加
		$mmodel = M('Member');
		if (false === $mmodel->create ()) {
			$this->error ( $mmodel->getError () );
		}
		$list = $mmodel->add ();
		*/
		$model = M('User');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//管理员添加
		$list = $model->add ();
		if ($list!==false) { //保存成功
		    //$this->history($list);
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}	
		if ($list!==false) { //保存成功
		    //$this->history($list);
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
   * 重置密码
   */
  public function resetPwd(){
    $model = D('User');
	$id = $_POST['id'];
	if($id<1)return false;
	$vo = $model->field('id,password')->where('id='.$id)->find();
	$data['password'] = md5($_POST['password']);
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
	if(!preg_match('/^[a-z]\w{2,}$/i',$_POST['account'])) {
		echo '用户名必须是字母，且3位或3位以上！' ;exit;
	}
	$User = M("User");
	// 检测用户名是否冲突
	$name  =  $_REQUEST['account'];
	$result  =  $User->getByAccount($name);
	if($result) {
		echo '该用户名已经存在！' ;exit;
		$this->error(iconv("GBK", "UTF-8", '该用户名已经存在！' ));
	}else {
		echo '该用户名可以使用！' ;exit;
		$this->success(iconv("GBK", "UTF-8", '该用户名可以使用！' ));
	}
  }

  /**
   * 查看权限
   */
  public function access_view(){
    $model = D('Role_user');
	$data['user_id'] = $_POST['uid'];
	$list = $model->field('a.role_id,b.name as group_name,c.node_id,d.name,d.title')->table("`".C('DB_PREFIX')."role_user` as a")->join("`".C('DB_PREFIX')."role` as b on a.	role_id=b.id")->join("`".C('DB_PREFIX')."access` as c on a.	role_id=c.role_id")->join("`".C('DB_PREFIX')."node` as d on c.	node_id=d.id")->where($data)->order('a.role_id asc,d.sort asc')->select();
	$this->assign('list',$list);
	$this->display();
  }

  /**
   * 更新全部菜单
   */
  public function update_all(){
    $model = D('user');
	$data['status'] = 1;
    $list = $model->field('id')->where($data)->select();
	foreach($list as $val){
	  $userid = $val['id'];
	  $num = ceil($userid/100);
	  $root_dir = './cache/access/'.$num.'/';
	  unlink($root_dir.$userid.'.php');
	  //$this->update_menu($val['id']);
	}
  }

  /**
   * 菜单更新
   */
  public function update_menu($userid){
	if($_GET['uid']){
	  $userid = $_GET['uid'];
	}
	$num = ceil($userid/100);
	$root_dir = './cache/access/'.$num.'/';
	$this->GiveCache($userid);
	if(is_file($root_dir.$userid.'.php')){
	  $this->success ('更新成功!');
	}else{
	  $this->error ('更新失败!');
	}
  }

  /**
   * 更新导航
   */
  public function GiveCache($userid){
	$model = D('Class_node');
    $table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'),'node'=>C('RBAC_NODE_TABLE'));
	if($userid!=1){
      $sql    =   "select node.id,node.name from ".
                    $table['role']." as role,".
                    $table['user']." as user,".
                    $table['access']." as access ,".
                    $table['node']." as node ".
                    "where user.user_id='{$userid}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=1 and node.status=1";
	}else{
      $sql    =   "select node.id,node.name from ".
                    $table['role']." as role,".
                    $table['user']." as user,".
                    $table['access']." as access ,".
                    $table['node']." as node ".
                    "where user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=1 and node.status=1";	
	}
    $apps =   $model->query($sql);
	$access =  array();
	foreach($apps as $key=>$app) {
		$appId	=	$app['id'];
		$appName	 =	 $app['name'];
		// 按排序读取项目的模块权限
		$access[strtoupper($appName)]   =  array();
		if($userid!=1){
		$sql    =   "select node.id,node.name,node.title from ".
				$table['role']." as role,".
				$table['user']." as user,".
				$table['access']." as access ,".
				$table['node']." as node ".
				"where user.user_id='{$userid}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=2 and node.pid={$appId} and node.status=1";
		}else{
		$sql    =   "select id,name,title from ".
				$table['node']." as node ".
				"where node.level=2 and node.pid={$appId} and node.status=1";		
		}
		$modules =   $model->query($sql);
	}
	//dump($modules);exit;
	//模块所在板块
	foreach($modules as $module){
	  /*
	  if($module['name']!='Index' && $module['name']!='Public'){
	    $ids[] = $module['id'];
	  }
	  */
	  if($module['name']!='Public'){
	    $ids[] = $module['id'];
	  }
	}
	$idsstr = implode(',',$ids);
	$bk_data['a.nid'] = array('in',$ids);
	$Model = M('Class_node as a');
	$bks = $Model->field('a.*,a.nname as title,b.sort as bk_sort,c.id,c.name,c.sort')->join('`'.C('DB_PREFIX').'class` as b ON a.cid = b.id')->join('`'.C('DB_PREFIX').'node` as c on a.nid=c.id')->where($bk_data)->order('c.sort asc')->select();
	//echo 1122;exit;
	foreach($bks as $bk){
	  $key = $bk['bk_sort'];
	  $menu[$key]['cid'] = $bk['cid'];
	  $menu[$key]['cname'] = $bk['cname'];
	  //$menu[$key]['nlist'][] = $bk;
	  $node['title'] = $bk['nname'];
	  $node['name'] = $bk['name'];
	  $node['id'] = $bk['id'];
	  //参数处理
	  if($bk['params']){
		  $params = unserialize($bk['params']);
		  $param_str = http_build_query($params);
		  $node['param_str'] = '&'.$param_str;
	  }else{
		  $node['param_str'] = '';
	  }
	  $node['access'] = 1;
	  $menu[$key]['nlist'][] = $node;
	}
	//dump($menu);exit;
	//echo $model->getlastsql();
	ksort($menu);
	$num = ceil($userid/100);
	$root_dir = './cache/access/'.$num.'/';
	mk_dir($root_dir);
	unlink($root_dir.$userid.'.php');
	//F($userid,$menu,$root_dir);
    return $menu;
  }
} 
?>