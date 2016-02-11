function redirect(url) {location.href = url;}
/**
 * 二秒自动关闭提示框
 */
function msg_time(msg) {
	dialog({
		id: 'msg_time',
		title: '提示',
		content: msg,
		width: 300
	}).show();
	setTimeout(function(){dialog.get('msg_time').close().remove();},
	2000);
}

/**
 * 确认对话框
 */
function msg_confirm(msg, url) {
	dialog({
		id: 'msg_confirm',
		title: '提示',
		width: 300,
		content: msg,
		okValue: '确定',
		ok: function() {
			window.location.href = url;
			return false;
		},
		cancelValue: '取消',
		cancel: function() {}
	}).show();

}

//ajax get请求
$('.confirm').live("click",
function() {
	var that = this;
	var target = $(this).attr('url');
	if ($(this).hasClass('confirm')) {
		if (!confirm('确认要执行该操作吗?')) {
			return false;
		}
		window.location.href = target;
	}
})

/**
 * 添加到收藏夹
 */
function favorite_add(obj) {
	var url = obj;
	$.getJSON(url,
	function(json) {
		if (json.status === 0) {
			msg_confirm(json.info, json.url);
			return;
		} else {
			msg_time(json.info);
		}

	})
}

$(document).ready(function($) {
	//ajax获取
	$(".ajax-get").live('click',
	function(event) {
		var url = $(this).attr('url');
		var _this = this;
		$.getJSON(url, {},
		function(data) {
			try {
				if (data.status == 1) {
					$(_this).toggleClass('ajax_on ajax_off');
				}
		} catch(e) {
			//TODO handle the exception
			}

		});
	});
});
$(function() {
    SetTableRowColor()
});
//用CSS控制奇偶行的颜色  
function SetTableRowColor() {
	$("tbody>tr:odd").addClass("even"); //为奇数行添加样式  
	$("tbody>tr:even").addClass("odd"); //为偶数行添加样式 
	$("tbody tr:first").css('height', '30px'); //固定标题行高度
	$("tbody>tr").not("tbody tr:first").mouseover(function() {$(this).addClass("selected");}).mouseout(function(){ $(this).removeClass("selected");});//鼠标移动的高亮显示
}

//排序
function EditSort(e, target){
	var target;
	var that = e;
	$.get(target).success(function(data){if(data.status == 1){location.reload();}});
}

//全选反选
$(".check-all").live("click",
function() {$(".ids:visible").prop("checked", this.checked);});
$(".ids").live("click",
function() {
	var option = $(".ids:visible");
	option.each(function(i) {
		if (!this.checked)
			{
				$(".check-all:visible").prop("checked", false);
				return false;
			} 
		else 
		{
			$(".check-all:visible").prop("checked", true);
		}
	});
});
/**
 * 显示和收起后台导航
 */
$(".ico_left").toggle(function() {$(".side").animate({left: "-200px"});
	$("#Container").animate({left: "0"});
	$(".welcome").animate({paddingLeft: "10px"});
	$(this).find("img").attr("src", respath + "images/ico_8a.png");
},
function(){
	$(".side").animate({left: "0px"});
	$("#Container").animate({left: "200px"});
	$(".welcome").animate({paddingLeft: "65px"    });
	$(this).find("img").attr("src", respath + "images/ico_8.png");
});

/**
 *日期比较 
 **/
function checkDateTime(beginValue, endValue) {
	var flag = 0;
	if (beginValue != null && beginValue != "" && endValue != null && endValue != "") 
	{
		var dateS = beginValue.split('-'); //日期是用'-'分隔,如果你日期用'/'分隔的话,你将这行和下行的'-'换成'/'即可
		var dateE = endValue.split('-');
		var beginDate = new Date(dateS[0], dateS[1], dateS[2]).getTime(); //如果日期格式不是年月日,需要把new Date的参数调整
		var endDate = new Date(dateE[0], dateE[1], dateE[2]).getTime();
		if (beginDate > endDate)
			{
				flag = 1;
			} else if (beginDate == endDate)
			{
				flag = 0;
			} 
			else
			{
				flag = -1;
			}
	}
	return flag;
}

/**
 * 倒计时
 * 
 * 只需前台调用count_down(end_time)即可;
 *
 * end_time & 到期时间戳
 *
 **/
function count_down(end_time){
	var timer = null;
	timer = setInterval(function() {
		if (end_time <= 0) {
			$('#timed').html('-');
			clearInterval(timer);
			return;
		}
		$('#timed').html(time_down(end_time));
	}, 1000);
}

function time_down(end_time) {
	var end_time = parseInt(end_time);
	var now_time = parseInt($.now() / 1000);
	var t = end_time - now_time;
	var d = 0;
	var h = 0;
	var m = 0;
	var s = 0;
	if(t >= 0){
		d = setDig(Math.floor(t /60 /60 / 24),2);
		h = setDig(Math.floor(t /60 / 60 % 24),2);
		m = setDig(Math.floor(t / 60 % 60),2);
		s = setDig(Math.floor(t % 60),2);
	}
	var _str = '';
	if (d != 0) {
		_str += "还剩&nbsp;<em>" + d + "</em> 天";
		_str += "<em> " + h + "</em> 时";
	} else {
		_str += "还剩&nbsp;<em> " + h + "</em> 时";
	}
	_str += "<em> " + m + "</em> 分";
	_str += "<em> " + s + "</em> 秒 结束";
	return _str;
}

// 不足两位时用0补齐
function setDig(num , n){ 
	var str = ""+num; 
	while(str.length < n){ 
		str="0"+str 
	} 
	return str; 
} 