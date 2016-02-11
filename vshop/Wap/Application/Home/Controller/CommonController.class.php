<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends BaseController {

  /**
   * 前期执行
   */
  public function _initialize(){
	parent::_initialize();
	if(!$this->user){
		if(IS_AJAX){
			ajaxErrReturn('请先登录',1001);
		}else{
			$this->error('请先登录');
		}
	}
	if(!IS_AJAX){
	  Cookie( '_redirectURL_', __SELF__ );
	}
  }

  /**
   * 列表页
   */
  public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = CONTROLLER_NAME;
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
  }

  /**
    +----------------------------------------------------------
	* 取得操作成功后要返回的URL地址
	* 默认返回当前模块的默认操作
	* 可以在action控制器中重载
    +----------------------------------------------------------
	* @access public
    +----------------------------------------------------------
	* @return string
    +----------------------------------------------------------
	* @throws ThinkExecption
    +----------------------------------------------------------
	*/
  function getReturnUrl() {
	return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
  }

  /**
    +----------------------------------------------------------
	* 根据表单生成查询条件
	* 进行列表过滤
    +----------------------------------------------------------
	* @access protected
    +----------------------------------------------------------
	* @param string $name 数据对象名称
    +----------------------------------------------------------
	* @return HashMap
    +----------------------------------------------------------
	* @throws ThinkExecption
    +----------------------------------------------------------
	*/
  protected function _search($name = '') {
	//生成查询条件
	if (empty ( $name )) {
		$name = CONTROLLER_NAME;
	}
	//$name=$this->getActionName();
	$model = D ( $name );
	$map = array ();
	foreach ( $model->getDbFields () as $key => $val ) {
		if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
			$this->assign($val,$_REQUEST [$val]);
			if($val=='nickname'){
			  $map [$val] = array('like','%'.$_REQUEST [$val].'%');
			}else{
			  $map [$val] = $_REQUEST [$val];
			}
		}
	}
	return $map;
  }

  /**
    +----------------------------------------------------------
	* 根据表单生成查询条件
	* 进行列表过滤
    +----------------------------------------------------------
	* @access protected
    +----------------------------------------------------------
	* @param Model $model 数据对象
	* @param HashMap $map 过滤条件
	* @param string $sortBy 排序
	* @param boolean $asc 是否正序
    +----------------------------------------------------------
	* @return void
    +----------------------------------------------------------
	* @throws ThinkExecption
    +----------------------------------------------------------
	*/
  protected function _list($model, $map, $sortBy = '', $asc = false) {
	//排序字段 默认为主键名
	if (isset ( $_REQUEST ['_order'] )) {
		$order = $_REQUEST ['_order'];
	} else {
		$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
	}
	//排序方式默认按照倒序排列
	//接受 sost参数 0 表示倒序 非0都 表示正序
	if (isset ( $_REQUEST ['_sort'] )) {
		$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
	} else {
		$sort = $asc ? 'asc' : 'desc';
	}
	//取得满足条件的记录数
	$count = $model->where ( $map )->count ();
	if (! empty ( $_REQUEST ['listRows'] )) {
		$listRows = $_REQUEST ['listRows'];
	} else {
		$page_size = C('page_size');
		$listRows = $page_size ? $page_size : 20;
	}
	$page_count = ceil($count/$listRows);
	$this->assign('count',$count);
	$this->assign('page_count',$page_count);
	if ($count > 0) {
		//import ( "@.ORG.Page1" );.
		import("@.ORG.Util.Page");
		//创建分页对象
		$p = new \Page ( $count, $listRows );
		//分页查询数据

		$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
		//分页跳转的时候保证查询条件
		foreach ( $map as $key => $val ) {
			if (! is_array ( $val )) {
				$p->parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		//分页显示
		$page = $p->show ();
		//列表排序显示
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示
		$this->assign ( 'list', $voList );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( "page", $page );
	}
	Cookie( '_currentUrl_', __SELF__ );
	return;
  }


  /**
   * 列表页
   */
  function get_more_msg(){
	$model = M(CONTROLLER_NAME);
	$page_size = $_REQUEST ['page_size'] ? $_REQUEST ['page_size'] : C('page_size');
	$pageno = $_POST['p'] ? $_POST['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->where($data)->order('id desc')->limit($offset.','.$page_size)->select();
	foreach($list as $key=>$val){
	  if($val['content'])$list[$key]['content'] = str_cut(htmlspecialchars_decode($val['content']),45);
	}
	ajaxSucReturn($list);
  }

  /**
   * 上传图片
   */
  protected function upload($options){
	import('@.ORG.UploadFile');
	//导入上传类
	$upload = new \UploadFile();
	//设置上传文件大小
	$upload->maxSize            = 3292200;
	//设置上传文件类型
	$upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');
	//设置附件上传目录
	$upload->savePath           = $options['savePath'] ?  $options['savePath'] : C('IMG_ROOT');
	//设置需要生成缩略图，仅对图像文件有效
	$upload->thumb              = $options['thumb'] ?  $options['thumb'] : C('thumb');

	// 设置引用图片类库包路径
	$upload->imageClassPath     = '@.ORG.Image';
	//设置需要生成缩略图的文件后缀
	$upload->thumbPrefix        = $options['thumbPrefix'] ?  $options['thumbPrefix'] : C('thumbPrefix');  //生产2张缩略图
	//设置缩略图最大宽度
	$upload->thumbMaxWidth      = $options['thumbMaxWidth'] ?  $options['thumbMaxWidth'] : C('thumbMaxWidth');
	//设置缩略图最大高度
	$upload->thumbMaxHeight     = $options['thumbMaxHeight'] ?  $options['thumbMaxHeight'] : C('thumbMaxHeight');
	//设置上传文件规则
	$upload->saveRule           = 'uniqid';
	//删除原图
	$upload->thumbRemoveOrigin  = true;
	if (!$upload->upload()) {
		//捕获上传异常
		$this->error($upload->getErrorMsg());
	} else {
		//取得成功上传的文件信息
		$info = $upload->getUploadFileInfo();
		//dump($info);exit;
		if($info!=""){
		  $result['data'] = $info;
		  foreach($info as $file){
			$key = $file['key'];
			$_POST[$key] = C('IMG_URL').$file['savename'];
			$result['url'][$key] = $_POST[$key];
			$result['info'][$key] = $file;
		  }
		}else{
		  $result = '';
		}
	}

	return $result;
  }

  /**
   * 检查是否有文件上传
   */
  public function checkFileUp(){
	  foreach($_FILES as $key=>$val){
		if($val['name']){
		  return true;
		}
	  }
	  return false;
  }

  /**
   * 历史操作记录
   */
  public function history($id,$action='',$model='',$info=''){
	$model = D('OpLog');
	//$model = M('History');
	$data['model'] = $model ? $model : CONTROLLER_NAME;
	$data['action'] = $action ? $action : ACTION_NAME;
	$data['member_id'] = $this->user['id'];
	$data['member_name'] = $this->user['username'];
	$data['create_time'] = time();
	$data['sourceid'] = $id;
	//记录详细更新内容
	$data['info'] = serialize($_REQUEST);
	$model->add($data);
  }

  /**
   * 空页面
   */
  public function _empty(){
	if(IS_AJAX){
	  $msg['error_code'] = '8001';
	  $msg['notice'] = '访问页面不存在';
	  echo json_encode($msg);
	}else{
	  $this->error('访问页面不存在');
	}
  }

}
?>