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
var URL = '/guoji/vshop/admin2/index.php/Article';
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
<SCRIPT LANGUAGE="JavaScript">
var imgurl = "<?php echo ($imgurl); ?>";

//插入分页
function insertPage(){
	var oEditor = FCKeditorAPI.GetInstance("content");
	if (oEditor.EditMode==FCK_EDITMODE_WYSIWYG)
	{
	   var text = document.getElementById('title').value;
	   oEditor.InsertHtml('[page]'+text+'[/page]');
	}
	else
	{
		return false;
	}
	return;
}

//缩略图
function insertsl(dir){
  var imgdir = dir;
  jQuery('#litpic').val(imgurl+imgdir);
  var img = "<img src='"+imgurl+imgdir+"' width='200px'>";
  jQuery('#showimg').html(img);
  InsertHTML("<img src='"+imgurl+imgdir+"'>");
}

//多图添加内容
function insertnrjson(msg){
  var objs = eval('('+msg+')');
  var oEditor = FCKeditorAPI.GetInstance("content");
  for(i=0;i<objs.length;i++){
    var img = "<img src='"+imgurl+objs[i].filepath+"' title='"+objs[i].title+"' alt='"+objs[i].title+"'>";
    oEditor.InsertHtml(img);
  }

}

//内容
function insertnr(dir){
  var imgdir = dir;
  var img = "<img src='"+imgurl+imgdir+"'>";
	var oEditor = FCKeditorAPI.GetInstance("content");
	if (oEditor.EditMode==FCK_EDITMODE_WYSIWYG)
	{
	   oEditor.InsertHtml(img);
	}
	else
	{
		return false;
	}
	return;
}

</SCRIPT>
<div class="content">
<div class="title">添加数据 [ <A HREF="/guoji/vshop/admin2/index.php/Article">返回列表</A> ]</div>
<FORM METHOD=POST id="form1" action="/guoji/vshop/admin2/index.php/Article/add?<?php echo time(); ?>" enctype="multipart/form-data">
<TABLE cellpadding=3 cellspacing=3 class="add">
<TR>
	<TD class="tRight" >标题：</TD>
	<TD class="tLeft" ><INPUT TYPE="text" class="medium bLeftRequire"  check='Require' warning="标题不能为空" NAME="title" value="<?php echo ($vo["title"]); ?>" ID="title"></TD>
</TR>

<TR>
	<TD class="tRight" >简略标题：</TD>
	<TD class="tLeft" ><INPUT TYPE="text" class="large bLeftRequire"  check='Require' warning="标题不能为空" NAME="shorttitle"   ID="shorttitle" value="<?php echo ($vo["shorttitle"]); ?>"></TD>
</TR>

