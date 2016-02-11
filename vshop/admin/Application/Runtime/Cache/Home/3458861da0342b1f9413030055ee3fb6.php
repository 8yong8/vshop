<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="x-ua-compatible"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($head_title); ?></title>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/css/style.css" />
<style>
body{
 background:#f1f1f1;
}
</style>
<!--[if IE 6]>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->

<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/jquery.js"></script>

<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/js/artDialog/skins/chrome.css" />
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/jquery.artDialog.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/artDialog/plugins/iframeTools.js"></script>

<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/css/icon.css">
<?php if(ACTION_NAME=='index' && MODULE_NAME!='Config'){ ?>
<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/js/EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/EasyUI/hd_default_config.js"></script>
<?php } ?>
<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址
var ROOT = '/guoji/vshop/admin2';
var URL = '/guoji/vshop/admin2/index.php/Product';
var APP	 =	 '/guoji/vshop/admin2/index.php';
var PUBLIC = '/guoji/vshop/admin2/Public';
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
		<img src="/guoji/vshop/admin2/Public/images/logo.png" alt="" height="60px;"/>
	</div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="top_menu fl">
			<?php if(is_array($menu1)): $i = 0; $__LIST__ = $menu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><a href='/guoji/vshop/admin2/index.php?c=<?php echo ($item['nlist'][0]['name']); echo ($item['nlist'][0]['param_str']); ?>' <?php if(($item["show"]) == "1"): ?>class='hover'<?php endif; ?> ><?php echo ($item["cname"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="/guoji/vshop/admin2/index.php?m=Cache&a=clear"><img src="/guoji/vshop/admin2/Public/images/ico_1.png" alt="" />更新缓存</a>
        <a href="javascript:;"><img src="/guoji/vshop/admin2/Public/images/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo $_SESSION['account']; ?></a>|
        <a href="/guoji/vshop/admin2/index.php?c=Index&a=uc_sup_infoxg" target="mainFrame">更改密码</a>|
        <a href="/guoji/vshop/admin2/index.php" target="_blank">网站前台</a>|
        <a href="/guoji/vshop/admin2/index.php?c=Public&a=logout">退出系统</a>|
    </div>
</div>

<div class="side">
    <div class="head">
		<?php if(!$_SESSION['logo']){ ?>
        <img src="/guoji/vshop/admin2/Public/images/head.jpg" width="43" height="43" alt="" />
		<?php }else{ ?>
		<img src="<?php echo $_SESSION['logo'];?>" width="43" height="43" alt="" />
		<?php } ?>

    </div>
    <h3><img src="/guoji/vshop/admin2/Public/images/ico_6.png" />管理员</h3>
    <ul>
		<?php if(is_array($left_nlist)): $i = 0; $__LIST__ = $left_nlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><li><a href='/guoji/vshop/admin2/index.php?c=<?php echo ($node["name"]); echo ($node["param_str"]); ?>' name='' class='n<?php echo ($node["id"]); ?> z_side <?php if((MODULE_NAME) == $node["name"]): ?>hover<?php endif; ?>'><?php echo ($node["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>

<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="/guoji/vshop/admin2/Public/images/ico_8.png" /></div>
	<!--
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
	-->
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<script>

//查看产品图
function album( sourceid ){
  window.location.href = "/guoji/vshop/admin2/index.php/Product/album/sourceid/"+sourceid;
}

function look(id){
    window.location.href="/guoji/vshop/admin2/index.php/Auction_log/index/aid/"+id;
}

function runguo(action){
    window.location.href="/guoji/vshop/admin2/index.php/Product/"+action;
} 
var pid;
function Timing(id){
  pid = id;
  var html = '年:<input id="year" value="<?php echo date("Y"); ?>"><br/>月:<input id="month"  value="<?php echo date("m"); ?>"><br/>日:<input id="day"  value="<?php echo date("d"); ?>"><br/>';
  html+= '小时:<select id="hour">';
  for(i=0;i<24;i++){
    html+= '<option value="'+i+'">'+i+'</option>';
  }
  html+= '</select>';
  html+= '分:<select id="minute">';
  for(i=0;i<60;i++){
    html+= '<option value="'+i+'">'+i+'</option>';
  }
  html+= '</select>';
  html+= '秒:<select id="second">';
  for(i=0;i<60;i++){
    html+= '<option value="'+i+'">'+i+'</option>';
  }
  html+= '</select>';
  ymPrompt.succeedInfo({message:html,width:400,height:260,title:'定时时间设置',handler:handler});
}
function handler(tp){
  if(tp=='close'){
	return false;
  }  
  var year = jQuery('#year').val();
  var month = jQuery('#month').val();
  var day = jQuery('#day').val();
  var hour = jQuery('#hour').val();
  var minute = jQuery('#minute').val();
  var second = jQuery('#second').val();
  var parameter = "pid="+pid+"&year="+year+"&month="+month+"&day="+day+"&hour="+hour+"&minute="+minute+"&second="+second;
  //alert(parth);return false;
  jQuery.ajax({
    type:"POST",
	url:URL+'/set_timing',
	data:parameter,
	success:function(msg){
	  alert(msg);
	}
  })
}

function auction(pid){
  //alert(pid);
  window.location.href="/guoji/vshop/admin2/index.php/Auction/add/pid/"+pid;
}

function add_attr(id){
  window.location.href="/guoji/vshop/admin2/index.php/Product/add_attr/id/"+id;
}

function prom(s){
  keyValue = getSelectCheckboxValues();
  if(!keyValue){
    alert('请先选择商品');
	return;
  }
  window.location.href="/guoji/vshop/admin2/index.php/Product/prom/id/"+keyValue+'/s/'+s;
}
</script>

	<!-- 内容区 -->
	<div class="content">
		<div class="site">
			<?php echo C('site_name');?> <?php echo ($board_title); ?> > <?php echo ($node_title); ?>
		</div>
		<span class="line_white"></span>
	<div class="goods mt10">
		<div class="guanli">
		<FORM METHOD=get ACTION="/guoji/vshop/admin2/index.php/Product">
			<span style="margin-right: 10px;">按分类查看</span>
			<select id="cat_id" name="cat_id" class="easyui-combobox combobox-f combo-f textbox-f" data-options="editable:false,panelHeight:'auto'" style="height: 26px;width:120px; display: none;" >
				<option value="">分类选择</option>
				<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i;?><option value="<?php echo ($cate["id"]); ?>" <?php if(($cat_id) == $cate["id"]): ?>selected<?php endif; ?> ><?php echo ($cate["node_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>

			<span style="margin-right: 10px;margin-left: 5px;">按品牌查看</span>

			<select id="brand_id" NAME="brand_id" class="easyui-combobox combobox-f combo-f textbox-f" data-options="editable:false,panelHeight:'auto'" style="height: 26px; display: none;" >
				<option value="">品牌选择</option>
				<?php if(is_array($brands)): $i = 0; $__LIST__ = $brands;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): $mod = ($i % 2 );++$i;?><option value="<?php echo ($brand["id"]); ?>" <?php if(($brand_id) == $brand["id"]): ?>selected<?php endif; ?>><?php echo ($brand["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>

			
			<span style="margin-right: 10px;margin-left: 5px;">商品状态</span>
			<select id="status" NAME="status" class="easyui-combobox combobox-f combo-f textbox-f" data-options="editable:false,panelHeight:'auto'" style="height: 26px; display: none;" >
			  <option value="">全选</option>
			  <option value="1" <?php if(($status) == "1"): ?>selected<?php endif; ?>>上架</option>
			  <option value="0" <?php if(($status) == "0"): ?>selected<?php endif; ?>>下架</option>
			</select>

			<span style="margin-right: 10px;margin-left: 5px;">警告状态：</span>

			<SELECT NAME="jg">
				<OPTION VALUE="" <?php if(($_GET['jg']) == ""): ?>SELECTED<?php endif; ?>>全部</OPTION>
				<OPTION VALUE="0" <?php if(($_GET['jg']) == "0"): ?>SELECTED<?php endif; ?>>正常商品</OPTION>
				<OPTION VALUE="1" <?php if(($_GET['jg']) == "1"): ?>SELECTED<?php endif; ?>>库存警告商品</OPTION>
			</SELECT>

			<span style="margin-right: 10px;margin-left: 5px;">搜索</span>
			<input id="keyword" name="keyword" class="easyui-textbox textbox-f" style="width: 210px; height: 26px; display: none;" prompt="输入商品名称/货号/条码" textboxname="keyword">
			<input type="submit" value="提交" class="button_search">
			</a>
		</FORM>
		</div>
	<dl class="mt10">
		<dt><p>
			<a href="/guoji/vshop/admin2/index.php?m=Product&a=index" <?php if(($status) == ""): ?>class="hover"<?php endif; ?>>全部商品</a>
			<a href="/guoji/vshop/admin2/index.php?m=Product&a=index&status=0" <?php if(($status) == "0"): ?>class="hover"<?php endif; ?>>下架商品</a>
			<!--
			<a href="/guoji/vshop/admin2/index.php?m=Product&a=index&status=2">缺货商品</a>
			<a href="/guoji/vshop/admin2/index.php?m=Product&a=index&status=3">库存警告</a>
			<a href="/guoji/vshop/admin2/index.php?m=Product&a=index&status=4">回收站</a></p>
			-->
		</dt>

		<dd>
			<div class="login mt10" style="border: none;">
				<table id="order_list_grid" style="width:100%"></table> 
			</div>
			<div id="mm1" class="easyui-menu" style="width:90px!important;">
					<!--
					<div>恢复商品</div>
					<div>销毁商品</div>
					-->
					<div onclick="prom(1);">设为促销</div>
					<div onclick="prom(0);">取消促销</div>
					<!--
					<div>设为热卖</div>
					<div>设为新品</div>
					<div>取消热卖</div>
					<div>取消新品</div>
					-->
					<div onclick="resume();">商品上架</div>
					<div onclick="forbid();">商品下架</div>
			 </div>
			<div class="clear"></div>
		</dd>

		<dd>
			<div class="login mt10" style="border: none;">
				<div class="panel datagrid easyui-fluid" style="width: 100%;">
				<div class="datagrid-wrap panel-body panel-body-noheader" title="" style="width: 99.8%;">
				<div class="datagrid-toolbar">
				<table cellspacing="0" cellpadding="0"><tbody>
				  <tr>
					<td>
					  <a href="javascript:add();" class="l-btn l-btn-small l-btn-plain" group="" id="addrow">
					  <span class="l-btn-left l-btn-icon-left"><span class="l-btn-text">添加</span><span class="l-btn-icon icon-add">&nbsp;</span></span></a>
					</td>
					<td><div class="datagrid-btn-separator"></div></td>
				    <td>
					<a href="javascript:foreverdel();" class="l-btn l-btn-small l-btn-plain" id="delrows"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text">删除</span><span class="l-btn-icon icon-del">&nbsp;</span></span>
					</a>
					</td>
					<td>
					<div class="datagrid-btn-separator"></div>
					</td>

					<td>
					  <a href="javascript:sort();" class="l-btn l-btn-small l-btn-plain" group="" id="import"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text" style="impBtn hMargin fLeft shadow">排序</span><span class="l-btn-icon icon-import">&nbsp;</span></span></a>
					</td>
					<td>
					<div class="datagrid-btn-separator"></div>
					</td>

					<td>
					  <a href="javascript:void(0)" class="l-btn l-btn-small l-btn-plain" group="" id="import"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text">导入</span><span class="l-btn-icon icon-import">&nbsp;</span></span></a>
					</td>
					<td><div class="datagrid-btn-separator"></div></td>
					<td>
					  <a href="javascript:void(0)" class="l-btn l-btn-small l-btn-plain" group="" id="export"><span class="l-btn-left l-btn-icon-left"><span class="l-btn-text">导出</span><span class="l-btn-icon icon-export">&nbsp;</span></span></a>
					</td>
					<td><div class="datagrid-btn-separator"></div></td>
					
					<td>
					<a href="javascript:void(0)" class="easyui-menubutton" data-options="iconCls:'icon-more',menu:'#mm1',menuAlign:'right',hasDownArrow:false">批量操作
					<span class="l-btn-icon icon-alledit">&nbsp;</span>
					</a>
					</td>
					
					<td><div class="datagrid-btn-separator"></div></td>
				</tr>
				</tbody>
			    </table>
				</div>

				  <div class="datagrid-view">
					<!-- Think 系统列表组件开始 -->
					<table id="checkList" class="list" cellpadding=0 cellspacing=0 >
					<!--<tr><td height="5" colspan="12" class="topTd" ></td></tr>-->
					<tr class="row" >
					  <th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th>
					  <th width="5%">
					  <?php if(($sort) == "1"): ?><div class="datagrid-cell datagrid-cell-c1-name datagrid-sort-desc" style="text-align: center;" onclick="javascript:sortBy('id','1','index');"><span>编号</span><span class="datagrid-sort-icon">&nbsp;</span></div>
					  <?php else: ?>
					  <div class="datagrid-cell datagrid-cell-c1-name datagrid-sort-asc" style="text-align: center;" onclick="javascript:sortBy('id','0','index');"><span>编号</span><span class="datagrid-sort-icon">&nbsp;</span></div>
					  </a><?php endif; ?>
					  </th>
					  <th>商品</th>
					  <th>分类</th>
					  <th>价格</th>
					  <th>库存</th>
					  <th>促销活动</th>
					  <th>添加时间</th>
					  <th>状态</th>
					  <th>操作</th>
					</tr>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="datagrid-row <?php if(($mod) == "1"): ?>datagrid-row-alt<?php endif; ?>"  >
					  <td><input type="checkbox" name="key"	value="<?php echo ($vo["id"]); ?>"></td>
					  <td><?php echo ($vo["id"]); ?></td>
					  <td style="text-align:left;height:auto;padding-left:8px;"><?php echo ($vo["name"]); ?></td>
					  <td><?php echo ($vo["cat_name"]); ?></td>
					  <td><?php echo ($vo["price"]); ?></td>
					  <td><?php echo ($vo["stock"]); ?></td>
					  <td><?php echo (rs($vo["is_pm"],'0#否@1#是#red')); ?> 【<a href="/guoji/vshop/admin2/index.php/Product/prom_list/prom_id/<?php echo ($vo["id"]); ?>" target="_blank">查看</a>】 </td>
					  <td>
					  <?php echo (toDate($vo["create_time"],'Y-m-d H#i#s')); ?>
					  </td>
					  <td><?php echo (rs($vo["status"],'0#下架#red@1#上架#blue')); ?></td>
					  <td style="text-align:left;height:auto;">
					  <?php if(($vo["status"]) == "1"): ?><a href="javascript:forbid(<?php echo ($vo["id"]); ?>)">下架</a>&nbsp;
					  <?php else: ?>
					  <a href="javascript:resume(<?php echo ($vo["id"]); ?>)">上架</a>&nbsp;<?php endif; ?>
					  <a href="javascript:edit('<?php echo ($vo["id"]); ?>')">编辑</a>&nbsp;
					  <a href="/guoji/vshop/admin2/index.php/Product/album/sourceid/<?php echo ($vo["id"]); ?>">图片编辑</a>&nbsp;
					  </td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</table>
					<!-- Think 系统列表组件结束 -->
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
			$(this).children().attr('src','/guoji/vshop/admin2/Public/images/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','/guoji/vshop/admin2/Public/images/ico_8.png');
		}
	  );
</script>