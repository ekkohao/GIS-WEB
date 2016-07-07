<?php
	require 'setting.php';
	session_init_and_redirect();
	if(!isset($_GET['page']))
		$_GET['page']='home';
	require ABSPATH.'/pages/'.$_GET['page'].'.php';
?>
