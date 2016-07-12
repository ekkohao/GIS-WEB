<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");

global $mydb;

$data=null;
$errorsinfo=null;

if($_POST['mode']==0){
	$isadd=$mydb->add_user($_POST['user_name'],$_POST['passwd'],$_POST['user_role'],$_POST['user_phone'],$_POST['user_email'],$_POST['is_send']);
	$errorsinfo=$mydb->__get('last_errors');
}
elseif($_POST['mode']==1){
	if($_POST['passwd']=="")
		$mydb->update_user_nopwd($_POST['user_id'],$_POST['user_name'],$_POST['user_role'],$_POST['user_phone'],$_POST['user_email'],$_POST['is_send']);
	else
		$mydb->update_user($_POST['user_id'],$_POST['user_name'],$_POST['passwd'],$_POST['user_role'],$_POST['user_phone'],$_POST['user_email'],$_POST['is_send']);
	$errorsinfo=$mydb->__get('last_errors');
}
elseif($_POST['mode']==2){
	$mydb->delete_user($_POST['user_id']);
	$errorsinfo=$mydb->__get('last_errors');
}

$jsonpre=array('data'=>$data,'errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>