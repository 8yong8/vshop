<?php 
//状态信息
function getStatus($status) 
{
	return $status=="1"?"<span style='color:blue'>启用</span>":"<span style='color:red'>禁用</span>";
}

//计算文件大小
function byte_format($input, $dec=0) 
{ 
  $prefix_arr = array("B", "K", "M", "G", "T"); 
  $value = round($input, $dec); 
  $i=0; 
  while ($value>1024) 
  { 
     $value /= 1024; 
     $i++; 
  } 
  $return_str = round($value, $dec).$prefix_arr[$i]; 
  return $return_str;
}

//状态信息
function rs($value,$str){
  $array = explode('@',$str);
  foreach($array as $arr){
    $val = explode('#',$arr);
	if($val['0']==$value){
	  if(!$val['2']){
		$color = 'black';
	  }else{
	    $color = $val['2']; 
	  }
	  return "<font color='".$color."'>".$val['1']."</font>";
	  break;
	}
  }
}

//图片信息
function getimg($src){
  if($src){
    return '<img src='.__ROOT__.'/'.$src.' >';
  }else{
    return ;
  }
}
//倒序
  function cmp($a,$b){
    return ($a['ctime'] > $b['ctime']) ? -1 : 1;
  }

//组名
function getGroupName($id) 
{
    if($id==0) {
    	return '无上级组';
    }
	if(Session::is_set('groupName')) {
		$name	=	Session::get('groupName');
		return $name[$id];
	}
	$Group	=	D("Group");
	$list	=	$Group->getFields('id,name');
	$name	=	$list[$id];
	Session::set('groupName',$list);
    return $name;
}

//状态显示
function showStatus($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
			break;
	}
	return $info;
}

//状态显示
function showStatus2($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">上架</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">下架</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
			break;
	}
	return $info;
}


//获得模块/操作名
function getmodelname($m){
  $model = D('Node');
  $data['name'] = $m;
  $vo = $model->where($data)->find();
  return $vo['title']." [ $m ]";
}

//获得操作名
function getactionname($a){
  switch ($a) {
    case 'add':
        $name = '添加';
        break;
    case 'edit':
        $name = '修改';
        break;
    case 'insert':
        $name = '写入';
        break;
    case 'update':
        $name = '更新';
        break;
    case 'foreverdelete':
        $name = '删除';
        break;
    case 'resume':
        $name = '恢复';
        break;
    case 'forbid':
        $name = '禁用';
        break;
    case 'ajax_delpic':
        $name = '删除图片';
        break;
    case 'upablum':
        $name = '更新图片集';
        break;
  }
  return $name." [ $a ]";
}

//密码哈希加密
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}

//缩略图处理
function ImageResize($srcFile,$toW,$toH){
   $toFile = $srcFile;
   $info = "";
   $data = getimagesize($srcFile,$info);
   switch ($data[2]) 
   {
	   case 1:
		    if(!function_exists("imagecreatefromgif")){
		    	echo "你的GD库不能使用GIF格式的图片，请使用Jpeg或PNG格式！<a href='javascript:go(-1);'>返回</a>";
		    	exit();
		    }
		    $im = imagecreatefromgif($srcFile);
		    break;
	   case 2:
		    if(!function_exists("imagecreatefromjpeg")){
		    	echo "你的GD库不能使用jpeg格式的图片，请使用其它格式的图片！<a href='javascript:go(-1);'>返回</a>";
		    	exit();
		    }
		    $im = imagecreatefromjpeg($srcFile);    
		    break;
	   case 3:
		    $im = imagecreatefrompng($srcFile);    
		    break;
  }
  $srcW=imagesx($im);
  $srcH=imagesy($im);
  $toWH=$toW/$toH;
  $srcWH=$srcW/$srcH;
  if($toWH<=$srcWH){
       $ftoW=$toW;
       $ftoH=$ftoW*($srcH/$srcW);
  }
  else{
      $ftoH=$toH;
      $ftoW=$ftoH*($srcW/$srcH);
  }    
  if($srcW>$toW||$srcH>$toH)
  {
     if(function_exists("imagecopyresampled")){
        $ni = imagecreatetruecolor($ftoW,$ftoH);
        imagecopyresampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
     }
     else{
        $ni=imagecreatetruecolor($ftoW,$ftoH);
       	imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
     }
     if(function_exists('imagejpeg')) imagejpeg($ni,$toFile);
     else imagepng($ni,$toFile);
     imagedestroy($ni);
  }
  imagedestroy($im);
}

