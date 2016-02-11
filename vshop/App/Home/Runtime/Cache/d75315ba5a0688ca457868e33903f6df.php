<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="nofollow" />
<TITLE>系统登录</TITLE>
<link rel='stylesheet' type='text/css' href='__PUBLIC__/Css/style.css'>
<script type="text/javascript" src="__PUBLIC__/Js/jquery.min.js"></script>

<SCRIPT LANGUAGE="JavaScript">
var PUBLIC	 =	 '__PUBLIC__';
var APP = '__APP__';

function checkLogin(){
  var username = $('#username').val();
  var password = $('#password').val();
  var verify = $('#verify').val();
  $.ajax({
	   type: "POST",
	   url: "__URL__/checkLogin",
	   data: "username="+username+"&password="+password+"&verify="+verify,
	   success: function(msg){
		 //alert( "Data Saved: " + msg );
		 if(msg==1){
		   window.location = '__APP__';
		 }else{		   
		   $('#result').html(msg);
		   $('#result').show();
		 }
	   }
  });   
  
}

function fleshVerify(){
//重载验证码
  var timenow = new Date().getTime();
  //alert('__URL__/verify/'+timenow);
  $('#verifyImg').attr('src','__URL__/verify/'+timenow);
}

$(document).keypress(function(e) {
   //alert(e.which);
// 回车键事件  
   if(e.which == 13) {  
     checkLogin();
   }  
}); 
</SCRIPT>
</HEAD>
<BODY>
<FORM METHOD="POST" name="login" id="form1" action="__APP__/Public/register2" >
<div class="tCenter hMargin">
<TABLE id="checkList" class="login shadow" cellpadding=0 cellspacing=0 >
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<TR class="row" ><Th colspan="2" class="tCenter space" > 后台管理登录</th></TR>
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<TR class="row" ><TD colspan="2" class="tCenter"><div id="result" style="display:none">信息提示</div></TD></TR>
<TR class="row" ><TD class="tRight" width="25%">帐 号：</TD><TD><INPUT TYPE="text" class="medium bLeftRequire" check="Require" warning="请输入帐号" NAME="username" id="username" value="admin"></TD></TR>
<TR class="row" ><TD class="tRight">密 码：</TD><TD><INPUT TYPE="password" class="medium bLeftRequire" check="Require" warning="请输入密码" NAME="password" id="password"  value="admin123"></TD></TR>
<TR class="row" ><TD class="tRight">验证码：</TD><TD><INPUT TYPE="text" onKeyDown="keydown(event)" class="small bLeftRequire" check="Require" warning="请输入验证码" NAME="verify" id="verify"> <IMG id="verifyImg" SRC="__URL__/verify/" onClick="fleshVerify()" style="cursor:pointer" BORDER="0" ALT="点击刷新验证码" align="absmiddle"></TD></TR>
<TR class="row" ><TD class="tCenter" align="justify" colspan="2">
<INPUT TYPE="hidden" name="ajax" value="1">
<INPUT TYPE="submit" value="登 录"  class="submit small hMargin">
<INPUT TYPE="reset" value="重 置" class="submit small">
</td></tr>
<tr><td height="5" colspan="2" class="bottomTd" ></td></tr>
</TABLE>
</div>
</FORM>
</div>
</BODY>
</HTML>