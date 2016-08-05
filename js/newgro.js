$(document).ready(function(){
	var old_gro_name=$('.form-newgro #inputGroName').val();
	var old_gro_loc=$('.form-newgro #inputGroLoc').val();
	var old_line_id1=$('.form-newgro #selectLine1').val();
	var old_line_id2=$('.form-newgro #selectLine2').val();
	var old_coor=$('.form-newgro #inputCoor').val();
	var old_usergid=$('.form-newgro #selectUserGName').val();
	var	commit_flag=false;
	var	intV=10;
	var haschange=false;
	$('.form-newgro').submit(function(e){
		e.preventDefault();
		var mode=0;
		var gro_id=0;
		var btn=$('.form-newgro button.btn');
		if($(this).hasClass('form-editgro')){
			gro_id=$('#inputGroID').val();
			mode=1;
		}
		btn.attr("disabled","disabled");
		$(this).find('span.error').html("").hide();
		var gro_name=$.trim($('.form-newgro #inputGroName').val());
		var gro_loc=$.trim($('.form-newgro #inputGroLoc').val());
		var line_id1=$('.form-newgro #selectLine1').val().replace(/[ ]/g,"");
		var line_id2=$('.form-newgro #selectLine2').val().replace(/[ ]/g,"");
		var coor=$('.form-newgro #inputCoor').val().replace(/[ ]/g,"");
		var usergid=$('.form-newgro #selectUserGName').val();
		var reg =/^[-\+]?\d+(\.\d+)\,[-\+]?\d+(\.\d+)$/;
		var hasError=0;
		if(haschange==false&&mode==1&&old_gro_name==gro_name&&old_gro_loc==gro_loc&&old_line_id1==line_id1&&old_line_id2==line_id2&&old_coor==coor&&old_usergid==usergid){
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
		var data="gro_id="+gro_id+"&gro_name="+gro_name+"&gro_loc="+gro_loc+"&line_id1="+line_id1+"&line_id2="+line_id2+"&coor="+coor+"&mode="+mode+"&usergid="+usergid;
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
								old_usergid==usergid;
								old_line_id2=line_id2;
								old_coor=coor;
								haschange=false;
		        		}
		        		else
		        			$('.form-newgro .success-msg').html("杆塔["+gro_name+"]添加成功").show();
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
	$('.form-newgro #selectLine1,.form-newgro #selectLine2').change(function(){
		if($(this).val()==0)
			$(this).next('label').find('a.edit-line').hide();
		else
			$(this).next('label').find('a.edit-line').show();
	});
	$('.remove-x').click(function(){
		$(this).parents('.pop-box').fadeOut(200);
			msgReset();
	});
	$('.form-addline').submit(function(e){
		e.preventDefault();
		submitClick();
	});
	var line_name;
	function msgReset(){
		clearInterval(sh);
		$('.form-addline .success-msg').html("").hide();
		commit_flag=false;
		intV=10;
	}
	function commitMsg(){
		$('.form-addline .success-msg').html((--intV)+"秒内再次点击按钮来添加线路["+line_name+"]");
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
		        		$('.form-newgro #selectLine'+which_line).next('label').find('a.edit-line').show();
		        		haschange=true;
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