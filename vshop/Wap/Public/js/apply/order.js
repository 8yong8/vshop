/* 确认订单页 */
//发票选择
function fp(){
  if($('#tax').attr("checked")=='checked'){
	$('#tax').attr("checked",false);
	$('#tax_msg').hide();
  }else{
	$('#tax').attr("checked",true);
	$('#tax_msg').show();
  }
  if($('#tax').attr("checked")=='checked'){
    tax = 1;
  }else{
    tax = 0;
  }
  jiage();
}
//红包选择
function hb(coupon_user_id,val){
	if(coupon_user_id>0){
		$('#coupon_user_id').attr('value',coupon_user_id);
		$('#coupon_user_id').text(val+'元红包');
	}else{
		$('#coupon_user_id').attr('value',0);
		$('#coupon_user_id').text('');		
	}
	coupon_price = val;
	jiage();
	$('#coupon').hide();
}
//促销选择
function cuxiao(sp_id,val,info){
	if(sp_id>0){
		$('#sp_id').attr('value',sp_id);
		$('#sp_id').text(info);
	}else{
		$('#sp_id').attr('value',0);
		$('#sp_id').text('');		
	}
	sp_id = sp_id;
	sp_price = val;
	jiage();
	$('#prom').hide();
}

//价格计算
function jiage(){
	var actual_paid = parseInt(total_fee)-parseInt(sp_price)-parseInt(coupon_price)+parseInt(shipping_price);
	if(tax){
	  actual_paid = actual_paid+parseInt(actual_paid*tax_rate);
	}
	//alert(actual_paid);
    $('#total_fee').text(actual_paid);
}
//提交信息
function sent(){
	var consignee_id = $('.s_name').attr('value');
	var tax = $("#tax:checked").val() ? 1 : 0;
	var tax_title = $('#tax_title').val();
	var tax_content = $('#tax_content').val();
	var memo = $('#memo').val();
	//alert(tax);
	if(!consignee_id){
		/*
		art.dialog({
			time: 1.5,
			content: '缺少收件人信息'
		});
		*/
		showMessage('缺少收件人信息',2000);
		return;
	}
	var coupon_user_id = $('#coupon_user_id').attr('value');
	alert(cart_ids+'/'+consignee_id+'/'+tax+'/'+tax_title+'/'+tax_content+'/'+memo+'/'+coupon_user_id+'/'+sp_id);return;
	//购物车支付
	if(type==1){
		sentdata = {
			cart_ids : cart_ids,
			consignee_id : consignee_id,
			tax : tax,
			tax_title : tax_title,
			tax_content : tax_content,
			memo : memo,
			coupon_user_id : coupon_user_id,
			sp_id : sp_id
		}
	}else{
	//直接下单
		sentdata = {
			product_id : product_id,
			item_id : item_id,
			num : item_num,
			consignee_id : consignee_id,
			tax : tax,
			tax_title : tax_title,
			tax_content : tax_content,
			memo : memo,
			coupon_user_id : coupon_user_id,
			sp_id : sp_id
		}	
	}
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Order/create',
		data:sentdata,
		async: false,
		error: function(request) {
			alert("系统繁忙");
		},
		success: function(data) {
			//alert(data);return;
			if(data.error_code==0){
				/*
				art.dialog({
					time: 1,
					content: '提交成功'
				});
				*/
				//showMessage('提交成功',1000);
				if(data.order_sn){
				  window.location.href = APP+'/Order/beforepay?order_sn='+data.order_sn;
				}else{
				  window.location.href = APP+'/Order/beforepay?mod_sn='+data.mod_sn;
				}				
				return;
			}else{
				alert(data.notice);
			}
		}
	});
}

/* 列表页 */

//取消订单
function cancel(order_sn){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Order/cancel',
		data:"order_sn="+order_sn,
		async: false,
		error: function(request) {
			//alert("系统繁忙");
			showMessage('系统繁忙',1000);
		},
		success: function(data) {
			if(data.error_code==0){
				showMessage('取消成功',1000);
				setTimeout(function(){
					location.reload();			
				},1500);
				return;
			}else{
				//alert(data.notice);
				showMessage(data.notice,2000);
			}
		}
	});
}

//删除订单
function del(order_sn){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Order/delete',
		data:"order_sn="+order_sn,
		async: false,
		error: function(request) {
			showMessage('系统繁忙',1000);
		},
		success: function(data) {
			if(data.error_code==0){
				showMessage('删除成功',1000);
				setTimeout(function(){
					location.reload();			
				},1500);
				return;
			}else{
				//alert(data.notice);
				showMessage(data.notice,2000);
			}
		}
	});
}

//付款
function payment(order_sn){
	//alert(order_sn);return;
	window.location = APP+'/Order/beforepay?order_sn='+order_sn;
}

//评价
function feedback(order_sn){
	window.location = APP+'/Order/feedback?order_sn='+order_sn;
}

//退款申请
function refund(order_sn){
	window.location = APP+'/Order/refund?order_sn='+order_sn;
}

//查看物流
function shipping(order_sn){
	window.location.href = APP+'/Order/shipping?order_sn='+order_sn;
}

//退货单填写
function return_order(order_sn){
	window.location.href = APP+'/Order/return_order?order_sn='+order_sn;
}

//退货单填写
function return_item(order_sn){
	window.location.href = APP+'/Order/return_item?order_sn='+order_sn;
}

//退货单填写
function finish(order_sn){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Order/finish',
		data:"order_sn="+order_sn,
		async: false,
		error: function(request) {
			showMessage('系统繁忙',1000);
		},
		success: function(data) {
			if(data.error_code==0){
				showMessage('确认成功',1000);
				setTimeout(function(){
					location.reload();			
				},1500);
				return;
			}else{
				showMessage(data.notice,2000);
			}
		}
	});
}