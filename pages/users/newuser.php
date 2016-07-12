<div class="widget">
	<h3>添加新用户</h3>
	<form class="form-horizontal form-newuser">
		<div class="form-group">
			<label for="inputUserName" class="col-sm-3 control-label input-required">用户名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputUserName"
					placeholder="用户名" required="required">
				<label>即登陆名，用户名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groname"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPasswd" class="col-sm-3 control-label input-required">密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputPasswd" placeholder="密码" required="required">
				<label>请输入8-25位密码&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-passwd"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPasswd2" class="col-sm-3 control-label input-required">确认密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputPasswd2" placeholder="密码" required="required">
				<label>确认密码&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-passwd"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectUserRole" class="col-sm-3 control-label">用户权限</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectUserRole">
					<option value="2">超级管理员</option>
					<option value="3">设备管理员</option>
					<option selected="selected" value="5">普通用户</option>
				</select>
				<label><span class="error"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputPhone" class="col-sm-3 control-label">用户手机</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputPhone" placeholder="手机">
				<label>不填或0则不绑定手机<span class="error error-phone"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectIsSend" class="col-sm-3 control-label">是否发送报警短信</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectIsSend">
					<option value="0">否</option>
					<option value="1">是</option>
				</select>
				<label><span class="error"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputEmail" class="col-sm-3 control-label">用户邮箱</label>
			<div class="col-sm-6">
				<input type="email" class="form-control" id="inputEmail" placeholder="邮箱">
				<label><span class="error error-email"></span></label>
			</div>
		</div>	
		<div class="form-group">
			<label for="" class="col-sm-3 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-success">确定添加</button>
				<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
			</div>
		</div>

	</form>
</div>

<script type="text/javascript" src="js/newuser.js"></script>