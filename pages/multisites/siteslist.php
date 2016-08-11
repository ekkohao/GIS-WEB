<div class="btn-group">
	<a href="index.php?page=multisites&action=newsite" class="btn btn-success btn-toaddsite">新增站点</a>
</div>
<?php
global $mydb;
$sites=$mydb->get_all_sites();

if($sites&&count($sites)>0){
?>
	<table class="table striped table-multisite">
		<thead><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>站点</th><th>备注</th>
		</tr></thead>
		<tbody>
		<?php
		foreach ($sites as $site){
			
			$html='';
			$html.= '<tr><td><input id="cb-select-'.$site['site_id'].'" type="hidden" value="'.$site['site_id'].'"><br />&nbsp;</td>';
			$html.='<td><strong><a href="http://'.$site['site_name'].'.kedinggis.com">'.$site['site_name'].'.kedinggis.com</a></strong>';
			$html.='<div class="row-actions"><span class="edit"><a href="index.php?page=multisites&action=editsite" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td>';
			$html.='<td>'.$site['site_remark'].'</td></tr>';
			echo $html;
			}
		?>
		</tbody>
		<tfoot><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>站点</th><th>备注</th>
		</tr></tfoot>
	</table>

<?php 
	$pgn_html=$mydb->__get("pgn_html");
	if(!empty($pgn_html))
		echo $pgn_html;
}
else{
?>
	<div class="widget widget-null">
		<h3>NOT FOUND</h3>
		<p><h2>&nbsp;&nbsp;站点列表为空</h2></p>
	</div>
<?php 
}
?>
<div class="pop-box pop-delsite">
	<div class="msgbox">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
		<input type="hidden" class="form-control" id="inputsiteName" />
		<p class="msg">站点<b><span class="msg-site-name"></span></b>将被删除</p>
		<div class="center-block">	
			<button type="submit" class="btn btn-danger btn-del-commit">确定</button>
			<button type="submit" class="btn btn-warning btn-del-cancel">取消</button>
			<p class="msg"><span class="error error-msg"></span><span class="error success-msg"></span></p>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/newsite.js"></script>