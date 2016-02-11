<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/jquery.js"></script>

<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/guoji/vshop/admin2';
var URL = '/guoji/vshop/admin2/index.php/Member';
var APP	 =	 '/guoji/vshop/admin2/index.php';
var PUBLIC = '/guoji/vshop/admin2/Public';
var uid = '<?php echo $_SESSION[C("USER_AUTH_KEY")]; ?>';
var hash = '<?php echo ($hash); ?>';
//图片预览
function yulan(file,div_id){
  var div_id = div_id ? div_id : 'preview';
  var img_id = div_id+'imghead';
  var MAXWIDTH  = 200;
  var MAXHEIGHT = 200;
  var div = document.getElementById(div_id);
  if (file.files && file.files[0])
  {
    div.innerHTML = '<img id='+img_id+'>';
    var img = document.getElementById(img_id);
    img.onload = function(){
      var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
      img.width = rect.width;
      img.height = rect.height;
      img.style.marginLeft = rect.left+'px';
      img.style.marginTop = rect.top+'px';
    }
    var reader = new FileReader();
    reader.onload = function(evt){img.src = evt.target.result;}
    reader.readAsDataURL(file.files[0]);
  }
  else
  {
    var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
    file.select();
    var src = document.selection.createRange().text;
    div.innerHTML = '<img id='+img_id+'>';
    var img = document.getElementById(img_id);
    img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
    var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
    status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
    div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;margin-left:"+rect.left+"px;"+sFilter+src+"\"'></div>";
  }
}

function clacImgZoomParam( maxWidth, maxHeight, width, height ){
    var param = {top:0, left:0, width:width, height:height};
    if( width>maxWidth || height>maxHeight )
    {
        rateWidth = width / maxWidth;
        rateHeight = height / maxHeight;
        if( rateWidth > rateHeight )
        {
            param.width =  maxWidth;
            param.height = Math.round(height / rateWidth);
        }else
        {
            param.width = Math.round(width / rateHeight);
            param.height = maxHeight;
        }
    }
    param.left = Math.round((maxWidth - param.width) / 2);
    param.top = Math.round((maxHeight - param.height) / 2);
    return param;
}
</SCRIPT>
</head>
<body>
<div class="header">
    <div class="logo fl" style="padding:0px;margin:0px;">
		<img src="/guoji/vshop/admin2/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/guoji/vshop/admin2/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/guoji/vshop/admin2/index.php?m=Cache&a=clear"><img src="/guoji/vshop/admin2/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/guoji/vshop/admin2/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/guoji/vshop/admin2/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/guoji/vshop/admin2/index.php" target="_blank">网站前台</a>|
        <a href="/guoji/vshop/admin2/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/guoji/vshop/admin2/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/guoji/vshop/admin2/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/guoji/vshop/admin2/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/guoji/vshop/admin2/Public/images/ico_8.png" /></div>
	<!--
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
	-->
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<script language="JavaScript">

function resetpwd(){
    var pass	 =	 $('#resetPwd').val();
	if(pass.length<4){
	  alert('密码必须大于3位');
	  return;
	}
	$.ajax({
	  type:'POST',
	  dataType:'json',
	  url:'/guoji/vshop/admin2/index.php/Member/resetPwd/',
	  data:'id=<?php echo ($vo["id"]); ?>&password='+encodeURIComponent(pass),
	  success:function(obj){
		if(obj.error_code==0){
		  //alert('密码已修改为'+encodeURIComponent(pass));
			  art.dialog({
				time: 2,
				content: obj.notice
			  });
		}else{
		  //alert(msg);
			  art.dialog({
				time: 2,
				content: obj.notice
			  });
		}
	  }
	})
}
</script>
<script type="text/javascript" charset="UTF-8" src="/guoji/vshop/admin2/Public/js/laydate/laydate.js"></script>
<div class="content">
<div class="title">编辑帐号 [ <a href="/guoji/vshop/admin2/index.php/Member">返回列表</a> ]</div>
<form method='post' id="form1" action="/guoji/vshop/admin2/index.php/Member/edit?<?php echo time(); ?>" enctype="multipart/form-data" onsubmit="return check();">
<table cellpadding=3 cellspacing=3 class="add">
<tr>
	<td class="tRight">重置密码：</td>
	<td class="tLeft" ><input type="text" name="resetPwd" id="resetPwd"> <INPUT type="button" value="重置密码" onclick="resetpwd();" class="button" style="height:25px"></td>
