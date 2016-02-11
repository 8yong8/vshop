// JavaScript Document

function setCookie(){ //设置cookie
    var username = $("#username").val(); //获取用户名信息

	
    var password = $("#password").val(); //获取登录密码信息

    var checked = $("[name='baocun']:checked");//获取“是否记住密码”复选框

    if(checked && checked.length > 0){ //判断是否选中了“记住密码”复选框

    $.cookie("username",username,{ path: '/', expires: 14 });//调用jquery.cookie.js中的方法设置cookie中的用户名
    $.cookie("password",$.base64.encode(password),{ path: '/', expires: 14 });//调用jquery.cookie.js中的方法设置cookie中的登录密码，并使用base64（jquery.base64.js）进行加密
    }else{ 
       $.cookie("username", null,{ path: '/' }); 
	   $.cookie("password", null,{ path: '/' }); 
    }	
} 
function getCookie(){ //获取cookie
 	var username = $.cookie("username"); //获取cookie中的用户名
 	var password =  $.cookie("password"); //获取cookie中的登录密码
 	if(password && password!=null && password!='null'){//密码存在的话把“记住用户名和密码”复选框勾选住
	$("[name='baocun']").attr("checked","true");
 	}
 	if(username && username!=null && username!='null'){//用户名存在的话把用户名填充到用户名文本框
 	
	$("#username").val(username);
 	}
 	if(password && password!=null && password!='null'){//密码存在的话把密码填充到密码文本框
 	$("#password").val($.base64.decode(password));
 	}
 }