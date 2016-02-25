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
var URL = '/git/vshop/Wap/index.php/product';
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

<link type="text/css" href="/git/vshop/Wap/Public/css/imgbox.css" rel="stylesheet"/>

<script type="text/javascript" src="/git/vshop/Wap/Public/js/jquery.event.drag-1.5.min.js"></script>
<script type="text/javascript" src="/git/vshop/Wap/Public/js/jquery.touchSlider.js"></script>


<script type="text/javascript">

$(document).ready(function(){

	$(".main_visual").hover(function(){
		$("#btn_prev,#btn_next").fadeIn()
	},function(){
		$("#btn_prev,#btn_next").fadeOut()
	});
	
	$dragBln = false;
	
	$(".main_image").touchSlider({
		flexible : true,
		speed : 200,
		btn_prev : $("#btn_prev"),
		btn_next : $("#btn_next"),
		paging : $(".flicking_con a"),
		counter : function (e){
			$(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
		}
	});
	
	$(".main_image").bind("mousedown", function() {
		$dragBln = false;
	});
	
	$(".main_image").bind("dragstart", function() {
		$dragBln = true;
	});
	
	$(".main_image a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	
	timer = setInterval(function(){
		$("#btn_next").click();
	}, 5000);
	
	$(".main_visual").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		},5000);
	});
	
	$(".main_image").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		}, 5000);
	});
	
});

</script>

<script type="text/javascript">

var item_id;//产品id
var type = 1;//1购物车下单 2立即购买 默认购物车
var stock = <?php echo ($vo["stock"]); ?>;//库存
var product_id = '<?php echo ($vo["id"]); ?>';
<?php if(($data) != ""): ?>var startTime = new Date().getTime();
var keys = eval('(<?php echo ($keys); ?>)');				//所有产品属性
var data = eval('(<?php echo ($data); ?>)');				//所有产品
<?php else: ?>
function sent(){
	if(type==1){
		$.ajax({
			cache: true,
			dataType:'json',
			type: "POST",
			url:'/git/vshop/Wap/index.php/Cart/add',
			data:'product_id=<?php echo ($vo["id"]); ?>&num='+$('#shu').val(),
			async: false,
			error: function(request) {
				showMessage('系统繁忙',1500);
				/*
				art.dialog({
					time: 1.5,
					content: '系统繁忙'
				});
				*/
			},
			success: function(data) {
				//alert(data);return;
				if(data.error_code==0){
					showMessage('添加购物车成功',1500);
					setTimeout(function(){
						document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none';			
					},1500);

					return;
				}else{
					showMessage(data.notice,1500);
				}
			}
		});	
	}else{
	  window.location.href = ROOT+'/Order/confirm?product_id=<?php echo ($vo["id"]); ?>&num='+$('#shu').val();
	}
}<?php endif; ?>
</script>

<?php if(($data) != ""): ?><script type="text/javascript" src="/git/vshop/Wap/Public/js/apply/sku.js"></script><?php endif; ?>
<script type="text/javascript" src="/git/vshop/Wap/Public/js/apply/product.js"></script>

</head>

<body>

<div class="viewport">
<div class="buy">
	<?php if(($favorite) != ""): ?><a class="sc" id="favorite" href="javascript:;" onclick="favorite(2,'Product',<?php echo ($favorite["id"]); ?>)"></a>
	<?php else: ?>
	<a class="ysc" id="favorite" href="javascript:;" onclick="favorite(1,'Product',<?php echo ($vo["id"]); ?>)"></a><?php endif; ?>
    <input type="button" value="立即购买" class="gbtn" onclick="set_type(2);">
    <input type="button" value="加入购物车" class="gwc" onclick="set_type(1);">
