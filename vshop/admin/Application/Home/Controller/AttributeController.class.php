<?php 
namespace Home\Controller;
use Think\Controller;
class AttributeController extends CommonController {

  /**
   * 添加数据
   */
  public function add() {
	  if(IS_POST){
		$name = CONTROLLER_NAME;
		$model = D ($name);
		$_POST['create_time'] = time();
		$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$_POST['user_name'] = $_SESSION['nickname'];
		if($this->checkFileUp()){
			$this->upload();
		}
		if($_POST['value']){
		   $val_str = implode('
',$_POST['value']);
		  $_POST['attr_values'] = $val_str;
		}
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list = $model->add ();
		if ($list!==false) { //保存成功
		    $this->history($list);
			if($_POST['value']){
			  $this->add_attr_val($list,$_POST['value']);
			}
			if(method_exists($this,'_after_add')){
			  $this->_after_add($list);
			}
			if(method_exists($this,'GiveCache')){
			  $this->GiveCache($list);
			}
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}	  
	  }else{
		$this->display ();
	  }
  }

  /**
    +----------------------------------------------------------
	* 添加属性值
    +----------------------------------------------------------
	* @access protected
    +----------------------------------------------------------
	* @param int	$attr_id		属性id
	* @param string $attr_values	属性值
    +----------------------------------------------------------
	*/
  protected function add_attr_val($attr_id,$values){
    $model = M('Attr_val');
    foreach($values as $val){
	  $data['attr_id'] = $attr_id;
	  $data['attr_value'] = $val;
	  $model->add($data);
	}
  }

  /**
   * 列表前置
   */
  function _before_index(){
	 $_REQUEST ['listRows'] = 50;
	 $this->get_cats();
  }

  /**
   * 添加 前置
   */
  function _before_add(){
	if($_POST){
	  $model = M('Product_type');
	  $data['id'] = $_POST['cat_id'];
	  $vo = $model->where($data)->find();
	  $_POST['cat_name'] = $vo['name'];
	}else{
	 $this->get_cats();
	}
  }

  /**
   * 添加 后置
   */
  function _after_add(){
	if($_POST){
		$this->update_num($_POST['cat_id']);
	}
  }

  /**
   * 属性条目更新
   */
  function update_num($id){
	$model = M('Product_type');
	$data['id'] = $id;
	$model2 = M('attribute');
	$data2['cat_id'] = $id;
	$count = $model2->where($data2)->count();
	$sdata['attr_num'] = $count;
	$model->where($data)->save($sdata);  
  }

  /**
   * 分类信息
   */
  function get_cats(){
	 $model = M('Product_type');
	 $data['status'] = 1;
	 $cats = $model->where($data)->select();
	 $this->assign('cats',$cats);
  }

  /**
   * 禁用
   */
  public function forbid() {
	$name=CONTROLLER_NAME;
	$model = D ($name);
	$model1 = D('Flag_module');
	$pk = $model->getPk ();
	$id = $_REQUEST [$pk];
	$condition = array ($pk => array ('in', $id ) );
	$list = $model->forbid ( $condition );
	if ($list!==false) {
		$condition1 = array ('fid' => array ('in', $id ) );
		$model1->forbid ( $condition1 );
		$this->assign ( "jumpUrl", __CONTROLLER__ );
		$this->success ( '状态禁用成功' );
	} else {
		$this->error  (  '状态禁用失败！' );
	}
  }

  /**
   * 启用
   */
  function resume() {
	//恢复指定记录
	$name = CONTROLLER_NAME;
	$model = D ($name);
	$model1 = D('Flag_module');
	$pk = $model->getPk ();
	$id = $_GET [$pk];
	$condition = array ($pk => array ('in', $id ) );
	if (false !== $model->resume ( $condition )) {
		$condition1 = array ('fid' => array ('in', $id ) );
		$model1->resume ( $condition1 );
		$this->assign ( "jumpUrl", __CONTROLLER__ );
		$this->success ( '状态恢复成功！' );
	} else {
		$model->getlastsql();exit;
		$this->error ( '状态恢复失败！' );
	}
  }

  /**
   * 编辑 前置
   */
  function _before_edit(){
	if($_POST){
	  $model = M('Product_type');
	  $data['id'] = $_POST['cat_id'];
	  $vo = $model->where($data)->find();
	  $_POST['cat_name'] = $vo['name'];	
	}else{
	 $this->get_cats();
	}
  }

  /**
   * 编辑信息
   */
  function edit() {
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	if(IS_POST){
	  //dump($_POST);exit;
	  $_POST['update_time'] = time();
	  //dump($model->create ());exit;
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $list = $model->save ();
	  //echo $model->getlastsql();exit;
	  if (false !== $list) {
		$this->history($_POST['id']);
		if($_POST['value']){
		  $this->add_attr_val($_POST['id'],$_POST['value']);
		}
		if($_POST['edit_value']){
		  $this->edit_attr_val($_POST['id'],$_POST['edit_value']);
		}
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	}else{
	  $name = CONTROLLER_NAME;
	  $model = M ( $name );
	  $id = $_REQUEST [$model->getPk ()];
	  $vo = $model->getById ( $id );
	  $this->assign ( 'vo', $vo );
	  //属性
	  $model = M('Attr_val');
	  $data['attr_id'] = $_REQUEST [$model->getPk ()];
	  $list = $model->where($data)->select();
	  $this->assign('list',$list);
	  $this->display();	
	}

  }

  /**
    +----------------------------------------------------------
	* 编辑属性值
    +----------------------------------------------------------
	* @access protected
    +----------------------------------------------------------
	* @param int	$attr_id		属性id
	* @param string $attr_values	属性值
    +----------------------------------------------------------
	*/
  protected function edit_attr_val($attr_id,$values){
    $model = M('Attr_val');
    foreach($values as $id=>$val){
	  $wdata['id'] = $id;
	  $sdata['attr_value'] = $val;
	  $model->where($wdata)->save($sdata);
	  //echo $model->getlastsql();exit;
	}
  }

  /**
   * 删除属性值
   */
  public function ajax_del_val(){
    $model = M('Attr_val');
    $data['id'] = $_POST['id'];
	$result = $model->where($data)->delete();
	if($result){
	  ajaxSucReturn('删除成功！');
	}else{
	  ajaxErrReturn('删除失败！');
	}
  }

  /**
   * 删除
   */
  public function delete() {
	//删除指定记录
	$name = CONTROLLER_NAME;
	$model = M ($name);
	$this->assign('jumpUrl',__APP__.'/'.$name);
	if (! empty ( $model )) {
		$pk = $model->getPk ();
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if (false !== $model->where ( $condition )->delete ()) {
			  $this->success ('删除成功！');
			} else {
			  $this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }


}
?>