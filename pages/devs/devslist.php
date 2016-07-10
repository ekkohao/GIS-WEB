<div class="btn-group">
	<a id="add_dev" href="index.php?page=devs&action=newdev" class="btn btn-success">新增设备 </a>
</div>
<?php 

global $mydb;
$devs=$mydb->get_all_devs();

if($devs&&count($devs)>0){
?>
	<table class="table striped table-devlist">
		<thead><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>设备编号</th><th>设备相位</th><th>所属杆塔</th><th>所在线路</th>
		</tr></thead>
		<tbody>
		<?php
		$i=1;
		foreach ($devs as $dev){
			$tdclass1=	($dev['group_name']=='&nbsp;')?'togray':'';
			$tdclass2=	($dev['line_name']=='未绑定'||$dev['line_name']=='线路已删除')?'togray':'';
			echo '<tr><td><input id="cb-select-'.$dev['dev_id'].'" type="checkbox" value="'.$dev['dev_id'].'"><br />&nbsp;</td><td><strong>'.$dev['dev_number'].'</strong><div class="row-actions"><span class="edit"><a href="index.php?page=devs&action=editdev&devid='.$dev['dev_id'].'" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td><td>'.$dev['dev_phase'].'</td><td class="'.$tdclass1.'">'.$dev['group_loc'].'<br />'.$dev['group_name'].'</td><td class="'.$tdclass1.'">'.$dev['line_name'].'</td></tr>';
		}
		?>
		</tbody>
		<tfoot><tr>
			<th><input id="cb-select-all" type="checkbox"></th><th>设备编号</th><th>设备相位</th><th>所属杆塔</th><th>所在线路</th>
		</tr></tfoot>
	</table>
	<div class="pop-box pop-deldev">
		<div class="msgbox">
			<div class="remove-x"><i class="fa fa-remove"></i></div>
			<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
			<input type="hidden" class="form-control" id="inputLineName" />
			<p class="msg">设备<b><span class="msg-gro-name"></span></b>将被删除</p>
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
	<h3>设备列表为空</h3>
<?php 
}

?>
<script type="text/javascript">
	$(document).ready(function(){
		var changeTr;
		$('.table-devlist tbody span.delete').click(function(){
			changeTr=$(this).parents('tr:first');
			var devn=changeTr.find('td:eq(1) strong').html();
			$('.pop-deldev .msg-gro-name').html(devn);
			$('.pop-deldev').fadeIn(200);
			$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).focus();
			$('.msgbox .error').html("");
		});
		$('.remove-x,.pop-box .msgbox .btn-del-cancel').click(function(){
			$(this).parents('.pop-box').fadeOut(200);
		});
		$('.pop-deldev .msgbox .btn-del-commit').click(function(e){
			var dev_id=changeTr.find('td:first input').val();
			$(this).attr("disabled","disabled");
			$('.msgbox .error').html("");
			var data="mode="+2+"&dev_id="+dev_id;
			$.ajax({
			        type: "post",
			        url: "pages/devs/newdevadd-ajax.php",
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