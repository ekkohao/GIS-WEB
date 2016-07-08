<div class="widget">
	<h3>添加新设备</h3>
	<form class="form-horizontal form-newdev">
		<div class="form-group">
			<label for="inputDevNum" class="col-sm-3 control-label">设备编号</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDevNum"
					placeholder="设备编号" required="required">
				<label>请输入10位设备编号&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-devnum"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputDevName" class="col-sm-3 control-label">设备名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDevName" placeholder="设备名" required="required">
				<label>设备名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-devname"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectDevPhase" class="col-sm-3 control-label">设备相位</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectDevPhase">
					<option selected="selected" value="0">选择相位</option>
					<option value="A相">A相</option>
					<option value="B相">B相</option>
					<option value="C相">C相</option>
					<option value="上">上</option>
					<option value="中">中</option>
					<option value="下">下</option>
					<option value="左上">左上</option>
					<option value="左中">左中</option>
					<option value="左下">左下</option>
					<option value="右上">右上</option>
					<option value="右中">右中</option>
					<option value="右下">右下</option>
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
					?>
						<option selected="selected" value="0">选择杆塔</option>
					<?php
						foreach ($groups as $group){
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
				<label><a href="javascript:void(0)">添加新杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)">添加线路至杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-group"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine" class="col-sm-3 control-label">所在线路</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine" disabled="disabled">
					<option selected="selected" value="0">请先选择杆塔</option>
				</select>
				<label><a href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine" class="col-sm-3 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-success">确定添加</button>
				<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
			</div>
		</div>
	</form>
</div>
<div class=""></div>
<script type="text/javascript" src="js/newdev.js"></script>