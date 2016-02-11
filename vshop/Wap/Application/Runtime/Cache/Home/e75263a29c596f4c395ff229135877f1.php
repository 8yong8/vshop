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
var URL = '/guoji/vshop/Wap/index.php/Order';
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
<script>
var type = '<?php if(($cart_ids) != ""): ?>1<?php else: ?>2<?php endif; ?>';
var cart_ids = '<?php echo ($cart_ids); ?>';
var product_id = '<?php echo ($product_id); ?>';
var item_id = '<?php echo ($item_id); ?>';
var item_num = '<?php echo ($item_num); ?>';
var total_fee = '<?php echo ($vo["total_fee"]); ?>';//订单总价
var consignee_id = '<?php echo ($vo["consignee"]["id"]); ?>';
var sp_id = "<?php if(($promotions) != ""): echo ($promotions[0]['id']); else: ?>0<?php endif; ?>";
var sp_price = "<?php if(($promotions) != ""): echo ($promotions[0]['award_value']); else: ?>0<?php endif; ?>";//促销优惠
var coupon_price = "0";//红包优惠
var shipping_price = "<?php echo ($shipping_fee); ?>";//邮费
var tax = 0;//税 0不要 1:要
var tax_rate = '<?php echo ($tax_rate); ?>';//率
</script>
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/apply/order.js"></script>
<div class="menu">
   <a class="back" href="/guoji/vshop/Wap/index.php/Cart/lists"><img src="/guoji/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">订单详情</div>
</div>
<div class="order">
	<?php if(($vo["consignee"]) != ""): ?><div class="adress">
        <a href="/guoji/vshop/Wap/index.php/Consignee/?id=<?php echo ($vo["consignee"]["id"]); ?>&from_url=<?php echo (urlencode($self_url)); ?>"><p class="s_name" value="<?php echo ($vo["consignee"]["id"]); ?>"><em>收货人：<?php echo ($vo["consignee"]["name"]); ?></em><?php echo ($vo["consignee"]["mobile"]); ?></p><p class="s_adress"><span>收货地址：</span><i><?php echo ($vo["consignee"]["province"]); ?> <?php echo ($vo["consignee"]["city"]); ?> <?php echo ($vo["consignee"]["district"]); ?> <?php echo ($vo["consignee"]["addr"]); ?></i></p></a>
    </div>
	<?php else: ?>
    <div class="adress_no"><a href="/guoji/vshop/Wap/index.php/Consignee/add?&from_url=<?php echo (urlencode($self_url)); ?>">你还未添加任何地址，请先添加地址</a></div><?php endif; ?>
    <div class="o_list">
	  <?php if(is_array($vo["data"])): $i = 0; $__LIST__ = $vo["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div class="o_box">
           <div class="o_price">￥<?php echo ($item["price"]); ?></div>
           <div class="o_img"><a href="/guoji/vshop/Wap/index.php/Product/detail?id=<?php echo ($item["product_id"]); ?>" target="_blank"><img src="<?php echo ($item["lit_pic"]); ?>"></a></div>
           <div class="o_name"><p><?php echo ($item["name"]); ?></p>数量：<?php echo ($item["num"]); ?></div>
       </div><?php endforeach; endif; else: echo "" ;endif; ?>
       <div class="o_aa">共<?php echo (count($vo["data"])); ?>件商品，合计：<em>￥<?php echo ($vo["total_fee"]); ?></em></div>
    </div>
    <div class="o_fp">
       <div class="fp_or"><input type="radio" class="radio" id="tax" onclick="fp();">发票</div>
       <div class="fp_info" id="tax_msg" style="display:none">
           <div class="o_left">发票信息</div>
           <div class="o_right">
              <ul>
                 <li><input type="text" value="" placeholder="发票抬头：张三" class="o_txt" name="tax_title" id="tax_title"></li>
                 <li><input type="text" value="" placeholder="发票内容" class="o_txt" name="tax_content" id="tax_content"></li>
              </ul>
           </div>
        </div>
    </div>
    <div class="o_bz">
      <div class="bz_info">
           <div class="o_left">备注</div>
           <div class="o_bzr"><input type="text" value="" placeholder="给卖家留言" class="o_txt" name="memo" id="memo"></div>
      </div>
    </div>
	<?php if(($coupons) != ""): ?><div class="o_hb">
         <div class="hb_list" id="coupon">
            <ul>
			   <?php if(is_array($coupons)): $i = 0; $__LIST__ = $coupons;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$coupon): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0)" onClick="hb(<?php echo ($coupon["id"]); ?>,<?php echo ($coupon["value"]); ?>);" ><?php echo ($coupon["info"]); ?>；<?php echo (toDate($coupon["deadline"],'Y-m-d H#i#s')); ?> 前可使用 ：<?php echo ($coupon["value"]); ?>元</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
               <li><a href="javascript:void(0)" onClick="hb(0,0);" >不使用红包</a></li>
            </ul>
         </div>
         <a href="javascript:void(0)" onClick="$('#coupon').toggle();" class="hb"><span id="coupon_user_id" value="0"></span></a>使用红包
    </div><?php endif; ?>
	<?php if(($promotions) != ""): ?><div class="o_hb">
         <div class="hb_list" id="prom">
            <ul>
			   <?php if(is_array($promotions)): $i = 0; $__LIST__ = $promotions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$promotion): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0)" onClick="cuxiao(<?php echo ($promotion["id"]); ?>,<?php echo ($promotion["award_value"]); ?>,'<?php echo ($promotion["info"]); ?>');" ><?php echo ($promotion["info"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
         </div>
         <a href="javascript:void(0)" onClick="$('#prom').toggle();" class="hb"><span id="sp_id" value="<?php echo ($promotions[0]['id']); ?>" title="<?php echo ($promotions[0]['info']); ?>"><?php echo ($promotions[0]['info']); ?></span></a>促销活动
    </div><?php endif; ?>

    <div class="o_hb">
         <a href="javascript:void(0)" class="hb"><span><?php echo ($shipping_fee); ?>元</span></a>邮费
    </div>

    <div class="o_qr">实付：<span id="total_fee"><?php echo ($total_fee); ?></span> 元   <input type="button" value="确认" class="o_ok" onclick="sent();"></div>
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