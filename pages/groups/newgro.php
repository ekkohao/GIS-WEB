<div class="widget">
	<h3>添加新杆塔</h3>
	<form class="form-horizontal form-newgro">
		<div class="form-group">
			<label for="inputGroName" class="col-sm-3 control-label">杆塔名</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputGroName"
					placeholder="杆塔名" required="required">
				<label>杆塔名长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groname"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="inputGroLoc" class="col-sm-3 control-label">杆塔地址</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputGroLoc" placeholder="杆塔地址" required="required">
				<label>杆塔地址长度在30个字符以内&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-groloc"></span></label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine1" class="col-sm-3 control-label">所在线路1</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine1">
					
					<?php 
					require_db();
					global $mydb;
					$lines=$mydb->get_all_lines();
					$html="";
					if($lines&&count($lines)>0){
						$html.='<option selected="selected" value="0">空（点击选择线路）</option>';
						foreach ($lines as $line){
							$html.= '<option class="line-'.$line['line_id'].'" value="'.$line['line_id'].'">'.$line['line_name'].'</option>';
						}
					}
					else{ 
						$html='<option selected="selected">空（暂无线路信息，请添加线路）</option>';
					}
					echo $html;
					?>
				</select>
				<label>
					<a href="javascript:void(0)">添加新线路</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line1"></span>
					<br />可为空，表示暂不添加线路
				</label>
			</div>
		</div>
				<div class="form-group">
			<label for="selectLine2" class="col-sm-3 control-label">所在线路2</label>
			<div class="col-sm-6">
				<select class="form-control" id="selectLine2">
					
					<?php 
					echo $html;
					?>
				</select>
				<label>
					可为空，表示暂不添加线路&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-line2"></span>
					
				</label>
			</div>
		</div>
				<div class="form-group">
			<label for="inputCoor" class="col-sm-3 control-label">杆塔坐标</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="inputCoor" placeholder="12.3456,12.3456" required="required">
				<label>
					<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">坐标查询</a>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="error error-coor"></span>
					<br />经纬度中间用英文半角逗号(,)隔开，且必须是小数
				</label>
			</div>
		</div>
		<div class="form-group">
			<label for="selectLine" class="col-sm-3 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-success">确定添加</button>
				<label><span class="error error-msg"></span><span class="error success-msg"></span></label>
			</div>
		</div>

	</form>
</div>
<div class=""></div>
<script type="text/javascript" src="js/newgro.js"></script>