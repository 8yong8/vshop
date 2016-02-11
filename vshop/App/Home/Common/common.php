<?php 
//计算文件大小
function byte_format($input, $dec=0) { 
  $prefix_arr = array("B", "K", "M", "G", "T"); 
  $value = round($input, $dec); 
  $i=0; 
  while ($value>1024) 
  { 
     $value /= 1024; 
     $i++; 
  } 
  $return_str = round($value, $dec).$prefix_arr[$i]; 
  return $return_str; 
}

//URL组装
function url_cz($r_str){
    $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
	$parse = parse_url($url);
	if(isset($parse['query'])) {
		parse_str($parse['query'],$params);
		unset($params[$r_str]);
		$url   =  $parse['path'].'?'.http_build_query($params);
	}
	return $url;
}	

/**
 * 判断商品规格是否存在
 *
 * @access  public
 * @return  array
 */
function check_goods_type_specifications($product_type){
	$model = M('attribute');
	$data['attr_type'] = 1;
	$data['cat_id'] = $product_type;
	$count = $model->where($data)->field('cat_id')->count();
	return $count;
}

//隐藏名称
function name_hide($name){
  $leng = mb_strlen($name,'utf8');
  if($leng>3){
    $str = mb_substr($name,0,1,'utf8').'**'.mb_substr($name,$leng-1,$leng,'utf8');
  }else{
    $str = mb_substr($name,0,1,'utf8').'**';
  }
  return $str;
}

?>