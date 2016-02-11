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
var URL = '/guoji/vshop/ADMIN2/index.php/Attribute';
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
<style>
#Validform_msg{display: none}
.operator{cursor: pointer;padding:10px;}
.spec_input{margin:10px;}
img{cursor: pointer}
</style>
<div class="content">
    <div id="top-alert" class="fixed alert alert-error" style="display: none;">
        <button class="close fixed" style="margin-top: 4px;">&times;</button>
        <div class="alert-content">这是内容</div>
    </div>
    <div class="title">编辑属性[ <A HREF="/guoji/vshop/ADMIN2/index.php/Attribute">返回列表</A> ]</div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="/guoji/vshop/ADMIN2/index.php/Attribute/edit?<?php echo time(); ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>规格名称：</strong>
                                            <input type="text" class="text_input" name="attr_name" value="<?php echo ($vo["attr_name"]); ?>"/><span class="Validform_checktip " style="padding-left: 43px;">设置商品规格名称　如：颜色
                                            </span>
                                        </li>

                                        <li>
                                            <strong>所属商品类型：</strong>
											<SELECT NAME="cat_id">
											  <?php if(is_array($cats)): $i = 0; $__LIST__ = $cats;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cat): $mod = ($i % 2 );++$i;?><option value="<?php echo ($cat["id"]); ?>" <?php if(($vo["cat_id"]) == $cat["id"]): ?>selected<?php endif; ?>><?php echo ($cat["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
											</SELECT>	
											
											<span class="Validform_checktip " style="padding-left: 43px;">设置商品规格名称　如：颜色
                                            </span>
                                        </li>

                                        <li>
                                            <strong>状态：</strong>
                                            <input type="radio" name="status" value="1" checked="checked" /> 显示 <input type="radio" name="status" value="0"  /> 禁用<span class="Validform_checktip" style="margin-left:214px;">设置商品规格是否显示，默认为显示</span>
                                        </li>
                                    </ul>
                                    <dl class="blue_table mt10">
                                        <dt style="height:42px;"><img src="/guoji/vshop/ADMIN2/Public/Images/spec_add.png" id="specAddButton" style="padding:10px;float:left"><span class="add" style="float:left;line-height: 44px;">点击按钮添加商品规格，多个规格可使用规格右侧“上移”“下移”调整规格顺序。</span></dt>
                                        <dd>
                                                <table>
                                                <tbody id='spec_box'>
													<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr class="td_c odd" style="height: 30px;" id="tr_<?php echo ($val["id"]); ?>">
														<td width="350" align="left" height="35">
															<input class="text_input spec_input" type="text" name="edit_value[<?php echo ($val["id"]); ?>]" value="<?php echo ($val["attr_value"]); ?>" alt="填写规格值">
														</td>
														<td align="right">
															<span class="operator">
																<a href="javascript:;" onclick="del_val(<?php echo ($val["id"]); ?>);">删除</a>
															</span>
														</td>
													</tr><?php endforeach; endif; else: echo "" ;endif; ?>
												</tbody>
												</table>
                                        </dd>
                                    </dl>
                                    <div class="submit">
										<INPUT TYPE="hidden" NAME="id" value="<?php echo ($vo["id"]); ?>">
                                        <input type="submit" class='button_search' value='提交'/>
										<a href="/guoji/vshop/ADMIN2/index.php/Attribute">返回</a>
                                    </div>
                                </dd>
                            </form>  
                        </dl>
                    </div>
            </dd>
        </dl>
        
    </div>
<script>
//添加规格按钮(点击绑定)
$('#specAddButton').click(function(){
	var specSize = $('#spec_box tr').size();
	var specRow = getTr(specSize);
	$('#spec_box').append(specRow);
	initButton(specSize);
})

//根据显示类型返回格式
function getTr(indexValue) {
	//规格图片格式
	var specInputHtml = getValHtml();
	//数据
	var specRow = '<tr class="td_c"><td  width="350" align="left" height="35"> '+specInputHtml+'</td>'
	+'<td align="right">'
	+'<span  class="operator">删除</span> '
	+'</td></tr>';
	return specRow;
}

//规格值html
function getValHtml(dataValue) {
	if(dataValue == undefined)
	dataValue = '';
	return '<input class="text_input spec_input" type="text" name="value[]" value="'+(dataValue ? dataValue :"")+'" datatype="s" alt="填写规格值" />';
}

//按钮(点击绑定)
function initButton(indexValue) {
	//上传图片按钮
	$('#spec_box tr:eq('+indexValue+') td img').click(function(){
	});
	//功能操作按钮
	$('#spec_box tr:eq('+indexValue+') .operator').each(function(i){
		switch(i){
		//向上排序
			case 0:
				$(this).click(function(){
					var insertIndex = $(this).parent().parent().prev().index();
					if(insertIndex >= 0){
						$('#spec_box tr:eq('+insertIndex+')').before($(this).parent().parent());
					}
				})
				break;
		//向下排序
		case 1:
			$(this).click(function(){
				var insertIndex = $(this).parent().parent().next().index();
				$('#spec_box tr:eq('+insertIndex+')').after($(this).parent().parent());
			})
			break;
		// 删除排序
		case 2:
			$(this).click(function() {
				var obj = $(this);
				alert(1122);
				art.dialog({width: 320,id:'spec_del',title:'温馨提示',content: '确定要删除么？',ok:function(){obj.parent().parent().remove()},cancel:true});
			})
			break;
		}
	})
}

function del_val(id){
	$.ajax({
		cache: true,
		dataType:'json',
		type: "POST",
		url:URL+"/ajax_del_val/time/"+new Date().getTime(),
		data:'id='+id,
		async: false,
		error: function(request) {
				art.dialog({
					time: 2,
					content: '系统繁忙'
				});
		},
		success: function(data) {
			if(data.error_code==0){
				if(data.notice!=null){
					art.dialog({
						id: 'ajax_del',
						time: 2,
						content: data.notice
					});
				}
				$('#tr_'+id).remove();
			}else{
				art.dialog({
					id: 'ajax_del',
					time: 2,
					content: data.notice
				});
			}
		}
	});
}
</script>
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