<?php 
namespace Home\Controller;
use Think\Controller;
class ConsigneeController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('MemberAddress');
  }

  /**
   * 列表页
   */
  function index() {
	//$model = M('MemberAddress');
	//获取全部收货人信息
	$data['member_id'] = $this->user['id'];
	$list = $this->db->where($data)->select();
	$this->assign('pageno',$pageno);
	$this->assign('list',$list);
	$this->assign('page_count',$page_count);
	$this->assign('from_url',urldecode($_GET['from_url']));
	$this->assign('headerTitle','Wap地址管理页');
	$this->assign('headerKeywords','Wap地址管理页');
	$this->assign('headerDescription','Wap地址管理页');
	$this->display();
  }

  /**
   * 添加
   */
  function add(){
    if(IS_POST){
	  $pvs = getCache('Region:pvs');
	  $cities = getCache('Region:cities');
	  $counties = getCache('Region:counties');
	  //获取全部收货人信息
	  $sdata['member_id'] = $this->user['id'];
	  $sdata['name'] = $_POST['name'];
	  $sdata['mobile'] = $_POST['mobile'];
	  $sdata['pv_id'] = $_POST['pv_id'];
	  $sdata['ct_id'] = $_POST['ct_id'];
	  $sdata['dist_id'] = $_POST['dist_id'];
	  $sdata['province'] = $pvs[$_POST['pv_id']]['area_name'];
	  $sdata['city'] = $cities[$_POST['ct_id']]['area_name'];
	  $sdata['district'] = $counties[$_POST['dist_id']]['area_name'];
	  $sdata['addr'] = $_POST['addr'];
	  $sdata['zip_code'] = $_POST['zip_code'];
	  $sdata['default'] = $_POST['default'] ? $_POST['default'] : 0;
	  $result = $this->db->add($sdata);
	  if($result){
		if($_POST['default']==1){
		  $wdata['id'] = array('neq',$result);
		  $sdata2['default'] = 0;
		  $this->db->where($wdata)->save($sdata2);
		}
		$msg['error_code'] = 0;
		$msg['notice'] = '添加成功';
		if($_POST['from_url']){
		  $msg['gourl'] = resetUrl($_POST['from_url'],'consignee_id').'&consignee_id='.$result;
		}else{
		  $msg['gourl'] = __CONTROLLER__;
		}
		ajaxSucReturn($msg);
	  }else{
		//ajaxErrReturn($model->getlastsql());
	    ajaxErrReturn('添加失败');
	  }
	}else{

	  $this->assign('from_url',urldecode($_GET['from_url']));
	  $this->assign('headerTitle','Wap地址添加');
	  $this->assign('headerKeywords','Wap地址添加');
	  $this->assign('headerDescription','Wap地址添加');
	  $this->display();
    }
  }

  /**
   * 编辑
   */
  function edit(){
    if(IS_POST){
 	  //$model = M('MemberAddress');
	  $pvs = getCache('Region:pvs');
	  $cities = getCache('Region:cities');
	  $counties = getCache('Region:counties');
	  //获取全部收货人信息
	  $wdata['member_id'] = $this->user['id'];
	  $wdata['id'] = $_POST['id'];
	  $sdata['name'] = $_POST['name'];
	  $sdata['mobile'] = $_POST['mobile'];
	  $sdata['pv_id'] = $_POST['pv_id'];
	  $sdata['ct_id'] = $_POST['ct_id'];
	  $sdata['dist_id'] = $_POST['dist_id'];
	  $sdata['province'] = $pvs[$_POST['pv_id']]['area_name'];
	  $sdata['city'] = $cities[$_POST['ct_id']]['area_name'];
	  $sdata['district'] = $counties[$_POST['dist_id']]['area_name'];
	  $sdata['addr'] = $_POST['addr'];
	  $sdata['zip_code'] = $_POST['zip_code'];
	  $sdata['default'] = $_POST['default'];
	  $result = $this->db->where($wdata)->save($sdata);
	  if($result){
		if($_POST['default']==1){
		  $wdata['id'] = array('neq',$_POST['id']);
		  $sdata2['default'] = 0;
		  $this->db->where($wdata)->save($sdata2);
		}
		$msg['error_code'] = 0;
		$msg['notice'] = '编辑成功';
		if($_POST['from_url']){
			$msg['gourl'] = resetUrl($_POST['from_url'],'consignee_id').'&consignee_id='.$_POST['id'];
		}
		ajaxSucReturn($msg);
	  }else{
		//ajaxErrReturn($this->db->getlastsql());
	    ajaxErrReturn('编辑失败');
	  }
	}else{
	  //$model = M('MemberAddress');
	  //获取全部收货人信息
	  $data['member_id'] = $this->user['id'];
	  $data['id'] = $_GET['id'];
	  $vo = $this->db->where($data)->find();
	  $this->assign('vo',$vo);
	  $this->assign('from_url',urldecode($_GET['from_url']));
	  $this->assign('headerTitle','Wap地址修改');
	  $this->assign('headerKeywords','Wap地址修改');
	  $this->assign('headerDescription','Wap地址修改');
	  $this->display();
    }
  }

  /**
   * 删除
   */
  public function delete(){
	$data['member_id'] = $this->user['id'];
	$data['id'] = $_POST['id'];
	$result = $this->db->where($data)->delete();
	if($result){
		ajaxSucReturn('删除成功');
	}else{
	    ajaxErrReturn('删除失败');
	}
  }

  /**
   * 设置默认地址
   */
  public function setdefault(){
	$wdata['member_id'] = $this->user['id'];
	$sdata['default'] = 0;
	$result = $this->db->where($wdata)->save($sdata);//其他设置非默认
	$wdata['id'] = $_POST['id'];
	$sdata['default'] = 1;
	$result = $this->db->where($wdata)->save($sdata);//设置默认
	if($result){
		ajaxSucReturn('设置成功');
	}else{
	    ajaxErrReturn('设置失败');
	}    
  
  }


}
?>