<?php

//购物车类
class Cart extends Think {

    //当前购物车名
    public $sessionName;

    //购物车总价格
    public $totalPrice;
    public function __construct($sessionName) {
        $this->sessionName = $sessionName;
    }

    //获取购物车的信息
    public function getCart() {
        $cur_cart_array = $_SESSION[$this->sessionName];
        return $cur_cart_array;
    }

    //获取购物车商品清单
    public function getCartList() {
        $cur_cart_array = $_SESSION[$this->sessionName];
        if ($cur_cart_array != "") {
            $Pro = M("Goods");
            $len = count($cur_cart_array);
            foreach($cur_cart_array as $key=>$val){
                $goodsid = $key;
                $num = $cur_cart_array[$key]["num"];
                $map['id']=$goodsid;
                $field="id,member_id,realname,lit_pic,inventory,price,product_name,subtitle,tid,type_name";
                $list = $Pro->where($map)->field($field)->find();
                $list["num"] = $num > $list['inventory'] ? $list['inventory'] : $num;
                $list["amount"] = $list["num"] * $list["price"];
                $cartList[$key] = $list;
                $totalPrice+=$list["amount"];
            }
            //返回商品总价格
            $this->totalPrice = $totalPrice;
            return $cartList;
        }
    }

    //加入购物车,购物车的商品id和购物车的商品数量
    public function addcart($goods_id, $goods_num) {
        $cur_cart_array = $_SESSION[$this->sessionName];
        if ($cur_cart_array == "") {
            $cart_info[$goods_id]["id"] = $goods_id; //商品id保存到二维数组中
            $cart_info[$goods_id]["num"] = $goods_num; //商品数量保存到二维数组中
            //$cart_info[$goods_id]['colorid']=$colorid;
            $_SESSION[$this->sessionName] = $cart_info;
        } else {
            //返回数组键名倒序取最大
            $len = count($ar_keys);
            //遍历当前的购物车数组
            //遍历每个商品信息数组的0值，如果键值为0且货号相同则购物车该商品已经添加
            $is_exist = $this->isexist($goods_id, $goods_num, $cur_cart_array);
            if ($is_exist == false) {  
                $cur_cart_array[$goods_id]["id"] = $goods_id;
                $cur_cart_array[$goods_id]["num"] = $goods_num;
                $_SESSION[$this->sessionName] = $cur_cart_array;
            } else {
                $arr_exist = explode("/", $is_exist);
                $id = $arr_exist[0];
                $num = $arr_exist[1];
                $colorid=$arr_exist[2];
                $cur_cart_array[$id]["num"] = $num;
                $_SESSION[$this->sessionName] = $cur_cart_array;
            }
        }
    }

    //判断购物车是否存在相同商品
    public function isexist($id, $num, $array) {
        $isexist = false;
        foreach ($array as $key1 => $value) {
            foreach ($value as $key => $arrayid) {
                if ($key == "id" && $arrayid == $id) {
                    $num = $value["num"] + $num;
                    $isexist = $key1 . "/" . $num;
                }
            }
        }
        return $isexist;
    }

    //从购物车删除
    //参数为要删除的goods_id
    public function delcart($goods_array_id) {
        //回复序列化的数组
        $cur_goods_array = $_SESSION[$this->sessionName];
        //删除该商品在数组中的位置
        unset($cur_goods_array[$goods_array_id]);
        $_SESSION[$this->sessionName] = $cur_goods_array;
        //使数组序列化完整的保存到cookie中
    }

    //清空购物车
    public function emptycart() {
       // $_SESSION[$this->sessionName] = "";
            unset($_SESSION[$this->sessionName]);
    }

    //修改购物车货品数量   
    public function update_cart($up_id, $up_num) {
        //回复序列化的数组
        if ($up_num == 0) {
            if(isset($_SESSION[$this->sessionName][$up_id])){
                unset($_SESSION[$this->sessionName][$up_id]);
            }else{
                    $cart_info[$up_id]["id"] = $up_id; //商品id保存到二维数组中
                    $cart_info[$up_id]["num"] = $up_num; //商品数量保存到二维数组中
                    $_SESSION[$this->sessionName] = $cart_info;
            }
        }else{
            if(isset($_SESSION[$this->sessionName][$up_id])){
                $_SESSION[$this->sessionName][$up_id]["num"]=$up_num; 
            }else{
                $cart_info[$up_id]["id"] = $up_id; //商品id保存到二维数组中
                $cart_info[$up_id]["num"] = $up_num; //商品数量保存到二维数组中
                $_SESSION[$this->sessionName] = $cart_info;
            }
        }
    }
    public function add_suit(){
        
    }

}

?>