<?php
class Page{
  public $all_page;    //总页数
  public $nonce_page;  //当前页
  public $turl;         //URL地址
  public $wurl;         //URL地址
  public $fenye;       //分页
  public $styles = 'dc_page_on';  //默认选中样式

  function __construct($get_page,$all_page,$turl,$wurl){
         $this->all_page=$all_page;
         $this->nonce_page=$get_page;
         $this->turl=$turl;
		 $this->wurl=$wurl;
   }

/**
 * 仿PHPchina分页模式
 * 上页，首页
 * 中间10页，本页前2页+本页+本页后7页
 * 尾页，下页
 */
 function Out(){
   if($this->all_page<1){ 
	 $this->all_page_null();
	}
   if(0<$this->all_page && $this->all_page<=10){ 
     $this->all_page_small();
	}
   if($this->all_page > 10){
     $this->all_page_big();
   }	 
  }
//-------------总页数为空---------------------------//
  function all_page_null(){
    $this->fenye = "";
  }

//-----------总页数小于10----------------------------//
  function all_page_small(){

    if($this->nonce_page>1){
		//$fenye.="<a href =".$this->turl.($this->nonce_page-1).$this->wurl."> << </a>";
	}
	for($i=1;$i<=$this->all_page;$i++){
		
		if($this->nonce_page == $i){ 
			$fenye.= "<span class='".$this->styles."'>".$i."</span>\n";
		}else{
             $fenye.= "<span><a href=".$this->turl.$i.$this->wurl.">".$i."</a></span>\n";
			 }
		}
	if($this->nonce_page!= $this->all_page){
		//$fenye.= "<a href=".$this->turl.($this->nonce_page+1).$this->wurl.">>></a>";
		}
    $this->fenye = $fenye;
  }

//---------------总页数大于10------------------------//
  function all_page_big(){
		 $i=0;$kk=0;$fenye="";  
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
	   //$fenye.="<a href=".$this->url."&page=".($this->nonce_page-1)."><<</a>\n";
	   $kk=10;
	   }
        if($this->nonce_page>3 && $this->nonce_page<=$this->all_page){
	   //$fenye.="<a href=".$this->url."&page=1>1..</a>\n<a href=".$this->url."&page=".($this->nonce_page-1)."><<</a>\n";
	   }
        if($this->nonce_page>=$this->all_page-7){
	$i=$this->all_page-9;
	$kk=$this->all_page;
	}
        for($i;$i<=$kk;$i++){
		 if($this->nonce_page==$i){ 
		 $fenye.= "<span class='".$this->styles."'>".$i."</span>\n";}
                 else{
                 $fenye.= "<span><a href=".$this->turl.$i.$this->wurl.">$i</a></span>\n";}
		 }
        if($this->nonce_page>=$this->all_page-7 && $this->nonce_page!=$this->all_page){
	//$fenye.="<a href=".$this->url."&page=".($this->nonce_page+1).">>></a>\n";
	}
	if($this->all_page-7>$this->nonce_page){
	  //$fenye.="<a href=".$this->url."&page=".($this->nonce_page+1).">>></a>\n<a href=".$this->url."&page=".$this->all_page.">..".$this->all_page."</a>\n";
	  }      
    $this->fenye=$fenye;
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