<?php
$config	=	require '../config.php';
$site_config_dir = $config['PUBLIC_CACHE'].'config/list.php';
$array	= array(
    'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
	'PRO_URL'				=> 'http://localhost/guoji/vshop/Wap',//项目地址
	'page_size'				=> 10,//分页数
);
if(file_exists($site_config_dir)!=false){
  $site_config = include $site_config_dir;
  return array_merge($config,$array,$site_config);
}else{
  return array_merge($config,$array);
}
?>