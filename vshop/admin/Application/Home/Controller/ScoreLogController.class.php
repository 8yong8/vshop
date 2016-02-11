<?php 
namespace Home\Controller;
use Think\Controller;
class ScoreLogController extends CommonController{

  /**
   * 添加信息
   */
  public function add(){
    if(IS_POST){
	  //dump($_POST);exit;
	  $model = M('Member');
	  $m_data['id'] = $_POST['member_id'];
	  $member = $model->field('id,username')->where($m_data)->find();
	  if(!$member){
	    $this->error('会员不存在');
	  }
	  $model = D('ScoreLog');
	  $_POST['member_name'] = $member['username'];
	  if (false === $model->create ()) {
			$this->error ( $model->getError () );
	  }
	  /*
	  $add_data['member_id'] = $member['id'];
	  $add_data['member_name'] = $member['username'];
	  $add_data['num'] = $member['num'];
	  $add_data['source'] = 'system';
	  $add_data['desc'] = $_POST['desc'];
	  $add_data['create_time'] = time();
	  */
	  $result = $model->add();
	  if($result){
	    $this->update_member_source($member['id']);
		$this->success('添加成功');
	  }else{
	    $this->error('添加失败');
	  }
	  exit;
	}
	$this->display();
  }

  /**
   * 更新会员积分
   */
  protected function update_member_source($member_id){
    $model = M('ScoreLog');
	$data['member_id'] = $member_id;
	$score = $model->where($data)->sum('score');
	$model = M('MemberWallet');
	$wdata['member_id'] = $member_id;
	$count = $model->where($wdata)->count();
	if($count>0){
	  $sdata['score'] = $score;
	  $rsult = $model->where($wdata)->save($sdata);	
	}else{
	  $wdata['score'] = $score;
	  $rsult = $model->add($wdata);		
	}
  }
} 
?>