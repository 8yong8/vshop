<?php

class ProductAction extends CommonAction{

	public function _before_index(){
		//$_REQUEST ['fileds'] = '*';
	}

	//首页
	function index() {
		$model = M(MODULE_NAME);
		//$_REQUEST['nickname'] = '明';
		$map = $this->_search();
		//排序字段 默认为主键名
		if ($_REQUEST ['_order']) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = 'update_time';
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if ($_REQUEST ['_sort']) {
			$sort = $_REQUEST ['_sort'];
		} else {
			$sort = 'desc';
		}
		if (isset ( $_REQUEST ['fileds'] )) {
			$fileds = $_REQUEST ['fileds'];
		} else {
			$fileds = 'id,name,subtitle,sn,lit_pic,cat_id,market_price,price,stock,sale_num,status';
		}
		$count = $model->where($map)->count();
		$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
		$page_count = ceil($count/$page_size);
		$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
		$offset = ($pageno - 1) * $page_size;
		$voList = $model->field($fileds)->where($map)->order( "`" . $order . "` " . $sort)->limit($offset. ',' . $page_size)->select();
		$list['count'] = $count;
		$list['page_count'] = $page_count;
		$list['data'] = $voList;
		echo  json_encode($list);exit;
	}

	//列表
	public function lists(){
		$model = M(MODULE_NAME);
		$map = $this->_search();
		//排序字段 默认为主键名
		if ($_REQUEST ['_order']) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = 'update_time';
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if ($_REQUEST ['_sort']) {
			$sort = $_REQUEST ['_sort'];
		} else {
			$sort = 'desc';
		}
		$count = $model->where($map)->count();
		$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('default_page_size');
		$page_count = ceil($count/$page_size);
		$pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
		$offset = ($pageno - 1) * $page_size;
		$voList = $model->field("id,name,subtitle,sn,lit_pic,cat_id,market_price,price,stock,sale_num,status")->where($map)->order( "`" . $order . "` " . $sort)->limit($offset. ',' . $page_size)->select();
		$list['count'] = $count;
		$list['page_count'] = $page_count;
		$list['data'] = $voList;
		echo  json_encode($list);exit;  
	}

	public function detail(){
		//session('name','abc');
		//dump(session('name'));
		//产品信息
		$model = M(MODULE_NAME);
		$data['id'] = $_GET['id'];
		$vo = $model->where($data)->find();
		$img_model = M('Pic');
		$img_data['source'] = 'Product';
		$img_data['sourceid'] = $vo['id'];
		//图集
		$imgs = $img_model->field('domain,filepath,savename')->where($img_data)->select();
		foreach($imgs as $key=>$img){
		  $imgs[$key]['url'] = $img['domain'].$img['filepath'].$img['savename'];
		  unset($imgs[$key]['domain']);
		  unset($imgs[$key]['filepath']);
		  unset($imgs[$key]['savename']);
		}
		//$vo['imgs'] = $imgs;
		//产品列表
		$model = M('Product_item');
		$list_data['source'] = 'Product';
		$list_data['product_id'] = $vo['id'];
		$products = $model->where($list_data)->select();
		$specval = array();
		foreach($products as $val){
			$attr_ids = explode(';',$val['attr_ids']);			  //属性分类
			$product_attr_ids = explode(';',$val['product_attr']);//商品属性
			foreach($product_attr_ids as $key=>$product_attr_id){
				$specval[$attr_ids[$key]][$product_attr_id]['ids'][] = $val['id'];
			}
		}

		dump($specval);exit;
		/*
		$all_ids = array();
		$specval = array();
		//组装属性关系网
		foreach($products as $val){
			unset($num);
			$ids = explode(';',$val['product_attr']);
			$attr_ids = explode(';',$val['attr_ids']);
			$product_attr = explode(';',$val['product_attr']);
			$product_attr_value = explode(';',$val['product_attr_value']);
			for($i=0;$i<count($attr_ids);$i++){
				$num[] = $i;
			}
			//dump($num);
			//$attr_name = explode('|',$val['attr_name']);
			for($i=0;$i<count($attr_ids);$i++){
				//echo $i.'<br>';
				unset($num2);
				$k = $product_attr[$i];
				$j = $attr_ids[$i];
				$num2[] = $i;
				//其他属性加入
				$diff = array_diff($num,$num2);
				//dump($diff);
				//if($jj==1){dump($diff);}
				foreach($diff as $n){
				  $z = $attr_ids[$n];
				  if(array_search($product_attr[$n],$specval[$j][$k][$z]['v'])===false || $specval[$j][$k][$z]['v']==''){
					  $specval[$j][$k][$z]['v'][] = $product_attr[$n];
					  $specval[$j][$k][$z]['v2'][] = $product_attr_value[$n];				  
				  }
				  //$attrs2[$j][$k][$z]['v'][] = $product_attr[$n];
				  //$attrs2[$j][$k][$z]['v2'][] = $product_attr_value[$n];
				}
				
			}
			//dump($jiegou);exit;
			$all_ids = array_unique(array_merge($all_ids,$ids));
		}
		*/
		//属性
		//$model = M('Product_attr');
		//$attr_data['attr_input_type'] = 1;//单一属性
		$attr_data['product_id'] = $vo['id'];
		$attr_data['a.id'] = array('in',$all_ids);
		$list = $model->table('`'.C('DB_PREFIX').'product_attr` as a')->join('`'.C('DB_PREFIX').'attribute` as b on a.attr_id=b.id')->field('a.*,b.attr_name')->where($attr_data)->order('b.id asc')->select();
		//dump($list);exit;
		foreach($list as $val){
		  $key = $val['attr_id'];
		  $attrs[$key]['atrr_name'] = $val['attr_name'];
		  $attrs[$key]['attr_id'] = $val['attr_id'];
		  $attr_val['id'] = $val['id'];
		  $attr_val['val'] = $val['attr_value'];
		  $attrs[$key]['val'][] = $attr_val;
		  //$attrs[$key]['ids'][] = $val['id'];
		  //$attrs[$key]['value'][] = $val['attr_value'];
		}
		$this->assign('attrs',$attrs);
		$i = 0;
		foreach($attrs as $attr){
			$attrs_new[$i] = $attr;
			$i++;
		}
		$json_data['data'] = $vo;
		$json_data['products'] = $products;
		//$json_data['attr_ids'] = $attr_ids;
		$json_data['specval'] = $specval;
		$json_data['attrs'] = $attrs_new;
		$json_data['imgs'] = $imgs;
		//dump($attrs_new);exit;
		echo json_encode($json_data);exit;
	}

}
?>