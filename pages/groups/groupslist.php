<div class="btn-group">
	<a id="add_group" href="index.php?page=groups&action=newgro" class="btn btn-success">新增杆塔 </a>
</div>
<?php 
require_db();
global $mydb;
$groups=$mydb->get_all_groups();
$lines=$mydb->get_all_lines_name_vi_id();
$lines[0]='无';
if($groups&&count($groups)>0){
?>
	<table class="table striped">
		<thead><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>杆塔名</th><th>杆塔位置</th><th>所在线路1</th><th>所在线路2</th><th>坐标</th>
		</tr></thead>
		<tbody>
		<?php
		foreach ($groups as $group){
			
			echo '<tr><td><input id="cb-select-'.$group['group_id'].'" type="checkbox" value="'.$group['group_id'].'"></td><td><strong>'.$group['group_name'].'</strong><div class="row-actions"><span class="edit"><a href="" title="编辑此项目">编辑</a></div></td><td><strong>'.$group['group_loc'].'</strong></td><td>'.$lines[$group['line_id']].'</td><td>'.$lines[$group['line_id2']].'</td><td>经度：'.$group['coor_long'].'<br/>维度：'.$group['coor_lat'].'</td></tr>';
		}
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>杆塔名</th><th>杆塔位置</th><th>所在线路1</th><th>所在线路2</th><th>坐标</th>
		</tr></tfoot>
	</table>
<?php 
}
else{
?>
	<h3>杆塔列表为空</h3>
<?php 
}
?>