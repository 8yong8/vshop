<?php

class ProductAction extends CommonAction {

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Product');
  }

  /**
   * 产品信息缓存
   */
  function SetDetail(){
    $ids = $_REQUEST['id'];
	$data['id'] = array('in',explode(',',$ids));
    $list = $this->db->where($data)->select();
	$pmodel = M('Pic');
	C('DATA_CACHE_TYPE','File');
	foreach($list as $vo){
		$p_data['source'] = 'Product';
		$p_data['sourceid'] = $vo['id'];
		$pics = $pmodel->field('domain,filepath,is_thumb')->where($p_data)->select();
		$options['dir'] = get_dir($vo['id']);
		$vo['imgs'] = $pics;
		setCache('detail',$vo,0,$options);
	}
	  foreach($list as $array){
	    $data[$array['id']] = $array;
	  }
	  if($_POST['from']=='self'){
	    return $data;exit;
	  }
	  $return = $this->SetCache('detail',$vo);
	  if($return){
	    $msg['error_code'] = 0;
	  }else{
	    $msg['error_code'] = 8002;
	  }
	  if($_POST['status'])$msg['data'] = $data;
	  echo json_encode($msg);exit;
  }

}
