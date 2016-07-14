<?php
require 'setting.php';


session_init_and_redirect(false);
header("Content-type:application/json; charset=UTF-8");
global $mydb;
$user_name = htmlspecialchars($_POST['u']);
$passwd = htmlspecialchars($_POST['p']);
$user=$mydb->get_user_vi_name_pwd($user_name, $passwd);
if($user){
	$_SESSION['user_id']=$user['user_id'];
	$mydb->update_user_last_login_time($user['user_id']);
	$stat=1;

}else
	$stat=0;
$data=array('stat'=>$stat);
$json=json_encode($data);
echo $json;
?>