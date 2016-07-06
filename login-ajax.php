<?php
require 'setting.php';
session_init_and_redirect(false);
header("Content-type:application/json; charset=UTF-8");
require_db();
$user_name = htmlspecialchars($_POST['u']);
$passwd = htmlspecialchars($_POST['p']);
$user_id=$mydb->get_user_id_vie_pwd($user_name, $passwd);
if($user_id){
	$_SESSION['user_id']=$user_id;
	$stat=1;
}else
	$stat=0;
$data=array('stat'=>$stat);
$json=json_encode($data);
echo $json;
?>