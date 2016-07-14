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
<?php
current_user_role_identify(1);

require ABSPATH.'/excel/Classes/PHPExcel.php';
//require ABSPATH.'/excel/Classes/PHPExcel/Writer/Excel5.php';
//或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls的

date_default_timezone_set('Asia/Shanghai');



if (! empty ( $_FILES ['file_stu'] ['name'] )){
	$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
	$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
	$file_type = $file_types [count ( $file_types ) - 1];

	/*判别是不是.xls文件，判别是不是excel文件*/
	if (strtolower ( $file_type ) != "xls"){
		echo '不是Excel文件，重新上传';
		exit();
	}

	$savePath = ABSPATH . '/upload/';

	/*以时间来命名上传的文件*/
	$str = date ( 'Ymdhis' );
	$file_name = $str . "." . $file_type;

	/*是否上传成功*/
	if (!copy( $tmp_file, $savePath . $file_name )){
		echo '上传失败';
		exit();
	}
	read($tmp_file);
}

function read($filename,$encode='utf-8'){
	
	$objReader = PHPExcel_IOFactory::createReader('Excel5'); //use Excel5 for 2003 format 
	$objPHPExcel = $objReader->load($filename); 
	$sheet = $objPHPExcel->getSheet(0); 

	$highestRow = $sheet->getHighestRow();           //取得总行数 

	//$highestColumn = $sheet->getHighestColumn(); //取得总列数
	//各式检查
	$titles=['','序号', '设备编号','相位','杆塔名','杆塔位置','纬度','经度','线路名'];
	for($k='A',$i=1;$i<=8;$k++,$i++){
		if($objPHPExcel->getActiveSheet()->getCell($k.'1')->getValue()!=$titles[$i]){
			echo 'excel文件数据格式有误';
			exit();
		}

	}  
	$rows=null;
	$keys=['','dev_number','dev_phase','group_name','group_loc','coor_long','coor_lat','line_name'];
	for($j=2;$j<=$highestRow;$j++){ 
		if($objPHPExcel->getActiveSheet()->getCell('B'.$j)->getValue()=='')
			continue;
        for($k='B',$tt=1;$k<='H';$k++,$tt++){ 

             $rows[$j][$keys[$tt]]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();//读取单元格

         } 
	}
	//print_r($rows);
	global $mydb;
	$infos=$mydb->input_devs_from_excel($rows);
	//echo '<pre>';
	//print_r($rows);echo '</pre>';
	foreach ($infos as $info) {
		echo $info.'<br />';
	}

}
?>

	</body>
</html>
