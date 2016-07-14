
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
			global $mydb;
			$alarms=$mydb->get_alarms();

			if($alarms&&count($alarms)>0){
				?>
				<table class="table striped table-alarmslist">
					<thead><tr>
						<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th>
					</tr></thead>
					<tbody>
						<?php

						foreach ($alarms as $alarm) {
							$html='';
							$html.='<tr><td>'.$alarm['dev_number'].'</td><td>'.$alarm['action_time'].'</td><td>'.$alarm['action_num'].'</td><td>'.$alarm['i_num'].'</td><td>'.$alarm['tem'].'</td><td>'.$alarm['hum'].'</td></tr>';
							echo $html;
						}
						?>
					</tbody>
					<tfoot><tr>
						<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th>
					</tr></tfoot>
				</table>
				<?php 
			}
			else{
				?>
				<div class="widget">
					<h3>NOT FOUND</h3>
					<p><h2>&nbsp;&nbsp;线路列表为空</h2></p>
				</div>
				<?php 
			}
			?>
		</div>
	</div>
	
	<?php get_footer();?>
	
</body>
</html>