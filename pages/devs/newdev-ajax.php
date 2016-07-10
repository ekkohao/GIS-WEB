<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
$group_id = htmlspecialchars($_POST['gId']);
$lines=$mydb->get_lines_vi_gid($group_id);
if($lines&&count($lines)>0){
	$stat=count($lines);
}
else 
	$stat=0;
$data=array('stat'=>$stat,'data'=>$lines);
$json=json_encode($data);
echo $json;
?>
