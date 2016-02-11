<?php 
$config	=	require '../config.php';
$site_config_dir = $config['PUBLIC_CACHE'].'config/list.php';
$array	=	array(
	'SESSION_PREFIX'=>'zy_',
	'DB_LIKE_FIELDS'=>'title|name|content',
    'DATA_CACHE_COMPRESS'   => true,   // 数据缓存是否压缩缓存
    //'DATA_CACHE_CHECK'      => true,   // 数据缓存是否校验缓存
);
if(file_exists($site_config_dir)!=false){
  $site_config = include $site_config_dir;
  return array_merge($config,$array,$site_config);
}else{
  return array_merge($config,$array);
}
?>
