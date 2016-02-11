<?php
function validateMobile($mobile){
/** 
  * 手机号码 
  * 移动：134[0-8],135,136,137,138,139,150,151,157,158,159,182,187,188 
  * 联通：130,131,132,152,155,156,185,186 
  * 电信：133,1349,153,180,189 
  */      
  $res1= preg_match('/^1(3[0-9]|5[0-35-9]|8[025-9])\\d{8}$/', $mobile);  
      
 /** 
  * 中国移动：China Mobile 
  11         * 134[0-8],135,136,137,138,139,150,151,157,158,159,182,187,188 
  */       
  $res2= preg_match('/^1(34[0-8]|(3[5-9]|5[017-9]|8[278])\\d)\\d{7}$/', $mobile);  
    
 /** 
  * 中国联通：China Unicom 
  * 130,131,132,152,155,156,185,186 
  */  
  $res3= preg_match('/^1(3[0-2]|5[256]|8[56])\\d{8}$/', $mobile);  
    
  /** 
  * 中国电信：China Telecom 
  * 133,1349,153,180,189 
  */  
  $res4= preg_match('/^1((33|53|8[09])[0-9]|349)\\d{7}$/', $mobile);  
    
  /** 
  * 大陆地区固话及小灵通 
  * 区号：010,020,021,022,023,024,025,027,028,029 
  * 号码：七位或八位 
  */  
  $res5= preg_match('/^0(10|2[0-5789]|\\d{3})\\d{7,8}$/', $mobile);  
    
  if ( strlen($mobile)==11 && ( $res1 ||$res2 ||$res3 ||$res4 ||$res5  ) )  
  {  
     return true;  
  }else{  
     return false;  
  } 

}

//身份证号验证
function checkIdCard($idcard){
    $City = array(11=>'北京',12=>'天津',13=>'河北',14=>'山西',15=>'内蒙古',21=>'辽宁',22=>'吉林',23=>'黑龙江',31=>'上海',32=>'江苏',33=>'浙江',34=>'安徽',35=>'福建',36=>'江西',37=>'山东',41=>'河南',42=>'湖北',43=>'湖南',44=>'广东',45=>'广西',46=>'海南',50=>'重庆',51=>'四川',52=>'贵州',53=>'云南',54=>'西藏',61=>'陕西',62=>'甘肃',63=>'青海',64=>'宁夏',65=>'新疆',71=>'台湾',81=>'香港',82=>'澳门',91=>'国外');
	$iSum = 0;

	$idCardLength = strlen($idcard);

	//长度验证
	if(!preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/", $idcard) && !preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/", $idcard))
	{
	  return false;
	}

	//地区验证
	if(!array_key_exists(intval(substr($idcard,0,2)),$City))
	{
	  return false;
	}

// 15位身份证验证生日，转换为18位
	if ($idCardLength == 15)
	{
	   $sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
		$d = new DateTime($sBirthday);
		$dd = $d->format('Y-m-d');
		
		if($sBirthday != $dd)
		{
		  return false;
		}
		$idcard = substr($idcard,0,6).'19'.substr($idcard,6,9);//15to18
		$Bit18 = getVerifyBit($idcard);//算出第18位校验码
		$idcard = $idcard.$Bit18;
	}
	// 判断是否大于2078年，小于1900年
	$year = substr($idcard,6,4);
	if ($year2078 )
	{
		return false;
	}
	//18位身份证处理
	$sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
	$d = new DateTime($sBirthday);
	$dd = $d->format('Y-m-d');
	if($sBirthday != $dd)
	{
	  return false;
	}
	//身份证编码规范验证
	$idcard_base = substr($idcard,0,17);
	if(strtoupper(substr($idcard,17,1)) != getVerifyBit($idcard_base))
	{
	  return false;
	}
	return true;
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function getVerifyBit($idcard_base)
{
	if(strlen($idcard_base) != 17)
	{
		return false;
	}
	//加权因子
	$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	//校验码对应值
	$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
	$checksum = 0;
	for ($i = 0; $i < strlen($idcard_base); $i++)
	{
		$checksum += substr($idcard_base, $i, 1) * $factor[$i];
	}
	$mod = $checksum % 11;
	$verify_number = $verify_number_list[$mod];
	return $verify_number;
}


?>