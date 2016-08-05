<?php
function require_db() {
	global $mydb;
	require_once ABSPATH.'/include/db.class.php';
	if ( !empty( $mydb ) )
		return;
	$mydb = new db();
}
//载入当前用户变量
function load_current_user(){
	global $__USER,$mydb;
	if (!isset($_SESSION)) {
		session_start();
	}
	if ( isset( $__USER ) )
		return;	
	if(isset($_SESSION['user_id']))
		$__USER = $mydb->get_user($_SESSION['user_id']);
}

require_db();
load_current_user();
?>