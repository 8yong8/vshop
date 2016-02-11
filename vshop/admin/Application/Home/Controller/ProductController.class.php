<?php
namespace Home\Controller;
use Think\Controller;
class ProductController extends CommonController{

  public function _initialize() {
	parent::_initialize();
	$this->db = D('Product');
	$this->pdb = D('productPm');
	$this->pldb = D('Pm_list');
	$this->ppldb = D('ProductPmList');
  }

  /**
   * 列表页 前置
   */
  public function _before_index(){
    //获取商品类型存在规格的类型
    $specifications = get_goods_type_specifications();
    $this->assign('specifications', $specifications);
	//商品分类
	$model = M('Product_category');
	$data['status'] = 1;
	//$data['_string'] = "find_in_set('商城',channel)";
	$category = $model->where($data)->select();
	$tree = new \My\Tree($category);
	$category = $tree->get_tree('0');
	$this->assign('category',$category);
	//品牌
	$model = M('Brand');
	$b_data['status'] = 1;
	$brands = $model->where($b_data)->select();
	$this->assign('brands',$brands);
  }

  /**
   * 查询条件
   */
  public function _search(){
	$data = array();
	if($_GET['id']!=""){
	  $data['id'] = $_GET['id'];
	  $this->assign("id",$_GET['id']);
	}
	if($_GET['status']!=""){
	  $data['status'] = $_GET['status'];
	  $this->assign("status",$_GET['status']);
	}
	if($_GET['name']!=""){
      $data['name'] = array('like','%'.$_GET['name'].'%');
	  $this->assign("name",$_GET['name']);
	}
	if($_GET['cat_id']!=""){
      $data['cat_id'] = $_GET['cat_id'];
	  $this->assign("cat_id",$_GET['cat_id']);
	}
	if($_GET['jg']!=""){
	  $data['_string'] = '`stock` <=  `warn_number`';
	  $this->assign("jg",$_GET['jg']);
	}
	return $data;
  }

  /**
   * 添加商品 前置
   */
  public function _before_add(){
	if(!IS_POST){
		$gt_model = M(CONTROLLER_NAME.'_type');
		$gt_data['status'] = 1;
		$product_types = $gt_model->where($gt_data)->select();
		$this->assign('product_types',$product_types);
	}
  }

  /**
   * 添加商品
   */
  public function add(){
	if($_POST){
		//dump($_POST);exit;
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('Product_category');
		$tdata['id'] = $_POST['cat_id'];
		$vv = $model->where($tdata)->find();
		$_POST['cat_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['top_cid'] = $vv['id'];
		}else{
		  $_POST['top_cid'] = $vv['id'];
		}
		//品牌处理
		if($_POST['brand_id']){
		  $model = D('Brand');
		  $bdata['id'] = $_POST['brand_id'];
		  $vv = $model->where($bdata)->find();
		  $_POST['brand_name'] = $vv['name'];		  
		}else{
		  $_POST['brand_name'] = '';
		}
		//图片处理处理
		if($this->checkFileUp()){
		  $this->upload();
		}
		$name = CONTROLLER_NAME;
		$model = D ( $name );
		$_POST['create_time'] = time();
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//添加
		$result = $model->add();
		if (false !== $result) {
			if($_POST['product_type']!=-1){
			  session('product_type',$_POST['product_type']);
			}
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
			//更新附件
			$this->update_att($result);
			//图片处理
			$this->update_pic($result);
			//更新缓存
			$this->GiveCache($result);
			//更新记录
			$this->history($result);
			//规格处理
			$_POST['product_id'] = $result;
			if($_POST['product_attr'] && $_POST['product_type']!=-1){
			  //规格添加
			  $this->product_add();
			}else{
			  //无规格添加
			}
			$this->success ('新增成功!');
		}else{
		  $this->error ('编辑失败!');
		}
		exit;
	}else{
		$product_type = session('product_type');
		$this->assign('product_type',$product_type);
		$this->other_msg();
		$this->cats_msg();
		$this->display();
	}
  }

  /**
   * 附件处理
   */
  protected function update_att($id){
	$model = M('Attachments');
	$att_wdata['hash'] = $_POST['hash'];
	$att_sdata['source'] = 'Poduct';
	$att_sdata['sourceid'] = $id;
	$model->where($att_wdata)->save($att_sdata); 
  }

