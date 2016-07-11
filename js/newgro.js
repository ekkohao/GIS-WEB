$(document).ready(function(){
	var old_gro_name=$('.form-newgro #inputGroName').val();
	var old_gro_loc=$('.form-newgro #inputGroLoc').val();
	var old_line_id1=$('.form-newgro #selectLine1').val();
	var old_line_id2=$('.form-newgro #selectLine2').val();
	var old_coor=$('.form-newgro #inputCoor').val();
	var	commit_flag=false;
	var	intV=10;
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
		var reg =/^[-\+]?\d+(\.\d+)\,[-\+]?\d+(\.\d+)$/;
		var hasError=0;
		if(mode==1&&old_gro_name==gro_name&&old_gro_loc==gro_loc&&old_line_id1==line_id1&&old_line_id2==line_id2&&old_coor==coor){
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
		        	if(t.stat==0){
		        		if(mode==1){
		        			$('.form-newgro .success-msg').html("杆塔["+old_gro_name+"]修改成功").show();
		        				old_gro_name=gro_name;
								old_gro_loc=gro_loc;
								old_line_id1=line_id1;
								old_line_id2=line_id2;
								old_coor=coor;
		        		}
		        		else
		        			$('.form-newgro .success-msg').html("杆塔["+gro_name+"]添加成功").show();
		        		btn.attr('disabled',false);
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.stat;i++){
		        			$('.form-newgro .error-msg').append(t.data[i]+'<br />').show();
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
	$('.form-newgro a.new-line').click(function(){
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
		if(line_name.length>30){
			$('.error-linename').html('线路名的长度过长').show();
			btn.attr('disabled',false);
			return;
		}
		if(!commit_flag){
			commit_flag=true;
			$('.form-addline .success-msg').html(intV+"秒内再次点击按钮来添加线路["+line_name+"]").show();
			sh=setInterval(commitMsg,1000);
			btn.attr('disabled',false);
			return;
		}
		msgReset();
		var data="line_name="+line_name+"&mode="+0;
		$.ajax({
		        type: "post",
		        url: "pages/lines/newlineadd-ajax.php",
		        dataType: "json",	        
		        data: data,
		        //beforeSend: LoadFunction, //加载执行方法    
		        //error: errorFunction,  //错误执行方法    
		        success: function (t) {
		        	if(t.stat==0){
		        		$('.form-addline .success-msg').html("线路["+line_name+"]添加成功").show();
		        		$('.form-newgro #selectLine1').append('<option selected="selected" class="line-'+t.line_id+'" value="'+t.line_id+'">'+line_name+'</option>');
		        		$('.form-newgro #selectLine2').append('<option class="line-'+t.line_id+'" value="'+t.line_id+'">'+line_name+'</option>');
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