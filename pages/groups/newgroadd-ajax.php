<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
$gro_name= $_POST['gro_name'];
$gro_loc= $_POST['gro_loc'];
$line_id1= $_POST['line_id1'];
$line_id2 = $_POST['line_id2'];
$coor=explode(",",$_POST['coor']);
$result=$mydb->add_group($gro_name,$gro_loc,$line_id1,$line_id2,$coor[0],$coor[1]);
$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
$json=json_encode($data);
echo $json;
?>
