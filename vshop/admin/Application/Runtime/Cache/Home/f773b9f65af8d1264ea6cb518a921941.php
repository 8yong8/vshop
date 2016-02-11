<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/jquery.js"></script>

<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/guoji/vshop/ADMIN2';
var URL = '/guoji/vshop/ADMIN2/index.php/Order';
var APP	 =	 '/guoji/vshop/ADMIN2/index.php';
var PUBLIC = '/guoji/vshop/ADMIN2/Public';
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
		<img src="/guoji/vshop/ADMIN2/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/guoji/vshop/ADMIN2/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/guoji/vshop/ADMIN2/index.php?m=Cache&a=clear"><img src="/guoji/vshop/ADMIN2/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/guoji/vshop/ADMIN2/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/guoji/vshop/ADMIN2/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/guoji/vshop/ADMIN2/index.php" target="_blank">网站前台</a>|
        <a href="/guoji/vshop/ADMIN2/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/guoji/vshop/ADMIN2/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/guoji/vshop/ADMIN2/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/guoji/vshop/ADMIN2/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/guoji/vshop/ADMIN2/Public/images/ico_8.png" /></div>
	<!--
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
	-->
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/apply/order.js"></script>
<script type="text/javascript">

</script>
<style>
#Validform_msg{display: none}
label{min-width: 90px;display:inline-table}
.list_order span{padding: 0 5px;}
.list_order span a{padding: 0 0px;}
</style>
<div class="content">
    <div class="title">编辑信息[ <A HREF="/guoji/vshop/ADMIN2/index.php/Order">返回列表</A> ]</div>
    <span class="line_white"></span>
    <div class="list_order">
        <div class="handle mt10">
        	<span class="fr"><a href="javascript:" onclick="view_log('<?php echo ($vo['order_sn']); ?>')">查看订单操作日志</a></span>
                <strong>订单操作：</strong>
                <!-- 
                确认订单：[先发货后支付 || [先支付后发货 && 已支付]] && 未确认
                -->
                <a href="javascript:;" <?php if(($vo['payment_mode'] == 2 || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 1)) && $vo['status'] == 0){ ?> onclick="order_update('confirm_order','status',1,<?php echo $vo['id']; ?>,location.reload());"<?php }else{ ?> class="disabled"<?php } ?>>确认订单</a>
                <!-- 
                确认付款：[先发货后支付 && 已发货 && 未支付] || [先支付后发货 && 待支付 && 待发货]
                -->
                <a href="javascript:;"<?php if ((($vo['pay_type'] == 2 && $vo['delivery_status'] == 1 && $vo['pay_status'] == 0) || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 0 && $vo['delivery_status'] == 0)) && $vo['status'] == 0){ ?> onclick="order_update('payment','pay_status',1,<?php echo $vo['id']; ?>,location.reload());"<?php }else{ ?> class="disabled"<?php } ?>>确认付款</a>
                <!-- 
                确认发货：[先发货后支付 || [先支付后发货 + 已支付]] && 已确认 && 待发货 
                -->
                <a href="javascript:;" <?php if (($vo['payment_mode'] == 2 || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 1)) && $vo['status'] == 1 && $vo['delivery_status'] == 0 ){?>  onclick="deliver(<?php echo $vo['id']; ?>);" <?php }else{ ?> class="disabled"<?php } ?>>发货</a>
                <!-- 
                确认完成：[先发货后支付 || 先支付后发货 && 已支付] &&  已确认 &&  已发货
                -->
                <a href="javascript:;"<?php if (($vo['pay_type'] == 1 || ($vo['pay_type'] == 0 && $vo['pay_status'] == 1)) && $vo['status'] == 1 && $vo['delivery_status'] == 1 ): ?> onclick="order_update('receipt','status',2,<?php echo $vo['id']; ?>,location.reload())"<?php else: ?> class="disabled"<?php endif ?>>确认完成</a>

                <a href="javascript:;" onclick="del(<?php echo ($vo["id"]); ?>);">关闭订单</a>

				<strong>退款：</strong><?php echo (rs($vo["is_refund"],'1#有退款#red@0#木有#blue')); ?>
        </div>
        <div class="details clearfix mt10">
            <div class="sub mt15 fr">
                <a id="print_order" href="javascript:;" data-id="<?php echo $vo['id']; ?>" style="">打印订单</a>
				<?php if(($prev) != ""): ?><a href="/guoji/vshop/ADMIN2/index.php/Order/edit/id/<?php echo ($prev["id"]); ?>">上一单</a>
                <?php else: ?>
                    <a>没有了</a><?php endif; ?>
				<?php if(($next) != ""): ?><a href="/guoji/vshop/ADMIN2/index.php/Order/edit/id/<?php echo ($next["id"]); ?>">下一单</a>
                <?php else: ?>
                    <a>没有了</a><?php endif; ?>
                <a href="/guoji/vshop/ADMIN2/index.php/Order/">返回订单列表</a>
            </div>
            <strong>订单<br />详情</strong>
            <span>订单号：<?php echo ($vo["order_sn"]); ?></span>
            <span>订单状态：
                <b>
				    <?php echo (rs($vo["status"],'0#未确认@1#确认订单@2#完成交易#red@-1#订单关闭#red')); ?>
					<?php echo (rs($vo["pay_status"],'0#未支付@1#已支付')); ?>
					<?php echo (rs($vo["delivery_status"],'0#未发货@1#已发货')); ?>
                </b>
            </span>
            <span>订单类型：<img src="/guoji/vshop/ADMIN2/Public/images/ico_d_<?php echo ($vo["source"]); ?>.png" alt="" /></span>
        </div>
		<?php if($vo['pay_status']==1 || $vo['payment_mode']==2){ ?>
        <div class="details clearfix mt10">
            <strong>支付<br />详情</strong>
            <span>
			支付方式：<?php echo (rs($vo["payment_mode"],'1#在线支付#blue@2#货到付款#blue')); ?>
			支付公司：<font color="blue"><?php echo ($vo["payment_company"]); ?></font>
			支付渠道：<font color="blue"><?php echo ($vo["payment_channel"]); ?></font>
			</span>
        </div>
		<?php } ?>
        <div class="detaxx">
            <table>
                <tr>
                    <th>应付订单金额</th>
                    <th>&nbsp;</th>
                    <th>商品总额</th>
                    <th>&nbsp;</th>
                    <th>配送费用</th>
                    <th>&nbsp;</th>
                    <th>发票税额</th>
                    <th>&nbsp;</th>
                    <th>保价费用</th>
                    <th>&nbsp;</th>
                    <th>商品折扣</th>
                    <th>&nbsp;</th>
                    <th>优惠券减免</th>
                    <th>&nbsp;</th>
                    <th>使用积分抵扣</th>
                </tr>
                <tr>
                    <td><font><?php echo ($vo["actual_paid"]); ?></font></td>
                    <td><b>=</b></td>
                    <td><b><?php echo ($vo["total_fee"]); ?></b></td>
                    <td><b>+</b></td>
                    <td><b><?php echo ($vo["shipping_fee"]); ?></b></td>
                    <td><b>+</b></td>
                    <td><b><?php echo ($vo["tax_fee"]); ?></b></td>
                    <td><b>+</b></td>
                    <td><b><?php echo ($vo["insure_fee"]); ?></b></td>
                    <td><b>-</b></td>
                    <td><b><?php echo ($vo["discount_fee"]); ?></b></td>
                    <td><b>-</b></td>
                    <td><b><?php echo ($vo["coupons_fee"]); ?></b></td>
                    <td><b>-</b></td>
                    <td><b><?php echo ($vo["score_fee"]); ?></b></td>
                </tr>
				<?php if($vo['bond']!=0 || $vo['balance_pay']!=0){ ?>
                <tr>
                    <td colspan="16" style="text-align:left;padding-left:65px;">定金/保证金：<font><?php echo ($vo["bond"]); ?></font>&nbsp;&nbsp;&nbsp;余额支付：<font><?php echo ($vo["balance_fee"]); ?></font></td>
                </tr>
				<?php } ?>
            </table>
            <ul>
                <li>
                    <strong>客户订单留言：</strong>
					<?php echo ((isset($vo["memo"]) && ($vo["memo"] !== ""))?($vo["memo"]):'--'); ?>
                </li>
                <li class="none">
                    <span><a href="javascript:" <?php if ((($vo['payment_mode'] == 2 && $vo['delivery_status'] == 1 && $vo['pay_status'] == 0) || ($vo['payment_mode'] == 1 && $vo['pay_status'] == 0 && $vo['delivery_status'] == 0)) && $vo['status'] == 0){ ?> id="editMoney"<?php }else{ ?> onclick="alert('当前订单状态不允许修改价格');"<?php } ?>>编辑费用信息</a></span>
                    <strong>发票信息：</strong>
					抬头：<?php echo ((isset($vo["tax_title"]) && ($vo["tax_title"] !== ""))?($vo["tax_title"]):'--'); ?>
					内容：<?php echo ((isset($vo["tax_content"]) && ($vo["tax_content"] !== ""))?($vo["tax_content"]):'--'); ?>
                </li>
            </ul>
        </div>
		<?php if(($vo["discount_fee"]) > "0"): ?><dl class="blue_table mt10">
            <dt>
            	<strong>减免信息</strong>
            </dt>
            <dd>
                <table>
                    <tr>
                        <th>类型</th>
                        <th>名称</th>
                        <th>信息</th>
                    </tr>
					<?php if(is_array($prom_list)): $i = 0; $__LIST__ = $prom_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$prom): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo (rs($prom["prom_type"],'order#订单优惠')); ?></td>
                        <td><?php echo ($prom["name"]); ?></td>
                        <td><?php echo ($prom["award_type"]); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </dd>
        </dl><?php endif; ?>
		<?php if(($vo["coupons_fee"]) > "0"): ?><dl class="blue_table mt10">
            <dt>
            	<strong>优惠券信息</strong>
            </dt>
            <dd>
                <table>
                    <tr>
                        <th>名称</th>
                        <th>信息</th>
                        <th>优惠</th>
                    </tr>
					<?php if(is_array($coupon_list)): $i = 0; $__LIST__ = $coupon_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$coupon): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo ($coupon["title"]); ?></td>
                        <td><?php echo ($coupon["info"]); ?></td>
                        <td><?php echo ($coupon["award_value"]); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </dd>
        </dl><?php endif; ?>
        <dl class="blue_table mt10">
            <dt>
            	<strong>收货人信息</strong>
            	<!--<span><a href="javascript:" onclick="editaccept()">编辑用户信息</a></span>-->
            </dt>
            <dd>
                <table>
                    <tr>
                        <th>会员号</th>
                        <th>收货人</th>
                        <th>手机号</th>
                        <th>邮编</th>
                        <th>详细地址</th>
                    </tr>
                    <tr>
                        <td><?php echo ($vo["member_name"]); ?></td>
                        <td><?php echo ($vo["recipient"]); ?></td>
                        <td><?php echo ($vo["mobile"]); ?></td>
                        <td><?php echo ($vo["zip_code"]); ?></td>
                        <td><?php echo ($vo["address"]); ?></td>
                    </tr>
                </table>
            </dd>
        </dl>
        <dl class="blue_table mt10">
            <dt>
            	<strong>支付配送方式</strong>   
            </dt>
            <dd>
                <table>
                    <tr>
                        <td>
                        <p>支付方式：<?php echo (rs($vo["payment_mode"],'1#在线支付@2#货到付款')); ?></p>
						</td>
                    </tr>
                    <tr>
                        <td><p>配送方式：<?php echo ($shipping["shipping_company"]); ?>
                            <?php if(($vo["delivery_status"]) == "1"): ?><!--
                                <a href="/guoji/vshop/ADMIN2/index.php/Order_delivery/look/id/<?php echo ($shipping["id"]); ?>" target="_blank" onclick="kuaidi('<?php echo ($shipping["id"]); ?>');">查询订单发货情况</a> 
								-->
                                <a href="javascript:;" onclick="kuaidi('<?php echo ($shipping["id"]); ?>');">查询订单发货情况</a><?php endif; ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </dd>
        </dl>
        <dl class="blue_table mt10">
            <dt>
            	<strong>商品信息</strong>
            	</dt>
            <dd>
                <table>
                    <tr>
                        <!--<th>商品条码</th>-->
                        <th>商品名称</th>
                        <th>商品属性</th>
                        <th>商品单价</th>
                        <th>购买数量</th>
                        <th>商品总价</th>
						<th>状态</th>
                    </tr>
					<?php if(is_array($items)): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo ($item["product_name"]); ?></td>
                        <td><?php echo ($item["spec"]); ?></td>
                        <td><?php echo ($item["price"]); ?></td>
                        <td><?php echo ($item["num"]); ?></td>
                        <td><?php echo (number_format($item['price'] * $item['num'], 2, '.', '')); ?></td>
                        <td id="item_<?php echo ($item["id"]); ?>">
						<?php if($item['status']==1 || $item['status']==2 || $vo['payment_mode']==2){ ?> 
							<?php if($item['refund_status']==0){ ?>
							<strong><?php echo (rs($item["status"],'1#已支付@2#完成交易')); ?></strong>
							<a href="javascript:;" onclick="order_update('apply','is_refund',1,<?php echo ($item["id"]); ?>);"><font class="red">申请退款</font></a>
							<?php }else if($item['refund_status']==1 && $vo['delivery_status']==0){ ?>
							<strong>申请退款</strong>
							<a href="javascript:;" onclick="order_update('nd_agree','refund_status',4,<?php echo ($item["id"]); ?>);"><font class="red">同意退款(未发货)</font></a>
							<?php }else if($item['refund_status']==1 && $vo['delivery_status']>0){ ?>
							<strong>申请退款</strong>
							<a href="javascript:;" onclick="order_update('yd_agree','refund_status',2,<?php echo ($item["id"]); ?>,location.reload());"><font class="red">同意退款(已发货)</font></a>
							<a href="javascript:;" onclick="order_update('refuse','is_refund',1,<?php echo ($item["id"]); ?>,location.reload());"><font color="blue">拒绝退款</font></a>
							<?php }else if($item['refund_status']==2){ ?>
							<strong>等待退货</strong>
							<?php }else if($item['refund_status']==3){ ?>
							<strong>退货中，等待确认</strong>
							<a href="javascript:;" onclick="kuaidi('<?php echo ($item["delivery_id"]); ?>');">查看物流</a>
							<a href="javascript:;" onclick="order_update('return_goods','refund_status',4,<?php echo ($item["id"]); ?>);"><font class="red">确认退货</font></a>
							<?php }else if($item['refund_status']==4){ ?>
							<strong>退货完成，等待退款</strong>
							<a href="javascript:;" onclick="return_refund(<?php echo ($item["id"]); ?>,<?php echo $item['num']*$item['price']; ?>);"><font class="red">确认退款</font></a>
							<?php }else if($item['refund_status']==5){ ?>
							<strong><font color="blue">完成退款</font></strong>
							<?php } ?>
						<?php } ?>
						<strong><?php echo (rs($item["status"],'-1#关闭')); ?></strong>
						</td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </dd>
        </dl>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
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
			$(this).children().attr('src','/guoji/vshop/ADMIN2/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/guoji/vshop/ADMIN2/Public/images/ico_8.png');
		}
	  );
