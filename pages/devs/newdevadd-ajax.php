<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;

//$data=null;
$errorsinfo=null;

if($_POST['mode']==0)
	$mydb->add_dev($_POST['dev_num'],$_POST['dev_phase'],$_POST['group_id'],$_POST['line_id']);
elseif($_POST['mode']==1)
	$mydb->update_dev($_POST['dev_id'],$_POST['dev_num'],$_POST['dev_phase'],$_POST['group_id'],$_POST['line_id']);
elseif($_POST['mode']==2)
	$mydb->delete_dev($_POST['dev_id']);

$errorsinfo=$mydb->__get('last_errors');

$jsonpre=array('errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>