</tr>
<tr>
	<td class="tRight" colspan="2"><hr></td>
</tr>
<tr>
	<td class="tRight" >昵称：</td>
	<td class="tLeft" ><font color="red"><strong><?php echo ($vo["username"]); ?></strong></font></td>
</tr>
<!--
<tr>
	<td class="tRight" >昵称：</td>
	<td class="tLeft" ><font color="red"><strong><input type="text" class="medium bLeft" name="nickname" id="nickname" value="<?php echo ($vo["nickname"]); ?>"></strong></font></td>
</tr>
-->
<tr>
	<td class="tRight" >性别：</td>
	<td class="tLeft" >
	<SELECT NAME="sex">
		<OPTION VALUE="1" <?php if(($vo["sex"]) == "1"): ?>SELECTED<?php endif; ?>>女</OPTION>
		<OPTION VALUE="2" <?php if(($vo["sex"]) == "2"): ?>SELECTED<?php endif; ?>>男</OPTION>
		<OPTION VALUE="3" <?php if(($vo["sex"]) == "3"): ?>SELECTED<?php endif; ?>>未知</OPTION>
	</SELECT>	
	</td>
</tr>

<tr>
	<td class="tRight" >用户类型：</td>
	<td class="tLeft" >
	<SELECT NAME="utype" id="utype" onchange="up_type()">
		<OPTION VALUE="1" <?php if(($vo["utype"]) == "1"): ?>SELECTED<?php endif; ?>>会员</OPTION>
		<OPTION VALUE="2" <?php if(($vo["utype"]) == "2"): ?>SELECTED<?php endif; ?>>战略合作商</OPTION>
		<OPTION VALUE="3" <?php if(($vo["utype"]) == "3"): ?>SELECTED<?php endif; ?>>合作商</OPTION>
		<OPTION VALUE="4" <?php if(($vo["utype"]) == "4"): ?>SELECTED<?php endif; ?>>分销商</OPTION>
		<OPTION VALUE="5" <?php if(($vo["utype"]) == "5"): ?>SELECTED<?php endif; ?>>代理商</OPTION>
	</SELECT>
	</td>
</tr>

<tr id="lv_tr">
	<td class="tRight" >会员等级：</td>
	<td class="tLeft" >
	<SELECT NAME="lv" id="lv">
		<OPTION VALUE="0" <?php if(($vo["lv"]) == "0"): ?>SELECTED<?php endif; ?>>普通会员</OPTION>
		<OPTION VALUE="1" <?php if(($vo["lv"]) == "1"): ?>SELECTED<?php endif; ?>>VIP1</OPTION>
		<OPTION VALUE="2" <?php if(($vo["lv"]) == "2"): ?>SELECTED<?php endif; ?>>VIP2</OPTION>
		<OPTION VALUE="3" <?php if(($vo["lv"]) == "3"): ?>SELECTED<?php endif; ?>>VIP3</OPTION>
		<OPTION VALUE="4" <?php if(($vo["lv"]) == "4"): ?>SELECTED<?php endif; ?>>VIP4</OPTION>
		<OPTION VALUE="5" <?php if(($vo["lv"]) == "5"): ?>SELECTED<?php endif; ?>>VIP5</OPTION>
		<OPTION VALUE="6" <?php if(($vo["lv"]) == "6"): ?>SELECTED<?php endif; ?>>钻石会员</OPTION>
	    <!--
	    <?php if(is_array($lvs)): $i = 0; $__LIST__ = $lvs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv): $mod = ($i % 2 );++$i;?><OPTION VALUE="<?php echo ($lv["val"]); ?>" <?php if(($vo["lv"]) == $lv["val"]): ?>SELECTED<?php endif; ?>><?php echo ($lv["name"]); ?></OPTION><?php endforeach; endif; else: echo "" ;endif; ?>
		-->
	</SELECT>	
	</td>
