<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");
require_db();
$line_id=0;
if($_POST['mode']==0){//添加
	$result=$mydb->add_line($_POST['line_name']);
	if($result['err_count']==0)
		$line_id=$mydb->get_line_id_vi_name($_POST['line_name']);
	$data=array('stat'=>$result['err_count'],'data'=>$result['err'],'line_id'=>$line_id);
}
elseif($_POST['mode']==1){//修改
	$result=$mydb->update_line($_POST['line_id'],$_POST['line_name']);
	$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
}
else{//修改
	$result=$mydb->delete_line($_POST['line_id']);
	$data=array('stat'=>$result['err_count'],'data'=>$result['err']);
}

$json=json_encode($data);
echo $json;
?>
