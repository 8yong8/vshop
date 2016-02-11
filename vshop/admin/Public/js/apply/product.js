/**
 *分类处理 
 */

//显示分类
function nb_category(pid, e){
    $(e).parent().nextAll('div').empty();
    $(e).parent().nextAll('div').css('background', '#EFF7FE');
    $(e).parent("div ").find("a ").removeClass("hover ");
    $(e).addClass("hover ");
    var strHTML = "";
    $.each(JsonCategory, function(InfoIndex, Info){
    if (pid == Info.pid)
        strHTML += " <a href = 'javascript:void(0)' onclick = 'nb_category(" + Info.id + ",this)' id = " + Info.id + " > " + Info.name + " </a>";
    });
	//alert(pid);
    if (pid == 0){
    	$(".root").html(strHTML);
    } else{
    	$(e).parent().next('div').css('background', '#FFF');
        $(e).parent().next('div').html(strHTML);
    }

}
// 判断是否重复选择
function nb_check_add_cat(val) {
	var arr = [];
	$('input[name="cat_id"]').each(function() {
		arr.push($(this).val());
	});
	var t1 = arr.sort().toString();
	var t2 = $.unique(arr).sort().toString();
	if (t1 == t2) {
		return false;
	} else {
		return true;
	}
}
//监控
$(function() {
	//添加分类
	$("#add_cat").on("click", function() {
		var id = $('.fendd').find('.hover:last').attr("id");
		if ($('.fendd .hover').length > 1) {
			$(".sl").append("<div>" + $('.fendd').find('.hover:last').html() + "<em><img src='"+PUBLIC+"/images/ico_close1.png' /></em><input name='cat_id' value='" + $('.fendd').find('.hover:last').attr("id") + "' type='hidden'></div>");
		}
		if (nb_check_add_cat(id)) {
			$(".sl div:last").remove();
			alert('已选择过此类别!');
			return;
		}

	});
	//分类双击事件监控
	$('.fendd a').on("dblclick", function() {
		if($('input[name="cat_id"]').length>0){
		  //只能选择一个分类,移除覆盖
		  $('input[name="cat_id"]').parent().remove();
		}
		if ($(this).parent().nextAll('div').text() == "") {
			$("#add_cat").click();
		} else {
			return;
		}
	});
	//删除分类
	$(".sl div em").on("click", function() {
		$(this).parent("div").remove();
	});
}); 

function prom(val){

}

/**
 *商品图册
 */
$(function() {
	//选择默认
	$(".filelist li img").on('click', function() {
		$(".filelist li").css("border", "1px solid #3b72a5");
		$(".filelist li").find("span").hide();
		$(this).parent().css("border", "1px solid #f48c3a");
		$(this).parent().find(".setdef").show();
		//排序
		$(this).parent().siblings().attr("order", "100");
		$(this).parent().attr("order", "99");
		var li = $('.filelist li').toArray().sort(function(a, b) {
			return parseInt($(a).attr("order")) - parseInt($(b).attr("order"));
		});
		$('.filelist').html(li);
	});
	//显示删除
	$(".filelist li").on({
		mouseenter: function() {
			$(this).find('.setdel').addClass('trconb');
			$(this).find('.setdel').show();
		},
		mouseleave: function() {
			$(this).find('.setdel').removeClass('trconb');
			$(this).find('.setdel').hide();
		}
	});
	//删除确认
	$(".setdel").on('click', function() {
		if (confirm('是否删除图片?')) {
			//alert($(this).attr('data-id'));
			var my = $(this);
			var picid = $(this).attr('data-id');
			if(picid!=null){
			  $.ajax({
				type:"POST",
				dataType:'json',
				url: URL+"/ajax_delpic",
				data:"pid="+picid,
				success:function(obj){
				  if(obj.error_code=='0'){
					my.parent().remove("li");
				  }else{
					  art.dialog({
						id  :'del',
						time: 1.5,
						content: obj.notice
					  });		
				  }
				}
			  })  			
			}else{
			  $(this).parent().remove("li");
			}
			return;
		}

	})
});


/**
 *添加规格 
 */
