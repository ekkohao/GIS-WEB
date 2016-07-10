$(document).ready(function(){
	var old_gro_name=$('#inputGroName').val();
	var old_gro_loc=$('#inputGroLoc').val();
	var old_line_id1=$('#selectLine1').val();
	var old_line_id2=$('#selectLine2').val();
	var old_coor=$('#inputCoor').val();
	$('.form-newgro,.form-editgro').submit(function(e){
		e.preventDefault();
		var mode=0;
		var gro_id=0;
		var btn=$('.form-newgro button.btn');
		if($(this).hasClass('form-editgro')){
			btn=$('.form-editgro button.btn');
			gro_id=$('#inputGroID').val();
			mode=1;
		}
		btn.attr("disabled","disabled");
		$(this).find('span.error').html("").hide();
		var gro_name=$.trim($('#inputGroName').val());
		var gro_loc=$.trim($('#inputGroLoc').val());
		var line_id1=$('#selectLine1').val().replace(/[ ]/g,"");
		var line_id2=$('#selectLine2').val().replace(/[ ]/g,"");
		var coor=$('#inputCoor').val().replace(/[ ]/g,"");
		var reg =/^[-\+]?\d+(\.\d+)\,[-\+]?\d+(\.\d+)$/;
		var hasError=0;
		if(mode==1&&old_gro_name==gro_name&&old_gro_loc==gro_loc&&old_line_id1==line_id1&&old_line_id2==line_id2&&old_coor==coor){
			$('.error-msg').html("新数据与旧数据相同");
			$('span.error').show();
			btn.attr('disabled',false);
			return;
		}
		if(gro_name.length>30){
			$('.error-groname').html("杆塔名的长度过长");
			hasError++;
		}
		if(gro_loc.length>30){
			$('.error-groloc').html("杆塔地址的长度过长");
			hasError++;
		}
		if(line_id1==line_id2&&line_id1!=0){
			$('.error-line2').html("线路2与线路1不能相同");
			hasError++;
		}
		if(coor.match(reg)==null){
			$('.error-coor').html("坐标格式错误");
			hasError++;
		}
		if(hasError>0){
			$('.error-msg').html(hasError+"处错误，请修改");
			$('span.error').show();
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
		        			$('.success-msg').html("杆塔["+old_gro_name+"]修改成功").show();
		        				old_gro_name=gro_name;
								old_gro_loc=gro_loc;
								old_line_id1=line_id1;
								old_line_id2=line_id2;
								old_coor=coor;
		        		}
		        		else
		        			$('.success-msg').html("杆塔["+gro_name+"]添加成功").show();
		        		return;
		        	}
		        	else {
		        		for(var i=0;i<t.stat;i++){
		        			$('.error-msg').append(t.data[i]+'<br />').show();
		        		}
		        	}
		        },
		        error: function () {
		        	$('.error-msg').html('网络错误，请稍后再试').show();
		        }
		 });
		btn.attr('disabled',false);

	})
})