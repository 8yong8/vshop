<?php
namespace Home\Controller;
use Think\Controller;
class WeixinController extends Controller {

	public $token;				//微信token
	public $wx_msg;				//账号信息
	public $wx_obj;				//微信类
	public $data = array ();    //解析后数据
	public $FromUserName = array();

    public function index(){

		$time = time();
		/*获取微信TOKEN*/
		$this->token = $_GET['token'];

		$wdata['token'] = $_GET['token'];

		$wx_msg = M('wx_user')->where($wdata)->find();

		$this->wx_msg = $wx_msg;

		$weixin = new \Org\Net\ThinkWechat($wx_msg);
		//$weixin = new \Org\Net\Wechat($this->token);

		$this->wx_obj =  $weixin;

		//$weixin->response('ceshi','text');exit;

		/* 获取请求信息 */
		$this->data = $data = $weixin->request();

		//记录微信信息
		if($wx_msg && $data){
		  $wm_model = M('WxMsg');
		  $wm_data['openid'] = $data['FromUserName'];
		  $wm_data['msg'] = serialize($data);
		  $wm_data['create_time'] = time();
		  $wm_model->add($wm_data);
		}
		
		/* 获取回复信息 */
		list($content, $type) = $this->reply($data);

		/* 响应当前请求 */
		$weixin->response($content, $type);
	}

	private function reply($data){

		$openid = $data['FromUserName']; //OPENID
		$openidWX = $data['ToUserName'];//公众账户原始ID

        //$member = $this->wx_obj->user($openid);
        //$weixin = new \Org\Net\ThinkWechat($this->wx_msg);
        //$member = $weixin->user($openid);

		if('text' == $data['MsgType']){
			
			$reply = $this->key_reply();//查找关键字
			
			//查找不到信息,默认回复
			if(!$reply){
				$reply = $this->reply_default($data,'text');//是否有默认回复
			}

		}elseif('image' == $data['MsgType']){

			$reply=$this->reply_default($data,'wimage');//是否有默认回复

		}elseif('event' == $data['MsgType'] && 'subscribe' == $data['Event']){//关注后回复			

			$wm_model = M('WxMember');

			$wm_data['openid'] = $openid;

			$member_wx = $wm_model->field('*')->where($wm_data)->find();

			if($member_wx){
				$wm_sdata['status'] = 1;
				//$uw_sdata['id'] = $uid;
				$wm_model->where($wm_data)->save($wm_sdata);//重新关注
			}else{

				//添加关注信息
				$model = M('WxMember');
				$wdata['token'] = $this->token;
				//$wdata['wxid'] = $data['ToUserName'];
				$wdata['openid'] = $data['FromUserName'];
				$wdata['create_time'] = time();
				$id = $model->add($wdata);

				//用户详细信息
                $member_msg = $this->wx_obj->user($openid);
			}
			
			return $reply=array('谢谢您的关注', 'text');
			
		}elseif('event' == $data['MsgType'] && 'CLICK' == $data['Event']){//自定义菜单

			$data['Content'] = $data['EventKey'];
			$reply = $this->key_reply();//查找关键字
			
			if(!$reply){
				$reply = $this->reply_default();//是否有默认回复
			}
		}elseif ('unsubscribe' == $data['Event']) {//解绑后

		  $uw_model = M('WxMember');
		  $uw_wdata['openid'] = $openid;
		  $uw_sdata['status'] = 0;
		  $uw_sdata['uid'] = 0;
		  $uw_model->where($uw_wdata)->save($uw_sdata);

        }elseif('event' == $data['MsgType'] && 'LOCATION' == $data['Event']){//获取用户地理位置
		    /*
			$longitude=$data['Longitude'];//经度
			$latitude=$data['Latitude'];//纬度
			$u = M('User');

			import("ORG.Net.Http");
            $server_ak=C('SERVER_AK');
            $url="http://api.map.baidu.com/geoconv/v1/?coords=$longitude,$latitude&from=3&to=5&ak=$server_ak";
            $rsource=Http::fsockopenDownload($url);
            $rsource=json_decode($rsource,TRUE);

			$udata['jindu']=$rsource['result'][0]['x'];
			$udata['weidu']=$rsource['result'][0]['y'];
			$flag=$u->where("openid = '%s'",$openid)->data($udata)->save();
			*/
		} else {
		  $reply=$this->reply_default($data,'wother');//是否有默认回复
		}
		return $reply;

	}

