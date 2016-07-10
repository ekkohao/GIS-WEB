<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
if($_POST['mode']==0)	//添加
	$result=$mydb->add_line($_POST['line_name']);
elseif($_POST['mode']==1)	//修改
	$result=$mydb->update_line($_POST['line_id'],$_POST['line_name']);
elseif($_POST['mode']==2)	//修改
	$result=$mydb->delete_line($_POST['line_id']);
$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
$json=json_encode($data);
echo $json;
?>
