<!DOCTYPE HTML>
<html lang="zh_CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="stylesheet" id="bootstrap-css" href="http://apps.bdimg.com/libs/bootstrap/3.2.0/css/bootstrap.css?ver=9.0.0" type="text/css" media="all">
	    <link rel="stylesheet" id="fontawesome-css" href="http://apps.bdimg.com/libs/fontawesome/4.2.0/css/font-awesome.min.css?ver=9.0.0" type="text/css" media="all">
	    <link href="css/main.css" rel="stylesheet">
	    <link href="css/datetimepick.css" rel="stylesheet">
	    <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js"></script>
	    <title>科鼎地理信息服务系统</title>
	</head>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
				<?php 		
				$actions=array("lineslist");
				hao_load_in('action',$actions,'pages/lines');
				?>
			</div>
	    </div>
		
		<?php get_footer();?>
    
	</body>
</html>