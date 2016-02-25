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
var URL = '/git/vshop/admin/index.php/Config';
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
	<style>
		#Validform_msg{display: none}
	</style>
	<div class="site">
		<?php echo C('site_name');?> <?php echo ($board_title); ?> > <?php echo ($node_title); ?>
	</div>
	<span class="line_white"></span>
	<div class="install tabs mt10">
		<dl>
			<dt>
			  <a href="javascript:" class="hover">站点信息</a>
			  <a href="javascript:">基本设置</a>
			  <a href="javascript:">购物设置</a>
			  <a href="javascript:">快递设置</a>
			  <a href="javascript:">站点配置</a>
			</dt>
			<form name="form" method="post" action="/git/vshop/admin/index.php/Config/edit?<?php echo time();?>">
				<dd>
					<ul class="web p0">
						<li> <strong>商城名称：</strong>
							<input type="text" class="text_input" name="site_name" value="<?php echo ($config["site_name"]); ?>" />
							<span style="margin-left:-1px">商城名称：将显示在导航条和标题中</span>
						</li>
						<li> <strong>公司名称：</strong>
							<input type="text"  class="text_input" name="site_company" value="<?php echo ($config["site_company"]); ?>" />
							<span style="margin-left:-1px">公司名称：将显示在公司信息处</span> 
						</li>
						<li> <strong>公司地址：</strong>
							<input type="text"  class="text_input" name="company_address" value="<?php echo ($config["company_address"]); ?>" />
							<span style="margin-left:-1px">公司地址：将显示在公司信息处</span> 
						</li>
						<li> <strong>工作时间：</strong>
							<input type="text"  class="text_input" name="work_time" value="<?php echo ($config["work_time"]); ?>" />
							<span style="margin-left:-1px">公司上班时间：将显示在页面底部公司信息处</span> 
						</li>
						<li> <strong>商城URL：</strong>
							<input type="text" class="text_input" name="site_url" value="<?php echo ($config["site_url"]); ?>" />
							<span style="margin-left:-1px">商城URL：将作为链接显示在页面底部</span> </li>
						<li> <strong>版权信息：</strong>
							<input type="text" class="text_input" name="site_copyright" value="<?php echo ($config["site_copyright"]); ?>" />
							<span style="margin-left:-1px">页面底部可以显示版权信息</span>
						</li>
						<li> <strong>网站备案信息代码：</strong>
							<input type="text" class="text_input" name="site_icp" value="<?php echo ($config["site_icp"]); ?>" />
							<span style="margin-left:-1px">页面底部可以显示ICP备案信息，如果网站已备案，在此输入您的备案号，它将显示在页面底部，如果没有请留空</span>
						</li>

						<li> <strong>网站第三方统计代码：</strong>
							<textarea name="site_countcode"><?php echo ($config["site_countcode"]); ?></textarea>
							<p>页面底部可以显示第三方统计，推荐CNZZ,百度统计</p>
						</li>
					</ul>
				</dd>
				<dd>
					<ul class="web p1">
						<li> <strong>商城LOGO：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["site_logo"]); ?>" name="site_logo" id="site_logo" />
							<font class="uplogo" style="cursor: pointer;position: absolute;left:300px;line-height: 22px;" onclick="PicUpload('site_logo',120,120)">选择</font>

							<font class="uplogo" style="cursor: pointer;line-height: 22px;position: absolute;left:330px;" onclick="viewImg('site_logo')">预览</font>

							<span>填写商城LOGO地址，请用逗号隔开URL，宽度和高度，如：logo.gif,120,60</span> </li>
						<li> <strong>标题附加字：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["site_subtitle"]); ?>" name="site_subtitle" />
							<span>网页标题通常是搜索引擎关注的重点，本附加字设置出现在标题中商城名称后，如有多个关键字，建议用分隔符分隔</span> </li>
						<li> <strong>Meta Keywords：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["site_keywords"]); ?>" name="site_keywords" />
							<span>Keywords项出现在页面头部的&lt;Meta&gt;标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔</span> </li>
						<li> <strong>Meta Description：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["site_description"]); ?>" name="site_description" />
							<span style="margin-left:-2px">Description出现在页面头部的Meta标签中，用于记录本页面的高腰与描述，建议不超过80个字</span> </li>
						<!--
						<li> <strong>其他头部信息：</strong>
							<textarea name="site_headecode"><?php echo C('site_headecode') ?></textarea>
							<p>如需在&lt;head&gt;&lt;/head&gt;中添加其他的HTML代码，可以使用本设置，否则请留空</p>
						</li>
						-->
					</ul>
				</dd>

				<dd>
					<ul class="web p2">

						<li> <strong>库存下降设置：</strong>
							<select name="site_inventorysetup" style="margin-right: 50px;">
								<option value="1" 
								<?php  if ((int)C('site_inventorysetup') == 1) {?> selected = "selected" <?php } ?> >订单下单成功时库存下降</option>
								<option value="2" 
								 <?php  if ((int)C('site_inventorysetup') == 2) {?> selected = "selected" <?php } ?> >订单支付完成时库存下降</option>
							</select>
							<span style="margin-left:-1px">设置库存下降时机，默认为当用户下单成功时商品库存下降，需开启库存管理</span>
						</li>
						<li> <strong>订单有效时间：</strong>
							<input type="text" class="text_input" name="order_expired" value="<?php echo C('order_expired') ?>" />
							<span style="margin-left:-2px">用户下单后，订单存在有效时间，超过这个时间自动关闭。单位小时，0则不过期。</span> 
						</li>
						<li> <strong>货币单位设置：</strong>
							<input type="text" class="text_input" name="site_monetaryunit" value="<?php echo C('site_monetaryunit') ?>" />
							<span style="margin-left:-1px">设置显示的商品价格格式，%s将被替换为相应的价格数字，默认为：￥%s元</span>
						</li>
						<li> <strong>商品货号前缀：</strong>
							<input type="text" class="text_input" name="site_numprefix" value="<?php echo C('site_numprefix') ?>" />
							<span style="margin-left:-2px">网页标题通常是搜索引擎关注的重点，本附加字设置出现在标题中商城名称后，如有多个关键字，建议用分隔符分隔</span> 
						</li>
						<li> <strong>积分消费设置：</strong>
							<input type="text" class="text_input" name="site_integralsetup" value="<?php echo C('site_integralsetup') ?>" />
							<span style="margin-left:-1px">设置每100个积分可以抵用多少元现金，默认为100元抵用1元现金，关闭此功能请设为0</span> 
						</li>
						<li> <strong>积分消费限制：</strong>
							<input type="text" class="text_input" name="site_integrallimit" value="<?php echo C('site_integrallimit') ?>" />
							<span style="margin-left:-1px">设置每消费100元最多可以使用多少元积分，设为0则不限制，此功能必须开启积分消费功能</span> 
						</li>
						<li> <strong>是否开启发票功能：</strong> <b>
								<label>
									<input type="radio" name="site_invoice" value="1" 
									  <?php  if (C('site_invoice') == 1) {?> checked <?php } ?> />
									开启 </label>
								<label>
									<input type="radio" name="site_tax" value="0"
									  <?php  if (C('site_tax') == 0) {?> checked <?php } ?> />
									关闭 </label>	
							</b> <span style="margin-left:-1px">设置是否启用发票功能,默认开启</span> 
						</li>
						<li> <strong>发票内容设置：</strong>
							<textarea name="tax_content"><?php echo C('tax_content') ?></textarea>
							<p>客户要求开发票时可以选择的内容。例如：办公用品。每一行代表一个选项</p>
						</li>
						<li> <strong>发票税率：</strong>
							<input type="text" class="text_input" name="tax_rate" value="<?php echo C('tax_rate') ?>" />
							<span style="margin-left:-1px">设置开发票的税率，单位为%，要开启发票功能才有效</span> 
						</li>
					</ul>
				</dd>
				<dd>
					<ul class="web p3">
						<li> <strong>默认快递公司：</strong>
							<select name="shipping_id" style="margin-right: 50px;">
								<?php if(is_array($shippings)): $i = 0; $__LIST__ = $shippings;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shipping): $mod = ($i % 2 );++$i;?><option value="<?php echo ($shipping["id"]); ?>" <?php if(($config["shipping_id"]) == $shipping['id']): ?>selected<?php endif; ?> ><?php echo ($shipping["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
							
							<span style="margin-left:-1px">将以此快递公司设置价格收取快递费用</span> 
						</li>
						<li> <strong>收件人姓名：</strong>
							<input type="text" class="text_input" name="consignee" value="<?php echo C('consignee');?>" />
							<span style="margin-left:-1px">寄件人姓名：将显示在快递单寄件人栏中</span> 
						</li>
						<li> <strong>收件人电话：</strong>
							<input type="text" class="text_input" name="consignee_mobile" value="<?php echo C('consignee_mobile');?>" />
							<span style="margin-left:-1px">寄件人电话：将显示在快递单寄件人电话栏中</span> 
						</li>
						<li> <strong>收件人地址：</strong>
							<input type="text" class="text_input" name="consignee_address" value="<?php echo C('consignee_address');?>" />
							<span style="margin-left:-1px">寄件人地址：将显示在快递单寄件人地址栏中</span> 
						</li>
						<li> <strong>快递秘钥：</strong>
							<input type="text" class="text_input" name="kuaidi_key" value="<?php echo C('kuaidi_key');?>" />
							<span style="margin-left:-1px">快递查询秘钥：[由快递100提供技术支持]</span> 
						</li>
					</ul>
				</dd>
				<dd>
					<ul class="web p3">
						<li> <strong>短信平台账号：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["sms_id"]); ?>" name="sms_id" />
							<span>短信平台账号 (<a href="http://www.zucp.net/" target="_blank"> 北京创世漫道科技有限公司</a>)</span> 
						</li>
						<li> <strong>短信平台密码：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["sms_pw"]); ?>" name="sms_pw" />
							<span>短信平台密码或key</span> 
						</li>
						<li> <strong>后台默认分页数：</strong>
							<input type="text" class="text_input" value="<?php echo ($config["admin_page_size"]); ?>" name="admin_page_size" />
							<span>后台默认分页数：管理员后台默认每页显示数据数量</span> 
						</li>
					</ul>
				</dd>

				<div class="input1" style="clear:both;padding-top:10px;">
					<input type="submit" value="提交" class="button_search">
				</div>
			</form>
		</dl>
	<script>  
		//切换
		$(function() {
			var tabTitle = ".tabs dt a";
			var tabContent = ".tabs dd";
			$(tabTitle + ":first").addClass("hover");
			$(tabContent).not(":first").hide();
			$(tabTitle).unbind("click").bind("click", function() {
				$(this).siblings("a").removeClass("hover").end().addClass("hover");
				var index = $(tabTitle).index($(this));
				$(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
			});
			//默认选中
			$(".tabs dt a").eq("<?php echo ($showpage); ?>").siblings("a").removeClass("hover").end().addClass("hover");
			$(".tabs dd").eq("<?php echo ($showpage); ?>").siblings(tabContent).hide().end().fadeIn(0);
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