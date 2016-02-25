<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/git/vshop/admin/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/git/vshop/admin/Public/js/jquery.js"></script>

<script type="text/javascript" src="/git/vshop/admin/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/git/vshop/admin/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/git/vshop/admin/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/git/vshop/admin';
var URL = '/git/vshop/admin/index.php/Position';
var APP	 =	 '/git/vshop/admin/index.php';
var PUBLIC = '/git/vshop/admin/Public';
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
		<img src="/git/vshop/admin/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/git/vshop/admin/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/git/vshop/admin/index.php?m=Cache&a=clear"><img src="/git/vshop/admin/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/git/vshop/admin/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/git/vshop/admin/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/git/vshop/admin/index.php" target="_blank">网站前台</a>|
        <a href="/git/vshop/admin/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/git/vshop/admin/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/git/vshop/admin/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/git/vshop/admin/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/git/vshop/admin/Public/images/ico_8.png" /></div>
	<!--
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
	-->
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<script type="text/javascript" src="/git/vshop/admin/Public/js/apply/position.js"></script>
<link rel="stylesheet" type="text/css" href="/git/vshop/admin/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<!-- 内容区 -->
<div class="content">
	<div class="site">
		<?php echo C('site_name');?> <?php echo ($board_title); ?> > <a href="/git/vshop/admin/index.php/Position"><?php echo ($node_title); ?></a>
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
	<div class="guanli">
	  <FORM METHOD=get ACTION="/git/vshop/admin/index.php/Position">
		<TABLE cellpadding="1" cellspacing="2" width="100%">
		<TR>
			<TD class="tRight">标题：</TD>
			<TD><INPUT TYPE="text" NAME="title" class="medium" value="<?php echo ($title); ?>"></TD>
			<TD>
			<INPUT TYPE="hidden" NAME="pid" value="<?php echo ($_GET['pid']); ?>">
			<input type="submit" value="提交" class="button_search">
			</TD>
		</TR>
		</TABLE>
	  </FORM>
	</div>
	<dl class="mt10">
		<dd>
			<div class="login mt10" style="border: none;">
				<div class="handle mt10" style="margin-bottom:10px;">
        		<span class="fr"><a href="/git/vshop/admin/index.php/Position/index/pid/<?php echo ($parent["pid"]); ?>">返回</a></span>
				<strong>推荐位：<font color="red"><?php echo ($parent["title"]); ?></font></strong>
				<?php echo ($parent["info"]); ?>
				</div>
				<div class="panel datagrid easyui-fluid" style="width: 100%;">
				<div class="datagrid-wrap panel-body panel-body-noheader" title="" style="width: 99.8%;">
				<div class="datagrid-toolbar">
				<table cellspacing="0" cellpadding="0"><tbody>
				  <tr>
					<td>
					  <a href="javascript:sub_add(<?php echo ($_GET['id']); ?>);" class="l-btn l-btn-small l-btn-plain" group="" id="add"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text" style="impBtn hMargin fLeft shadow">添加</span><span class="l-btn-icon icon-add">&nbsp;</span></span></a>
					</td>
					<td><div class="datagrid-btn-separator"></div></td>


				    <td>
					  <a href="javascript:sub_forbid();" class="l-btn l-btn-small l-btn-plain" group="" id="no"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text" style="impBtn hMargin fLeft shadow">禁用</span><span class="l-btn-icon icon-no">&nbsp;</span></span></a>
					</td>
					<td>
					<div class="datagrid-btn-separator"></div>
					</td>

				    <td>
					  <a href="javascript:sub_resume();" class="l-btn l-btn-small l-btn-plain" group="" id="ok"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text" style="impBtn hMargin fLeft shadow">通过</span><span class="l-btn-icon icon-ok">&nbsp;</span></span></a>
					</td>
					<td><div class="datagrid-btn-separator"></div></td>

				    <td>
					  <a href="javascript:sort(<?php echo ($_GET['id']); ?>);" class="l-btn l-btn-small l-btn-plain" group="" id="restore"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text" style="impBtn hMargin fLeft shadow">排序</span><span class="l-btn-icon icon-restore">&nbsp;</span></span></a>
					</td>
					<td>
					<div class="datagrid-btn-separator"></div>
					</td>

				</tr>
				</tbody>
			    </table>
				</div>
				  <div class="datagrid-view">
				    <FORM METHOD=POST ACTION="">
					<table id="checkList" class="list" cellpadding=0 cellspacing=0 >
					<tr class="row" >
					  <th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th>
					  <th width="5%">
					  <?php if(($sort) == "1"): ?><div class="datagrid-cell datagrid-cell-c1-name datagrid-sort-desc" style="text-align: center;" onclick="javascript:sortBy('id','1','index');"><span>编号</span><span class="datagrid-sort-icon">&nbsp;</span></div>
					  <?php else: ?>
					  <div class="datagrid-cell datagrid-cell-c1-name datagrid-sort-asc" style="text-align: center;" onclick="javascript:sortBy('id','0','index');"><span>编号</span><span class="datagrid-sort-icon">&nbsp;</span></div>
					  </a><?php endif; ?>
					  </th>
					  <th width="8%">名称</th>
					  <th width="8%">关联</th>
					  <th width="8%">排序</th>
					  <th width="30%">图片</th>
					  <th>状态</th>
					  <th>操作</th>
					</tr>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="row_<?php echo ($vo["id"]); ?>" class="datagrid-row <?php if(($mod) == "1"): ?>datagrid-row-alt<?php endif; ?>">
					  <td><input type="checkbox" name="key"	value="<?php echo ($vo["id"]); ?>"></td>
					  <td><?php echo ($vo["id"]); ?></td>
					  <td><a href="/git/vshop/admin/index.php/Position/edit/id/<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></a></td>
					  <td><?php echo (rs($vo["data_type"],'product_list#产品列表@product_detail#产品详情@article_list#文章列表@article_detail#文章详情')); ?></td>
					  <td>
					  <?php echo ($vo["sort"]); ?>
					  </td>
					  <td style="text-align:left;padding:8px; cursor: pointer;"> 
					  
					  <img src="<?php echo ($vo["lit_pic"]); ?>" height="50px;" onclick="art_open(\'<img src=<?php echo ($vo["lit_pic"]); ?>>\');">
					  
					  </td>
					  <td><?php echo (rs($vo["status"],'0#禁用#red@1#启用#blue')); ?></td>
					  <td style="text-align:left;height:auto;">
						&nbsp;<a href="/git/vshop/admin/index.php/Position/sub_edit/id/<?php echo ($vo["id"]); ?>" >编辑</a>&nbsp;
						<a href="javascript:;" onclick="ajax_sub_del('<?php echo ($vo["id"]); ?>')">删除</a>&nbsp;
					  </td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</table>
					</FORM>
					<!--  分页显示区域 -->
					  <div class="datagrid-pager pagination">
					   <div class="page" style="float:left"><?php echo ($page); ?></div>
					   <div class="pagination-info">
						共<?php echo ($count); ?>条记录
					   </div>
					   <div style="clear:both;"></div>
					  </div>
				  </div>
			
			<div class="clear"></div>
		</dd>
	</dl>
<!-- /内容区 -->
</div>
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
			$(this).children().attr('src','/git/vshop/admin/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/git/vshop/admin/Public/images/ico_8.png');
		}
	  );
</script>