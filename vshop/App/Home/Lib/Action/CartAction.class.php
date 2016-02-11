<?php
class CartAction extends CommonAction {

	/*
	public function _initialize(){
		parent::_initialize();
	}
	*/

	//购物车列表
	public function lists(){
        $model = M('Cart');
        $data['a.member_id'] = $this->user['id'];
        $count = $model->where($map)->count();
        $page_size = 50;
        $page_count = ceil($count/$page_size);
        $pageno = $_REQUEST['pageno'] ? $_REQUEST['pageno'] : 1;
        $offset = ($pageno - 1) * $page_size;
        $voList = $model->alias('a')->join('`'.C('DB_PREFIX').'product` as b on a.product_id=b.id')->join('`'.C('DB_PREFIX').'product_item` as c on a.item_id=c.id')->field('a.*,b.name,b.lit_pic,b.price,b.stock,c.product_attr,c.attr_name,c.product_attr_value,c.price as item_price,c.stock as item_stock')->where($data)->order('id desc')->select();
        //dump($voList);exit;
        //echo $model->getlastsql();exit;
        foreach($voList as $key=>$val){
            $spec = '';
            if($val['item_id']){
                $names = explode(';',$val['attr_name']);
                $values = explode(';',$val['product_attr_value']);
                foreach($names as $k=>$name){
                    $spec .= $name.'：'.$values[$k].' ';
                }
                $voList[$key]['spec'] = $spec;
                $price = $voList[$key]['price'] = $val['item_price'];
                $voList[$key]['stock'] = $val['item_stock'];
            }else{
                $price = $val['price'];
            }
            $total_fee += $price*$val['num'];
            unset($voList[$key]['product_attr']);
            unset($voList[$key]['attr_name']);
            unset($voList[$key]['product_attr_value']);
            unset($voList[$key]['item_price']);
            unset($voList[$key]['item_stock']);
        }
		$list['total_fee'] = $total_fee;
		$list['count'] = $count;
		$list['page_count'] = $page_count;
		$list['data'] = $voList;
		//dump($list);
		echo  json_encode($list);exit;
	}


	//添加购物车
    public function add(){
	  if(!$_POST['item_id'] && !$_POST['product_id']){
		//错误提示
		$msg['error_code'] = 8002;
		$msg['notice'] = '商品必须';
		echo json_encode($msg);exit;	  
	  }
	  $model = D('Cart');
	  $data['member_id'] = $this->user['id'];
	  if($_POST['item_id']){
		  $data['item_id'] = $_POST['item_id'];	  
	  }else{
		  $data['product_id'] = $_POST['product_id'];
	  }
	  $vo = $model->where($data)->find();
	  if($vo){
		$sdata['num'] = $vo['num']+$_POST['num'];
		$result = $model->where($data)->save($sdata);
	  }else{
		$_POST['member_id'] = $this->user['id'];
		$_POST['create_time'] = time();
		if (false === $model->create ()) {
			//错误提示
			$msg['error_code'] = 8002;
			$msg['notice'] = $model->getError ();
			echo json_encode($msg);exit;
		}
		//保存当前数据对象
		$result = $model->add ();
	  }
	  if($result){
		$msg['error_code'] = 0;
		$msg['notice'] = '添加成功';
		ajaxSucReturn($msg); 
	  }else{
		ajaxErrReturn('添加失败');	  
	  }
	  exit;
    }

	//修改购物车
	public function update(){
		$model = D('Cart');
		$data['member_id'] = $this->user['id'];
		$data['id'] = $_POST['id'];
		$sdata['num'] = $_POST['num'];
		$result = $model->where($data)->save($sdata);
		if($result){
			$msg['error_code'] = 0;
			$msg['notice'] = '修改成功';
			ajaxSucReturn($msg);  
		}else{
			ajaxErrReturn('修改失败'); 	  
		}	
	}

	//删除购物车
	public function delete(){
		$model = D('Cart');
		$data['member_id'] = $this->user['id'];
		$data['id'] = $_POST['id'];
		$result = $model->where($data)->delete();
		if($result){
			$msg['error_code'] = 0;
			$msg['notice'] = '删除成功';
			ajaxSucReturn($msg);  
		}else{
			ajaxErrReturn('删除失败'); 	  
		}	
	
	}

}
?>