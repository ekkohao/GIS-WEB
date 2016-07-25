<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");

global $mydb;

$data=null;
$errorsinfo=null;

$mydb->update_userself($_POST['user_id'],$_POST['old_name'],$_POST['user_name'],$_POST['oldpasswd'],$_POST['passwd'],$_POST['user_phone'],$_POST['user_email'],$_POST['is_send']);
$errorsinfo=$mydb->__get('last_errors');

$jsonpre=array('data'=>$data,'errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>