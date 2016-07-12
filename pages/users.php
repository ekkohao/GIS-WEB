<?php
	current_user_role_identify(2);
?>
<!DOCTYPE HTML>
<html lang="zh_CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="css/bootstrap.css" rel="stylesheet">
	    <link href="css/main.css" rel="stylesheet">
	    <link href="css/datetimepick.css" rel="stylesheet">
	    <script type="text/javascript" src="js/jquery.js"></script>
	    <title>科鼎地理信息服务系统</title>
	</head>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
				<?php 
				$actions=array("userslist","newuser","edituser");
				hao_load_in('action',$actions,'pages/users');
				?>
			</div>
	    </div>
		
		<?php get_footer();?>
	
    
	</body>
</html>