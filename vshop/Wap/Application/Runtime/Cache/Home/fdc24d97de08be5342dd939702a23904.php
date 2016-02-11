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
var URL = '/guoji/vshop/Wap/index.php/Member';
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
   <a class="back" href="javascript:;" onclick="history.go(-1);"><img src="/guoji/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">会员中心</div>
   <a class="out" href="/guoji/vshop/Wap/index.php/Public/logout"><img src="/guoji/vshop/Wap/Public/images/btn_out.png"></a>
</div>
<div class="menber">
    <div class="m_info"><input type="button" value="编辑头像" onclick="window.location='/guoji/vshop/Wap/index.php/Member/edit'" class="bj_tx">
         <div class="m_tx">
			<?php if(($user["logo"]) != ""): ?><img src="<?php echo ($user["logo"]); ?>" width="100%" height="100%">
			<?php else: ?>
            <img src="/guoji/vshop/Wap/Public/images/m_tx.png" width="100%" height="100%"><?php endif; ?>
         </div>
         <div class="m_zh">
            <p><span>
			<?php echo ($user["username"]); ?>
			</span></p>
            <p><?php echo ($user["lv_name"]); ?></p>
            <p>云币：<em><?php echo ($user["balance"]); ?></em></p>
         </div>
    </div>
    <div class="my_order">
        <div class="my_allorder"><a href="/guoji/vshop/Wap/index.php/Order">我的订单<span>查看全部已购商品</span></a></div>
        <div class="mo_list">
           <ul>
              <li><a href="/guoji/vshop/Wap/index.php/Order/index?status=1" class="mico_fk">
			  <?php if(($dfk_count) > "0"): ?><i class="ico_num"><?php echo ($dfk_count); ?></i><?php endif; ?>待付款
			  </a></li>
              <li><a href="/guoji/vshop/Wap/index.php/Order/index?status=2" class="mico_fh">
			  <?php if(($dfh_count) > "0"): ?><i class="ico_num"><?php echo ($dfh_count); ?></i><?php endif; ?>待发货			  
			  </a></li>
              <li><a href="/guoji/vshop/Wap/index.php/Order/index?status=3" class="mico_sh">
			  <?php if(($dqr_count) > "0"): ?><i class="ico_num"><?php echo ($dqr_count); ?></i><?php endif; ?>待收货				  
			  </a></li>
              <li><a href="/guoji/vshop/Wap/index.php/Order/index?status=4" class="mico_pj">
			  <?php if(($dpj_count) > "0"): ?><i class="ico_num"><?php echo ($dpj_count); ?></i><?php endif; ?>待评价</a></li>
              <li><a href="/guoji/vshop/Wap/index.php/Order/index?status=5" class="mico_tk">
			  <?php if(($dtk_count) > "0"): ?><i class="ico_num"><?php echo ($dtk_count); ?></i><?php endif; ?>退款</a></li>
           </ul>
        </div>
    </div>
    <div class="men_list">
       <ul>
          <li class="ico_qb"><a href="/guoji/vshop/Wap/index.php/Wallet">我的钱包<span>余额、红包等</span></a></li>
          <!--<li class="ico_jm"><a href="#">加盟代理</a></li>-->
          <li class="ico_sy"><a href="/guoji/vshop/Wap/index.php/Wallet/income">我的收益<span>推荐人数、会员分享收益等</span></a></li>
       </ul>
    </div>
    <div class="men_list">
       <ul>
          <li class="ico_ewm"><a href="/guoji/vshop/Wap/index.php/Member/qrcode">我的二维码<span></span></a></li>
          <li class="ico_sc"><a href="/guoji/vshop/Wap/index.php/Collect">我的收藏<span></span></a></li>
          <li class="ico_sh"><a href="/guoji/vshop/Wap/index.php/Consignee">我的收货地址<span></span></a></li>
          <li class="ico_zh"><a href="/guoji/vshop/Wap/index.php/Member/safety">账户安全<span></span></a></li>
       </ul>
    </div>
    <div class="men_list">
       <ul>
          <li class="ico_kf"><a href="#">联系客服<span></span></a></li>
       </ul>
    </div>
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