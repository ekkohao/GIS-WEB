$(document).ready(function(){
	var commit_flag=false;
	var sh;
	var intV=10;
	var line_name;
	var line_old_name;
	var line_id=0;
	var modeName=["添加","修改","删除"];
	var mode=0;
	var changeTr=null;
	//添加新线路
	$('.btn-group .btn-topopaddline').click(function(){
		mode=0;
		$('.form-addline h3').html("添加新线路");
		$('.form-addline button.btn').html("确定添加");
		$('.pop-box.pop-addline').fadeIn(200);
		$('#inputLineName').val('');
		$('#inputLineName')[0].focus();
		$('.form-addline span.error').html("");
	});
	//编辑
	$('.table-line tbody span.edit').click(function(e){
		changeTr=$(this).parents('tr:first');
		mode=1;
		line_id=changeTr.find('td:first input').val();
		line_old_name=changeTr.find('td:eq(1) strong').html();
		$('.form-addline button.btn').html("确定修改");
		$('.form-addline h3').html("编辑线路["+line_old_name+"]");
		$('.pop-box.pop-addline').fadeIn(200);
		$('#inputLineName').val(line_old_name).focus();
		$('.form-addline span.error').html("");
	});
	//删除
	$('.table-line tbody span.delete').click(function(e){
		changeTr=$(this).parents('tr:first');
		mode=2;
		$('.msgbox span.msg-line-name').html(changeTr.find('td:eq(1) strong').html());
		line_id=changeTr.find('td:first input').val();
		$('.pop-box.pop-delline').fadeIn(200);
		$('.pop-box .msgbox .btn-del-commit').attr("disabled",false).focus();
		$('.msgbox .error').html("");
	});
	$('.remove-x,.pop-box .msgbox .btn-del-cancel').click(function(){
		$(this).parents('.pop-box').fadeOut(200);
		if(mode!=2)
			msgReset();
	});
	$('.form-addline').submit(function(e){
		e.preventDefault();
		submitClick();
	});
	//删除确认click事件
	$('.pop-box .msgbox .btn-del-commit').click(function(e){
		$(this).attr("disabled","disabled");
		$('.msgbox .error').html("");
		var data="mode="+mode+"&line_id="+line_id;
		$.ajax({
		        type: "post",
		        url: "pages/lines/newlineadd-ajax.php",
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
	function commitMsg(){
		$('.success-msg').html((--intV)+"秒内再次点击按钮来"+modeName[mode]+"线路["+line_name+"]");
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
		$('.form-addline span.error').html("").hide();
		var btn=$('.form-addline button.btn');
		btn.attr("disabled","disabled");
		line_name=$.trim($('#inputLineName').val());
		if(line_name.length>30){
			$('.error-linename').html('线路名的长度过长').show();
			btn.attr('disabled',false);
			return;
		}
		if(mode==1&&line_name==line_old_name){
			$('.error-linename').html('线路名称未改动').show();
			btn.attr('disabled',false);
			return;
		}
		if(!commit_flag){
			commit_flag=true;
			$('.success-msg').html(intV+"秒内再次点击按钮来"+modeName[mode]+"线路["+line_name+"]").show();
			sh=setInterval(commitMsg,1000);
			btn.attr('disabled',false);
			return;
		}
		msgReset();
		var data="line_name="+line_name+"&mode="+mode+"&line_id="+line_id;
		$.ajax({
		        type: "post",
		        url: "pages/lines/newlineadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.stat==0){
		        		$('.form-addline .success-msg').html("线路["+line_name+"]"+modeName[mode]+"成功").show();
		        		if(mode==0)
		        			$('.table-line tbody').append('<tr><td><input id="cb-select-'+t.line_id+'" type="hidden" value="'+t.line_id+'"><br />&nbsp;</td><td><strong>'+line_name+'</strong><div class="row-actions"><span class="edit"><a href="javascript:void(0)" title="编辑此项目">编辑</a></span><span class="delete"><a href="javascript:void(0)" title="删除此项目">删除</a></span></div></td><td></td></tr>');
		        		else
		        			changeTr.find('td:eq(1) strong').html(line_name);
		        		line_old_name=line_name;
		        		btn.attr('disabled',false);
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.stat;i++){
		        			$('.form-addline .error-msg').append(t.data[i]+'<br />').show();
		        		}
		        	}
		        	btn.attr('disabled',false);
		        },
		        error: function () {
		        	$('.form-addline .error-msg').html('网络错误，请稍后再试').show();
		        	btn.attr('disabled',false);
		        }
		 });
		
	}
})
