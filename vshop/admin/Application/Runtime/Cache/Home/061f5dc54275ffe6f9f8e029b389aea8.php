<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <!-- 内容区 -->
      <style>
    #Validform_msg {
	display: none
	}
	img {
		cursor: pointer;
	}
	.editor .ke-toolbar span,
	.editor .ke-statusbar span {
		padding: 0px;
	}
	.editor .ke-toolbar .ke-outline {
		border: 1px solid #F0F0EE;
		cursor: pointer;
		display: block;
		float: left;
		font-size: 0;
		line-height: 0;
		margin: 1px;
		overflow: hidden;
		padding: 1px 2px;
	}
	.imgWrap div {
		border: 1px solid #3B72A5;
		cursor: pointer;
		float: left;
		margin-left: 26px;
		margin-top: 20px;
		position: relative;
	}
	.imgWrap div span {
		color: #FFFFFF;
		display: none;
		padding: 0 5px;
		position: absolute;
	}
	.setdef {
		background: none repeat scroll 0 0 #F48C3A;
		bottom: 0;
		line-height: 18px;
		height: 18px;
		position: absolute;
		right: 0;
        padding: 0 5px;
        display: none;
        z-index: 9999;
        color: #FFFFFF;
	}
    .setdel:hover{
        color: #E4F14A;
    }
	.setdel {
        cursor:pointer;
		background: none repeat scroll 0 0 #3b72a5;
		top: 200;
		color: #FFFFFF;
		display: none;
		height: 18px;
		line-height: 18px;
		padding: 0 5px;
		position: absolute;
		right: 0px;
        z-index: 9999;
	}
	table.areaBox th {
		background: #e8f5fc;
		border-bottom: 1px solid #e8f5fc;
	}
	table.areaBox {
		border-left: 1px solid #e8f5fc;
		border-right: 1px solid #e8f5fc;
		border-top: 1px solid #e8f5fc;
		margin: 10px auto;
		width: 720px;
	}
	table.areaBox td {
		border-bottom: 1px solid #e8f5fc;
	}
	.add_area,
	table.areaBox {
	width: 98%;
}</style>
    <div class="content">
        <div class="site">
	        <?php echo C('site_name');?> <a href="/guoji/vshop/admin2/index.php/Product">商品管理</a> > 编辑商品
    	</div>
		<span class="line_white"></span>
    <div class="install tabs mt10">
        <dl>
            <dt><a href="javascript:" class="hover">基本信息</a><a href="javascript:">详细描述</a><a href="javascript:">商品图册</a><a href="javascript:">商品规格</a></dt>
            <form method="post" action="/guoji/vshop/admin2/index.php/Product/edit?<?php echo time(); ?>" name="goodsForm" class="goodsForm" enctype="multipart/form-data">
                <dd>
                    <ul class="web">
                        <li>
                            <strong>商品名称：</strong>
                            <input type="text" value="<?php echo ($vo["name"]); ?>" class="text_input" datatype="*" nullmsg="请输入商品名称！" name="name"  required/><span>填写商品名称</span>
                        </li>

                        <li>
                            <strong>副标题：</strong>
                            <input type="text" value="<?php echo ($vo["subtitle"]); ?>" class="text_input" datatype="*" nullmsg="请输入商品副标题！" name="subtitle"  /><span>填写商品副标题</span>
                        </li>

                        <li>
                            <strong>商品货号：</strong>
                            <input type="text" value="<?php echo ($vo["sn"]); ?>" class="text_input" datatype="*" nullmsg="请输入商品货号！" name="sn" id="sn" /><span>填写商品货号</span>
                        </li>


                        <li>
                            <strong>市场价：</strong>
                            <input type="text" value="<?php echo ($vo["market_price"]); ?>" class="text_input" datatype="*" nullmsg="请输入市场价！" name="market_price"  required/><span>填写市场价</span>
                        </li>

                        <li>
                            <strong>销售价：</strong>
                            <input type="text" value="<?php echo ($vo["price"]); ?>" class="text_input" datatype="*" nullmsg="请输入销售价！" name="price"  required/><span>填写销售价</span>
                        </li>

                        <li>
                            <strong>库存：</strong>
                            <input type="text" value="<?php echo ($vo["stock"]); ?>" class="text_input" name="stock"  /><span>填写库存，有规格则根据规格库存统计</span>
                        </li>

						<li>
                            <strong>推荐图：<input type="file" name="lit_pic" id="lit_pic" onchange="yulan(this,'show1')"></strong>
							<span id="show1">
							<?php if(($vo["lit_pic"]) != ""): ?><img src="<?php echo (get_thumb($vo["lit_pic"],'200')); ?>" width="200px;"><?php endif; ?>
							</span>
						</li>
                        <li>
                        <strong>商品分类：</strong>
                        <div class="fenlei">
                            <div class="fentt clearfix">
                                <h3 class="fl">所选<br />分类</h3>
                                <div class="sl fl">

								<div> <?php echo ($vo["cat_name"]); ?> <em><img src="/guoji/vshop/admin2/Public/images/ico_close1.png"></em><input name="cat_id" value="<?php echo ($vo["cat_id"]); ?>" type="hidden"></div>

                                </div>
                                <div class="flts fr">
                                    	选择商品所属分类，一个商品可选择多个分类
                                </div>
                            </div>
                            <div class="fendd clearfix">
                                <div class="root">
								 <?php if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$producttype): $mod = ($i % 2 );++$i; if(($producttype["pid"]) == "0"): ?><a href="javascript:void(0)" onclick="nb_category(<?php echo ($producttype["id"]); ?>,this)" id="<?php echo ($producttype["id"]); ?>" <?php if(($vo["top_cid"]) == $producttype["id"]): ?>class="hover"<?php endif; ?>> <?php echo ($producttype["name"]); ?> </a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <div class="bgef" <?php if(($types2) != ""): ?>style="background: rgb(255, 255, 255);"<?php endif; ?>>
								 <?php if(is_array($types2)): $i = 0; $__LIST__ = $types2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$producttype): $mod = ($i % 2 );++$i;?><a href="javascript:void(0)" onclick="nb_category(<?php echo ($producttype["id"]); ?>,this)" id="<?php echo ($producttype["id"]); ?>"  <?php if(($producttype["hover"]) == "1"): ?>class="hover"<?php endif; ?>> <?php echo ($producttype["name"]); ?> </a><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <div class="bgef" <?php if(($types3) != ""): ?>style="background: rgb(255, 255, 255);"<?php endif; ?>>
								 <?php if(is_array($types3)): $i = 0; $__LIST__ = $types3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$producttype): $mod = ($i % 2 );++$i;?><a href="javascript:void(0)" onclick="nb_category(<?php echo ($producttype["id"]); ?>,this)" id="<?php echo ($producttype["id"]); ?>" <?php if(($producttype["hover"]) == "1"): ?>class="hover"<?php endif; ?>> <?php echo ($producttype["name"]); ?> </a><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <div class="bgef" <?php if(($types4) != ""): ?>style="background: rgb(255, 255, 255);"<?php endif; ?>>
								 <?php if(is_array($types4)): $i = 0; $__LIST__ = $types4;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$producttype): $mod = ($i % 2 );++$i;?><a href="javascript:void(0)" onclick="nb_category(<?php echo ($producttype["id"]); ?>,this)" id="<?php echo ($producttype["id"]); ?>" <?php if(($producttype["hover"]) == "1"): ?>class="hover"<?php endif; ?>> <?php echo ($producttype["name"]); ?> </a><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class=" xtishi">
                            <div class="fl">小提示：所选分类应为最后一级子分类。双击可选中该分类或点击按钮确定选择</div>
                            <div class="submit fr">
                                <a href="javascript:" id="add_cat">选择当前分类</a>
                            </div>
                        </div>
						</li>
                        <li>
                            <strong>商品品牌：</strong>
                             <select name="brand_id" class="select" style="margin-right: 48px;" required>
                                <option value="0">请选择</option>
								 <?php if(is_array($brands)): $i = 0; $__LIST__ = $brands;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): $mod = ($i % 2 );++$i;?><option value="<?php echo ($brand["id"]); ?>" <?php if(($vo["brand_id"]) == $brand["id"]): ?>selected<?php endif; ?> >
								 <?php echo ($brand["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
							<span>为商品选择所属品牌，便于用户按照品牌进行查找</span>
                        </li>
                        <li>
                            <strong>上架销售：</strong>
                            <b style="margin-right: 44px;">
                                <label><input type="radio" <?php if(($vo["status"]) == "1"): ?>checked<?php endif; ?> name="status" value="1" /> 是 </label>
                                <label><input type="radio" <?php if(($vo["status"]) == "0"): ?>checked<?php endif; ?> name="status" value="0" /> 否 </label>
                            </b>
                            <span style="margin-left:1px">设置当前商品是否上架销售，默认为是，如选择否，将不在前台显示该商品</span>
                        </li>
                        <li>
                            <strong>免邮：</strong>
                            <b style="margin-right: 44px;">
                                <label><input type="radio" <?php if(($vo["is_free_shipping"]) == "1"): ?>checked<?php endif; ?> name="is_free_shipping" value="1" /> 是 </label>
                                <label><input type="radio" <?php if(($vo["is_free_shipping"]) == "0"): ?>checked<?php endif; ?> name="is_free_shipping" value="0" /> 否 </label>
                            </b>
                            <span style="margin-left:1px">设置商品是否免邮，默认为是，如选择否，将在确认订单时显示邮费</span>
                        </li>

                        <li>
                            <strong>货重：</strong>
                            <input type="text" value="<?php echo ($vo["nw"]); ?>" class="text_input" name="nw"  /><span>填写货重，货物重量，计算邮费使用。单位公斤</span>
                        </li>

                        <li>
                            <strong>自定义属性：</strong>
                            <b style="margin-right: 44px;">
							<?php if(is_array($flags)): $k = 0; $__LIST__ = $flags;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$flag): $mod = ($k % 2 );++$k;?><label>
							<input name="flags[]" value="<?php echo ($flag['id']); ?>" type="checkbox"
							<?php if($vo['flags']){ $ar = explode(',',$vo['flags']); if(array_search($flag['id'],$ar)!==false){ echo 'checked="checked"'; } } ?>	
							><?php echo ($flag['name']); ?>&nbsp;
							<?php if($k%5==0){ echo "<br>"; } ?>
							</label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </b>
                            <span style="margin-left:1px">设置商品状态属性，可进行多选</span>
                        </li>
						<!--
                        <li>
                            <strong>库存警告：</strong>
                            <input type="text" class="text_input" value="<?php echo ($vo["warn_number"]); ?>" name="warn_number"><span>填写商品库存警告数，当库存小于等于警告数，系统就会提醒此商品为库存警告商品，系统默认为2</span>
                        </li>
						-->
                        <li>
                            <strong>商品积分：</strong>
                            <input type="text" class="text_input" value="<?php echo ($vo["integral"]); ?>" name="integral" ><span>设置此商品每消费1元可以获得多少积分，默认为-1，即按照系统设置的积分换算比例，设为0则此商品不参与积分</span>
                        </li>

                        <li>
                            <strong>商品关键词：</strong>
                            <input type="text" value="<?php echo ($vo["keyword"]); ?>" name="keyword" class="text_input" /><span style="margin-left:-2px">用于在前台、后台筛选商品，多个关键词用空格分开，同时作为商品的Meta Keyword，有利于搜索引擎优化</span>
                        </li>
                        <li>
                            <strong>商品描述：</strong>
                            <textarea name="description" style="margin-right: 52px;"><?php echo ($vo["description"]); ?></textarea><p class="p">为商品编辑内容描述，同时作为商品的Meta Description，有利于搜索引擎优化</p>
                        </li>
                    </ul>
                </dd>

                <dd>
                   <div class="edit_box">
                       <strong class="edit">您正在编辑当前商品详细信息，默认所见即所得模式，您也可以点击HTML源码切换到代码模式进行编辑。</strong>
                       <div class="editor edit">
					   <textarea id="content" style="width:650px;height:345px" name="content" ><?php echo ($vo["content"]); ?></textarea><script type="text/javascript" src="/guoji/vshop/admin2/Public/Js/KindEditor/kindeditor-min.js"></script><script>KindEditor.create('#content', {uploadJson : '/guoji/vshop/admin2/index.php/Attachment/editer_upload',fileManagerJson : '/guoji/vshop/admin2/index.php/Attachment/editer_manager',allowFileManager : false,items:['source','title', 'fontname', 'fontsize', '|', 'link', 'unlink','|', 'forecolor', 'bgcolor', 'bold','italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', '|', 'image', 'multiimage', 'emoticons','baidumap']});</script>
					   </div>
                   </div>
                </dd>
                <dd>
			    <?php if(($vo["imgs"]) == ""): ?><div id="wrapper">
				   <div id="container">
					<!--头部，相册选择和格式选择-->
					 <div id="uploader">
					   <div class="queueList">
						   <div id="dndArea" class="placeholder">
							  <div id="filePicker">
							  </div>
							</div>
						   <ul class="filelist">
						   </ul>
					   </div>
					   <div class="statusBar" style="display:none">
						   <div class="progress">
								<span class="text">0%</span>
								<span class="percentage"></span>
						   </div>
						   <div class="info"></div>
						   <div class="btns">
							 <div id="filePicker2" class="webuploader-containe webuploader-container"></div><div class="uploadBtn state-finish">开始上传</div>
						   </div>
					   </div>
					 </div>
				   </div>
				</div>
				<?php else: ?>
				<div id="wrapper">
				   <div id="container">
					  <div id="uploader">
						 <div class="queueList">
							 <div id="dndArea" class="placeholder element-invisible">
								<div id="filePicker" class="webuploader-container"></div>
								</div>
							 <ul class="filelist">
							 <?php if(is_array($vo["imgs"])): $i = 0; $__LIST__ = $vo["imgs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><li style="border: 1px solid rgb(59, 114, 165)" order="100"><img width="152" height="152" alt="" src="<?php echo ($img["domain"]); echo ($img["filepath"]); ?>"><span class="setdel" style="display: none;" data-id="<?php echo ($img["picid"]); ?>">删除</span></li><?php endforeach; endif; else: echo "" ;endif; ?>		 
							 </ul>
						</div>
						<div class="statusBar" style="">
						   <div class="progress">
								<span class="text"></span>
								<span class="percentage"></span>
						   </div>
						   <div class="info"></div>
						   <div class="btns">
							  <div id="filePicker2" class="webuploader-containe webuploader-container"></div>
							  <div class="uploadBtn state-finish">开始上传</div>
						   </div>
						</div>
					  </div>
				   </div>
			    </div><?php endif; ?>
				<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/style.css" />
				<link rel="stylesheet" type="text/css" href="/guoji/vshop/admin2/Public/webuploader.css" />
				<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/image-upload/webuploader.js"></script>
                </dd>

                <dd>
					<ul class="web">
					  <li>
                            <strong>商品类型：</strong>
							  <select name="product_type" id="product_type" onchange="goods_type_change();">
								 <option value="-1">无属性</option>
								 <?php if(is_array($product_types)): $i = 0; $__LIST__ = $product_types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$producttype): $mod = ($i % 2 );++$i;?><option value="<?php echo ($producttype["id"]); ?>" <?php if(($vo["product_type"]) == $producttype["id"]): ?>selected<?php endif; ?> >
								 <?php echo ($producttype["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							  </select>	
							<span>为商品选择类型，不同类型有不同商品属性。</span>
                        </li>
					</ul>
					<ul class="web2 p1">
					<li>
						<sTRong>规格数据：</sTRong>
						<dl class="blue_table mt10">
							<dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;"><img src="/guoji/vshop/admin2/Public/images/spec_add.png" onclick="selSpec($('#product_type').val())" style="padding:10px;float:left;cursor: pointer;">
							<span class="add" style="float:left;line-height: 22px;;cursor: pointer;padding-left: 50px;margin-top:13px;">点击添加商品规格可为不同规格的商品指定不同的库存和和价格，方便用户选择购买</span>
							<span class="delete_checked_goods" onclick="delChecked()">多选删除</span>
							<span class="change_all_goods">批量修改</span>
							<img src="/guoji/vshop/admin2/Public/images/input_8.png" onclick="delAll()" style="padding:10px;float:right;cursor: pointer;">
							</dt>
							<div>
								<table class="border_table">
									<thead id="goodsBaseHead">
									<TR>
										<th width="5%"><label id="select_all"><input type="checkbox"> 全选</label></th>
										<th width="15%">商品条码</th>
										<th width="15%">商品货号</th>
										<?php if(is_array($attribute)): $i = 0; $__LIST__ = $attribute;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attribute_value): $mod = ($i % 2 );++$i;?><th><?php echo ($attribute_value["attr_name"]); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
										<th width="5%">库存</th>
										<th width="10%">销售价格</th>
										<th width="10%">图片</th>
										<th>操作</th>
									</TR>
									</thead>

									<tbody id="goodsBaseBody">
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product): $mod = ($i % 2 );++$i;?><TR id="tr_<?php echo ($product["id"]); ?>" data-id="<?php echo ($product["id"]); ?>">
									  <TD><input type="checkbox" name="key" value="<?php echo ($product["id"]); ?>"></TD>
									  <TD>
									  <INPUT TYPE="text" NAME="spec_barcode[<?php echo ($product["id"]); ?>]" value="<?php echo ($product["barcode"]); ?>" placeholder="请输入条形码">
									  <!--
									  <span onclick="field_value_update(this, 'barcode', <?php echo ($product["id"]); ?>)"><?php echo ((isset($product["barcode"]) && ($product["barcode"] !== ""))?($product["barcode"]):'请输入商品条码！'); ?></span>
									  -->
									  </TD>
									  <TD>
									  <INPUT TYPE="text" NAME="spec_sn[<?php echo ($product["id"]); ?>]" value="<?php echo ($product["sn"]); ?>" placeholder="请输入货号">
									  <!--
									  <span onclick="field_value_update(this, 'sn', <?php echo ($product["id"]); ?>)" style="width:500px;"><?php echo ($product["sn"]); ?></span>
									  -->
									  </TD>
									  <TD>
									  <INPUT TYPE="text" NAME="spec_stock[<?php echo ($product["id"]); ?>]" value="<?php echo ($product["stock"]); ?>">
									  </TD>
									  <?php if(is_array($product["product_attr"])): $i = 0; $__LIST__ = $product["product_attr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods_attr): $mod = ($i % 2 );++$i;?><TD scope="col"><div align="center"><?php echo ($goods_attr); ?></div></TD><?php endforeach; endif; else: echo "" ;endif; ?>
									  <TD>
									  <INPUT TYPE="text" NAME="spec_price[<?php echo ($product["id"]); ?>]" value="<?php echo ($product["price"]); ?>">
									  <!--
									  <span onclick="field_value_update(this, 'price', <?php echo ($product["id"]); ?>)"><?php echo ($product["price"]); ?></span>
									  --></TD>
									  <TD>
									  <INPUT TYPE="text" NAME="spec_pic[<?php echo ($product["id"]); ?>]" id="pic_<?php echo ($product["id"]); ?>" value="<?php echo ($product["lit_pic"]); ?>">
										<font class="uplogo" style="cursor: pointer;left:300px;line-height: 22px;" onclick="PicUpload('pic_<?php echo ($product["id"]); ?>',300,300)">选择</font>
										<font class="uplogo" style="cursor: pointer;line-height: 22px;left:330px;" onclick="viewImg('pic_<?php echo ($product["id"]); ?>')">预览</font>
									  </TD>
									  <TD>
									  <a href="javascript:void(0)" onclick="product_del(<?php echo ($product["id"]); ?>,<?php echo ($product["product_id"]); ?>);">删除</a>
									  </TD>
									</TR>
									<INPUT TYPE="hidden" NAME="item_id[]" value="<?php echo ($product["id"]); ?>"><?php endforeach; endif; else: echo "" ;endif; ?>
									</tbody>
								</table>
							</div>
						</dl>
					</li>    
					</ul>
					<style>
					.web2 td span{
					  /*margin-top:13px;*/
					  width:100%;
					  /*border:1px red solid;*/
					}

					.web2 td input{
					  padding:0px;
					  margin:0px;
					}

					.blue_table table tr {
					  height: 40px;
					  line-height: 30px;
					  border-top: 1px solid #aadaff;
					}

					.blue_table input {
					  width: 50%;
					}
					</style>
					<script>
						var defaultProductNo = "<?php echo ($vo["sn"]); ?>";
						var default_sn = '<?php echo ($vo["sn"]); ?>';
						var default_number = '1';
						var default_price = '<?php echo ($vo["price"]); ?>';
						var products = <?php echo ($list_json); ?>;
						var product_id = '<?php echo ($vo["id"]); ?>';
						//类型改变
						function goods_type_change(){
						  var id = $("#product_type").val();
						  $.ajax({
							type:"POST",
							dataType:'json',
							url: URL+"/get_attr_val",
							data:"id="+id,
							success:function(objs){
							  $('.add_th').remove();
							  if(objs){
								  html = '';
								  for(i=0;i<objs.length;i++){
									html += '<th class="add_th">'+objs[i].attr_name+'</th>';
								  }
								  if(html)$('#select_all').parents().find('th').eq(3).after(html);
							  }
							}
						  })
						}
						//批量修改信息
						$('.change_all_goods').click(function(){
						    //alert(1122);
							art.dialog({
								padding: '0px ',
								id: 'BatchEditingMoney',
								background: '#ddd',
								opacity: 0.3,
								title: '批量编辑商品信息',
								content: document.getElementById('batcheditGoods'),
								ok:function() {
									var number_change 	= $('[name="number_change"]').val();
									var price_change 		= $('[name="price_change"]').val();
									var num_reg = /^[-\+]?\d*$/;
									var price_reg = /^[-\+]?\d+(\.\d{2})?$/;
									if(!(num_reg.test(number_change)) || !(price_reg.test(price_change))){
										alert('请输入正确的数字!');
										return false;
									}
									//库存
									$('#goodsBaseBody [name^="spec_stock"]').each(function(index,data){
										num = parseInt($(this).val()) + parseInt(number_change);
										num = num < 0 ? 0 : num;
										$(this).val(num);
									})
									//销售价
									$('#goodsBaseBody [name^="spec_price"]').each(function(index,data){
										num = Number($(this).val()) + Number(price_change);
										num = num < 0 ? 0 : num;
										$(this).val(num.toFixed(2));
									})

									$('[name="number_change"]').val('+0');
									$('[name="price_change"]').val('+0.00');
									return true;
								},
								cancel:true
							});
						});
						// 全选
						$(window).load(function(){
							$('#select_all').on('click',"input",function() {
								if ($(this).is(':checked') == true) {
									$("input[name='key']").each(function() {
										$(this).attr("checked",true);
									});
								} else {
									$("input[name='key']").each(function() {
										$(this).attr("checked",false);
									});
								}                
							});
							$("input[name='key']").click(function() {
								if($(this).attr("checked")){
									$(this).attr("checked","true");
								}else{
									$(this).removeAttr("checked");
								}
								var num= 0;
								$("input[name='key']").each(function() {
									if($(this).attr("checked")){
										num++;
									}
								});
								if(num==$("input[name='key']").length){
									$('#select_all').children('input').attr("checked","true");
								}else{
									$('#select_all').children('input').removeAttr("checked");
								}
							});
							goods_type_change();
						})
					</script>
                </dd>

                <div class="submit">
				    <INPUT TYPE="hidden" NAME="hash" value="<?php echo ($hash); ?>">
					<INPUT TYPE="hidden" NAME="id" value="<?php echo ($vo["id"]); ?>">
                    <input type="submit" class='button_search' value='提交'/>
                </div>
            </form>
        </dl>
    </div>
	<!--批量编辑商品信息弹窗-->
	<div id="batcheditGoods" class="BatchEditingMoney">
		<ul>
			<li class="w85"><strong>库存</strong></li>
			<li class="w85"><strong>销售价格</strong></li>
			<!--
			<li class="w85"><strong>市场价格</strong></li>
			<li class="w85"><strong>成本价格</strong></li>
			-->
		</ul>
		<ul>
			<li class="w85"><input type="text" name="number_change" value="+0" /></li>
			<li class="w85"><input type="text" name="price_change" value="+0.00" /></li>
			<!--
			<li class="w85"><input type="text" name="_market_price_change" value="+0.00" /></li>
			<li class="w85"><input type="text" name="_cost_price_change" value="+0.00" /></li>
			-->
		</ul>
		<p>小提示：此处修改的值将对所有商品值进行加减修改如:+10 -5<br>库存必须是整数  价格可带两位小数</p>
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
	<script type="text/javascript" src="/guoji/vshop/admin2/Public/js/apply/product.js"></script>
	<script>
	$(function() {
		//切换
		var tabTitle = ".tabs dt a";
		var tabContent = ".tabs dd";
		$(tabTitle + ":first").addClass("hover");
		$(tabContent).not(":first").hide();
		$(tabTitle).unbind("click").bind("click", function() {
			$(this).siblings("a").removeClass("hover").end().addClass("hover");
			var index = $(tabTitle).index($(this));
			$(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
		});
		$(tabTitle).eq(2).click(function(){
			var timenow = new Date().getTime();
			$.getScript("/guoji/vshop/admin2/Public/js/image-upload/upload.js?"+timenow);
		})
		//初始化分类选择
		JsonCategory = <?php echo (json_encode($types)); ?> ;
	})
	</script>