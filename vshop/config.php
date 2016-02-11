<?php
//服务器配置
return array(
	'DB_TYPE'=>'mysql',
	'DB_HOST'=>'localhost',
	'DB_USER'=>'root',
	'DB_PWD'=>'',
	'DB_NAME'=>'vshop',
	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'zy_',
	'DB_CHARSET' =>  'UTF8',  //数据库编码
	'ROOT_SITE_DIR'=>'D:\wamp\www\gh\vshop/',	//跟网站目录
	'SITE_URL'=>'http://localhost/gh/vshop/wap',	//默认网站URL
	'WAP_URL'=>'http://localhost/gh/vshop/wap',	//wap网站URL
	'PC_URL'=>'http://localhost/gh/vshop/pc',		//pc网站URL
	'API_URL'=>'http://localhost/gh/vshop/BaseApi',//API接口URL
	'MEMBER_SITE_URL'=>'http://localhost/gh/vshop/member',//会员地址
	'STATIC_SITE_URL'=>'http://localhost/gh/vshop/static',//静态文件地址
	'IMG_ROOT'=>'D:\wamp\www\gh\vshop\img0/',     //图片上传路径
	'IMG_URL'=>'http://localhost/gh/vshop/img0/', //图片地址
	'PUBLIC_INCLUDE'=>'D:\wamp\www\gh\vshop\include/', //公共库目录
	'INTERFACE_PATH'=>'D:\wamp\www\gh\vshop\interface/',//接口路径
	'APP_DOMAIN'=>'', //cookie域名设置
    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 60*60*24*30,    // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_PREFIX'         => 'gj',    // Cookie前缀 避免冲突
	'SESSION_LIFETIME'=>10800, //session有效期
	'VAR_PAGE'=>'p',//分页标签
    //'ERROR_PAGE'    => 'http://localhost/gh/vshop/admin',
	'TOKEN_ON'              =>false,
    'sign'                  =>'H9UImivKbqmzh653s5wKaB3sMDgCrEqi',//签名
	'site_company'          =>'浙江vion网络科技有限公司',
	'site_name'             =>'vion电商',
    'WEB_DEPLOY_TYPE'       => 1, // 站点部署方式:0 集中式(单一服务器),1 分布式(集群)
	/*'SESSION_OPTIONS'		=>array(
		'type'=> 'db',//session采用数据库保存
		'expire'=>28800,//session过期时间，如果不设就是php.ini中设置的默认值
	  ),
	'SESSION_TABLE'			=>'zy_session', //必须设置成这样，如果不加前缀就找不到数据表，这个需要注意
	*/
    /*
	'DATA_CACHE_TYPE'       => 'Memcache',  // 数据缓存类型,
	'MEMCACHE_HOST'  => 'tcp://localhost:11211',
    */
    'DATA_CACHE_PATH'       => 'D:\wamp\www\gh\vshop\Cache/',// 缓存路径设置 (仅对File方式缓存有效)
    //'DATA_CACHE_SUBDIR'     => true,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    //'DATA_PATH_LEVEL'       => 3,        // 子目录缓存级别
	'DATA_CACHE_TIME' => 0,
	//'UP_DRIVER'             =>'Ftp',//FTP上传
	'wx_desc'				=>'vion电商欢迎您',
	'version'				=>'0.6',
	'UP_DRIVER' =>'Local',//图片上传方式,默认本地
);
?>