</tr>

<tr id="bus_lv_tr" <?php if(($vo["utype"]) == "1"): ?>style="display:none"<?php endif; ?>>
	<td class="tRight" >商家等级：</td>
	<td class="tLeft" >
	<SELECT NAME="bus_lv" id="bus_lv">
		<OPTION VALUE="0" <?php if(($vo["bus_lv"]) == "0"): ?>SELECTED<?php endif; ?>>普通/区代理</OPTION>
		<OPTION VALUE="1" <?php if(($vo["bus_lv"]) == "1"): ?>SELECTED<?php endif; ?>>银牌/市代理</OPTION>
		<OPTION VALUE="2" <?php if(($vo["bus_lv"]) == "2"): ?>SELECTED<?php endif; ?>>金牌/省代理</OPTION>
	</SELECT>	
	</td>
</tr>

<script>
  //会员
  var lvs1 = [{'name':'普通会员','val':0},{'name':'VIP1','val':1},{'name':'VIP1','val':2},{'name':'VIP1','val':3},{'name':'VIP1','val':4},{'name':'VIP1','val':5},{'name':'钻石会员','val':6}];
  //合作商
  var lvs3 = [{'name':'银牌','val':1},{'name':'金牌','val':2}];

  function up_type(){
    var utype = jQuery('#utype').val();
	//alert(utype);
	if(utype==1){
	  /*jQuery('#lv_tr').show();
	  var html = '';
	  for(i=0;i<lvs1.length;i++){
		html += '<option value='+lvs1[i].val+'>'+lvs1[i].name+'</option>';	
	  }
	  */
	  jQuery('#bus_lv_tr').hide();
	  //html = '<option value="0"></option>';
	}else{
	  //jQuery('#sub_lv_tr').hide();
	  jQuery('#bus_lv_tr').show();
	  //html = '<option value="0">普通</option>';
	}
	//jQuery('#sub_lv').html(html);
	//alert(lvs1[0]['name']);
  }
</script>

<TR>
	<TD class="tRight" >
		到期时间：
	</TD>
	<TD class="tLeft">
		<input type="text" name="exp_time" id="exp_time" value="<?php echo (toDate($vo["exp_time"],'Y-m-d')); ?>" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"> 
		<strong><font color="red">日期格式：<?php echo date('Y-m-d'); ?>；0表示永久有效</font></strong>
	</TD>
</TR>

<tr>
	<td class="tRight" >真实姓名：</td>
	<td class="tLeft" ><input type="text" class="medium bLeft" name="realname" value="<?php echo ($vo["realname"]); ?>"></td>
</tr>

<tr>
	<td class="tRight" >Email：</td>
	<td class="tLeft" ><input type="text" class="medium bLeft" name="email" value="<?php echo ($vo["email"]); ?>"></td>
</tr>

<tr>
	<td class="tRight" >头像：<a href="/guoji/vshop/admin2/index.php/Member/head/id/<?php echo ($vo["id"]); ?>">裁剪</a></td>
	<td class="tLeft" ><input type="file" name="logo" onchange="yulan(this,'show1')">
	<div id="show1">
	<?php if(($vo["logo"]) != ""): ?><!--<img src="<?php echo ($vo["logo"]); ?>">-->
	<img src="<?php echo str_replace('logo.jpg','m_logo.jpg',$vo['logo']); ?>"><?php endif; ?>
	</div>
	</td>
</tr>

