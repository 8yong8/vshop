<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/css/style.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/ADMIN2/Public/js/artDialog/skins/blue.css" />
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/ADMIN2/Public/js/artDialog/plugins/iframeTools.js"></script>
<title>发货</title>
<style>
.content2 {
  padding: 0 18px;
  height: 100%;
}
</style>
<meta http-equiv="MSThemeCompatible" content="Yes" />

</head>
<body>
<div class="content2" style="height:480px;">
<div class="title">编辑物流信息</div>
<FORM METHOD=POST id="form1" action="/guoji/vshop/ADMIN2/index.php/Order/deliver?<?php echo time(); ?>">
<TABLE cellpadding=3 cellspacing=3 class="add">
<TR>
	<TD colspan="2"><div id="result" class="result none"></div></TD>
</TR>

<TR>
	<TD class="tRight">
		&nbsp;购买者：</TD>
	<TD  style="width:450px;">
		<?php echo ($vo["member_name"]); ?> | <?php echo ($vo["realname"]); ?>
	</TD>
</TR>

<TR>
	<TD class="tRight" >支付方式：</TD>
	<TD class="tLeft" >
	<?php echo (rs($vo["payment_mode"],'1#在线支付@2#线下支付/到付')); ?>
	</TD>
</TR>

<TR>
	<TD class="tRight" >订单号：</TD>
	<TD class="tLeft" >
	<a href="/guoji/vshop/ADMIN2/index.php/Order/index/order_id/<?php echo ($vo["order_id"]); ?>" target="_blank" style="color:red"><?php echo ($vo["order_sn"]); ?></a>
	</TD>
</TR>

<TR>
	<TD class="tRight" >交易类型：</TD>
	<TD class="tLeft" >
	<?php echo (rs($vo["type"],'1#商品@2#1元云购@3#兑换商品@4#拍卖@5#众筹')); ?>
	</TD>
</TR>

<TR>
	<TD class="tRight" >交易金额：</TD>
	<TD class="tLeft" >
	<?php echo ($vo["total_fee"]); ?>
	</TD>
</TR>

<TR>
	<TD class="tRight" >物流公司：</TD>
	<TD class="tLeft" >
	<select name="shipping_id">
		<?php if(is_array($shippings)): $i = 0; $__LIST__ = $shippings;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ship): $mod = ($i % 2 );++$i;?><option value="<?php echo ($ship["id"]); ?>" <?php if(($shipping["shipping_id"]) == $ship["id"]): ?>selected<?php endif; ?> ><?php echo ($ship["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</select>
	</TD>
</TR>

<TR>
	<TD class="tRight" >物流单号：</TD>
	<TD class="tLeft" >
	<input type="text" name="shipping_no" id="shipping_no" value="<?php echo ($shipping["shipping_no"]); ?>">
	</TD>
</TR>


<TR>
	<TD class="tRight" >备注：</TD>
	<TD class="tLeft" >
	<TEXTAREA NAME="memo" ROWS="5" COLS="20"><?php echo ($shipping["memo"]); ?></TEXTAREA>
	</TD>
</TR>

<TR>
	<TD></TD>
	<TD>
	<INPUT TYPE="hidden" NAME="id" value="<?php echo ($vo["id"]); ?>">
	<INPUT TYPE="hidden" NAME="delivery_status" value="1">
	<INPUT TYPE="submit" value="保 存" class="button">
	</TD>
</TR>
</TABLE>
</FORM>
</div>
<!--
<script>
// 返回数据到主页面

function returnHomepage(){

	var origin = artDialog.open.origin;

	var dom = origin.document.getElementById('td_<?php echo ($_GET['id']); ?>');

	dom.innerHTML = '<input type="button" value="确认收货"  onclick=\'order_update("receipt","shipping_status",2,<?php echo $vo['id']; ?>);\' class="button">';
	setTimeout("art.dialog.close()", 1000 );
}
<?php if(($_GET['suc']) == "1"): ?>returnHomepage();<?php endif; ?>
</script>
-->