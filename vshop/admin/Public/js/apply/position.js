/*列表*/

function ajax_sub_del(id){
	
	if (window.confirm('确实要删除选择项吗？删除将不可恢复'))
	{
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:URL+"/ajax_sub_del/time/"+new Date().getTime(),
			data:'id='+id,
			async: false,
			error: function(request) {
				//showMessage('系统繁忙',2000);
					art.dialog({
						time: 2,
						content: '系统繁忙'
					});
			},
			success: function(data) {
				//alert(data.notice);return;
				if(data.error_code==0){
					if(data.notice!=null){
						art.dialog({
							id: 'ajax_del',
							time: 2,
							content: data.notice
						});
					}
					$('#row_'+id).remove();
				}else{
					art.dialog({
						id: 'ajax_del',
						time: 2,
						content: data.notice
					});
				}
			}
		});
	}

}

function sub_forbid(id){
	var keyValue;
	if (id){
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue){
		alert('请选择删除项！');
		return false;
	}
     window.location.href = URL+"/sub_forbid/id/"+keyValue;
}


function sub_resume(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要还原的项目！');
		return false;
	}
    window.location.href = URL+"/sub_resume/id/"+keyValue;
}

function sub_add(id){
  window.location.href = URL+"/sub_add/id/"+id;
}


/*添加编辑*/
//关联修改
function change(){
  var val = $('#data_type').val();
  if(val=='product_list'){
	  $('.list').show();
	  $.ajax({
		type:"POST",
		dataType:'json',
		url: APP+"/Product_category/lists",
		data:"ajax=1",
		error: function(request) {
			art.dialog({
				time: 1,
				content: '系统繁忙'
			});
		},
		success:function(obj){
		  //alert(obj);
		  var _html = '';
		  for(var i=0;i<obj.length;i++){
		    _html += '<option value="'+obj[i].id+'">'+obj[i].node_name+'</option>';
		  }
		  $('#cat_id').html(_html);
		}
	  })  
  }else if(val=='article_list'){
      $('.list').show();
	  $.ajax({
		type:"POST",
		dataType:'json',
		url: APP+"/Classify/lists",
		data:"ajax=1",
		error: function(request) {
			art.dialog({
				time: 1,
				content: '系统繁忙'
			});
		},
		success:function(obj){
		  //alert(obj);
		  var _html = '';
		  for(var i=0;i<obj.length;i++){
		    _html += '<option value="'+obj[i].id+'">'+obj[i].node_name+'</option>';
		  }
		  $('#cat_id').html(_html);
		}
	  })
  }else{
    $('.list').hide();
  }
}