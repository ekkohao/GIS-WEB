<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
global $mydb;
if(isset($_POST['coor']))
	$coor=explode(",",$_POST['coor']);
if($_POST['mode']==1)
	$result=$mydb->update_group($_POST['gro_id'],$_POST['gro_name'],$_POST['gro_loc'],$_POST['line_id1'],$_POST['line_id2'],$coor[0],$coor[1]);
elseif($_POST['mode']==2)
	$result=$mydb->delete_group($_POST['gro_id']);
else
	$result=$mydb->add_group($_POST['gro_name'],$_POST['gro_loc'],$_POST['line_id1'],$_POST['line_id2'],$coor[0],$coor[1]);
$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
$json=json_encode($data);
echo $json;
?>
