<?php
current_user_role_identify(3);
if(!isset($_GET['groid']))
	hao_redirect("index.php?page=groups&action=groupslist");
global $mydb;
$group=$mydb->get_group($_GET['groid']);
?>
<div class="widget">
	<h3>编辑杆塔</h3>
	<?php 
	if($group!=null){ 
	?>
		<form class="form-horizontal form-newgro form-editgro">
			<div class="form-group">
				<label for="inputGroName" class="col-sm-3 control-label input-required">杆塔名</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="inputGroName"
						placeholder="杆塔名" required="required" value="<?php echo $group['group_name']; ?>">
					<label>杆塔名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groname"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="inputGroLoc" class="col-sm-3 control-label input-required">杆塔地址</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="inputGroLoc" placeholder="杆塔地址" required="required" value="<?php echo $group['group_loc']; ?>">
					<label>杆塔地址长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groloc"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectLine1" class="col-sm-3 control-label">所在线路1</label>
				<div class="col-sm-6">
					<select class="form-control" id="selectLine1">
						
						<?php 
						global $mydb;
						$lines=$mydb->get_all_lines();
						$html="";
						$tohide=" tohide";
						if($lines&&count($lines)>0){
							if($group['line_name']=="已删除")
								$html.='<option value="0">已删除（点击选择线路）</option>';
							else
								$html.='<option value="0">空（点击选择线路）</option>';
							foreach ($lines as $line){
								if($line['line_id']==$group['line_id']){
									$tohide="";
									$html.= '<option selected="selected" class="line-'.$line['line_id'].'" value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
								}
								else
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
						<a class="new-line" href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="edit-line<?php echo $tohide;?>" href="javascript:void(0)">编辑此线路</a>
					<br />可为空，表示暂不添加线路>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line1"></span>
					</label>
				</div>
			</div>
					<div class="form-group">
				<label for="selectLine2" class="col-sm-3 control-label">所在线路2</label>
				<div class="col-sm-6">
					<select class="form-control" id="selectLine2">
						
						<?php 
						$html="";
						$tohide=" tohide";
						if($lines&&count($lines)>0){
							if($group['line_name2']=="已删除")
								$html.='<option value="0">已删除（点击选择线路）</option>';
							else
								$html.='<option value="0">空（点击选择线路）</option>';
							foreach ($lines as $line){
								if($line['line_id']==$group['line_id2']){
									$tohide="";
									$html.= '<option selected="selected" class="line-'.$line['line_id'].'" value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
								}
								else
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
						<a class="new-line" href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="edit-line<?php echo $tohide;?>" href="javascript:void(0)">编辑此线路</a>
					<br />可为空，表示暂不添加线路>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line2"></span>
						
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="inputCoor" class="col-sm-3 control-label input-required">杆塔坐标</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="inputCoor" placeholder="例如：12.3456,12.3456" required="required" value="<?php echo $group['coor_long'].','.$group['coor_lat']; ?>">
					<label>
						<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">坐标查询</a>
						&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-coor"></span>
						<br />经纬度中间用英文半角逗号(,)隔开，且必须是小数
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectUserGName" class="col-sm-3 control-label">管理小组</label>
				<div class="col-sm-6">
					<select class="form-control" id="selectUserGName">
						<option<?php echo ($group['user_gid']==0)?' selected="selected"':''; ?> value="0">默认</option>
						<?php
						$usergroups=$mydb->get_all_usergroups();
						if(!empty($usergroups))
							foreach ($usergroups as $usergroup) {
								$html= '<option';
								if($group['user_gid']==$usergroup['user_gid'])
									$html.=' selected="selected"';
								$html.=' value="'.$usergroup['user_gid'].'">'.$usergroup['user_gname'].'</option>';
								echo $html;
							}
						?>
					</select>
					<label>不分配小组则默认接收所有设备的报警信息<span class="error"></span></label>
				</div>
			</div>
			<div class="form-group">
				<label for="selectLine" class="col-sm-3 control-label"></label>
				<div class="col-sm-6">
					<button type="submit" class="btn btn-success">确定修改</button>
					<a href="index.php?page=groups&action=groupslist" class="btn btn-warning">返回杆塔列表</a>
					<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
				</div>
			</div>
			<input type="hidden" id="inputGroID" value="<?php echo $group['group_id'];?>" />
		</form>
	<?php
	}
	else{	
	?>
		<h1>&nbsp;&nbsp;杆塔不存在或已删除<br />&nbsp;&nbsp;<small><a href="index.php?page=groups&action=groupslist">返回杆塔列表</a></small></h1>
	<?php
	}
	?>
</div>
<div class="pop-box pop-addline">
	<form class="form-horizontal form-addline">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">添加新线路</h3>
		<div class="form-group">
			<label for="inputLineName" class="col-sm-3 control-label input-required">线路名</label>
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
<script type="text/javascript" src="js/newgro.js"></script>