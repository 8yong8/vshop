<?php 
	class WxPayConfig{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx2be892adc35c8c7d';
	//受理商ID，身份标识
	const MCHID = '1241883502';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '9c4288b110e38a6fa892a0bd11bf9908';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '9c4288b110e38a6fa892a0bd11bf9908';
	//=======【curl代理设置】===================================
	/**
	 * 
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认0.0.0.0和0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = '0.0.0.0';
	const CURL_PROXY_PORT = 8080;	

	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = 'D:\wamp\www\guoji\yunwang/Wxpay/cacert20150528/apiclient_cert.pem';
	const SSLKEY_PATH = 'D:\wamp\www\guoji\yunwang/Wxpay/cacert20150528/apiclient_key.pem';
	
	//=======【上报信息配置】===================================
	/**
	 * 
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	?>