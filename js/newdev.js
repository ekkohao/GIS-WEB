$(document).ready(function(){
	if($('.form-newdev #selectGroup').val()==0)
		$(".form-newdev a.newdev-l-editgro").hide();

	$('.widget-click h3').click(function(){
		$('.widget-click').toggleClass('widget-hidden');
	});
	var old_dev_num=$('.form-newdev #inputDevNum').val();
	var old_dev_phase=$('.form-newdev #selectDevPhase').val();
	var old_group_id=$('.form-newdev #selectGroup').val();
	var old_line_id=$('.form-newdev #selectLine').val();
	var dev_haschange=false;
	var gro_haschange=false;
	$('.form-newdev #selectGroup').change(function(){
		updateLine($(this).val());
	});
	$('.form-newdev').submit(function(e){
		var mode=0;
		var dev_id=0;
		e.preventDefault();
		if($(this).hasClass('form-editdev')){
			mode=1;
			dev_id=$('.form-newdev #inputDevID').val();
		}
		$('.form-newdev  span.error').html("");

		var dev_num=$.trim($('.form-newdev #inputDevNum').val());
		var dev_phase=$.trim($('.form-newdev #selectDevPhase').val());
		var group_id=$.trim($('.form-newdev #selectGroup').val());
		var line_id=$.trim($('.form-newdev #selectLine').val());
		var reg=/^[A-Za-z0-9]+$/;
		var hasError=0;
		if(dev_haschange==false&&mode==1&&old_dev_num==dev_num&&old_dev_phase==dev_phase&&old_group_id==group_id&&old_line_id==line_id){
			$('.form-newdev .error-msg').html("未进行任何修改").show();
			return;
		}
		if(dev_num.match(reg)==null||dev_num.length!=10){
			$('.form-newdev .error-devnum').html("设备编号的格式或长度错误");
			hasError++;
		}
		if(dev_phase==0){
			$('.form-newdev .error-devphase').html("请选择相位");
			hasError++;
		}
		if(group_id==0){
			$('.form-newdev .error-group').html("请选择杆塔");
			hasError++;
		}
		if(line_id==0){
			$('.form-newdev .error-line').html("请选择线路");
			hasError++;
		}
		if(hasError>0){
			$('.form-newdev .error-msg').html(hasError+"处错误，请修改");
			$('.form-newdev  span.error').show();
			return;
		}
		var data="dev_id="+dev_id+"&dev_num="+dev_num+"&dev_phase="+dev_phase+"&group_id="+group_id+"&line_id="+line_id+"&mode="+mode;
		$.ajax({
		        type: "post",
		        url: "pages/devs/newdevadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.errorsinfo==null||t.errorsinfo.count==0){
		        		if(mode==1){
		        			$('.form-newdev .success-msg').html("设备["+dev_num+"]修改成功").show();
		        			old_dev_num=dev_num;
		        			old_dev_phase=dev_phase;
							old_group_id=group_id;
							old_line_id=line_id;
							dev_haschange=false;
		        		}
		        		else
		        			$('.form-newdev .success-msg').html("设备["+dev_num+"]添加成功").show();
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.errorsinfo.count;i++){
		        			$('.form-newdev .error-msg').append(t.errorsinfo.errors[i]+'<br />').show();
		        		}
		        	}
		        },
		        error: function () {
		        	$('.form-newdev .error-msg').html('网络错误，请稍后再试').show();
		        }
		 });
	})
	function updateLine(groupId,hasselect=0){
		var sL=$(".form-newdev #selectLine");
		sL.children(".error").html("");
		if(groupId=='0'){
			$(".form-newdev a.newdev-l-editgro").hide();
			sL.attr("disabled","disabled");
			sL.children("option:first").html('请先选择杆塔').attr("selected","selected");
			return;
		}
		$(".form-newdev a.newdev-l-editgro").show();
		$.ajax({
	        type: "post",
	        url: "pages/devs/newdev-ajax.php",
	        dataType: "json",
	        data: "gro_id="+ groupId,
	        success:function(t){
	        	if(t.data.linescount==0){
	        			sL.children("option:first").html('此杆塔暂无线路，请添加').attr("selected","selected");
	        			$(".form-newdev #selectLine").attr("disabled","disabled");
	        	}
	        	else{
	        		sL.children("option:not(:first)").remove();
	        		sL.children("option:first").html('请选择线路');
	        		for(var i=0;i<t.data.linescount;i++){
	        			var ss=(t.data.lines[i]['line_id']==hasselect)?' selected="selected"':'';
	        			sL.append('<option'+ss+' value="'+t.data.lines[i]['line_id']+'">'+t.data.lines[i]['line_name']+'</option>');
	        		}
					sL.attr("disabled",false);
	        	}
	        },
	        error:function(){
	        	sL.children(".error").html("网络错误请稍后再试");
	        }
		})
	}

	$('.form-newdev a.newdev-l-newgro').click(function(){
		if($('.form-newgro').hasClass('form-editgro'))
			$('.form-newgro').removeClass('form-editgro');
		if($('.widget-click').hasClass('widget-hidden'))
			$('.widget-click').removeClass('widget-hidden');
		$('.widget-click h3 span').text("添加新杆塔");
		$('.form-newgro a.edit-line').hide();
		$('.form-newgro #inputGroName').val("");
		$('.form-newgro #inputGroLoc').val("");
		$('.form-newgro #selectLine1').val(0);
		$('.form-newgro #selectLine2').val(0);
		$('.form-newgro #inputCoor').val("");
		$('.form-newgro button.btn').text("确认添加");
		$('.form-newdev .error').html("").hide();
		$('html,body').animate({scrollTop:$('.widget-click').offset().top}, 400); 
	});
	var old_gro_name,old_gro_loc,old_line_id1,old_line_id2,old_coor;
	$('.form-newdev a.newdev-l-editgro').click(function(){
		$('.form-newdev .error').html("").hide();
		var gro_id=$('.form-newdev #selectGroup').val();
		$.ajax({
			type:"post",
			url:"pages/devs/get-gro-ajax.php",
			dataType:"json",
			data:{"gro_id":gro_id},
			success:function(t){
				if(!$('.form-newgro').hasClass('form-editgro'))
					$('.form-newgro').addClass('form-editgro');
				if(t.data!=null){
					old_gro_name=t.data['group_name'];
					$('.form-newgro #inputGroName').val(old_gro_name);
					old_gro_loc=t.data['group_loc'];
					$('.form-newgro #inputGroLoc').val(old_gro_loc);
					old_line_id1=t.data['line_id'];
					var sLL=$('.form-newgro #selectLine1');
					sLL.val(old_line_id1);
					if(sLL.val()==0)
						sLL.next('label').find('a.edit-line').hide();
					else
						sLL.next('label').find('a.edit-line').show();
					old_line_id2=t.data['line_id2'];
					sLL=$('.form-newgro #selectLine2');
					sLL.val(old_line_id2);
					if(sLL.val()==0)
						sLL.next('label').find('a.edit-line').hide();
					else
						sLL.next('label').find('a.edit-line').show();
					old_coor=t.data['coor_long']+','+t.data['coor_lat'];
					$('.form-newgro #inputCoor').val(old_coor);
					$('.form-newgro button.btn').text("确认修改");
					if($('.widget-click').hasClass('widget-hidden'))
						$('.widget-click').removeClass('widget-hidden');
					$('html,body').animate({scrollTop:$('.widget-click').offset().top}, 400); 
					$('.widget-click h3 span').text("编辑杆塔");
				}
			},
			error:function(){
				$('.form-newdev .error-group').html("获取杆塔数据失败，请稍后再试").show();
			},
		});

	});


	var	commit_flag=false;
	var	intV=10;
	var sh;
	$('.form-newgro').submit(function(e){
		e.preventDefault();
		var mode=0;
		var gro_id=0;
		var btn=$('.form-newgro button.btn');
		if($(this).hasClass('form-editgro')){
			gro_id=$('.form-newdev #selectGroup').val();
			mode=1;
		}
		btn.attr("disabled","disabled");
		$(this).find('span.error').html("").hide();
		var gro_name=$.trim($('.form-newgro #inputGroName').val());
		var gro_loc=$.trim($('.form-newgro #inputGroLoc').val());
		var line_id1=$('.form-newgro #selectLine1').val().replace(/[ ]/g,"");
		var line_id2=$('.form-newgro #selectLine2').val().replace(/[ ]/g,"");
		var coor=$('.form-newgro #inputCoor').val().replace(/[ ]/g,"");
		var reg =/^[-\+]?\d+(\.\d+)\,[-\+]?\d+(\.\d+)$/;
		var hasError=0;
		if(gro_haschange==false&&mode==1&&old_gro_name==gro_name&&old_gro_loc==gro_loc&&old_line_id1==line_id1&&old_line_id2==line_id2&&old_coor==coor){
			$('.form-newgro .error-msg').html("新数据与旧数据相同");
			$('.form-newgro span.error').show();
			btn.attr('disabled',false);
			return;
		}
		if(gro_name.length>30){
			$('.form-newgro .error-groname').html("杆塔名的长度过长");
			hasError++;
		}
		if(gro_loc.length>30){
			$('.form-newgro .error-groloc').html("杆塔地址的长度过长");
			hasError++;
		}
		if(line_id1==line_id2&&line_id1!=0){
			$('.form-newgro .error-line2').html("线路2与线路1不能相同");
			hasError++;
		}
		if(coor.match(reg)==null){
			$('.form-newgro .error-coor').html("坐标格式错误");
			hasError++;
		}
		if(hasError>0){
			$('.form-newgro .error-msg').html(hasError+"处错误，请修改");
			$('.form-newgro span.error').show();
			btn.attr('disabled',false);
			return;
		}
		var data="gro_id="+gro_id+"&gro_name="+gro_name+"&gro_loc="+gro_loc+"&line_id1="+line_id1+"&line_id2="+line_id2+"&coor="+coor+"&mode="+mode;
		$.ajax({
		        type: "post",
		        url: "pages/groups/newgroadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.errorsinfo==null||t.errorsinfo.count==0){
		        		if(mode==1){
		        			$('.form-newgro .success-msg').html("杆塔["+old_gro_name+"]修改成功").show();		
	        				old_gro_name=gro_name;
							old_gro_loc=gro_loc;
							old_line_id1=line_id1;
							old_line_id2=line_id2;
							old_coor=coor;
							var hasselect=$('.form-newdev #selectLine').val();
							updateLine(gro_id,hasselect);
							gro_haschange=false;
		        		}
		        		else{
		        			$('.form-newgro .success-msg').html("杆塔["+gro_name+"]添加成功").show();
		        			$('.form-newdev #selectGroup').append('<option selected="selected" class="'+t.data.gro_id+'" value="'+t.data.gro_id+'">'+gro_loc+'-'+gro_name+'</option>');
		        			updateLine(t.data.gro_id);

		        		}
		        		dev_haschange=true;
		        		$('html,body').animate({scrollTop:$('.form-newdev  #selectGroup').offset().top - 200}, 400); 
		        		btn.attr('disabled',false);
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.errorsinfo.count;i++){
		        			$('.form-newgro .error-msg').append(t.errorsinfo.errors[i]+'<br />').show();
		        		}
		        	}
		        	btn.attr('disabled',false);
		        },
		        error: function () {
		        	$('.form-newgro .error-msg').html('网络错误，请稍后再试').show();
		        	btn.attr('disabled',false);
		        }
		 });
		

	})
	var old_line_name;
	var which_line=1;
	var line_add_mode=0;
	var mode_arr=['添加','修改'];
	$('.form-newgro a.new-line').click(function(){
		if($('.pop-addline').hasClass('edit-line'))
			$('.pop-addline').removeClass('edit-line');
		line_add_mode=0;
		
		which_line=($(this).siblings('span').hasClass('error-line1'))?1:2;
		$('.pop-addline h3').text('添加新线路');
		$('.pop-addline form button').text('确认添加');
		$('.pop-addline #inputLineName').val("");
		$('.pop-addline').fadeIn(200);
		$('.form-addline span.error').html("");
	})
	$('.form-newgro a.edit-line').click(function(){
		if(!$('.pop-addline').hasClass('edit-line'))
			$('.pop-addline').addClass('edit-line');
		line_add_mode=1;
		which_line=($(this).siblings('span').hasClass('error-line1'))?1:2;
		$('.pop-addline h3').text('编辑线路');
		$('.pop-addline form button').text('确认编辑');
		old_line_name=$(this).parents('.form-group').find("option:selected").text();
		$('.pop-addline #inputLineName').val(old_line_name);
		$('.pop-addline').fadeIn(200);
		$('.form-addline span.error').html("");
	})
	$('.remove-x').click(function(){
		$(this).parents('.pop-box').fadeOut(200);
		msgReset();
	});
	$('.form-addline').submit(function(e){
		e.preventDefault();
		submitClick();
	});
	$('.form-newgro #selectLine1,.form-newgro #selectLine2').change(function(){
		if($(this).val()==0)
			$(this).next('label').find('a.edit-line').hide();
		else
			$(this).next('label').find('a.edit-line').show();
	});
	var line_name;
	function msgReset(){
		clearInterval(sh);
		$('.form-addline .success-msg').html("").hide();
		commit_flag=false;
		intV=10;
	}
	function commitMsg(){
		$('.form-addline .success-msg').html((--intV)+"秒内再次点击按钮来"+mode_arr[line_add_mode]+"线路["+line_name+"]");
		if(intV<1)
			msgReset();
	}
	function submitClick(){
		$('.form-addline span.error').html("").hide();
		var btn=$('.form-addline button.btn');
		btn.attr("disabled","disabled");
		line_name=$.trim($('.form-addline #inputLineName').val());
		var line_id=$('.form-newgro #selectLine'+which_line).val();
		if(line_add_mode==1&&old_line_name==line_name){
			$('.error-linename').html('新数据与旧数据相同').show();
			btn.attr('disabled',false);
			return;
		}
		if(line_name.length>30){
			$('.error-linename').html('线路名的长度过长').show();
			btn.attr('disabled',false);
			return;
		}
		if(!commit_flag){
			commit_flag=true;
			$('.form-addline .success-msg').html(intV+"秒内再次点击按钮来"+mode_arr[line_add_mode]+"线路["+line_name+"]").show();
			sh=setInterval(commitMsg,1000);
			btn.attr('disabled',false);
			return;
		}
		msgReset();
		var data={"line_name":line_name,"mode":line_add_mode,"line_id":line_id};
		$.ajax({
		        type: "post",
		        url: "pages/lines/newlineadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.errorsinfo==null||t.errorsinfo.count==0){
		        		if(line_add_mode==0){
			        		$('.form-newgro #selectLine'+which_line).append('<option selected="selected" class="line-'+t.data.line_id+'" value="'+t.data.line_id+'">'+line_name+'</option>');
			        		$('.form-newgro #selectLine'+(which_line%2+1)).append('<option class="line-'+t.data.line_id+'" value="'+t.data.line_id+'">'+line_name+'</option>');
		        		}else{
		        			$('.form-newgro #selectLine'+which_line+' option[value='+line_id+']').text(line_name);
		        			$('.form-newgro #selectLine'+(which_line%2+1)+' option[value='+line_id+']').text(line_name);
		        		}
		        		gro_haschange=true;
		        		$('.form-addline .success-msg').html("线路["+line_name+"]"+mode_arr[line_add_mode]+"成功").show();
		        		btn.attr('disabled',false);
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.errorsinfo.count;i++){
		        			$('.form-addline .error-msg').append(t.errorsinfo.errors[i]+'<br />').show();
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