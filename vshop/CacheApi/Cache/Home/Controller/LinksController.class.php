<?php
namespace Home\Controller;
use Think\Controller;
class LinksController extends CommonController {

  /**
   * 列表缓存
   */
  function SetList(){
	  $model = D('Links');
	  $wdata['status'] = 1;
	  $list = $model->where($wdata)->select();
	  if($_POST['from']=='self'){
	    return $list;exit;
	  }
	  $return = $this->SetCache('list',$list);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  if($_POST['status'])$msg['data'] = $list;
	  echo json_encode($msg);exit;
  }

}
