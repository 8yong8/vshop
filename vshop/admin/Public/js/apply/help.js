//城市
function help_child(id,obj){
	//alert(id);
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
		url:URL+'/help_child',
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