<?php 
namespace Home\Controller;
use Think\Controller;
class WxUserController extends CommonController{

  /**
   * 添加 前置
   */
  public function _before_add(){
	if($_POST){
		$_POST['carete_time'] = time();
		$new_token = md5($_SESSION[C('USER_AUTH_KEY')].uniqid());
		$new_token = substr($new_token, 2,15); 
		$_POST['token'] = $new_token;
    }
  }

  /**
   * 自定义菜单列表
   */
  public function diymenu(){
	$model = M('WxCustomMenu');
	$data['token'] = $_GET['token'];
	$_SESSION['token'] = $_GET['token'];
	$data['pid'] = 0;
	$class = $model->where($data)->order('sort desc')->select();
	foreach($class as $key=>$vo){
		$data['pid'] = $vo['id'];
		$c = $model->where($data)->order('sort desc')->select();
		$class[$key]['class']=$c;
	}
	//dump($class);exit;
	$this->assign('class',$class);
	$this->display();
  }

  /**
   * 添加自定义菜单
   */
  public function class_add(){
	$model = D ('WxCustomMenu');
	if($_POST){
		$_POST['token'] = $_SESSION['token'];
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list = $model->add ();
		if ($list!==false) { //保存成功
		    $this->history($list);
		    $this->assign('message','添加成功!');
		    $this->display('Public:success2');exit;
			//$this->success ('新增成功!');
		} else {
			//失败提示
			//$this->error ('新增失败!');
		    $this->assign('error','添加失败!');
		    $this->display('Public:error2');exit;
		}
	}else{
		$class = $model->where(array('token'=>$_SESSION['token'],'pid'=>0))->order('sort desc')->select();
		$this->assign('class',$class);
		$this->display();
	}
  }

  /**
   * 删除自定义菜单
   */
  public function  class_del(){
	$model = M('WxCustomMenu');
	$class = $model->where(array('token'=>$_SESSION['token'],'pid'=>$_GET['id']))->order('sort desc')->find();
	//echo M('Wx_custom_menu')->getLastSql();exit;
	if($class==false){
		$ids = explode(',',$_GET['id']);
		$data['token'] = $_SESSION['token'];
		$data['id'] = array('in',$ids);
		$back=M('Wx_custom_menu')->where($data)->delete();
		if($back==true){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}else{
		$this->error('请删除该分类下的子分类');
	}
  }

  /**
   * 编辑自定义菜单
   */
  public function class_edit(){
	$model = D ('WxCustomMenu');
	if($_POST){
		//dump($model->create ());exit;
		if (false === $model->create ()) {
		  $this->error ( $model->getError () );
		}
		// 更新数据
		$list = $model->save ();
		//echo $model->getlastsql();exit;
		if (false !== $list) {
		  //成功提示
		  $this->history($_POST['id']);
		  //$this->assign('jumpUrl',U('Wx_user/diymenu',array('token'=>$_SESSION['token'])));
		  //$this->success ('编辑成功!');
		  $this->assign('message','编辑成功!');
		  $this->display('Public:success2');exit;
		} else {
		  //错误提示
		  //$this->error ('编辑失败!');
		    $this->assign('error','编辑失败!');
		    $this->display('Public:error2');exit;
		}
		EXIT;
	}else{
		$data = $model->where(array('token'=>$_SESSION['token'],'id'=>$_GET['id']))->find();
		if($data==false){
			$this->error('您所操作的数据对象不存在！');
		}else{
			$class = $model->where(array('token'=>$_SESSION['token'],'pid'=>0))->order('sort desc')->select();
			$this->assign('class',$class);
			$this->assign('show',$data);
		}
		$this->display();
	}
  }

  /**
   * 发布自定义菜单
   */
  public function  class_send(){
		$api = M('WxUser')->where(array('token'=>$_SESSION['token']))->find();
		$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
		$json=json_decode($this->curlGet($url_get));

		if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
		$data = '{"button":[';

		$class=M('WxCustomMenu')->where(array('token'=>$_SESSION['token'],'pid'=>0))->limit(3)->order('sort desc')->select();
		//dump($class);
		$kcount=M('WxCustomMenu')->where(array('token'=>$_SESSION['token'],'pid'=>0))->limit(3)->order('sort desc')->count();
		$k=1;
		foreach($class as $key=>$vo){
			//主菜单
			$data.='{"name":"'.$vo['title'].'",';
			$c=M('WxCustomMenu')->where(array('token'=>$_SESSION['token'],'pid'=>$vo['id']))->limit(5)->order('sort desc')->select();
			$count=M('WxCustomMenu')->where(array('token'=>$_SESSION['token'],'pid'=>$vo['id']))->limit(5)->order('sort desc')->count();
			//子菜单
			if($c!=false){
				$data.='"sub_button":[';
			}else{
				  if($vo['url']){
					$data.='"type":"view","url":"'.$vo['url'].'"';
						
					}else{
						
						$data.='"type":"click","key":"'.$vo['keyword'].'"';
						
					}
				  
			}
			$i=1;
			foreach($c as $voo){
				if($i==$count){
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"}';
					}else{
						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
					}
				}else{
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"},';
					}else{
						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
					}
				}
				$i++;
			}
			if($c!=false){
				$data.=']';
			}

			if($k==$kcount){
				$data.='}';
			}else{
				$data.='},';
			}
			$k++;
		}
		$data.=']}';

