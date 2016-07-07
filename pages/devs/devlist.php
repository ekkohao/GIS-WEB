<div class="btn-group">
	<a id="add_dev" href="index.php?page=devs&action=newdev" class="btn btn-success">新增设备 </a>
</div>
<?php 
require_db();
$devs=$mydb->get_all_devs();
if($devs&&count($devs)>0){
?>
	<table class="table striped">
		<thead><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>设备编号</th><th>设备名</th><th>设备相位</th><th>所属杆塔</th><th>所在线路</th>
		</tr></thead>
		<tbody>
		<?php
		$i=1;
		foreach ($devs as $dev)
			echo '<tr><td><input id="cb-select-'.$dev['dev_number'].'" type="checkbox" value="'.$dev['dev_number'].'"></td><td><strong>'.$dev['dev_number'].'</strong><div class="row-actions"><span class="edit"><a href="" title="编辑此项目">编辑</a></div></td><td>'.$dev['dev_name'].'</td><td>'.$dev['dev_phase'].'</td><td>'.$dev['group_loc'].'<br />'.$dev['group_name'].'</td><td>'.$dev['line_name'].'</td></tr>';
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>设备编号</th><th>设备名</th><th>设备相位</th><th>所属杆塔</th><th>所在线路</th>
		</tr></tfoot>
	</table>
<?php 
}
else{
?>
	<h3>设备列表为空</h3>
<?php 
}
?>