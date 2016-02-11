//组装URL
function search(){
	//分类
	if(cate_id3!=null){
		cate_id = cate_id3;
	}else if(cate_id2!=null){
		cate_id = cate_id2;
	}
	var go_url = URL+'product/product/list?';
	//alert(cate_id);
	if(area!==''){
	  go_url += '&area='+area;
	}

	if(cate_id){
	  go_url += '&cate_id='+cate_id;
	}
	if(brand_id){
	  go_url += '&brand_id='+brand_id;
	}
	if(size){
	  go_url += '&size='+size;
	}
	window.location = go_url;
	//alert(cate_id);
}
//设置值
function set_val(field,val){
  //区域改变
  if(field=='area' && val!=area){
	  $.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:URL+'product/cate/list',
			data:'area='+val+'&level=1',
			async: false,
			error: function(request) {
				alert("Connection error");
			},
			success: function(data) {
				if(data.cate_list.length>0){
				  var cate_list = data.cate_list;
				  var level = cate_list[0].level;//分类等级
				  $('.cate_list').remove();//清除分类
				  var html = '<div class="sx_list_tj cate_list"><div class="sxtj_left">分类：</div><div class="sxtj_right"><ul id="cate_id">';
				  for(i=0;i<cate_list.length;i++){
					 html += '<li><a href="javascript:;" onclick="set_val(\'cate_id\','+cate_list[i].id+');" id="cate_id_'+cate_list[i].id+'">'+cate_list[i].cate_name+'</a></li>';				  
				  }
				  html += '</ul></div></div>';
				  $('#area_div').after(html);
				}
			}
	});   
  }
  //alert(field);
  selected = true; //默认选中读取子分类
  eval(field+' = '+val);
  $('#'+field+' li').each(function(){
	 var obj = $(this).find('a');
     var id = obj.attr('id');
	 if(id==field+'_'+val){
	   if(obj.attr('class')=='cur'){
	     obj.attr('class','');
		 eval(field+' = "0"');
		 selected = false;
	   }else{
	     obj.attr('class','cur');
	   }
	 }else{
	   obj.attr('class','');
	 }
  });

  //获取子分类
  if((field=='cate_id' || field=='cate_id2') && selected){
	  //alert(URL+'/product/cate/list');
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:URL+'product/cate/list',
		data:'parent_id='+val,
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
			if(data.cate_list.length>0){
			  var cate_list = data.cate_list;
			  var level = cate_list[0].level;//分类等级
			  var html = '<div class="sx_list_tj cate_list"><div class="sxtj_left">分类：</div><div class="sxtj_right"><ul id="cate_id'+level+'">';
			  for(i=0;i<cate_list.length;i++){
				 html += '<li><a href="javascript:;" onclick="set_val(\'cate_id'+level+'\','+cate_list[i].id+');" id="cate_id'+level+'_'+cate_list[i].id+'">'+cate_list[i].cate_name+'</a></li>';				  
			  }
			  html += '</ul></div></div>';
			  $('#'+field).parent().parent().nextAll('.cate_list').remove();
			  $('#'+field).parent().parent().after(html);
			}
		}
	});  
  }
}