</div>
<div id="light" class="white_info">
    <div class="close2"><a href="javascript:void(0);" onClick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'"></a></div> 
    <div class="g_info">
       <div class="gimg"><img src="<?php echo ($vo["lit_pic"]); ?>"></div>
	   <div class="ginfo">
           <p>￥<span id="price"><?php echo ($vo["price"]); ?></span></p>
           <span id="stock">库存<?php echo ($vo["stock"]); ?>件</span><br>
		   <?php if(($attrs) != ""): ?><span id="spec">请选择<?php if(is_array($attrs)): $i = 0; $__LIST__ = $attrs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?>&nbsp;&nbsp;<?php echo ($attr["atrr_name"]); endforeach; endif; else: echo "" ;endif; ?>
		   </span><?php endif; ?>
       </div>
    </div>
    <div class="g_ch">
	   <?php if(is_array($attrs)): $k = 0; $__LIST__ = $attrs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($k % 2 );++$k;?><div class="g_size">
          <div class="g_ch_tt"><?php echo ($attr["atrr_name"]); ?></div>
          <ul>
			 <?php if(is_array($attr["val"])): $i = 0; $__LIST__ = $attr["val"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr_val): $mod = ($i % 2 );++$i;?><li><input type="button" class="sku" attr_id="<?php echo ($attr_val["id"]); ?>" value="<?php echo ($attr_val["val"]); ?>" attr_name="<?php echo ($attr["atrr_name"]); ?>" /></li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
       </div><?php endforeach; endif; else: echo "" ;endif; ?>
       <div class="g_num">
            <div class="num_left">数 量</div>
            <div class="num_right">
                 <input type="button" class="jj" value="-" onclick="update_num(1);">
                 <input type="text" class="shu" id="shu" value="1"  onkeyup="value=value.replace(/[^\d]/g,'') " onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" >
                 <input type="button" class="jj" value="+" onclick="update_num(2);">
            </div>
       </div>
    </div>
    <div class="g_sure"><input type="button" value="确 定" class="ok" onclick="sent();return;document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'" class="close"></div>
</div>
<div id="fade" class="black_over"></div>  
<div class="top">
    <div class="logo"></div>
    <div class="top_menu">
       <ul>
          <li><a href="#"></a></li>
          <li><a href="#"></a></li>
          <li><a href="#"></a></li>
          <li><a href="#"></a></li>
       </ul>
    </div>
</div>
<div class="menu2">
   <a class="back2" href="/git/vshop/Wap/index.php/Product/lists"><img src="/git/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">商品详情</div>
</div>
<!--图片展示开始-->
<div class="goods_img">
     <div class="main_visual">
	<div class="flicking_con">
		<?php if(is_array($vo["imgs"])): $k = 0; $__LIST__ = $vo["imgs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($k % 2 );++$k;?><a href="#"><?php echo ($i+1); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
	<div class="main_image">
		<ul>
			<?php if(is_array($vo["imgs"])): $k = 0; $__LIST__ = $vo["imgs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($k % 2 );++$k;?><li><img src="<?php echo (get_thumb($img["url"],'200')); ?>"></li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>
<div class="clear"></div>
</div>
<!--图片展示开始结束-->
<div class="goods_info">
   <div class="goods_name"><?php echo ($vo["name"]); ?></div>
   <div class="goods_price">￥<?php echo ($vo["price"]); ?><em>￥<?php echo ($vo["market_price"]); ?></em></div>
   <div class="huodong">
      <?php if(($pm) != ""): ?><div class="hd_st1"><?php echo ($pm["info"]); ?></div><?php endif; ?>
      <?php if(($vo["is_free_shipping"]) == "1"): ?><div class="hd_st2">本商品是包邮商品</div><?php endif; ?>
   </div>
   <div class="promise">
      <ul>
         <li><span class="a">100%正品</span></li>
         <li><span class="b">厂家直供</span></li>
         <li><span class="c">7天包退换</span></li>
      </ul>
   </div>
   <div class="others">
      <ul>
         <li><a class="tv" href="/git/vshop/Wap/index.php/Product/imageText?id=<?php echo ($vo["id"]); ?>">图文详情</a></li>
         <li><a class="pj" href="/git/vshop/Wap/index.php/Feedback/index?product_id=<?php echo ($vo["id"]); ?>">商品评价</a></li>
      </ul>
   </div>
</div>
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