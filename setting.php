<?php
if( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__));
function load_conf(){
	$pos=strpos($_SERVER['HTTP_HOST'],'kedinggis.com');
	if($pos>0&&substr($_SERVER['HTTP_HOST'], 0,$pos)!='www.'){
		$file_path=ABSPATH.'/conf/conf-'.substr($_SERVER['HTTP_HOST'], 0,$pos-1).'.php';
		if(file_exists($file_path))
			require_once $file_path;
		return;
	}
	require_once ABSPATH.'/conf.php';
}

if(!isset($setting_load))
	$setting_load=false;
if(!$setting_load){

	load_conf();
	require_once ABSPATH.'/include/functions.php';
	require_once ABSPATH.'/include/load.php';
	require_once ABSPATH.'/include/db.class.php';
	
	$setting_load=true;
	/*设置时区*/
	if(function_exists('date_default_timezone_set'))
		date_default_timezone_set('PRC');
}

?>