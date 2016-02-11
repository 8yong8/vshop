<?php
//+---------------------------------------------------------------------
// | Author: zzy <8yong8@163.com>
// | Name :分页显示 
//+---------------------------------------------------------------------
namespace Think;
class Page{
  protected $all_page;    //总页数
  protected $nonce_page;  //当前页
  protected $url;         //URL地址
  public $parameter;      //页数跳转时要带的参数
  public $listRows;       //列表每页显示行数
  public $firstRow;       //分页起始行数
  public $style = 'digg'; //分页样式 digg,yahoo,meneame,flickr,sabrosus,scott,quotes,black,black2,black-red,grayr,yellow,jogger,starcraft2,tres,megas512,technorati,youtube,msdn,badoo,manu,green-black,viciao,yahoo2
  protected $totalRows;   //总行数
  protected $var_page = 'p';    //总页数


  function __construct($totalRows,$listRows='',$parameter=''){
	 $this->parameter = $parameter;
     $this->totalRows = $totalRows;
	 $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
     $this->listRows = !empty($listRows)?$listRows:C('LIST_NUMBERS');
     $this->all_page = ceil($this->totalRows/$this->listRows);     //总页数
     $this->nonce_page = !empty($_GET[C('VAR_PAGE')])&&($_GET[C('VAR_PAGE')] >0)?$_GET[C('VAR_PAGE')]:1;//本页
     $this->url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
	 $p = $this->var_page;
	 $parse = parse_url($this->url);
	 if(isset($parse['query'])) {
		parse_str($parse['query'],$params);
		unset($params[$p]);
		$this->url   =  $parse['path'].'?'.http_build_query($params);
	 }
	 //echo $this->url;exit;
     if(!empty($this->all_page) && $this->nonce_page>$this->all_page) {
          $this->nonce_page = $this->all_page;
	   }
     $this->firstRow = $this->listRows*($this->nonce_page-1);
   }

/**
 * 仿DZ分页模式
 * 上页，首页
 * 中间10页，本页前2页+本页+本页后7页
 * 尾页，下页
 */
 function Show(){
   if($this->all_page<1){ 
	 return $this->all_page_null();
	}
   if(0<$this->all_page && $this->all_page<=10){ 
     return $this->all_page_small();
	}
   if($this->all_page > 10){
     return $this->all_page_big();
   }	 
  }
//总页数为空
  function all_page_null(){
    return $this->fenye = "";
  }

//总页数小于10
  function all_page_small(){
    $fenye="<div class='".$this->style."'>";
    if($this->nonce_page>1){
		$fenye.="<a href =".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page-1)."> << </a>";
		}
	for($i=1;$i<=$this->all_page;$i++){
		if($this->nonce_page == $i){ 
			$fenye.= "<span class='current'>".$i."</span>\n";
					  }else{
             $fenye.= "<a href=".$this->url."&".C('VAR_PAGE')."=".$i.">".$i."</a>\n";
			 }
		}
	if($this->nonce_page!= $this->all_page){
		$fenye.= "<a href=".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page+1).">>></a>";
		}
	$fenye .= '共'.$this->all_page.'页/'.$this->totalRows.'条';
	$fenye .= "</div>";   
    return $fenye;
  }

//总页数大于10
  function all_page_big(){
		 $i=0;$kk=0;$fenye="<div class='".$this->style."'>";  
		 if($this->nonce_page>2){  //本页大于2从本页前2页开始循环
			 $i=$this->nonce_page-2;
			 }else{
				 $i=1;
				 }
		 if($this->nonce_page+7<$this->all_page){ //本页+7小于总页数，循环7次
			 $kk=$this->nonce_page+7;
			 }else{
				 $kk=$this->all_page;             //循环到总页数
				 }
	     if($this->nonce_page==1){   //本页等于1则循环10次
			 $kk=10;
			 }
        if($this->nonce_page<=3 && 1<$this->nonce_page){
	   $fenye.="<a href=".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page-1)."><<</a>\n";
	   $kk=10;
	   }
        if($this->nonce_page>3 && $this->nonce_page<=$this->all_page){
	   $fenye.="<a href=".$this->url."&".C('VAR_PAGE')."=1>1..</a>\n<a href=".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page-1)."><<</a>\n";
	   }
        if($this->nonce_page>=$this->all_page-7){
	$i=$this->all_page-9;
	$kk=$this->all_page;
	}
        for($i;$i<=$kk;$i++){
		 if($this->nonce_page==$i){ 
		 $fenye.= "<span class='current'>".$i."</span>\n";}
                 else{
                 $fenye.= "<a href=".$this->url."&".C('VAR_PAGE')."=$i>$i</a>\n";}
		 }
        if($this->nonce_page>=$this->all_page-7 && $this->nonce_page!=$this->all_page){
	$fenye.="<a href=".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page+1).">>></a>\n";
	}
	if($this->all_page-7>$this->nonce_page){
	  $fenye.="<a href=".$this->url."&".C('VAR_PAGE')."=".($this->nonce_page+1).">>></a>\n<a href=".$this->url."&".C('VAR_PAGE')."=".$this->all_page.">..".$this->all_page."</a>\n";
	  }
	$fenye .= '共'.$this->all_page.'页/'.$this->totalRows.'条';
	$fenye .= "</div>";   
    return $fenye;
  }

/**
 *下拉式分页模式
 *适合页数较少页面
 */
 function OutSelect(){
   $fenye .="<script language=javascript>";
   $fenye .="function gopage(){
          var aa=document.getElementById('fenye').selectedIndex+1;
          window.location.href='{$this->url}&page='+aa;
		  }";
   $fenye .= "</script>";
   $fenye .= "<select onchange='gopage()' id='fenye' style='width:50px'>";
   for($i=1;$i<=$this->all_page;$i++){
	if($i==$this->nonce_page){
	$fenye .= "<option value={$i} selected='selected'>{$i}</option>";
	}else{
    $fenye .= "<option value={$i}>{$i}</option>";
	}
   }
  $this->fenye = $fenye;
 }

}
?>