//var tempUrl = URL+'/search_spec';
function selSpec(product_type) {
	var model_id = $('[name="model_id"]').val();
	var goods_id = $('[name="id"]').val();
	var url = URL+'/search_spec/product_type/'+product_type;
	//alert(url);
	art.dialog.open(url, {
		title: '设置商品的规格',
		background: '#ddd',
		opacity: 0.3,
		okVal: '生成所有规格',
		width: 620,
		ok: function(iframeWin, topWin) {
			//货品是否已经存在
			if ($('input:hidden[name^="_spec_array"]').length > 0) {
				if ($('#goodsBaseBody tr').length > 1) {
					$("#goodsBaseBody tr").remove();
					//initProductTable();
				}

			}
			//添加的规格
			var addSpecObject = $(iframeWin.document).find('.spec_values .hover');
			//alert(addSpecObject.length);
			if (addSpecObject.length == 0) {
				//initProductTable();
				return;
			}
			//开始遍历规格
			var specValueData = {}
			var specData = {};
			var selectedNewItem = [];
			var specIds = {};
			addSpecObject.each(function() {
				if ($(this).hasClass('addsx') == true) {	// 如果是全选则排除添加属性这个<li>
					return true;
				}
				var data_id = parseInt($(this).attr('data-id'));
				var data_name = $(this).attr('data-name');
				var data_value = $(this).attr('data-value');
				var data_value_id = $(this).attr('val-id');
				var data_type = $(this).attr('data-type');
				
				if (typeof(specIds[data_value]) == 'undefined') {
					specIds[data_value] = [];
				}
				specIds[data_value].push(data_value_id);
				if (typeof(specValueData[data_id]) == 'undefined') {
					specValueData[data_id] = [];
				}
				//已属性id组装
				specValueData[data_id].push(data_value);
				specData[data_id] = {
					'id': data_id,
					'name': data_name,
					'type': data_type
				};
				selectedNewItem.push({
					'id': data_id,
					'value': data_value,
					'value_id': data_value_id,
					'type': data_type
				});
			});

			selectedItem = selectedNewItem;
			
			//生成货品的笛卡尔积
			var specMaxData = descartes(specValueData, specData);

			//从表单中获取默认商品数据
			var productJson = {};
			if(!default_sn){
			  default_sn = $('#sn').val();
			}
			productJson['sn'] = default_sn;
			//alert(default_sn);
			//生成最终的货品数据
			var productList = [];
			var html;
			//alert(specMaxData.length);
			for (var i = 0; i < specMaxData.length; i++) {
				var html2 = '';
				var c = false;
				var productItem = {};
				for (var index in productJson) {
					//自动组建货品
					if (index == 'sn') {
						//值为空时设置默认货号

						if (productJson[index] == '') {
							productJson[index] = defaultProductNo;
						}

						if (productJson[index].match(/(?:\-\d*)$/) == null) {
							//正常货号生成
							productItem['sn'] = productJson[index] + '-' + (i + 1);
						} else {
							//货号已经存在则替换
							productItem['sn'] = productJson[index].replace(/(?:\-\d*)$/, '-' + (i + 1));
						}

					} else {
						productItem[index] = productJson[index];
					}
				}
				var product_attr_value = '';//组装属性
				for(var j = 0; j < specMaxData[i].length; j++){
				  product_attr_value += ';'+specMaxData[i][j].value;
				  var a = specMaxData[i][j].value;
				  html2 += '<TD scope="col"><div align="center">'+specMaxData[i][j].value+'</div><INPUT TYPE="hidden" NAME="product_attr[-1]['+specMaxData[i][j].id+'][]" value="'+specMaxData[i][j].value+'"><INPUT TYPE="hidden" NAME="product_attr_name[-1]['+specMaxData[i][j].id+'][]" value="'+specMaxData[i][j].name+'"><INPUT TYPE="hidden" NAME="attr_val_id[-1]['+specMaxData[i][j].id+'][]" value="'+specIds[a]+'"></TD>';
				}
				product_attr_value = product_attr_value.substring(1);
				if(products.length>0){
					for(var k =0;k<products.length;k++){
					  //判断属性是否已存在
					  if(products[k].product_attr_value==product_attr_value){
						  products[k].product_attr_value;
						  c = true;
						  continue;
					  }
					}
				}
				if(c==true){
				  continue;
				}
				if(!default_price){
				  default_price = $('#price').val();
				}
				html += '<TR><TD><input type="checkbox" name="key" value="-1"></TD><TD><INPUT TYPE="text" NAME="spec_barcode[-1][]" value="" style="width:50%" placeholder="请输入条形码" ></TD><TD><INPUT TYPE="text" NAME="spec_sn[-1][]" value="'+default_sn+'" style="width:50%" placeholder="货号" ></TD><TD><INPUT TYPE="text" NAME="spec_stock[-1][]" value="1"></TD>';
				html += html2;//添加属性信息
				html += '<TD><INPUT TYPE="text" NAME="spec_price[-1][]" value="'+default_price+'" style="width:50%"></TD><TD><INPUT TYPE="text" NAME="spec_pic[-1][]" id="pic2_'+i+'" value=""> <font class="uplogo" style="cursor: pointer;left:300px;line-height: 22px;" onclick="upyunPicUpload(\'pic2_'+i+'\',300,300)">选择</font> <font class="uplogo" style="cursor: pointer;line-height: 22px;left:330px;" onclick="viewImg(\'pic2_'+i+'\')">预览</font></TD><TD><a href="javascript:void(0)" onclick="product_del2(this);">删除</a></TD></TR>';
				//productItem['spec_array'] = specMaxData[i];//属性分类
				//productList.push(productItem);
			}
			$('#goodsBaseBody').append(html);
			return;
		}
	});
	
}

