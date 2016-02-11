//ajax提交表单
function sendForm(id,url,callback){
	var status = true;
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:url,
		data:$('#'+id).serialize(),// 你的formid
		async: false,
		error: function(request) {
			//showMessage('系统繁忙',2000);
				art.dialog({
					time: 2,
					content: '系统繁忙'
				});
		},
		success: function(data) {
			if(data.error_code==0){
				art.dialog({
					id: 'sendForm',
					time: 2,
					content: data.notice
				});
				if(callback){
				  callback;
				}else if(data.callback){

				}else if(data.gourl){
					setTimeout(function(){
					  window.location = data.gourl;
					},1000);
				}
				status = true;
			}else{
				art.dialog({
					time: 2,
					content: data.notice
				});
				status =  false;
			}
		}
	});
	return status;
}


/**
+--------------------------
 * AJAX修改功能
 * 修改表字段值
+--------------------------
 * @param obj  对象
 * @param act  操作方法
 * @param id   id
 */

function field_value_update(obj, field, id)
{
  var tag = obj.firstChild.tagName;

  if (typeof(tag) != "undefined" && tag.toLowerCase() == "input")
  {
    return;
  }
  
  /* 保存原始的内容 */
  var self = obj;
  var org = $(obj).html();
  var val = $(obj).text();
  //var org = obj.innerHTML;
  //var val = Browser.isIE ? obj.innerText : obj.textContent;

  /* 创建一个输入框 */
  var txt = document.createElement("INPUT");
  txt.value = (val == 'N/A') ? '' : val;

  txt.style.width = (obj.offsetWidth + 12) + "px" ;
  //txt.style.width = (obj.offsetWidth/3) + "px" ;

  /* 隐藏对象中的内容，并将输入框加入到对象中 */
  obj.innerHTML = "";
  obj.appendChild(txt);
  txt.focus();

  /* 编辑区输入事件处理函数 */
  txt.onkeypress = function(e)
  {
    if (e.which == 13)
    {
      obj.blur();

      return false;
    }

    if (e.which == 27)
    {
      obj.parentNode.innerHTML = org;
    }
  }

  /* 编辑区失去焦点的处理函数 */
  txt.onblur = function(e)
  {
    var val = $(obj).find('input').val();
	//alert(org+'/'+val);
	//alert(val+'/'+id);
	//值不改变不提交
	if(org==val){
	  obj.innerHTML = org;
	  return;
	}
    if (val.length > 0){
	  $.ajax({
		type:"POST",
		dataType:'json',
		url: URL+"/field_value_update/",
		data:"id="+id+'&val='+val+'&field='+field,
		error: function(request) {
				art.dialog({
					time: 1,
					content: '系统繁忙'
				});
		},
		success:function(obj){
			//alert(obj);
		  if(obj.error_code==0){
		    obj.innerHTML = $(obj).find('input').val();
		  }else{
			$(self).html(org);
			art.dialog({
				time: 2,
				content: obj.notice
			});	
		  }
		}
	  })
	  obj.innerHTML = $(obj).find('input').val();
    }else{
      obj.innerHTML = org;
    }
  }
}
//全选
function CheckAll(strSection){
	var i;
	var	colInputs = document.getElementById(strSection).getElementsByTagName("input");
	for	(i=1; i < colInputs.length; i++)
	{
		colInputs[i].checked=colInputs[0].checked;
	}
}
//添加
function add(id){
	if (id)
	{
		 location.href  = URL+"/add/id/"+id;
	}else{
		 location.href  = URL+"/add/";
	}
}
//ajax添加
function ajax_add(id,w,h){
	if (id)
	{
		 var url  = URL+"/add/id/"+id;
	}else{
		 var url  = URL+"/add/";
	}
	var width = 600;
	var height = 400;
	if(w!=null){
	  width = w;
	}
	if(h!=null){
	  height = h;
	}
	  art.dialog.data('width', width);
	  art.dialog.data('height',height);
	  
	  art.dialog.open(
		  url,
		  {
			lock:true,
			title:'添加信息',
			width:width,
			height:height,
			yesText:'关闭',
			background: '#000',
			opacity: 0.45
		  }
	  );
}
//排序
function sort(id){
	var keyValue;
	if(id){
	  keyValue = id;
	}else{
	  keyValue = getSelectCheckboxValues();
	}
	location.href = URL+"/sort/sortId/"+keyValue;
}
//审核通过
function pass(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择审核项！');
		return false;
	}

	if (window.confirm('确实审核通过吗？'))
	{
		window.location.href = URL+	'/checkPass/id/'+keyValue;
	}
}
//排序
function sortBy (field,sort){
	var url = location.href.split('#')[0];
	url = url.replace(/&_order[^&]+/g, "")
	url = url.replace(/&_sort[^&]+/g, "");
	var reg = /\?/;
	var r = url.match(reg);
	if(r==null){
	  url += "?&_order="+field+"&_sort="+sort;
	}else{
	  url += "&_order="+field+"&_sort="+sort;
	}
	location.href = url;
}
//ajax缓存
function ajax_cache(){
	sendForm(URL+'/cache');
}

