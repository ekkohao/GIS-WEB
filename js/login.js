$(document).ready(function() {
	$(".form-login").submit(function(e){
		e.preventDefault();
		$data="u="+$("#inputUserName").val()+"&p="+$("#inputPasswd").val()+"";
		console.log($data);
		$.ajax({
		        type: "post",
		        url: "login-ajax.php",
		        dataType: "json",	        
		        data: $data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: erryFunction,  //错误执行方法    
		        success: function (t) {
		        	//var t=eval("("+t+")");
		        	console.log(t);
		        }
		 })
	})
})