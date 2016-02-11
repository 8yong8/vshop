<?php
require_once(C('PUBLIC_INCLUDE')."lib/core.function.php");
require_once(C('PUBLIC_INCLUDE')."lib/md5.function.php");
require_once(C('PUBLIC_INCLUDE')."lib/rsa.function.php");
class Api extends Action{

	protected $config;			  //配置
	protected $timestamp;		  //客户端请求时间戳
	protected $gateway;			  //网关地址

	function __construct($config){
	  if($config){
	    $this->config = $config;
	  }else{
		require_once("../config.php");
		$this->config = $config;
	  }
	  $this->gateway = $config['gateway'];
	  $this->timestamp = time();
	}

	public function set_timestamp($time){
	  $this->timestamp = $time;
	}

	public function set_appid($appid){
	  $this->appid = $appid;
	}

	public function set_appkey($appkey){
	  $this->appkey = $appkey;
	}
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	function buildRequestMysign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = ApiCreateLinkstringUrlencode($para_sort);
		switch ($this->config['sign_type']) {
			case 'RSA':
				$mysign = ApiRsaSign($prestr,$this->config['private_key_path']);	
				break;
			case 'MD5':
				$mysign = ApiMd5Sign($this->config['appid'],$this->config['appkey'],$prestr,$this->timestamp);	
				break;
			case 2:
				$mysign = ApiMd5Sign($this->config['appid'],$this->config['appkey'],$prestr,$this->timestamp);	
				break;
		}
		return $mysign;
	}

	/**
     * 生成要请求参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
	function buildRequestPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = ApiParaFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = ApiArgSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		//$para_sort['sign_type'] = strtoupper(trim($this->config['sign_type']));
		
		return $para_sort;
	}

	//获取缓存值
	public function get($para,$data){
	  //应用的APP_ID
	  $para['appid'] = $this->config['appid'];
	  //客户端请求时间戳
	  $para['time_stamp'] = $this->timestamp;
	  //API接口版本号
	  $para['version'] = $this->config['version'];

	  //随机字符串
	  $para['nonce_str'] = rand_string(32,4);

	  //授权签证
	  $sent_data = $this->buildRequestPara($para);
	  //sign 使用POST传值
	  $data['sign'] = $sent_data['sign'];

	  $data['sign_type'] = strtoupper(trim($this->config['sign_type']));

	  unset($sent_data['sign']);

	  $sent_url = $this->gateway.ApiCreateLinkstring($sent_data);
	  //dump($data);echo $sent_url;exit;
	  $result = httpPOST($sent_url,$data);
	  //dump($result);exit;
	  return $result;	  
	}

	//获取缓存值
	public function rm($para,$data){
	  //应用的APP_ID
	  $para['appid'] = $this->config['appid'];
	  //客户端请求时间戳
	  $para['time_stamp'] = $this->timestamp;
	  //API接口版本号
	  $para['version'] = $this->config['version'];

	  //随机字符串
	  $para['nonce_str'] = rand_string(32,4);

	  //授权签证
	  $sent_data = $this->buildRequestPara($para);

	  $sent_url = $this->gateway.ApiCreateLinkstring($sent_data);

	  $result = httpPOST($sent_url,$data);

	  return $result;	  
	}

	
}
?>