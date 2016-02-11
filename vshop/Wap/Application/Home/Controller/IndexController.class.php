<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends PublicController {
  public function _initialize(){
	parent::_initialize();
  }


	public function gopayAction() 
	{
		if (!form($this)) 
		{
			$this->json['errno'] = '1';
			$this->json['errmsg'] = $this->error->firstMessage();
			$this->_helper->json($this->json);
		}
		
		$order = $this->_db->select()
			->from(array('o' => 'order'),array('id','subject','body','pay_amount'))
			->where('o.id = ?',$this->input->id)
			->query()
			->fetch();
		$order['seller_id'] = $this->_config->pay->alipay->sellerId;
		$order['notify_url'] = DOMAIN_NAME . 'pay/aliapp/notify';
		$order['autoclose'] = '';
		
		$this->json['errno'] = '0';
		$this->json['order'] = $order;
		$this->_helper->json($this->json);
	}

  /**
   * 首页
   */
  public function index(){
	//$model = M('Position_data');
	$pos_config = array(
		0 =>array
		(
			'id'=>10,
			'limit'=>6,
			'style'=>'mgt15',
		),
		1 =>array
		(
			'id'=>11,
			'limit'=>3,
			'style'=>'bbb',
		),
		2 =>array
		(
			'id'=>12,
			'limit'=>1,
			'style'=>'mgt15',
		),	
		3 =>array
		(
			'id'=>13,
			'limit'=>3,
			'style'=>'bbb',
		),	
		4 =>array
		(
			'id'=>14,
			'limit'=>1,
			'style'=>'mgt15',
		),	
		5 =>array
		(
			'id'=>15,
			'limit'=>3,
			'style'=>'bbb',
		),
		6 =>array
		(
			'id'=>16,
			'limit'=>3,
		),
		7 =>array
		(
			'id'=>17,
			'limit'=>20,
		),
		
	);
	/* 通栏 */
	
	$cacheId = 'Wap:index';

	$positions = getCache($cacheId);
	if(!$positions){
	  $positions = $this->get_position_data($pos_config);
	  setCache($cacheId,$positions);
	}
	$this->assign('positions',$positions);
	$this->assign('headerTitle','Wap首页');
	$this->assign('headerKeywords','Wap首页');
	$this->assign('headerDescription','Wap首页');
	//$tpl =  $this->fetch();
	$this->display();
  }

  //获取位置数据
  protected function get_position_data($pos_config){
	$model = M('Position_data');
	foreach($pos_config as $position){
		$wdata['position_id'] = $position['id'];
		$wdata['status'] = 1;
		$positionsData = $model->where($wdata)->order('sort asc,id asc')->limit($position['limit'])->select();
		foreach ($positionsData as $key=>$positionData) 
		{
			$params = unserialize($positionData['params']);
			if($positionData['url']!=''){
				$positionsData[$key]['url'] = $positionData['url'];
			}else if($positionData['data_type']=='product_detail'){
				$positionsData[$key]['url'] = __APP__.'Product/detail/?id='.$params['product_id'];
			}else if($positionData['data_type']=='product_list'){
				$param_str = http_build_query($params);
				$positionsData[$key]['url'] = __APP__.'Product/list?'.$param_str;				
			}else if($positionData['data_type']=='article_detail'){
				$positionsData[$key]['url'] = __APP__.'News/detail/?id='.$params['news_id'];					
			}
		}
		$id = $position['id'];
		$data[$id] = $positionsData;
	}
	return $data;  
  }

  public function zhifu_test(){
	require_once getcwd()."/Wxpay/lib/WxPay.Api.php";
	require_once getcwd()."/Wxpay/unit/WxPay.JsApiPay.php";
	//获取用户openid
	$tools = new JsApiPay();
	$openId = $tools->GetOpenid();
	//cookie('openId',$openId);
	//统一下单
	/*
	$input = new WxPayUnifiedOrder();
	$input->SetBody("test");
	$input->SetAttach("test");
	$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
	$input->SetTotal_fee("1");
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("test");
	$input->SetNotify_url(C('SITE_URL')."/index.php/Index/wx_notify");
	$input->SetTrade_type("JSAPI");
	$input->SetOpenid($openId);
	$order = WxPayApi::unifiedOrder($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	//dump($order);echo $jsApiParameters; exit; 
	$this->assign('jsApiParameters',$jsApiParameters);
	*/
	$this->display();
  }

  public function zhifu_test2(){
	require_once getcwd()."/Wxpay/lib/WxPay.Api.php";
	require_once getcwd()."/Wxpay/unit/WxPay.JsApiPay.php";
	$openId = cookie('openId');
	//统一下单
	$tools = new JsApiPay();
	$input = new WxPayUnifiedOrder();
	$payAmount = (string)100;
	$input->SetBody("test");
	$input->SetAttach("test");
	$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
	$input->SetTotal_fee($payAmount);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("test");
	//$input->SetNotify_url(C('SITE_URL')."/index.php/Index/wx_notify");
	$input->SetNotify_url(C('SITE_URL')."/index.php/Wx_Payment/ali_notify_merge");
	$input->SetTrade_type("JSAPI");
	$input->SetOpenid($openId);
	$order = WxPayApi::unifiedOrder($input);
	//dump($order);echo $jsApiParameters; exit; 
	$jsApiParameters = $tools->GetJsApiParameters($order);
	//dump($order);echo $jsApiParameters; exit; 
	$this->assign('jsApiParameters',$jsApiParameters);
	$this->display();
  }

  public function app_test(){
	require_once getcwd()."/Wxpay/lib/WxPay.Api.php";
	require_once getcwd()."/Wxpay/unit/WxPay.JsApiPay.php";
	$payAmount = (string)100;
	//统一下单
	$tools = new JsApiPay();
	$input = new WxPayUnifiedOrder();
	$input->SetBody("test");
	$input->SetAttach("test");
	$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
	$input->SetTotal_fee($payAmount);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("test");
	//$input->SetNotify_url(C('SITE_URL')."/index.php/Index/wx_notify");
	$input->SetNotify_url(C('SITE_URL')."/index.php/Wx_Payment/ali_notify_merge");
	$input->SetTrade_type("APP");
	$order = WxPayApi::unifiedOrder($input);
		$this->json['appid'] = $order['appid'];
		$this->json['partnerid'] = $order['mch_id'];
		$this->json['prepayid'] = $order['prepay_id'];
		$this->json['package'] = 'Sign=WXPay';
		$this->json['noncestr'] = $order['nonce_str'];
		$this->json['timestamp'] = time();

		//再次生成签名
		ksort($this->json);
		//$string = $aa->ToUrlParams($this->json);
		$buff = "";
		foreach ($this->json as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		$buff = trim($buff, "&");
		$string = $buff;
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".WxPayConfig::KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		$this->json['sign'] = $result;
		echo json_encode($this->json);
  }

  public function wx_notify(){
	require_once getcwd()."/Wxpay/lib/WxPay.Api.php";
	$rrmodel = M('record_remark');
	$rrdata['type'] = 2;
	$rrdata['rid'] = 0;
	if($_POST){
	  $rrdata['info'] = serialize($_POST);
	}else{
	  $rrdata['info'] = file_get_contents('php://input');
	}
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	$xml = '<xml><appid><![CDATA[wx2be892adc35c8c7d]]></appid>
<attach><![CDATA[test]]></attach>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1241883502]]></mch_id>
<nonce_str><![CDATA[wtk0c2zlxxx9yk10poxa4zpcrhr2prql]]></nonce_str>
<openid><![CDATA[oNjyPs7mmMVX7xf0qsScaRq4RRpY]]></openid>
<out_trade_no><![CDATA[124188350220150604143444]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[D07549833387139CF68B97D6A50B4E6B]]></sign>
<time_end><![CDATA[20150604143150]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[1009530519201506040206996268]]></transaction_id>
</xml>';
    $array_data = WxPayResults::FromXml($xml);
	dump(unserialize('a:17:{s:5:"appid";s:18:"wx2be892adc35c8c7d";s:6:"attach";s:4:"test";s:9:"bank_type";s:3:"CFT";s:8:"cash_fee";s:1:"1";s:8:"fee_type";s:3:"CNY";s:12:"is_subscribe";s:1:"Y";s:6:"mch_id";s:10:"1241883502";s:9:"nonce_str";s:32:"ciqfhznmzsxcxwl6j9olwio9y3jeumkz";s:6:"openid";s:28:"oNjyPs7mmMVX7xf0qsScaRq4RRpY";s:12:"out_trade_no";s:24:"124188350220150604170134";s:11:"result_code";s:7:"SUCCESS";s:11:"return_code";s:7:"SUCCESS";s:4:"sign";s:32:"F35BD87FAC7D03C4B53AD0A4539A06C7";s:8:"time_end";s:14:"20150604165822";s:9:"total_fee";s:1:"1";s:10:"trade_type";s:5:"JSAPI";s:14:"transaction_id";s:28:"1009530519201506040207633897";}'));exit;
	dump($array_data);exit;
	//微信签名认证
	$result = WxPayResults::Init2($xml);
	if($result){
	  $rrdata['error_msg'] = '签名通过';
	}else{
	  $rrdata['error_msg'] = '签名错误';
	  return false;
	}
	$transaction_id = $array_data['transaction_id'];
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);

	dump(serialize($result));exit;	
	$rrdata['rstatus'] = 0;
	$rrdata['order_id'] = 0;//微信订单
	$rrdata['oid'] = $array_data['out_trade_no'];//我方订单
	$rrdata['create_time'] = time();
	$remark_id = $rrmodel->add($rrdata);
    //echo "SUCCESS";EXIT;
  }

}