	//默认回复
	private function reply_default(){
	  $reply = array('目前问题无回答','text');
	}

	//关键字回复
	private function key_reply(){

		$openid = $this->data['FromUserName']; //OPENID
		$openidWX = $this->data['ToUserName'];//公众账户原始ID
		$time = time();

		//保存消息记录
		if(!empty($data['Content'])){

		}

		$url = C('Wurl');
		$Wroot = C('Wroot');

		$msg_default = '您查找的信息不存在';

		return $reply = array($msg_default, 'text');

		$wk_model = M('Weixin_keyreply');

		//$wk_data['status'] = 1;
		$wk_data['key_words'] = array('like','%'.$this->data['Content'].'%');
		//先查文字回复
		$key = $wk_model->where($wk_data)->field('*')->find();
		//return $reply = array(M('')->getlastsql(), 'text');
		if(!$key){
			return $reply = array($msg_default, 'text');
		}elseif($key['match']==1){//精确搜索

			$key_words = explode(',', $key['key_words']);

			foreach ($key_words as $v) {
				if($v==$this->data['Content']){
					$key_words_if = 1;continue;
				}
			}
			if(!$key_words_if){
				return $reply = array($msg_default, 'text');
			}
		}

		//检查类型
		if($key['is_tuwen']==1){//图文回复
			$wt_model = M('Weixin_tuwenreply');
			$tuwen = $wt_model->where("aid={$key[id]}")->order('`sort` desc,id desc')->select();
			if($tuwen){
				foreach ($tuwen as $k => $v) {
					//组装图片地址
					if($Wroot){
					  $image_path = $url.'/'.$Wroot.'/Uploads/keyword/'.date('Y-m-d',$key['add_time']).'/s_'.$v['image_path'];
					}else{
					  $image_path = $url.'/Uploads/keyword/'.date('Y-m-d',$key['add_time']).'/s_'.$v['image_path'];					
					}
					//组装url
					if($v['image_url']){
					  $key = strpos($v['image_url'],'?');
					  if($key===false){
						$image_url = $v['image_url']."?openid={$openid}&openidWX={$openidWX}";
					  }else{
						$image_url = $v['image_url']."&openid={$openid}&openidWX={$openidWX}";
					  }
					}else{
					  $image_url = $url;
					}
					$news[] = array($v['image_title'],$v['image_description'],$image_path,$image_url);
				}
				$reply = array($news,'news');
			}else{
				$reply = array($msg_default, 'text');
			}

		}elseif($key['is_tuwen']==0){//关键字回复

			if ($key['reply_type']==1) {//文字
				$reply = array($key['image_title'], 'text');
			}else{
				//组装图片地址
				if($Wroot){
				  $image_path = $url.'/'.$Wroot.'/Uploads/keyword/'.date('Y-m-d',$key['add_time']).'/s_'.$key['image_path'];
				}else{
				  $image_path = $url.'/Uploads/keyword/'.date('Y-m-d',$key['add_time']).'/s_'.$key['image_path'];	
				}
				//组装url
				if($key['image_url']){
				  $key = strpos($key['image_url'],'?');
				  if($key===false){
					$image_url = $key['image_url']."?openid={$openid}&openidWX={$openidWX}";
				  }else{
					$image_url = $key['image_url']."&openid={$openid}&openidWX={$openidWX}";
				  }
				}else{
				  $image_url = $url;
				}
				$news[] = array($key['image_title'],$key['image_description'],$image_path,$image_url);
				$reply = array($news,'news');
			}

		}
		return $reply;
	}

	public function check_token(){
	
	
	}
}