 loadJScript();
 var sh;
 var nowId=$('#inputAlarmId').val();
 var map;
 function loadJScript() {
         var script = document.createElement("script");
         script.type = "text/javascript";
         script.src = "http://api.map.baidu.com/api?v=2.0&ak=TTTvM1MdjlGPiZq45xjiEgpx6vcarBYy&callback=initMap";
         document.body.appendChild(script);
     }
 function initMap(){
	map = new BMap.Map("map-box");          // 创建地图实例  
	var point = new BMap.Point(117.223,31.833);  // 创建点坐标  
	map.centerAndZoom(point, 12);                 // 初始化地图，设置中心点坐标和地图级别  
	map.addControl(new BMap.NavigationControl({type: BMAP_NAVIGATION_CONTROL_SMALL} ));    //左上方的平移控件
	map.addControl(new BMap.ScaleControl());    //左下方比例尺
	map.addControl(new BMap.OverviewMapControl()); //右下方缩略图   
	map.addControl(new BMap.MapTypeControl()); 
	map.enableScrollWheelZoom();
		//请求数据
		$.ajax({
        type: "post",
        url: "pages/monitors/mapsinfo-ajax.php",
        dataType: "json",	        
        data: null,
        success:function(t){
        	if(!t)
        		return;
        	if(t.lines_group_coor!=null)
    			drawLines(t.lines_group_coor);
    		if(t.groups!=null)
    			drawgroups(t.groups);
    		sh=setInterval(listeningEvent,5000);
        }
    });
}
//添加地图标注
function drawgroups(groups){
	var myIcon = new BMap.Icon("http://app.baidu.com/map/images/us_mk_icon.png", new BMap.Size(21,21),{imageOffset: new BMap.Size(-21, 0),offset: new BMap.Size(21, 21) });
	for(var i=0;i<groups.length;i++){
		//创建图标
		point=new BMap.Point(groups[i]['coor_long'],groups[i]['coor_lat']);
		var marker = new BMap.Marker(point, {icon: myIcon});    
	 	map.addOverlay(marker);
	 	var title=groups[i]['group_loc']+"-"+groups[i]['group_name'];
	 	var content='';
		content+='<b>'+groups[i]['line_name']+':&nbsp;</b>'+tohref(groups[i]['dev_on_A'])+'&nbsp;|&nbsp;'+tohref(groups[i]['dev_on_B'])+'&nbsp;|&nbsp;'+tohref(groups[i]['dev_on_C'])+'<br />';
		content+='<b>'+groups[i]['line_name2']+':&nbsp;</b>'+tohref(groups[i]['dev_on_2A'])+'&nbsp;|&nbsp;'+tohref(groups[i]['dev_on_2B'])+'&nbsp;|&nbsp;'+tohref(groups[i]['dev_on_2C']); 
	  	addClickHandler(marker,point,title,content);
	}	
}
function addClickHandler(marker,point,title,content){
	var opts = {
		//width : 200,     // 信息窗口宽度
		//height: 100,     // 信息窗口高度
		title : "<b>"+title+"</b>" , // 信息窗口标题
		}
	marker.addEventListener("click",function(){
		var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象 
		map.openInfoWindow(infoWindow,point); 
	});
}
function drawLines(lines_group_coor){
	console.log(lines_group_coor);
	for(var i=0;i<lines_group_coor.length;i++){
		var points=new Array();
		for(var j=0;j<lines_group_coor[i].length;j++){
			points[j]=new BMap.Point(lines_group_coor[i][j]['lng'],lines_group_coor[i][j]['lat']);
		}
	
		var polyline = new BMap.Polyline(points,{strokeColor:"blue", strokeWeight:5, strokeOpacity:0.7});
		map.addOverlay(polyline);
	}
}
function tohref(dev_name){
	var html;
	if(dev_name=='无'||dev_name=='设备已删除')
		html='<span>'+dev_name+'</span>';
	else
		html='<a target="_blank" href="index.php?page=charts&devnum='+dev_name+'">'+dev_name+'</a>';
	return html;
}
function listeningEvent(){
	$.ajax({
		type: "post",
        url: "pages/monitors/listening-ajax.php",
        dataType: "json",	        
        data: {"now_id":nowId},
        success:function(t){
        	if(t.has_alarm==1){
        		nowId=t.data.alarm.id;
        		point=new BMap.Point(t.data.group.coor_long,t.data.group.coor_lat);
        		title='设备'+t.data.dev.dev_number+'报警';
        		content='报警时间：'+t.data.alarm.action_time+'，动作次数：'+t.data.alarm.action_num+'，电流：'+t.data.alarm.i_num+'uA，温度：'+t.data.alarm.tem+'℃，湿度：'+t.data.alarm.hum+'%<br />杆塔：'+t.data.dev.group_loc_name+'，线路：'+t.data.dev.line_name+'，相位：'+t.data.dev.dev_phase;
        		alarmRing(point,title,content);
        		if($('.widget-alarmslist').hasClass('tohide'))
        			$('.widget-alarmslist').removeClass('tohide');
        		$('.table-alarmslist tbody').append('<tr><td>'+t.data.dev.dev_number+'</td><td>'+t.data.alarm.action_time+'</td><td>'+t.data.alarm.action_num+'</td><td>'+t.data.alarm.i_num+'</td><td>'+t.data.alarm.tem+'</td><td>'+t.data.alarm.hum+'</td><td>'+t.data.dev.group_loc_name+'<br />'+t.data.dev.line_name+'-'+t.data.dev.dev_phase+'</td></tr>');
        		$('#warning_mp3')[0].play();
        	}
        }
	})
}
function alarmRing(point,title,content){
	map.panTo(point);
	var opts = {
		title : "<b class='warning-title'>"+title+"</b>" , // 信息窗口标题
	}
	var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象 
	map.openInfoWindow(infoWindow,point); 
	var marker = new BMap.Marker(point);
	map.addOverlay(marker);
	marker.setAnimation(BMAP_ANIMATION_BOUNCE);
	infoWindow.addEventListener("close",function(){
		map.removeOverlay(marker);
		$('#warning_mp3')[0].pause();
	});
}
$(document).ready(function(){
	$('.btn-listen').click(function(){
		if($(this).hasClass('listening')){
			$(this).removeClass('listening').text('开始监听');
			clearInterval(sh);
		}
		else{
			$(this).addClass('listening').text('正在监听');
			sh=setInterval(listeningEvent,5000);
		}
	});
})