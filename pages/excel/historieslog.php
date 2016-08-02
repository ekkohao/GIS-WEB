
<!DOCTYPE HTML>
<html lang="zh_CN">
<?php get_head(); ?>
<body>

	<?php get_header();?>	
	
	<div id="bodier" class="">
		<div class="container">	
		<p><small>例行上报日志列表</small></p>
			<?php 
			global $mydb;
			$histories=$mydb->get_histories();

			if($histories&&count($histories)>0){
				?>
				
				<table class="table striped table-historieslist">
					<thead><tr>
						<th>设备编号</th><th>上报时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th><th>所在杆塔与线路</th>
					</tr></thead>
					<tbody>
						<?php

						foreach ($histories as $history) {
							$html='';
							$html.='<tr><td><a href="index.php?page=charts&devnum='.$history['dev_number'].'" target="_blank">'.$history['dev_number'].'</a></td><td>'.$history['action_time'].'</td><td>'.$history['action_num'].'</td><td>'.$history['i_num'].'</td><td>'.$history['tem'].'</td><td>'.$history['hum'].'</td><td>'.$history['group_loc_name'].'<br />'.$history['line_name'].'-'.$history['dev_phase'].'</td></tr>';
							echo $html;
						}
						?>
					</tbody>
					<tfoot><tr>
						<th>设备编号</th><th>上报时间</th><th>动作次数</th><th>泄漏电流</th><th>温度</th><th>湿度</th><th>所在杆塔与线路</th>
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
					<p><h2>&nbsp;&nbsp;历史列表为空</h2></p>
				</div>
				<?php 
			}
			?>
		</div>
	</div>
	
	<?php get_footer();?>
	
</body>
</html>