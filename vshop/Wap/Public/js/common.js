$(document).ready(function(){
  
  
});

/**
 *  手机短信发送
 * @param type 类型 reg:注册 bdmb:绑定手机 fpsw:找回密码
 */
var time1 = '';
var time_type = 'reg';
function sentmsm(type){
	var mobile = $('#mobile').val();
	time_type = type;
	if(mobile.length!=11){
		/*
		art.dialog({
			id:'reg',
			time: 2,
			content: '手机格式错误'
		});
		*/
		showMessage('手机格式错误',2000);
		return false;
	}
	$.ajax({
		dataType:'json',
		type: "POST",
		data:'mobile='+mobile+'&type='+type,
		url:APP+'/Public/reg_sms',
		error: function(request) {
			//alert("Connection error");
			showMessage('系统繁忙',2000);
		},
		success: function(data) {
		  //alert(data.errmsg);return;
		  if(data.error_code==0){
			$('#show_msg').text('60秒');
			$('#show_msg').unbind("click");
			time1 = setInterval(GetRTime,1000);
		  }else{
			//alert(data.notice);
			showMessage(data.notice,2000);
		  }
		}
	});							
}
/*
/ 短信倒计时
*/
function GetRTime(){
  var s = parseInt($('#show_msg').attr('num'));
  if(s==1){
	$('#show_msg').text('重新发送');
	$('#show_msg').attr('num',60);
	clearInterval(time1);
	$('#show_msg').click(function(){
	   sentmsm(time_type);
	});
  }else{
	  //alert(s);
	$('#show_msg').text((s-1)+'秒');
	$('#show_msg').attr('num',s-1);
  }
}

//ajax提交表单
function send(id,url){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:url,
		data:$('#'+id).serialize(),//你的formid
		async: false,
		error: function(request) {
			showMessage('系统繁忙',2000);
		},
		success: function(data) {
			if(data.error_code==0){
				/*
				art.dialog({
					time: 2,
					content: data.notice
				});
				*/
				showMessage(data.notice,2000);
				//alert(data.gourl);
				if(data.gourl){
					setTimeout(function(){
					  window.location = data.gourl;
					},1000);
				}
			}else{
				//alert(data.notice);
				showMessage(data.notice,2000);
				/*
				art.dialog({
					time: 2,
					content: data.notice
				});
				*/
			}
		}
	});
}

//关闭弹出框
function artclose(){
	var list = art.dialog.list;
	for (var i in list) {
		list[i].close();
	};
}


//页面跳转
function gourl(gourl){
	window.location = gourl;
}

// 显示提示框
function showMessage(message,time) {
	$('#notice_msg').html(message);
	$('#notice_msg').show();
	if(time){
	  setTimeout(function(){
		//$('#notice_msg').hide();
		$("#notice_msg").fadeOut("normal");
	  },time);
	}else{
	  setTimeout(function(){
		$("#notice_msg").fadeOut("normal");
	  },2000);
	}
}


/*
 * Javascript base64_encode() base64加密函数
   用于生成字符串对应的base64加密字符串
 * @param string str 原始字符串
 * @return string 加密后的base64字符串
*/
function base64_encode(str){
		var c1, c2, c3;
		var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";                
		var i = 0, len= str.length, string = '';

		while (i < len){
				c1 = str.charCodeAt(i++) & 0xff;
				if (i == len){
						string += base64EncodeChars.charAt(c1 >> 2);
						string += base64EncodeChars.charAt((c1 & 0x3) << 4);
						string += "==";
						break;
				}
				c2 = str.charCodeAt(i++);
				if (i == len){
						string += base64EncodeChars.charAt(c1 >> 2);
						string += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
						string += base64EncodeChars.charAt((c2 & 0xF) << 2);
						string += "=";
						break;
				}
				c3 = str.charCodeAt(i++);
				string += base64EncodeChars.charAt(c1 >> 2);
				string += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
				string += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
				string += base64EncodeChars.charAt(c3 & 0x3F)
		}
				return string
}
/*
 * Javascript base64_decode() base64解密函数
   用于解密base64加密的字符串
 * 吴先成  www.51-n.com ohcc@163.com QQ:229256237
 * @param string str base64加密字符串
 * @return string 解密后的字符串
*/
function base64_decode(str){
		var c1, c2, c3, c4;
		var base64DecodeChars = new Array(
				-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
				-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
				-1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57,
				58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0,  1,  2,  3,  4,  5,  6,
				7,  8,  9,  10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
				25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36,
				37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1,
				-1, -1
		);
		var i=0, len = str.length, string = '';

		while (i < len){
				do{
						c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
				} while (
						i < len && c1 == -1
				);

				if (c1 == -1) break;

				do{
						c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
				} while (
						i < len && c2 == -1
				);

				if (c2 == -1) break;

				string += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));

				do{
						c3 = str.charCodeAt(i++) & 0xff;
						if (c3 == 61)
								return string;

						c3 = base64DecodeChars[c3]
				} while (
						i < len && c3 == -1
				);

				if (c3 == -1) break;

				string += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));

				do{
						c4 = str.charCodeAt(i++) & 0xff;
						if (c4 == 61) return string;
						c4 = base64DecodeChars[c4]
				} while (
						i < len && c4 == -1
				);

				if (c4 == -1) break;

				string += String.fromCharCode(((c3 & 0x03) << 6) | c4)
		}
		return string;
}

//判断是否json数据
function is_json(obj){
  var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length; 
  return isjson;
}