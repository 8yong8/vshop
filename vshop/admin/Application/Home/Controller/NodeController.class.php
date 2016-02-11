<?php
namespace Home\Controller;
use Think\Controller;
class NodeController extends CommonController {
	public function _filter(&$map)
	{
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		if($_GET['pid']!=''){
			$map['pid']=$_GET['pid'];
		}
		$_SESSION['currentNodeId']	=	$map['pid'];
		//获取上级节点
		$node  = M("Node");
        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {
                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}

	public function _before_index() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->getField('id,title');
		$this->assign('groupList',$list);
	}

	// 获取配置类型
	public function _before_add() {
	  if(!IS_POST){
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
	  }
	}

    public function _before_patch() {
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	M("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }

	public function _before_edit() {
	  if(!IS_POST){
		$model	=	M("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
	  }
	}

	/*
	public function _after_edit(){
	  $model = M('Class_node');
	  $wdata['nid'] = $_POST['id'];
	  $sdata['nname'] = $_POST['title'];
	  $model->where($wdata)->save($sdata);
	}
	*/

    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = M('Node');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentNodeId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
			//echo $node->getlastsql();
        }
        $this->assign("sortList",$sortList);
		$this->assign('node_count',count($sortList));
        $this->display();
        return ;
    }

	/**
	 * 排序
	 */
    function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			//更新数据对象
		$name = CONTROLLER_NAME;
		$model = D ($name);
			$col = explode ( ',', $seqNoList );
			//启动事务
			$model->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$model->id = $val [0];
				$model->sort = $val [1];
				$result = $model->save ();
				/*if (! $result) {
					break;
				}
				echo $model->getlastsql().'<br>';*/
			}
			//提交事务
			$model->commit ();
			if ($result!==false) {
				//采用普通方式跳转刷新页面
				//$this->success ('更新成功');
				$msg['error_code'] = 0;
				$msg['notice'] = '更新成功';
				echo json_encode($msg);exit;
			} else {
				//$this->error ( $model->getError () );
				$msg['error_code'] = 0;
				$msg['notice'] = '更新成功';
				echo json_encode($msg);exit;
			}
		}
	}

  /**
   * 生成缓存
   */
  function GiveCache($status=1){
	  $model = D('Node');
	  $data1['level'] = 2;
	  $data1['status'] = 1;
	  $list = $model->where($data1)->order('sort asc')->select();
	  foreach($list as $array){
	    $data[$array['id']] = $array;
	  }

	  mk_dir('./cache/node/');
	  F('list',$data,'./cache/node/');
	  //CacheAction::set('node',$data);
	  //$this->assign('jumpUrl',__CONTROLLER__);
	  file_put_contents('./cache/access/update_time.txt',time());
	  if($status==1){
		$this->assign('jumpUrl',__CONTROLLER__);
	    $this->success ('生成缓存完毕!');
	  }
  }

}
?>