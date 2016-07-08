<?php
//数据库接口封装
class db{
	//保存查询结果或查询是否成功的布尔值
	protected $result;
	//保存查询结果的列的列（字段）属性
	protected $col_info;
	//保存执行的查询语句
	var $queries;
	//设置数据库重连次数
	protected $reconnect_retries = 5;
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
	protected function get_rows($query){
		if(!$query)
			return null;
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$query);
			$i=0;
			while($row=mysqli_fetch_array($result))
				$rows[$i++]=$row;
		}
		else{
			$result=mysql_query($query,$this->dbcon);
			$i=0;
			while($row=mysql_fetch_array($result))
				$rows[$i++]=$row;
		}
		return $rows;
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
	//返回所有设备信息数组
	public function get_all_devs(){
		$query ="SELECT * FROM devs";
		return $this->get_rows($query);
	}
	protected function load_dev_info($dev_number){
		$query ="SELECT devs.dev_number,devs.dev_name,devs.dev_phase,groups.group_name,groups.group_loc,`liness`.line_name FROM devs,groups,`liness` WHERE devs.dev_number='" . $dev_number . "' AND devs.group_id=groups.group_id AND devs.line_id=`liness`.line_id";
		if($this->is_use_mysqli)
			$this->result=mysqli_query($this->dbcon,$query);
		else
			$this->result=mysql_query($query,$this->dbcon);
	}
	//返回指定设备信息
	public function get_dev_info($dev_number=null){
		if($dev_number==null)
			die("没有获取到设备编号");
		$this->load_dev_info($dev_number);
		if(!$this->result)
			return false;
		if($this->is_use_mysqli)
			$row=mysqli_fetch_array($this->result);
		else
			$row=mysql_fetch_array($this->result);
		return $row;
	} 
	public function add_dev_vi_GLname($dev_number,$dev_name,$dev_phase,$group_name,$group_loc,$line_name ){
		$group_id=$this->get_group_id($group_name,$group_loc);
		$line_id=$this->get_line_id($line_name);
		return add_dev($dev_number,$dev_name,$dev_phase,$group_id,$line_id);
	}
	public function add_dev($dev_number,$dev_name,$dev_phase,$group_id,$line_id){
		$err=null;
		$i=0;
		if(!$this->is_group_has_line($group_id,$line_id))
			$err[$i++]="指定杆塔未绑定相应线路";
		if($this->has_dev($dev_number))
			$err[$i++]="设备编号为".$dev_number."的设备已存在";
		elseif($o_dev_id=$this->is_group_line_phase_has_dev($dev_phase,$group_id,$line_id))
			$err[$i++]="设备编号为".$o_dev_id."的设备已存在于此位置（含有相同的杆塔、线路和相位信息）";
		if($i==0){
			$query="INSERT INTO devs(dev_number,dev_name,dev_phase,group_id,line_id) VALUES('" . $dev_number."','".$dev_name."','".$dev_phase."',".$group_id.",".$line_id . ")";
			if($this->is_use_mysqli)
				$result=mysqli_query($this->dbcon,$query);
			else 
				$result=mysql_query($query,$this->dbcon);
			if(!$result)
				$err[$i++]="添加设备失败，请稍后再试";
		}
		$results=array('err_count'=>$i,'err'=>$err);
		return $results;
	}
	//返回所有杆塔信息数组
	public function get_all_groups(){
		$query="SELECT * FROM groups ORDER BY group_loc";
		return $this->get_rows($query);
	}
	public function get_line_vi_gid($group_id){
		$query="SELECT groups.group_id,liness.line_id,liness.line_name FROM groups,liness WHERE groups.group_id=".$group_id." AND (groups.line_id=liness.line_id OR groups.line_id2=liness.line_id)";
		return $this->get_rows($query);
	}
	public function add_group_vi_line_name($group_name,$group_loc,$line_name=null,$line_name2=null,$coor_long,$coor_lat){
		
		$line_id=$this->get_line_id($line_name);
		$line_id2=$this->get_line_id($line_name2);
		$results=add_group($group_name,$group_loc,$line_id,$line_id2,$coor_long,$coor_lat);
		return $results;
	}
	public function get_all_groups_info_vi_id(){
		$query="SELECT * FROM groups";
		return $this->get_group_info_rows($query);
	}
	protected function get_group_info_rows($query){
		if(!$query)
			return null;
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$query);
			$i=0;
			while($row=mysqli_fetch_array($result))
				$rows[$row['group_id']]=$row;
		}
		else{
			$result=mysql_query($query,$this->dbcon);
			$i=0;
			while($row=mysql_fetch_array($result))
				$rows[$row['group_id']]=$row;
		}
		return $rows;
	}
	public function add_group($group_name,$group_loc,$line_id=0,$line_id2=0,$coor_long,$coor_lat){
		$err=null;
		$i=0;
		if($this->get_group_id($group_name, $group_loc))
			$err[$i++]="已有相同名字的杆塔名和杆塔地址";
		if($line_id==$line_id2&&$line_id)
			$err[$i++]="一个杆塔的两条线路不能相同";

		if($i==0){
			$query="INSERT INTO groups(group_name,group_loc,line_id,line_id2,coor_long,coor_lat) VALUES('".$group_name."','".$group_loc."',".$line_id.",".$line_id2.",".$coor_long.",".$coor_lat.")";
			if($this->is_use_mysqli)
				$result=mysqli_query($this->dbcon,$query);
			else 
				$result=mysql_query($query,$this->dbcon);
			if(!$result)
				$err[$i++]="添加线路失败，请稍后再试";
		}
		$results=array('err_count'=>$i,'err'=>$err);
		return $results;
	}
	public function get_all_lines(){
		$query="SELECT * FROM liness";
		return $this->get_rows($query);
	}
	public function get_all_lines_name_vi_id(){
		$query="SELECT * FROM liness";
		return $this->get_line_name_rows($query);
	}
	protected function get_line_name_rows($query){
		if(!$query)
			return null;
		$rows=null;
		if($this->is_use_mysqli){
			$result=mysqli_query($this->dbcon,$query);
			$i=0;
			while($row=mysqli_fetch_array($result))
				$rows[$row['line_id']]=$row['line_name'];
		}
		else{
			$result=mysql_query($query,$this->dbcon);
			$i=0;
			while($row=mysql_fetch_array($result))
				$rows[$row['line_id']]=$row['line_name'];
		}
		return $rows;
	}
	public function add_line($line_name){
		if($this->get_line_id($line_name))
			die("添加失败，已有相同名字的线路");
		$query="INSERT INTO `liness`(`line_name`) VALUES ('".$line_name."')";
		if($this->is_use_mysqli)
			$result=mysqli_query($this->dbcon,$query);
		else 
			$result=mysql_query($query,$this->dbcon);
		if(!$result)
			die("添加线路失败");
	}
	protected function get_group_id($group_name,$group_loc){
		$query="SELECT group_id FROM groups WHERE group_name='" . $group_name . "' AND group_loc='" . $group_loc ."'";
		$result=$this->get_rows($query);
		if($result)
			return $result[0]['group_id'];
		return 0;
	}
	protected function get_line_id($line_name){
		$query="SELECT `line_id` FROM `liness` WHERE `line_name`='" . $line_name . "'";
		$result=get_rows($query);
		if($result)
			return $result[0]['line_id'];
		return 0;
	}
	protected function get_group_name_vi_id($group_id){
		$query="SELECT group_name FROM groups WHERE group_id=" . $group_id;
		$result=$this->get_rows($query);
		if($result)
			return $result['group_name'];
		return "杆塔已删除";
	}
	protected function get_line_name_vi_id($line_id){
		$query="SELECT line_name FROM `liness` WHERE `line_id`=" . $line_id;
		$result=get_rows($query);
		if($result)
			return $result['line_id'];
		return "线路已删除";
	}
	//指定杆塔是否绑定了指定线路
	public function is_group_has_line($group_id,$line_id){
		$query="SELECT group_id FROM groups WHERE group_id=".$group_id." AND (line_id=".$line_id." OR line_id2=".$line_id.")";
		$rows=$this->get_rows($query);
		if($rows&&count($rows)>0)
			return true;
		return false;
	}
	public function has_dev($dev_number){
		$query="SELECT group_id FROM devs WHERE dev_number=".$dev_number;
		$rows=$this->get_rows($query);
		if($rows&&count($rows)>0)
			return true;
		return false;
	}
	//某杆塔和线路的某相位上是否存在设备，若存在返回存在设备编号
	public function is_group_line_phase_has_dev($dev_phase,$group_id,$line_id){
		$query="SELECT dev_number FROM devs WHERE group_id=".$group_id." AND line_id=".$line_id." AND dev_phase='".$dev_phase."'" ;
		$rows=$this->get_rows($query);
		if($rows&&count($rows)>0)
			return $rows[0]['dev_number'];
		return false;
	}
	public function get_user_id_vie_pwd($user_name,$passwd){
		$passwdd=md5($passwd);
		$query="SELECT user_id FROM users WHERE user_name='" . $user_name . "' AND passwd='" . $passwdd ."'";
		if($this->is_use_mysqli)
			$row=mysqli_fetch_array(mysqli_query($this->dbcon,$query));
		else
			$row=mysql_fetch_array(mysql_query($query,$this->dbcon));
		return $row['user_id'];
		
	}
	public function add_user($user_name,$passwd,$user_role=10,$register_time=null){
		$passwdd=md5($passwd);
		if ($this->get_user_id_vie_pwd($user_name, $passwd))
			die("用户名已存在");
		$last_login_time=time();
		$register_time=$register_time?$register_time:time();
		$query="INSERT INTO users(user_name,passwd,user_role,last_login_time,register_time) VALUES('".$user_name."','".$passwdd."','".$user_role."','".$last_login_time."','".$register_time."')";
		echo $query;
		if($this->is_use_mysqli)
			$result=mysqli_query($this->dbcon,$query);
		else
			$result=mysql_query($query,$this->dbcon);
		if(!$result)
			die("添加用户失败");
	}
}
?>