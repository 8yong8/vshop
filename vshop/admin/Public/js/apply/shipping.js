//添加帮助
function addrow(obj, type, pid) {
    num = num+1;
	var tr_id = num;
	var temp = "<tr class='area_item' id='"+ tr_id +"'>"
			+"<td><span style=\"float: left; color: #0F0F0F;text-indent: 30px;\" class=\"area_val\">请选择配送地区</span></td>"
			+"<td><span class=\"btnArea\" style=\"color:#2d689f;margin-left: -50px;cursor:pointer\" onclick=\"_dialog(this)\">编辑区域</span> <input class=\"small\" name=\"shipping_region[add][fw_price][]\" type=\"text\" value=\"\" /><input type=\"hidden\" name=\"shipping_region[add][area_id][]\"></td>"
			+"<td><input class=\"small\" name=\"shipping_region[add][aw_price][]\" type=\"text\" value=\"\" /></td><td><a href=\"javascript:void(0)\" onclick=\"removeRow(this)\">删除</a></td>"
			+"</tr>";
	$(obj).parent().next().children().find('tbody').append(temp);
}

//编辑区域
function _dialog(o) {
	item_id = $(o).parents('tr').attr('id');
	init();
	art.dialog({
		padding: '5px ',
		id: 'testID',
		background: '#ccc',
		opacity: 0.35,
		title: '选择地区',
		fixed:true,
		lock:true,
		content:document.getElementById('testID'),
		ok: function () {
			return _insert(o);
		},
		cancel: true
	});
}

/* 插入数据 */
function _insert(o) {
    //alert($(o).parents('tr').attr('id'));
	var in_area_id = Array();
	var in_area_val = Array();
	$('.fendd input[item_id='+item_id+'][ischk=true]').each(function(i){
		in_area_id.push($(this).attr('value'));
		in_area_val.push($(this).parents('li').find('span').text());
	});
	in_area_id = in_area_id.toString();
	in_area_val = in_area_val.toString();
	if (in_area_id.length < 1) {
		alert('请选择配送地区');
		return false;
	}
	//$(o).parents('td').find("input[type=hidden]").attr('value', in_area_id);
	//$(o).parents('tr').find("span.area_val").html(in_area_val);
	$(o).parent().parent().find("input[type=hidden]").attr('value', in_area_id);
	$(o).parent().parent().find("span.area_val").html(in_area_val);
	return true;
}

