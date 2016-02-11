<?php 
namespace Home\Controller;
use Think\Controller;
class ConfigController extends CommonController{

  /**
   * 列表信息
   */
  public function index(){

	  //dump(getCache('list'));exit;
    $model = D('Config');
	$data['status'] = 1;
	$list = $model->where($data)->select();
	foreach($list as $k=>$val){
	  $key = $val['key'];
	  $config[$key] = $val['val'];
	}
	$this->assign('config',$config);
	//快递公司
	$model = M('Shipping');
	$shippings = $model->where($data)->select();
	$this->assign('shippings',$shippings);
	$this->display();
  }

  /**
   * 保存信息
   */
  public function edit(){
	$model = D('config');
    foreach($_POST as $key=>$val){
	  //$sql = "INSERT INTO `".C('DB_PREFIX')."config` (id,title,value) VALUES ('','".$key."','".$val."') ON DUPLICATE KEY UPDATE value='".$val."'; ";
	  //$model->query($sql);
	  $where['key']=$key;
	  $count = $model->where($where)->count();
	  if($count>0){
	    $value['val']=$val;
	    $info = $model->where($where)->save($value);	  
	  }else{
	    $where['val']=$val;
	    $info = $model->add($where);	  
	  }
	  unset($where);
	}
	$this->history(0);
	$this->giveCache();
	$this->success ('修改成功!');
  }

  /**
   * 生成缓存
   */
  protected function giveCache(){
	  $model = D('Config');
	  $wdata['status'] = 1;
	  $list = $model->where($wdata)->select();
	  foreach($list as $array){
	    $data[$array['key']] = $array['val'];
		$data[$array['key']] = $array['val'];
	  }
	  //mk_dir(C('DATA_CACHE_PATH').'/config/');
	  //F('list',$data,C('DATA_CACHE_PATH').'/config/');
	  setCache('list',$data);
  }

  /**
   * 会员VIP信息
   */
  public function vip_config(){
    if($_POST){
	  $model = D('config');
      foreach($_POST as $key=>$val){
	    $where['key']=$key;
	    $value['val']=$val;
	    $info = $model->where($where)->save($value);
	  }
	  $this->success ('修改成功!');
	  exit;
	}
	$_REQUEST['listRows'] = 100;
	$_REQUEST['_sort'] = 'asc';
	$_REQUEST['status'] = 1;
	$_REQUEST['type'] = 2;
	$map = $this->_search ();
	$model = M('Config');
	$this->_list($model,$map);
	$this->display();
  
  }
} 
?>