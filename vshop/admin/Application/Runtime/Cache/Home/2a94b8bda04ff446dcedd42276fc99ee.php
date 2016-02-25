<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/git/vshop/admin/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/git/vshop/admin/Public/js/jquery.js"></script>

<script type="text/javascript" src="/git/vshop/admin/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/git/vshop/admin/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/git/vshop/admin';
var URL = '/git/vshop/admin/index.php/Index';
var APP	 =	 '/git/vshop/admin/index.php';
var PUBLIC = '/git/vshop/admin/Public';
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
		<img src="/git/vshop/admin/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/git/vshop/admin/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/git/vshop/admin/index.php?m=Cache&a=clear"><img src="/git/vshop/admin/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/git/vshop/admin/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/git/vshop/admin/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/git/vshop/admin/index.php" target="_blank">网站前台</a>|
        <a href="/git/vshop/admin/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/git/vshop/admin/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/git/vshop/admin/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/git/vshop/admin/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/git/vshop/admin/Public/images/ico_8.png" /></div>
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
    <div class="site">
        <?php echo C('site_name');?> <a href="#"><?php echo ($board_title); ?></a> > <?php echo ($node_title); ?>
    </div>
    <span class="line_white"></span>
    <?php if (file_exists(DOC_ROOT.'install/')): ?>
        <div class="tisi mt5" tips-id="09">系统检测到存在安装向导目录[install]。为了安全起见，建议您尽快删除或重命名。<a href="javascript:;" class="closetips" title="关闭后24小时内将不再显示">X</a></div>
    <?php endif ?>
    <dl class="box fl mt10">
        <dt><strong>今日统计概况</strong></dt>
        <dd>
            <ul class="odd1">
                <li><span>￥ <?php echo ($real_amount); ?> 元</span>销售总额：</li>
                <li><span><?php echo ($total_order); ?> 个</span>订单数量：</li>
                <li><span> <?php echo ($withdraw); ?> 笔</span>提现申请：</li>
                <li><span><?php echo ($reg); ?> 人</span>今日新增会员：</li>
                <li><span><?php echo ($feedback); ?> 条</span>今日新增商品评论：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fr mt10">
        <dt><strong>订单处理情况</strong></dt>
        <dd>
            <ul class="odd2">
                <li><span><a href="<?php echo U('Order/index',array('status'=>0));?>" target="_blank" title="未处理订单总数"><i><?php echo ($dqr_count); ?></i></a> 个</span>未处理订单总数：</li>
                <li><span><a href="<?php echo U('Order/index',array('delivery_status'=>1,''=>0));?>"  target="_blank" title="待发货订单总数"><i><?php echo ($dfh_count); ?></i></a> 个</span>待发货订单总数：</li>
                <li><span><a href="/git/vshop/admin/index.php?c=user&c=member_consult&a=lists" title="商品咨询总数"><?php echo $order_count['consult_total'] ?></a> 条</span>商品咨询总数：</li>
                <li><span><a href="/git/vshop/admin/index.php?c=user&c=member_consult&a=lists" title="未处理咨询总数"><i><?php echo $order_count['consult_reply'] ?></i></a> 条</span>未处理咨询总数：</li>
                <li><span><a href="<?php echo U('Order/index',array('status'=>2));?>" title="已完成订单总数"><?php echo ($wc_count); ?></a>个</span>已完成订单总数：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fl mt10">
        <dt><strong>商品信息统计</strong></dt>
        <dd>
            <ul class="odd3">
                <li><span><a href="<?php echo U('Product/index',array('status'=>1));?>" title=""><i><?php echo ($p_count); ?></i></a> 件</span>上架商品总数：</li>
                <li><span><a href="<?php echo U('Product/index',array('jg'=>1));?>" title=""><i><?php echo ($jg_count); ?></i></a> 件</span>商品库存警告：</li>
                <li><span><a href="<?php echo U('Goods/Goods/lists',array('label'=>2)) ?>" title=""><i><?php echo $goods_count['goods_message'] ?></i></a> 件</span>商品缺货登记：</li>
                <li><span><i><?php echo ($ct_count); ?></i> 张</span>今日发放优惠券：</li>
                <li><span><i><?php echo ($cu_count); ?></i> 张</span>今日使用优惠券：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fr mt10">
        <dt><strong>系统信息</strong></dt>
        <dd>
            <ul class="odd4">
                <li><span>THinkphp <?php echo (THINK_VERSION); ?></span>程序版本：</li>
                <li><span><?php echo $sys_info['os'] ?> / PHP v<?php echo $sys_info['phpv'] ?></span>服务器系统及PHP：</li>
                <li><span><?php echo $sys_info['web_server'] ?></span>服务器软件：</li>
                <li><span><?php echo $sys_info['mysqlv'] ?></span>服务器MySQL版本：</li>
                <li><span><?php echo $sys_info['mysqlsize'] ?> MB</span>当前数据库尺寸：</li>
            </ul>
        </dd>
    </dl>
    <div class="clear"></div>
<script>
$(function(){
    $(".odd1 li:even").css("background","#fff");
    $(".odd2 li:even").css("background","#fff");
    $(".odd3 li:even").css("background","#fff");
    $(".odd4 li:even").css("background","#fff");
    $(".odd5 li:even").css("background","#fff");
});
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
			$(this).children().attr('src','/git/vshop/admin/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/git/vshop/admin/Public/images/ico_8.png');
		}
	  );
</script>