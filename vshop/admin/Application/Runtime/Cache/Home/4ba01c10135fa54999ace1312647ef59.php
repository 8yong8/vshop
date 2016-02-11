<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/js/artDialog/skins/blue.css" />
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/plugins/iframeTools.js"></script>
<title>图片上传</title>

<meta http-equiv="MSThemeCompatible" content="Yes" />

</head>

<body style="background:#fff">

<?php if(!isset($_GET['error_code'])){ ?>

<div></div>

<div style="background:#fefbe4;border:1px solid #f3ecb9;color:#993300;padding:10px;width:90%;margin:40px auto 5px auto;">选中文件后点击上传按钮或者点击“从素材库选择”直接从已上传文件中选择</div>

<form enctype="multipart/form-data" action="" id="thumbForm" method="POST" style="font-size:14px;padding:30px 20px 10px 20px;">

<p><div><div style="font-size:14px;">选择本地文件：<br><br>

<input type="file" style="width:80%;border:1px solid #ddd" name="<?php echo ((isset($_GET['name']) && ($_GET['name'] !== ""))?($_GET['name']):'photo'); ?>">
<INPUT TYPE="hidden" NAME="width">
<INPUT TYPE="hidden" NAME="height">
</div><div style="padding:20px 0;text-align:center;"><input id="submitbtn" name="doSubmit" type="submit" class="btnGreen" value="上传" onclick="this.value='上传中...'"></input> 
<!--
<input name="btnchoose" onclick="location.href='{weiwin::U('Attachment/my',array('type'=>'my'))}'" type="button" class="btnGreen" value="从素材库选择" />
-->
</div></p>

<input type="hidden" value="<?php echo ($_GET['width']); ?>" id="width" name="width" />
<input type="hidden" value="<?php echo ($_GET['height']); ?>" id="height" name="height" />

</form>

<script>

if (art.dialog.data('width')) {

	document.getElementById('width').value = art.dialog.data('width');// 获取由主页面传递过来的数据

	document.getElementById('height').value = art.dialog.data('height');

};

</script>

<?php }else{ ?>

<div style="text-align:center;line-height:140px;font-size:14px;">
<span style="float:left;width:100%;"><img src="<?php echo ($_GET['msg']); ?>" height="<?php echo ((isset($_GET['height']) && ($_GET['height'] !== ""))?($_GET['height']):300); ?>"></span>
<span style="float:left;width:100%;height:30px;"><img src="/guoji/vshop/admin2/Public/images/export.png"  /> <?php if($_GET['error']==0){echo '上传成功';}else{echo $_GET['msg'];} ?> </span></div>

<script>

var domid=art.dialog.data('domid');

// 返回数据到主页面

function returnHomepage(url){

	var origin = artDialog.open.origin;

	var dom = origin.document.getElementById(domid);

	var domsrcid=domid+'_src';



	if(origin.document.getElementById(domsrcid)){

	origin.document.getElementById(domsrcid).src=url;

	}
	$(window.parent.document).find("#"+domid).val(url); 
	setTimeout("art.dialog.close()", 1000 )

}

<?php if($_GET['error_code']==0){ ?>

returnHomepage('<?php echo $_GET['msg']; ?>');

<?php } ?>

</script>

<?php } ?>

</body>

</html>