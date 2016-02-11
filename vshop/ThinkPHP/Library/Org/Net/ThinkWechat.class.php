<?php
namespace Org\Net;
use Think\Log;
class ThinkWechat {

	/**
	 * 微信配置信息
	 * @var array
	 */
	public $config;

	/**
	 * 微信推送过来的数据或响应数据
	 * @var array
	 */
	private $data = array();
	
	/**
	 * 主动发送的数据
	 * @var array
	 */
	private $send = array();

	/**
	 * 构造函数
	 * @return
	 */
	public function __construct($config){
	  if($config){
	    $this->config = $config;
		$this->config['token'] = $config['token'];
		$this->config['appid'] = $config['appid'];
		$this->config['appsecret'] = $config['appsecret'];
	  }else{
		if(!$this->config){
			$this->config['token'] = C ( 'WECHAT_TOKEN' );
			$this->config['appid'] = C ( 'WECHAT_APPID' );
			$this->config['appsecret'] = C ( 'WECHAT_APPSECRET' );
		}
	  }
	}

		
	/**
	 * 获取微信推送的数据
	 * @return array 转换为数组后的数据
	 */
	public function request(){

        $this->auth() || die;
        if ($_SERVER['REQUEST_METHOD']=='GET') {
            echo $_GET['echostr'];
            die;
        } else {
            $xml = file_get_contents('php://input');
            $xml = new \SimpleXMLElement($xml);
            $xml || die;
			
            foreach ($xml as $key => $value) {
                $this->data[$key] = strval($value);
            }
        }
       	return $this->data;
	}

	/**
	 * * 被动响应微信发送的信息（自动回复）
	 * @param  string $to      接收用户名
	 * @param  string $from    发送者用户名
	 * @param  array  $content 回复信息，文本信息为string类型
	 * @param  string $type    消息类型
	 * @param  string $flag    是否新标刚接受到的信息
	 * @return string          XML字符串
	 */
	public function response($content, $type = 'text', $flag = 0){
		/* 基础数据 */
		$this->data = array(
			'ToUserName'   => $this->data['FromUserName'],
			'FromUserName' => $this->data['ToUserName'],
			'CreateTime'   => NOW_TIME,
			'MsgType'      => $type,
		);

		/* 添加类型数据 */
		$this->$type($content);

		/* 添加状态 */
		$this->data['FuncFlag'] = $flag;

		/* 转换数据为XML */
		$xml = new \SimpleXMLElement('<xml></xml>');
		$this->data2xml($xml, $this->data);
		exit($xml->asXML());
	}

  /**
   * 作用：array转xml
   */
	 public function arrayToXml($arr)
	  {
	  	//Log::write("111".$arr,'ERR');
	    $xml = "<xml>";
	    foreach ($arr as $key=>$val)
	    {
	      if (is_numeric($val))
	      {
	        $xml.="<".$key.">".$val."</".$key.">";

	      }
	      else
	        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
	    }
	    $xml.="</xml>";
	    return $xml;
	  }


	/**
	 * * 主动发送消息
	 *
	 * @param string $content   内容
	 * @param string $openid   	发送者用户名
	 * @param string $type   	类型
	 * @return array 返回的信息
	 */
	
	public function sendMsg($content, $openid = '', $type = 'text') {

		/* 基础数据 */
		$this->send ['touser'] = $openid;
		$this->send ['msgtype'] = $type;
		/* 添加类型数据 */
		$sendtype = 'send' . $type;
		$this->$sendtype ( $content );
		/* 发送 */
		$sendjson = jsencode ( $this->send );
		$restr = $this->send ( $sendjson );
		return $restr;

	}
	/**
	 * 发送文本消息
	 * 
	 * @param string $content
	 *  要发送的信息
	 */
	private function sendtext($content) {
		$this->send ['text'] = array (
				'content' => $content 
		);
	}
	
