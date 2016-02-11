<?php 
namespace Home\Controller;
use Think\Controller;
require_once C('PUBLIC_INCLUDE')."function.inc.php";
class EmptyController extends Controller {

  /**
   * 模块不要存在
   */
  public function _initialize(){
	ajaxErrReturn('模块不存在');
  }

}
?>