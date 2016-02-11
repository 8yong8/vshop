//城市
function city_child(id,obj){
	var self = $(obj);
	var class_name = self.attr('class');
	if(self.hasClass("tree-collapsed")){
	  //展开
	  self.removeClass("tree-collapsed");
	  self.addClass("tree-expanded");
	  if($('.treegrid-tr-tree_'+id).length>0){
		//$('.treegrid-tr-tree_'+id).remove();
		$('.treegrid-tr-tree_'+id).show();
	    return false;
	  }
	}else{
	  //收回
	  self.removeClass("tree-expanded");
	  self.addClass("tree-collapsed");
	  $('.treegrid-tr-tree_'+id).hide();
	  //下面子节点也关闭
	  $('.treegrid-tr-tree_'+id).each(function(){
	    var tr_id = $(this).attr('id');
		var child_id = tr_id.replace(/[^\d]/g,'');
		$('.treegrid-tr-tree_'+child_id).hide();
		//子节点左侧标签关闭
	    $('#row_'+child_id).find('.tree-hit').removeClass("tree-expanded");
	    $('#row_'+child_id).find('.tree-hit').addClass("tree-collapsed");
	  });
	  return false;
	}
	$.ajax({
		cache: true,
		//dataType:'json',
		type: "POST",
		url:URL+'/city_child',
		data:'pid='+id,
		async: false,
		error: function(request) {
			//showMessage('系统繁忙',2000);
				art.dialog({
					time: 2,
					content: '系统繁忙'
				});
		},
		success: function(html) {
			$('#row_'+id).after(html);
		}
	});

}
//区域列表
function county_child(id,obj){
	var self = $(obj);
	var class_name = self.attr('class');
	if(self.hasClass("tree-collapsed")){
	  //展开
	  self.removeClass("tree-collapsed");
	  self.addClass("tree-expanded");
	  if($('.treegrid-tr-tree_'+id).length>0){
		//$('.treegrid-tr-tree_'+id).remove();
		$('.treegrid-tr-tree_'+id).show();
	    return false;
	  }
	}else{
	  //收回
	  self.removeClass("tree-expanded");
	  self.addClass("tree-collapsed");
	  $('.treegrid-tr-tree_'+id).hide();
	  return false;
	}
	$.ajax({
		cache: true,
		//dataType:'json',
		type: "POST",
		url:URL+'/county_child',
		data:'pid='+id,
		async: false,
		error: function(request) {
			//showMessage('系统繁忙',2000);
				art.dialog({
					time: 2,
					content: '系统繁忙'
				});
		},
		success: function(html) {
			//alert(html);
			$('#row_'+id).after(html);
		}
	});

}