  /**
   * 图片处理
   */
  protected function update_pic($id){
	$model = M('Pic');
	foreach($_POST['imgs'] as $img){
		$data['source'] = CONTROLLER_NAME;
		$data['sourceid'] = $id;
		$data['domain'] = C('IMG_URL');
		$data['filepath'] = str_ireplace(C('IMG_URL'),"",$img);
		$count = $model->where($data)->count();
		if($count==0){
			$img_dir = str_ireplace(C('IMG_URL'),C('IMG_ROOT'),$img);
			$size = filesize($img_dir);
			//$ar = getimagesize($img_dir);
			$data['size'] = $size;
			$data['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
			$model->add($data);
		}
	}
  }

  /**
   * 编辑前处理处理
   */
  public function _before_edit(){
    //获取商品类型存在规格的类型
	if(!IS_POST){
		$gt_model = M(CONTROLLER_NAME.'Type');
		$gt_data['status'] = 1;
		$product_types = $gt_model->where($gt_data)->select();
		$this->assign('product_types',$product_types);
	}
  }
  
  /**
   * 编辑商品
   */
  public function edit(){
    if(IS_POST){
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('ProductCategory');
		$tdata['id'] = $_POST['cat_id'];
		$vv = $model->where($tdata)->find();
		$_POST['cat_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['top_cid'] = $vv['id'];
		}else{
		  $_POST['top_cid'] = $vv['id'];
		}
		//品牌处理
		if($_POST['brand_id']){
		  $model = D('Brand');
		  $bdata['id'] = $_POST['brand_id'];
		  $vv = $model->where($bdata)->find();
		  $_POST['brand_name'] = $vv['name'];		  
		}else{
		  $_POST['brand_name'] = '';
		}
	    //图片处理处理
		if($this->checkFileUp()){
			$this->upload();
		}
		$model = D (CONTROLLER_NAME);
		//$_POST['create_time'] = time();
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$result = $model->save();
		if (false !== $result) {
			//更新附件
			$this->update_att($_POST['id']);
			//图片处理
			$this->update_pic($_POST['id']);
			//成功提示
			$this->history($_POST['id']);
			
			//相关属性表修改
			$amodel = D('FlagList');
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
			  $amodel = D('FlagList');
			  $del_data['source'] = $name;
			  $del_data['sourceid'] = $_POST['id'];
			  $flagslist = $amodel->where($del_data)->delete();
			}
			if($_POST['product_attr'] && $_POST['product_type']!=-1 || $_POST['item_id']){
			  //规格添加
			  $_POST['product_id'] = $_POST['id'];
			  $this->product_add();
			}
			
			$this->GiveCache($_POST['id']);
			$this->success ('编辑成功!');
		}else{
		  $this->error ('编辑失败!');
		}
		exit;
	}else{
	  $model = D (CONTROLLER_NAME);
	  $data['id'] = $_GET['id'];
	  $vo = $model->where($data)->find();
	  if($vo['product_type']>0){
		//规格
		$model = M('ProductItem');
        $p_data['product_id'] = $vo['id'];
		$list = $model->where($p_data)->order('sort asc,product_attr asc')->select();
		$list_json = json_encode($list ? $list : '');
		//产品属性值
		foreach ($list as $key => $value){
		  $_goods_attr_array = explode(';', $value['product_attr_value']);
		  $list[$key]['product_attr'] = $_goods_attr_array;
		}
		$this->assign ('list', $list );
		$this->assign ('list_json', $list_json );
	  }else{
		$this->assign ('list', '' );
		$this->assign ('list_json', '""' );	  
	  }
	  //图片
	  $model = M('Pic');
	  $p_data['source'] = 'Product';
	  $p_data['sourceid'] = $_GET['id'];
	  $pics = $model->where($p_data)->select();
	  $vo['imgs'] = $pics;
	  $this->other_msg();
	  $this->cats_msg($vo['cat_id']);
	  $this->assign('vo',$vo);
	  $this->display();
	}
  }

