

<?php 
global $mydb;
$lines=$mydb->get_all_lines();
$gros=$mydb->get_arr_groups_on_line();
if(is_current_user_can_see(3)){
?>
	<div class="btn-group">
		<button class="btn btn-primary btn-topopaddline">新增线路</button>
	</div>
<?php
}
if($lines&&count($lines)>0){
?>
	<table class="table striped table-line">
		<thead><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>线路名</th><th>绑定杆塔</th>
		</tr></thead>
		<tbody>
		<?php
		foreach ($lines as $line){
			if(!isset($gros[$line['line_id']]))
				$allgros="";
			else
				$allgros=substr($gros[$line['line_id']],0,strlen('，')*(-1));
				$html='';
				$html.= '<tr><td><input id="cb-select-'.$line['line_id'].'" type="hidden" value="'.$line['line_id'].'"><br />&nbsp;</td><td><strong>'.$line['line_name'].'</strong>';
				if(is_current_user_can_see(3))
					$html.='<div class="row-actions"><span class="edit"><a href="javascript:void(0)" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div>';
				$html.='</td><td>'.$allgros.'</td></tr>';
				echo $html;
			}
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="hidden"></th><th>线路名</th><th>绑定杆塔</th>
		</tr></tfoot>
	</table>
<?php 
}
else{
?>
	<div class="widget">
		<h3>NOT FOUND</h3>
		<p><h2>&nbsp;&nbsp;线路列表为空</h2></p>
	</div>
<?php 
}
if(is_current_user_can_see(3)){
?>
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
<div class="pop-box pop-delline">
	<div class="msgbox">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
		<input type="hidden" class="form-control" id="inputLineName" />
		<p class="msg">线路<b><span class="msg-line-name"></span></b>将被删除</p>
		<div class="center-block">	
			<button type="submit" class="btn btn-danger btn-del-commit">确定</button>
			<button type="submit" class="btn btn-warning btn-del-cancel">取消</button>
			<p class="msg"><span class="error error-msg"></span><span class="error success-msg"></span></p>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/newline.js"></script>
<?php
}
?>