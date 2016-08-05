<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$data=null;
$errorsinfo=null;
if($_POST['mode']==0){//添加
	$isadd=$mydb->add_usergroup($_POST['user_gname']);
	$errorsinfo=$mydb->__get('last_errors');
	if($isadd)
		$data['user_gid']=$mydb->get_usergid($_POST['user_gname']);
}
elseif($_POST['mode']==1){//修改
	$mydb->update_usergroup($_POST['user_gid'],$_POST['user_gname']);
	$errorsinfo=$mydb->__get('last_errors');
}
else{//修改
	$mydb->delete_usergroup($_POST['user_gid']);
	$errorsinfo=$mydb->__get('last_errors');
}
$jsonpre=array('data'=>$data,'errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>