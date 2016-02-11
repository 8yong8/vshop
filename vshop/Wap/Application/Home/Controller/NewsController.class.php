<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends PublicController {

  /**
   * 首页
   */
  public function index(){
	$model = M('FlagList');
	$fl_data['fid'] = 14;
	$ads = $model->field('b.id,title,shorttitle,lit_pic2 as lit_pic,content,b.create_time')->alias('a')->join('`zy_article` as b on a.sourceid=b.id')->where($fl_data)->limit(4)->order('a.orderindex asc,b.id desc')->select();
	$this->assign('ads',$ads);
	foreach($ads as $ad){
	  $ids[] = $ad['id'];
	}
	//分类
	$type_model = M('classify');
	$type_data['id'] = array('gt',1);
	$type_data['pid'] = 0;
	$types = $type_model->where($type_data)->select();
	$this->assign('types',$types);
	//列表
    $model = M('article');
	$data = $this->_search();
	//dump($data);
	$data['id'] = array('not in',$ids);
	$page = $_GET['p'] ? $_GET['p'] : 1;
	$order = 'create_time desc,id desc';
	$count = $model->where($data)->count();
	//echo $model->getlastsql();
	import("@.ORG.Util.Page");
	$page_size = 12;
	$p = new Page ( $count, $page_size );
	$page_count = ceil($count / $page_size);
	$pageno = $_GET['p'] ? $_GET['p'] : 1;
	$offset = ($pageno - 1) * $page_size;
	$list = $model->field('id,lit_pic,title,content,create_time')->where($data)->order($order)->limit($offset.','.$page_size)->select();
	//echo $model->getlastsql();
	//dump($list);
	$page = $p->show ();
	$this->assign('list', $list);
	$this->assign('p',$pageno);
	$this->assign ("page",$page );

	//dump($page);exit;
	$this->assign('count',$count);
	$this->assign('page_count', $page_count);
	$title = '艺术品咨询- '.$this->configs['webname'];
	$keywords = $product['keywords'];
	$description = $product['description'];
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
    $this->display();
  }

  /**
   * 搜索条件
   */
  public function _search(){
	$map = array ();
	if($_GET['tid']){
	  $map['top_cid'] = $_GET['tid'];
	}else{
	  $map['top_cid'] = array ('neq',1);
	}
	if($_GET['keyword']){
	  $map['title'] = array('like','%'.$_GET['keyword'].'%');
	  $this->assign('keyword',$_GET['keyword']);
	}
	return $map;
  }

  /**
   * 详情页
   */
  public function detail(){
    $model = M('article');
	$data['id'] = $_GET['id'];
	$vo = $model->where($data)->find();
	if(!$vo){
	  $this->error('不存在!');
	}
	$model->where($data)->setInc('pv_count',1);
	$this->assign('vo',$vo);
	//下一篇
	//$data['top_cid'] = $vo['top_cid'];
	$data['id'] = array('gt',$_GET['id']);
	$next = $model->field('id,title')->where($data)->order('id asc')->find();
	$this->assign('next',$next);
	//上一篇
	$data['id'] = array('lt',$_GET['id']);
	$prev = $model->field('id,title')->where($data)->order('id desc')->find();
	$this->assign('prev',$prev);

	$title = $vo['title'].'- 新闻资讯- '.C('site_name');
	$keywords = $product['keywords'];
	$description = $product['description'];
	$this->assign('title', $title);
	$this->assign('keywords', $keywords);
	$this->assign('description', $description);
	$this->display();
  }

}