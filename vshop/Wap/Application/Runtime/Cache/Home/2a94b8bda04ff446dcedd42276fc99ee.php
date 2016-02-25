<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title><?php echo ($headerTitle); ?></title>
<meta name="keywords" content="<?php echo ($headerKeywords); ?>" />
<meta name="description" content="<?php echo ($headerDescription); ?>" />

<link rel="stylesheet" rev="stylesheet" href="/git/vshop/Wap/Public/css/meixie.css">
<link rel="stylesheet" rev="stylesheet" href="/git/vshop/Wap/Public/css/login.css">
<link rel="stylesheet" rev="stylesheet" href="/git/vshop/Wap/Public/css/member.css">
<link type="image/gif" href="/favicon.gif" rel="shortcut icon">
<link type="image/gif" href="/favicon.gif" rel="bookmark">
<script src="/git/vshop/Wap/Public/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/git/vshop/Wap/Public/js/common.js"></script>

<link href="/git/vshop/Wap/Public/js/artDialog/skins/twitter.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/git/vshop/Wap/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/git/vshop/Wap/Public/js/artDialog/plugins/iframeTools.js"></script>
<script>
var ROOT = '/git/vshop/Wap';
var URL = '/git/vshop/Wap/index.php/index';
var APP	 =	 '/git/vshop/Wap/index.php';
var PUBLIC = '/git/vshop/Wap/Public';
</script>
</head>
<body>
<div class="viewport">
<div class="wdl" id="notice_msg">您还未登录，请登录！</div>
<div class="top">
    <a href="/git/vshop/Wap/"><div class="logo"><img src="/git/vshop/Wap/Public/images/logo.png"></div></a>
    <div class="top_menu">
       <ul>

          <li><a href="/git/vshop/Wap/index.php?m=Search&a=top10"><img src="/git/vshop/Wap/Public/images/top_s.png"></a></li>

          <li>
		   <a href="javascript:;"><img src="/git/vshop/Wap/Public/images/top_e.png"></a>
		  </li>

          <li>
		   <?php if(($user) != ""): ?><a href="/git/vshop/Wap/index.php/Member"><img src="/git/vshop/Wap/Public/images/top_m.png"></a>
		   <?php else: ?>
		   <a href="/git/vshop/Wap/index.php/Public/login"><img src="/git/vshop/Wap/Public/images/top_m.png"></a><?php endif; ?>		  
		  </li>

          <li>
		   <?php if(($user) != ""): ?><a href="/git/vshop/Wap/index.php/Cart/lists"><img src="/git/vshop/Wap/Public/images/top_g.png"></a>
		   <?php else: ?>
		   <a href="/git/vshop/Wap/index.php/Public/login"><img src="/git/vshop/Wap/Public/images/top_g.png"></a><?php endif; ?>		  
		  </li>

       </ul>
    </div>
</div>
<div class="home_menu">
   <ul>
      <li><a href="/git/vshop/Wap/"><img src="/git/vshop/Wap/Public/images/home_menu_01.png" width="100%"></a></li>
      <li><a href="/git/vshop/Wap/Product/lists"><img src="/git/vshop/Wap/Public/images/home_menu_02.jpg" width="100%"></a></li>
      <li><a href="/git/vshop/Wap/Product/lists?tag=1"><img src="/git/vshop/Wap/Public/images/home_menu_03.jpg" width="100%"></a></li>
      <li><a href="/git/vshop/Wap/Product/brand"><img src="/git/vshop/Wap/Public/images/home_menu_04.jpg" width="100%"></a></li>
   </ul>
</div>

  <?php if(is_array($positions[10])): $i = 0; $__LIST__ = $positions[10];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($i % 2 );++$i;?><div class="ad mgt15"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>

  <?php if(is_array($positions[11])): $k = 0; $__LIST__ = $positions[11];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><div class="ad  bbb"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>

  <?php if(is_array($positions[12])): $k = 0; $__LIST__ = $positions[12];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><div class="ad  mgt15"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>

  <?php if(is_array($positions[13])): $k = 0; $__LIST__ = $positions[13];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><div class="ad  bbb"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>

  <?php if(is_array($positions[14])): $k = 0; $__LIST__ = $positions[14];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><div class="ad  mgt15"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>

  <?php if(is_array($positions[15])): $k = 0; $__LIST__ = $positions[15];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><div class="ad  bbb"><a href="<?php echo ($position["url"]); ?>"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></div><?php endforeach; endif; else: echo "" ;endif; ?>
<div class="home_kd">
  <ul>
	 <?php if(is_array($positions[16])): $k = 0; $__LIST__ = $positions[16];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($k % 2 );++$k;?><li><a href="<?php echo ($position["url"]); ?>" class="l"><img src="<?php echo ($position["lit_pic"]); ?>" width="100%"></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
