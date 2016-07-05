<?php
function require_db() {
	global $mydb;
	require_once( ABSPATH . '/include/db.php' );
	if ( isset( $db ) )
		return;
	$mydb = new db();
}
?>