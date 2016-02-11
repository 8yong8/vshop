<?php 
namespace Home\Controller;
use Think\Controller;
class MemberCardController extends CommonController {

	function _before_index(){
	  $_REQUEST ['listRows'] = 50;
	
	}

  /**
   * 添加信息
   */
  public function add(){
    if(IS_POST){
		$model = M('Member_card');
		import("ORG.Net.UploadFile");
		if($_FILES['text']['name']){
			$upload = new UploadFile();
			$upload->thumbRemoveOrigin=true;
			//$firstLetter=substr($this->token,0,1);
			// 设置附件上传目录
			$firstLetter = get_dir(session('uid'));
			//$upload->savePath =  './uploads/'.$firstLetter.'/'.$this->token.'/';// 设置附件上传目录
			$upload->savePath =  C('IMG_ROOT').$firstLetter.'/'.$this->token.'/'.date('Y').'/'.date('m').'/'.date('d').'/';// 设置附件上传目录
			mk_dir($upload->savePath,0777);
			$upload->upload();
			$info =  $upload->getUploadFileInfo();
			//echo $info[0]['savepath'].$info[0]['savename'];exit;
			$arr_code = $this->daoru($info[0]['savepath'].$info[0]['savename']);
		}else{
		   //$arr_code = explode(',',$_POST['sns']);
		   $arr_code['code'][] = $_POST['code'];
		   $arr_code['psw'][] = $_POST['psw'];
		}
		if(!$arr_code){
		  $this->error('添加失败');
		}
		foreach($arr_code['code'] as $key=>$code){
		  $add_data['code'] = $code;
		  $count = $model->where($add_data)->count();
		  if($count>0){
			 continue;
		    //$this->error($code.'已经导入');
			//exit;
		  }
		  $add_data['psw'] = $arr_code['psw'][$key];
		  $id = $model->add($add_data);
		}
		if ($id) {
			$this->success ( '添加成功！' );
		} else {
			$this->error ( '添加失败！' );
		}
		exit;
	}
	$this->display();
  }

  /**
   * 编辑信息
   */
  function edit() {
	$name = CONTROLLER_NAME;
	$model = D ( $name );
	if($_POST){
	  $model1 = D('Flag_module');
	  $w_data['fid'] = $_POST['id'];
	  $_POST['update_time'] = time();
	  if (false === $model->create ()) {
		$this->error ( $model->getError () );
	  }
	  // 更新数据
	  $list=$model->save ();
	  if (false !== $list) {
		//成功提示
		if($_POST['status']==0){
		  //删除
		  $model1->where($w_data)->delete();
		}else{
		  $s_data['fname'] = $_POST['name'];
		  $model1->where($w_data)->save($s_data);
		}
		//$this->history($_POST['id']);
		//$this->GiveCache();
		$this->success ('编辑成功!');
	  } else {
		//错误提示
		$this->error ('编辑失败!');
	  }
	}else{
	  $name=CONTROLLER_NAME;
	  $model = M ( $name );
	  $id = $_REQUEST [$model->getPk ()];
	  $vo = $model->getById ( $id );
	  $this->assign ( 'vo', $vo );
	  $this->display();	
	}

  }

  /**
   * 删除信息
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
			  $condition1 = array ('fid' => array ('in', explode ( ',', $id ) ) );
			  $model1 = D('flag_module');
			  $model1->where($condition1)->delete();
			  $this->success ('删除成功！');
			} else {
			  $this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
	}
  }

  /**
   * excel导入信息
   */
  function import(){
    if(IS_POST){
		$model = M('Member_card');
		if($_FILES['text']['name']){
			//$file_dir = $this->upload();
			//dump($file_dir);exit;
			//import("ORG.Net.UploadFile");
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->allowExts  = array('xls');
			$upload->thumbRemoveOrigin=true;
			//$firstLetter=substr($this->token,0,1);
			// 设置附件上传目录
			$upload->savePath =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';// 设置附件上传目录
			mk_dir($upload->savePath,0777);
			$upload->upload();
			$info =  $upload->getUploadFileInfo();
			if($info){
			  $arr_code = $this->daoru($info[0]['savepath'].$info[0]['savename']);
			  $_POST['file_dir'] = $info[0]['savepath'].$info[0]['savename'];			
			}else{
			  $this->error($upload->getErrorMsg());
			}
		}else{
		   //$arr_code = explode(',',$_POST['sns']);
		   $arr_code['code'][] = $_POST['code'];
		   $arr_code['psw'][] = $_POST['psw'];
		}
		if(!$arr_code){
		  $this->error('添加失败');
		}
		foreach($arr_code['code'] as $key=>$code){
		  $add_data['code'] = $code;
		  $count = $model->where($add_data)->count();
		  if($count>0){
			 continue;
		  }
		  $add_data['psw'] = $arr_code['psw'][$key];
		  $id = $model->add($add_data);
		}
		if ($id) {
			$this->history($id);
			$this->success ( '添加成功！' );
		} else {
			$this->error ( '添加失败！' );
		}	
	}else{
	  $this->display();
	}
  
  }

  /**
   * 导入excel方法
   */
  function daoru($file_path='./user.xls'){
	/*
	require_once 'Excel/reader.php';
	require_once 'Excel/oleread.inc';
	require_once 'Classes/PHPExcel.php';
	require_once 'Classes/PHPExcel/Reader/Excel2007.php';
	$PHPExcel = new PHPExcel();
	$PHPReader = new PHPExcel_Reader_Excel2007();
	if(!$PHPReader->canRead($file_path)){
		$PHPReader = new PHPExcel_Reader_Excel5();
		if(!$PHPReader->canRead($file_path)){
			$this->error('Excel文件处理错误!');
		}
	}
	$PHPExcel = $PHPReader->load($file_path);
	$currentSheet = $PHPExcel->getSheet(0);
	$currentSheet->ToArray();
	dump($currentSheet->ToArray());exit;
	EXIT;
	*/
    require_once C('PUBLIC_INCLUDE').'Excel/reader.php';
	require_once C('PUBLIC_INCLUDE').'Excel/oleread.inc';
	require_once C('PUBLIC_INCLUDE').'Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP936');
	$data->read($file_path);
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
		//dump($data->sheets[0]['cells'][$i][1]);exit;
		$arr_code['code'][] = iconv("GBK", "UTF-8",$data->sheets[0]['cells'][$i][1]);
		$arr_code['psw'][] = iconv("GBK", "UTF-8",$data->sheets[0]['cells'][$i][2]);
	}
	return $arr_code;
  }



}
?>