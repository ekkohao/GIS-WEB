<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");

global $mydb;
$alarm=$dev=$group=null;
$has_alarm=0;
$last_alarm_id=$mydb->get_last_alarm_id();
if($last_alarm_id>$_POST['now_id']){
	$alarm=$mydb->get_alarm($last_alarm_id);
	if($alarm)
		$dev=$mydb->get_dev($alarm['dev_id']);
	if($dev)
		$group=$mydb->get_group($dev['group_id']);
	$has_alarm=1;
}
$jsonpre=array('has_alarm'=>$has_alarm,'data'=>array('alarm'=>$alarm,'dev'=>$dev,'group'=>$group));
$json=json_encode($jsonpre);
echo $json;
?>