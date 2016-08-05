
<div class="btn-group">
	<button class="btn btn-primary btn-topopaddug">新增小组</button>
</div>
<?php 
global $mydb;
$usergroups=$mydb->get_all_usergroups();
$users=$mydb->get_reindex_string('users','user_gid','user_name');
$groups=$mydb->get_reindex_string('groups','user_gid',array('group_loc','group_name'));
if(!empty($usergroups)){
?>
	<table class="table striped table-usergroup">
		<thead><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>小组名</th><th>小组成员</th><th>管理杆塔</th>
		</tr></thead>
		<tbody>
		<?php
			foreach ($usergroups as $usergroup) {
				$html= '<tr><td><input id="cb-select-'.$usergroup['user_gid'].'" type="hidden" value="'.$usergroup['user_gid'].'"><br />&nbsp;</td>';
				$html.='<td><strong>'.$usergroup['user_gname'].'</strong><div class="row-actions"><span class="edit"><a href="javascript:void(0)" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td>';
				if(empty($users[$usergroup['user_gid']]))
					$users[$usergroup['user_gid']]='';
				$html.='<td>'.$users[$usergroup['user_gid']].'</td>';
				if(empty($groups[$usergroup['user_gid']]))
					$groups[$usergroup['user_gid']]='';
				$html.='<td>'.$groups[$usergroup['user_gid']].'</td></tr>';
				echo $html;
			}
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="hidden"></th><th>小组名</th><th>小组成员</th><th>管理杆塔</th>
		</tr></tfoot>
	</table>

<?php 
	$pgn_html=$mydb->__get("pgn_html");
	if(!empty($pgn_html))
		echo $pgn_html;
}
else{
?>
	<table class="table striped table-usergroup tohide">
		<thead><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>小组名</th><th>小组成员</th><th>管理杆塔</th>
		</tr></thead>
		<tbody>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="hidden"></th><th>小组名</th><th>小组成员</th><th>管理杆塔</th>
		</tr></tfoot>
	</table>
	<div class="widget widget-null">
		<h3>NOT FOUND</h3>
		<p><h2>&nbsp;&nbsp;小组列表为空</h2></p>
	</div>
<?php 
}
?>
<div class="pop-box pop-addug">
	<form class="form-horizontal form-addug">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">添加小组</h3>
		<div class="form-group">
			<label for="inputUserGName" class="col-sm-3 control-label input-required">小组名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputUserGName"
					placeholder="小组名" required="required">
				<label>小组名长度在16个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-usergname"></span></label>
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
<div class="pop-box pop-delug">
	<div class="msgbox">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
		<input type="hidden" class="form-control" id="inputUserGName" />
		<p class="msg">小组<b><span class="msg-usergroup-name"></span></b>将被删除</p>
		<div class="center-block">	
			<button type="submit" class="btn btn-danger btn-del-commit">确定</button>
			<button type="submit" class="btn btn-warning btn-del-cancel">取消</button>
			<p class="msg"><span class="error error-msg"></span><span class="error success-msg"></span></p>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/newusergroup.js"></script>