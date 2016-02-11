<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/jquery.js"></script>

<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/guoji/vshop/ADMIN2';
var URL = '/guoji/vshop/ADMIN2/index.php/Node';
var APP	 =	 '/guoji/vshop/ADMIN2/index.php';
var PUBLIC = '/guoji/vshop/ADMIN2/Public';
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
		<img src="/guoji/vshop/ADMIN2/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/guoji/vshop/ADMIN2/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/guoji/vshop/ADMIN2/index.php?m=Cache&a=clear"><img src="/guoji/vshop/ADMIN2/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/guoji/vshop/ADMIN2/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/guoji/vshop/ADMIN2/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/guoji/vshop/ADMIN2/index.php" target="_blank">网站前台</a>|
        <a href="/guoji/vshop/ADMIN2/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/guoji/vshop/ADMIN2/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/guoji/vshop/ADMIN2/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/guoji/vshop/ADMIN2/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/guoji/vshop/ADMIN2/Public/images/ico_8.png" /></div>
	<!--
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
	-->
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<div class="content">
<div class="title">编辑<?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?> [ <a href="/guoji/vshop/ADMIN2/index.php/Node">返回列表</a> ] [ <a href="/guoji/vshop/ADMIN2/index.php/Node/index/pid/<?php echo ($vo["pid"]); ?>">返回上级</a> ]</div>
<form method='post'  id="form1" action='/guoji/vshop/ADMIN2/index.php/Node/edit?<?php echo time(); ?>'>
<table cellpadding=3 cellspacing=3 class="add">
<tr>
	<td class="tRight" ><?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?>名：</td>
	<td class="tLeft" ><input type="text" class="medium bLeftRequire" check='Require' warning="<?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?>名称不能为空,且不能含有空格"  name="name" value="<?php echo ($vo["name"]); ?>"></td>
</tr>
<tr>
	<td class="tRight" >显示名：</td>
	<td class="tLeft" ><input type="text" class="medium bLeftRequire" check='Require' warning="显示名称必须" name="title" value="<?php echo ($vo["title"]); ?>"></td>
</tr>
<tr>
	<td class="tRight" >分 组：</td>
	<td class="tLeft" >
	<SELECT class="medium bLeft" name="group_id">
	<option value="">未分组</option>
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><option value="<?php echo ($group["id"]); ?>" <?php if(($group["id"]) == $vo['group_id']): ?>selected<?php endif; ?>><?php echo ($group["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</SELECT>
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
	<td class="tRight">显示：</td>
	<td class="tLeft"><SELECT class="small bLeft"  name="is_display">
	<option value="1" <?php if(($vo["is_display"]) == "1"): ?>selected<?php endif; ?>>显示</option>
	<option value="0" <?php if(($vo["is_display"]) == "0"): ?>selected<?php endif; ?>>不显示</option>
	</SELECT></td>
</tr>
<tr>
	<td class="tRight tTop">描 述：</td>
	<td class="tLeft"><TEXTAREA class="large bLeft" name="remark"  rows="5" cols="57"><?php echo ($vo["remark"]); ?></textarea></td>
</tr>
<tr>
	<td></td>
	<td>
	<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
	<div class="impBtn fLeft"><input type="submit" value="保存" class="button"></div>
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
			$(this).children().attr('src','/guoji/vshop/ADMIN2/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/guoji/vshop/ADMIN2/Public/images/ico_8.png');
		}
	  );
</script>