//EXCELS下载
function excelDown($array,$keynames,$filename){
	header("Content-type: application/vnd.ms-excel; charset=gbk");
    header("Content-Disposition: attachment; filename=$filename.xls");
	header("Content-Type:application/force-download");
	header("Content-Type:application/octet-stream");
	header("Content-Type:application/download");
	header("Content-Transfer-Encoding:binary");
    $data = '';
    foreach ($keynames as $k=>$v){
    	$data .= $v."\t";
    }
    $data .= "\n";
    foreach ($array as $key=>$val){
    	foreach ($val as $k=>$v){
    		//$data .= $v."\t";
			if($k=='create_time' || $k=='update_time'){
			  $data .= date('Y-m-d H:i:s',$v)."\t";
			}else{
			  $data .= $v."\t";
			}
    	}
    	$data .= "\n";
    }
    $data .= "\n";
    echo $data;
}

//coreseek查询调用
function CsGetData($index='*',$words='卫浴',$wheres='',$orderby='id desc',$firstRow=0,$listRows=10,$timeout=1,$host='localhost',$port=9312){
  require C('INTERFACE_PATH').'coreseek/api/sphinxapi.php';
  $mem = new Memcache;
  $result = $mem->connect('localhost', 11211);
  $mem_key = 'coreseek_config';
  if($mem->get($mem_key)){
	$config = $mem->get($mem_key);
  }else{
	$config = require C('ROOT_SITE_DIR').'coreseek_config.php';
	$mem->set($mem_key,$config,0,86400*365);
  }
  if(date('H')>5 && date('H')<6){
   $host = $config[1]['host'];
   $port = $config[1]['port'];
  }else{
   $host = $config[1]['host'];
   $port = $config[1]['port'];
  }
  $cl = new SphinxClient();
  $cl->SetServer($host,$port);
  $cl->SetArrayResult(true);
  $cl->SetConnectTimeout($timeout);
  if($wheres){
    foreach($wheres as $where){
      $cl->    SetFilter($where['field'],array($where['value']),$where['status']);
    }
  }
  $cl->SetSortMode(SPH_SORT_EXTENDED, $orderby);
  $cl->SetLimits($firstRow,$listRows);
  $cl->AddQuery($words,$index);
  $result = $cl->RunQueries();
  return $result;
}

//coreseek属性修改
function CsDataUpAttr($ids,$index='*',$attrs=array('status'),$values=0,$timeout=1,$host='localhost',$port=9312){
  require C('INTERFACE_PATH').'coreseek/api/sphinxapi.php';
  $cl = new SphinxClient();
  $cl->SetServer($host,$port);
  $cl->SetConnectTimeout($timeout);
  foreach($ids as $id){
    $cl->UpdateAttributes($index  , $attrs, array($id=>array($values)) );
  }
}

function get_member_msg($id,$fields='id,username,realname'){
  $model = M('Member');
  $vo = $model->field($fields)->where('id='.$id)->find();
  return $vo;
}

/**
 * 获取商品类型中包含规格的类型列表
 *
 * @return  array
 */
function get_goods_type_specifications(){
	$model = M('Attribute');
	$data['attr_type'] = 1;
	$row = $model->Distinct(true)->where($data)->field('cat_id')->select();
    $return_arr = array();
    if (!empty($row))
    {
        foreach ($row as $value)
        {
            $return_arr[$value['cat_id']] = $value['cat_id'];
        }
    }
    return $return_arr;
}

/*
   生成云购码 
   CountNum @ 生成个数
   len 	    @ 生成长度
   sid	    @ 商品ID
*/
function content_get_go_codes($allnum=null,$base_num=null){	
  $num = ceil($allnum/$base_num);
  $code_i = $allnum;
  $last_num = 0;
  for($i=1;$i<=$allnum;$i++){
	$codes[]=10000000+$i;
  }
  shuffle($codes);
  //验证码生成
  $codes_arr = array_chunk($codes,$base_num);
  return $codes_arr;
}

/**
 +----------------------------------------------------------
 * 属性处理
 +----------------------------------------------------------
 * $cat_id 分类ID
 +----------------------------------------------------------
 * $attr_vals 商品属性值
 +----------------------------------------------------------
 * @throws ThinkExecption
 +----------------------------------------------------------
 */
