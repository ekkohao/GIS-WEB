<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$group_id = htmlspecialchars($_POST['gro_id']);
$lines=$mydb->get_lines_on_group($group_id);
if($lines&&count($lines)>0){
	$linescount=count($lines);
}
else 
	$linescount=0;
$data=array('linescount'=>$linescount,'lines'=>$lines);
$jsonpre=array('data'=>$data);
$json=json_encode($jsonpre);
echo $json;
?>