function cache(){
	window.location.href = URL+'/GiveCache/status/1';  
}
//禁用
function forbid(id){
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
     window.location.href = URL+"/forbid/id/"+keyValue;
}

//根据条件恢复表数据
function recycle(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择要还原的项目！');
		return false;
	}
	location.href = URL+"/recycle/id/"+keyValue;
}

//多选id
function getSelectCheckboxValues(){
	var obj = document.getElementsByName('key');
	var result ='';
	var j= 0;
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true){
				selectRowIndex[j] = i+1;
				result += obj[i].value+",";
				j++;
		}
	}
	return result.substring(0, result.length-1);
}

//恢复
function resume(id){
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
    window.location.href = URL+"/resume/id/"+keyValue;
}

function trace(id){
	location.href = URL+"/trace/id/"+id;
}
function output(){
	location.href = URL+"/output/";
}
//子列表
function child(id){
	location.href = URL+"/index/pid/"+id;
}

function action(id){
	location.href = URL+"/action/groupId/"+id;
}

function access(id){
	location.href= URL+"/access/id/"+id;
}
function app(id){
	location.href = URL+"/app/groupId/"+id;
}

function module(id){
	location.href = URL+"/module/groupId/"+id;
}
function addv(id){
		 location.href  = URL+"/addv/id/"+id;
}

function user(id){
	location.href = URL+"/user/id/"+id;
}
//页面跳转
function jump(action){
	location.href = URL+"/"+action;
}

//+---------------------------------------------------
//|	打开模式窗口，返回新窗口的操作值
//+---------------------------------------------------
function PopModalWindow(url,width,height)
{
	var result=window.showModalDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+"px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
	return result;
}

function read(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/read/id/"+keyValue;
}

function edit(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/edit/id/"+keyValue;
}

function ajax_edit(id,w,h,ext){

	if (id)
	{
		 var url  = URL+"/edit/id/"+id;
	}else{
		 alert('id必须');return;
	}
	var width = 600;
	var height = 400;
	if(w!=null){
	  width = w;
	}
	if(h!=null){
	  height = h;
	}
	//alert(width);
	  art.dialog.data('width', width);
	  art.dialog.data('height',height);
	  
	  art.dialog.open(
		  url,
		  {
			lock:true,
			title:'编辑信息',
			width:width,
			height:height,
			yesText:'关闭',
			background: '#000',
			opacity: 0.45
		  }
	  );
}

function look(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/look/id/"+keyValue;
}

function grades(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/grades/id/"+keyValue;
}
function addmoney(id){
	location.href =  URL+"/addmoney/id/"+id;
}
var selectRowIndex = Array();

function del(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？'))
	{
		window.location.href = URL+"/delete/id/"+keyValue+"/time/"+new Date().getTime();
	}
}

