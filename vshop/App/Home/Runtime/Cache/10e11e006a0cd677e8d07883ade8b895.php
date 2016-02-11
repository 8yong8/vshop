<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><!-- <meta name="viewport" content="width=320,minimum-scale=0.5, maximum-scale=5, user-scalable=no"> --><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="format-detection" content="telephone=no"><link rel="stylesheet" href="__PUBLIC__/css/common.css" /><title></title></head><style>	.main
	{
		/*padding: 10px;*/

	}
	.title
	{
		padding: 20px;
		border-bottom: 1px solid #ddd;
		overflow: hidden;
	}
	.title h2
	{
		color:#333333;
		font-size: 18px;
		font-weight: 700;
	}
	.title span
	{
		float: right;
		color: #888;
	}
	.title span img
	{
		vertical-align: middle;
		position: relative;
		top: 0px;
	}
	.lz
	{
		padding: 20px;
		padding-top: 10px;


	}
    .fn
	{
		display: block;
		width: 100%;
		overflow: hidden;
		line-height: 25px;
	}
	.fn b
	{
		font-size: 17px;
		font-weight: normal;
		color: #329ae8;
	}
	.fn span
	{
		font-size: 12px;
		color: #999;
		margin-left: 5px;
	}
	.fn span img
	{
		vertical-align: middle;
		position: relative;
		top: -2px;
	}
	.fn span.time
	{
		float: right;
		font-size: 14px;
	}
	.contain
	{
		padding-top: 5px;
		font-size: 15px;
		color: #333;
	}
	.contain img
	{
		width:100%;
	}
	.updown_title
	{
		padding-left: 20px;
		padding-right: 20px;
	}
	.updown
	{
		padding: 20px;
		padding-top: 10px;
		border-bottom: 1px solid #ddd;
	}
	.updown_title
	{
		font-size: 17px;
		font-weight: normal;
		color: #007ac8;
		line-height: 40px;
		line-height: 40px;
		border-bottom: 1px solid #007ac8;
	}
	.updown_title b
	{
		font-weight: normal;
		font-size: 12px;
		color: #999;
		margin-left: 5px;
	}
</style><body><div class="main"><div class="title"><h2><?php echo ($vo["subject"]); ?></h2><span><p><img width=20 src="__PUBLIC__/images/iconfont-geiwoliuyan.png" alt="" /><?php echo ($vo["comment_num"]); ?></p></span></div><div class="lz"><p class="fn"><b><?php echo ($vo["user_name"]); ?></b><span><img src="__PUBLIC__/images/iconfont-unie620.png" width=12 alt="" />楼主</span><span class="time"><?php echo (todate($vo["create_time"],'m-d H:i:s')); ?></span></p><p class="contain"><?php echo ($vo["content"]); ?></p></div><h2 class="updown_title">全部回复<b>(<?php echo ($vo["comment_num"]); ?>)</b></h2><?php if(is_array($vo["data"])): $i = 0; $__LIST__ = $vo["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><div class="updown"><p class="fn"><b><?php echo ($pl["user_name"]); ?></b><span class="time"><?php echo (todate($pl["create_time"],'m-d H:i:s')); ?></span></p><p class="contain"><?php echo ($pl["message"]); ?></p></div><?php endforeach; endif; else: echo "" ;endif; ?></div></body></html>