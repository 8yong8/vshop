<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title><?php echo ($headerTitle); ?></title>
<meta name="keywords" content="<?php echo ($headerKeywords); ?>" />
<meta name="description" content="<?php echo ($headerDescription); ?>" />

<link rel="stylesheet" rev="stylesheet" href="/guoji/vshop/Wap/Public/css/meixie.css">
<link rel="stylesheet" rev="stylesheet" href="/guoji/vshop/Wap/Public/css/login.css">
<link rel="stylesheet" rev="stylesheet" href="/guoji/vshop/Wap/Public/css/member.css">
<link type="image/gif" href="/favicon.gif" rel="shortcut icon">
<link type="image/gif" href="/favicon.gif" rel="bookmark">
<script src="/guoji/vshop/Wap/Public/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/common.js"></script>

<link href="/guoji/vshop/Wap/Public/js/artDialog/skins/twitter.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/artDialog/plugins/iframeTools.js"></script>
<script>
var ROOT = '/guoji/vshop/Wap';
var URL = '/guoji/vshop/Wap/index.php/Consignee';
var APP	 =	 '/guoji/vshop/Wap/index.php';
var PUBLIC = '/guoji/vshop/Wap/Public';
</script>
</head>
<body>
<div class="viewport">
<div class="wdl" id="notice_msg">您还未登录，请登录！</div>
<div class="top">
    <a href="/guoji/vshop/Wap/"><div class="logo"><img src="/guoji/vshop/Wap/Public/images/logo.png"></div></a>
    <div class="top_menu">
       <ul>

          <li><a href="/guoji/vshop/Wap/index.php?m=Search&a=top10"><img src="/guoji/vshop/Wap/Public/images/top_s.png"></a></li>

          <li>
		   <a href="javascript:;"><img src="/guoji/vshop/Wap/Public/images/top_e.png"></a>
		  </li>

          <li>
		   <?php if(($user) != ""): ?><a href="/guoji/vshop/Wap/index.php/Member"><img src="/guoji/vshop/Wap/Public/images/top_m.png"></a>
		   <?php else: ?>
		   <a href="/guoji/vshop/Wap/index.php/Public/login"><img src="/guoji/vshop/Wap/Public/images/top_m.png"></a><?php endif; ?>		  
		  </li>

          <li>
		   <?php if(($user) != ""): ?><a href="/guoji/vshop/Wap/index.php/Cart/lists"><img src="/guoji/vshop/Wap/Public/images/top_g.png"></a>
		   <?php else: ?>
		   <a href="/guoji/vshop/Wap/index.php/Public/login"><img src="/guoji/vshop/Wap/Public/images/top_g.png"></a><?php endif; ?>		  
		  </li>

       </ul>
    </div>
</div>
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/region.js" charset="UTF-8" p="<?php echo ($vo["pv_id"]); ?>" c1="<?php echo ($vo["ct_id"]); ?>" c2="<?php echo ($vo["dist_id"]); ?>"  id="js_region"></script>
<FORM METHOD=POST ACTION="" id="consignee_update" onsubmit="return check();">
<div class="menu">
   <a class="back" href="<?php if(($from_url) != ""): echo ($from_url); else: ?>/guoji/vshop/Wap/index.php/Consignee<?php endif; ?>"><img src="/guoji/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">修改收货地址</div>
</div>
<div class="menber">
   <div class="cdz_box">
      <ul>
         <li><span>收货人</span><div class="cdz_info"><input type="text" value="<?php echo ($vo["name"]); ?>" name="name" id="name" class="cdz_txt"></div></li>
         <li><span>手机号</span><div class="cdz_info"><input type="text" value="<?php echo ($vo["mobile"]); ?>" class="cdz_txt" id="mobile" name="mobile"></div></li>
		 <li id="select_region"></li>
         <li><span>详细地址</span><div class="cdz_info"><textarea id="addr" name="addr"><?php echo ($vo["addr"]); ?></textarea></div></li>
         <li><span>邮　编</span><div class="cdz_info"><input type="text" value="<?php echo ($vo["zip_code"]); ?>" class="cdz_txt" id="zip_code" name="zip_code"></div></li>
      </ul>
      <div class="cdz_mr"><input type="checkbox" name="default" value="1" <?php if(($vo["default"]) == "1"): ?>checked<?php endif; ?>>设为默认地址</div>
	  <INPUT TYPE="hidden" NAME="from_url" value="<?php echo ($from_url); ?>">
	  <INPUT TYPE="hidden" NAME="id" value="<?php echo ($vo["id"]); ?>">
      <div class="cdz_btn"><input type="submit" value="保存" class="btn_bc"></div>
   </div>
</div>
</FORM>
<script>
function check(){
  var name = $('#name').val();
  var mobile = $('#mobile').val();
  var addr = $('#addr').val();
  var zip_code = $('#zip_code').val();
  if(!name){
    showMessage('收件人必须',1000);
    return false;
  }
  if(!mobile){
    showMessage('手机号必须',1000);
    return false;
  }
  if(!addr){
    showMessage('地址必须',1000);
    return false;
  }
  if(!zip_code){
    showMessage('邮编必须',1000);
    return false;
  }
  send('consignee_update','/guoji/vshop/Wap/index.php/Consignee/edit');
  return false;
}
</script>
<!-- footer -->
<div class="f_menu">
   <ul> 
     <li><a href="/guoji/vshop/Wap/">首  页</a></li>
     <li><a href="/guoji/vshop/Wap/Help">帮助中心</a></li>
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
   <div class="c_menu"><a href="/guoji/vshop/Wap/Public/login">登录</a><a href="/guoji/vshop/Wap/Public/register">注册</a><a href="/guoji/vshop/Wap/Public/download">客户端</a></div>
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
			var wx_imgUrl = '/guoji/vshop/Wap/Public/images/fx_logo.png';
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