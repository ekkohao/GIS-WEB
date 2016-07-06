<?php
header("Content-type:application/json; charset=UTF-8");
require 'setting.php';
require_db();
$user_name = $_POST['u'];
$passwd = $_POST['p'];
$stat=$mydb->get_user_id_vie_pwd($user_name, $passwd)?1:0;
//$stat=$mydb->get_line_id('高线');
$data=array('stat'=>$stat);
$json=json_encode($data);
echo $json;
?>