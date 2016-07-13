<?php 
current_user_role_identify(3);
require ABSPATH.'/excel/Classes/PHPExcel.php';
//require ABSPATH.'/excel/Classes/PHPExcel/Writer/Excel5.php';
//或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls的

date_default_timezone_set('Asia/Shanghai');

global $mydb;

$data= $mydb->output_devs_to_excel();
$name='output-dev-'.date("Y/m/d");    //生成的Excel文件文件名
if($data)
	push($data,$name);
//echo '<pre>';
//print_r($data);
//echo '</pre>';

	/* 导出excel函数*/
function push($data,$name='dev'){


		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("keding")
		->setLastModifiedBy("keding")
		->setTitle("数据EXCEL导出")
		->setSubject("数据EXCEL导出")
		->setDescription("备份数据")
		->setKeywords("excel")
		->setCategory("result file");
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '设备编号');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '相位');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '杆塔名');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '杆塔位置');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '纬度');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '经度');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '线路名');
		/*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
		$num=1;
		foreach($data as $k => $v){

			$num++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $num-1);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$num, $v['dev_number'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$num, $v['dev_phase']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$num, $v['group_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$num, $v['group_loc']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$num, $v['coor_long']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$num, $v['coor_lat']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$num, $v['line_name']);
		}

		$write = new PHPExcel_Writer_Excel5($objPHPExcel);

		header("Pragma: public");

		header("Expires: 0");

		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");

		header("Content-Type:application/force-download");

		header("Content-Type:application/vnd.ms-execl");

		header("Content-Type:application/octet-stream");

		header("Content-Type:application/download");;

		header('Content-Disposition:attachment;filename="'.$name.'.xls"');

		header("Content-Transfer-Encoding:binary");

		$write->save('php://output');
}