function init() {
	for (var i = 0; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
}

/* 获取区块内容 */
function getShowBox(obj) {
	var level = parseInt($(obj).parents('.bgef').attr('data-level')),//当前层级
		area_id = $(obj).attr('data-areaid');
	var _this = "div.bgef[data-level="+ (level + 1) +"]";
	$(obj).parents('.bgef').find('li').removeClass('hover');
	$(obj).addClass('hover');
	if(level > 2) {
		return false;
	}
	ischked = false;
	return getarealist(level, area_id);
}
/* 获取地区列表 */
function getarealist(level, area_id) {
	//if (level == 3) return false;
	if (level == 2) return false;//到市结束
	var _this = "div.bgef[data-level="+ (level + 1) +"]";
	/* 当前按钮 */
	var _this_input = "div.bgef[data-level="+ level +"] li[data-areaid="+ area_id +"] input";
	for (var i = level; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
	last_id = $(_this_input).val();
	var chked = (ischked == true || $(_this_input).attr('checked')) ? ' checked' : '';
	var dised = ($(_this_input).attr('checked') && $(_this_input).attr['item_id'] != item_id && $(_this_input).attr['ischk'] == true) ? ' disabled="disabled"' : '';
	if($(_this + ' ul#area_'+area_id).length > 0) {
		$(_this + ' ul#area_'+area_id + ' input').each(function(i) {
			if ($(_this_input).attr('checked')) {
				$(this).attr('item_id', $(_this_input).attr('item_id'));
			}
			if ($(this).attr('checked') && $(this).attr('item_id').length > 0 && $(this).attr('item_id') != item_id) {
				$(this).attr("disabled", true);
			} else {
				$(this).attr("disabled", false);
			}
		});
		$(_this + ' ul#area_'+area_id).show();
		return false;
	}
	var _html = '';
	//alert(level);return;
	$.ajax({
		type: "post",
		async: false,
		url: APP+"/Region/get_area_list",
		data: {level:level, area_id:area_id},
		dataType: "json",
		success: function (ret) {
		    //alert(ret);
			var item =  (chked) ? $(_this_input).attr('item_id') : 0;
			_html += '<ul id="area_'+ area_id +'" data-parentid="'+area_id+'">';
			$.each(ret, function(i, n) {
				var dised = '';
				if (level > 0 && parseInt($(_this_input).attr['item_id']) > 0 && $(_this_input).attr['item_id'] != item_id || (item > 0 && item_id != item)) {
					dised = ' disabled="disabled"';
				}
				var chked = '';
				var dised = '';
				var ischk = false;
				if (region_conf.indexOf(n.id) > -1 || ischked == true || $(_this_input).attr('checked')) {
					chked = 'checked="checked"';
					ischk = true;
					if ($.isArray(region_tid[item_id]) == true && region_tid[item_id].indexOf(n.id) > -1) {
						item = item_id;
						dised = '';
					} else {
						ischk = false;
						dised = ' disabled="disabled"';
					}
					if (item != item_id) {
						dised = ' disabled="disabled"';
					} else {
						dised = '';
					}
				}
				_html += '<li class="li_1" data-areaid="'+ n.id +'" onclick="getShowBox(this)"><input type="checkbox" name="area_id[]" value="'+ n.id +'" item_id="'+ item +'" ischk="'+ ischk +'" onclick="setChecked(this)" '+ chked + dised +'><span id="' + n.id + '">'+ n.area_name +'</span></li>';
			});
			_html += '</ul>';
			$(_this).append(_html);
		},
		error: function (err) {
			alert(err);
		}
	});
}
/* 勾选窗体并赋值 */
function setChecked(obj) {
	var level = parseInt($(obj).parents('.bgef').attr('data-level'));
	var area_id = $(obj).attr('value');
	ischked = ($(obj).attr('checked')) ? true : false;
	getarealist(level, area_id);
	for (var i = level; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
	childCk(level, area_id, $(obj).attr('checked'));
	parentCk(level, area_id, $(obj).attr('checked'));
}
/* 循环上一级 */
function parentCk(level, area_id, t) {
	if (level < 2) return false;
	l = level - 1;
	/* 当前表单 */
	var _this_chk = "div.bgef[data-level="+ level +"] li[data-areaid="+area_id+"] input";
	 /* 获取上级ID */
	var parent_id = $(_this_chk).parents('ul').attr('data-parentid');
	/* 所有同级 */
	var _this_input = "div.bgef[data-level="+ level +"] ul[data-parentid="+parent_id+"] input";
	/* 所属上级 */
	var _this_parent = "div.bgef[data-level="+ l +"] li[data-areaid="+ parent_id +"] input";
	if (t == 'checked') {
		$(_this_chk).attr('item_id', item_id);
	} else {
		$(_this_chk).attr('item_id', 0);
	}
	if ($(_this_input).length == $(_this_input + ':checked').length) {
		$(_this_parent).prop('checked', true);
		if ($(_this_input).length == $(_this_input + '[item_id='+ item_id +']:checked').length) {
			$(_this_parent).attr('ischk', true);
			$(_this_input).attr('ischk', false);
		}
	} else {
		$.each($(_this_input + '[item_id='+ item_id +']'), function(i, n) {
			if ($(this).attr('checked') == 'checked') {
				$(this).attr('ischk', true);
			} else {
				$(this).attr('ischk', false);
			}
		});	
		$(_this_parent).prop('checked', false);
		$(_this_parent).attr('ischk', false); 
	}
	parentCk(l, parent_id, t);
}
/* 循环下级 */
function childCk(level, area_id, t) {
	if (level == 3) return false;
	var l = level + 1;
	/* 当前表单 */
	var _this_chk = "div.bgef[data-level="+ level +"] li[data-areaid="+area_id+"] input";
	 /* 获取上级ID */
	var parent_id = $(_this_chk).parents('ul').attr('data-parentid');
	/* 所有同级 */
	var _this_input = "div.bgef[data-level="+ level +"] ul[data-parentid="+parent_id+"] input";
	/* 所有下级 */
	var _this_child = "div.bgef[data-level="+ l +"] ul[data-parentid="+area_id+"] input";
	if (t == 'checked') {
		$(_this_child+'[item_id=0]').attr('item_id', item_id);
		$(_this_child+'[item_id='+ item_id +']').prop('checked', true);
		if ($(_this_child+'[item_id='+ item_id +']:checked').length == $(_this_child).length) {
			$(_this_child+'[item_id='+ item_id +']:checked').attr('ischk', false);
			$(_this_chk).attr('item_id', item_id);
		} else {
			$(_this_child+'[item_id='+ item_id +']:checked').attr('ischk', true);
			$(_this_chk).attr('item_id', 0);
		}
		$(_this_chk).prop('checked', true);
	} else {
		$(_this_child+'[item_id='+ item_id +']').prop('checked', false);
		$(_this_child+'[item_id='+ item_id +']').attr('item_id', 0);
		$(_this_chk).attr('item_id', 0);
		$(_this_chk).prop('checked', false);
		$(_this_chk).attr('ischk', false);
	}
	$.each($(_this_input + '[item_id='+ item_id +']'), function(i, n){
		if ($(this).attr('checked') == 'checked') {
			$(_this_chk).attr('ischk', true);
		} else {
			$(_this_chk).attr('ischk', false);
		}
	});
}

//移除行
function removeRow(obj,id){
  if(id){
	if (window.confirm('确实要删除选择项吗？')){
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:URL+"/ajax_del_sr?"+new Date().getTime(),
			data:'id='+id,
			async: false,
			error: function(request) {
					art.dialog({
						time: 2,
						content: '系统繁忙'
					});
			},
			success: function(data) {
				if(data.error_code==0){
					art.dialog({
						id: 'ajax_del',
						time: 2,
						content: data.notice
					});
					$('#'+id).remove();
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
  }else{
    $(obj).parent().parent().remove();
  }
}

//删除
function delRow(obj){
  $(obj).parent().parent().remove();
}