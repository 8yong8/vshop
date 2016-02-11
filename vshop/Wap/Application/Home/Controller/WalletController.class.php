<?php
namespace Home\Controller;
use Think\Controller;
class WalletController extends CommonController {

  /**
   * 我的钱包
   */
  public function index(){

	$this->assign('headerTitle','我的钱包');
	$this->assign('headerKeywords','我的钱包');
	$this->assign('headerDescription','我的钱包');
	$this->assign('wx_title','我的钱包');
	$this->assign('wx_desc','微信分享');
	$this->display();
  }


}
?>