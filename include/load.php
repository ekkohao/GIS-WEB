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
function session_init_and_redirect($doredirect=true){
	if (!isset($_SESSION)) {
		session_start();
	}
	if(isset($_SESSION['user_id'])||!$doredirect)
		return;
	hao_redirect(SITE_URL."/login.php");
	exit();
}
function hao_redirect($location, $status = 302) {
	if ( ! $location )
		return false;
	$location = hao_sanitize_redirect($location);
	header("Location: $location", true, $status);

			return true;
}
function hao_sanitize_redirect($location) {
	$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!*\[\]()]|i', '', $location);
	$location = hao_kses_no_null($location);

	// remove %0d and %0a from location
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$location = _deep_replace($strip, $location);
	return $location;
}
function hao_kses_no_null($string) {
	$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
	$string = preg_replace('/(\\\\0)+/', '', $string);

	return $string;
}
function _deep_replace( $search, $subject ) {
	$subject = (string) $subject;

	$count = 1;
	while ( $count ) {
		$subject = str_replace( $search, '', $subject, $count );
	}
	return $subject;
}
?>