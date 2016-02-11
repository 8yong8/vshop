$(function() {
	//类型选择
	$("#prom_type").live("change",function(){
		  var type = parseInt($("#prom_type").val());
		  var expression = '';
		  switch(type){
		    case 1:{
		      expression = '<li><b class="red">*</b>优惠金额： <input name="award_value" type="text" class="small"  value="" datatype="num" /> <label>立减金额（元）</label> </li>';
		      break;
		    }
		    case 2:{
		      expression = '<li><b class="red">*</b>折扣： <input name="award_value" type="text" class="small"  value="" datatype="numrange" min="1"  max="99" /> <label>% 折扣值(1-100 如果打9折，请输入90)</label> </li>';
		      break;
		    }
		    case 3:{
		      expression = '<li><b class="red">*</b>倍数： <input name="award_value" type="text" class="small"  value="" datatype="n" /> <label>商品送积分的倍数!</label> </li>';
		      break;
		    }
		    case 4:{
		      _html='';
				$.ajax({
					cache: true,
					dataType:'json',
					type: "POST",
					url:APP+'/Coupon/lists/ajax/1',
					data:'',
					async: false,
					error: function(request) {
						alert("系统繁忙");
					},
					success: function(coupons_list) {
						  $.each(coupons_list,function(index,item){
							_html+='<option value="'+item.id+'">'+item.title+'</option>'
						  });
						  expression = '<li><b class="red">*</b>优惠券：<select name="award_value">'+ _html +'</option>{/query}</select></li>';
						 
					}
				});
				break;
		    }
		    case 5:{
		      expression = '';
		      break;
		    }
		  }
		  $("#expression").html(expression);
	});
	//初始选中
	$("#prom_type").attr("value",award_type).trigger("change");
	$("[name='award_value']").val(award_value);
})
//选择商品
function goods_search(id){
	idArr = [];
	art.dialog.open(URL+'/product_list/pid/'+id, {
		title: '选择商品',
		background: '#ddd',
		opacity: 0.3,
		width: '875px',
		okVal:'确认所选商品',
		ok:function(iframeWin, topWin){
			var rs = $(iframeWin.document).find('.ids:checked');
			//alert($("#goodsBaseBody tr").length);
			var num = $("#goodsBaseBody tr").length;
			goodsArr = [];
			var _html;
			rs.each(function() {
				//alert($(this).attr('data-id'));
				var data_id = $(this).attr('data-id');
				var data_name = $(this).attr('data-name');
				var data_price = $(this).attr('data-price');
				var data_stock = $(this).attr('data-stock');
				if($.inArray(data_id, idArr) == -1){
					idArr.push(data_id)
					goodsArr.push({
						'id': data_id,
						'name': data_name,
						'price': data_price,
						'stock': data_stock
					});
				}
				if(num%2==1){
				  var class_name = 'odd';
				}else{
				  var class_name = 'even';
				}
				num++;
				//var class_name = 'even';
				_html += '<tr class="'+class_name+'"><td class="mo_text">'+data_name+'</td><td>'+data_price+'</td><td>'+data_stock+'</td><td><input name="product_id[]" type="hidden" value="'+data_id+'"><a href="javascript:void(0)" onclick="$(this).parent().parent().remove();">删除</a></td></tr>';
			});
			$('#goodsBaseBody').append(_html);
		}
	})
}
/**
 * 清除规格
 */
function delAll() {
	if (confirm('是否真要清空?')) {
	  idArr = Array();
	  $("#goodsBaseBody tr").each(function(){
		if($(this).attr('data-id')){
		  idArr.push($(this).attr('data-id'));
		}
	  });
	  var id_str = idArr.toString();
	  if(id_str)ajax_del(id_str);
	  $("#goodsBaseBody tr").remove();
	}
}