  /**
   * 生成缓存
   */
  protected function GiveCache($id = 0){
    $ids = $id ? $id : $_REQUEST['id'];
	$data['id'] = array('in',explode(',',$ids));
    $list = $this->db->where($data)->select();
	//echo $this->db->getlastsql();dump($list);exit;
	$pmodel = M('Pic');
	C('DATA_CACHE_TYPE','File');
	foreach($list as $vo){
		$p_data['source'] = 'Product';
		$p_data['sourceid'] = $vo['id'];
		$pics = $pmodel->field('domain,filepath,is_thumb')->where($p_data)->select();
		$options['dir'] = get_dir($vo['id']);
		$options['id'] = $vo['id'];
		$vo['imgs'] = $pics;
		setCache('detail',$vo,0,$options);
		//setCache('detail',$vo,0,$options);//详情
		//setCache('pics',$pics,0,$options);//图片
	}
  } 

  /**
   * 促销
   */
  public function prom_list(){
	if($_GET['prom_type']){
	  $where['prom_type'] = $_GET['prom_type'];
	  $this->assign('prom_type',$_GET['prom_type']);
	}
	if($_GET['prom_id']){
	  $where['prom_id'] = $_GET['prom_id'];
	  $this->assign('prom_id',$_GET['prom_id']);
	}
	if(isset($_GET['_order'])) {
		$order = $_GET['_order'];
	}else {
		$order = !empty($sortBy)? $sortBy: $this->ppldb->getPk();
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if(isset($_GET['_sort'])) {
		$sort = $_GET['_sort']?'asc':'desc';
	}else {
		$sort = $asc?'asc':'desc';
	}
	if(!empty($_GET['listRows'])) {
		$listRows  =  $_GET['listRows'];
	}else{
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 20;
	}
	$count = $this->pldb->where($where)->count();
	//echo $this->pldb->getlastsql();exit;
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if($count>0){
	  //创建分页对象
	  //$listRows = 1;
	  $p = new \My\Page($count,$listRows);
	  $list = $this->pldb->field('*')->where($where)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->select();
	  foreach($list as $key=>$val){
		$array = unserialize($val['info']);
	    $list[$key]['limt'] = $array['limt'];
		$list[$key]['award_value'] = $array['award_value'];
		$list[$key]['award_type'] = $array['award_type'];
		$list[$key]['pid'] = $array['id'];
		$list[$key]['info'] = $array['info'];
	  }
	  //dump($list);exit;
	  //分页显示
	  $page       = $p->Show();
	}
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	$this->assign('list',       $list);
	$this->assign('sort',       $sort);
	$this->assign('order',      $order);
	$this->assign('sortImg',    $sortImg);
	$this->assign('sortType',   $sortAlt);
	$this->assign("page",       $page);  
    $this->display();
  }

  /**
   * 促销
   */
  public function prom(){
    if(IS_GET){
	  $wdata['id'] = array('in',explode(',',$_GET['id']));
	  $sdata['is_prom'] = $_GET['s'];
	  $result = $this->db->where($wdata)->save($sdata);
	  //echo $this->db->getlastsql();exit;
	  if($result){
		$this->success ('修改成功!');
	  }else{
		$this->error ('修改失败!');
	  }
	}
  }

  /**
   * 分类处理
   */
  public function update_cat($product_id){
    $model = M('Product_list');
    $data['product_id'] = $product_id;
	foreach($_POST['cat_ids'] as $cat_id){
	  $data['cat_id'] = $cat_id;
	  $model->add($data);
	}
  }

  /**
   * 其他信息
   */
  protected function other_msg(){
	//品牌
	$model = M('Brand');
	$b_data['status'] = 1;
	$brands = $model->where($b_data)->select();
	$this->assign('brands',$brands);
	//属性
	$flags = $this->get_moudel_flags();
	$this->assign('flags',$flags);
  }

  /**
   * 分类信息
   */
  protected function cats_msg($cat_id=0){
	$model = M('Product_category');
	//一级目录
	$data['status'] = 1;
	$types = $model->where($data)->select();
	$this->assign('types',$types);
	if($cat_id){
	  $data2['id'] = $cat_id;
	  $cate = $model->where($data2)->find();
	  $list = $this->get_category($cate['pid']);
	  foreach($list as $key=>$val){
	    if($cat_id==$val['id']){
		  $list[$key]['hover'] = 1;
		}
	  }
	  $lv = $cate['lv'];
	  $type_name = 'types'.$lv;
	  $$type_name = $list;
	  //本级目录
	  $this->assign($type_name,$$type_name);

	  if($lv!=2){
	     $data2['id'] = $cate['pid'];
	     $cate = $model->where($data2)->find();
		 $lv = $cate['lv'];
		 $type_name = 'types'.$lv;
		 $list = $this->get_category($cate['pid']);
		 foreach($list as $key=>$val){
			if($cate['id']==$val['id']){
			  $list[$key]['hover'] = 1;
			}
		 }
		 $$type_name = $list;
	    //上级目录
	    $this->assign($type_name,$$type_name);
	  }
	}
  }

  /**
   * 分类更新
   */
  function cat_update(){
    $model = M('Product_category');
	for($i=1;$i<4;$i++){
	  $data['lv'] = $i;
	  $list = $model->field('id,pid,lv')->where($data)->select();
	  foreach($list as $val){
	    $wdata['pid'] = $val['id'];
		$sdata['lv'] = $val['lv']+1;
		$model->where($wdata)->save($sdata);
	  }
	}
  
  }

  /**
   * 获取分类列表
   */
  function get_category($pid){
	$model = M('Product_category');
	$data['status'] = 1;
	$data['pid'] = $pid;
	$types = $model->where($data)->select();
	if(!$types){
	  unset($data);
	  $data['id'] = $_POST['pid'];
	  $types = $model->where($data)->select();
	}
	return $types;
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
	$model = D('Product');
	$pdata['id'] = $_GET['sourceid'];
	$vo = $model->field('id,cat_name,name,lit_pic')->where($pdata)->find();
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
	  import("@.ORG.UploadFile");
	  $upload = new UploadFile();
	  $is_thumb = 1;
	  $upload->thumb = $is_thumb;
	  $upload->thumbPrefix = '48_,400_';
	  $upload->thumbMaxWidth = '48,400,640';
	  $upload->thumbMaxHeight = '48,400,640';
	  //设置上传文件大小
	  $upload->maxSize  = 1024*1024*2 ;
	  //设置上传文件类型
	  $upload->allowExts  = array('jpg','gif','png','jpeg');
	  $upload->saveRule = 'uniqid';
	  $path = $upload->savePath =  C('IMG_ROOT');
	  //$filepath = date('Y').'/'.date('m').'/'.date('d').'/';
	  mk_dir($upload->savePath);
	  $upload->upload();
	  $info = $upload->getUploadFileInfo();
	  if($info!=""){
		foreach($info as $file){
		  $key = $file['key'];
		  if($key=='up_zp'.$_POST['f_logo']){
			$_POST['logo'] = C('IMG_URL').$file['savename'];
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
	  $model = M('Product');
	  $vdata['id'] = $_POST['id'];
	  $vo = $model->field('id,seller_id')->where($vdata)->find();
	  $member_id = $vo['seller_id'];
	}
	$model = D('Pic');
	for($i=0;$i<count($pics['filepath']);$i++){
	  $pdata['title'] = $pics['title'][$i];
	  $pdata['filepath'] = $pics['savename'][$i];
	  //$pdata['savename'] = $pics['savename'][$i];
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
	  //echo $model->getlastsql();exit;
	}
	if($_POST['select_logo']){
		$name = CONTROLLER_NAME;
		$model = D ( $name );
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
	  $msg['error_code'] = 0;
	  $msg['notice'] = '删除成功';
	  echo json_encode($msg);exit;
	}else{
	  $msg['error_code'] = 8002;
	  $msg['notice'] = '删除失败';
	  echo json_encode($msg);exit;
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
   * 添加属性
   */
  function add_attr(){
	//产品信息
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	$data['id'] = $_GET['id'];
	$vo = $model->field('id,lit_pic,product_type')->where($data)->find();
	$this->assign('vo',$vo);
	//产品属性
	$pa_model = M($name.'_attr');
	$pa_data['product_id'] = $_GET['id'];
	$product_attrs = $pa_model->where($pa_data)->select();
	//echo $pa_model->getlastsql();exit;
	//dump($product_attrs);
	//组装属性数组
	if($product_attrs){
		foreach($product_attrs as $attrs){
		  $key = $attrs['attr_id'];
		  $attr_vals[$key][] = $attrs;
		}
	}
	$gt_model = M($name.'_type');
	$gt_data['status'] = 1;
	$product_types = $gt_model->where($gt_data)->select();
	$this->assign('product_types',$product_types);
	//读取属性
	if($vo['product_type']){
	  $cat_id = $vo['product_type'];
	}else{
		foreach($product_types as $type){
		  $cat_id = $type['id'];
		  if($type['default']==1){
			$cat_id = $type['id'];
			break;
		  }
		}	
	}
	$this->assign('cat_id',$cat_id);
    $attrs = get_attr_assign($cat_id,$attr_vals);
	$this->assign('attrs',$attrs);
    $this->display();
  }

  /**
   * 获得属性值
   */
  function get_attr_val(){
	$model = M('Attribute');
	$data['cat_id'] = $_POST['id'];
	$data['attr_type'] = 1;
	$Attrs = $model->field('id,attr_name')->where($data)->select();
	echo json_encode($Attrs);
  }

  /**
   * 获得商品属性
   */
  function get_attr(){
	//产品信息
    $model = M('Product');
	$data['id'] = $_POST['product_id'];
	$vo = $model->field('id,lit_pic,product_type')->where($data)->find();
	//产品属性
	$ga_model = M('Product_attr');
	$ga_data['product_id'] = $_POST['product_id'];
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
   * 获得商品属性
   */
  function get_product_attr(){
	//产品信息
    $model = M('Product');
	$data['id'] = $_POST['product_id'];
	$vo = $model->field('id,lit_pic,product_type')->where($data)->find();
	//产品属性
	$ga_model = M('Product_attr');
	$ga_data['product_id'] = $_POST['product_id'];
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
   * 添加商品属性
   */
  function insert_attr(){
	//dump($_POST);exit;
	$model = M('Product');
	$goods_data['id'] = $_POST['product_id'];
	$vo = $model->field('id,product_type')->where($goods_data)->find();
	if($vo['product_type']!=$_POST['tid']){
	  $goods_sdata['product_type'] = $_POST['tid'];
	  $model->where($goods_data)->save($goods_sdata);
	  if($vo['product_type']>0){
	    $model = M('Product_attr');
	    $ga_data['product_id'] = $_POST['product_id'];
	    $model->where($ga_data)->delete();
		$model->query('OPTIMIZE TABLE  '.C('DB_PREFIX').'`_product_attr`');
	  }
	}
	$model = M('Product_attr');
    foreach($_POST['attr_value_list'] as $key=>$value_list){
	  if($value_list==''){
	    continue;
	  }
	  unset($add_data);
	  unset($save_data);
	  if(is_array($value_list)){
	    foreach($value_list as $k=>$val){
		  if($val==''){
			continue;
		  }
		  unset($add_data);
		  $add_data['product_id'] = $_POST['product_id'];
		  $add_data['attr_id'] = $key;
		  $add_data['attr_value'] = $val;
		  $count = $model->where($add_data)->count();
		  if($count>0){
		    $save_data['attr_price'] = $_POST['attr_price_list'][$key][$k];
			$save_data['attr_pic'] = $_POST['attr_pic_list'][$key][$k];
			$add_data['attr_pic'] = $_POST['attr_pic_list'][$key][$k] ? $_POST['attr_pic_list'][$key][$k] : '';
	        $model->where($add_data)->save($save_data);		  
		  }else{
		    //echo $model->getlastsql();exit;
		    $add_data['attr_price'] = $_POST['attr_price_list'][$key][$k];
			$add_data['attr_pic'] = $_POST['attr_pic_list'][$key][$k] ? $_POST['attr_pic_list'][$key][$k] : '';
	        $model->add($add_data);			  
		  }
		  //echo $model->getlastsql();echo '<br/>';
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
   * 删除商品属性
   */
  function product_attr_del(){
    $model = D('Product_attr');
	$data['id'] = $_POST['id'];
	$result = $model->where($data)->delete();
	if($result){
	  ajaxSucReturn('删除成功！');
	}else{
	  ajaxErrReturn('删除失败！');
	}
  }

  /**
   * 商品列表
   */
  function goods_list(){
    $model = M('Product');
	$data['id'] = $_GET['id'];
	$vo = $model->field('id,sn, name, product_type, price')->where($data)->find();
	$this->assign('vo',$vo);
    $attr_data['product_id'] = $_GET['id'];
	$attr_data['attr_type'] = 1;
    $attribute = $model->table('`'.C('DB_PREFIX').'product_attr` as a')->join('`'.C('DB_PREFIX').'attribute` as b on a.attr_id=b.id')->field('a.*,b.attr_name')->where($attr_data)->order('b.id asc')->select();
	//组装所有属性
    foreach ($attribute as $attribute_value){
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
	    $_attribute[$attribute_value['attr_id']]['product_attr_id'][] = $attribute_value['id'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
    $attribute_count = count($_attribute);
	$this->assign('attribute',$_attribute);
	//dump($_attribute);exit;
	$this->assign('attribute_count',$attribute_count);
	$model = M('Product_item');
	$p_data['source'] = CONTROLLER_NAME;
	$p_data['product_id'] = $_GET['id'];
	$count = $model->where($p_data)->count();
	$listRows = '20';
	$p = new \My\Page ( $count, $listRows );
	$list = $model->where($p_data)->order('sort asc,product_attr asc')->limit($p->firstRow . ',' . $p->listRows)->select();
	$list_json = json_encode($list ? $list : '');
	//产品属性
	$ga_model = M('Product_attr');
	$ga_data['product_id'] = $_GET['id'];
	$results = $ga_model->where($ga_data)->select();
	//dump($results);exit;
	//组装属性数组
	foreach($results as $attrs){
	  $key = $attrs['id'];
	  $goods_attr[$key] = $attrs;
	}
	foreach ($list as $key => $value){
	  $_goods_attr_array = explode(';', $value['product_attr']);
	  if (is_array($_goods_attr_array)){
		$_temp = '';
		foreach ($_goods_attr_array as $_goods_attr_value){
			 $_temp[] = $goods_attr[$_goods_attr_value];
		}
		$list[$key]['product_attr'] = $_temp;
	  }
	}
	$this->assign ('list', $list );
	//dump($list_json);exit;
	$this->assign ('list_json', $list_json );
	$this->assign ("page", $page );
	$mb = $_GET['mb'] ? $_GET['mb'] : 1; 
	$this->display('goods_list'.$mb);
  }

  /**
   * 添加修改
   */
  function product_add(){
    $model = D('Product_item');
	$attr_model = M('Attribute');
	//$pa_model = M('Product_attr');attr_val
	$av_model = M('Attr_val');
	//修改参数
	if($_POST['item_id']){
		foreach($_POST['item_id'] as $item_id){
			$wdata['id'] = $item_id;
			$sdata['barcode'] = $_POST['spec_barcode'][$item_id];
			$sdata['sn'] = $_POST['spec_sn'][$item_id];
			$sdata['stock'] = $_POST['spec_stock'][$item_id];
			$sdata['price'] = $_POST['spec_price'][$item_id];
			$sdata['lit_pic'] = $_POST['spec_pic'][$item_id];
			if($result){
			  $model->where($wdata)->save($sdata);
			}else{
			  $result = $model->where($wdata)->save($sdata);
			}
		}
	}
	
	//添加新数据
	foreach($_POST['spec_sn'][-1] as $key=>$val){
	  $add_data['barcode'] = $_POST['spec_barcode'][-1][$key];
	  $add_data['sn'] = $val;
	  $add_data['stock'] = $_POST['spec_stock'][-1][$key];
	  $add_data['price'] = $_POST['spec_price'][-1][$key];
	  $add_data['lit_pic'] = $_POST['spec_pic'][-1][$key];
	  //dump($product_attrs);exit;
	  //组装属性
	  $product_attr = '';
	  $product_attr_value = '';
	  $attr_ids = '';
	  $attr_name = '';
	  $add_data['spec'] = '';
	  //dump($_POST['product_attr'][-1]);exit;
	  foreach($_POST['product_attr'][-1] as $attr_id=>$attr_val){
		$_attr_name = $attr_val[$key];
		$product_attr_id = $_POST['attr_val_id'][-1][$attr_id][$key];
		$product_attr .= ';'.$product_attr_id;
		$product_attr_value .= ';'.$_attr_name;
		$attr_ids .= ';'.$attr_id;
		$attr_name .= ';'.$_POST['product_attr_name'][-1][$attr_id][$key];
	    $add_data['spec'] .= $_POST['product_attr_name'][-1][$attr_id][$key].' '.$_attr_name.';';
	  }
	  $add_data['product_attr'] = substr($product_attr,1);
	  $add_data['product_attr_value'] = substr($product_attr_value,1);
	  $add_data['attr_ids'] = substr($attr_ids,1);
	  $add_data['attr_name'] = substr($attr_name,1);
	  $add_data['product_id'] = $_POST['product_id'];
	  $add_data['create_time'] = time();
	  //dump($add_data);exit;
	  $result = $model->add($add_data);
	}
	//dump($result);exit;
	if($result){
	  $this->check_stock($_POST['product_id']);
	  //$this->success ('编辑成功!');
	}else{
	  //$this->error ('编辑失败'.$ext);
	}
  }

  /**
   * 添加修改方法2
   */
  function product_add2(){
    $model = D('Product_item');
	if($_POST['pic']){
	  foreach($_POST['pic'] as $id=>$pic_url){
		//值没变化不提交
		if($_POST['pus'][$id]==0){
		  continue;
		}
	    $wdata['id'] = $id;
		$sdata['pic'] = $pic_url;
		$model->where($wdata)->save($sdata);
	  }
	}
	$attr_model = M('Attribute');
	$pa_model = M('Product_attr');
	foreach($_POST['attr'] as $key=>$val){
		$attr_ar[] = $key;
	}
	$attr_data['id'] = array('in',$attr_ar);
	$attrs = $attr_model->field('id,attr_name')->where($attr_data)->select();
	//重新组装属性
	$reset_attrs = array();
	foreach($attrs as $val){
	  $reset_attrs[$val['id']] = $val;
	}
	//echo $attr_model->getlastsql();
	//dump($reset_attrs);exit;
	foreach($_POST['stock'] as $key=>$stock){
	  unset($add_data);
	  $add_data['product_id'] = $_POST['product_id'];
	  $count_data['product_id'] = $_POST['product_id'];
	  $add_data['stock'] = $stock ? $stock : 1;
	  $add_data['sn'] = $_POST['sn'][$key];
	  $add_data['price'] = $_POST['price'][$key];
	  $add_data['pic'] = $_POST['pic2'][$key];
	  if($add_data['sn']==''){
	    continue;
	  }
	  //dump($attr_value);
	  foreach($_POST['attr'] as $attr){
	    $add_data['product_attr'][] = $attr[$key];
		//$add_data['product_attr_value'][] = $attr[$key];
	  }
	  //属性值组装,按product_attr值排序
	  $pa_data['id'] = array('in',$add_data['product_attr']);
	  $order = 'FIELD(`id`, '.implode(',',$add_data['product_attr']).')';
	  $attr_value = $pa_model->where($pa_data)->order($order)->select();
	  foreach($attr_value as $val){
	    $add_data['product_attr_value'][] = $val['attr_value'];//属性值
		$add_data['attr_ids'][] = $reset_attrs[$val['attr_id']]['id'];//属性id
		$add_data['attr_name'][] = $reset_attrs[$val['attr_id']]['attr_name'];//属性名称
	  }
	  //sort($add_data['product_attr']);
	  $add_data['product_attr'] = implode(';',$add_data['product_attr']);
	  $add_data['product_attr_value'] = implode(';',$add_data['product_attr_value']);
	  $add_data['attr_name'] = implode(';',$add_data['attr_name']);
	  $add_data['attr_ids'] = implode(';',$add_data['attr_ids']);
	  $count_data['product_attr'] = $add_data['product_attr'];
	  $count = $model->where($count_data)->count();
	  if($count>0){
	    //$result = $model->where($count_data)->save($add_data);
		$ext = $add_data['product_attr_value'].'已存在!';
		continue;
	  }else{
		$add_data['create_time'] = time();
	    $result = $model->add($add_data);
		//echo $model->getlastsql();exit;
	  }
	}
	if($result){
	  $this->check_stock($_POST['product_id']);
	  $this->success ('编辑成功!');
	}else{
	  $this->error ('编辑失败	'.$ext);
	}
  }

  /**
   * 删除商品
   */
  function product_del(){
	$model = M('Product_item');
	$data['id'] = array('in',explode(',',$_POST['id']));
	$result = $model->where($data)->delete();
	if($result){
	  $msg['error_code'] = 0;
	  $msg['notice'] = '删除成功';
	}else{
	  $msg['error_code'] = 8002;
	  $msg['notice'] = '删除失败';	
	}
	$this->check_stock($_POST['product_id']);
	$this->history($_POST['product_id']);
	echo json_encode($msg);exit;
  }

  /**
   * 字段值修改
   */
  function field_value_update(){
	$model = M('Product_item');
	$data['source'] = CONTROLLER_NAME;
	$data['id'] = $_POST['id'];
	$field = $_POST['field'];
	$vo = $model->where($data)->find();
	$sdata[$field] = $_POST['val'];
	$result = $model->where($data)->save($sdata);    
	if($result){
	  $this->check_stock($vo['id']);
	  $msg['error_code'] = 0;
	  $msg['notice'] = '修改成功';
	}else{
	  $msg['error_code'] = 8002;
	  $msg['notice'] = '修改失败';	
	}
	$this->GiveCache($_POST['id']);
	echo json_encode($msg);exit;   
  }

  /**
   * 库存统计
   */
  protected function check_stock($product_id){
	$model = M('Product_item');
	$data['source'] = CONTROLLER_NAME;
	$data['product_id'] = $product_id;
	$stock = $model->where($data)->sum('stock');
	$model = M('Product');
	$_data['id'] = $product_id;
	$_sdata['stock'] = $stock;
	$model->where($_data)->save($_sdata);
	//更新缓存
	$this->GiveCache($product_id);
  }

  /**
   * 查找规格
   */
  public function search_spec(){
		//所有属性
		$model = M('Attribute');
		$av_model = M('Attr_val');
		$data['cat_id'] = $_GET['product_type'];
		$data['attr_type'] = 1;//单选框
		$data['status'] = 1;
		$list = $model->where($data)->order('id asc')->select();
		foreach($list as $key=>$val){
		  $id = $val['id'];
		  $ids[] = $id;
		  $list2[$id] = $val;
		}
		//查出所有属性值,并组装
		$av_data['attr_id'] = array('in',$ids);
		$vals = $av_model->where($av_data)->select();
		foreach($vals as $val){
		  $id = $val['attr_id'];
		  $list2[$id]['values'][] = $val;
		  $list2[$id]['val_count']++;
		}
		//dump($list2);exit;
		$this->assign('list',$list2);
		$this->display();
  }

  /**
   * 添加规则
   */
  public function add_spec(){
		$model = M('Product_type');
		$data['id'] = $_POST['cat_id'];
		$vo = $model->where($data)->find();
		$model = M('Attribute');
		$_POST['cat_name'] = $vo['name'];
		if($model->create($_POST)){
		   $model->add();
		   $this->update_product_type($_POST['cat_id']);
		   $msg['notice'] = '规则添加成功';
		   ajaxSucReturn($msg);
		}  else {
		   ajaxErrReturn('规则添加失败');
		}
  }

  /**
   * 添加或编辑商品时：新增属性
   */
  public function add_spec_value() {
		$model = M('Attr_val');
		$data       = array();
		$data['attr_id'] = (int)$_POST['spec_id'];
		$new_value  = str_replace('，',',',trim($_POST['new_value']));
		$values  = explode(',',$new_value);
		if (empty($new_value)) ajaxErrReturn('请填写要添加的属性值');
		if ($_POST['spec_id'] < 1) ajaxErrReturn('该规格不存在或规格ID有误');
		foreach($values as $val){
		  $data['attr_value'] = $val;
		  $result = $model->add($data);
		}
		if ($result){ 
		   $msg['notice'] = '规则添加成功';
		   ajaxSucReturn($msg);
		}else{
		  $this->ajaxErrReturn('新增属性失败');
		}
		
	}

  /**
   * 更新规格信息
   */
  public function update_product_type($id){
	$model = M('Attribute');
	$data['cat_id'] = $id;
	$count = $model->where($data)->count();
    $model = M('product_type');
	$wdata['id'] = $id;
	$sdata['attr_num'] = $count;
	$model->where($wdata)->save($sdata);
  }

  /**
   * 获取产品信息
   */
  function get_product(){
	$model = M('Product');
	if(is_numeric($_POST['key'])){
	  $data['id'] = $_POST['key'];
	}else{
      $data['name'] = array('like','%'.$_POST['key'].'%');
	}
	$list = $model->field('id,name,member_name,realname,artist_name')->where($data)->select();
	if(IS_AJAX){
	  echo json_encode($list);
	}else{
	  return $list;
	}
  }

}
?>