</script> 
    </div>
</div>

<!--编辑费用信息弹窗-->
<div id="editMoneybox" class="editMoneybox">

	<ul>
		<li class="w85">应付订单金额</li>
		<li class="w85"></li>
		<li class="w85">折扣率(%)</li>
		<li class="w85"></li>
		<li class="w155">实付订单金额</li>
	</ul>
	<ul>
		<li class="w85"><span class="red3"><?php echo ($vo["actual_paid"]); ?></span></li>
		<li class="w85"> X </li>
		<li><input type="text" name="discount"/></li>
		<li class="w85"> = </li>
		<li><input type="text" name="actual_paid"/></li>
	</ul>

	<p>小提示：您可以直接调整订单的最终付款价格，输入实付订单金额货折扣率均可</p>
</div>
<script type="text/javascript">
var real_amount = '<?php echo ($vo["actual_paid"]); ?>';
$("#editMoney").click(function() {
	art.dialog({
		padding: '0px ',
		id: 'editMoneybox',
		background: '#ddd',
		opacity: 0.3,
		title: '编辑费用信息',
		content: document.getElementById('editMoneybox'),
		ok:function() {
			var actual_paid = $("input[name=actual_paid]").val();
			//alert(actual_paid);return;
			$.post('/guoji/vshop/ADMIN2/index.php?m=Order&a=edit_price', {
				order_id: '<?php echo ($vo["order_id"]); ?>',
				oldPrice:'<?php echo ($vo["actual_paid"]); ?>',
				actual_paid:actual_paid
			}, function(ret) {
			    //alert(ret);
				if(ret.error_code == 0) {
					window.location.reload();
					return true;
				} else {
					alert(ret.notice);
					return false;
				}
			}, 'JSON');
			return false;
		},
		cancel:true
	});
});