function get_attr_assign($cat_id,$attr_vals){
	$model = M('attribute');
	$attr_data['cat_id'] = $cat_id;
	$attrs = $model->where($attr_data)->select();
	foreach($attrs as $key=>$val){
	  $attr_id = $val['id'];
	  //唯一属性
	  if($val['attr_type']==0){
		if($val['attr_input_type']==0){
		//手动录入
		  if($attr_vals[$attr_id]){
		    $attrs[$key]['content'] = '<input name="attr_value_list['.$attr_id.']" value="'.$attr_vals[$attr_id][0]['attr_value'].'">';
		  }else{
	        $attrs[$key]['content'] = '<input name="attr_value_list['.$attr_id.']">';
		  }
		}else if($val['attr_input_type']==1){
		//列表中选择
		   $vals = explode('
',$val['attr_values']);
		   $attrs[$key]['content'] = '<select name="attr_value_list['.$attr_id.']"><option value="" selected>请选择...</option>';
		   foreach($vals as $v){
			 if($attr_vals[$attr_id] && $v==$attr_vals[$attr_id][0]['attr_value']){
			   $select = 'selected';
			 }else{
			   $select = '';
			 }
		     $attrs[$key]['content'] .= "<option value='$v' $select>$v</option>";
		   }
		   $attrs[$key]['content'] .= '</select>';		 
		}else if($val['attr_input_type']==2){
		//多行文本框
		  if($attr_vals[$attr_id]){
		    $attrs[$key]['content'] = '<TEXTAREA NAME="attr_value_list['.$attr_id.']" ROWS="18" COLS="8">'.$attr_vals[$attr_id][0]['attr_value'].'</TEXTAREA>';
		  }else{
		    $attrs[$key]['content'] = '<TEXTAREA NAME="attr_value_list['.$attr_id.']" ROWS="18" COLS="8"></TEXTAREA>';		  
		  }
		}
	  //单选属性
	  }else if($val['attr_type']==1){
			if($val['attr_input_type']==0){
			//手动录入
			  if($attr_vals[$attr_id]){
				foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <input name="attr_value_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_value'].'">';
					$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <input name="attr_value_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_value'].'">';
					$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';				  
				  }
				}
			  }else{
			    $attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <input name="attr_value_list['.$attr_id.'][]">';
			    $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';
			  }
			}else if($val['attr_input_type']==1){
			//列表中选择
			  $vals = explode('
',$val['attr_values']);
              if($attr_vals[$attr_id]){
			    foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					  $attrs[$key]['content'] .= '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <select name="attr_value_list['.$attr_id.'][]"><option value="" selected>请选择...</option>';
					  foreach($vals as $v){
						 if($v==$goods_attr_val['attr_value']){
						   $select = 'selected';
						 }else{
						   $select = '';
						 }
						$attrs[$key]['content'] .= "<option value='$v' $select>$v</option>";
					  }
					  $attrs[$key]['content'] .= '</select>';
					  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" />属性图片 <input type="text" name="attr_pic_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_pic'].'" id="pic_'.$goods_attr_val['id'].'"/><font class="uplogo" style="cursor: pointer;left:300px;line-height: 22px;" onclick="PicUpload(\'pic_'.$goods_attr_val['id'].'\',300,300)">选择</font>
						<font class="uplogo" style="cursor: pointer;line-height: 22px;left:330px;" onclick="viewImg(\'pic_'.$goods_attr_val['id'].'\')">预览</font>&nbsp;<input type="button" class="button" value=" - " onclick="product_attr_del('.$goods_attr_val['id'].',this)"></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <select name="attr_value_list['.$attr_id.'][]"><option value="" selected>请选择...</option>';
					  foreach($vals as $v){
						 if($v==$goods_attr_val['attr_value']){
						   $select = 'selected';
						 }else{
						   $select = '';
						 }
						$attrs[$key]['content'] .= "<option value='$v' $select>$v</option>";
					  }
					  $attrs[$key]['content'] .= '</select>';
					  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" />属性图片 <input type="text" name="attr_pic_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_pic'].'" id="pic_'.$goods_attr_val['id'].'"/><font class="uplogo" style="cursor: pointer;left:300px;line-height: 22px;" onclick="PicUpload(\'pic_'.$goods_attr_val['id'].'\',300,300)">选择</font><font class="uplogo" style="cursor: pointer;line-height: 22px;left:330px;" onclick="viewImg(\'pic_'.$goods_attr_val['id'].'\')">预览</font>&nbsp;<input type="button" class="button" value=" - " onclick="product_attr_del('.$goods_attr_val['id'].',this)"></span>';				  
				  }
				}
			  }else{
				  $attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <select name="attr_value_list['.$attr_id.'][]">';
				  foreach($vals as $v){
					$attrs[$key]['content'] .= "<option value='$v'>$v</option>";
				  }
				  $attrs[$key]['content'] .= '</select>';
				  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';
			  }
			}else if($val['attr_input_type']==2){
			//多行文本框
			  if($attr_vals[$attr_id]){
				foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8">'.$goods_attr_val['attr_value'].'</TEXTAREA>';
				    $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8">'.$goods_attr_val['attr_value'].'</TEXTAREA>';
				      $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';				  
				  }
				}			  
			  }else{
				$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8"></TEXTAREA>';
				$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';			  
			  }

			}	  
	  
	  //复选属性
	  }else if($val['attr_type']==2){
			if($val['attr_input_type']==0){
			//手动录入
			  if($attr_vals[$attr_id]){
				foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <input name="attr_value_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_value'].'">';
					$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <input name="attr_value_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_value'].'">';
					$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';				  
				  }
				}
			  }else{
			    $attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <input name="attr_value_list['.$attr_id.'][]">';
			    $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';
			  }
			}else if($val['attr_input_type']==1){
			  $vals = explode('
',$val['attr_values']);
              if($attr_vals[$attr_id]){
			    foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					  $attrs[$key]['content'] .= '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <select name="attr_value_list['.$attr_id.'][]"><option value="" selected>请选择...</option>';
					  foreach($vals as $v){
						 if($v==$goods_attr_val['attr_value']){
						   $select = 'selected';
						 }else{
						   $select = '';
						 }
						$attrs[$key]['content'] .= "<option value='$v' $select>$v</option>";
					  }
					  $attrs[$key]['content'] .= '</select>';
					  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <select name="attr_value_list['.$attr_id.'][]">';
					  foreach($vals as $v){
						 if($v==$goods_attr_val['attr_value']){
						   $select = 'selected';
						 }else{
						   $select = '';
						 }
						$attrs[$key]['content'] .= "<option value='$v' $select>$v</option>";
					  }
					  $attrs[$key]['content'] .= '</select>';
					  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';				  
				  }
				}
			  }else{
				  $attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <select name="attr_value_list['.$attr_id.'][]">';
				  foreach($vals as $v){
					$attrs[$key]['content'] .= "<option value='$v'>$v</option>";
				  }
				  $attrs[$key]['content'] .= '</select>';
				  $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';
			  }
			}else if($val['attr_input_type']==2){
			//多行文本框
			  if($attr_vals[$attr_id]){
				foreach($attr_vals[$attr_id] as $gav_key=>$goods_attr_val){
				  if($gav_key==0){
					$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');" id=""><img src="'.__PUBLIC__.'/images/expandall.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8">'.$goods_attr_val['attr_value'].'</TEXTAREA>';
				    $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';
				  }else{
					  $attrs[$key]['content'] .= '<span><br/><a href="javascript:;" onclick="val_del(this)"><img src="'.__PUBLIC__.'/images/minus.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8">'.$goods_attr_val['attr_value'].'</TEXTAREA>';
				      $attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" value="'.$goods_attr_val['attr_price'].'" size="5" maxlength="10" /></span>';				  
				  }
				}			  
			  }else{
				$attrs[$key]['content'] = '<span id="'.$attr_id.'"><a href="javascript:val_add('.$attr_id.');"><img src="'.__PUBLIC__.'/images/expandall.png"></a> <TEXTAREA NAME="attr_value_list['.$attr_id.'][]" ROWS="18" COLS="8"></TEXTAREA>';
				$attrs[$key]['content'] .= ' 属性价格 <input type="text" name="attr_price_list['.$attr_id.'][]" size="5" maxlength="10" /></span>';			  
			  }
			}	  
	  
	  }

	}
	return $attrs;
}


//获取省名称
function get_province_name($ids_str){
  static $pvs;
  static $cities;
  if(!$pvs){
	$pvs = getcache('Region:pvs');
  }
  if(!$cities){
	$cities = getcache('Region:cities');
  }
  $ids = explode(',',$ids_str);
  foreach($ids as $id){
    if($pvs[$id]){
	  $provinces[] = $pvs[$id]['area_name'];
	}else if($cities[$id]){
	  $provinces[] = $cities[$id]['area_name'];
	}
  }
  return implode(',',$provinces);
}
?>