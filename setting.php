<?php

if( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__));
if(!isset($setting_load))
	$setting_load=false;
if(!$setting_load){
	require_once ABSPATH.'/conf.php';
	require_once ABSPATH.'/include/functions.php';
	require_once ABSPATH.'/include/load.php';
	require_once ABSPATH.'/include/db.class.php';
	
	$setting_load=true;
	/*设置时区*/
	if(function_exists('date_default_timezone_set'))
		date_default_timezone_set('PRC');
}

?>