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
<div class="menu">
   <a class="back" href="<?php if(($from_url) != ""): echo (resetUrl($from_url,'consignee_id')); else: ?>/guoji/vshop/Wap/index.php/Member<?php endif; ?>"><img src="/guoji/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">地址管理</div>
   <a class="btn_tj" href="/guoji/vshop/Wap/index.php/Consignee/add<?php if(($from_url) != ""): ?>?from_url=<?php echo (urlencode($from_url)); endif; ?>"><img src="/guoji/vshop/Wap/Public/images/btn_tj.png"></a>
</div>
<script>
var from_url = "<?php echo (resetUrl($from_url,'consignee_id')); ?>";
//默认选择
function choice(id){
	window.location.href = from_url+'&consignee_id='+id;
}

//设置默认
function setdefault(id){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Consignee/setdefault',
		data:'id='+id,
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
			if(data.error_code==0){
				showMessage('设置成功',1000);
				$('.dz_box').removeClass('dz_cur');
				$('#c'+id).addClass('dz_cur');
				return;
			}else{
				showMessage(data.notice,2000);
			}
		}
	});

}
//编辑
function edit(id){
  window.location.href = APP+'/Consignee/edit?&id='+id+'<?php if(($from_url) != ""): ?>&from_url=<?php echo (urlencode($from_url)); endif; ?>';
}

//删除
function del(id,o){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:APP+'/Consignee/delete',
		data:'id='+id,
		async: false,
		error: function(request) {
			alert("Connection error");
		},
		success: function(data) {
			if(data.error_code==0){
				$('#c'+id).remove();
				return;
			}else{
				showMessage(data.notice,2000);
			}
		}
	});
}
</script>
<div class="menber">
   <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$consignee): $mod = ($i % 2 );++$i;?><div class="dz_box <?php if(($consignee["default"]) == "1"): ?>dz_cur<?php endif; ?>" id="c<?php echo ($consignee["id"]); ?>">
      <p class="sg"><em><?php echo ($consignee["name"]); ?></em><?php echo ($consignee["mobile"]); ?></p>
      <p><?php echo ($consignee["province"]); ?> <?php echo ($consignee["city"]); ?> <?php echo ($consignee["district"]); ?> <?php echo ($consignee["addr"]); ?></p>
	  <?php if(($from_url) != ""): ?><p class="mgt15"><input type="button" value="修改并选择" class="dz_xg"  onclick="edit(<?php echo ($consignee["id"]); ?>);"><input type="button" value="选择" class="dz_xz" onclick="choice(<?php echo ($consignee["id"]); ?>);"></p>
	  <?php else: ?>
      <p class="mgt15"><input type="button" value="修改" class="dz_xg"  onclick="edit(<?php echo ($consignee["id"]); ?>);"><input type="button" value="默认" class="dz_xz" onclick="setdefault(<?php echo ($consignee["id"]); ?>);">  
	  <input type="button" value="删除" class="dz_sc" onclick="del(<?php echo ($consignee["id"]); ?>);"></p><?php endif; ?>
   </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
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