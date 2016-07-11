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
		<form class="form-horizontal form-newdev form-editdev">
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
						$tohide="tohide";
						$groups=$mydb->get_all_groups();
						if($groups&&count($groups)>0){
							if($dev['group_loc_name']=='已删除')
								echo '<option selected="selected" value="0">(已删除)选择杆塔</option>';
							else
								echo '<option value="0">(空)选择杆塔</option>';
							foreach ($groups as $group){
								if($group['group_id']==$dev['group_id']){
									echo '<option selected="selected" class="group-'.$group['group_id'].'" value="'.$group['group_id'].'">'.$group['group_loc'].'-'.$group['group_name'].'</option>';
									$tohide="";
								}
							
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
					<label><a class="newdev-l-newgro" href="javascript:void(0)">添加新杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="newdev-l-editgro<?php echo $tohide; ?>" href="javascript:void(0)">编辑此杆塔</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-group"></span></label>
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
					<label><span class="error error-line"></span></label>
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
<div class="widget widget-hidden widget-click">
	<h3><span>添加新杆塔</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
	<form class="form-horizontal form-newgro widget-to-hidden">
		<div class="form-group">
			<label for="inputGroName" class="col-sm-3 control-label">杆塔名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputGroName"
					placeholder="杆塔名" required="required">
				<label>杆塔名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groname"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputGroLoc" class="col-sm-3 control-label">杆塔地址</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputGroLoc" placeholder="杆塔地址" required="required">
				<label>杆塔地址长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groloc"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine1" class="col-sm-3 control-label">所在线路1</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine1">
					
					<?php 
					require_db();
					global $mydb;
					$lines=$mydb->get_all_lines();
					$html="";
					if($lines&&count($lines)>0){
						$html.='<option selected="selected" value="0">空（点击选择线路）</option>';
						foreach ($lines as $line){
							$html.= '<option class="line-'.$line['line_id'].'" value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
						}
					}
					else{ 
						$html='<option selected="selected">空（暂无线路信息，请添加线路）</option>';
					}
					echo $html;
					?>
				</select>
				<label>
					<a class="new-line" href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="edit-line tohide" href="javascript:void(0)">编辑此线路</a>
					<br />可为空，表示暂不添加线路&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line1"></span>
				</label>
			</div>
		</div>
				<div class="form-group">
			<label for="selectLine2" class="col-sm-3 control-label">所在线路2</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine2">
					
					<?php 
					echo $html;
					?>
				</select>
				<label>
					<a class="new-line" href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="edit-line tohide" href="javascript:void(0)">编辑此线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<br />可为空，表示暂不添加线路&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line2"></span>
					
				</label>
			</div>
		</div>
				<div class="form-group">
			<label for="inputCoor" class="col-sm-3 control-label">杆塔坐标</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputCoor" placeholder="12.3456,12.3456" required="required">
				<label>
					<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">坐标查询</a>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-coor"></span>
					<br />经纬度中间用英文半角逗号(,)隔开，且必须是小数
				</label>
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
<div class="pop-box pop-addline">
	<form class="form-horizontal form-addline">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">添加新线路</h3>
		<div class="form-group">
			<label for="inputLineName" class="col-sm-3 control-label">线路名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputLineName"
					placeholder="线路名" required="required">
				<label>线路名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-linename"></span></label>
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
<script type="text/javascript" src="js/newdev.js"></script>