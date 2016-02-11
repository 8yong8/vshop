<?php
namespace Home\Controller;
use Think\Controller;
class UploadifyController extends CommonController {

  public function _initialize() {
    //parent::_initialize();//不能有验证,遨游报错
	//$this->checkUser();

  }

  /**
   * ajax上传图片
   */
  public function flash_upload(){
	$this->checkUser();
	$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';		//上传类型
	$num = isset($_GET['num']) ? intval($_GET['num']) : 0;				//上传个数
	$size = isset($_GET['size']) ? intval($_GET['size']) : 0;				//最大size大小
	$frame = isset($_GET['frame']) ? htmlspecialchars($_GET['frame']) : '';		//iframe的ID
	$input = isset($_GET['input']) ? htmlspecialchars($_GET['input']) : '';	//父框架保存图片地址的input的id
	$desc=$type;//类型描述
	$this->assign('type',$type);
	$this->assign('num',$num);
	$this->assign('size',$size);
	$this->assign('frame',$frame);
	$this->assign('input',$input);
	$this->assign('token',$_GET['token']);
	$this->assign('doaction',$_GET['doaction']);
	$this->display('flash_upload');
  }
  
  //Flash图片上传
  public function flash_upload_insert(){
	//dump($_FILES['Filedata']);exit;
    if (!isset($_POST['sessid'])) {
		exit;
	} else {
		session_id($_POST['sessid']);
	}
	$name = uniqid();
	$msg['ok'] = 'yes';
	$path =  C('IMG_ROOT').date('Y').'/'.date('m').'/'.date('d').'/';
	mk_dir($path);
	$array = explode('.',$_FILES['Filedata']['name']);
	$type = array_pop($array);
	$up_name = array_shift($array);
	move_uploaded_file($_FILES['Filedata']['tmp_name'],$path.$name.'.'.$type);
	$msg['file_url'] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$name.'.'.$type;
	$msg['file_name'] = $up_name;
	$msg['text'] = '<a href="'.C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$name.'.'.$type.'" target="_blank">'.$up_name.'</a>';
	/*
	//添加附件信息
	$model = M('attachments');
	$add_data['source'] = $_POST['doaction'];
	$add_data['filename'] = $up_name;
	$add_data['title'] = $up_name;
	$add_data['type'] = $_FILES['Filedata']['type'];
	$add_data['size'] = $_FILES['Filedata']['size'];
	$add_data['filepath'] = C('IMG_URL').date('Y').'/'.date('m').'/'.date('d').'/'.$name.'.'.$type;
	$add_data['hash'] = $_POST['hash'];
	$add_data['size'] = $_FILES['Filedata']['size'];
	$add_data['user_id'] = $_SESSION[C('USER_AUTH_KEY')] ? $_SESSION[C('USER_AUTH_KEY')] : 0;
	*/
	echo json_encode($msg);exit;
  }

  /*
  删除上传的图片
  */
  public function delupload(){
	$img_dir = str_ireplace(C('IMG_URL'),C('IMG_ROOT'),$_POST['filename']);
	unlink($img_dir);
  }


}
?>