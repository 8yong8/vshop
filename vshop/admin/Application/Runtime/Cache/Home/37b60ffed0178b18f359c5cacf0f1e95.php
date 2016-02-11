<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理中心</title>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/style.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/artDialog/skins/blue.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/plugins/iframeTools.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
.content {
  padding: 0 8px;
  background: #f1f1f1;
  min-width: 400px;
  height: 100%;
   
}

.tishi {
  border: 1px solid #cdcdcd;
  /*margin-top: 160px;*/
  width: 50%;   height: 50%;   overflow: auto;   margin: auto;   position: absolute;   top: 0; left: 0; bottom: 0; right: 0; 
}

</style>
</head>
<body>
<div class="content">

    <div class="" style="height: 20px;"></div>
    <dl class="tishi" style="width:<?php echo ((isset($_GET['width']) && ($_GET['width'] !== ""))?($_GET['width']):400); ?>px;height:<?php echo ((isset($_GET['height']) && ($_GET['height'] !== ""))?($_GET['height']):200); ?>px;">
    	<dt><em><img src="/guoji/vshop/ADMIN2/Public/images/ico_i.png" /></em><span>温馨提示</span></dt>
        <dd>
            <h3><?php echo ($message); ?></h3>
				<p>
                	<a href="<?php echo ($jumpUrl); ?>">如果您的浏览器没有自动跳转，请点击这里</a>
                </p>
        </dd>
    </dl>

</div>


<script language="javascript">
setTimeout(function(){
   window.location.href = '<?php echo ($jumpUrl); ?>';
},1500);
</script>