<tr>
	<td class="tRight">状态：</td>
	<td class="tLeft"><SELECT class="small bLeft"  name="status">
	<option <?php if(($vo["status"]) == "1"): ?>selected<?php endif; ?> value="1">启用</option>
	<option <?php if(($vo["status"]) == "0"): ?>selected<?php endif; ?> value="0">禁用</option>
	</SELECT></td>
</tr>

<tr>
	<td class="tRight" colspan="2"><hr></td>
</tr>

<tr>
	<td class="tRight">电话：</td>
	<td class="tLeft"><INPUT TYPE="text" NAME="phone" id="phone" value="<?php echo ($vo["phone"]); ?>"></td>
</tr>

<tr>
	<td class="tRight">手机：</td>
	<td class="tLeft"><INPUT TYPE="text" NAME="mobile" id="mobile" value="<?php echo ($vo["mobile"]); ?>"></td>
</tr>

<tr>
	<td class="tRight">地区：</td>
	<td class="tLeft">
	<SELECT name="pv_id" id="province"  size=1 onchange="pvchange(this);">   
		<OPTION>请选择</OPTION>
		<?php if(is_array($pvlist)): $i = 0; $__LIST__ = $pvlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pv): $mod = ($i % 2 );++$i;?><OPTION value="<?php echo ($pv["id"]); ?>" <?php if(($vo["pv_id"]) == $pv["id"]): ?>selected<?php endif; ?>><?php echo ($pv["area_name"]); ?></OPTION><?php endforeach; endif; else: echo "" ;endif; ?>
	</SELECT>
	<span>
	<SELECT name="ct_id" id="city" size=1 onchange="ctchange(this);">   
		<OPTION>请选择</OPTION>
		<?php if(is_array($ctlist)): $i = 0; $__LIST__ = $ctlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ct): $mod = ($i % 2 );++$i;?><OPTION value="<?php echo ($ct["id"]); ?>" <?php if(($vo["ct_id"]) == $ct["id"]): ?>selected<?php endif; ?>><?php echo ($ct["area_name"]); ?></OPTION><?php endforeach; endif; else: echo "" ;endif; ?>
	</SELECT>
	</span>
	<span>
	<SELECT name="dist_id" id="district" size=1>   
		<OPTION>请选择</OPTION>
		<?php if(is_array($districts)): $i = 0; $__LIST__ = $districts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ct): $mod = ($i % 2 );++$i;?><OPTION value="<?php echo ($ct["id"]); ?>" <?php if(($vo["dist_id"]) == $ct["id"]): ?>selected<?php endif; ?>><?php echo ($ct["area_name"]); ?></OPTION><?php endforeach; endif; else: echo "" ;endif; ?>
	</SELECT>
	</span>
	<INPUT TYPE="hidden" NAME="province" id="post_pv">
	<INPUT TYPE="hidden" NAME="city" id="post_ct">
	<INPUT TYPE="hidden" NAME="district" id="post_district">
	<INPUT TYPE="hidden" NAME="lv_name" id="lv_name" value="<?php echo ($vo["lv_name"]); ?>">
	<INPUT TYPE="hidden" NAME="bus_lv_name" id="bus_lv_name" value="<?php echo ($vo["bus_lv_name"]); ?>">
	</td>
