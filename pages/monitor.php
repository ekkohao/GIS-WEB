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
	
	    <div id="bodier" class="block-center">
	        	
	        	<div class="widget" style="width:96%;margin:0 auto">
	        		<h3 class="clearfix">在线监测<button class="pull-right btn btn-success btn-listen listening">正在监听</button></h3>
					<div id="map-box">
						
					</div>
				</div>
				<div class="widget widget-alarmslist tohide" style="width:96%;margin:0 auto">
				  	<h3>最近报警信息</h3>

				  		<table class="table striped table-alarmslist">
							<thead><tr>
								<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th>
							</tr></thead>
							<tbody>

							</tbody>
							<tfoot><tr>
								<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th>
							</tr></tfoot>
						</table>
			
	    </div>
	    <?php 
	    	global $mydb;
	    	$last_alarm_id=$mydb->get_last_alarm_id();
	    ?>
		<input id="inputAlarmId" type="hidden" value="<?php echo $last_alarm_id;?>" />
		<?php get_footer();?>
    
	</body>
	<!-- 百度地图api -->
	<script type="text/javascript" src="js/monitor.js">></script>
</html>