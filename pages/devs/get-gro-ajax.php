<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$group_id = $_POST['gro_id'];
$group=$mydb->get_group($group_id); 
$jsonpre=array('data'=>$group);
$json=json_encode($jsonpre);
echo $json;
?>
