<?php 
current_user_role_identify(3);
require ABSPATH.'/excel/Classes/PHPExcel.php';
//require ABSPATH.'/excel/Classes/PHPExcel/Writer/Excel5.php';
//或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls的

date_default_timezone_set('Asia/Shanghai');

global $mydb;

$data= $mydb->get_alarms();;
$name='output-alarms-'.date("Y/m/d");    //生成的Excel文件文件名
if($data)
	push($data,$name);
//echo '<pre>';
//print_r($data);
//echo '</pre>';

	/* 导出excel函数*/
function push($data,$name='alarms'){


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
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '报警时间');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '设备编号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '动作次数');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '泄漏电流');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '温度');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '湿度');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '所属杆塔');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '所在线路');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '相位');
		/*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
		$num=1;
		foreach($data as $k => $v){

			$num++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $num-1);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$num, $v['action_time']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$num, $v['dev_number'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$num, $v['action_num']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$num, $v['i_num']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$num, $v['tem']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$num, $v['hum']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$num, $v['group_loc_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$num, $v['line_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$num, $v['dev_phase']);
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
