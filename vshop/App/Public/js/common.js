$(document).ready(function(){
  //登陆
  $('#btn-login').click(function(){
    $('#login_form').submit();
  });
  var ids =  '';
  $('.onlineSell_times').each(function(){
	var id = $(this).attr('id');
    ids += id.substr(13)+',';
  });
  if(ids){
    ids = ids.substr(0,ids.length-1);
	setInterval(update_bid, 3000);
  }
  //添加一个商品
  $('.add_cart_one').click(function(){
	var num = 1;
	var id = $(this).attr('gid');
	before_cart(id,num);
  });

  //多条
  $(".shoppingCart").click(function(){
	var num = $("#product_amount").val();
	var id = $("#product").val();
	before_cart(id,num);
  });

  //添加
  function before_cart(id,num){
	var url=APP+"?m=Goods&a=ajax_addcart";
	$.ajax({
		type: "post",
		url: url,
		data:{"id":id,"num":num},
		beforeSend: function(XMLHttpRequest){
		//ShowLoading();
		//console.log("正在加载");
		},
		success:ajax_to_cart,
		complete: function(XMLHttpRequest, textStatus){
		//HideLoading();
		//console.log("加载完成");
		},
		error: function(){
			alert("出错了");
		}
	});
  }
  //加入购物车回调
  function ajax_to_cart(msg){
	   var data = eval('('+msg+')');
	   //alert(msg);
	   if(data.status==-1){
			  alert('您还没有登录，请登录');
			  window.location.href=ROOT+'/index.php?m=Public&a=login&from_url='+SELF;
	   }else if(data.status==1){
		  //alert('加入购物车成功');
		  $('#numCircle').text(data.notice);
		  art.dialog({
			time: 1.5,
			content: '加入购物车成功'
		  });
	   }else{
		  art.dialog({
			time: 1.5,
			content: data.notice
		  }); 
	   }
  }

	
	function ajax_favorite() {
		$('.collect').click(function() {
				var pid = $("#product").val();
				$.post(APP+'?m=Product&a=ajax_favorite', {pid: pid}, function(data) {
						if (data == 2) {
								alert('您还没有登录，请登录');
								window.location.href = 'http://www.137home.com/index.php?m=Public&a=login&from_url='+SELF;
						} else if (data == -2) {
								alert('您已经收藏过了！');
						} else if (data == -1) {
								alert('收藏失败');
						} else if (data == 1) {
								alert('收藏成功！');
						} else {
								alert('未知错误！');
						}
				})
		})
	}
    
	function  card_location(){
	  $("#add_tocard").click(function(){
		var num=$("#product_amount").val();
		var id=$("#product").val();
		   before_card_location(id,num);
	  });
	}
    //点击报名预定和跳入购物车
    function before_card_location(id,num){
		//alert(domain+"?m=Product&a=ajax_addcart");
		var url=APP+"?m=Goods&a=ajax_addcart";
		$.ajax({
			type: "post",
			url: url,
			data:{"id":id,"num":num},
			beforeSend: function(XMLHttpRequest){
			//ShowLoading();
			//console.log("正在加载");
			},
			success:location,
			complete: function(XMLHttpRequest, textStatus){
			//HideLoading();
			//console.log("加载完成");
			},
			error: function(){
				alert("出错了");
			}
		});
    }
    //预订报名，不同于加入购物车的回调数据
  function location(data){
	  //alert(data);return false;
	  //document.write(data);
          if(data==-1){
              alert('您还没有登录，请登录');
              window.location.href=ROOT+'/index.php?m=Public&a=login&from_url='+SELF
           }else if(data==2){
                  alert('本产品限购1个数量！');
           }else if(data==0){
                 alert("商品售罄，如需订购，请致电客服热线"+tel);   
           }else{
              window.location.href=APP+"?m=Product&a=cartinfo";
           }
    }
  
});


/*
/ 手机短信发送
*/
var time1 = '';
function sentmsm(){
	var mobile = $('#mobile').val();
	if(mobile.length!=11){
		art.dialog({
			id:'reg',
			time: 2,
			content: '手机格式错误'
		});
		return false;
	}
	$.ajax({
		dataType:'json',
		type: "POST",
		data:'mobile='+mobile,
		url:URL+'user/account/sendcode',
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
		  //alert(data.errmsg);return;
		  if(data.errno==1){
			art.dialog({
				time: 2,
				content: data.errmsg
			});
		  }else{
			$('#show_msg').text('60秒');
			$('#show_msg').unbind("click");
			time1 = setInterval(GetRTime,1000);
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
	   sentmsm();
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
		data:$('#'+id).serialize(),// 你的formid
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
			if(data.errno==0){
				art.dialog({
					time: 2,
					content: data.notice
				});
				if(data.gourl){
					setTimeout(function(){
					  window.location = data.gourl;
					},1000);
				}
			}else{
				art.dialog({
					time: 2,
					content: data.errmsg
				});
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


//ajax登录
function ajax_login(){
  var msg = '<FORM METHOD=POST id="login_form"  action="'+APP+'/Public/checkLogin"><div class="input-group"><div class="account"><input type="text" placeholder="账号/邮箱/手机号码" name="username" id="username"></div><div class="password"><input type="password" placeholder="密码" name="password" id="password"></div>				<a href="javascript:$(\'#login_form\').submit();" id="btn-login" style="margin-top:0px;">登&nbsp;&nbsp;录</a></FORM>';
  art.dialog({
	id: 'ajaxlogin',
	title:'登陆',
	zIndex:'99999',
	//drag : false,
	content:msg
  });
}

//ajax登录检测
function ajax_checkLogin(){
  var url = URL+'/ajax_checkLogin';
  var data = {
	username: $('#username').val(), 
	password: $('#password').val(),  
  }
  $.ajax({
	dataType:'json',
	type: "POST",
	url:url,
	data:data,
	error: function(request) {
		alert("Connection error");
	},
	success: function(data) {
		art.dialog({
			time: 1.5,
			content: data.notice
		});
	  if(data.status==1){
	    setTimeout(function(){
		  parent.location.reload();
		},500);
	  }
	}
  });
}