<TR>
	<TD class="tRight" >链接地址：</TD>
	<TD class="tLeft" ><INPUT TYPE="text" class="huge bLeftRequire" NAME="link"   ID="link" value="<?php echo ($vo["link"]); ?>"> 如果填上链接地址,将会直接跳转此地址.(链接地址需要有http://)</TD>
</TR>

<TR>
	<TD class="tRight">状态：</TD>
	<TD class="tLeft">
	<SELECT class="small bLeft"  NAME="status">
	<option <?php if(($vo[status]) == "1"): ?>selected<?php endif; ?> value="1">通过</option>
	<option <?php if(($vo[status]) == "0"): ?>selected<?php endif; ?> value="0">待审核</option>
	</SELECT>
	</TD>
</TR>

<TR>
	<TD class="tRight" >文章来源：</TD>
	<TD class="tLeft" ><input name="source" type="text" id="f_source" style="width:160px;" value="<?php echo C('site_name');?>"/> &nbsp;作 者：<input name="author" type="text" id="author" style="width:120px;"  value="<?php echo C('site_name');?>"/></TD>
</TR>

<TR>
	<TD class="tRight" >关键字：</TD>
	<TD class="tLeft" ><INPUT TYPE="text" class="large bLeftRequire" NAME="keyword" value="<?php echo ($vo['keyword']); ?>"></TD>
</TR>
<TR>
	<TD class="tRight" >描述</TD>
	<TD class="tLeft" >
	<TEXTAREA NAME="description" ROWS="6" COLS="45"><?php echo ($vo['description']); ?></TEXTAREA>
	</TD>
</TR>

<TR>
	<TD class="tRight" >分类：</TD>
	<TD class="tLeft" >
	<span id="cid_span">
	<select name="cid" id="cid" onchange="cid_change();">
	<?php if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><option value="<?php echo ($type[id]); ?>" <?php if(($vo[cid]) == $type[id]): ?>selected<?php endif; ?> >
	<?php echo ($type["node_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</select>
	</span>
	</TD>
</TR>
<?php if(($flags) != ""): ?><TR>
	<TD class="tRight" >属性：</TD>
	<TD class="tLeft" >
	<?php if(is_array($flags)): $k = 0; $__LIST__ = $flags;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$flag): $mod = ($k % 2 );++$k;?><input name="flags[]" value="<?php echo ($flag['id']); ?>" type="checkbox"
	<?php if($vo['flags']){ $ar = explode(',',$vo['flags']); if(array_search($flag['id'],$ar)!==false){ echo 'checked="checked"'; } } ?>
	><?php echo ($flag['name']); ?>&nbsp;
	<?php if($k%6==0){ echo "<br>"; } endforeach; endif; else: echo "" ;endif; ?>
	</TD>
</TR><?php endif; ?>
<TR>
	<TD class="tRight tTop">缩略图：</TD>
	<TD class="tLeft">
	<!--<input type="button" class="button" value="显示多缩略图" onclick="showhide()">-->
	<div>
		<input type="file" id="lit_pic" name="lit_pic" onchange="yulan(this,'show1')"> 大小260*260
		<div id="show1">
		<?php if(($vo["lit_pic"]) != ""): ?><img src="<?php echo ($vo["litpic"]); ?>">
		<INPUT TYPE="hidden" NAME="lit_pic" value="<?php echo ($vo["lit_pic"]); ?>"><?php endif; ?>
		</div>
	</div>
	</TD>
</TR>


<TR>
	<TD class="tRight tTop">广告图2：</TD>
	<TD class="tLeft">
	<div>
		<input type="file" id="lit_pic2" name="lit_pic2" onchange="yulan(this,'show2','img_id2')"> 大小750*337
		<div id="show2">
		<?php if(($vo["lit_pic2"]) != ""): ?><img src="<?php echo ($vo["lit_pic2"]); ?>" width="260px"><?php endif; ?>
		</div>
	</div>
	</TD>
</TR>
<!--
<TR>
	<TD class="tRight tTop">内 容：</TD>
	<TD class="tLeft"><font color="red">	<input type="button" onclick="window.open('/guoji/vshop/admin2/index.php/Article/ajax_upimg/jsdo/insertnrjson/hash/<?php echo ($hash); ?>')" class="button" value="上传图片"></font></TD>
</TR>
-->
<TR>
    <TD class="tRight tTop">内 容：</TD>
	<TD class="tLeft" colspan="2">
	<textarea id="content" style="width:650px;height:345px" name="content" ><?php echo ($vo["content"]); ?></textarea><script type="text/javascript" src="/guoji/vshop/admin2/Public/Js/KindEditor/kindeditor-min.js"></script><script>KindEditor.create('#content', {uploadJson : '/guoji/vshop/admin2/index.php/Attachment/editer_upload',fileManagerJson : '/guoji/vshop/admin2/index.php/Attachment/editer_manager',allowFileManager : false,items:['source','title', 'fontname', 'fontsize', '|', 'link', 'unlink','|', 'forecolor', 'bgcolor', 'bold','italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', '|', 'image', 'multiimage', 'emoticons','baidumap']});</script>
	</TD>
</TR>

<TR>
	<TD class="tRight" ></TD>
	<TD class="tLeft" >
	<div id="yulan"></div>
	</TD>
</TR>

<TR>
	<TD ></TD>
	<TD>
	<INPUT TYPE="submit" value="保 存" class="button" >
	</TD>
</TR>
</TABLE>
</FORM>

<!-- 主页面结束 -->

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