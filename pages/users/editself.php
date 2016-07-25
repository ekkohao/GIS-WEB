<?php 
	
	global $mydb,$__USER;
	$user=$mydb->get_user($__USER['user_id']);
?>

<div class="widget">
	<h3>修改资料</h3>
	<form class="form-horizontal form-newuser form-editself">
		<div class="form-group">
			<label for="inputUserName" class="col-sm-3 control-label input-required">用户名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputUserName"
					placeholder="用户名" required="required" value="<?php echo $user['user_name'];?>"/>
				<label>即登陆名，用户名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groname"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputOldPasswd" class="col-sm-3 control-label input-required">旧密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputOldPasswd" placeholder="新密码" required="required">
				<label>请输入旧密码&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-oldpasswd"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPasswd" class="col-sm-3 control-label">新密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputPasswd" placeholder="新密码">
				<label>请输入8-25位密码，为空则不修改&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-passwd"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPasswd2" class="col-sm-3 control-label">确认密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputPasswd2" placeholder="密码">
				<label>确认密码&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-passwd"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPhone" class="col-sm-3 control-label">手机</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputPhone" placeholder="手机" value="<?php echo $user['user_phone'];?>"/>
				<label>不填或0则不绑定手机<span class="error error-phone"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectIsSend" class="col-sm-3 control-label">是否发送报警短信</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectIsSend">
					<option<?php echo ($user['is_send']==0)?' selected="selected"':''; ?> value="0">否</option>
					<option<?php echo ($user['is_send']==1)?' selected="selected"':''; ?>  value="1">是</option>
				</select>
				<label><span class="error"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-sm-3 control-label">邮箱</label>
			<div class="col-sm-6">
				<input type="email" class="form-control" id="inputEmail" placeholder="邮箱" value="<?php echo $user['user_email'];?>">
				<label><span class="error error-email"></span></label>
			</div>
		</div>	
		<div class="form-group">
			<label for="" class="col-sm-3 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-success">确定修改</button>
				<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
			</div>
		</div>
		<input type="hidden" class="form-control" id="inputUID" value="<?php echo $__USER['user_id'];?>"/>
	</form>
</div>

<script type="text/javascript" src="js/editself.js"></script>