	/**
	 * 发送图片消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendimage($content) {
		$this->send ['image'] = array (
				'media_id' => $content 
		);
	}

	/**
	 * 发送视频消息
	 * @param  string $content 要发送的信息
	 */
	private function sendvideo($video){
		list (
			$video ['media_id'],
			$video ['title'],
			$video ['description']
		) = $video;
		
		$this->send ['video'] = $video;
	}
	
	/**
	 * 发送语音消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendvoice($content) {
		$this->send ['voice'] = array (
				'media_id' => $content 
		);
	}
	
	/**
	 * 发送音乐消息
	 * 
	 * @param string $content
	 *        	要发送的信息
	 */
	private function sendmusic($music) {
		list ( 
			$music ['title'], 
			$music ['description'], 
			$music ['musicurl'], 
			$music ['hqmusicurl'], 
			$music ['thumb_media_id']
		) = $music;
		
		$this->send ['music'] = $music;
	}
	
	/**
	 * 发送图文消息
	 * @param  string $news 要回复的图文内容
	 */
	private function sendnews($news){
		//Log::write("tiansongtiansongtiansong",'ERR');
		$articles = array();
		foreach ($news as $key => $value) {
			list(
					$articles[$key]['title'],
					$articles[$key]['description'],
					$articles[$key]['url'],
					$articles[$key]['picurl']
			) = $value;
			if($key >= 9) { break; } //最多只允许10调新闻
		}
		$this->send['articles'] = $articles;
		//Log::write("999999999999999999999999999999",'ERR');
	}
	
	
	/**
	 * * 获取微信用户的基本资料
	 * 
	 * @param string $openid   	发送者用户名
	 * @return array 用户资料
	 */
	public function user($openid = '') {
		if ($openid) {
			header ( "Content-type: text/html; charset=utf-8" );
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info';
			$params = array ();
			$params ['access_token'] = $this->getToken ();
			$params ['openid'] = $openid;
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$params ['access_token'].'&openid='.$openid.'&lang=zh_CN';
			//echo $url;exit;
			$httpstr = $this->curlGet ( $url, $params );
			$harr = json_decode ( $httpstr, true );

			//dump($harr);exit;
			return $harr;
		} else {
			return false;
		}
	}
	
	/**
	 * 生成菜单
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function setMenu($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		//file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token);
		$this->delMenu();
		$menustr = $this->api_notice_increment($url,$data);
		return $menustr;
	}
	
	/**
	 * 删除菜单
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function delMenu($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
		$menustr = $this->curlGet($url);
		return $menustr;
	}
	/**
	 * 新增分组
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function addGroup($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$access_token}";
		$str = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $str;
	}
	/**
	 * 获取分组
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function getGroup($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$access_token}";
		$str = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $str;
	}
	/**
	 * 修改分组
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function editGroup($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token={$access_token}";
		$str = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $str;
	}
	/**
	 * 移动分组
	 * @param  string $data 菜单的str
	 * @return string  返回的结果；
	 */
	public function goGroup($data = NULL){
		$access_token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";
		$str = http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"), true);
		return $str;
	}
	/**
	 * 多客服
	 * @param  string $content 发送的内容
	 */
	private function transfer_customer_service($content){
		$this->data['Content'] = $content;
	}


	/**
	 * 回复文本信息
	 * @param  string $content 要回复的信息
	 */
	private function text($content){
		$this->data['Content'] = $content;
	}

	/**
	 * 回复音乐信息
	 * @param  string $content 要回复的音乐
	 */
	private function music($music){
		list(
			$music['Title'], 
			$music['Description'], 
			$music['MusicUrl'], 
			$music['HQMusicUrl']
		) = $music;
		$this->data['Music'] = $music;
	}

	/**
	 * 回复图文信息
	 * @param  string $news 要回复的图文内容
	 */
	private function news($news){
		$articles = array();
		foreach ($news as $key => $value) {
			list(
				$articles[$key]['Title'],
				$articles[$key]['Description'],
				$articles[$key]['PicUrl'],
				$articles[$key]['Url']
			) = $value;
			if($key >= 9) { break; } //最多只允许10调新闻
		}
		$this->data['ArticleCount'] = count($articles);
		$this->data['Articles'] = $articles;
		 $udata['content']=$articles;
        $udata['addtime']=time();
        M("Test")->data($udata)->add();
	}
		
