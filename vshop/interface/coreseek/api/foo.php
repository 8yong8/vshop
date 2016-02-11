<form name='myform' method='post' 
	  action='foo.php'>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right">id：</td>
            <td><input type="text" name="id" value="" /><input type="submit" value="提交"/></td>
          </tr>
        </table>
</form>
<?php
//header("Content-Type:text/html;charset=gbk");
header("Content-Type:text/html;charset=utf-8");
error_reporting(5);
require './sphinxapi.php';
if($_POST['id']){
  //152011
  $cl = new SphinxClient();
  $cl->SetServer('61.152.154.43',9312);
  $id = $_POST['id'] = 161218;
  $cl->UpdateAttributes( "article_index,article_zl_index", array("status"), array($id=>array(1)) );
//UpdateAttributes
  dump($_POST);
}
//require '../function.php';
$cl = new SphinxClient();
$cl->SetServer('61.152.154.37',9312);
$cl->SetArrayResult(true);
$cl->SetConnectTimeout(1);
//$cl->SetFieldWeights('title');
//$cl->SetMaxQueryTime(100);
//$cl->SetSortMode(SPH_SORT_RELEVANCE,'id DESC,create_time ASC');
//false,查到
//$cl->SetFilter('cid',array(496),false);
//$cl->SetFilter('status',array(0),true);

$cl->SetSortMode(SPH_SORT_EXTENDED, 'id desc');
//$cl->SetGroupDistinct ( 'title' );
$cl->SetLimits(0,8);
//$words = iconv("GBK", "UTF-8", "愚人节最佳蛊惑爆料");
$cl->SetMatchMode (SPH_MATCH_EXTENDED2);
//$cl->min_prefix_len = 2;//最小索引前缀长度
$cl->min_infix_len = 2;//最小索引中缀长度
$cl->infix_fields = 'title';
$words = "酒店|吊灯|卫浴";
//$cl->AddQuery( "吊灯", "article_index,article_zl_index" );
$cl->AddQuery( "地板十大品牌", "article_index,article_zl_index" );
$result = $cl->RunQueries();
//$result = $cl->Query('卫浴','article_index,article_zl_index');
//echo $cl->GetLastError();

if(!$result){
 $cl->SetServer('localhost',9313);
 $result = $cl->RunQueries();
}

dump($result);exit;
var_dump($result);
// 浏览器友好的变量输出
function dump($var, $echo=true,$label=null, $strict=true){
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES)."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
//152011
//D:\wamp\bin\mysql\mysql5.1.36\bin/mysql.exe mysql -u137137home -psdZbf3o1 -Dtest -e 'REPLACE INTO sph_counter SELECT 1, MAX(id) FROM documents'
?>