<?php

if( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__));
if(!isset($setting_load))
	$setting_load=false;
if(!$setting_load){
	require_once ABSPATH.'/conf.php';
	require_once ABSPATH.'/include/db.php';
	require_once ABSPATH.'/include/load.php';
	require_db();
	$setting_load=true;
}

?>