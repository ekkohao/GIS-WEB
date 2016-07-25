<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
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
								<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th><th>杆塔与线路</th></tr>
							</tr></thead>
							<tbody>

							</tbody>
							<tfoot><tr>
								<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th>
							<th>杆塔与线路</th></tr></tfoot>
						</table>
			
	    </div>
	    <?php 
	    	global $mydb;
	    	$last_alarm_id=$mydb->get_last_alarm_id();
	    ?>
		<input id="inputAlarmId" type="hidden" value="<?php echo $last_alarm_id;?>" />
		<audio id="warning_mp3" loop="loop">
			  <source src="audios/warning.mp3" type="audio/mpeg">
			  您的浏览器不支持我们的播放器。
		</audio>
		<?php get_footer();?>
    
	</body>
	<!-- 百度地图api -->
	<script type="text/javascript" src="js/monitor.js">></script>
</html>