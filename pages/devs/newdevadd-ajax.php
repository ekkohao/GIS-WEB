<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
$dev_num= $_POST['dev_num'];
$dev_name= $_POST['dev_name'];
$dev_phase= $_POST['dev_phase'];
$group_id = $_POST['group_id'];
$line_id=$_POST['line_id'];
$result=$mydb->add_dev($dev_num,$dev_name,$dev_phase,$group_id,$line_id);
$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
$json=json_encode($data);
echo $json;
?>
