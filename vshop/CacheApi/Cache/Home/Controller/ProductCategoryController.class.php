<?php 
namespace Home\Controller;
use Think\Controller;
class ProductCategoryController extends CommonController {

  /**
   * 商城分类缓存
   */
  function SetSc_list(){
	  $model = D('ProductCategory');
	  $wdata['status'] = 1;
	  $wdata['_string'] = "find_in_set('商城',channel)";
	  $list = $model->where($wdata)->order('lv asc,sort asc')->select();

	  if($_POST['from']=='self'){
	    return $list;exit;
	  }
	  if($_POST['status'])$msg['data'] = $list;
	  $return = $this->SetCache('sc_list',$list);

	  $wdata['pid'] = 0;
	  $top_cat = $model->where($wdata)->order('sort asc')->select();

	  $this->SetCache('sc_list_top',$top_cat);
	  $cdata['status'] = 1;
	  foreach($list as $val){
		$cdata['pid'] = $val['id'];
		$child = $model->where($cdata)->order('sort asc')->select();
		if($child){
		  $this->SetCache('sc_list_'.$val['id'],$child);
		}
		$this->SetCache('sc_detail_'.$val['id'],$val);
	  }

	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  echo json_encode($msg);exit;
  }

}
?>