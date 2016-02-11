<?php
namespace Home\Controller;
use Think\Controller;
class ProductController extends PublicController{

	public function index(){
		//$_REQUEST ['fileds'] = '*';
		$this->redirect('Product/lists');
	}

  /**
   * 列表页
   */
  function lists() {
	$model = M(CONTROLLER_NAME);
	$map = $this->_search();

	//排序字段
	switch ($_GET['order'])
	{
		case 'sells':
			$order = 'sale_num';
			break;
		case 'new':
			$order = 'create_time';
			break;
		case 'price':
			$order = 'price';
			break;
		case 'weight':
			$order = 'weight';
			break;
		default:
			$order = 'weight desc,id ';
			break;
	}
	$this->assign('order',$order);

	//排序方式
	switch ($_GET['sort'])
	{
		case 'asc':
			$sort = 'asc';
			break;
		case 'desc':
			$sort = 'desc';
			break;
		default:
			if($order=='price'){
			  $sort = 'asc';
			}else{
			  $sort = 'desc';
			}				
			break;
	}
	$this->assign('sort',$sort);
	$count = $model->where($map)->count();
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('page_size');
	//$page_size = 4;
	$page_count = ceil($count/$page_size);
	$pageno = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->field('id,name,subtitle,sn,lit_pic,cat_id,market_price,price,sale_num,status')->where($map)->order( "" . $order . " " . $sort)->limit($offset. ',' . $page_size)->select();
	//echo $model->getlastsql();dump($list);exit;
	//总页数
	$pages = ceil($count/$page_size);
	if($pageno>$pages){
	  $pageno = $pages;
	}
	//上一页
	if($pageno>1){
	  $prev_page = $pageno-1;
	}else{
	  $prev_page = 1;
	}
	//下一页
	if($pageno>=$pages){
	  $next_page = $pages;
	}else{
	  $next_page = $pageno+1;
	}
	$catList = getCache('Product_category:sc_list_top');
	$this->assign('catList',$catList);
	$this->assign('prev_page',$prev_page);
	$this->assign('next_page',$next_page);
	$this->assign('pageno',$pageno);
	$this->assign('list',$list);
	$this->assign('page_count',$page_count);
	$this->assign('self_url',__SELF__);
	$this->assign('headerTitle','Wap首页');
	$this->assign('headerKeywords','Wap首页');
	$this->assign('headerDescription','Wap首页');
	$this->display();
  }

  /**
   * 搜索条件
   */
  public function _search(){
	$data = array();
	if($_GET['cat_id']){
	  $vo = getCache('Product_category:sc_detail_'.$_GET['cat_id']);
	  if($vo['lv']==1){
		$this->assign('cat_id',$_GET['cat_id']);
		$data['top_cid'] = $_GET['cat_id'];
	  }else if($vo['lv']==2){
		//父节点
		$parent = getCache('Product_category:sc_detail_'.$vo['pid']);
		//2级别分类
		$catList2 = getCache('Product_category:sc_list_'.$parent['id']);
		$this->assign('catList2',$catList2);
		//3级别分类
		$catList3 = getCache('Product_category:sc_list_'.$vo['id']);
		$this->assign('catList3',$catList3);
		$this->assign('cat_id',$parent['id']);
		$this->assign('cat_id2',$vo['id']);
		//有子类
		if($catList3){
		  foreach($catList3 as $cat){
			$cids[] = $cat['id'];
		  }
		  $data['cat_id'] = array('in',$cids);
		}else{
		  $data['cat_id'] = $vo['id'];
		}
	  }else if($vo['lv']==3){
		//父节点
		$parent = getCache('Product_category:sc_detail_'.$vo['pid']);
		//2级别分类
		$catList2 = getCache('Product_category:sc_list_'.$parent['pid']);
		$this->assign('catList2',$catList2);
		//3级别分类
		$catList3 = getCache('Product_category:sc_list_'.$parent['id']);
		$this->assign('catList3',$catList3);
		//顶级节点
		$top = getCache('Product_category:sc_detail_'.$parent['pid']);
		$this->assign('cat_id',$top['id']);
		$this->assign('cat_id2',$parent['id']);	
		$this->assign('cat_id3',$vo['id']);
		$data['cat_id'] = $vo['id'];
	  }
	}
	return $data;
  }

  /**
   * 子分类信息
   */
  public function get_cat(){
	  //$model = M('Product_category');
	  //$data['pid'] = $_POST['pid'];
	  $catList = getCache('Product_category:sc_list_'.$_POST['pid']);
	  if(IS_AJAX){
		$msg['cate_list'] = $catList;
	    ajaxSucReturn($msg);
	  }else{
	    return $catList;
	  }
  }

