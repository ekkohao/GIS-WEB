<?php 
if(!defined('ABSPATH'))
	define('ABSPATH', dirname(dirname(dirname(__FILE__))));
require ABSPATH.'/setting.php';
header("Content-type:application/json; charset=UTF-8");

global $mydb;
$lines_group_coor=$mydb->get_all_groups_coor_index_line_id();
$lines_group_coor=rewritearr($lines_group_coor,1);
$groups=$mydb->get_all_groups();
$groups=rewritearr($groups,2);
$jsonpre=array('lines_group_coor'=>$lines_group_coor,'groups'=>$groups);
$json=json_encode($jsonpre);
echo $json;

function rewritearr($arrs,$mode){
	$i=0;
	$j=0;
	$rerows=null;
	if(!$arrs)
		return;
	foreach ($arrs as $arr) {
		$j=0;
		if($mode==1){
			if($arr){
				foreach ($arr as $ar) 
					$rerows[$i][$j++]=$ar;
			}
		}
		else{
			$rerows[$i]=$arr;
		}
		$i++;
	}
	return $rerows;
}
?>