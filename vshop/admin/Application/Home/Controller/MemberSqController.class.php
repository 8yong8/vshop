<?php
namespace Home\Controller;
use Think\Controller;
class MemberSqController extends CommonController {

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['id']){
	  $data['id'] = $_GET['id'];
	  $this->assign('id',$_GET['id']);
	}
	if($_GET['username']){
	  $data['member_name'] = $_GET['username'];
	  $this->assign('username',$_GET['username']);
	}
    return $data;
  }

  /**
   * 显示
   */
  public function show(){
    $model = M('MemberSq');
    $data['c.id'] = $_GET['id'];
	$vo = $model->table('`'.C('DB_PREFIX').'member` as a')->join('`'.C('DB_PREFIX').'member_msg` as b on a.id=b.member_id')->join('`'.C('DB_PREFIX').'member_sq` as c on a.id=c.member_id')->field('a.*,b.*,c.id as id,c.status as status,c.remark,c.tel')->where($data)->find();
	//dump($vo);exit;
	$this->assign('vo',$vo);
	$this->display();
  }

  /**
   * 编辑信息
   */
  public function sh_edit(){
    $model = M('MemberSq');
	$data['id'] = $_POST['id'];
	$vo = $model->where($data)->find();
	if($vo['status']!=0){
	  //$this->error('已审核过');exit;
	}
	if (false === $model->create ()) {
	  $this->error ( $model->getError () );
	}
	$result = $model->save();
	//echo $model->getlastsql();exit;
	if($result && $_POST['status']==1){
	  $model = M('member');
	  $mdata['id'] = $vo['member_id'];
	  $msdata['utype'] = $vo['type'];
	  $msdata['bus_lv'] = 0;
	  $msdata['bus_lv_name'] = '普通';
	  $result = $model->where($mdata)->save($msdata);
	  //echo $model->getlastsql();exit;
	}
	if (false !== $result) {
	  $this->success ('操作成功!');
	} else {
	  //错误提示
	  $this->error ('操作失败!');
	}
  }


}
?>