</tr>
<script>
  function check(){
	var pv = jQuery('#province option:selected').text();
	var ct = jQuery('#city option:selected').text();
	var district = jQuery('#district option:selected').text();
	var lv_name = jQuery('#lv option:selected').text();
	var bus_lv_name = jQuery('#bus_lv option:selected').text();
	//alert(bus_lv_name);return false;
	if(!pv || pv=='请选择'){
	  alert('省必须选择!');
	  return false;
	}
	if(!ct || ct=='请选择'){
	  alert('市必须选择!');
	  return false;
	}
	if(!district || district=='请选择'){
	  alert('区域必须选择!');
	  return false;
	}
	jQuery('#post_pv').val(pv);
	jQuery('#post_ct').val(ct);
	jQuery('#post_district').val(district);
	jQuery('#lv_name').val(lv_name);
	jQuery('#bus_lv_name').val(bus_lv_name);
	return true;
  }

  //显示城市
  function pvchange(){
	var pid = jQuery('#province').val();
	jQuery.ajax({
	   type: "POST",
	   url: "/guoji/vshop/admin2/index.php/Member/get_city",
	   data: "pid="+pid,
	   success: function(msg){
		 var objs = eval('('+msg+')');
		 //var html = '<select id="city" name="ct_id">';
		 var html = '';
		 for(i=0;i<objs.length;i++){
			//html += '<option value='+objs[i].id+'>'+objs[i].class_name+'</option>';
			if(i==0){
			   html += '<option value='+objs[i].id+' selected>'+objs[i].area_name+'</option>';
			}else{
			   html += '<option value='+objs[i].id+'>'+objs[i].area_name+'</option>';	
			}
		 }
		 //html += '</select>';
		 jQuery('#city').html(html);
		 ctchange();
	   }
	}); 
  }

  //显示区域
  function ctchange(){
	var pid = jQuery('#city').val();
	jQuery.ajax({
	   type: "POST",
	   url: "/guoji/vshop/admin2/index.php/Member/get_district",
	   data: "pid="+pid,
	   success: function(msg){
		 var objs = eval('('+msg+')');
		 //var html = '<select id="city" name="ct_id">';
		 var html = '';
		 for(i=0;i<objs.length;i++){
			html += '<option value='+objs[i].id+'>'+objs[i].area_name+'</option>';
		 }
		 //html += '</select>';
		 jQuery('#district').html(html);
	   }
	}); 
  }
</script>
<tr>
	<td class="tRight tTop">简 介：</td>
	<td class="tLeft"><textarea id="intro" style="width:650px;height:345px" name="intro" ><?php echo ($vo["intro"]); ?></textarea><script type="text/javascript" src="/guoji/vshop/admin2/Public/Js/KindEditor/kindeditor-min.js"></script><script>KindEditor.create('#intro', {uploadJson : '/guoji/vshop/admin2/index.php/Attachment/editer_upload',fileManagerJson : '/guoji/vshop/admin2/index.php/Attachment/editer_manager',allowFileManager : false,items:['source','title', 'fontname', 'fontsize', '|', 'link', 'unlink','|', 'forecolor', 'bgcolor', 'bold','italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', '|', 'image', 'multiimage', 'emoticons','baidumap']});</script></td>
</tr>
<tr>
	<td></td>
	<td>
	<INPUT TYPE="hidden" NAME="id" value="<?php echo ($vo["id"]); ?>">
	<input type="submit" value="保 存" class="button">
	</td>
</tr>
</table>
</form>

	<div class="copy"><span class="line_white"></span>Powered by vion 0.5 版权所有 © 2013-2015 vion，并保留所有权利。</div>
  </div><!--content结束-->
</div>
</body>
</html>
<script>
$(".z_side").click(function() {
    $("iframe").attr("src", $(this).attr("data"));
});
/*
if (top.location !== self.location) {
    top.location = self.location;
}
*/
//$(".side a[name!='disabled']").eq(0).addClass('hover').click();

//左侧side中的hover 效果
$(function(){
	$(".side li a").click(function(){
		if($(this).hasClass('disabled')) return false;
		$(".side li a").removeClass("hover");
		$(this).addClass("hover");
	});
});
/**
 * 显示和收起后台导航
 */
$(".ico_left").toggle(function(){
			$(".side").animate({left:"-200px"});
			$("#Container").animate({left:"0"});
			$(".welcome").animate({paddingLeft:"10px"});
			$(this).children().attr('src','/guoji/vshop/admin2/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/guoji/vshop/admin2/Public/images/ico_8.png');
		}
	  );
</script>