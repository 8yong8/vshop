<?php 
namespace Home\Controller;
use Think\Controller;
class ApiController extends CommonController {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Api');
  }

  public function _before_add(){
    if(!IS_POST){
	  $appid = date('Y').(microtime(true)*10000);
	  $this->assign('appid',$appid);
	  $appkey = rand_string(32,-1);
	  $this->assign('appkey',$appkey);
	}
  
  }

  /**
   * 生成缓存
   */
  function GiveCache(){
	  /*
	  $wdata['status'] = 1;
	  $list = $this->db->where($wdata)->select();
	  foreach($list as $array){
	    $data[$array['id']] = $array;
	  }
	  mk_dir(C('DATA_CACHE_PATH').'/api/');
	  F('list',$data,C('DATA_CACHE_PATH').'/api/');
	  $this->assign('jumpUrl',__URL__);
	  $this->success ('操作成功!');
	  */
  }



}
?>