<?php
namespace Home\Controller;
use Think\Controller;
class AttachmentController extends CommonController{

    protected function _upload_init($upload) {
        $file_type = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        $allow_exts_conf = explode(',', C('ftx_attr_allow_exts')); // 读取配置
        $allow_max = C('ftx_attr_allow_size'); // 读取配置
        //和总配置取交集
        $allow_exts = array_intersect($ext_arr[$file_type], $allow_exts_conf);
        $allow_max && $upload->maxSize = $allow_max * 1024;   //文件大小限制
        $allow_exts && $upload->allowExts = $allow_exts;  //文件类型限制
        //$upload->savePath =  './data/upload/editer/' . $file_type . '/';
		$upload->savePath =  C('IMG_ROOT');
        $upload->saveRule = 'uniqid';
        $upload->autoSub = true;
        $upload->subType = 'date';
        $upload->dateFormat = 'Y/m/d/';
        return $upload;
    }
    
    /**
     * 编辑器上传
     */
    public function editer_upload() {
        $file_type = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        //$result = $this->_upload($_FILES['imgFile']);
		$result = $this->upload();
		if($result){
			$result['error'] = 0;
		}else{
			$result['error'] = 8002;
		}
        if ($result['error']) {
            echo json_encode(array('error'=>1, 'message'=>'上传失败'));
        } else {
            echo json_encode(array('error'=>0, 'url'=> $result['url']['imgFile']));
        }
        exit;
    }
    
    /**
     * 编辑器图片空间 
     */
    public function editer_manager() {
		echo '不开发';exit;
        $root_path = './data/upload/editer/';
        $root_url = './data/upload/editer/';
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        if ($dir_name !== '' && $dir_name != 'file') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if (!file_exists($root_path)) {
				mkdir($root_path);
            }
        }
        
        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        echo realpath($root_path);
        //排序形式，name or size or type
        $this->_order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
		if ($filename{0} == '.') continue;
		$file = $current_path . $filename;
		if (is_dir($file)) {
			$file_list[$i]['is_dir'] = true; //是否文件夹
			$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
			$file_list[$i]['filesize'] = 0; //文件大小
			$file_list[$i]['is_photo'] = false; //是否图片
			$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
		} else {
			$file_list[$i]['is_dir'] = false;
			$file_list[$i]['has_file'] = false;
			$file_list[$i]['filesize'] = filesize($file);
			$file_list[$i]['dir_path'] = '';
			$file_ext = strtolower(array_pop(explode('.', trim($file))));
			$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
			$file_list[$i]['filetype'] = $file_ext;
		}
		$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
		$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
		$i++;
            }
            closedir($handle);
        }
        usort($file_list, array($this, '_cmp_func'));
        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
        exit;
    }
    
    /**
     * 排序
     */
    private function _cmp_func($a, $b) {
        if ($a['is_dir'] && !$b['is_dir']) {
            return -1;
        } else if (!$a['is_dir'] && $b['is_dir']) {
            return 1;
        } else {
            if ($this->_order == 'size') {
                if ($a['filesize'] > $b['filesize']) {
                    return 1;
                } else if ($a['filesize'] < $b['filesize']) {
                    return -1;
                } else {
                    return 0;
                }
            } else if ($this->_order == 'type') {
                return strcmp($a['filetype'], $b['filetype']);
            } else {
                return strcmp($a['filename'], $b['filename']);
            }
	  }
    }

    /**
     * 上传文件
     */
    protected function _upload($file, $dir = '', $thumb = array(), $save_rule='uniqid') {
		import("@.ORG.UploadFile");
        $upload = new \UploadFile();
        if ($dir) {
            $upload_path = C('IMG_ROOT') . $dir . '/';
            $upload->savePath = $upload_path;
        }
        if ($thumb) {
            $upload->thumb = true;
            $upload->thumbMaxWidth = $thumb['width'];
            $upload->thumbMaxHeight = $thumb['height'];
            $upload->thumbPrefix = '';
            $upload->thumbSuffix = isset($thumb['suffix']) ? $thumb['suffix'] : '_thumb';
            $upload->thumbExt = isset($thumb['ext']) ? $thumb['ext'] : '';
            $upload->thumbRemoveOrigin = isset($thumb['remove_origin']) ? true : false;
        }
        //自定义上传规则
        $upload = $this->_upload_init($upload);
        if( $save_rule!='uniqid' ){
            $upload->saveRule = $save_rule;
        }

        if ($result = $upload->uploadOne($file)) {
            return array('error'=>0, 'info'=>$result);
        } else {
            return array('error'=>1, 'info'=>$upload->getErrorMsg());
        }
    }

    /**
     * 弹出框图片上传
     */
	public function local(){
		if (IS_POST){
			$img = $this->upload(0);
			if($img){
				$return['error_code'] = 0;
				$return['msg'] = $img['url']['photo'];
			}else{
				$return['error_code'] = 8002;		
			}
			echo '<script>location.href="'.__APP__.'?c=Attachment&a=local&error_code='.$return['error_code'].'&msg='.urlencode($return['msg']).'";</script>';
		}else {
			$this->display('local');
		}
	}

    /**
     * ajax多图插件上传
     */
    public function swfupload() {
		$options['thumb'] = true;
		$options['maxSize'] = 1024*1024*2;
		$options['allowExts'] = array('jpg','gif','png','jpeg');
		$options['saveRule'] = 'uniqid';
		$options['savePath'] = C('IMG_ROOT');
		$list = $this->upload($options);
		$info = $list['data'];
		$model = M('attachments');
		foreach($info as $file){
		    $key = $file['key'];
		    $_POST[$key] = C('IMG_URL').$file['savename'];
			$data['user_id'] = $_POST['uid'];
			$data['hash'] = $_POST['hash'];
			$data['type'] = $file['type'];
			$data['title'] = $file['name'];
			$data['remote'] = C('IMG_URL');
			$data['filepath'] = $file['savename'];
			$data['thumb'] = 1;
			$data['create_time'] = time();
			$model->add($data);
			$list['url'] = C('IMG_URL').$file['savename'];
			$list['file_name'] = $file['name'];
			$list['text'] = '<a href="'.C('IMG_URL').$file['savename'].'" target="_blank">'.$up_name.'</a>';
		}
		$this->ajaxReturn($list);exit;
    }

}