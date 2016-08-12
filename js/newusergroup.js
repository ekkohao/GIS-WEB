$(document).ready(function(){
	var commit_flag=false;
	var sh;
	var intV=10;
	var user_gname;
	var usergroup_old_name;
	var user_gid=0;
	var modeName=["添加","修改","删除"];
	var mode=0;
	var changeTr=null;
	//添加新小组
	$('.btn-group .btn-topopaddug').click(function(){
		mode=0;
		$('.form-addug h3').html("添加新小组");
		$('.form-addug button.btn').html("确定添加");
		$('.pop-box.pop-addug').fadeIn(200);
		$('#inputUserGName').val('');
		$('#inputUserGName')[0].focus();
		$('.form-addug span.error').html("");
	});
	//编辑
	$('.table-usergroup tbody').on('click','span.edit',function(e){
		changeTr=$(this).parents('tr:first');
		mode=1;
		user_gid=changeTr.find('td:first input').val();
		usergroup_old_name=changeTr.find('td:eq(1) strong').html();
		$('.form-addug button.btn').html("确定修改");
		$('.form-addug h3').html("编辑小组["+usergroup_old_name+"]");
		$('.pop-box.pop-addug').fadeIn(200);
		$('#inputUserGName').val(usergroup_old_name).focus();
		$('.form-addug span.error').html("");
	});
	//删除
	$('.table-usergroup tbody').on('click','span.delete',function(e){
		changeTr=$(this).parents('tr:first');
		mode=2;
		$('.msgbox span.msg-usergroup-name').html(changeTr.find('td:eq(1) strong').html());
		user_gid=changeTr.find('td:first input').val();
		$('.pop-box.pop-delug').fadeIn(200);
		$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).focus();
		$('.msgbox .error').html("");
	});
	$('.remove-x,.pop-box .msgbox .btn-del-cancel').click(function(){
		$(this).parents('.pop-box').fadeOut(200);
		if(mode!=2)
			msgReset();
	});
	$('.form-addug').submit(function(e){
		e.preventDefault();
		submitClick();
	});
	//删除确认click事件
	$('.pop-box .msgbox .btn-del-commit').click(function(e){
		$(this).attr("disabled","disabled");
		$('.msgbox .error').html("");
		var data="mode="+mode+"&user_gid="+user_gid;
		$.ajax({
		        type: "post",
		        url: "pages/users/newusergroupadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.errorsinfo==null||t.errorsinfo.count==0){
		        		$('.msgbox .success-msg').html("删除成功").show();
		        		changeTr.remove();
		        		return;
		        	}
		        	else {
		        		$('.msgbox .error-msg').html("");
		        		for(var i=0;i<t.errorsinfo.count;i++){
		        			$('.msgbox .error-msg').append(t.errorsinfo.errors[i]+'<br />');
		        		}
		        		$('.msgbox .error-msg').show();
		        		$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).html("重试");
		        	}
		        },
		        error: function () {
		        	$('.msgbox .error-msg').html('网络错误，请稍后再试').show();
		        	$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).html("重试");
		        }
		 });

	});
	function commitMsg(){
		$('.success-msg').html((--intV)+"秒内再次点击按钮来"+modeName[mode]+"小组["+user_gname+"]");
		if(intV<1)
			msgReset();
	}
	function msgReset(){
		clearInterval(sh);
		$('.success-msg').html("").hide();
		commit_flag=false;
		intV=10;
	}
	function submitClick(){
		$('.form-addug span.error').html("").hide();
		var btn=$('.form-addug button.btn');
		btn.attr("disabled","disabled");
		user_gname=$.trim($('#inputUserGName').val());
		if(user_gname.length>16){
			$('.error-usergname').html('小组名的长度过长').show();
			btn.attr('disabled',false);
			return;
		}
		if(mode==1&&user_gname==usergroup_old_name){
			$('.error-usergname').html('小组名称未改动').show();
			btn.attr('disabled',false);
			return;
		}
		if(!commit_flag){
			commit_flag=true;
			$('.success-msg').html(intV+"秒内再次点击按钮来"+modeName[mode]+"小组["+user_gname+"]").show();
			sh=setInterval(commitMsg,1000);
			btn.attr('disabled',false);
			return;
		}
		msgReset();
		var data="user_gname="+user_gname+"&mode="+mode+"&user_gid="+user_gid;
		$.ajax({
		        type: "post",
		        url: "pages/users/newusergroupadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.errorsinfo==null||t.errorsinfo.count==0){
		        		$('.form-addug .success-msg').html("小组["+user_gname+"]"+modeName[mode]+"成功").show();
		        		if(mode==0){
		        			if($('.table-usergroup').hasClass('tohide')){
		        				$('.table-usergroup').removeClass('tohide');
		        				$('.widget-null').addClass('tohide');
		        			}
		        			$('.table-usergroup tbody').append('<tr><td><input id="cb-select-'+t.data['user_gid']+'" type="hidden" value="'+t.data['user_gid']+'"><br />&nbsp;</td><td><strong>'+user_gname+'</strong><div class="row-actions"><span class="edit"><a href="javascript:void(0)" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td><td></td></tr>');
		        			$('html,body').animate({scrollTop:$('.table-usergroup tfoot').offset().top-300}, 400); 

		        		}
		        		else
		        			changeTr.find('td:eq(1) strong').html(user_gname);
		        		usergroup_old_name=user_gname;
		        		btn.attr('disabled',false);
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.errorsinfo.count;i++){
		        			$('.form-addug .error-msg').append(t.errorsinfo.errors[i]+'<br />').show();
		        		}
		        	}
		        	btn.attr('disabled',false);
		        },
		        error: function () {
		        	$('.form-addug .error-msg').html('网络错误，请稍后再试').show();
		        	btn.attr('disabled',false);
		        }
		 });
		
	}
})
