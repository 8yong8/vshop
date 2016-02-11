<?php 
// 后台管理
class BankAction extends CommonAction{

  /**
   * 导入信息 废弃
   */
  function daoru(){
	exit;
    $model = M('bank');
    $str = '工商银行,建设银行,农业银行,中国银行,交通银行,招商银行,光大银行,兴业银行,民生银行,中信银行,广发银行,华夏银行,深圳发展银行,浦发银行,平安银行,中国邮政储蓄银行';
	$arr = explode(',',$str);
	foreach($arr as $val){
	  $data['name'] = $val;
	  $count = $model->where($data)->count();
	  if($count){
	    continue;
	  }
	  $model->add($data);
	}
	dump($arr);
  }

} 
?>