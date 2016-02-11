<?php
namespace Home\Controller;
use Think\Controller;
// 角色模块
class RoleController extends CommonController {
	function _filter(&$map){
		$map['name'] = array('like',"%".$_POST['name']."%");
	}
     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setApp()
    {
		//dump($_POST);exit;
        $id     = $_POST['groupAppId'];
		$groupId	=	$_POST['groupId'];
		$group    =   D('Role');
		$group->delGroupApp($groupId);
		$result = $group->setGroupApps($groupId,$id);
		
		if($result===false) {
			$msg['error_code'] = 1;
			$msg['notice'] = '项目授权失败！';
			echo json_encode($msg);exit;
			$this->error('项目授权失败！' );
		}else {
			$msg['error_code'] = 0;
			$msg['notice'] = '项目授权成功！';
			echo json_encode($msg);exit;
			$this->success('项目授权成功！');
		}
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function app()
    {
        //读取系统的项目列表
        $node    =  D("Node");
        $list	=	$node->where('level=1')->field('id,title')->order('sort asc')->select();
		foreach ($list as $vo){
			$appList[$vo['id']]	=	$vo['title'];
		}
        //读取系统组列表
		$group   =  D('Role');
        $list       =  $group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);
        //获取当前用户组项目权限信息
        $groupId =  isset($_GET['groupId'])?$_GET['groupId']:'';
		$groupAppList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的操作权限列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$groupAppList[$vo['id']]	=	$vo['id'];
			}
		}
		$this->assign('groupAppList',$groupAppList);
        $this->assign('appList',$appList);
        $this->display();

        return;
    }

     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setModule()
    {
        $id     = $_POST['groupModuleId'];
		$groupId	=	$_POST['groupId'];
        $appId	=	$_POST['appId'];
		$group    =   D("Role");
		$group->delGroupModule($groupId,$appId);
		$result = $group->setGroupModules($groupId,$id);
		if($result===false) {
			$msg['error_code'] = 1;
			$msg['notice'] = '模块授权失败！';
			echo json_encode($msg);exit;
			//$this->error('模块授权失败！');
		}else {
			$msg['error_code'] = 0;
			$msg['notice'] = '模块授权成功！';
			echo json_encode($msg);exit;
			//$this->success('模块授权成功！');
		}
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function module()
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];

		$group   =  D("Role");
        //读取系统组列表
        $list = $group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		//dump($groupList);
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        $node    =  D("Node");
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的模块列表
			$where['level']=2;
			$where['pid']=$appId;
			$where['status'] = 1;
            $nodelist=$node->field('id,title')->order('sort asc')->where($where)->select();
			foreach ($nodelist as $vo){
				$moduleList[$vo['id']]	=	$vo['title'];
			}
        }

        //获取当前项目的授权模块信息
		$groupModuleList = array();
		if(!empty($groupId) && !empty($appId)) {
            $grouplist	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($grouplist as $vo){
				$groupModuleList[$vo['id']]	=	$vo['id'];
			}
		}

		$this->assign('groupModuleList',$groupModuleList);
        $this->assign('moduleList',$moduleList);

        $this->display();

        return;
    }

     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setAction()
    {
        $id     = $_POST['groupActionId'];
		$groupId	=	$_POST['groupId'];
        $moduleId	=	$_POST['moduleId'];
		$group    =   D("Role");
		$group->delGroupAction($groupId,$moduleId);
		$result = $group->setGroupActions($groupId,$id);

		if($result===false) {
			$msg['error_code'] = 1;
			$msg['notice'] = '操作授权失败！';
			echo json_encode($msg);exit;
			//$this->error('操作授权失败！' );
		}else {
			$msg['error_code'] = 0;
			$msg['notice'] = '操作授权成功！';
			echo json_encode($msg);exit;
			//$this->success('操作授权成功！');
		}
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function action()
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];
        $moduleId  = $_GET['moduleId'];

		$group   =  D("Role");
        //读取系统组列表
        $grouplist=$group->field('id,name')->select();
		foreach ($grouplist as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的授权模块列表
            $list	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($list as $vo){
				$moduleList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("moduleList",$moduleList);
        }
        $node    =  D("Node");
        if(!empty($moduleId)) {
            $this->assign("selectModuleId",$moduleId);
        	//读取当前项目的操作列表
			$map['level']=3;
			$map['pid']=$moduleId;
            $list	=	$node->where($map)->field('id,title')->order('sort asc')->select();
			if($list) {
				foreach ($list as $vo){
					$actionList[$vo['id']]	=	$vo['title'];
				}
			}
        }


        //获取当前用户组操作权限信息
		$groupActionList = array();
		if(!empty($groupId) && !empty($moduleId)) {
			//获取当前组的操作权限列表
            $list	=	$group->getGroupActionList($groupId,$moduleId);
			if($list) {
			foreach ($list as $vo){
				$groupActionList[$vo['id']]	=	$vo['id'];
			}
			}

		}

		$this->assign('groupActionList',$groupActionList);
		//$actionList = array_diff_key($actionList,$groupActionList);
        $this->assign('actionList',$actionList);

        $this->display();

        return;
    }

    /**
     +----------------------------------------------------------
     * 增加组操作权限
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setUser()
    {
        $id     = $_POST['groupUserId'];
		$groupId	=	$_POST['groupId'];
		$group    =   D("Role");
		$group->delGroupUser($groupId);
		$result = $group->setGroupUsers($groupId,$id);
		if($result===false) {
			ajaxErrReturn('授权失败！' );
		}else {
		    ajaxSucReturn('授权成功！' );
		}
    }

    /**
     +----------------------------------------------------------
     * 组操作权限列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function user()
    {
        //读取系统的用户列表
        $user    =   D("User");
		$data['status'] = 1;
		if($_GET['h']==1){
		  $data['b.role_id'] = $_GET['id'];
		}else if($_GET['h']==2){
		  //$data['b.role_id'] = array('neq',$_GET['id']);
		  $data = "( `status` = 1 ) AND ( b.role_id != '1' or b.role_id is null )";
		}
		$list2=$user->table('`'.C('DB_PREFIX').'user` as a')->field('id,account,nickname')->join('`'.C('DB_PREFIX').'role_user` as b on a.id=b.user_id')->where($data)->order('id desc')->select();
		//echo $user->getlastsql();exit;
		//dump($list2);
		foreach ($list2 as $vo){
			$userList[$vo['id']]	=	$vo['account'].' '.$vo['nickname'];
		}
		$group    =   D("Role");
        $list=$group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        //获取当前用户组信息
        $groupId =  isset($_GET['id'])?$_GET['id']:'';
		$groupUserList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的用户列表
            $list	=	$group->getGroupUserList($groupId);
			foreach ($list as $vo){
				$groupUserList[$vo['id']]	=	$vo['id'];
			}
		}
		$this->assign('groupUserList',$groupUserList);
        $this->assign('userList',$userList);
        $this->display();

        return;
    }
	public function _before_edit(){
	   $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);

	}
	public function _before_add(){
	  if(!$_POST){
	   $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);
	  }
	}
    public function select()
    {
        $map = $this->_search();
        //创建数据对象
        $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);
        $this->display();
        return;
    }

	public function site(){
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];
        $moduleId  = $_GET['moduleId'];

		$group   =  D("Role");
        //读取系统组列表
        $grouplist=$group->field('id,name')->select();
		foreach ($grouplist as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的授权模块列表
            $list	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($list as $vo){
				$moduleList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("moduleList",$moduleList);
        }
        $node    =  D("Node");
        if(!empty($moduleId)) {
            $this->assign("selectModuleId",$moduleId);
        	//读取当前项目的操作列表
			$map['level']=3;
			$map['pid']=$moduleId;
            $list	=	$node->where($map)->field('id,title')->order('sort asc')->select();
			if($list) {
				foreach ($list as $vo){
					$actionList[$vo['id']]	=	$vo['title'];
				}
			}
        }


        //获取当前用户组操作权限信息
		$groupActionList = array();
		if(!empty($groupId) && !empty($moduleId)) {
			//获取当前组的操作权限列表
            $list	=	$group->getGroupActionList($groupId,$moduleId);
			if($list) {
			foreach ($list as $vo){
				$groupActionList[$vo['id']]	=	$vo['id'];
			}
			}

		}

		$this->assign('groupActionList',$groupActionList);
		//$actionList = array_diff_key($actionList,$groupActionList);
        $this->assign('actionList',$actionList);

        $this->display();

        return;
	}

  public function usersearch(){
    $model = D('user');
	$keys = explode(',',$_REQUEST['key']);
	$str = ' status=1 and (';
	foreach($keys as $y=>$kk){
	  if($y!=count($keys)-1){
	    $str .= " nickname like '%".$kk."%' or ";
	  }else{
	    $str .= " nickname like '%".$kk."%'";
	  }
	}
	$str .= ')';
	//$data = '';
	$list1 = $model->where($str)->select();
	//echo $model->getlastsql();exit;
	$data1['role_id'] = $_REQUEST['gr'];
	$list2 = $model->field('a.*')->table('`'.C('DB_PREFIX').'user` as a')->join('`137_role_user` as b on a.id=b.user_id')->where($data1)->select();
	//echo $model->getlastsql();
	if($list2){
	  $list1 = array_merge($list1,$list2);
	  //$list = array_unique($list);
	}
	foreach($list1 as $k1 => $v1){
		$list4[$v1['id']] = $v1;
	}
	foreach($list4 as $k1 => $v1){
		$list[] = $v1;
	}
	//dump($list3);exit;
	$group = D("Role");
	$list3 = $group->getGroupUserList($_POST['gr']);
	foreach ($list3 as $vo){
		$groupUserList[$vo['id']]	=	$vo['id'];
	}
	foreach($list as $k=>$v){
	  $list[$k]['nickname'] = $v['nickname'];
	  $key = $v['id'];
	  if(!empty($groupUserList) && ($groupUserList == $key || in_array($key,$groupUserList))) {
	    $list[$k]['check'] = 1;
	  }else{
	    $list[$k]['check'] = 0;
	  }
	}
	echo json_encode($list);
  
  }


}
?>