		file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json->access_token);

		$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
		if($this->api_notice_increment($url,$data)==false){
			$this->error('操作失败');
		}else{
			$this->success('操作成功');
		}
		exit;
  }

  /**
   * 清空自定义菜单
   */
  function class_clear(){
    $model = M('WxCustomMenu');
	$data['token'] = $_SESSION['token'];
	$model->where($data)->delete();
	$api=M('wx_user')->where(array('token'=>$_SESSION['token']))->find();
	$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
	$json = json_decode($this->curlGet($url_get),1);
	
	$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json['access_token'];
	$json = json_decode($this->curlGet($url),1);
	if($json['errcode']==0){
	  $this->success('操作成功',U('Wx_user/diymenu',array('token'=>$_SESSION['token'])));	  
	}else{
	  $this->error('操作失败');
	}
  }

  /**
   * 拉取微信用户清单
   */
  function update_member(){
	$model = M('WxUser');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	//dump($vo);
	//查看最后一个用户
	$model = M('wx_member');
	$wm_data['token'] = $vo['token'];
	$wm_vo = $model->field('openid')->where($wm_data)->order('id desc')->find();
	//echo $model->getlastsql();
	//dump($wm_vo);exit;
	$firstLetter = $vo['token'];
	$appId = $vo['appid'];
	$appSecret = $vo['appsecret'];
	$path = C('DATA_CACHE_PATH').$firstLetter.'/access_token.json';
	//echo $path;exit;
	$status = file_exists($path);
	if($status){
	  $data = json_decode(file_get_contents($path),1);
	}
    if (!$status || $data['expire_time'] < time()) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
	  $res = json_decode($this->curlGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data['expire_time'] = time() + 7000;
        $data['access_token'] = $access_token;
		file_put_contents($path,json_encode($data));
      }
    } else {
      $access_token = $data['access_token'];
    }
	if($wm_vo){
	  $res = $this->get_list($access_token,$wm_vo['openid']);
	}else{
	  $res = $this->get_list($access_token);
	}
	if($res['total']==$res['count']){
	  $list = $res['data']['openid'];
	}else{
	  $list = $res['data']['openid'];
	  //next_openid
	  $page_num = ceil($res['total']/$res['count']);
	  for($i=0;$i<$page_num-1;$i++){
	    $res = $this->get_list($access_token,$res['next_openid']);
		if($res['data']){
		   $list = array_merge($list,$res['data']['openid']);
		}
	  }
	}
	$model = M('WxMember');
	foreach($list as $openid){
	  $wm_data['openid'] = $openid;
	  $count = $model->where($wm_data)->count();
	  if($count){
	    continue;
	  }else{
	    //拉取用户信息
		$url2 = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
		$res2 = json_decode($this->curlGet($url2),1);
		$add_data['token'] = $vo['token'];
		$add_data['openid'] = $openid;
		$add_data['nickname'] = $res2['nickname'];
		$add_data['sex'] = $res2['sex'];
		$add_data['city'] = $res2['city'];
		$add_data['province'] = $res2['province'];
		$add_data['country'] = $res2['country'];
		$add_data['headimgurl'] = $res2['headimgurl'];
		$add_data['create_time'] = $res2['subscribe_time'];
		$add_data['remark'] = $res2['remark'];
		$add_data['groupid'] = $res2['groupid'];
		$model->add($add_data);
		//echo $model->getlastsql();exit;
		//dump($res2);exit;
	  }
	}
	$this->success('更新完成');	 
  }

  /**
   * 请求获取信息
   */
  protected function get_list($access_token,$next_openid=''){
	if($next_openid){
	  $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=$next_openid";
	}else{
	  $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token";	
	}
	$res = json_decode($this->curlGet($url),1);
	return $res;
  }
  
  /**
   * CURL请求
   */
  function api_notice_increment($url, $data){
	$ch = curl_init();
	$header = "Accept-Charset: utf-8";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);
	$msg = json_decode($tmpInfo,true);
	if($msg['errcode']>0){
	  return false;
	}
	if (curl_errno($ch)) {
		return false;
	}else{

		return true;
	}
  }

  /**
   * CURL请求
   */
  function curlGet($url){
	$ch = curl_init();
	$header = "Accept-Charset: utf-8";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$temp = curl_exec($ch);
	return $temp;
  }

} 
?>