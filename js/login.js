$(document).ready(function() {
	$(".form-login").submit(function(e){
		e.preventDefault();
		$('.form-login .error-msg').html('');
		var data="u="+$("#inputUserName").val()+"&p="+$("#inputPasswd").val()+"";
		console.log(data);
		$.ajax({
		        type: "post",
		        url: "login-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.stat==0)
		        		$('.form-login .error-msg').html('账号或密码错误');
		        	else {
		        		var reurl="/"+GetQueryString('redirect');
		        		if(reurl=null||reurl.toString.length<1)
		        			reurl=location.href.replace("login","index");
		        		window.location.href=reurl;
		        	}
		        }
		 });

	});

})
function errorFunction(){
		$('.form-login .error-msg').html('登陆失败，请稍后再试');
}
function GetQueryString(name)
{
	 var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	 var r = window.location.search.substr(1).match(reg);
	 if(r!=null)return  unescape(r[2]); return null;
}