<?php 
namespace Home\Controller;
use Think\Controller;
class CacheController extends CommonController{

  /**
   * 清除页面缓存
   */
	public function clear(){
	  $list = array(
		 'WAP首页' => 'Wap:index',
	  );
	  foreach($list as $key=>$val){
	    delcache($val);
	  }
	  $this->success('缓存删除完毕');
	}

}