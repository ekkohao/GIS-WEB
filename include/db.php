<?php
//数据库接口封装
class db{
	//保存查询结果或查询是否成功的布尔值
	protected $result;
	//保存查询结果的列的列（字段）属性
	protected $col_info;
	//保存执行的查询语句
	protected $queries;
	//设置数据库重连次数
	protected $last_errors=null;
	//protected $reconnect_retries = 5;
	//数据库前缀
	var $prefix = '';
	//数据库连接句柄
	protected $dbcon;
	//是否数据库已连接
	protected $has_connected=false;
	protected $ready=false;
	private $is_use_mysqli=false;
	//构造函数
	public function __construct() {
		register_shutdown_function( array( $this, '__destruct' ) );
		//若存在mysqli模块则启用
		if ( function_exists( 'mysqli_connect' ) ) 
			$this->is_use_mysqli = true;
		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return;
		}
		$this->db_connect();
	}
	//析构函数
	public function __destruct() {
		$this->db_close();
		return true;
	}
	/** 封装get **/
	public function __get( $name ) {
		if ( 'col_info' === $name )
			$this->load_col_info();
		return $this->$name;
	}
	/** 封装set **/
	public function __set( $name, $value ) {
		$this->$name = $value;
	}
	/** 封装isset **/
	public function __isset( $name ) {
		return isset( $this->$name );
	}
	/** 封装unset **/
	public function __unset( $name ) {
		unset( $this->$name );
	}
	//连接数据库
	public function db_connect() {
		/*
		 * Deprecated in 3.9+ when using MySQLi. No equivalent
		 * $new_link parameter exists for mysqli_* functions.
		 */
		$new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;
		if ( $this->is_use_mysqli ) {
			$this->dbcon = mysqli_init();
			// mysqli_real_connect doesn't support the host param including a port or socket
			// like mysql_connect does. This duplicates how mysql_connect detects a port and/or socket file.
			$port = null;
			$socket = null;
			$host = DB_HOST;
			$port_or_socket = strstr( $host, ':' );
			if ( ! empty( $port_or_socket ) ) {
				$host = substr( $host, 0, strpos( $host, ':' ) );
				$port_or_socket = substr( $port_or_socket, 1 );
				if ( 0 !== strpos( $port_or_socket, '/' ) ) {
					$port = intval( $port_or_socket );
					$maybe_socket = strstr( $port_or_socket, ':' );
					if ( ! empty( $maybe_socket ) ) {
						$socket = substr( $maybe_socket, 1 );
					}
				} else {
					$socket = $port_or_socket;
				}
			}
			if ( H_DEBUG ) {
				mysqli_real_connect( $this->dbcon, $host, DB_USER, DB_PASSWORD, null, $port, $socket, $client_flags );
			} else {
				@mysqli_real_connect( $this->dbcon, $host, DB_USER, DB_PASSWORD, null, $port, $socket, $client_flags );
			}
			if ( $this->dbcon->connect_errno ) {
				$this->dbcon = null;
				/* It's possible ext/mysqli is misconfigured. Fall back to ext/mysql if:
		 		 *  - We haven't previously connected, and
		 		 *  - WP_USE_EXT_MYSQL isn't set to false, and
		 		 *  - ext/mysql is loaded.
		 		 */
				$attempt_fallback = true;
				if ( $this->has_connected ) {
					$attempt_fallback = false;
				} else if ( ! function_exists( 'mysql_connect' ) ) {
					$attempt_fallback = false;
				}
				if ( $attempt_fallback) {
					$this->is_use_mysqli = false;
					$this->db_connect();
				}
			}
		} else {
			if ( H_DEBUG ) {
				$this->dbcon = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD, $new_link, $client_flags );
			} else {
				$this->dbcon = @mysql_connect( DB_HOST, DB_USER, DB_PASSWORD, $new_link, $client_flags );
			}
		}
		if ( ! $this->dbcon  ) {
			//未完成
			die("数据库连接失败");
			return false;
		} else {
			$this->has_connected = true;
			$this->set_charset( $this->dbcon );
			//$this->set_sql_mode();
			$this->ready = true;
			$this->select( DB_NAME, $this->dbcon );
			return true;
		}
		return false;
	}
	public function db_close(){
		if($this->has_connected){
			if($this->is_use_mysqli)
				mysqli_close($this->dbcon);
			else
				mysql_close($this->dbcon);
			$this->has_connected=false;
		}
	}
	//设置数据库编码
	public function set_charset( $dbh, $charset = null) {
		if ( ! isset( $charset ) )
			$charset = DB_CHARSET;
		if ( $this->is_use_mysqli)
			mysqli_set_charset( $dbh, $charset );		
 		else 
			mysql_set_charset( $charset, $dbh );	
	}
	/** 选择数据库 **/
	public function select( $db, $dbh = null ) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;
		if ( $this->is_use_mysqli ) {
			$success = @mysqli_select_db( $dbh, $db );
		} else {
			$success = @mysql_select_db( $db, $dbh );
		}
		if ( ! $success ) {
			$this->ready = false;
			//未完成
			die("数据库选择失败");
			return;
		}
	}

	protected function get_rows(){
		if(!$this->queries)
			return null;
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$this->queries);
			if(!$result)
				return null;
			$i=0;
			while($row=mysqli_fetch_array($result))
				$rows[$i++]=$row;
		}
		else{
			$result=mysql_query($this->queries,$this->dbcon);
			if(!$result)
				return null;
			$i=0;
			while($row=mysql_fetch_array($result))
				$rows[$i++]=$row;
		}
		return $rows;
	}
	protected function get_result(){
		if($this->is_use_mysqli)
			$result=mysqli_query($this->dbcon,$this->queries);
		else
			$result=mysql_query($this->queries,$this->dbcon);
		return $result;
	}
	/**
	 *获取当前查询结果集中所有列（字段）的对象数组，
	 *每个对象包含了字段名name、所属表名table、字段最大长度max_length......等等
	 **/
	protected function load_col_info() {
		if ( $this->col_info )
			return;
		if ( $this->is_use_mysqli ) {
			for ( $i = 0; $i < @mysqli_num_fields( $this->result ); $i++ ) {
				$this->col_info[ $i ] = @mysqli_fetch_field( $this->result );
			}
		} else {
			for ( $i = 0; $i < @mysql_num_fields( $this->result ); $i++ ) {
				$this->col_info[ $i ] = @mysql_fetch_field( $this->result, $i );
			}
		}
	}
	/** 返回结果集中的某个结果列（字段）的某个属性值或所有列（当$col_offset==-1时）的某个属性值数组**/
	public function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		$this->load_col_info();
		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				$new_array = array();
				foreach( (array) $this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}
	protected function set_errors($err_count,$err_arr){
		$this->last_errors['count']=$err_count;
		$this->last_errors['errors']=$err_arr;
	}
	/**********************************************
	 *dev
	 *********************************************/
	//----------------------------------------------------------已规范化
	public function add_dev($dev_number,$dev_phase,$group_id,$line_id){
		$err=null;
		$i=0;
		if(!isset($dev_number)||strlen($dev_number)!=10)
			$err[$i++]="设备编号位数错误";
		if(!in_array($dev_phase, array('A相','B相','C相')))
			$err[$i++]="设备相位错误";
		if(!$this->is_group_has_line($group_id,$line_id))
			$err[$i++]="指定杆塔未绑定相应线路";
		if($this->get_dev_vi_number($dev_number))
			$err[$i++]="设备编号为".$dev_number."的设备已存在";
		elseif($o_dev=$this->has_dev_on_line_group_phase($dev_phase,$group_id,$line_id))
			$err[$i++]="设备编号为".$o_dev['dev_number']."的设备已存在于此位置（含有相同的杆塔、线路和相位信息）";
		if($i==0){
			$this->queries="INSERT INTO devs(dev_number,dev_phase,group_id,line_id) VALUES('" . $dev_number."','".$dev_phase."',".$group_id.",".$line_id . ")";
				$result=$this->get_result();
			if(!$result)
				$err[$i++]="添加设备失败，请稍后再试";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了设备".$dev_number);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//----------------------------------------------------------已规范化
	public function update_dev($dev_id,$dev_number,$dev_phase,$group_id,$line_id){
		$err=null;
		$i=0;
		if(!$this->is_group_has_line($group_id,$line_id))
			$err[$i++]="指定杆塔未绑定相应线路";
		if(($flagid=$this->get_dev_vi_number($dev_number))&&$flagid!=$dev_id)
			$err[$i++]="设备编号为".$dev_number."的设备已存在";
		elseif(($o_dev=$this->has_dev_on_line_group_phase($dev_phase,$group_id,$line_id))&&$o_dev['dev_id']!=$dev_id)
			$err[$i++]="设备编号为".$o_dev['dev_number']."的设备已存在于此位置（含有相同的杆塔、线路和相位信息）";
		if($i==0){
			$dev=$this->get_dev($dev_id);
			$this->queries="UPDATE devs SET dev_number='".$dev_number."',dev_phase='".$dev_phase."',group_id=".$group_id.",line_id=".$line_id." WHERE dev_id=".$dev_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="修改设备信息失败";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."修改了设备".$dev['dev_number']);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//删除设备----------------------------------------已规范化
	public function delete_dev($dev_id){
		$err=null;
		$i=0;
		if(!$this->has_dev($dev_id))
			$err[$i++]="设备不存在或已删除";
		else{
			$dev=$this->get_dev($dev_id);
			$this->queries="DELETE FROM devs WHERE dev_id=".$dev_id;
			if($this->is_use_mysqli)
				$result=$this->get_result();
			if(!$result)
				$err[$i++]=("删除失败失败，请稍后再试");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."删除了设备".$dev['dev_number']);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//是否存在某个线路--------------------------已规范化
	protected function has_dev($dev_id){
		$this->queries="SELECT `dev_id` FROM `devs` WHERE `dev_id`=".$dev_id;
		$result=$this->get_result();
		if($result)
			return true;
		return false;
	}
	//若有返回devid
	public function get_dev_vi_number($dev_number){
		$this->queries="SELECT dev_id FROM devs WHERE dev_number=".$dev_number;
		$rows=$this->get_rows();
		if($rows&&count($rows)>0)
			return $rows[0]['dev_id'];
		return 0;
	}
	//某杆塔和线路的某相位上是否存在设备，若存在返回存在设备信息
	public function has_dev_on_line_group_phase($dev_phase,$group_id,$line_id){
		$this->queries="SELECT dev_number,dev_id FROM devs WHERE group_id=".$group_id." AND line_id=".$line_id." AND dev_phase='".$dev_phase."'" ;
		$rows=$this->get_rows();
		if($rows&&count($rows)>0)
			return $rows[0];
		return false;
	}
	//获取设备信息，组合进杆塔和线路信息
	public function get_dev($dev_id){
		$this->queries ="SELECT * FROM devs WHERE dev_id=".$dev_id;
		$rows=$this->get_rows();
		if(!$rows||sizeof($rows)<1)
			return null;
		$rows[0]['line_name']=$this->get_real_line_name($rows[0]['line_id']);
		$rows[0]['group_loc_name']=$this->get_real_gro_loc_name($rows[0]['group_id']);
		return $rows[0];

	}
	//返回所有设备信息数组，组合了杆塔和线路信息
	public function get_all_devs(){
		$this->queries ="SELECT * FROM devs";
		$rows=$this->get_rows();
		$groupsarr=$this->get_all_groups_info_index_id();
		$linesarr=$this->get_all_lines_info_index_id();
		$groupsarr[0]['group_loc']=$linesarr[0]['line_name']='未绑定';
		$groupsarr[0]['group_name']='&nbsp;';
		$rerows=null;
		$i=0;
		if($rows)
			foreach ($rows as $row) {
				$rerows[$i]=$row;
				$rerows[$i]['group_loc']=isset($groupsarr[$row['group_id']]['group_loc'])?$groupsarr[$row['group_id']]['group_loc']:'杆塔已删除';
				$rerows[$i]['group_name']=isset($groupsarr[$row['group_id']]['group_name'])?$groupsarr[$row['group_id']]['group_name']:'&nbsp;';
				$rerows[$i++]['line_name']=isset($linesarr[$row['line_id']]['line_name'])?$linesarr[$row['line_id']]['line_name']:'线路已删除';

			}
		return $rerows;
	}
	//获取设备的id下标数组
	protected function get_all_devs_info_index_id(){
		$this->queries="SELECT * FROM devs";
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$this->queries);
			if(!$result)
				return null;		
			while($row=mysqli_fetch_array($result))
				$rows[$row['dev_id']]=$row;
		}
		else{
			$result=mysql_query($this->queries,$this->dbcon);
			if(!$result)
				return null;
			while($row=mysql_fetch_array($result))
				$rows[$row['dev_id']]=$row;
		}
		return $rows;
	}
	//返回一个三维数组，用【线路ID】【杆塔ID】【相位】下标反查设备编号
	protected function get_all_devs_num_index_line_id_gro_id_dev_phase(){
		$this->queries ="SELECT * FROM devs";
		$rows=$this->get_rows();
		if(!$rows||sizeof($rows)<1)
			return null;
		$rerows=null;
		foreach ($rows as $row) {
			$rerows[$row['line_id']][$row['group_id']][$row['dev_phase']]=$row['dev_number'];
		}
		return $rerows;
	}

	/************************************************************************************
	 *group杆塔操作库
	 *
	 *
	 ************************************************************************************/
	//-----------------------------------已规范化
	public function add_group($group_name,$group_loc,$line_id=0,$line_id2=0,$coor_long,$coor_lat){
		$err=null;
		$i=0;
		if(!isset($group_name)||strlen($group_name)<1||!isset($group_loc)||strlen($group_loc)<1)
			$err[$i++]="杆塔名和杆塔地址不能位空";
		if($this->get_group_id($group_name, $group_loc))
			$err[$i++]="已有相同名字的杆塔名和杆塔地址";
		if($line_id==$line_id2&&$line_id)
			$err[$i++]="一个杆塔的两条线路不能相同";

		if($i==0){
			$this->queries="INSERT INTO groups(group_name,group_loc,line_id,line_id2,coor_long,coor_lat) VALUES('".$group_name."','".$group_loc."',".$line_id.",".$line_id2.",".$coor_long.",".$coor_lat.")";
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="添加线路失败，请稍后再试";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了杆塔".$group_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//----------------------------------已规范化
	public function update_group($group_id,$group_name,$group_loc,$line_id=0,$line_id2=0,$coor_long,$coor_lat){
		$err=null;
		$i=0;
		if(($o_gid=$this->get_group_id($group_name, $group_loc))&&$o_gid!=$group_id)
			$err[$i++]="已有相同名字的杆塔名和杆塔地址";
		if($line_id==$line_id2&&$line_id)
			$err[$i++]="一个杆塔的两条线路不能相同";
		if($i==0){
			$group=$this->get_group($group_id);
			$this->queries="UPDATE groups SET group_name='".$group_name."',group_loc='".$group_loc."',line_id=".$line_id.",line_id2=".$line_id2.",coor_long=".$coor_long.",coor_lat=".$coor_lat." WHERE group_id=".$group_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="修改线路失败，请稍后再试";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."修改了杆塔".$group['group_name']."的资料");
				return true;
			}
		}
		
		$this->set_errors($i,$err);
		return false;
	}
	//删除杆塔---------------------------------------------------------已规范化
	public function delete_group($group_id){
		$err=null;
		$i=0;
		if(!$this->has_group($group_id))
			$err[$i++]="杆塔不存在或已删除";
		else{
			$group=$this->get_group($group_id);
			$this->queries="DELETE FROM groups WHERE group_id=".$group_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]=("删除杆塔失败，请稍后再试");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."删除了杆塔".$group['group_name']);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//是否存在杆塔-------------------------已规范化
	protected function has_group($group_id){
		$this->queries="SELECT group_id FROM groups WHERE group_id=".$group_id;
		$rows=$this->get_rows();
		if(!$rows||sizeof($rows)<1)
			return false;
		return true;
	}
	//根据杆塔位置和名字确定杆塔id，不存在则返回0--------------------------------已规范化
	public function get_group_id($group_name,$group_loc){
		$this->queries="SELECT group_id FROM groups WHERE group_name='" . $group_name . "' AND group_loc='" . $group_loc ."'";
		$rows=$this->get_rows();
		if($rows&&sizeof($rows)>0)
			return $rows[0]['group_id'];
		return 0;
	}
	//获取单个杆塔信息,已组合相关线路信息------------------------------------------已规范化
	//若无此杆塔则返回null
	public function get_group($group_id){
		$this->queries="SELECT * FROM groups WHERE group_id=".$group_id;
		$rows=$this->get_rows();
		if(!$rows||sizeof($rows)<1)
			return null;
		$rows[0]['line_name']=$this->get_real_line_name($rows[0]['line_id']);
		$rows[0]['line_name2']=$this->get_real_line_name($rows[0]['line_id2']);
		return $rows[0];
	}
	public function get_real_gro_loc_name($group_id){
		if($group_id==0)
			return "无";
		$row=$this->get_group($group_id);
		if($row!=null)
			return $row['group_loc'].'-'.$row['group_name'];
		return "已删除";
	}
	//返回所有杆塔信息数组
	public function get_all_groups(){
		$this->queries="SELECT * FROM groups ORDER BY group_loc";
		$rows=$this->get_rows();
		$linesarr=$this->get_all_lines_info_index_id();
		$devnumsarr=$this->get_all_devs_num_index_line_id_gro_id_dev_phase();
		$linesarr[0]['line_name']='未绑定';
		$rerows=null;
		$i=0;
		foreach ($rows as $row) {
			$rerows[$i]=$row;
			$rerows[$i]['line_name']=isset($linesarr[$row['line_id']]['line_name'])?$linesarr[$row['line_id']]['line_name']:'线路已删除';
			$rerows[$i]['line_name2']=isset($linesarr[$row['line_id2']]['line_name'])?$linesarr[$row['line_id2']]['line_name']:'线路已删除';
			$rerows[$i]['dev_on_A']=isset($devnumsarr[$row['line_id']][$row['group_id']]['A相'])?$devnumsarr[$row['line_id']][$row['group_id']]['A相']:'无';
			$rerows[$i]['dev_on_B']=isset($devnumsarr[$row['line_id']][$row['group_id']]['B相'])?$devnumsarr[$row['line_id']][$row['group_id']]['B相']:'无';
			$rerows[$i]['dev_on_C']=isset($devnumsarr[$row['line_id']][$row['group_id']]['C相'])?$devnumsarr[$row['line_id']][$row['group_id']]['C相']:'无';
			$rerows[$i]['dev_on_2A']=isset($devnumsarr[$row['line_id2']][$row['group_id']]['A相'])?$devnumsarr[$row['line_id2']][$row['group_id']]['A相']:'无';
			$rerows[$i]['dev_on_2B']=isset($devnumsarr[$row['line_id2']][$row['group_id']]['B相'])?$devnumsarr[$row['line_id2']][$row['group_id']]['B相']:'无';
			$rerows[$i++]['dev_on_2C']=isset($devnumsarr[$row['line_id2']][$row['group_id']]['C相'])?$devnumsarr[$row['line_id2']][$row['group_id']]['C相']:'无';
		}
		return $rerows;
	}


	//返回某杆塔绑定的所有线路数组
	public function get_lines_vi_gid($group_id){
		$this->queries="SELECT groups.group_id,liness.line_id,liness.line_name FROM groups,liness WHERE groups.group_id=".$group_id." AND (groups.line_id=liness.line_id OR groups.line_id2=liness.line_id)";
		return $this->get_rows();
	}
	public function add_group_vi_line_name($group_name,$group_loc,$line_name=null,$line_name2=null,$coor_long,$coor_lat){
		
		$line_id=$this->get_line_id($line_name);
		$line_id2=$this->get_line_id($line_name2);
		$results=add_group($group_name,$group_loc,$line_id,$line_id2,$coor_long,$coor_lat);
		return $results;
	}
	//数组下标存数杆塔id，相应值为数组的所有信息
	public function get_all_groups_info_index_id(){
		$this->queries="SELECT * FROM groups";
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$this->queries);
			if(!$result)
				return null;		
			while($row=mysqli_fetch_array($result))
				$rows[$row['group_id']]=$row;
		}
		else{
			$result=mysql_query($this->queries,$this->dbcon);
			if(!$result)
				return null;
			while($row=mysql_fetch_array($result))
				$rows[$row['group_id']]=$row;
		}
		return $rows;
	}

	//获取单个线路信息------------------------------------------已规范化
	//若无此线路则返回null
	public function get_line($line_id){
		$this->queries="SELECT * FROM liness WHERE line_id=".$line_id;
		$rows=$this->get_rows();
		if(!$rows||sizeof($rows)<1)
			return null;
		return $rows[0];
	}
	/*获取单个线路线路名------------------------------------------已规范化
	若线路id为0表示选择线路为“空”，其他查不到的id则返回“已删除”*/
	protected function get_real_line_name($line_id){
		if($line_id==0)
			return "无";
		$row=$this->get_line($line_id);
		if($row!=null)
			return $row['line_name'];
		return "已删除";
	}
	//获取一个数组，数组下标为线路id，相应值为绑定杆塔的字符串
	public function get_arr_groups_on_line(){
		$this->queries="SELECT groups.group_name,groups.group_loc,liness.line_id FROM groups,liness WHERE groups.line_id=liness.line_id OR groups.line_id2=liness.line_id ORDER BY groups.group_loc";
		$results=$this->get_rows();
		if(!$results)
			return null;
		foreach ($results as $result) {
			if(!isset($rows[$result['line_id']]))
				$rows[$result['line_id']]="";
			$rows[$result['line_id']].=$result['group_loc'].'—'.$result['group_name'].'，';
		}
		return $rows;
	}
	public function get_all_groups_coor_index_line_id(){
		$this->queries="SELECT groups.group_id,groups.coor_long,groups.coor_lat,liness.line_id FROM groups,liness WHERE groups.line_id=liness.line_id OR groups.line_id2=liness.line_id ORDER BY groups.group_loc";
		$rows=$this->get_rows();
		$rerows=null;
		if($rows)
			foreach ($rows as $row) {
				$rerows[$row['line_id']][$row['group_id']]['lng']=$row['coor_long'];
				$rerows[$row['line_id']][$row['group_id']]['lat']=$row['coor_lat'];
			}
		return $rerows;			
	}
	//指定杆塔是否绑定了指定线路-------------------------------已规范化
	public function is_group_has_line($group_id,$line_id){
		$this->queries="SELECT group_id FROM groups WHERE group_id=".$group_id." AND (line_id=".$line_id." OR line_id2=".$line_id.")";
		//$this->queries="SELECT * FROM groups";
		$result=$this->get_result();
		if($result)
			return true;
		return false;
	}


	/*******************************************************
	 *line
	 ******************************************************/
	public function add_line($line_name){
		$err=null;
		$i=0;
		if($line_name==""||!$line_name)
			$err[$i++]="线路名不能为空";
		if($this->get_line_vi_name($line_name))
			$err[$i++]="添加失败，已有相同名字的线路";
		if($i==0){
			$this->queries="INSERT INTO `liness`(`line_name`) VALUES ('".$line_name."')";
			$result=$this->get_result();
			if(!$result)
				$err[$i++]=("添加线路失败");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了线路".$line_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	public function update_line($line_id,$line_new_name){
		$err=null;
		$i=0;
		if(!isset($line_new_name)||strlen($line_new_name)<1)
			$err[$i++]="线路名不能为空";
		if($this->get_line_vi_name($line_new_name))
			$err[$i++]="修改线路名失败，已有相同名字的线路";
		if($i==0){
			$line=$this->get_line($line_id);
			$this->queries="UPDATE `liness` SET line_name='".$line_new_name."' WHERE line_id=".$line_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]=("修改线路失败");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."修改线路".$line['line_name']."为".$line_new_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//删除线路----------------------------------------已规范化
	public function delete_line($line_id){
		$err=null;
		$i=0;
		if(!$this->has_line($line_id))
			$err[$i++]="线路不存在或已删除";
		else{
			$line=$this->get_line($line_id);
			$this->queries="DELETE FROM liness WHERE line_id=".$line_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]=("删除线路失败，请稍后再试");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."删除了线路".$line['line_name']);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	//是否存在某个线路--------------------------已规范化
	protected function has_line($line_id){
		$this->queries="SELECT `line_id` FROM `liness` WHERE `line_id`=".$line_id;
		$rows=$this->get_rows();
		if($rows&&sizeof($rows)>0)
			return true;
		return false;
	}
	public function get_line_vi_name($line_name){
		$this->queries="SELECT `line_id` FROM `liness` WHERE `line_name`='" . $line_name . "'";
		$result=$this->get_rows();
		if($result)
			return $result[0];
		return 0;
	}
	protected function get_group_name_vi_id($group_id){
		$this->queries="SELECT group_name FROM groups WHERE group_id=" . $group_id;
		$result=$this->get_rows();
		if($result)
			return $result[0]['group_name'];
		return false;
	}
	protected function get_line_name_vi_id($line_id){
		$this->queries="SELECT line_name FROM `liness` WHERE `line_id`=" . $line_id;
		$result=$this->get_rows();

		if($result)
			return $result[0]['line_name'];
		return false;
	}
	public function get_all_lines(){
		$this->queries="SELECT * FROM liness";
		return $this->get_rows();
	}
	public function get_all_lines_info_index_id(){
		$this->queries="SELECT * FROM liness";
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$this->queries);
			if(!$result)
				return null;
			while($row=mysqli_fetch_array($result))
				$rows[$row['line_id']]=$row;
		}
		else{
			$result=mysql_query($this->queries,$this->dbcon);
			if(!$result)
				return null;
			while($row=mysql_fetch_array($result))
				$rows[$row['line_id']]=$row;
		}
		return $rows;
	}
	/*******************************************
	 *USERS
	 *user_role 1系统管理员  2超级管理员 3设备管理员 5普通用户
	 ******************************************/
	/*
	 *添加用户
	 *返回值：true，添加成功；false，添加失败
	 */
	public function add_user($user_name,$passwd,$user_role=5,$user_phone=0,$user_email=null,$is_send=0){
		$err=null;
		$i=0;
		$passwdd=md5($passwd);
		if ($this->has_user_name($user_name))
			$err[$i++]=("用户名已存在");
		if($i==0){
			$this->queries="INSERT INTO users(user_name,passwd,user_role,user_phone,user_email,is_send,last_login_time,register_time) VALUES('".$user_name."','".$passwdd."',".$user_role.",'".$user_phone."','".$user_email."',".$is_send.",NOW(),NOW())";
			$result=$this->get_result();
			if(!$result)
				$err[$i++]=("添加用户失败，请联系管理员");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了用户".$user_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;	
	}	
	/*
	 *编辑用户
	 *返回值：true，编辑成功；false，编辑失败
	 */
	public function update_user($user_id,$user_name,$passwd,$user_role,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		$passwdd=md5($passwd);
		if (($o_user_id=$this->has_user_name($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($i==0){
			$this->queries="UPDATE users SET user_name='".$user_name."',passwd='".$passwdd."',user_role=".$user_role.",user_phone='".$user_phone."',user_email='".$user_email."',is_send=".$is_send." WHERE user_id=".$user_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="修改用户失败，请联系管理员";
			else{
				global $__USER;
				$user=$this->get_user($user_id);
				$this->add_dolog($__USER['user_name']."修改了用户".$user['user_name']."的资料");
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;	
	}

	public function update_userself($user_id,$old_name,$user_name,$oldpasswd,$passwd,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		if($passwd==""||$passwd==null)
			$passwd=$oldpasswd;
		$passwdd=md5($passwd);
		if (($o_user_id=$this->has_user_name($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($this->get_user_vi_name_pwd($old_name,$oldpasswd)==null)
			$err[$i++]=("旧密码输入错误");
		if($i==0){
			$this->queries="UPDATE users SET user_name='".$user_name."',passwd='".$passwdd."',user_phone='".$user_phone."',user_email='".$user_email."',is_send=".$is_send." WHERE user_id=".$user_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="修改用户失败，请联系管理员";
			else
				return true;
		}
		$this->set_errors($i,$err);
		return false;	
	}

	public function update_user_nopwd($user_id,$user_name,$user_role,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		$passwdd=md5($passwd);
		if (($o_user_id=$this->has_user_name($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($i==0){
			$this->queries="UPDATE users SET user_name='".$user_name."',user_role=".$user_role.",user_phone='".$user_phone."',user_email='".$user_email."',is_send=".$is_send." WHERE user_id=".$user_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="修改用户失败，请联系管理员";
			else{
				global $__USER;
				$user=$this->get_user($user_id);
				$this->add_dolog($__USER['user_name']."修改了用户".$user['user_name']."的资料");
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;	
	}
	public function delete_user($user_id){
		$err=null;
		$i=0;
		if(!$this->has_user($user_id))
			$err[$i++]="用户不存在或已删除";
		if($user_id==1)
			$err[$i++]="权限不足，此用户无法删除";
		if($i==0){
			$user=$this->get_user($user_id);
			$this->queries="DELETE FROM users WHERE user_id=".$user_id;
			$result=$this->get_result();
			if(!$result)
				$err[$i++]="删除用户失败，请联系管理员";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."删除了用户".$user['user_name']);
				return true;
			}
		}		
		$this->set_errors($i,$err);
		return false;
	}
	//是否存在某用户ID
	protected function has_user($user_id){
		$this->queries="SELECT user_id FROM users WHERE user_name='".$user_id."'";
		$result=$this->get_result();
		if(!$result)
			return false;
		return true;
	} 
	//是否存在某用户名
	protected function has_user_name($user_name){
		$this->queries="SELECT user_id FROM users WHERE user_name='".$user_name."'";
		$rows=$this->get_rows();
		if($rows!=null)
			return $rows[0]['user_id'];
		return false;
	}
	public function get_user_vi_name_pwd($user_name,$passwd){
		$passwdd=md5($passwd);
		$this->queries="SELECT * FROM users WHERE user_name='".$user_name."' AND passwd='".$passwdd."'";
		$rows=$this->get_rows();
		if($rows!=null)
			return $rows[0];
		return null;
	}
	//更新用户最后登陆时间
	public function update_user_last_login_time($user_id){
		$this->queries="UPDATE users SET last_login_time=NOW() WHERE user_id=".$user_id;
		$result=$this->get_result();
		return $result;
	}
	public function get_user($user_id){
		$this->queries="SELECT user_id,user_name,user_role,user_phone,user_email,is_send,last_login_time,register_time FROM users WHERE user_id=".$user_id;
		$rows=$this->get_rows();
		if($rows!=null)
			return $rows[0];
		return null;

	}
	public function get_all_users(){
		$this->queries="SELECT user_id,user_name,user_role,user_phone,user_email,is_send,last_login_time,register_time FROM users WHERE user_role<>1 ORDER BY user_role";
		$rows=$this->get_rows();
		if(!$rows)
			return null;
		return $rows;
	}

	public function get_alarms(){
		$this->queries="SELECT * FROM alarms ORDER BY action_time DESC";
		$rows=$this->get_rows();
		$devsarr=$this->get_all_devs_info_index_id();
		$groupsarr=$this->get_all_groups_info_index_id();
		$linesarr=$this->get_all_lines_info_index_id();
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groupsarr[$devsarr[$row['dev_id']]['group_id']]))?($groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_loc'].'-'.$groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($linesarr[$devsarr[$row['dev_id']]['line_id']]))?$linesarr[$devsarr[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}

	public function get_last_alarms(){
		$this->queries="SELECT * FROM alarms ORDER BY action_time DESC LIMIT 0,20";
		$rows=$this->get_rows();
		$devsarr=$this->get_all_devs_info_index_id();
		$groupsarr=$this->get_all_groups_info_index_id();
		$linesarr=$this->get_all_lines_info_index_id();
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groupsarr[$devsarr[$row['dev_id']]['group_id']]))?($groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_loc'].'-'.$groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($linesarr[$devsarr[$row['dev_id']]['line_id']]))?$linesarr[$devsarr[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}
	public function get_last_alarm_id(){
		$this->queries="SELECT id FROM alarms ORDER BY id DESC LIMIT 0,1 ";
		$rows=$this->get_rows();
		if($rows)
			return $rows[0]['id'];
		return 0;
	}
	public function get_alarm($id){
		$this->queries="SELECT * FROM alarms where id=".$id;
		$rows=$this->get_rows();
		if($rows)
			return $rows[0];
		return null;
	}
	public function get_dev_alarms($dev_id,$date_from,$date_to){

		$this->queries="SELECT * FROM alarms where dev_id=".$dev_id." AND action_time BETWEEN '".$date_from."' AND '".$date_to."' ORDER BY action_time";
				$rows=$this->get_rows();
		if($rows)
			return $rows;
		return null;
	}

	public function get_histories(){
		$this->queries="SELECT * FROM histories ORDER BY action_time DESC";
		$rows=$this->get_rows();
		$devsarr=$this->get_all_devs_info_index_id();
		$groupsarr=$this->get_all_groups_info_index_id();
		$linesarr=$this->get_all_lines_info_index_id();
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devsarr[$row['dev_id']]))?$devsarr[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groupsarr[$devsarr[$row['dev_id']]['group_id']]))?($groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_loc'].'-'.$groupsarr[$devsarr[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($linesarr[$devsarr[$row['dev_id']]['line_id']]))?$linesarr[$devsarr[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}

	public function output_devs_to_excel(){
		$this->queries="SELECT * FROM devs";
		$rows=$this->get_rows();
		if(!$rows)
			return null;
		$rerows=null;
		$groups=$this->get_all_groups_info_index_id();
		$lines=$this->get_all_lines_info_index_id();
		foreach ($rows as $key => $row) {
			$rerows[$key]=$row;
			$rerows[$key]['group_name']=(isset($groups[$row['group_id']]['group_name']))?$groups[$row['group_id']]['group_name']:'无';
			$rerows[$key]['group_loc']=(isset($groups[$row['group_id']]['group_loc']))?$groups[$row['group_id']]['group_loc']:'无';
			$rerows[$key]['coor_long']=(isset($groups[$row['group_id']]['coor_long']))?$groups[$row['group_id']]['coor_long']:0.0;
			$rerows[$key]['coor_lat']=(isset($groups[$row['group_id']]['coor_lat']))?$groups[$row['group_id']]['coor_lat']:0.0;
			$rerows[$key]['line_name']=(isset($lines[$row['line_id']]['line_name']))?$lines[$row['line_id']]['line_name']:'无';
		}
		return $rerows;
	}
	public function input_devs_from_excel($devs){
		$i=0;
		$err=null;
		foreach ($devs as $dev) {
			$dev_id=$this->get_dev_vi_number($dev['dev_number']);
			if($dev_id!=0){
				$err[$i++]="设备[".$dev['dev_number']."]已存在";
				continue;
			}
			$line_id=($dev['line_name']=='无')?0:$this->get_line_vi_name($dev['line_name'])['line_id'];
			if($line_id==0&&$dev['line_name']!='无'){
				$this->add_line($dev['line_name']);
				$line_id=$this->get_line_vi_name($dev['line_name']);
				$err[$i++]="新插入了线路[".$dev['line_name']."]";
			}
			$group_id=($dev['group_name']=='无')?0:$this->get_group_id($dev['group_name'],$dev['group_loc']);
			if($group_id==0&&$dev['group_name']!='无'){
				$this->add_group($dev['group_name'],$dev['group_loc'],$line_id,0,$dev['coor_long'],$dev['coor_lat']);
				$group_id=$this->get_group_id($dev['group_name'],$dev['group_loc']);
				$err[$i++]="新插入了杆塔[".$dev['line_name']."]";
			}
			elseif(!$this->is_group_has_line($group_id,$line_id)){
				$err[$i++]="设备[".$dev['dev_number']."]插入失败，导入的杆塔未绑定相应线路";
				continue;
			}
			if($this->add_dev($dev['dev_number'],$dev['dev_phase'],$group_id,$line_id))
				$err[$i++]="设备[".$dev['dev_number']."]插入成功";
			else{
				for($j=0;$j<$this->last_errors['count'];$j++)
					$err[$i++]=$this->last_errors['errors'][$j];
			}
		}
		return $err;
	}

	protected function add_dolog($msg){
		$this->queries="INSERT INTO dologs(do_msg) VALUES('".$msg."')";
		$result=$this->get_result();
	}
	public function get_dologs(){
		$this->queries="SELECT * FROM dologs ORDER BY do_time DESC";
		$rows=$this->get_rows();
		return $rows;
	}
}
?>