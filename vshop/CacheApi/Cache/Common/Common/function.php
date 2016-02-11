<?php 
//判断内网ip
function check_ip(){
  //$ip = _get_ip();
  $ip = '192.168.1.115';
  $ip = ip2long($ip);
  $net_a = ip2long('10.255.255.255') >> 24; //A类网预留ip的网络地址
  $net_b = ip2long('172.31.255.255') >> 20; //B类网预留ip的网络地址
  $net_c = ip2long('192.168.255.255') >> 16; //C类网预留ip的网络地址
  return $ip >> 24 === $net_a || $ip >> 20 === $net_b || $ip >> 16 === $net_c;
}
?>