	/**
	 * 主动发送的信息
	 * @param  string $data    json数据
	 * @return string          微信返回信息
	 */
	private function send($data = NULL) {
		$access_token = $this->getToken ();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$restr = http ( $url, $data, 'POST', array ( "Content-type: text/html; charset=utf-8" ), true );
		return $restr;
	}

    /**
     * 数据XML编码
     * @param  object $xml  XML对象
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    protected static function data2xml($xml, $data, $item = 'item') {
    	//Log::write("dddddddddddddddddddddddddd",'ERR');
        foreach ($data as $key => $value) {
            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;

            /* 添加子元素 */
            if(is_array($value) || is_object($value)){
                $child = $xml->addChild($key);
                self::data2xml($child, $value, $item);
            } else {
                if(is_numeric($value)){
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $cdata = $node->ownerDocument->createCDATASection($value);
                    $node->appendChild($cdata);
                }
            }
        }
    }


    /**
	 * 对数据进行签名认证，确保是微信发送的数据
	 * @param  string $token 微信开放平台设置的TOKEN
	 * @return boolean       true-签名正确，false-签名错误
	 */
	private function auth(){

		/* 获取数据 */
		$data = array($_GET['timestamp'], $_GET['nonce'], $_REQUEST['token']);
		$sign = $_GET['signature'];
		
		/* 对数据进行字典排序 */
		sort($data,SORT_STRING);

		/* 生成签名 */
		$signature = sha1(implode($data));

		return true || $signature === $sign;//验证通过 URL(服务器地址)不能带参数
	}
	/**
	 * 获取保存的accesstoken
	 */
	private function getToken() {
		$w = M('Wx_user');
		$w_data['id'] = $this->config['id'];
		if($this->config['token']){
			$nowtime = time ();
			$difftime = $nowtime - $this->config['access_token_time']; // 判断缓存里面的TOKEN保存了多久；
			if ($difftime > 7000 or empty($this->config['access_token'])) { // TOKEN有效时间7200 判断超过7000就重新获取;
				$accesstoken = $this->getAcessToken (); // 去微信获取最新ACCESS_TOKEN
				$s_data['access_token'] = $accesstoken;
				$s_data ['access_token_time'] = $nowtime;
				$w->where($w_data)->save($s_data);
			} else {
				$accesstoken = $this->config ['access_token'];
			}

		}else{
			$accesstoken = $this->getAcessToken (); // 去微信获取最新ACCESS_TOKEN
			$s_data['access_token'] = $accesstoken;
			$s_data ['access_token_time'] = $nowtime;
			$w_data['id'] = $this->config['id'];
			$w->where($w_data)->save($s_data);
		}
		return $accesstoken;
	}
	
	/**
	 * 重新从微信获取accesstoken
	 */
	private function getAcessToken() {
		$token = $this->config['token'];
		$appid = $this->config['appid'];
		$appsecret = $this->config['appsecret'];
		$url = 'https://api.weixin.qq.com/cgi-bin/token';
		$params = array ();
		$params ['grant_type'] = 'client_credential';
		$params ['appid'] = $appid;
		$params ['secret'] = $appsecret;
		$url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
		$json = json_decode($this->curlGet($url_get),true);
		if($json){
		  return $json ['access_token'];
		}else{
		  return false;
		}
		/*
		$httpstr = http ( $url, $params );
		$harr = json_decode ( $httpstr, true );
		return $harr ['access_token'];
		*/
	}
		/**================================模板接口消息==================**/
	 /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    private function request_post($url = '', $param = '')
    {
		//echo $url;dump($param);exit;
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//zzy
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//zzy
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }


     /**
     * 发送自定义的模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend($touser, $template_id, $url, $data, $topcolor = '#7B68EE'){
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->getAcessToken();
        $dataRes = $this->request_post($url, urldecode($json_template));
        if ($dataRes['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

  /**
   * CURL请求
   */
  function curlGet($url,$data){
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

}