</div>
<?php if(($positions[17]) != ""): ?><div class="f_kd">
   <div class="kd_tit">品牌推荐</div>
   <div class="kd_box">
     <ul>
	 <?php if(is_array($positions[17])): $i = 0; $__LIST__ = $positions[17];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($i % 2 );++$i; if ($i%4==0 && $i!=0){ ?>
	 </ul>
	 <ul>
	 <?php } ?>
	 <?php if (($i+1)%4==0 && $i!=0){ ?>
	 <li><a href="<?php echo ($position["url"]); ?>"  class="brw"><?php echo ($brand["title"]); ?></a></li>
	 <?php }else { ?>
	 <li><a href="<?php echo ($position["url"]); ?>"><?php echo ($brand["title"]); ?></a></li>
	 <?php } endforeach; endif; else: echo "" ;endif; ?>
	 </ul>
   </div>
   <!--
   <div class="kd_more">
   <a href="#">显示更多</a>
   </div>
   -->
</div><?php endif; ?>
<!-- footer -->
<div class="f_menu">
   <ul> 
     <li><a href="/git/vshop/Wap/">首  页</a></li>
     <li><a href="/git/vshop/Wap/Help">帮助中心</a></li>
     <li><a href="#">反馈建议</a></li>
   </ul>
</div>
<div class="foot">
   <ul>
      <li><span class="foot_c">厂家直供<br>百分百正品</span></li>
      <li><span class="foot_s">7天包退换</span></li>
   </ul>
</div>
<div class="copy">
   <div class="c_menu"><a href="/git/vshop/Wap/Public/login">登录</a><a href="/git/vshop/Wap/Public/register">注册</a><a href="/git/vshop/Wap/Public/download">客户端</a></div>
   <div class="c_info">
   <p><?php echo ($configs["site_copyright"]); ?></p><?php echo ($configs["site_icp"]); ?>
   <!--<p>&copy;2015美鞋家 ALL Rights Reserved</p>浙ICP备12398761-->
   </div>
</div>
</div>
</body>
</html>
<!-- 消息提示框/-->
<?php if(($iswx) == "true"): ?><script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
$(function(){
	<?php if(($user) != ""): ?>var fx_url = location.href.split('#')[0];
	var reg = '/\?/';
	var r = fx_url.match(reg);
	if(r==null){
	  //fx_url += '?&referee=<?php echo (base64_encode($user->account)); ?>';
		<?php if(($user["mobile"]) != ""): ?>fx_url += '?&r=<?php echo (base64_encode($user["mobile"])); ?>';
		<?php else: ?>
			fx_url += '?&r=<?php echo (base64_encode($user["openid"])); ?>';<?php endif; ?>
	}else{
	  
		<?php if(($user["mobile"]) != ""): ?>fx_url += '&r=<?php echo (base64_encode($user["mobile"])); ?>';
		<?php else: ?>
			fx_url += '&r=<?php echo (base64_encode($user["openid"])); ?>';<?php endif; ?>
	}
	<?php else: ?>
	var fx_url = location.href.split('#')[0];<?php endif; ?>
	var wx_url = location.href.split('#')[0];
	//alert(wx_url);
	//微信处理
	$.ajax({
		type:"GET",
		dataType: "json",
		url: APP+"/Public/wxsign?&url="+encodeURIComponent(wx_url),
		success:function(signPackage){
		  //alert(signPackage.appId);
		  //alert(location.href.split('#')[0]);
		  wx.config({
			//debug: true,
			appId: signPackage.appId,
			timestamp: signPackage.timestamp,
			nonceStr: signPackage.nonceStr,
			signature: signPackage.signature,
			jsApiList: [
				  'checkJsApi',
				  'onMenuShareAppMessage',
				  'onMenuShareTimeline',
				  'onMenuShareQQ',
				  'onMenuShareWeibo'
			  // 所有要调用的 API 都要加到这个列表中
			]
		  });
		  wx.ready(function () {
			// 在这里调用 API
			//wx.hideOptionMenu();
			var wx_title = "<?php echo ($wx_title); ?>";
			var wx_link = fx_url;
			var wx_imgUrl = '/git/vshop/Wap/Public/images/fx_logo.png';
			var wx_desc = "免费送鞋";
			//分享到朋友圈
			wx.onMenuShareTimeline({
				title: wx_title, // 分享标题
				link: wx_link, // 分享链接
				imgUrl: wx_imgUrl, // 分享图标
				success: function () {
					// 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			//分享给朋友
			wx.onMenuShareAppMessage({
				title: wx_title, // 分享标题
				desc: wx_desc, // 分享描述
				link: wx_link, // 分享链接
				imgUrl: wx_imgUrl, // 分享图标
				type: 'link', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function () { 
					// 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			wx.onMenuShareQQ({
				title: wx_title, // 分享标题
				desc: wx_desc, // 分享描述
				link: wx_link, // 分享链接
				imgUrl: wx_imgUrl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
				   // 用户取消分享后执行的回调函数
				}
			});
			wx.onMenuShareWeibo({
				title: wx_title, // 分享标题
				desc: wx_desc, // 分享描述
				link: wx_link, // 分享链接
				imgUrl: wx_imgUrl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});

		  });
		}
	})

});
</script><?php endif; ?>