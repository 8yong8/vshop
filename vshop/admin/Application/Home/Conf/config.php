<?php 
$config	=	require '../config.php';
//$site_config_dir = $config['DATA_CACHE_PATH'].'config/list.php';
$array	=	array(
    /* 运行时间设置 */
    //'SHOW_RUN_TIME'			=> false,   // 运行时间显示
    //'SHOW_ADV_TIME'			=> false,   // 显示详细的运行时间
    //'SHOW_DB_TIMES'			=> false,   // 显示数据库查询和写入次数
    //'SHOW_CACHE_TIMES'		=> false,   // 显示缓存操作次数
    //'SHOW_USE_MEM'			=> false,   // 显示内存开销
    //'SHOW_PAGE_TRACE'		=> false,   // 显示页面Trace信息 由Trace文件定义和Action操作赋值
    'SHOW_ERROR_MSG'        => true,    // 显示错误信息
	'LANG_SWITCH_ON'=>	TRUE,       //语言包开启
	'ArrayCache'            =>  './Cache/', //缓存数组地址
	'USER_AUTH_KEY'			=>	'zyId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'			=>'_administrator_',
	'LIKE_MATCH_FIELDS'		=>	'name',
	'NOT_AUTH_MODULE'		=>'Public',		// 默认无需认证模块
    'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
    'DEFAULT_AJAX_RETURN'   => 'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
	'USER_AUTH_ON'          =>true,               //开启认证
	'USER_AUTH_TYPE'		=>1,	// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_MODEL'		=>'Member',	// 默认验证数据表模型
	'RBAC_ROLE_TABLE'=>'zy_role',
	'RBAC_USER_TABLE'	=>	'zy_role_user',
	'RBAC_ACCESS_TABLE' =>	'zy_access',
	'RBAC_NODE_TABLE'	=> 'zy_node',
	'USER_AUTH_GATEWAY'	=>'?c=Public&a=login',	// 默认认证网关
    'GUEST_AUTH_ON'          => false,    // 是否开启游客授权访问
    //'Memcache_ON'               => true,     // mencache是否开启
    'LOG_RECORD'            => false,   // 默认不记录日志
	'LOG_LEVEL'=>'EMERG',
	'SESSION_PREFIX'=>'zy_',
	'DB_LIKE_FIELDS'=>'title|name|content',
	'TAGLIB_LOAD'               => true,//加载标签库打开
	'APP_AUTOLOAD_PATH'         =>'@.TagLib',
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
	//其他
	'page_size'     => 20, //默认每页显示条数
	/*缩略图配置*/
	'thumb' => true,
	'thumbPrefix' => '640_,200_',
	'thumbMaxWidth' => '640,200',
	'thumbMaxHeight' => '640,200',
	//注册其他的根命名空间
	'AUTOLOAD_NAMESPACE' => array(    
		'My'     => APP_PATH.'Library/My',
	),
	//'APP_USE_NAMESPACE'    =>    false,
    //'URL_MODEL'             => 3, 
	/*
	'TAGLIB_BUILD_IN'           =>'Cx,Lists',
	*/
);
return array_merge($config,$array);
/*
if(file_exists($site_config_dir)!=false){
  $site_config = include $site_config_dir;
  return array_merge($config,$array,$site_config);
}else{
  return array_merge($config,$array);
}
*/
?>
