<?php 
namespace Home\Controller;
use Think\Controller;
class TradeController extends CommonController {

  /**
   * 添加信息 前置
   */
  public function _before_add(){
    if(!$_POST){
	  import('@.ORG.Util.Tree');
	  $name=CONTROLLER_NAME;
	  $model = D ($name);
	  $data['status'] = 1;
	  $list = $model->where($data)->order('id asc')->select();
	  //echo $model->getlastsql();
	  //dump($list);
	  $tree = new Tree($list);
	  $list = $tree->get_tree('0');
	  
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
	    $name=CONTROLLER_NAME;
	    $model = D ($name);
		$pdata['id'] = $_POST['pid'];
		$vo = $model->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	  $_SESSION['pid'] = $_POST['pid'] ? $_POST['pid'] : 0;
	  
	}
  }

  /**
   * 编辑信息 前置
   */
  public function _before_edit(){
	if(!$_POST){
	  import('@.ORG.Util.Tree');
	  $name=CONTROLLER_NAME;
	  $model = D ($name);
	  $data['status'] = 1;
	  $list = $model->where($data)->order('sort asc,id desc')->select();
	  $tree = new Tree($list);
	  $list = $tree->get_tree('0');
	  $this->assign('types',$list);
	}else{
	  if($_POST['pid']){
	    $name=CONTROLLER_NAME;
	    $model = D ($name);
		$pdata['id'] = $_POST['pid'];
		$vo = $model->field('id,name')->where($pdata)->find();
		$_POST['pname'] = $vo['name'];
	  }else{
	    $_POST['pname'] = '-';
	  }
	  
	}
  }

  /**
   * 行业导入 废弃
   */
  public function daoru(){
	exit;
    $model = M('trade');
    $str = '互联网/电子商务  计算机软件  计算机硬件 IT服务/系统集成  通信/电信  电子技术/半导体/集成电路  仪器仪表/工业自动化 财务/审计  金融/银行  保险  贸易/进出口 批发/零售  快速消费品(食品/饮料等)  耐用消费品（家具/家电等）  服装/纺织/皮革 办公用品及设备  钢铁/机械/设备/重工  汽车/摩托车  医疗/保健/卫生/美容 生物/制药/医疗器械  广告/创意  公关/市场推广/会展  文体/影视/艺术 媒体传播  出版/印刷/造纸  房地产/物业管理  建筑/建材 家居/室内设计/装潢  中介/专业服务  检测/认证  法律/法务 教育/科研/培训  旅游/酒店  娱乐休闲/餐饮/服务  交通/运输/物流 航天/航空  化工/采掘/冶炼  能源（电力/水利/矿产）  原材料和加工 政府/非盈利机构  环保  农林牧渔  多元化集团 人力资源服务  其他行业';
    $arr = explode('  ',$str);
	foreach($arr as $val){
	  $add_data['name'] = $val;
	  $count = $model->where($add_data)->count();
	  if($count>0){
	    continue;
	  }
	  $model->add($add_data);
	}
	dump($arr);
  
  }


}
?>