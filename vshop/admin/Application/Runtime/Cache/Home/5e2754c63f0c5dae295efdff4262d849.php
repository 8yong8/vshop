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
<script src="/guoji/vshop/admin2/Public/js/jquery.form.js"></script>  
<script src="/guoji/vshop/admin2/Public/js/Jcrop/jquery.Jcrop.min.js"></script>  
<link rel="stylesheet" href="/guoji/vshop/admin2/Public/js/Jcrop/css/jquery.Jcrop.css" type="text/css" /> 
<style>
.user_face_all {
    width: 710px;
    float: left;
    background: #fff;
    padding: 0 40px;
}

</style>
<script language="JavaScript">

</script>

<div class="content">
<div class="title">编辑帐号 [ <a href="/guoji/vshop/admin2/index.php/Member">返回列表</a> ]</div>
<div class="user_face_all">
        
        <div class="my_face"><!--my_face begin-->
      
      <div class="upload_ts">建议上传一张您的近期生活照，头像通过审核后才能被大家看到哦</div>
      <div class="upload_error" style="display:none;"></div>
      <div class="upload_ok" style="display:none;"></div>
      <div class="upload_area"><!--upload_area begin-->
        
        <div class="btns"><!--btns begin-->
          
          <form id="uploadImgForm" method="post" enctype="multipart/form-data">
            <input class="btn_file_molding" size="6" type="file" name="profileLogo" onchange="javascript:uploadImage(this);">
			<input id="member_id" name="member_id" value="<?php echo ($_GET['id']); ?>" type="hidden">
            <a href="#"></a>
          </form>
        </div>
        <!--btns end-->

        <div class="loading" style="display: none"><a href="#"></a></div>
        <img id="target" style="display: none;">
        <form id="logoCutForm" action="/guoji/vshop/admin2/index.php/Member/head/id/10108?<?php echo (NOW_TIME); ?>" method="post">
          <input id="filePath" name="filePath" type="hidden">
          <input id="member_id" name="member_id" value="<?php echo ($_GET['id']); ?>" type="hidden">
          <input id="x" name="x" type="hidden">
          <input id="y" name="y" type="hidden">
          <input id="h" name="h" type="hidden">
          <input id="w" name="w" type="hidden">
        </form>

        </div>
		
      <!--upload_area end-->
      
      <div class="preview_face"><!--preview_face begin-->
        
        <p>裁剪后的效果</p>
        <span><img src="<?php echo ((isset($img) && ($img !== ""))?($img):'/guoji/vshop/admin2/Public/images/noavatar_big.gif'); ?>" id="preview_120" width="120" height="120"></span> </div>
      <!--preview_face end-->
      
      <div class="btn_area"><a href="javascript:void(0);" style="display: none" class="save"></a>
        <div class="reload" style="display: none"><!--btns begin-->
          
          <form id="uploadImgForm" method="post" enctype="multipart/form-data">
            <input class="reload_input" size="6" type="file" name="profileLogo" onchange="javascript:uploadImage(this);">
			<input id="member_id" name="member_id" value="<?php echo ($_GET['id']); ?>" type="hidden">
            <a href="#"></a>
          </form>
        </div>
        <div class="reloading" style="display: none"><!--btns begin--> 
          
          <a href="javascript:void(0);"></a> </div>
        <!--btns end--> 
        
      </div>
    </div>
        
    </div>
</div>
<script>
$('.save').click(function(){
//alert(12223);
  $('#logoCutForm').submit();
});
var jcrop_api;
function prepareJcrop() {
	var b = null;
	var a = null;
	$("#target").Jcrop({
		onChange: function(d) {
			updatePreview(d, b, a)
		},
		onSelect: function(d) {
			updatePreview(d, b, a)
		},
		aspectRatio: 1
	},
	function() {
		var e = this.getBounds();
		b = e[0];
		a = e[1];
		var d = 120;
		if (a < 180) {
			d = 90
		}
		var c = (b / 2) - d / 2;
		var f = (a / 2) - d / 2;
		jcrop_api = this;
		jcrop_api.animateTo([c, f, c + d, f + d]);
		jcrop_api.setOptions(this.checked ? {
			minSize: [200, 200]
		}: {
			minSize: [200, 200]
		})
	})
}


function updatePreview(f, b, a) {
	if (parseInt(f.w) > 0) {
		var e = 180 / f.w;
		var d = 180 / f.h;
		$("#preview_180").css({
			width: Math.round(e * b) + "px",
			height: Math.round(d * a) + "px",
			marginLeft: "-" + Math.round(e * f.x) + "px",
			marginTop: "-" + Math.round(d * f.y) + "px"
		});
		e = 120 / f.w;
		d = 120 / f.h;
		$("#preview_120").css({
			width: Math.round(e * b) + "px",
			height: Math.round(d * a) + "px",
			marginLeft: "-" + Math.round(e * f.x) + "px",
			marginTop: "-" + Math.round(d * f.y) + "px"
		});
		e = 50 / f.w;
		d = 50 / f.h;
		$("#preview_50").css({
			width: Math.round(e * b) + "px",
			height: Math.round(d * a) + "px",
			marginLeft: "-" + Math.round(e * f.x) + "px",
			marginTop: "-" + Math.round(d * f.y) + "px"
		});
		$("#x").val(f.x);
		$("#y").val(f.y);
		$("#w").val(f.w);
		$("#h").val(f.h)
	}
}

function uploadImage(b) {
	var c = b.value;
	$("div.upload_area > div.btns").hide();
	$("div.my_face > div.upload_error").hide();
	$("div.my_face > div.upload_ts").hide();
	if (!$("div.btn_area>a.save").is(":visible")) {
		$("div.upload_area > div.loading").show()
	} else {
		$("div.btn_area > div.reloading").show()
	}
	$("div.btn_area > div.reload").hide();
	$("div.my_face > div.upload_ok").hide();
	var a = {
		//url: "http://localhost/cs/jcrop/upload.php",
		url: "/guoji/vshop/admin2/index.php/Member/head_up",
		type: "POST",
		dataType: "json",
		iframe: "true",
		success: function(d) {
			$("#target").attr("src", d.img).show();
			$("#preview_120").attr("src", d.img);
			$("#filePath").val(d.img);
			//$("#imghead").attr("src", d.img).show();
			//$('#imgUrl').val(d.img);
			if (jcrop_api != null) {
				jcrop_api.destroy()
			}
			prepareJcrop();
			$("div.upload_area > div.loading").hide();
			$("div.my_face > div.upload_ok ").text("已成功上传 " + c);
			$("div.my_face > div.upload_ok").show();
			$("div.btn_area > a").show();
			$("div.btn_area > div.reload").show();
			$("div.btn_area > div.reloading").hide()

		},
		error: function(d) {
			var e = d.responseText;
			if (e != null && e.indexOf("413 Request Entity Too Large") != -1) {
				$("div.my_face > div.upload_error").text("图片不要大于4M")
			} else {
				$("div.my_face > div.upload_error").text("上传失败，请稍后再试")
			}
			$("div.my_face > div.upload_error").show();
			$("div.upload_area > div.loading").hide();
			$("div.btn_area > div.reloading").hide();
			if ($("div.btn_area>a.save").is(":visible")) {
				$("div.btn_area > div.reload").show()
			} else {
				$("div.upload_area > div.btns").show()
			}
		}
	};
	$(b).parent().ajaxSubmit(a);
	return false
};
</script>
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