//笛卡儿积组合
function descartes(list, specData) {
	//parent上一级索引;count指针计数
	var point = {};
	var result = [];
	var pIndex = null;
	var tempCount = 0;
	var temp = [];
	//根据参数列生成指针对象
	for (var index in list) {
		if (typeof list[index] == 'object') {
			point[index] = {
				'parent': pIndex,
				'count': 0
			}
			pIndex = index;
		}
	}
	//单维度数据结构直接返回
	if (pIndex == null) {
		return list;
	}

	//动态生成笛卡尔积
	while (true) {
		for (var index in list) {
			tempCount = point[index]['count'];
			temp.push({
				"id": specData[index].id,
				"type": specData[index].type,
				"name": specData[index].name,
				"value": list[index][tempCount]
			});
		}
		//压入结果数组
		result.push(temp);
		temp = [];
		//检查指针最大值问题
		while (true) {
			if (point[index]['count'] + 1 >= list[index].length) {
				point[index]['count'] = 0;
				pIndex = point[index]['parent'];
				if (pIndex == null) {
					return result;
				}

				//赋值parent进行再次检查
				index = pIndex;
			} else {
				point[index]['count'] ++;
				break;
			}
		}
	}
}

/**
 * 清除规格
 */
function delAll() {
	if (confirm('是否真要清空?')) {
		//$("#goodsBaseBody tr").remove();
		var checkeds = $('input[name="key"]');
		//alert(checkeds.length);
		var item_id_str = '';
		checkeds.each(function(i,v) {
			// 获取当前产品的id
			//alert($(this).val());
			if($(this).val()>0){
			  item_id_str += ','+$(this).val();
			}else{
			  $(this).parent().parent().remove();
			}
		})
		var item_ids = item_id_str.substring(1);
		if(item_id_str)product_del(item_ids);
	}
}

/**
 * 删除选中的规格
 */
function delChecked() {
	var checkeds = $('input[name="key"]:checked');
	if (checkeds.length < 1) {
		alert('请选择您要删除的规格商品');
		return false;
	}
	if (confirm('删除选中的规格，确定?')) {
		if(checkeds.length == $("input[name='key']").length){
            delAll();
            return false;
        }
		var item_id_str = '';
		checkeds.each(function(i,v) {
			// 获取当前产品的id
			item_id_str += ','+$(this).val();
		})
		var item_ids = item_id_str.substring(1);
		product_del(item_ids);
	}
}

//删除货品
function product_del(id){
  if(confirm("您确定要删除吗？")){
	  $.ajax({
		type:"POST",
		dataType:'json',
		url: URL+"/product_del",
		data:"id="+id+'&product_id='+product_id,
		success:function(obj){
		  if(obj.error_code=='0'){
			/*
		    art.dialog({
			  id  :'del',
			  time: 1.5,
			  content: obj.notice
		    });
			*/
		    $('#tr_'+id).remove();
			ss = id.split(",");
			for(i=0;i<ss.length;i++){
			  //document.write (ss[i]);
			  $('#tr_'+ss[i]).remove();
			} 
		  }else{
			  art.dialog({
			    id  :'del',
				time: 1.5,
				content: obj.notice
			  });		
		  }
		}
	  })  
  }
}

//移除未存数据库的货品
function product_del2(obj){
  $(obj).parent().parent().remove(); 
}

/*属性添加*/