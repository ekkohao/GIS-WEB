<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;

//$data=null;
$errorsinfo=null;
if($_POST['mode']==0)
	$mydb->add_site($_POST['site_name'],$_POST['site_remark'],$_POST['dbhost'],$_POST['dbname'],$_POST['dbuser'],$_POST['dbpasswd'],$_POST['is_use_default']);
elseif($_POST['mode']==1)
	$mydb->update_site($_POST['site_id'],$_POST['site_name'],$_POST['site_remark'],$_POST['dbhost'],$_POST['dbname'],$_POST['dbuser'],$_POST['dbpasswd'],$_POST['is_use_default']);
elseif($_POST['mode']==2)
	$mydb->delete_site($_POST['site_id']);

$errorsinfo=$mydb->__get('last_errors');

$jsonpre=array('errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>