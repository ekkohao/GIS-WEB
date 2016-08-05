<?php
function is_current_user_can_see($role){
	global $__USER;
	if($__USER['user_role']>$role)
		return false;
	return true;
}
function current_user_role_identify($role){
	global $__USER;
	if($__USER['user_role']>$role){
		hao_redirect("index.php?page=home",404);
		exit();
	}
	return;
}
//加载footer.php
function get_footer($file=null){
	if ($file)
		require_once ABSPATH."/".$file;
	else
		require_once ABSPATH.'/footer.php';
}
//加载header.php
function get_header($file=null){
	if ($file)
		require_once ABSPATH."/".$file;
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
function hao_require($filepath){
	if(file_exists($filepath))
		require $filepath;
	else{
		hao_redirect(SITE_URL."/404.php",404);
		exit();
	}
}
function hao_require_once($filepath){
	if(file_exists($filepath))
		require_once $filepath;
	else{
		header("status: 404 Not Found");
		hao_redirect(SITE_URL."/404.php",404);
		exit();
	}
}
//单页面入后函数，$keyname为url变量名，arr位变量取值范围，arr[0]位默认值
function hao_load_in($keyname,$arr,$dir){
	if(!isset($_GET[$keyname])||!in_array($_GET[$keyname],$arr))
		$_GET[$keyname]=$arr[0];
	hao_require(ABSPATH.'/'.$dir.'/'.$_GET[$keyname].'.php');
}

function get_head(){
	$html='<head>';
	$html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	$html.='<meta name="viewport" content="width=device-width, initial-scale=1">';
	$html.='<link rel="icon" href="favicon.ico" type="image/x-icon"/>';
	$html.='<link rel="bookmark" href="favicon.ico"/>';
	$html.='<link rel="stylesheet" href="css/bootstrap.css" type="text/css" media="all">';
	$html.='<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" media="all">';
	$html.='<link rel="stylesheet" href="css/main.css">';
	$html.='<script type="text/javascript" src="js/jquery.js"></script>';
	$html.='<title>线路型避雷器在线监测系统</title>';
	$html.='</head>';
	echo $html;
}
function get_ip(){
	$ip="未知";
	if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
	elseif ( !empty($_SERVER['REMOTE_ADDR']))
		$ip = $_SERVER['REMOTE_ADDR']; 
	elseif(!empty($_SERVER["HTTP_CLIENT_IP"]))
  		$ip = $_SERVER["HTTP_CLIENT_IP"];
	return $ip;
}
?>