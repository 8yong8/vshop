<?php
namespace Home\Controller;
use Think\Controller;
class ShoplistController extends CommonController{

  public function _before_index(){
    $_GET['q_uid'] = 0;//期数
  }

  /**
   * 之前列表
   */
  public function before(){
	$model = D('Shoplist');
	$where['sid'] = $_GET['id'];
	$where['id'] = array('neq',$_GET['id']);
	$count = $model->where($where)->count();
	$this->assign('count',$count);
	if($count>0){
	  //创建分页对象
	  $listRows = 20;
	  $p = new \My\Page($count,$listRows);
	  $list = $model->field('*')->where($where)->order('id desc')->limit($p->firstRow.','.$p->listRows)->select();
	  //echo $model->getlastsql();
	//分页跳转的时候保证查询条件
	foreach($map as $key=>$val) {
		if(is_array($val)) {
			foreach ($val as $t){
				$p->parameter	.= $key.'[]='.urlencode($t)."&";
			}
		}else{
			$p->parameter   .=   "$key=".urlencode($val)."&";        
		}
	}
	//分页显示
	$page       = $p->Show();
	}
	//列表排序显示
	$sortImg    = $sort ;                                   //排序图标
	$sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
	$sort       = $sort == 'desc'? 1:0;                     //排序方式
	//模板赋值显示
	//echo json_encode($list);exit;
	$this->assign('list',       $list);
	$this->assign('sort',       $sort);
	$this->assign('order',      $order);
	$this->assign('sortImg',    $sortImg);
	$this->assign('sortType',   $sortAlt);
	$this->assign("page",       $page);
	$this->display();	
  
  }