function ajax_del(id,obj){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？删除将不可恢复'))
	{
		if(obj)var self = obj;
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:URL+"/ajax_del/id/"+keyValue+"/time/"+new Date().getTime(),
			data:'id='+keyValue,// 你的formid
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
					if(data.callback){
					  eval(data.callback);
					}
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

function foreverdel(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		window.location.href = URL+"/foreverdelete/id/"+keyValue+"/time/"+new Date().getTime();
	}
}
function getTableRowIndex(obj){ 
	selectRowIndex[0] =obj.parentElement.parentElement.rowIndex;/*当前行对象*/
}

function doDelete(data,status){
		if (status==1)
		{
		var Table = $('checkList');
		var len	=	selectRowIndex.length;
		if(len==0){
			window.location.reload();
		}
		for (var i=len-1;i>=0;i-- )
		{
			//删除表格行
			Table.deleteRow(selectRowIndex[i]);
		}
		selectRowIndex = Array();
		}
}

function delAttach(id,showId){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要删除选择项吗？'))
	{
		$('result').style.display = 'block';
		//ThinkAjax.send(URL+"/delAttach/","id="+keyValue+'&_AJAX_SUBMIT_=1');
		window.location.href = URL+"/delAttach/id/"+keyValue+"/time/"+new Date().getTime();
		if (showId != undefined)
		{
			$(showId).innerHTML = '';
		}
	}
}

function clearData(){
	if (window.confirm('确实要清空全部数据吗？'))
	{
	location.href = URL+"/clear/";
	}
}

//生成随机数
function randomString(len) {
　　len = len || 32;
　　var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
　　var maxPos = $chars.length;
　　var str = '';
　　for (i = 0; i < len; i++) {
		str += $chars.charAt(Math.floor(Math.random() * maxPos));
　　}
　　return str;
}

//图片上传预览
function PicUpload(domid,width,height){
	art.dialog.data('width', width);
	art.dialog.data('height', height);
	art.dialog.data('domid', domid);
	art.dialog.data('lastpic', $('#'+domid).val());
	art.dialog.open(APP+'?&c=Attachment&a=local&width='+width+'&height='+height,{lock:true,title:'上传图片',width:600,height:400,yesText:'关闭',background: '#000',opacity: 0.45});
}
function viewImg(domid){
	if($('#'+domid).val()){
		var html='<img src="'+$('#'+domid).val()+'" />';
	}else{
		var html='没有图片';
	}
	art.dialog({title:'图片预览',content:html,lock:true,background: '#000',opacity: 0.45});
}
function addLink(domid,iskeyword){
	art.dialog.data('domid', domid);
	art.dialog.open('?c=Link&a=insert&iskeyword='+iskeyword,{lock:true,title:'插入链接或关键词',width:600,height:400,yesText:'关闭',background: '#000',opacity: 0.45});
}
function chooseFile(domid,type){
	art.dialog.data('domid', domid);
	art.dialog.open('?c=Attachment&a=index&type='+type,{lock:true,title:'选择文件',width:600,height:400,yesText:'关闭',background: '#000',opacity: 0.45});
}

//展示
function art_open(html){
	 art.dialog(
	   {
		 title:'信息展示',
		 content:html,
		 lock:true,
		 background: '#000',
		 opacity: 0.45
	   }
	 );
}

//关闭打开窗口
function dialog_close(){
	var list = art.dialog.list;
	for (var i in list) {
		list[i].close();
	};
}

//定时刷新
function refresh(time,url){
	setTimeout(function(){
		if(url){
			window.location = url;
		}else{
			location.reload();
		}
	},time);
}

//移除对象
function remove(id){
  if(typeof id=="object"){
    $(id).remove();
  }else{
    $('#'+id).remove();
  }
}

//预览提示
;(function(jQuery){
	$.fn.preview = function(height){
		var w = jQuery(window).width();
		//alert(w);
		var h = jQuery(window).height();
		var height = height ? height : '300px';
		$(this).each(function(){
			$(this).hover(function(e){
				if(/.png$|.gif$|.jpg$|.bmp$|.jpeg$|.JPG$/.test($(this).attr("data-bimg"))){
					$("body").append("<div id='preview'><img src='"+jQuery(this).attr('data-bimg')+"' height='"+height+"' /></div>");
				}
				var show_x = $(this).offset().left + $(this).width();
				var show_y = $(this).offset().top;
				var scroll_y = $(window).scrollTop();
				$("#preview").css({
					position:"absolute",
					padding:"4px",
					border:"1px solid #f3f3f3",
					backgroundColor:"#eeeeee",
					top:show_y + "px",
					left:show_x + "px",
					zIndex:1000
				});
				$("#preview > div").css({
					padding:"5px",
					backgroundColor:"white",
					border:"1px solid #cccccc"
				});
				//var img_height = $('#preview').find('img').height();
				if (show_y + 300 > h + scroll_y) {
					$("#preview").css("bottom", h - show_y - $(this).height() + "px").css("top", "auto");
				} else {
					$("#preview").css("top", show_y + "px").css("bottom", "auto");
				}
				$("#preview").fadeIn("fast")
			},function(){
				$("#preview").remove();
			})					  
		});
	};
})(jQuery);