  /**
   * 商品详情
   */
  public function detail(){
	//产品信息
	$options['dir'] = get_dir($_GET['id']);
	$vo = getcache('detail',$options);//产品基本信息
	if(!$vo){
		$model = M(CONTROLLER_NAME);
		$data['id'] = $_GET['id'];
		$vo = $model->where($data)->find();
	}
	if(!$vo || $vo['status']==0){
	  $this->error('产品不存在');
	}
	//图集
	/*
	$img_model = M('Pic');
	$img_data['source'] = 'Product';
	$img_data['sourceid'] = $vo['id'];
	$imgs = $img_model->field('domain,filepath')->where($img_data)->select();
	foreach($imgs as $key=>$img){
	  $imgs[$key]['url'] = $img['domain'].$img['filepath'];
	  unset($imgs[$key]['domain']);
	  unset($imgs[$key]['filepath']);
	}
	*/
	foreach($vo['imgs'] as $key=>$img){
	  $imgs[$key]['url'] = $img['domain'].$img['filepath'];
	}
	$vo['imgs'] = $imgs;
	//产品列表
	$model = M('ProductItem');
	$list_data['source'] = 'Product';
	$list_data['product_id'] = $vo['id'];
	$list_data['stock'] = array('gt',0);
	$products = $model->where($list_data)->select();
	if($products){
		//重新组装sku
		$all_ids = array();
		foreach($products as $key=>$val){
			$key = $val['product_attr'];
			$items[$key]['price'] = $val['price'];
			$items[$key]['count'] = $val['stock'];
			//取得所有商品属性id
			$ids = explode(';',$val['product_attr']);
			$vals = explode(';',$val['product_attr_value']);
			$attr_names = explode(';',$val['attr_name']);
			foreach($ids as $key2=>$id){
			  //if(!in_array($id,$keys[$key]))$keys[$key][] = $id;
			  //属性名称
			  $attrs[$key2]['atrr_name'] = $attr_names[$key2];
			  //属性值组装
			  if(!in_array($id,$keys[$key2])){
				  $keys[$key2][] = $id;
				  $attr_val['id'] = $id;
				  $attr_val['val'] = $vals[$key2];
				  $attrs[$key2]['val'][] = $attr_val;
			  }
			}
			$all_ids = array_unique(array_merge($all_ids,$ids));
			$items[$key]['item_id'] = $val['id'];
			$items[$key]['stock'] = $val['stock'];
			$items[$key]['sn'] = $val['sn'];
			$items[$key]['spec'] = str_ireplace(";", " ",$val['product_attr_value']);
			//$items[$key]['product_id'] = $val['product_id'];
		}
		//attrs组装
		$this->assign('attrs',$attrs);

		//淘宝sku调用
		$keys = json_encode($keys);
		$this->assign('keys',$keys);

		$items = json_encode($items);
		$this->assign('data',$items);
	}
	//用户登录是否已收藏
	if($this->user['id']){
	  $model = M('Collect');
	  $c_data['member_id'] = $this->user['id'];
	  $c_data['source'] = 'Product';
	  $c_data['sourceid'] = $_GET['id'];
	  $favorite = $model->field('id')->where($c_data)->find();
	  $this->assign('favorite',$favorite);
	}
	//有促销
	if($vo['is_prom']==1){
	  $pm_model = M('ProductPmList');
	  $pm_data['status'] = 1;
	  $pm_data['btime'] = array('lt',time());
	  $pm_data['etime'] = array('gt',time());
	  $pm_data['product_id'] = $_GET['id'];
	  $pm_vo = $pm_model->where($pm_data)->find();
	  $pm = unserialize($pm_vo['info']);
	  $this->assign('pm',$pm);
	}
	$this->assign('vo',$vo);
	$this->assign('headerTitle','商品详情-'.$vo['name']);
	$this->assign('headerKeywords','商品详情');
	$this->assign('headerDescription','商品详情');
	$this->display();
  }

  /**
   * 商品图文详情
   */
  public function imageText(){
	//$data['id'] = $_GET['id'];
	//$vo = $model->where($data)->find();
	$options['dir'] = get_dir($_GET['id']);
	$vo = getcache('detail',$options);//产品基本信息
	if(!$vo && $vo['status']==0){
	  $this->error('产品不存在');
	}  
	$this->assign('vo',$vo);
	$this->assign('headerTitle','商品详情-'.$vo['name']);
	$this->assign('headerKeywords','商品详情');
	$this->assign('headerDescription','商品详情');
	$this->display();  
  }

}
?>