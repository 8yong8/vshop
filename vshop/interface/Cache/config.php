<?php 
$config['appid'] ='201586485090108670';
$config['appkey'] = 'vqN7ex8QfPsGD5HUSETxeAIpiYjAvKZu';
$config['sign_type'] = strtoupper('RSA'); //MD5和RSA
$config['gateway'] = 'http://localhost/git/vshop/CacheApi/?'; //网关地址
//私钥（后缀是.pem）文件相对路径
$config['private_key_path']	= dirname(__FILE__).'/key/my_rsa_private_key.pem';
//公钥（后缀是.pem）文件相对路径
$config['public_key_path'] = dirname(__FILE__).'/key/my_rsa_public_key.pem';
$config['input_charset'] = strtolower('utf-8');
$config['cacert'] = '../cacert.pem';
$config['transport'] ='http';
$config['version'] = '1.0';
?>