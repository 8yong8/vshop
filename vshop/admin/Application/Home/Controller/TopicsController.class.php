<?php 
namespace Home\Controller;
use Think\Controller;
class TopicsController extends CommonController {
  
  public function _before_index(){
	 
  }

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['id']!=""){
      $data['id'] = $_GET['sid'];
	  $this->assign("id",       $_GET['id']);
	}
	if($_GET['title']!=""){
      $data['title'] = array('like','%'.$_GET['title'].'%');
	  $this->assign("title",       $_GET['title']);
	}
	return $data;
  }

  /**
   * 添加信息 前置
   */
  function _before_add(){
	   if($_POST){
		 $_POST['starttime'] = strtotime($_POST['starttime']);
		 $_POST['endtime'] = strtotime($_POST['endtime']);
		//拍卖者处理
		if($_POST['member_id']){
		  $m_model = M('member');
		  $m_data['id'] = $_POST['member_id'];
		  $member = $m_model->field('id,username,realname')->where($m_data)->find();
		  if(!$member){
			$this->error('发布人不存在');
		  }
		  $_POST['member_name'] = $member['username'];
		  $_POST['realname'] = $member['realname'];
		}else{
		  $_POST['member_name'] = C('company_name');
		  $_POST['realname'] = C('company_name');
		}
	   }else{
		 $flags = $this->get_flags();
		 $this->assign('flags',$flags);   
	   }
  }

  /**
   * 编辑信息 前置
   */
  function _before_edit(){
	   if($_POST){
		 $_POST['starttime'] = strtotime($_POST['starttime']);
		 $_POST['endtime'] = strtotime($_POST['endtime']);
		//拍卖者处理
		if($_POST['member_id']){
		  $m_model = M('member');
		  $m_data['id'] = $_POST['member_id'];
		  $member = $m_model->field('id,username,realname')->where($m_data)->find();
		  if(!$member){
			$this->error('发布人不存在');
		  }
		  $_POST['member_name'] = $member['username'];
		  $_POST['realname'] = $member['realname'];
		}else{
		  $_POST['member_name'] = C('company_name');
		  $_POST['realname'] = C('company_name');
		}
	   }else{
		 $flags = $this->get_flags();
		 $this->assign('flags',$flags);   
	   }
  }

  /**
   * 生成缓存
   */
  function GiveCache(){

  }

}
?>