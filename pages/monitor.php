<!DOCTYPE HTML>
<html lang="zh_CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="stylesheet" id="bootstrap-css" href="http://apps.bdimg.com/libs/bootstrap/3.2.0/css/bootstrap.css?ver=9.0.0" type="text/css" media="all">
	    <link rel="stylesheet" id="fontawesome-css" href="http://apps.bdimg.com/libs/fontawesome/4.2.0/css/font-awesome.min.css?ver=9.0.0" type="text/css" media="all">
	    <link href="css/main.css" rel="stylesheet">
	    <link href="css/datetimepick.css" rel="stylesheet">
	    <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js"></script>
	    <title>科鼎地理信息服务系统</title>
	</head>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
	        	<div class="widget">
	        		<h3>在线监测</h3>
					<div id="map-box">
						
					</div>
				</div>
			</div>
	    </div>
	
		<?php get_footer();?>
    
	</body>
	<!-- 百度地图api -->
	<script type="text/javascript"> 
	 function loadJScript() {
         var script = document.createElement("script");
         script.type = "text/javascript";
         script.src = "http://api.map.baidu.com/api?v=2.0&ak=TTTvM1MdjlGPiZq45xjiEgpx6vcarBYy&callback=initMap";
         document.body.appendChild(script);
     }
     function initMap(){
		var map = new BMap.Map("map-box");          // 创建地图实例  
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
	    			drawLines(map,t.lines_group_coor);
	    		if(t.groups!=null)
	    			drawgroups(map,t.groups);
	        }
	    });
	}
	//添加地图标注
	function drawgroups(map,groups){
		var myIcon = new BMap.Icon("http://app.baidu.com/map/images/us_mk_icon.png", new BMap.Size(21,21),{imageOffset: new BMap.Size(-21, 0),offset: new BMap.Size(21, 21) });
		for(var i=0;i<groups.length;i++){
			//创建图标
			point=new BMap.Point(groups[i]['coor_long'],groups[i]['coor_lat']);
			var marker = new BMap.Marker(point, {icon: myIcon});    
		 	map.addOverlay(marker);
		 	var title=groups[i]['group_loc']+"-"+groups[i]['group_name'];
		 	var content='';
			content+=groups[i]['line_name']+": "+groups[i]['dev_on_A']+","+groups[i]['dev_on_B']+","+groups[i]['dev_on_C']+"<br />";
			content+=groups[i]['line_name2']+": "+groups[i]['dev_on_2A']+","+groups[i]['dev_on_2B']+","+groups[i]['dev_on_2C']; 
		  	addClickHandler(map,marker,point,title,content);
		}
		
	}
	function addClickHandler(map,marker,point,title,content){
		var opts = {
			width : 200,     // 信息窗口宽度
			height: 100,     // 信息窗口高度
			title : "<b>"+title+"</b>" , // 信息窗口标题
			}
		marker.addEventListener("click",function(){
			var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象 
			map.openInfoWindow(infoWindow,point); 
		});
	}
	function drawLines(map,lines_group_coor){
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
	loadJScript();

	</script>
</html>