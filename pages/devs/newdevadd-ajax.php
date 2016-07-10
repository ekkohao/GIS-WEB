<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
if($_POST['mode']==1)
	$result=$mydb->update_dev($_POST['dev_id'],$_POST['dev_num'],$_POST['dev_phase'],$_POST['group_id'],$_POST['line_id']);
elseif($_POST['mode']==2)
	$result=$mydb->delete_dev($_POST['dev_id']);
else
	$result=$mydb->add_dev($_POST['dev_num'],$_POST['dev_phase'],$_POST['group_id'],$_POST['line_id']);
$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
$json=json_encode($data);
echo $json;
?>
