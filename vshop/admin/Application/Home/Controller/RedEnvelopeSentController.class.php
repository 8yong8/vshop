<?php 
namespace Home\Controller;
use Think\Controller;
class RedEnvelopeSentController extends CommonController {

  function _before_index(){
	  //$_REQUEST ['listRows'] = 50;
	  //dump($_SESSION);
  }

  /**
   * 发放红包
   */
  function _before_add(){
	if($_POST){
      $model = M('redEnvelopeIntegral');
      $data['create_time'] = array('lt',time());
	  $_POST['wl_num'] = $_POST['hb_num'] = $model->where($data)->sum('num');
	  //echo $model->getlastsql();exit;
	}else{
	  $model = M('red_envelope_integral');
	  $hb_num = $model->sum('num');
	  $this->assign('hb_num',$hb_num);
	}
  }

  /**
   * 删除
   */
  public function delete() {
	//删除指定记录
	$name = CONTROLLER_NAME;
	$model = M ($name);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
			  $condition1 = array ('fid' => array ('in', explode ( ',', $id ) ) );
			  $model1 = D('flag_module');
			  $model1->where($condition1)->delete();
			  $this->success ('删除成功！');
			} else {
			  $this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }


}
?>