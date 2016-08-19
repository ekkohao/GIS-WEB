<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$data=null;
$errorsinfo=null;
if($_POST['mode']==0){//添加
	$isadd=$mydb->add_line($_POST['line_name']);
	$errorsinfo=$mydb->__get('last_errors');
	if($isadd)
		$data['line_id']=$mydb->get_line_id($_POST['line_name']);
}
elseif($_POST['mode']==1){//修改
	$mydb->update_line($_POST['line_id'],$_POST['line_name']);
	$errorsinfo=$mydb->__get('last_errors');
}
else{//修改
	$mydb->delete_line($_POST['line_id']);
	$errorsinfo=$mydb->__get('last_errors');
}
$jsonpre=array('data'=>$data,'errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>
