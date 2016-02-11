<?php
namespace Home\Controller;
use Think\Controller;
class GiftController extends CommonController{

  public function _before_index(){
    //获取商品类型存在规格的类型
    $specifications = get_goods_type_specifications();
    $this->assign('specifications', $specifications);
  }

  /**
   * 添加
   */
  public function add(){
	if($_POST){
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('category');
		$tdata['id'] = $_POST['tid'];
		$vv = $model->where($tdata)->find();
		$_POST['type_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['toptid'] = $vv['id'];
		}else{
		  $_POST['toptid'] = $vv['id'];
		}
	    //图片处理处理
		if($_FILES){
		  $this->upload();
		}
		$name=CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['create_time'] = time();
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$result = $model->add();
		if (false !== $result) {
			//成功提示
			$this->history($result);
			//相关属性表修改
			$amodel = D('flag_list');
			if($flags){
				$data1['source'] = $name;
				$data1['sourceid'] = $result;
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  $amodel->add($data1);
				}
			}
		    if($_POST['add_type']){
		      $this->assign('jumpUrl',U($_POST['add_type'].'/add',array('pid'=>$result)));
		    }
			$this->success ('新增成功!');
		}else{
		  $this->error ('编辑失败!');
		}
		exit;
	}else{
		$this->other_msg();
		$flags = $this->get_moudel_flags();
		$this->assign('flags',$flags);
		$hash = md5($_SESSION[C('USER_AUTH_KEY')].time());
		$this->assign('imgurl',C('IMG_URL'));
		$this->assign('hash',$hash);
		$this->display();
	}
  }

  public function _before_edit(){
    //获取商品类型存在规格的类型
    $specifications = get_goods_type_specifications();
    $this->assign('specifications', $specifications);
  }	

  /**
   * 编辑
   */
  public function edit(){
    if($_POST){
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('category');
		$tdata['id'] = $_POST['tid'];
		$vv = $model->where($tdata)->find();
		$_POST['type_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['toptid'] = $vv['id'];
		}else{
		  $_POST['toptid'] = $vv['id'];
		}
	    //图片处理处理
		if($_FILES){
		  import("ORG.Net.UploadFile");
		  $upload = new UploadFile();
		  //设置上传文件大小
		  $upload->maxSize  = 10241024 ;
		  //设置上传文件类型
		  $upload->allowExts  = array('jpg','gif','png','jpeg');
		  $upload->saveRule = 'uniqid';
		  $path = $upload->savePath =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';
		  mk_dir($upload->savePath);
		  $upload->upload();
		  $info = $upload->getUploadFileInfo();
		  if($info!=""){
			foreach($info as $file){
			  if($file['size']>1024*500){
				//保留原图并压缩
				$ytfile = $path.$file['savename'].'_yt.jpg';
				copy($path.$file['savename'],$ytfile);
				//500KB
				$full_blitfilename = $path.$file['savename'];
				ImageResize($full_blitfilename,1000,1000);
			  }
			  $key = $file['key'];
			  $ar = getimagesize($path.$file['savename']);
			  if($file['size']>307200 || $ar[0]>250 || $ar[1]>250){
				//300KB
				$full_blitfilename = $path.$file['savename'];
				ImageResize($full_blitfilename,250,250);
			  }
			  $_POST[$key] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$file['savename'];
			}
		  }  	
		}
		$name=CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['create_time'] = time();
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$result = $model->save();
		if (false !== $result) {
			//成功提示
			$this->history($_POST['id']);
			//相关属性表修改
			$amodel = D('flag_list');
			$wdata['source'] = $name;
			$wdata['sourceid'] = $_POST['id'];
			$flagslist = $amodel->field('fid')->where($wdata)->select();
			foreach($flagslist as $v){
			  $key = array_search($v['fid'],$flags);
			  if($key!==false){
				//记录已经存在的
				$k_fids[] = $v['fid'];
			  }else{
				//要删除的
				$d_fids[] = $v['fid'];
			  }
			  $oldflags[] = $v['fid'];
			}

			if($d_fids){
			  $wdata['fid'] = array('in',$d_fids);
			  $amodel->where($wdata)->delete();
			}
			if($flags){
				$data1['source'] = $name;
				$data1['sourceid'] = $_POST['id'];
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  if($oldflags){
					$key = array_search($fid,$oldflags);
					//已存在信息修改
					if($key!==false){
					  $wdata['fid'] = $fid;
					  unset($data1['sort']);
					  $amodel->where($wdata)->save($data1);
					  continue;
					}
				  }
				  $amodel->add($data1);
				  //echo $amodel->getlastsql();exit;
				}
			}else{
			  $amodel = D('flag_list');
			  $del_data['source'] = $name;
			  $del_data['sourceid'] = $_POST['id'];
			  $flagslist = $amodel->where($del_data)->delete();
			}
			$this->success ('编辑成功!');
		}else{
		  $this->error ('编辑失败!');
		}
		exit;
	}else{
	  $flags = $this->get_moudel_flags();
	  $this->assign('flags',$flags);
	  $name=CONTROLLER_NAME;
	  $model = D ( $name );
	  $data['id'] = $_GET['id'];
	  $vo = $model->where($data)->find();
	  $this->assign('vo',$vo);
	  $this->other_msg($vo['toptid']);
	  $this->display();
	}
  }

  /**
   * 其他信息
   */
  function other_msg($pid=0){
	//$producttypes = $this->get_type();
	$model = M('Gif_category');
	$data['status'] = 1;
	//$data['pid'] = $pid;
	$types = $model->where($data)->select();
	$data['pid'] = $pid ? $pid : $types[0]['id'];
	$types2 = $model->where($data)->select();
	/*
	import('@.ORG.Util.Tree');
	$tree = new Tree($types);
	$types = $tree->get_tree('0');
	*/
	$this->assign('types',$types);
	$this->assign('types2',$types2);
  }

  /**
   * 分类信息
   */
  function get_category(){
	$model = M('Gif_category');
	$data['status'] = 1;
	$data['pid'] = $_POST['pid'];
	$types = $model->where($data)->select();
	if(!$types){
	  unset($data);
	  $data['id'] = $_POST['pid'];
	  $types = $model->where($data)->select();
	}
    echo json_encode($types);
  }

  /**
   * 相册管理
   */
  public function album(){
    $model = D('Pic');
	$data['source'] = CONTROLLER_NAME;
	$data['sourceid'] = $_GET['sourceid'];
	$pics = $model->where($data)->select();
	$this->assign('pics',$pics);
	$model = D('gift');
	$pdata['id'] = $_GET['sourceid'];
	$vo = $model->field('id,type_name,name,lit_pic')->where($pdata)->find();
	if(!$vo){
	  $this->error('产品不存在!');
	}
	$this->assign('vo',$vo);
    $this->display();
  }
  
  /**
   * 图片修改
   */
  public function upablum(){
	if($_FILES){
	  import("ORG.Net.UploadFile");
	  $upload = new UploadFile();
	  $is_thumb = 0;
	  $upload->thumb = $is_thumb;
	  $upload->thumbPrefix = '48_,400_';
	  $upload->thumbMaxWidth = '48,400';
	  $upload->thumbMaxHeight = '48,400';
	  //设置上传文件大小
	  $upload->maxSize  = 10241024 ;
	  //设置上传文件类型
	  $upload->allowExts  = array('jpg','gif','png','jpeg');
	  $upload->saveRule = 'uniqid';
	  $path = $upload->savePath =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';
	  $filepath = date('Y').'/'.date('m').'/'.date('d').'/';
	  mk_dir($upload->savePath);
	  $upload->upload();
	  $info = $upload->getUploadFileInfo();
	  if($info!=""){
		foreach($info as $file){
		  $key = $file['key'];
		  if($key=='up_zp'.$_POST['f_logo']){
			$_POST['logo'] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$file['savename'];
		  }
		  if($file['size']>307200){
			//保留原图并压缩
			$ytfile = $path.$file['savename'].'_yt.jpg';
			copy($path.$file['savename'],$ytfile);
			//300KB
		    $full_blitfilename = $path.$file['savename'];
			ImageResize($full_blitfilename,800,800);
		  }
		  preg_match('/\d+/i',$key,$matches);
		  $pics['title'][] = $_POST['title'.$matches[0]];
		  $pics['size'][] = filesize($path.$file['savename']);
		  $pics['filepath'][] = $filepath;
		  $pics['click'][] = rand(0,10);
		  $pics['savename'][] = $file['savename'];
		  //$pics['title'][] = 'title'.$matches[0];
		  //匹配出数字和title对应
		}
	  }
	}
	//dump($pics);exit;
	if(count($pics['filepath'])>0){
	  $model = D('goods');
	  $vdata['id'] = $_POST['id'];
	  $vo = $model->field('id,member_id')->where($vdata)->find();
	  $member_id = $vo['member_id'];
	}
	$model = D('Pic');
	for($i=0;$i<count($pics['filepath']);$i++){
	  $pdata['title'] = $pics['title'][$i];
	  $pdata['filepath'] = $pics['filepath'][$i];
	  $pdata['savename'] = $pics['savename'][$i];
	  $pdata['is_thumb'] = $is_thumb;
	  $pdata['click'] = $pics['click'][$i];
	  $pdata['size'] = $pics['size'][$i];
	  $pdata['domain'] = C('IMG_URL');
	  $pdata['source'] = CONTROLLER_NAME;
	  $pdata['sourceid'] = $_POST['id'];
	  $pdata['sort'] = $i+1;
	  $pdata['member_id'] = $member_id;
	  //$pdata['webset'] = $vo['webset'];
	  $pdata['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
	  $pdata['createname'] = $_SESSION['nickname'];
	  $pdata['create_time'] = time();
	  $picid = $model->add($pdata);
	}
	if($_POST['select_logo']){
	  $model = D('goods');
	  $data['id'] = $_POST['id'];
	  $sdata['lit_pic'] = $_POST['select_logo'];
	  $result = $model->where($data)->save($sdata);	
	}
	if($result || $picid){
	  $this->history($_POST['id']);
	  $this->success ('修改成功!');
	}else{
	  $this->error ('修改失败!');
	}
  }

  /**
   * ajax删除图片
   */
  public function ajax_delpic(){
	$model = D('Pic');
	$data['source'] = CONTROLLER_NAME;
	$data['picid'] = $_POST['pid'];
	$vo = $model->field('domain,filepath,savename,is_thumb')->where($data)->find();
	//dump($vo);exit;
    //include "../../Ftp.php";
	//是否开启FTP删除
	if(false){
	  import ( '@.ORG.Ftp' );
	  $ftphost = $_SCONFIG['ftphost'];
	  $ftpport = $_SCONFIG['ftpport'];
	  $ftpuser = $_SCONFIG['ftpuser'];
	  $ftppassword = $_SCONFIG['ftppassword'];
	  $ftp = new ftp($ftphost,$ftpport,$ftpuser,$ftppassword);// 打开FTP连接
	  $dir = $_FTP[$vo['domain']].'/'.$vo['filepath'];        //删除远程文件
	  $ftp->del_file($dir);	
	}else{
	  //$dir = $_NFTP[$vo['domain']].'/'.$vo['filepath'];
	  $dir = C('IMG_ROOT').$vo['filepath'].$vo['savename'];
	  //echo C('IMG_ROOT').$vo['filepath'].$vo['savename'];exit;
	  unlink($dir);
	  if($vo['is_thumb']){
	    $dir = C('IMG_ROOT').$vo['filepath'].'thumb_'.$vo['savename'];
	    unlink($dir);
	    $dir = C('IMG_ROOT').$vo['filepath'].'thumb2_'.$vo['savename'];
	    unlink($dir);
	  }
	}
	$url= $vo['domain'].$vo['filepath'];
	$result = $model->where($data)->delete();
	//dump($result);exit;
	/*
	if(!$fp=@fopen($url,"r")){
	  //echo iconv("GBK", "UTF-8", "远程文件不存在！");
	}else{
	  echo "文件删除失败";
	  exit;
	}
	*/
	if($result){
	  $this->history($_POST['pid'],'deletepic');
	  echo "1";
	}else{
	  echo "文件删除失败";
	  exit;
	}
	exit;
  }

  /**
   * 排序页面
   */
  public function sort(){
	$name = CONTROLLER_NAME;
    $amodel = D('flag_list');
	$flags = $this->get_moudel_flags();
	$this->assign('flags',$flags);
	if($_GET['fid']){
	  $this->assign('fid',$_GET['fid']);
	  $data1['fid'] = $data2['fid'] = $_GET['fid'];
	  $data1['source'] = $data2['a.source'] = $name;
	  if($_GET['toptid']){
		$this->assign('toptid',$_GET['toptid']);
		$data1['toptid']=$_GET['toptid'];
	  }
	  $count = $amodel->where($data1)->count();
	  if($count>0){
	    //创建分页对象
	    $listRows = '20';
	    $p = new \My\Page($count,$listRows);
		$pageno = $_GET['p'] ? $_GET['p'] : 1;
		$offset = ($pageno-1)*$page_size;
	    $list = $amodel->table('`'.C('DB_PREFIX').'flag_list` as a')->join('`'.C('DB_PREFIX').strtolower($name).'` as b on a.sourceid=b.id')->field('b.id,b.name,sort,b.create_time')->where($data2)->order('sort asc,sourceid desc')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $amodel->getlastsql();exit;
		$page = $p->Show();
		$this->assign('page',$page);
		$this->assign('list',$list);
	  }
	}
    $this->display();
  }

  /**
   * 编辑属性
   */
  function add_attr(){
	//产品信息
    $model = M('Gift');
	$data['id'] = $_GET['id'];
	$vo = $model->field('id,lit_pic,goods_type')->where($data)->find();
	$this->assign('vo',$vo);
	//产品属性
	$ga_model = M('gift_attr');
	$ga_data['goods_id'] = $_GET['id'];
	$goods_attrs = $ga_model->where($ga_data)->select();
	//组装属性数组
	foreach($goods_attrs as $attrs){
	  $key = $attrs['attr_id'];
	  $attr_vals[$key][] = $attrs;
	}
	$gt_model = M('product_type');
	$gt_data['status'] = 1;
	$goods_types = $gt_model->where($gt_data)->select();
	$this->assign('goods_types',$goods_types);
	//读取属性
	if($vo['goods_type']){
	  $cat_id = $vo['goods_type'];
	}else{
		foreach($goods_types as $type){
		  $cat_id = $type['id'];
		  if($type['is_select']==1){
			$cat_id = $type['id'];
			break;
		  }
		}	
	}
	$this->assign('cat_id',$cat_id);
	//$this->attr_assign($cat_id,$attr_vals);
    $attrs = get_attr_assign($cat_id,$attr_vals);
	$this->assign('attrs',$attrs);
    $this->display();
  }

  /**
   * 获取属性
   */
  function get_attr(){
	//产品信息
    $model = M('gift');
	$data['id'] = $_POST['goods_id'];
	$vo = $model->field('id,lit_pic,goods_type')->where($data)->find();
	//产品属性
	$ga_model = M('goods_attr');
	$ga_data['goods_id'] = $_POST['goods_id'];
	$goods_attrs = $ga_model->where($ga_data)->select();
	//组装属性数组
	foreach($goods_attrs as $attrs){
	  $key = $attrs['attr_id'];
	  $attr_vals[$key][] = $attrs;
	}
	$cat_id = $_POST['tid'];
	//$this->attr_assign($cat_id,$attr_vals);
    $attrs = get_attr_assign($cat_id,$attr_vals);
	$this->assign('attrs',$attrs);
    $this->display();
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
  function attr_assign($cat_id,$attr_vals){
    $attrs = get_attr_assign();
	$this->assign('attrs',$attrs);
  }

  /**
   * 添加属性
   */
  function insert_attr(){
	$model = M('goods');
	$goods_data['id'] = $_POST['goods_id'];
	$vo = $model->field('id,goods_type')->where($goods_data)->find();
	if($vo['goods_type']!=$_POST['tid']){
	  $goods_sdata['goods_type'] = $_POST['tid'];
	  $model->where($goods_data)->save($goods_sdata);
	  if($vo['goods_type']>0){
	    $model = M('goods_attr');
	    $ga_data['goods_id'] = $_POST['goods_id'];
	    $model->where($ga_data)->delete();
		$model->query('OPTIMIZE TABLE  '.C('DB_PREFIX').'`_goods_attr`');
	  }
	}
	$model = M('goods_attr');
    foreach($_POST['attr_value_list'] as $key=>$value_list){
	  if($value_list==''){
	    continue;
	  }
	  unset($add_data);
	  unset($save_data);
	  $add_data['goods_id'] = $_POST['goods_id'];
	  if(is_array($value_list)){
	    foreach($value_list as $k=>$val){
		  if($val==''){
			continue;
		  }
		  $add_data['attr_id'] = $key;
		  $add_data['attr_value'] = $val;
		  $count = $model->where($add_data)->count();
		  if($count>0){
		    $save_data['attr_price'] = $_POST['attr_price_list'][$key][$k];
	        $model->where($add_data)->save($save_data);		  
		  }else{
		    $add_data['attr_price'] = $_POST['attr_price_list'][$key][$k];
	        $model->add($add_data);			  
		  }
		}
	  }else{
		$add_data['attr_id'] = $key;
		//$add_data['attr_value'] = $value_list;
		$count = $model->where($add_data)->count();
		if($count>0){
		    $save_data['attr_value'] = $value_list;
	        $model->where($add_data)->save($save_data);		  
		}else{
		    $add_data['attr_value'] = $value_list;
	        $model->add($add_data);			  
		}
	    //$model->add($add_data);
	  }
	}
	$this->success ('修改完成!');
  }

  /**
   * 商品列表
   */
  function product_list(){
    $model = M('goods');
	$data['id'] = $_GET['goods_id'];
	$vo = $model->field('id,goods_sn, name, goods_type, price')->where($data)->find();
	$this->assign('vo',$vo);
    $attr_data['goods_id'] = $_GET['goods_id'];
	$attr_data['attr_type'] = 1;
    $attribute = $model->table('`zy_goods_attr` as a')->join('`zy_attribute` as b on a.attr_id=b.id')->field('a.*,b.attr_name')->where($attr_data)->order('b.id asc')->select();
	//组装所有属性
    foreach ($attribute as $attribute_value){
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
	    $_attribute[$attribute_value['attr_id']]['goods_attr_id'][] = $attribute_value['id'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
	//dump($_attribute);//exit;
    $attribute_count = count($_attribute);
	$this->assign('attribute',$_attribute);
	$model = M('products');
	$p_data['source'] = MODULE_NAME;
	$p_data['goods_id'] = $_GET['goods_id'];
	$count = $model->where($p_data)->count();
	if ($count > 0) {
	  $listRows = '20';
	  $p = new \My\Page ( $count, $listRows );
	  $list = $model->where($p_data)->order('product_id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		//产品属性
		$ga_model = M('goods_attr');
		$ga_data['goods_id'] = $_GET['goods_id'];
		$results = $ga_model->where($ga_data)->select();
		//组装属性数组
		foreach($results as $attrs){
		  $key = $attrs['id'];
		  $goods_attr[$key] = $attrs;
		}
	  foreach ($list as $key => $value){
			$_goods_attr_array = explode('|', $value['goods_attr']);
			if (is_array($_goods_attr_array))
			{
				$_temp = '';
				
				foreach ($_goods_attr_array as $_goods_attr_value){
					 $_temp[] = $goods_attr[$_goods_attr_value];
				}
				$list[$key]['goods_attr'] = $_temp;
			}
	  }
	  $this->assign ( 'list', $list );
	  $this->assign ( "page", $page );
	}
	$this->display();
  }

  /**
   * 添加修改
   */
  function product_add(){
    $model = M('products');
	foreach($_POST['product_number'] as $key=>$product_number){
	  unset($add_data);
	  $add_data['source'] = MODULE_NAME;
	  $add_data['goods_id'] = $_POST['goods_id'];
	  $count_data['source'] = MODULE_NAME;
	  $count_data['goods_id'] = $_POST['goods_id'];
	  $add_data['product_number'] = $product_number ? $product_number : 1;
	  $add_data['product_sn'] = $_POST['product_sn'][$key];
	  if($add_data['product_sn']==''){
	    continue;
	  }
	  foreach($_POST['attr'] as $attr){
	    $add_data['goods_attr'][] = $attr[$key];
	  }
	  sort($add_data['goods_attr']);
	  $add_data['goods_attr'] = implode('|',$add_data['goods_attr']);
	  $count_data['goods_attr'] = $add_data['goods_attr'];
	  $count = $model->where($count_data)->count();
	  if($count>0){
	    $result = $model->where($count_data)->save($add_data);
	  }else{
	    $result = $model->add($add_data);
	  }
	}
	if($result){
	  $this->check_stock($_POST['goods_id']);
	  $this->success ('编辑成功!');
	}else{
	  $this->error ('编辑失败!');
	}
  }

  /**
   * 删除产品
   */
  function product_del(){
    $model = M('products');
    $data['product_id'] = $_POST['product_id'];
	$result = $model->where($data)->delete();
	if($result){
	  $msg['status'] = 1;
	  $msg['notice'] = '删除成功';
	}else{
	  $msg['status'] = 0;
	  $msg['notice'] = '删除失败';	
	}
	$this->check_stock($_POST['goods_id']);
	echo json_encode($msg);exit;
  }

}
?>