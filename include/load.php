<?php
function require_db() {
	global $mydb;
	require_once( ABSPATH . '/include/db.php' );
	if ( isset( $db ) )
		return;
	$mydb = new db();
}
function get_footer($file=null){
	if ($file)
		require(ABSPATH."/".$file);
	else
		require_once ABSPATH.'/footer.php';
}
function get_header($file=null){
	if ($file)
		require(ABSPATH."/".$file);
	else
		require_once ABSPATH.'/header.php';
}
?>