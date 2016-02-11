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
var URL = '/guoji/vshop/Wap/index.php/Product';
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
var cat_id;//1级分类
var cat_id2;//2级分类
var cat_id3;//3级分类
var brand_id = '';//品牌
var size = '';//尺码
</script>
<script type="text/javascript" src="/guoji/vshop/Wap/Public/js/apply/product.js"></script>
<div id="light" class="white_content"> 
    <div class="shaixuanlist_tt">
       <div class="nav">高级筛选</div>
       <a href="javascript:void(0)" onclick="$('#light').hide();$('#fade').hide();" class="close"></a>
    </div>
    <div class="sx_list">
		<?php if(($kw) != ""): ?><div class="sx_list_tj cate_list">
           <div class="sxtj_left">关键词：</div>
           <div class="sxtj_right">
              <ul id="cate_id">
				 <li>
				 <a href="<?php echo (resetUrl($self_url,'kw')); ?>" class="cur"  ><?php echo ($kw); ?></a>
				 </li>
              </ul>
           </div>
        </div><?php endif; ?>
		<!--
        <div class="sx_list_tj" id="area_div">
           <div class="sxtj_left">区域：</div>
           <div class="sxtj_right2">
              <ul id="area">
                 <li><a href="javascript:;" onclick="set_val('area',0);" id="area_0" value="0" <?php if(($area) == "0"): ?>class="cur"<?php endif; ?>>免费区</a></li>
                 <li><a href="javascript:;" onclick="set_val('area',1);" id="area_1" value="1" <?php if(($area) == "1"): ?>class="cur"<?php endif; ?>>消费区</a></li>
              </ul>
           </div>
        </div>
		-->
		<?php if(($catList) != ""): ?><div class="sx_list_tj cate_list">
           <div class="sxtj_left">分类：</div>
           <div class="sxtj_right2">
              <ul id="cat_id">
				 <?php if(is_array($catList)): $i = 0; $__LIST__ = $catList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cat): $mod = ($i % 2 );++$i;?><li><a href="javascript:;" onclick="set_val('cat_id',<?php echo ($cat["id"]); ?>);" id="cat_id_<?php echo ($cat["id"]); ?>" <?php if(($cat_id) == $cat["id"]): ?>class="cur"<?php endif; ?>  value="<?php echo ($cat['id']); ?>"><?php echo ($cat["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
           </div>
        </div><?php endif; ?>
		<?php if(($catList2) != ""): ?><div class="sx_list_tj cate_list">
           <div class="sxtj_left">分类：</div>
           <div class="sxtj_right2">
              <ul id="cat_id2">
			     <?php if(is_array($catList2)): $i = 0; $__LIST__ = $catList2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cat): $mod = ($i % 2 );++$i;?><li><a href="javascript:;" onclick="set_val('cat_id2',<?php echo ($cat["id"]); ?>);" id="cat_id2_<?php echo ($cat["id"]); ?>" <?php if(($cat_id2) == $cat["id"]): ?>class="cur"<?php endif; ?>  value="<?php echo ($cat['id']); ?>"><?php echo ($cat["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
           </div>
        </div><?php endif; ?>
		<?php if(($catList3) != ""): ?><div class="sx_list_tj cate_list">
           <div class="sxtj_left">分类：</div>
           <div class="sxtj_right2">
              <ul id="cat_id3">
			     <?php if(is_array($catList3)): $i = 0; $__LIST__ = $catList3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cat): $mod = ($i % 2 );++$i;?><li><a href="javascript:;" onclick="set_val('cat_id3',<?php echo ($cat["id"]); ?>);" id="cat_id3_<?php echo ($cat["id"]); ?>" <?php if(($cat_id3) == $cat["id"]): ?>class="cur"<?php endif; ?>  value="<?php echo ($cat['id']); ?>"><?php echo ($cat["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
           </div>
        </div><?php endif; ?>
        <div class="sx_list_tj">
           <div class="sxtj_left">品牌：</div>
           <div class="sxtj_right2">
              <ul id="brand_id">
				 <?php if(is_array($brands)): $i = 0; $__LIST__ = $brands;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): $mod = ($i % 2 );++$i;?><li><a href="javascript:;" onclick="set_val('brand_id',<?php echo ($brand["id"]); ?>);" id="brand_id_<?php echo ($brand["id"]); ?>" {if $brand_id==$brand['id']}class="cur"{/if} value="<?php echo ($brand['id']); ?>"><?php echo ($brand["brand_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
           </div>
        </div>
		<!--
        <div class="sx_list_tj">
           <div class="sxtj_left">尺码：</div>
           <div class="sxtj_right">
              <ul id="size">
			     {foreach $sizes as $lsize}
                 <li><a href="javascript:;" onclick="set_val('size',<?php echo ($lsize); ?>);" id="size_<?php echo ($lsize); ?>" {if $size==$lsize}class="cur"{/if}><?php echo ($lsize); ?></a></li>
				 {/foreach}
              </ul>
           </div>
        </div>
		-->
		<?php if( $area || $brand_id || $cate_id || $kw){ ?>
        <div class="sx_list_tj">
           <div class="sxtj_left">搜索条件：</div>
           <div class="sxtj_right">
              <ul>
				 <li><a href="/guoji/vshop/Wap/index.php/Product/list">取消搜索条件</a></li>
              </ul>
           </div>
        </div>
		<?php } ?>
        <div class="qd_btn"><input type="submit" value="确 定" class="ok" onclick="$('#light').hide();$('#fade').hide();search();" class="close"></div>
    </div>
</div> 
<div id="fade" class="black_overlay"> 
</div> 
<div class="menu">
   <a class="back" href="javascript:;" onclick="history.go(-1)"><img src="/guoji/vshop/Wap/Public/images/menu_back.png"></a>
   <div class="tit">商品列表</div>
   <a class="goods_sx" href="javascript:;" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'"><img src="/guoji/vshop/Wap/Public/images/menu_sx.png"></a>
</div>
<div class="shaixuan">
   <ul>
      <li <?php if(($order) == "weight"): ?>class="cur"<?php endif; ?>>
	  <span>
	  <a href="<?php echo (resetUrl($self_url,'order,sort,p')); ?>&order=weight<?php if(($order) == "weight"): if(($sort) == "desc"): ?>&sort=asc<?php else: ?>&sort=desc<?php endif; endif; ?>" <?php if(($order) == "weight"): if(($sort) == "asc"): ?>class="down"<?php else: ?>class="up"<?php endif; endif; ?> >推荐</a>
	  </span>
	  </li>
      <li <?php if(($order) == "price"): ?>class="cur"<?php endif; ?>>
	  <span><a href="<?php echo (resetUrl($self_url,'order,sort,p')); ?>&order=price<?php if(($order) == "price"): if(($sort) == "desc"): ?>&sort=asc<?php else: ?>&sort=desc<?php endif; endif; ?>" <?php if(($order) == "price"): if(($sort) == "asc"): ?>class="down"<?php else: ?>class="up"<?php endif; endif; ?> >价格</a></span>
	  </li>
      <li <?php if(($order) == "sale_num"): ?>class="cur"<?php endif; ?>>
	  <span><a href="<?php echo (resetUrl($self_url,'order,sort,p')); ?>&order=sells<?php if(($order) == "sale_num"): if(($sort) == "desc"): ?>&sort=asc<?php else: ?>&sort=desc<?php endif; endif; ?>" <?php if(($order) == "sale_num"): if(($sort) == "asc"): ?>class="down"<?php else: ?>class="up"<?php endif; endif; ?> >销量</a></span>
	  </li>
      <li <?php if(($order) == "new"): ?>class="cur"<?php endif; ?>>
	  <span><a href="<?php echo (resetUrl($self_url,'order,sort,p')); ?>&order=new<?php if(($order) == "create_time"): if(($sort) == "desc"): ?>&sort=asc<?php else: ?>&sort=desc<?php endif; endif; ?>" <?php if(($order) == "create_time"): if(($sort) == "asc"): ?>class="down"<?php else: ?>class="up"<?php endif; endif; ?> >最新</a></span>
	  </li>
   </ul>
</div>
<div class="goodslist">
    <ul>
	   <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product): $mod = ($i % 2 );++$i;?><li><span><a href="/guoji/vshop/Wap/index.php/Product/detail?id=<?php echo ($product["id"]); ?>"><img src="<?php echo ($product["lit_pic"]); ?>" width="100%"><p><?php echo ($product["name"]); ?></p><p class="price"><?php echo ($product["price"]); ?><em>￥<?php echo ($product["market_price"]); ?></em></p></a></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>
<div class="clear"></div>
<?php if(($page_count) != ""): ?><div class="page">
    <ul>
       <li><a href="<?php echo (resetUrl($self_url,'p')); ?>&p=<?php echo ($prev_page); ?>" class="up">上一页</a></li>
       <li><a href="javascript:;" class="all"><?php echo ($pageno); ?>/<?php echo ($page_count); ?></a></li>
       <li><a href="<?php echo (resetUrl($self_url,'p')); ?>&p=<?php echo ($next_page); ?>" class="next">下一页</a></li>
    </ul>
</div><?php endif; ?>
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