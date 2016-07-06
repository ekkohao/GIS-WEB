<?php
	require 'setting.php';
	session_init_and_redirect();
	//$_SESSION['user_id']=2;
	header("Content-type:text/html;charset=utf-8");
	
	require_db();
	
	
	echo $mydb->__get("is_use_mysqli");
	//$mydb->add_line("高新线");
	//$mydb->add_group("1号杆塔", "圣惠路", "高新线");
	//$mydb->add_dev("0912345688", "测试设备", "A相", "1号杆塔", "圣惠路", "高新线");
	$mydb->get_dev_info("0912345678");
	echo $mydb->get_user_id_vie_pwd("admin", "111111");
	echo "<pre>";
	print_r($_SESSION);
	//print_r($mydb->__get('dbcon'));
	//print_r($mydb->get_dev_info("0912345678"));
	
	//print_r($mydb->__get('col_info'));
	echo "</pre>";
?>
<a href="<?php echo SITE_URL; ?>/login.php?action=logout">登出</a>