<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$dev_id=$mydb->get_dev_id($_POST['devId']);
$dev=$mydb->get_dev($dev_id);
$data=$mydb->get_dev_alarms($dev_id,$_POST['dateFrom'].':00',$_POST['dateTo'].':00');
$jsonpre=array('data'=>$data,'dev'=>$dev);
$json=json_encode($jsonpre);
echo $json;
