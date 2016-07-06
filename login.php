<!DOCTYPE html>
<?php require 'setting.php';?>
<html lang="zh-CN">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel="stylesheet" />
    <link href="css/main.css" rel="stylesheet" />
    <link href="css/datetimepick.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/login.js"></script>
    <title>登陆-科鼎地理信息服务系统</title>
</head>
<body>

    <header id="header">
        <div class="container">
            <h1 class="center-block">科鼎地理信息服务系统</h1>
        </div>
    </header>

    <div id="bodier" class="">
        <div class="container">
        	<div class="wrap">

			</div>
			<aside class="login-bar">
				<form class="form-horizontal form-login" action="login.php">
					<div class="form-group">
						<label for="inputUserName" class="col-sm-2 control-label">用户名</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputUserName" placeholder="用户名">
						</div>
					</div>
					<div class="form-group">
						<label for="inputPasswd" class="col-sm-2 control-label">密码</label>
						<div class="col-sm-10">
							<input type="passwd" class="form-control" id="inputPasswd"
								placeholder="密码">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<div class="checkbox">
								<label> <input type="checkbox">记住我
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">Sign in</button>
						</div>
					</div>
				</form>
			</aside>
		</div>
    </div>

	<?php get_footer();?>
    
</body>
</html>
