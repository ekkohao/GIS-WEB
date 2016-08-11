
<div class="widget">
	<h3>添加新站点</h3>

	<form class="form-horizontal form-newsite">
		<div class="form-group">
			<label for="inputSiteName" class="col-sm-3 control-label input-required">站点名</label>
			<div class="col-sm-6">
				<div class="input-group">
					<input type="text" class="form-control" id="inputSiteName" placeholder="站点名" required="required">
					<div class="input-group-addon">.kedinggis.com</div>
				</div>
				<label>站点名长度在16个字符以内,只能输入小写英文字母&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-sitename"></span>
				<br /><strong>添加新站点前务必先在域名注册商处添加二级域名</strong>
				</label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputSiteRemark" class="col-sm-3 control-label">备注</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputSiteRemark" placeholder="备注" >
				
				<label>备注长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-siteremark"></span></label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
				<div class="checkbox">
					<label>
						<input id="checkToUseDefault" type="checkbox"> 使用默认数据库
					</label>
				</div>
			</div>
		</div>
		<br />

		<div class="form-group form-tolock">
			<label for="inputDBhost" class="col-sm-3 control-label">数据库地址</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDBhost" placeholder="数据库地址"">
				
				<label>数据库的域名地址或ip&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-dbhost"></span></label>
			</div>
		</div>
		<div class="form-group form-tolock">
			<label for="inputDBname" class="col-sm-3 control-label">数据表名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDBname" placeholder="数据表名">
				
				<label>所连接的数据表名称&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-dbname"></span></label>
			</div>
		</div>
		<div class="form-group form-tolock">
			<label for="inputDBuser" class="col-sm-3 control-label">数据库用户名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputDBuser" placeholder="数据库用户名" >
				
				<label><span class="error error-dbuser"></span></label>
			</div>
		</div>
		<div class="form-group form-tolock">
			<label for="inputDBpasswd" class="col-sm-3 control-label">数据库密码</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" id="inputDBpasswd" placeholder="数据库密码">

				<label><span class="error error-dbpasswd"></span></label>
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

<script type="text/javascript" src="js/newsite.js"></script>