$(document).ready(function(){
	$('#selectGroup').change(function(){
		var groupId=$(this).val();
		if(groupId=='0')
			return;
		$.ajax({
	        type: "post",
	        url: "pages/devs/newdev-ajax.php",
	        dataType: "json",
	        data: "gId="+ groupId,
	        success:function(t){
	        	
	        }
		})
	})
})