<?php 
	$alipay_config['id']='2';
	$pay_config['title']='支付宝';
	$alipay_config['partner']='2088411553531200';
	$alipay_config['key']= '9mzf5xxuxq2hjlytfawqer96c5juve6y';
	$alipay_config['sign_type']= strtoupper('MD5');
	$alipay_config['input_charset']= strtolower('utf-8');
	$alipay_config['cacert']='../cacert.pem';
	$alipay_config['transport']='http';
	$seller_email = 'candy@sunnybeauty-china.com';
	$notify_url = 'http://localhost/guoji/yunwang/index.php/Ali_Payment';
	$return_url = 'http://localhost/guoji/yunwang/index.php/Ali_Payment/ali_return';
	?>