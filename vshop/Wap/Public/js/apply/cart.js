$(document).ready(function(){
  $('.owe_on').click(function(){
	 if($(this).attr('class')=='owe_on'){
		$(this).attr('class','owe_no');
	 }else{
		$(this).attr('class','owe_on');
	 }
	 //价格信息更新
	 amount_update();
  });
  $('#msg').click(function(){
    sent();
  });
  $('#check_all').click(function(){
	  //alert($(this).attr('class'));
	  var self = $(this);
	  var className = self.attr('class');
	  if(className=='c_all'){
		self.attr('class','c_no');
		$('div .owe_on').attr('class','owe_no');
	  }else{
		self.attr('class','c_all');
		$('div .owe_no').attr('class','owe_on');
	  }
	  //价格信息更新
	  amount_update();
  });

});

/* 数量加减
 * var type 1减 2加
 * var id 产品id
*/
function update_num(type,id){
  if(type==1){
	 if(Number($('#shu'+id).val())>1)$('#shu'+id).val(Number($('#shu'+id).val())-1);
  }else{
	 if(Number($('#shu'+id).val())<$('#shu'+id).attr('stock')){
		 $('#shu'+id).val( Number($('#shu'+id).val())+1);
	 }else{
		/*
		art.dialog({
			id : 'num',
			time: 2,
			content: '超过库存数量'
		});
		*/
		showMessage('超过库存数量',2000);
	 }
  }
    var sentdata = {
		id : id,
		num : $('#shu'+id).val()
	}
	//更新购物车数量
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Cart/update',
		data:sentdata,
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {

		}
	});  
  //价格信息更新
  amount_update();
}

//价格更新
function amount_update(){
  var num = 0;
  var amount = 0;
  $('.car_box').each(function(){
	var obj = $(this).children(":first");
	var className = obj.attr('class');
	var item_id = obj.attr('item_id');
	var cart_id = obj.attr('cart_id');
	var item_price = obj.attr('item_price');
	var item_num = $('#shu'+cart_id).val();
	//alert(Number(item_price));return;
	//被选中
	if(className=='owe_on'){
	  num += 1;
	  //alert(Number(item_id));
	  amount += Number(item_price)*Number(item_num);
	}
  });
  var text = '<em>全选</em> 合计： '+amount+' 元<input type="button" value="去结算（'+num+'）" class="gjiesuan" >';
  $('#msg').html(text);
}

//提交信息
function sent(){
  var carts = '';
  $('.car_box').each(function(){
	var obj = $(this).children(":first");
	var className = obj.attr('class');
	var cart_id = obj.attr('cart_id');
	var item_id = obj.attr('item_id');
	var item_num = $('#shu'+cart_id).val();
	//被选中
	if(className=='owe_on'){
	  if(carts==''){
	    carts = cart_id;
	  }else{
		carts += ','+cart_id;
	  }

	}
  });
  //base64加密
  //alert(carts);
  //var carts = base64_encode(carts);
  window.location.href = APP+'/Order/confirm?cart_ids='+carts;
}

//删除购物车
function item_del(){
  $('.car_box').each(function(){
	var cart = $(this);
	var obj = $(this).children(":first");
	var className = obj.attr('class');
	var cart_id = obj.attr('cart_id');
	//被选中
	if(className=='owe_on'){
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:APP+'/Cart/delete',
			data:'cart_ids='+cart_id,
			async: false,
			error: function(request) {
				alert("Connection error");
			},
			success: function(data) {
				//alert(data);
				if(data.error_code==0){
					/*
					art.dialog({
						time: 1.5,
						content: '删除购物车成功'
					});
					*/
					showMessage('删除购物车成功',2000);
					$(cart).remove();
					amount_update();
					return;
				}else{
					alert(data.notice);
				}
			}
		});	
	}
  });

}