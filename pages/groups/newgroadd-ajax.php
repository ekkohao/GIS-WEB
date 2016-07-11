<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");

global $mydb;
$coor=(isset($_POST['coor']))?explode(",",$_POST['coor']):null;
$data=null;
$errorsinfo=null;

if($_POST['mode']==0){
	$isadd=$mydb->add_group($_POST['gro_name'],$_POST['gro_loc'],$_POST['line_id1'],$_POST['line_id2'],$coor[0],$coor[1]);
	$errorsinfo=$mydb->__get('last_errors');
	if($isadd)
		$data['gro_id']=$mydb->get_group_id($_POST['gro_name'],$_POST['gro_loc']);

}
elseif($_POST['mode']==1){
	$mydb->update_group($_POST['gro_id'],$_POST['gro_name'],$_POST['gro_loc'],$_POST['line_id1'],$_POST['line_id2'],$coor[0],$coor[1]);
	$errorsinfo=$mydb->__get('last_errors');
}
elseif($_POST['mode']==2){
	$mydb->delete_group($_POST['gro_id']);
	$errorsinfo=$mydb->__get('last_errors');
}

$jsonpre=array('data'=>$data,'errorsinfo'=>$errorsinfo);
$json=json_encode($jsonpre);
echo $json;
?>
