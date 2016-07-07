<div class="widget">
	<h3>添加新设备</h3>
	<form class="form-horizontal">
		<div class="form-group">
			<label for="inputDevNum" class="col-sm-3 control-label">设备编号</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDevNum"
					placeholder="设备编号" required="required">
				<label>请输入10位设备编号</label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputDevName" class="col-sm-3 control-label">设备名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDevName"
					placeholder="设备名" required="required">
			</div>
		</div>
		<div class="form-group">
			<label for="selectDevPhase" class="col-sm-3 control-label">设备相位</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectDevPhase">
					<option selected="selected">选择相位</option>
					<option>A相</option>
					<option>B相</option>
					<option>C相</option>
					<option>上</option>
					<option>中</option>
					<option>下</option>
					<option>左上</option>
					<option>左中</option>
					<option>左下</option>
					<option>右上</option>
					<option>右中</option>
					<option>右下</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="selectGroup" class="col-sm-3 control-label">所属杆塔</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectGroup">
					
					<?php 
					require_db();
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
						<option selected="selected">请添加杆塔</option>
					<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine" class="col-sm-3 control-label">所在线路</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine">
					<option selected="selected">请先选择杆塔</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine" class="col-sm-3 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-success">确定添加</button>
			</div>
		</div>



	</form>
</div>
<script type="text/javascript" src="js/newdev.js"></script>