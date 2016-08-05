
<!DOCTYPE HTML>
<html lang="zh_CN">
<?php get_head();?>
<body>

	<?php get_header();?>	
	
	<div id="bodier" class="">
		<div class="container">	
			<?php 
			global $mydb;
			$dologs=$mydb->get_dologs();

			if($dologs&&count($dologs)>0){
				?>
				<table class="table striped table-alarmslist">
					<thead><tr>
						<th>时间</th><th>动作</th><th>IP</th>
					</tr></thead>
					<tbody>
						<?php

						foreach ($dologs as $dolog) {
							$html='';
							$html.='<tr><td>'.$dolog['do_time'].'</td><td>'.$dolog['do_msg'].'</td><td>'.$dolog['do_ip'].'</td></tr>';
							echo $html;
						}
						?>
					</tbody>
					<tfoot><tr>
						<th>时间</th><th>动作</th><th>IP</th>
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
					<p><h2>&nbsp;&nbsp;日志为空</h2></p>
				</div>
				<?php 
			}
			?>
		</div>
	</div>
	
	<?php get_footer();?>
	
</body>
</html>