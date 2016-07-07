$(document).ready(function(){
	$('#selectGroup').change(function(){
		var groupId=$(this).val();
		var sL=$("#selectLine");
		sL.children(".error").html("");
		if(groupId=='0'){
			sL.attr("disabled","disabled");
			sL.children("option:first").html('请先选择杆塔').attr("selected","selected");
			return;
		}
		$.ajax({
	        type: "post",
	        url: "pages/devs/newdev-ajax.php",
	        dataType: "json",
	        data: "gId="+ groupId,
	        success:function(t){
	        	console.log(t.data);
	        	if(t.stat==0){
	        			sL.children("option:first").html('此杆塔暂无线路，请添加').attr("selected","selected");
	        			$("#selectLine").attr("disabled","disabled");
	        	}
	        	else{
	        		sL.children("option:not(:first)").remove();
	        		sL.children("option:first").html('请选择线路').attr("selected","selected");
	        		for(var i=0;i<t.stat;i++)
	        			sL.append('<option value="'+t.data[i].line_id+'">'+t.data[i].line_name+'</option>');
					sL.attr("disabled",false);
	        	}
	        },
	        error:function(){
	        	sL.children(".error").html("网络错误请稍后再试");
	        }
		})
	})
	$('.form-newdev').submit(function(e){
		e.preventDefault();
		$('.form-newdev span.error').html("");
		var dev_num=$.trim($('#inputDevNum').val());
		var dev_name=$.trim($('#inputDevName').val());
		var dev_phase=$.trim($('#selectDevPhase').val());
		var group_id=$.trim($('#selectGroup').val());
		var line_id=$.trim($('#selectLine').val());
		var reg=/^[A-Za-z0-9]+$/;
		var hasError=0;
		if(!reg.test(dev_num)||dev_num.length!=10){
			$('.error-devnum').html("设备编号的格式或长度错误");
			hasError++;
		}
		if(dev_name.length>30){
			$('.error-devname').html("设备名的长度过长");
			hasError++;
		}
		if(dev_phase==0){
			$('.error-devphase').html("请选择相位");
			hasError++;
		}
		if(group_id==0){
			$('.error-group').html("请选择杆塔");
			hasError++;
		}
		if(line_id==0){
			$('.error-line').html("请选择线路");
			hasError++;
		}
		if(hasError>0){
			$('.error-msg').html(hasError+"处错误，请修改");
			$('.form-newdev span.error').show();
			return;
		}
		var data="dev_num="+dev_num+"&dev_name="+dev_name+"&dev_phase="+dev_phase+"&group_id="+group_id+"&line_id="+line_id;
		$.ajax({
		        type: "post",
		        url: "pages/devs/newdevadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.stat==0){
		        		$('.success-msg').html("设备添加成功").show();
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.stat;i++){
		        			$('.error-msg').append(t.data[i]+'<br />').show();
		        		}
		        	}
		        	console.log(t);
		        }
		 });
	})
})