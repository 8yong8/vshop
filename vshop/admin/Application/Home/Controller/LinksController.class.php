<?php
namespace Home\Controller;
use Think\Controller;
class LinksController extends CommonController {

  /**
   * 查询条件
   */
  public function _search(){
	if($_GET['name']!=""){
      $data['name'] = $_GET['name'];
	  $this->assign("name",$_GET['name']);
	}
	return $data;
  }

  /**
   * 删除信息
   */
  public function foreverdelete() {
	//删除指定记录
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		//$id = $_REQUEST [$pk];
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
				$ids =  explode ( ',', $id );
				foreach($ids as $id){
				  $this->history($id);
				}
				$this->GiveCache();
				$this->success ('删除成功！');
			} else {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }

  /**
   * 生成缓存
   */
  function GiveCache(){
	$model = D('Links');
	$wdata['status'] = 1;
	$list = $model->where($wdata)->select();
	/*
	if($list)F('list',$list,C('DATA_CACHE_PATH').'/links/');
	*/
	if($list)setCache('list',$list);
	if($_GET['re']){
	  $this->success ('更新完成！');
	}
	
  }

}
?>