  /**
   * 添加信息
   */
  public function add(){
	if($_POST){
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('Category');
		$tdata['id'] = $_POST['tid'];
		$vv = $model->where($tdata)->find();
		$_POST['type_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['toptid'] = $vv['id'];
		}else{
		  $_POST['toptid'] = $vv['id'];
		}
	    //图片处理处理
		if($_FILES){
		  $this->upload();
		}
		$model = D ( CONTROLLER_NAME );
		$model->startTrans();//启用事务
		$_POST['create_time'] = time();
		$_POST['zongrenshu'] = $_POST['shenyurenshu'] = ceil($_POST['money']/$_POST['yunjiage']);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$id = $model->add();
		if (false !== $id) {
			//sid处理
			$s_data['sid'] = $id;
			$w_data['id'] = $id;
			$model->where($w_data)->save($s_data);
			//验证码生成
			$result = 1;
			$smodel = M('shopcodes_2015');
			$zongrenshu = ceil($_POST['money']/$_POST['yunjiage']);
			$base_num = 200;
			$codes_len = ceil($zongrenshu/$base_num);
			if($zongrenshu==0 || ($zongrenshu-$canyurenshu)==0){
				$this->error("云购价格不正确");
			}
			$codes = content_get_go_codes($zongrenshu,$base_num);
			foreach($codes as $key=>$code){
			   $s_add_data['s_id'] = $id;
			   $s_add_data['s_cid'] = $key+1;
			   $s_add_data['s_len'] = count($code);
			   $s_add_data['s_codes'] = serialize($code);
			   $s_add_data['s_codes_tmp'] = serialize($code);
			   if($result)$result = $smodel->add($s_add_data);
			}
			if($result){
			  $model->commit();
			}else{
			  $model->rollback();
			  $this->error('新增失败');
			  exit;
			}
			//成功提示
			$this->history($id);
			//相关属性表修改
			$amodel = D('flag_list');
			if($flags){
				$data1['source'] = $name;
				$data1['sourceid'] = $result;
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  $amodel->add($data1);
				}
			}

			$this->success ('新增成功!');
		}else{
		  $this->error ('新增失败!');
		}
		exit;
	}else{
		//$arr = content_get_go_codes(88,20);
		$this->other_msg();
		$flags = $this->get_moudel_flags();
		$this->assign('flags',$flags);
		$hash = md5($_SESSION[C('USER_AUTH_KEY')].time());
		$this->assign('imgurl',C('IMG_URL'));
		$this->assign('hash',$hash);
		$this->display();
	}
  }

  /**
   * 编辑信息
   */
  public function edit(){
    if($_POST){
		$flags = $_POST['flags'];
		if($_POST['flags']){
		  $_POST['flags'] = implode(',',$_POST['flags']);
		}else{
		  $_POST['flags'] = '';
		}
		//分类处理
		$model = D('category');
		$tdata['id'] = $_POST['tid'];
		$vv = $model->where($tdata)->find();
		$_POST['type_name'] = $vv['name'];
		if($vv['pid']){
		  $tdata['id'] = $vv['pid'];
		  $vv = $model->where($tdata)->find();
		  if($vv['pid']){
		   $tdata['id'] = $vv['pid'];
		   $vv = $model->where($tdata)->find();	    
		  }
		  $_POST['toptid'] = $vv['id'];
		}else{
		  $_POST['toptid'] = $vv['id'];
		}
	    //图片处理处理
		if($_FILES){
		  $this->upload();  	
		}
		$model = D ( CONTROLLER_NAME );
		$_POST['create_time'] = time();
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$result = $model->save();
		if (false !== $result) {
			//成功提示
			$this->history($_POST['id']);
			//相关属性表修改
			$amodel = D('flag_list');
			$wdata['source'] = $name;
			$wdata['sourceid'] = $_POST['id'];
			$flagslist = $amodel->field('fid')->where($wdata)->select();
			foreach($flagslist as $v){
			  $key = array_search($v['fid'],$flags);
			  if($key!==false){
				//记录已经存在的
				$k_fids[] = $v['fid'];
			  }else{
				//要删除的
				$d_fids[] = $v['fid'];
			  }
			  $oldflags[] = $v['fid'];
			}

			if($d_fids){
			  $wdata['fid'] = array('in',$d_fids);
			  $amodel->where($wdata)->delete();
			}
			if($flags){
				$data1['source'] = $name;
				$data1['sourceid'] = $_POST['id'];
				$data1['sort'] = 200;
				$flagss = $this->get_moudel_flags();
				foreach($flags as $fid){
				  $data1['fid'] = $fid;
				  $data1['fname'] = $flagss[$fid]['name'];
				  $data1['create_time'] = time();
				  if($oldflags){
					$key = array_search($fid,$oldflags);
					//已存在信息修改
					if($key!==false){
					  $wdata['fid'] = $fid;
					  unset($data1['sort']);
					  $amodel->where($wdata)->save($data1);
					  continue;
					}
				  }
				  $amodel->add($data1);
				  //echo $amodel->getlastsql();exit;
				}
			}else{
			  $amodel = D('flag_list');
			  $del_data['source'] = $name;
			  $del_data['sourceid'] = $_POST['id'];
			  $flagslist = $amodel->where($del_data)->delete();
			}
			$this->success ('编辑成功!');
		}else{
		  $this->error ('编辑失败!');
		}
		exit;
	}else{
	  $flags = $this->get_moudel_flags();
	  $this->assign('flags',$flags);
	  $model = D ( CONTROLLER_NAME );
	  $data['id'] = $_GET['id'];
	  $vo = $model->where($data)->find();
	  $this->assign('vo',$vo);
	  $this->other_msg();
	  $this->display();
	}
  }

  /**
   * 其他信息
   */
  function other_msg($pid=0){
	//$producttypes = $this->get_type();
	$model = M('One_category');
	$data['status'] = 1;
	$data['pid'] = 0;
	//$data['_string'] = "find_in_set('云购',channel)";
	//$data['pid'] = $pid;
	$types = $model->where($data)->select();
	$data['pid'] = $pid ? $pid : $types[0]['id'];
	$types2 = $model->where($data)->select();
	/*
	import('@.ORG.Util.Tree');
	$tree = new Tree($types);
	$types = $tree->get_tree('0');
	*/
	$this->assign('types',$types);
	$this->assign('types2',$types2);
  }

  /**
   * 分类信息
   */
  function get_category(){
	$model = M('One_category');
	$data['status'] = 1;
	//$data['_string'] = "find_in_set('云购',channel)";
	$data['pid'] = $_POST['pid'];
	$types = $model->where($data)->select();
	if(!$types){
	  unset($data);
	  $data['id'] = $_POST['pid'];
	  $types = $model->where($data)->select();
	}
    echo json_encode($types);
  }

  /**
   * 相册管理
   */
  public function album(){
    $model = D('Pic');
	$data['source'] = CONTROLLER_NAME;
	$data['sourceid'] = $_GET['sourceid'];
	$pics = $model->where($data)->select();
	$this->assign('pics',$pics);
	$model = D('Product');
	$pdata['id'] = $_GET['sourceid'];
	$vo = $model->field('id,cat_name,name,lit_pic')->where($pdata)->find();
	if(!$vo){
	  $this->error('产品不存在!');
	}
	$this->assign('vo',$vo);
    $this->display();
  }
  
  /**
   * 图片修改
   */
  public function upablum(){
	if($_FILES){
	  $list = $this->upload();
	  $info = $list['data'];
	  if($info!=""){
		foreach($info as $file){
		  $key = $file['key'];
		  if($key=='up_zp'.$_POST['f_logo']){
			$_POST['logo'] = C('IMG_URL').$file['savename'];
		  }
		  preg_match('/\d+/i',$key,$matches);
		  $pics['title'][] = $_POST['title'.$matches[0]];
		  $pics['size'][] = filesize($path.$file['savename']);
		  $pics['filepath'][] = $filepath;
		  $pics['click'][] = rand(0,10);
		  $pics['savename'][] = $file['savename'];
		  //$pics['title'][] = 'title'.$matches[0];
		  //匹配出数字和title对应
		}
	  }
	}
	//dump($pics);exit;
	if(count($pics['filepath'])>0){
	  $model = M('Product');
	  $vdata['id'] = $_POST['id'];
	  $vo = $model->field('id,seller_id')->where($vdata)->find();
	  $member_id = $vo['seller_id'];
	}
	$model = D('Pic');
	for($i=0;$i<count($pics['filepath']);$i++){
	  $pdata['title'] = $pics['title'][$i];
	  $pdata['filepath'] = $pics['filepath'][$i].$pics['savename'][$i];
	  //$pdata['savename'] = $pics['savename'][$i];
	  $pdata['is_thumb'] = $is_thumb;
	  $pdata['click'] = $pics['click'][$i];
	  $pdata['size'] = $pics['size'][$i];
	  $pdata['domain'] = C('IMG_URL');
	  $pdata['source'] = CONTROLLER_NAME;
	  $pdata['sourceid'] = $_POST['id'];
	  $pdata['sort'] = $i+1;
	  $pdata['member_id'] = $member_id;
	  //$pdata['webset'] = $vo['webset'];
	  $pdata['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
	  $pdata['createname'] = $_SESSION['nickname'];
	  $pdata['create_time'] = time();
	  $picid = $model->add($pdata);
	  echo $model->getlastsql();exit;
	}
	if($_POST['select_logo']){
		$name=CONTROLLER_NAME;
		$model = D ( $name );
		$data['id'] = $_POST['id'];
		$sdata['lit_pic'] = $_POST['select_logo'];
		$result = $model->where($data)->save($sdata);	
	}
	if($result || $picid){
	  $this->history($_POST['id']);
	  $this->success ('修改成功!');
	}else{
	  $this->error ('修改失败!');
	}
  }

  /**
   * ajax删除图片
   */
  public function ajax_delpic(){
	$model = D('Pic');
	$data['source'] = CONTROLLER_NAME;
	$data['picid'] = $_POST['pid'];
	$vo = $model->field('domain,filepath,savename,is_thumb')->where($data)->find();
	//dump($vo);exit;
    //include "../../Ftp.php";
	//是否开启FTP删除
	if(false){
	  import ( '@.ORG.Ftp' );
	  $ftphost = $_SCONFIG['ftphost'];
	  $ftpport = $_SCONFIG['ftpport'];
	  $ftpuser = $_SCONFIG['ftpuser'];
	  $ftppassword = $_SCONFIG['ftppassword'];
	  $ftp = new ftp($ftphost,$ftpport,$ftpuser,$ftppassword);// 打开FTP连接
	  $dir = $_FTP[$vo['domain']].'/'.$vo['filepath'];        //删除远程文件
	  $ftp->del_file($dir);	
	}else{
	  //$dir = $_NFTP[$vo['domain']].'/'.$vo['filepath'];
	  $dir = C('IMG_ROOT').$vo['filepath'].$vo['savename'];
	  //echo C('IMG_ROOT').$vo['filepath'].$vo['savename'];exit;
	  unlink($dir);
	  if($vo['is_thumb']){
	    $dir = C('IMG_ROOT').$vo['filepath'].'thumb_'.$vo['savename'];
	    unlink($dir);
	    $dir = C('IMG_ROOT').$vo['filepath'].'thumb2_'.$vo['savename'];
	    unlink($dir);
	  }
	}
	$url= $vo['domain'].$vo['filepath'];
	$result = $model->where($data)->delete();
	//dump($result);exit;
	/*
	if(!$fp=@fopen($url,"r")){
	  //echo iconv("GBK", "UTF-8", "远程文件不存在！");
	}else{
	  echo "文件删除失败";
	  exit;
	}
	*/
	if($result){
	  $this->history($_POST['pid'],'deletepic');
	  $msg['error_code'] = 0;
	  $msg['notice'] = '删除成功';
	  echo json_encode($msg);exit;
	}else{
	  $msg['error_code'] = 8002;
	  $msg['notice'] = '删除失败';
	  echo json_encode($msg);exit;
	}
	exit;
  }

  /**
   * 排序页面
   */
  public function sort(){
	$name = CONTROLLER_NAME;
    $amodel = D('flag_list');
	$flags = $this->get_moudel_flags();
	$this->assign('flags',$flags);
	if($_GET['fid']){
	  $this->assign('fid',$_GET['fid']);
	  $data1['fid'] = $data2['fid'] = $_GET['fid'];
	  $data1['source'] = $data2['a.source'] = $name;
	  if($_GET['toptid']){
		$this->assign('toptid',$_GET['toptid']);
		$data1['toptid']=$_GET['toptid'];
	  }
	  $count = $amodel->where($data1)->count();
	  if($count>0){
	    //创建分页对象
	    $listRows = '20';
	    $p = new \My\Page($count,$listRows);
		$pageno = $_GET['p'] ? $_GET['p'] : 1;
		$offset = ($pageno-1)*$page_size;
	    $list = $amodel->table('`'.C('DB_PREFIX').'flag_list` as a')->join('`'.C('DB_PREFIX').strtolower($name).'` as b on a.sourceid=b.id')->field('b.id,b.name,sort,b.create_time')->where($data2)->order('sort asc,sourceid desc')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $amodel->getlastsql();exit;
		$page = $p->Show();
		$this->assign('page',$page);
		$this->assign('list',$list);
	  }
	}
    $this->display();
  }

  /**
   * 购买产品
   */
  function buy_pay(){
	$s_id = $_POST['s_id'] = 1;
	$s_num = $_POST['s_num'] = 100;
	$member_id = 3;
	$member_name = '8yong8';
	$realname = '阿勇';
	$address_id = 7;
    $model = M('shoplist');
	$data['id'] = $s_id;
	$vo = $model->field('id,name,lit_pic,yunjiage,qishu,status')->where($data)->find();
	if($vo['status']==2){
	  $this->error('此商品交易已完成');
	}
	if($vo['status']==0){
	  $this->error('此商品交易关闭');
	}
	$time = time();
	$ip = _get_ip();
	$model->startTrans();//启用事务
	//获得云码
	$codes = pay_get_shop_codes($s_num,$vo['id']);
	if($codes['code_len']<$s_num){
	  $s_num = $codes['code_len'];
	}
	if($codes==false || $codes['code_len']==0){
	  $this->error('下单失败');exit;
	}
	$pay = $vo['yunjiage']*$s_num;
	$mwmodel = M('member_wallet');
	$mw_data['member_id'] = $member_id;
	$mw_vo = $mwmodel->where($mw_data)->find();
	if($mw_vo['balance']<$pay){
	  $this->error('余额不够');
	}
	$mamodel = M('member_address');
	$ma_data['id'] = $address_id;
	$address = $mamodel->where($ma_data)->find();
	//生成订单
	$omodel = M('order');
	$o_data['type'] = 2;
	$o_data['source'] = 'Shoplist';
	$o_data['sourceid'] = $s_id;
	$o_data['title'] = '购买'.$vo['name'];
	$goods[] = $vo;
	$o_data['goods'] = serialize($goods);
	$order_id = build_order_no($member_id);
	$o_data['order_id'] = $order_id;
	$o_data['payment_mode'] = 1;
	$o_data['payment_company'] = '网站余额';
	$o_data['pay_order_id'] = $order_id;
	$o_data['total_price'] = $pay;
	$o_data['total_num'] = $s_num;
	$o_data['member_id'] = $member_id;
	$o_data['member_name'] = $member_name;
	$o_data['realname'] = $realname;
	$o_data['address_id'] = $address_id;
	$o_data['recipient'] = $address['name'];
	$o_data['address'] = $address['address'];
	$o_data['postcode'] = $address['postcode'];
	$o_data['tel'] = $address['mobile'];
	$o_data['remark'] = $_POST['remark'] ? $_POST['remark'] : '';
	$o_data['ip'] = $ip;
	$o_data['create_time'] = $time;
	$o_data['pay_time'] = $time;
	$o_data['order_time'] = time()+3600*24;
	$o_data['status'] = 1;
	$oid = $omodel->add($o_data);
	//echo $omodel->getlastsql();exit;
	if(!$oid){
	    $model->rollback();
	    $this->error('订单生成失败');
	    exit;		
	}
	//扣除余额
	$result = $mwmodel->where($mw_data)->setDec('balance',$pay);
	if($result){
	  //记录扣钱
	  $rmodel = M('record');
	  $rdata['member_id'] = $member_id;
	  $rdata['member_name'] = $member_name;
	  $rdata['realname'] = $realname;
	  $rdata['payment_mode'] = 1;
	  $rdata['payment_company'] = '网站余额';
	  $rdata['pay_order_id'] = build_order_no($member_id);
	  $rdata['order_id'] = $order_id;
	  $rdata['pay_type'] = 2;
	  $rdata['pay'] = $pay-2*$pay;
	  $rdata['balance'] = $mw_vo['balance']-$pay;
	  $rdata['buyer'] = $member_name;
	  $rdata['buyer'] = $member_name;
	  $rdata['ip'] = $ip;
	  $rdata['content'] = '购买'.$vo['name'].'云码'.$s_num.'个';
	  $rdata['create_time'] = $time;
	  $rdata['pay_time'] = $time;
	  $rdata['status'] = 1;
	  $rid = $rmodel->add($rdata);
	  if(!$rid){
	      $model->rollback();
	      $this->error('财务订单生成失败');
	      exit;		  
	  }
	  //记录购买云码
	  $mgrmodel = M('go_record');
	  $mgr_data['member_id'] = $member_id;
	  $mgr_data['member_name'] = $member_name;
	  $mgr_data['order_id'] = $oid;
	  $mgr_data['shopid'] = $vo['id'];
	  $mgr_data['shopname'] = $vo['name'];
	  $mgr_data['shopqishu'] = $vo['qishu'];
	  $mgr_data['goucode'] = implode(',',$codes['codes']);
	  $mgr_data['code_num'] = $codes['code_len'];
	  $timearr = explode(' ',microtime());
	  $mgr_data['ms'] = substr($timearr[0],2,3);
	  $mgr_data['create_time'] = $timearr[1];
	  //$mgr_data['create_time'] = time();
	  $result = $mgrmodel->add($mgr_data);
	  //$model->rollback();
	  //echo $mgrmodel->getlastsql();exit;
	  if(!$result){
	    $model->rollback();
	    $this->error('云码生成失败');
	    exit;			
	  }
	}
	//修改商品信息
	$result = $model->where($data)->setInc('canyurenshu',$s_num);
	if($result){
	  $result = $model->where($data)->setDec('shenyurenshu',$s_num);
	}
	if($result){
	  $model->commit();
	  echo 'ok';
	}else{
	  $model->rollback();
	  $this->error('新增失败3');
	  exit;
	}	
  }

  /**
   * 揭晓中奖信息
   */
  function jiexiao(){
	$id = 1;
    $model = M('shoplist');
	$data['id'] = $id;
    $vo = $model->field('id,name,zongrenshu,canyurenshu,lit_pic,yunjiage,qishu,status')->where($data)->find();
	if($vo['status']==2){
	  $this->error('已揭晓');
	}
	if($vo['zongrenshu']!=$vo['canyurenshu']){
	  $this->error('人数未满');
	}
	import("@.ORG.Tocode");
	$tocode = new Tocode();
	$tocode->run_tocode(time(),50,$vo['zongrenshu']);
	echo $tocode->go_code;
	dump(unserialize($tocode->go_content));
	//dump($aar);
	/*$model = M('member_go_record');
	$time = microtime();
	$timearr = explode(' ',microtime());
	$sdata['ms'] = substr($timearr[0],2,3);
	$model->where('id=1')->save($sdata);
	echo $model->getlastsql();
	*/

  }

}
?>