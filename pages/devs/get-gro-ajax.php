<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$group_id = htmlspecialchars($_POST['gro_id']);
$group=$mydb->get_group($group_id); 
$data=array('stat'=>0,'data'=>$group);
$json=json_encode($data);
echo $json;
?>
