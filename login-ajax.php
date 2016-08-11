<?php
require 'setting.php';


session_init_and_redirect(false);
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$user_name = htmlspecialchars($_POST['u']);
$passwd = htmlspecialchars($_POST['p']);
$user_id=$mydb->get_user_id($user_name, $passwd);
if($user_id>0){
	$_SESSION['user_id']=$user_id;
	$mydb->update_user_last_login_time($user_id);
	$stat=1;
}else
	$stat=0;
$data=array('stat'=>$stat);
$json=json_encode($data);
echo $json;
?>