<div class="btn-group">
	<a id="add_group" href="index.php?page=groups&action=newgro" class="btn btn-success">新增杆塔 </a>
</div>

<?php 

global $mydb;
$groups=$mydb->get_all_groups();
if($groups&&count($groups)>0){
?>
	<table class="table striped table-grolist">
		<thead><tr>
			<th><input id="cb-select-all" type="hidden"></th><th>杆塔名</th><th>杆塔位置</th><th>所在线路1</th><th>所在线路2</th><th>坐标</th>
		</tr></thead>
		<tbody>
		<?php
		foreach ($groups as $group){
			$tdclass1=	($group['line_name']=='未绑定'||$group['line_name']=='线路已删除')?'togray':'';
			$tdclass2=	($group['line_name2']=='未绑定'||$group['line_name2']=='线路已删除')?'togray':'';
			$iclass1= $group['dev_on_A']!='无'?'tolight':'';
			$iclass2= $group['dev_on_B']!='无'?'tolight':'';
			$iclass3= $group['dev_on_C']!='无'?'tolight':'';
			$iclass4= $group['dev_on_2A']!='无'?'tolight':'';
			$iclass5= $group['dev_on_2B']!='无'?'tolight':'';
			$iclass6= $group['dev_on_2C']!='无'?'tolight':'';
			echo '<tr><td><input id="cb-select-'.$group['group_id'].'" type="hidden" value="'.$group['group_id'].'"><br />&nbsp;</td><td><strong>'.$group['group_name'].'</strong><div class="row-actions"><span class="edit"><a href="index.php?page=groups&action=editgro&groid='.$group['group_id'].'" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td><td><strong>'.$group['group_loc'].'</strong></td><td class="'.$tdclass1.'">'.$group['line_name'].'<br /><span class="line-circle"><i class="'.$iclass1.'" title="'.$group['dev_on_A'].'"></i><i class="'.$iclass2.'" title="'.$group['dev_on_B'].'"></i><i class="'.$iclass3.'" title="'.$group['dev_on_C'].'"></i></span></td><td class="'.$tdclass2.'">'.$group['line_name2'].'<br /><span class="line-circle"><i class="'.$iclass4.'" title="'.$group['dev_on_2A'].'"></i><i class="'.$iclass5.'" title="'.$group['dev_on_2B'].'"></i><i class="'.$iclass6.'" title="'.$group['dev_on_2C'].'"></i></span></td><td>经度：'.$group['coor_long'].'<br/>维度：'.$group['coor_lat'].'</td></tr>';
		}
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="hidden"></th><th>杆塔名</th><th>杆塔位置</th><th>所在线路1</th><th>所在线路2</th><th>坐标</th>
		</tr></tfoot>
	</table>
	<div class="pop-box pop-delgro">
		<div class="msgbox">
			<div class="remove-x"><i class="fa fa-remove"></i></div>
			<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
			<input type="hidden" class="form-control" id="inputLineName" />
			<p class="msg">杆塔<b><span class="msg-gro-name"></span></b>将被删除</p>
			<div class="center-block">	
				<button type="submit" class="btn btn-danger btn-del-commit">确定</button>
				<button type="submit" class="btn btn-warning btn-del-cancel">取消</button>
				<p class="msg"><span class="error error-msg"></span><span class="error success-msg"></span></p>
			</div>
		</div>
	</div>
<?php 
}
else{
?>
	<h3>杆塔列表为空</h3>
<?php 
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		var changeTr;
		$('.table-grolist tbody span.delete').click(function(){
			changeTr=$(this).parents('tr:first');
			var gro=changeTr.find('td:eq(2) strong').html()+'-'+changeTr.find('td:eq(1) strong').html();
			$('.pop-delgro .msg-gro-name').html(gro);
			$('.pop-delgro').fadeIn(200);
			$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).focus();
			$('.msgbox .error').html("");
		});
		$('.remove-x,.pop-box .msgbox .btn-del-cancel').click(function(){
			$(this).parents('.pop-box').fadeOut(200);
		});
		$('.pop-delgro .msgbox .btn-del-commit').click(function(e){
			var gro_id=changeTr.find('td:first input').val();
			$(this).attr("disabled","disabled");
			$('.msgbox .error').html("");
			var data="mode="+2+"&gro_id="+gro_id;
			$.ajax({
			        type: "post",
			        url: "pages/groups/newgroadd-ajax.php",
			        dataType: "json",	        
			        data: data,
			        //beforeSend: LoadFunction, //加载执行方法    
			        //error: errorFunction,  //错误执行方法    
			        success: function (t) {
			        	if(t.stat==0){
			        		$('.msgbox .success-msg').html("删除成功");
			        		changeTr.remove();
			        		return;
			        	}
			        	else {
			        		$('.msgbox .error-msg').html("");
			        		for(var i=0;i<t.stat;i++){
			        			$('.msgbox .error-msg').append(t.data[i]+'<br />');
			        		}
			        		$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).html("重试");
			        	}
			        },
			        error: function () {
			        	$('.msgbox .error-msg').html('网络错误，请稍后再试');
			        	$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).html("重试");
			        }
			 });

		});
	})
</script>