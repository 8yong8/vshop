//修改状态
function order_update(action,field,val,id,callback){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:URL+'/'+action,
		data:'field='+field+'&val='+val+'&id='+id,//数据
		async: false,
		error: function(request) {
				art.dialog({
					time: 1,
					content: '系统繁忙'
				});
		},
		success: function(data) {
			//alert(data);return;
			if(data.error_code==0){
				art.dialog({
					time: 2,
					content: data.notice
				});
				if(callback){
				  callback;
				}else if(data.callback){
				  if(data.obj_id){
				    $('#'+data.obj_id).html(data.callback);
				  }else{
					$('#td_'+id).html(data.callback);
				  }
				  
				}
			}else{
				art.dialog({
					time: 2,
					content: data.notice
				});
			}
		}
	});
}

//确认订单
function confirm_order(){

}

//发货
function deliver(id){
  art.dialog.data('width', '600');
  art.dialog.data('height','500');
  art.dialog.open(
	  APP+'?&c=Order&a=deliver&id='+id,
	  {
	    lock:true,
		title:'物流处理',
		width:600,
		height:480,
		yesText:'关闭',
		background: '#000',
		opacity: 0.45
	  }
  );

}

//操作记录
function view_log(order_sn){
	$.ajax({
		cache: true,
		//dataType:'json',
		type: "GET",
		url:URL+'?&c=OrderTrack&a=lists&order_sn='+order_sn,
		data:'',//数据
		async: false,
		error: function(request) {
				art.dialog({
					time: 1,
					content: '系统繁忙'
				});
		},
		success: function(ret) {
			var _html = ret;
			art.dialog({
				id:'view_log',
				title:'订单操作详情',
				fixed:true,
				lock:true,
				content:_html,
				ok:true
			});
		}
	});
}