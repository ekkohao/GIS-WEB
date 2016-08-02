
<!DOCTYPE HTML>
<html lang="zh_CN">
<?php get_head();?>
<body>

	<?php get_header();?>	
	
	<div id="bodier" class="">
		<div class="container">	
			<?php 
			global $mydb;
			$alarms=$mydb->get_alarms();

			if($alarms&&count($alarms)>0){
				?>
				<?php if(is_current_user_can_see(3)){ ?>
					<div class="btn-group">
					
						<a id="output_dev" href="index.php?page=excel&action=outputalarms" class="btn btn-success">导出报警日志</a>
					
					</div>
				<?php } ?>
				<table class="table striped table-alarmslist">
					<thead><tr>
						<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th><th>所在杆塔与线路</th>
					</tr></thead>
					<tbody>
						<?php

						foreach ($alarms as $alarm) {
							$html='';
							$html.='<tr><td><a href="index.php?page=charts&devnum='.$alarm['dev_number'].'" target="_blank">'.$alarm['dev_number'].'</a></td><td>'.$alarm['action_time'].'</td><td>'.$alarm['action_num'].'</td><td>'.$alarm['i_num'].'</td><td>'.$alarm['tem'].'</td><td>'.$alarm['hum'].'</td><td>'.$alarm['group_loc_name'].'<br />'.$alarm['line_name'].'-'.$alarm['dev_phase'].'</td></tr>';
							echo $html;
						}
						?>
					</tbody>
					<tfoot><tr>
						<th>报警设备</th><th>报警时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th><th>所在杆塔与线路</th>
					</tr></tfoot>
				</table>
				<?php 
				$pgn_html=$mydb->__get("pgn_html");
				if(!empty($pgn_html))
					echo $pgn_html;
			}
			else{
				?>
				<div class="widget">
					<h3>NOT FOUND</h3>
					<p><h2>&nbsp;&nbsp;报警日志为空</h2></p>
				</div>
				<?php 
			}
			?>
		</div>
	</div>
	
	<?php get_footer();?>
	
</body>
</html>