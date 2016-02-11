<?php
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', 'localhost');
define('UC_DBUSER', '137137home');
define('UC_DBPW', '7f6r5ghh1');
define('UC_DBNAME', 'bbsdata');
define('UC_DBCHARSET', 'gbk');
define('UC_DBTABLEPRE', '`bbsdata`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', 'dsfYjsj12jsyzzM');
define('UC_API', 'http://192.168.1.108/gongsi/obj1/137home/ucenter');
define('UC_CHARSET', 'gbk');
define('UC_IP', '');
define('UC_APPID', '2');
define('UC_PPP', '20');


//ucexample_2.php 用到的应用程序数据库连接参数
$dbhost = 'localhost';			// 数据库服务器
$dbuser = '137137home';			// 数据库用户名
$dbpw = '7f6r5ghh1';				// 数据库密码
$dbname = 'bbsdata';			// 数据库名
$pconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
$tablepre = 'cdb_';   		// 表名前缀, 同一数据库安装多个论坛请修改此处
$dbcharset = 'gbk';			// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照论坛字符集设定

//同步登录 Cookie 设置
$cookiedomain = 'http://192.168.1.108'; 			// cookie 作用域
$cookiepath = '/';			// cookie 作用路径