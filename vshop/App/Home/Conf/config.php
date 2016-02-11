<?php 
$config	=	require '../config.php';
$site_config_dir = $config['PUBLIC_CACHE'].'config/list.php';
$array	=	array(
    /* 运行时间设置 */
    'SHOW_RUN_TIME'			=> false,   // 运行时间显示
    'SHOW_ADV_TIME'			=> false,   // 显示详细的运行时间
    'SHOW_DB_TIMES'			=> false,   // 显示数据库查询和写入次数
    'SHOW_CACHE_TIMES'		=> false,   // 显示缓存操作次数
    'SHOW_USE_MEM'			=> false,   // 显示内存开销
    'SHOW_PAGE_TRACE'		=> false,   // 显示页面Trace信息 由Trace文件定义和
	'APP_DEBUG'			=>	false,  // 是否开启调试模式
    'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
	'APP_AUTOLOAD_PATH'     =>'@.ORG',
	/*
	'SESSION_OPTIONS'=>array(     
		'type'=> 'db',//session采用数据库保存     
		'expire'=>7200,//session过期时间，如果不设就是php.ini中设置的默认值   
	),
	*/
	'SESSION_TABLE'=>'zy_session', //必须设置成这样，如果不加前缀就找不到数据表，这个需要注意
	'SESSION_PREFIX'=>'wy_',	
    //'SESSION_AUTO_START'    => false,    // 是否自动开启Session
	'PRO_URL'				=> 'http://localhost/guoji/yunwang/Api',//项目地址
);
if(file_exists($site_config_dir)!=false){
  $site_config = include $site_config_dir;
  return array_merge($config,$array,$site_config);
}else{
  return array_merge($config,$array);
}

?>
