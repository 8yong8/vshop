<?php
class Alipay{
  
  public function _alipayto(){

  //支付信息
$alipaySubmit = new AlipaySubmit($alipay_config);
//$anti_phishing_key = $alipaySubmit->query_timestamp();
$payment_type = "1";
$exter_invoke_ip = $_SERVER['REMOTE_ADDR'];
$out_trade_no = $row['pay_order_id'];
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		//"show_url"	=> $show_url,
		//"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);
//var_dump($parameter);exit;
	//建立请求
	//$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
  //echo '提交成功';
  
  }


}
?>