<?php
	require 'setting.php';
	session_init_and_redirect();
	$pages=array("home","devs","groups","lines","users","monitor","excel","charts");
	hao_load_in('page',$pages,'pages');
?>
