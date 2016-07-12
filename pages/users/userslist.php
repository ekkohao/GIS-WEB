<div class="btn-group">
	<a href="index.php?page=users&action=newuser" class="btn btn-success">新用户</a>
</div>
<?php 
global $mydb;
$users=$mydb->get_all_users();
$realrole=['','系统管理员','超级管理员','设备管理员','','普通用户'];
if($users&&count($users)>0){
?>
	<table class="table striped table-userlist">
		<thead><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>用户名</th><th>用户权限</th><th>手机</th><th>邮箱</th><th>上次登录</th><th>注册时间</th>
		</tr></thead>
		<tbody>
		<?php
		global $__USER;
		foreach ($users as $user){
			$phoneclass=($user['is_send'])?"issend":"notsend";
			$html='';
			$html.= '<tr><td><input id="cb-select-'.$user['user_id'].'" type="hidden" value="'.$user['user_id'].'"><br />&nbsp;</td><td><strong>'.$user['user_name'].'</strong>';
			if($user['user_role']>$__USER['user_role'])
				$html.='<div class="row-actions"><span class="edit"><a href="index.php?page=users&action=edituser&uid='.$user['user_id'].'" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div>';
			$html.='</td><td>'.$realrole[$user['user_role']].'</td><td class="'.$phoneclass.'">'.$user['user_phone'].'</td><td>'.$user['user_email'].'</td><td>'.$user['last_login_time'].'</td><td>'.$user['register_time'].'</td></tr>';
			echo $html;
		}
		?>
		</tbody>
		<tfoot><tr>
			<th class="select"><input id="cb-select-all" type="hidden"></th><th>用户名</th><th>用户权限</th><th>手机</th><th>邮箱</th><th>上次登录</th><th>注册时间</th>
		</tr></tfoot>
	</table>
<?php 
}
else{
?>
	<div class="widget">
		<h3>NOT FOUND</h3>
		<p><h2>&nbsp;&nbsp;用户列表为空</h2></p>
	</div>
<?php 
}
?>
<div class="pop-box pop-deluser">
	<div class="msgbox">
		<div class="remove-x"><i class="fa fa-remove"></i></div>
		<h3 class="center-block">确定删除吗？<br />此操作不可恢复</h3>
		<input type="hidden" class="form-control" id="inputLineName" />
		<p class="msg">用户<b><span class="msg-user-name"></span></b>将被删除</p>
		<div class="center-block">	
			<button type="submit" class="btn btn-danger btn-del-commit">确定</button>
			<button type="submit" class="btn btn-warning btn-del-cancel">取消</button>
			<p class="msg"><span class="error error-msg"></span><span class="error success-msg"></span></p>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var changeTr;
		$('.table-userlist tbody span.delete').click(function(){
			changeTr=$(this).parents('tr:first');
			var username=changeTr.find('td:eq(1) strong').html();
			$('.pop-deluser .msg-user-name').html(username);
			$('.pop-deluser').fadeIn(200);
			$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).focus();
			$('.msgbox .error').html("");
		});
		$('.remove-x,.pop-box .msgbox .btn-del-cancel').click(function(){
			$(this).parents('.pop-box').fadeOut(200);
		});
		$('.pop-deluser .msgbox .btn-del-commit').click(function(e){
			var user_id=changeTr.find('td:first input').val();
			$(this).attr("disabled","disabled");
			$('.msgbox .error').html("");
			var data="mode="+2+"&user_id="+user_id;
			$.ajax({
			        type: "post",
			        url: "pages/users/newuseradd-ajax.php",
			        dataType: "json",	        
			        data: data,
			        //beforeSend: LoadFunction, //加载执行方法    
			        //error: errorFunction,  //错误执行方法    
			        success: function (t) {
			        	if(t.errorsinfo==null||t.errorsinfo.count==0){
			        		$('.msgbox .success-msg').html("删除成功");
			        		changeTr.remove();
			        		return;
			        	}
			        	else {
			        		$('.msgbox .error-msg').html("");
			        		for(var i=0;i<t.errorsinfo.count;i++){
			        			$('.msgbox .error-msg').append(t.errorsinfo.errors[i]+'<br />');
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