//监控价格修改
$(document).ready(function(){
	$('input[name=discount]').on('keypress keyup blur', function(){
		var discount = $(this).val();
		alert(discount);
		var money = (real_amount * discount / 100).toFixed(2);
		$("input[name=actual_paid]").attr('value', money);
	})
	$('input[name=actual_paid]').on('keypress keyup blur', function(){
		var money = $(this).val();
		var discount = ((money / real_amount) * 100).toFixed(2);
		$("input[name=discount]").attr('value', discount);
	})
});

//打印
$('#print_order').bind('click',function(){
    var order_id = $(this).attr('data-id');
    if (order_id < 1) alert('您的订单号有误！');
    location.href = "<?php echo U('Order/print_order') ?>?" + '&order_id=' + order_id;
})

//物流跟踪 
function kuaidi(id) {
	$.get(APP+'?m=Order_delivery&a=look&ajax=1', {
		id:id
	}, function(_html) {
	
		art.dialog({
			id:'kuaidi',
			title:'物流详情&nbsp;',
			fixed:true,
			lock:true,
			content:_html,
			ok:true
		});
	});
}

//确认退款
function return_refund(id,amount) {
	var _html = '<FORM METHOD=POST id="form1" action="/guoji/vshop/ADMIN2/index.php/Order/add?<?php echo time(); ?>"><TABLE class="add"><TR><TD class="tRight" >支付方式：</TD><TD class="tLeft" ><SELECT NAME="payment_mode"><OPTION VALUE="在线支付" <?php if(($vo["payment_mode"]) == "在线支付"): ?>SELECTED<?php endif; ?>>在线支付</OPTION><OPTION VALUE="线下支付" <?php if(($vo["payment_mode"]) == "线下支付"): ?>SELECTED<?php endif; ?>>线下支付</OPTION></SELECT></TD></TR><TR><TD class="tRight" >支付单位：</TD><TD class="tLeft" ><SELECT NAME="payment_company"><OPTION VALUE="支付宝" <?php if(($vo["payment_company"]) == "支付宝"): ?>SELECTED<?php endif; ?>>支付宝支付</OPTION><OPTION VALUE="微信支付" <?php if(($vo["payment_company"]) == "微信支付"): ?>SELECTED<?php endif; ?>>微信支付</OPTION><OPTION VALUE="余额支付" <?php if(($vo["payment_company"]) == "余额支付"): ?>SELECTED<?php endif; ?>>余额支付</OPTION></SELECT></TD></TR><TR><TD class="tRight" >支付交易号：</TD><TD class="tLeft" ><INPUT TYPE="text" NAME="pay_order_sn"> 如有`交易号`请使用交易号,查账用.</TD></TR><TR><TD class="tRight" >交易金额：</TD><TD class="tLeft" ><INPUT TYPE="text" NAME="amount" value="'+amount+'" readonly="readonly"></TD></TR><TR><TD class="tRight" >备注：</TD><TD class="tLeft" ><TEXTAREA NAME="memo" ROWS="10" COLS="30"></TEXTAREA></TD></TR><INPUT TYPE="hidden" NAME="id" value="'+id+'"></TABLE></FORM>'; 
	art.dialog({
		id:'kuaidi',
		title:'退款确认&nbsp;',
		fixed:true,
		lock:true,
		content:_html,
		ok:function(){
		  var status = sendForm('form1','/guoji/vshop/ADMIN2/index.php/Order/return_refund');
		  //alert(status);
		  dialog_close();
		  if(status==true){
		    $('#item_'+id).html('<strong><font color="blue">完成退款</font></strong>');
		  }
		  //return false;
		}
	});
}
</script>