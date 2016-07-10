<?php
if(!isset($_GET['devid']))
	hao_redirect("index.php?page=devs&action=devslist");
global $mydb;
$dev=$mydb->get_dev($_GET['devid']);

?>
<div class="widget">
	<h3>添加新设备</h3>
	<?php
	if($dev){
		$lines=$mydb->get_lines_vi_gid($dev['group_id']);
	?>
		<form class="form-horizontal form-editdev">
			<div class="form-group">
				<label for="inputDevNum" class="col-sm-3 control-label">设备编号</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="inputDevNum" placeholder="设备编号" required="required" value="<?php echo $dev['dev_number'];?>">
					<label>请输入10位设备编号&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-devnum"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectDevPhase" class="col-sm-3 control-label">设备相位</label>
				<div class="col-sm-6">
					<select class="form-control" id="selectDevPhase" value="<?php echo $dev['dev_phase'];?>">
						<option value="0">选择相位</option>
						<option <?php if($dev['dev_phase']=='A相') echo 'selected="selected" '; ?>value="A相">A相</option>
						<option <?php if($dev['dev_phase']=='B相') echo 'selected="selected" '; ?>value="B相">B相</option>
						<option <?php if($dev['dev_phase']=='C相') echo 'selected="selected" '; ?>value="C相">C相</option>
					</select>
					<label><span class="error error-devphase"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectGroup" class="col-sm-3 control-label">所属杆塔</label>
				<div class="col-sm-6">
					<select class="form-control" id="selectGroup">
						
						<?php 
						require_db();
						global $mydb;
						$groups=$mydb->get_all_groups();
						if($groups&&count($groups)>0){
							if($dev['group_loc_name']=='已删除')
								echo '<option selected="selected" value="0">(已删除)选择杆塔</option>';
							else
								echo '<option value="0">(空)选择杆塔</option>';
							foreach ($groups as $group){
								if($group['group_id']==$dev['group_id'])
									echo '<option selected="selected" class="group-'.$group['group_id'].'" value="'.$group['group_id'].'">'.$group['group_loc'].'-'.$group['group_name'].'</option>';
							
								else
									echo '<option class="group-'.$group['group_id'].'" value="'.$group['group_id'].'">'.$group['group_loc'].'-'.$group['group_name'].'</option>';
							}
						}
						else{ 
						?>
							<option selected="selected">暂无杆塔信息，请添加杆塔</option>
						<?php
						}
						?>
					</select>
					<label><a href="javascript:void(0)">添加新杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-group"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectLine" class="col-sm-3 control-label">所在线路</label>
				<div class="col-sm-6">
					<?php 
					if($lines&&sizeof($lines)>0)
						echo '<select class="form-control" id="selectLine">';
					else
						echo '<select class="form-control" id="selectLine" disabled="disabled">';
					?>
					<?php
						if($dev['line_name']=="已删除")
							echo '<option value="0">（已删除）请先选择杆塔</option>';
						else
							echo '<option value="0">（空）请先选择杆塔</option>';
						if($lines&&sizeof($lines)>0){
							foreach ($lines as $line) {
								if($dev['line_id']==$line['line_id'])
									echo '<option selected="selected" value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
								else
									echo '<option value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
							}
						}
					?>
					</select>
					<label><a href="javascript:void(0)">添加线路至杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectLine" class="col-sm-3 control-label"></label>
				<div class="col-sm-6">
					<button type="submit" class="btn btn-success">确定修改</button>
					<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
				</div>
			</div>
			<input type="hidden" id="inputDevID" value="<?php echo $dev['dev_id'];?>" />
		</form>
		<?php
		}
		else{
		?>
			<h1>&nbsp;&nbsp;设备不存在或已删除<br />&nbsp;&nbsp;<small><a href="index.php?page=devs&action=devslist">返回杆塔列表</a></small></h1>
		<?php
		}
		?>
</div>
<div class=""></div>
<script type="text/javascript" src="js/newdev.js"></script>