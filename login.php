<?php 
require 'setting.php';
session_init_and_redirect(false);
if(!isset($_GET['action'])){
	if(isset($_SESSION['user_id']))
		hao_redirect(SITE_URL."/index.php");
}elseif($_GET['action'] == "logout"){
		unset($_SESSION['user_id']);
		echo '注销登录成功！';
}elseif($_GET['action'] == "login"){
	if(!isset($_POST['username'])||!isset($_POST['passwd'])||!trim($_POST['username'])||!trim($_POST['passwd'])){
		echo "用户名或密码不能为空";

	}else{
		require_db();
		$user_name = htmlspecialchars(trim($_POST['username']));
		$passwd = htmlspecialchars(trim(($_POST['passwd'])));
		$user_id=$mydb->get_user_id_vie_pwd($user_name, $passwd);
		if($user_id){
			$_SESSION['user_id']=$user_id;
			hao_redirect(SITE_URL."/index.php");
			exit();
		}else
			echo "用户名或密码错误"; 
	}
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
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
				<form class="form-horizontal form-login" action="login.php?action=login" method="post">
					<div class="form-group">
						<label for="inputUserName" class="col-sm-2 control-label">用户名</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputUserName" name="username" placeholder="用户名">
						</div>
					</div>
					<div class="form-group">
						<label for="inputPasswd" class="col-sm-2 control-label">密码</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" id="inputPasswd"name="passwd" placeholder="密码">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<div class="checkbox">
								<label> <input type="checkbox" />记住我</label>
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
