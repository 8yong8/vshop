/* 搜索页 */
//组装URL
function search(){
	var go_url = APP+'/Product/lists?';

	$('#cat_id a').each(function(){
		var className = $(this).attr('class');
		if(className=='cur'){
			cat_id = $(this).attr('value');
		}
	});

	$('#cat_id2 a').each(function(){
		var className = $(this).attr('class');
		if(className=='cur'){
			cat_id = $(this).attr('value');
		}
	});

	$('#cat_id3 a').each(function(){
		var className = $(this).attr('class');
		if(className=='cur'){
			cat_id = $(this).attr('value');
		}
	});
	if(cat_id){
	  go_url += '&cat_id='+cat_id;
	}
	$('#brand_id a').each(function(){
		var className = $(this).attr('class');
		if(className=='cur'){
			brand_id = $(this).attr('value');
		}
	});
	if(brand_id){
	  go_url += '&brand_id='+brand_id;
	}
	if(size){
	  go_url += '&size='+size;
	}
	window.location = go_url;
}

//设置值
function set_val(field,val){
  selected = true; //默认选中读取子分类
  $('#'+field+' li').each(function(){
	 var obj = $(this).find('a');
     var id = obj.attr('id');
	 //alert(field+'_'+val+'/'+id);
	 if(id==field+'_'+val){
	   if(obj.attr('class')=='cur'){
	     obj.attr('class','');
		 eval(field+' = "0"');
		 selected = false;
		 if(field=='cat_id'){
		   $('#cat_id2 li a').attr('class','');
		   $('#cat_id3 li a').attr('class','');
		 }else if(field=='cat_id2'){
		   $('#cat_id3 li a').attr('class','');
		 }
	   }else{
	     obj.attr('class','cur');
	   }
	 }else{
	   obj.attr('class','');
	 }
  });
  //获取子分类
  if((field=='cat_id' || field=='cat_id2') && selected){
	  //alert(URL+'/product/cate/list');
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Product/get_cat',
		data:'pid='+val,
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
			//alert(data.cate_list);return;
			if(data.cate_list.length>0){
			  var cate_list = data.cate_list;
			  var level = cate_list[0].lv;//分类等级
			  var html = '<div class="sx_list_tj cate_list"><div class="sxtj_left">分类：</div><div class="sxtj_right2"><ul id="cat_id'+level+'">';
			  for(i=0;i<cate_list.length;i++){
				 html += '<li><a href="javascript:;" onclick="set_val(\'cat_id'+level+'\','+cate_list[i].id+');" id="cat_id'+level+'_'+cate_list[i].id+'" value="'+cate_list[i].id+'">'+cate_list[i].name+'</a></li>';				  
			  }
			  html += '</ul></div></div>';
			  $('#'+field).parent().parent().nextAll('.cate_list').remove();
			  $('#'+field).parent().parent().after(html);
			}
		}
	});  
  }
}

/* 详情页 */

//设置哪种模式购买
function set_type(i){
	$('#light').show();
	$('#fade').show();
	type = i;
}

//2个规格商品id交集
function cros(arr1,arr2){
    var hash={}, result=[];
    for(var i=0;arr1[i]!=null;i++)hash[arr1[i]]=true;
    for(var i=0;arr2[i]!=null;i++){
        if(hash[arr2[i]]){
            result.push(arr2[i])
        }
    }
    return result
}

//购买数量  type 1减 2加
function update_num(type){
  if(type==1){
	 if(Number($('#shu').val())>1)$('#shu').val(Number($('#shu').val())-1);
  }else{
	 if(Number($('#shu').val())<stock){
		 $('#shu').val( Number($('#shu').val())+1);
	 }else{
		/*
		art.dialog({
			id : 'add_num',
			time: 2,
			content: '超过库存数量'
		});
		*/
		showMessage('超过库存数量',2000);
	 }
  }
}

/*
 * 产品收藏
 * var type 1收藏 2取消
 * var source 来源
 * var id 产品id
*/
function favorite(type,source,id){
	if(type==1){
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:APP+'/Collect/add',
			data:'source='+source+'&sourceid='+id+'&type=0',
			async: false,
			error: function(request) {
				showMessage('系统繁忙',1500);
			},
			success: function(data) {
				if(data.error_code==0){
					/*
					art.dialog({
						time: 1.5,
						content: '收藏成功'
					});
					*/
					$('#favorite').removeClass('ysc');
					$('#favorite').attr('class','sc');
					$('#favorite').attr('onclick','favorite(2,"'+source+'",'+data.id+')');
					showMessage('收藏成功',1500);
					return;
				}else{
					showMessage(data.notice,1500);
				}
			}
		});	
	
	}else{
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:APP+'/Collect/delete',
			data:'id='+id,
			async: false,
			error: function(request) {
				showMessage('系统繁忙',1500);
			},
			success: function(data) {
				//alert(data);
				if(data.error_code==0){
					/*
					art.dialog({
						time: 1.5,
						content: '取消成功'
					});
					*/
					showMessage('取消成功',1500);
					$('#favorite').removeClass('sc');
					$('#favorite').attr('class','ysc');
					$('#favorite').attr('onclick','favorite(1,"'+source+'",'+product_id+')');
					return;
				}else{
					showMessage(data.notice,1500);
				}
			}
		});
	}


}