<?php
require_once ABSPATH.'/include/pagination.class.php';
//数据库接口封装
class db{
	//保存查询结果或查询是否成功的布尔值
	protected $result;
	//保存查询结果的列的列（字段）属性
	protected $col_info;
	//保存执行的查询语句
	protected $queries;
	
	protected $last_errors=null;
	//设置数据库重连次数
	//protected $reconnect_retries = 5;
	//保存分页对象数组
	protected $pgn;
	//所有的结果行
	protected $total_results;
	//分页的html
	protected $pgn_html=null;
	//数据库前缀
	protected $prefix = '';
	//数据库连接句柄
	protected $dbcon;
	//是否数据库已连接
	protected $has_connected=false;
	protected $ready=false;
	private $is_use_mysqli=false;
	/** 构造函数 **/
	public function __construct() {
		register_shutdown_function( array( $this, '__destruct' ) );
		/*若存在mysqli模块则启用*/
		if ( function_exists( 'mysqli_connect' ) ) 
			$this->is_use_mysqli = true;
		$this->prefix=DB_PREFIX;
		$this->db_connect();
	}
	/** 析构函数 **/
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

	/**
	 * db_connect
	 * 连接数据库
	 * @param void
	 * @return void
	 **/	
	public function db_connect() {

		$new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;
		if ( $this->is_use_mysqli ) {
			$this->dbcon = mysqli_init();
			// mysqli不支持数据库主机参数带有端口号
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

	/**
	 * db_close
	 * 关闭数据库连接
	 * @param void
	 * @return void
	 **/	
	public function db_close(){
		$this->flush();
		if($this->has_connected){
			if($this->is_use_mysqli)
				mysqli_close($this->dbcon);
			else
				mysql_close($this->dbcon);
			$this->has_connected=false;
		}
	}

	/**
	 * flush
	 * 重置连接和结果变量
	 * @param void
	 * @return void
	 **/		
	public function flush() {
		$this->col_info = null;
		$this->set_errors(0,array());
		if ( $this->is_use_mysqli && $this->result instanceof mysqli_result ) {
			mysqli_free_result( $this->result );
			$this->result = null;

			// 检查句柄
			if ( empty( $this->dbcon ) || !( $this->dbcon instanceof mysqli ) ) {
				return;
			}
			while ( mysqli_more_results( $this->dbcon ) ) {
				mysqli_next_result( $this->dbcon );
			}
		} elseif ( is_resource( $this->result ) ) {
			mysql_free_result( $this->result );
		}
	}

	/**
	 * set_charset
	 * 设置数据库编码
	 * @param 1 string $dbh 数据库连接的句柄
	 * @param 2 string $charset 编码,默认为配置文件的编码设置
	 * @return void
	 **/
	public function set_charset( $dbh, $charset = null) {
		if ( ! isset( $charset ) )
			$charset = DB_CHARSET;
		if ( $this->is_use_mysqli)
			mysqli_set_charset( $dbh, $charset );		
 		else 
			mysql_set_charset( $charset, $dbh );	
	}

	/**
	 * select
	 * 选择数据库
	 * @param 1 string $db 数据库名
	 * @param 2 string $dbh 数据库连接的句柄,null为默认句柄
	 * @return void 选择失败跳转错误页面
	 **/
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

	/**
	 * get_rows
	 * 获取结果集的所有行数
	 * @param null/string $index 可选,null-下标由0开始自增;[string]-下标为列$index对应的列值
	 * @return null/array null-结果集为空;array-结果集的二维数组
	 **/
	protected function get_rows($index=null){
		if(!$this->queries)
			return null;
		$rows=null;
		$this->get_result();
		if($this->is_use_mysqli){
			if(!$this->result)
				return null;
			if($index==null){
				$i=0;
				while($row=mysqli_fetch_array($this->result))
					$rows[$i++]=$row;
			}
			else{
				if($this->has_col($index))
					while($row=mysqli_fetch_array($this->result))
						$rows[$row[$index]]=$row;
			}
		}
		else{
			if(!$this->result)
				return null;
			if($index==null){
				$i=0;
				while($row=mysql_fetch_array($result))
					$rows[$i++]=$row;
			}
			else{
				if($this->has_col($index))
					while($row=mysql_fetch_array($result))
						$rows[$row[$index]]=$row;
			}
		}
		return $rows;
	}
	/**
	 * get_result
	 * 获取结果集
	 * @param void
	 * @return bool/obj UPDATE,DELETE,INSERT返回语句执行结果的布尔值;SELECT等查询语句若查询失败返回false,成功返回结果对象
	 **/
	protected function get_result(){
		if(empty($this->queries))
			return $this->result;
		$this->flush();
		if($this->is_use_mysqli)
			$this->result=mysqli_query($this->dbcon,$this->queries);
		else
			$this->result=mysql_query($this->queries,$this->dbcon);
		return $this->result;
		$this->queries='';
	}

	/**
	 * load_col_info
	 * 载入结果集的字段信息(name,table,maxlength...),存入到$col_info
	 * @param void
	 * @return void
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

	/**
	 * get_col_info
	 * 获取结果集的一个或多个字段信息
	 * @param 1 string $info_type 需要获取的信息类型,默认为name
	 * @param 2 int $col_offset 需要获取的字段下标,默认为所有字段
	 * @return string/array string-当指定$col_offset的值时,返回指定字段的信息;array-一维数组,当
	 *         $col_offset值为-1时,返回所有字段信息的一维数组
	 **/
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

	/**
	 * has_col
	 * 是否存在某个字段名
	 * @param string $col_name 需要检索的字段名
	 * @return bool false-不存在;true-存在
	 **/
	protected function has_col($col_name){
		$this->load_col_info();
		if ( $this->is_use_mysqli ) {
			for ( $i = 0; $i < @mysqli_num_fields( $this->result ); $i++ ) {
				if($this->col_info[ $i ]->{'name'} ==$col_name)
					return true;
			}
		} else {
			for ( $i = 0; $i < @mysql_num_fields( $this->result ); $i++ ) {
				if($this->col_info[ $i ]->{'name'} ==$col_name)
					return true;
			}
		}
		return false;
	}

	/**
	 * set_errors
	 * 设置$this->last_errors二维数组,保存错误信息
	 * @param int $err_count 错误信息个数
	 * @param array $err_arr 错误信息的一维数组
	 * @return void
	 **/
	protected function set_errors($err_count,$err_arr){
		$this->last_errors['count']=$err_count;
		$this->last_errors['errors']=$err_arr;
	}

	/**
	 * set_pagination
	 * 分页设置
	 * @param string 分页的数据表名
	 * @return void
	 **/
	public function set_pagination($table){
		$this->queries="SELECT COUNT(*) FROM ".$this->prefix.$table;
		$rows=$this->get_rows();
		$this->total_results=empty($rows)?0:$rows[0][0];
		if(strpos($table,"users")>=0)
			$this->total_results--;
		$this->pgn[$table]=new pagination($this->total_results);
		$this->pgn_html=$this->pgn[$table]->showpgn();
	}

	/**
	 * _real_cols_by_ref
	 * 展开字段参数,若为字段串则不变,若为数组则转为字符串
	 * @param string/array &$cols 引用字段参数
	 * @return void
	 **/
	private function _real_cols_by_ref(&$cols){
		if(is_array($cols))
			$cols=implode(',',$cols);
	}

	/**
	 * _real_where_by_ref
	 * 展开where参数,若为字段串则不变,若为数组则转为字符串
	 * @param string/array &$where 引用where参数
	 * @return void
	 **/
	private function _real_where_by_ref(&$where){
		if (empty($where)) {
			return;
		}
		$str='';
		$i=0;
		if(is_array($where)){
			foreach($where as $k=>$v){
				if($i%2==0)
					$str.=(' '.$k.'='.$v);
				else{
					$str.=(' '.$v);
				}
				$i++;
			}
			$where=substr($str,1);
		}
		unset($str);
	}

	/**
	 * _real_orderby_by_ref
	 * 展开排序字段参数,若为字段串则不变,若为数组则转为字符串
	 * @param string/array &$orderby 引用排序字段参数
	 * @return void
	 **/
	private function _real_orderby_by_ref(&$orderby){
		if (empty($orderby)) {
			return;
		}
		if(is_array($orderby))
			$orderby=implode(',',$orderby);
	}

	/**
	 * get_table
	 * 获取一个查询结果集所有行的二维数组,下标重写为$index的列值
	 * @param string $table 数据表名
	 * @param string $index 结果数组的下标,$table的某一列
	 * @param string/array $cols 需要查询的字段,默认为所有字段
	 * @param array $where 条件查询,如:array('ID'=>'1','AND','dev_id'=>'2')
	 * @param array $orderby 排序基准字段
	 * @param bool $is_set_pgn 是否设置分页,默认为否
	 * @return  null/array null-$table表中无数据;array-下标为$index列值,数组值为$cols的列值的字符串数组
	 **/
	protected function get_table($table,$index=null,$cols='*',$where=array(),$orderby=array(),$is_set_pgn=false){
		if(empty($cols))
			return false;
		$this->_real_cols_by_ref($cols);
		$this->_real_where_by_ref($where);
		$this->_real_orderby_by_ref($orderby);
		$colss=($cols=='*')?'*':($index==null)?$cols:$cols.','.$index;
		if($is_set_pgn)
			$this->set_pagination($table);
		$this->queries='SELECT '.$colss.' FROM '.$this->prefix.$table;
		if(!empty($where))
			$this->queries.=' WHERE '.$where;
		if(!empty($orderby))
			$this->queries.=' ORDER BY '.$orderby;
		if($is_set_pgn)
			$this->queries.=' '.$this->pgn[$table]->limit;
		//echo $this->queries;
		$rows=$this->get_rows($index);
		return $rows;
	}
	/**
	 * insert_table
	 * 插入行
	 * @param string $table 数据表名
	 * @param string/array $arrays 需要插入的字段和值如array('name'=>'jerry','sex'=>'male')
	 * @return  bool true-插入成功;false-插入失败
	 **/
	protected function insert_table($table,$arrays){
		if(empty($arrays))
			return false;
		$keystr='';
		$valuestr='';
		if(is_array($arrays)){
			foreach ($arrays as $key => $value){
				$keystr.=',`'.$key.'`';
				$valuestr.=','.$value;
			}
		}
		else{
			$arrays=explode(',',$arrays);
			foreach ($arrays as $v) {
				$pos=strpos($v,'=');
				if($pos<0)
					continue;
				$keystr.=',`'.substr($v,0,$pos).'`';
				$keystr.=','.substr($v,$pos+1);
			}
		}
		$keystr=substr($keystr, 1);
		$valuestr=substr($valuestr, 1);
		$this->queries='INSERT INTO `'.$this->prefix.$table.'`('.$keystr.') VALUES('.$valuestr.')';
		return $this->get_result();
	}
	/**
	 * update_table
	 * 更新表的某一个或多个字段
	 * @param string $table 数据表名
	 * @param string/array $arrays 需要更新的字段和值如array('name'=>'jerry','sex'=>'male')
	 * @param string/array $where 条件查询,如:array('ID'=>'1','AND','dev_id'=>'2')
	 * @return  bool true-更新成功;false-更新失败
	 **/
	protected function update_table($table,$arrays,$where){
		$setstr='';
		$this->_real_where_by_ref($where);
		if(empty($arrays)||empty($where))
			return false;
		foreach ($arrays as $key => $value) 
				$setstr.=",`$key`=$value";
		$setstr=substr($setstr, 1);
		$this->queries='UPDATE `'.$this->prefix.$table.'` SET '.$setstr;
		if(!empty($where))
			$this->queries.=(' WHERE '.$where);
		return $this->get_result();
	}
	/**
	 * delete_table
	 * 删除行
	 * @param string $table 数据表名
	 * @param string/array $where 条件查询,如:array('ID'=>'1','AND','dev_id'=>'2')
	 * @return  bool true-删除成功;false-删除失败
	 **/
	protected function delete_table($table,$where){
		if(empty($where))
			return false;
		$this->_real_where_by_ref($where);
		$this->queries='DELETE FROM `'.$this->prefix.$table.'` WHERE '.$where;
		return $this->get_result();
	}
	/**
	 * get_reindex_string
	 * 获取一个一维字符串数组,下标重写为$index的列值,相应数组的值为$cols的列值(多个列之间用'-'连接);相同下标
	 * 以','连接为字符串
	 * @param string $table 数据表名
	 * @param string $index 重写的下标,$table的某一列
	 * @param string/array $cols 数组下标对应的值,$table的某一列或多列
	 * @return  null/array null-$table表中无数据;array-下标为$index列值,数组值为$cols的列值的字符串数组
	 **/
	public function get_reindex_string($table,$index,$cols){
		if(!is_array($cols))
			$this->queries='SELECT '.$cols.','.$index.' FROM '.$this->prefix.$table;
		else{
			$colss=implode(',',$cols);
			$this->queries='SELECT '.$colss.','.$index.' FROM '.$this->prefix.$table;
		}
		$rows=$this->get_rows();
		if($rows==null)
			return null;
		$rerows=null;
		//$rerows[0]='无';
		$i=0;
		foreach ($rows as $row) {
			if($row[$index]==0)
				continue;
			if(empty($rerows[$row[$index]])){
				if(!is_array($cols))
					$rerows[$row[$index]]=$row[$cols];
				else{
					$str='';
					foreach ($cols as $col){
						$str.='-';
						$str.=$row[$col];
					}
					$rerows[$row[$index]]=substr($str,1);
				}
			}
			else{
				if(!is_array($cols)){
					$rerows[$row[$index]].=', ';
					$rerows[$row[$index]].=$row[$cols];
				}
				else{
					$str='';
					foreach ($cols as $col){
						$str.='-';
						$str.=$row[$col];			
					}
					$rerows[$row[$index]].=', ';
					$rerows[$row[$index]].=substr($str,1);
				}
			}
		}
		return $rerows;
	}
	/*********************************************数据类分界********************************************************/
	/**
	 * add_dolog
	 * 添加操作日志
	 * @param string $msg 需要添加的操作信息字符串
	 * @return  bool true-添加成功;false-添加失败
	 **/
	protected function add_dolog($msg){
		$ip=get_ip();
		return $this->insert_table('dologs',array('do_msg'=>"'$msg'",'do_ip'=>"'$ip'"));
	}

	/**
	 * get_dologs
	 * 获取操作日志列表
	 * @param void
	 * @return  bool/array false-获取列表空,array-结果集二维数组
	 **/
	public function get_dologs(){
		return $this->get_table('dologs',null,'*',array(),array('do_time DESC'),true);
	}

	/********************************************************************************************
	 *dev
	 *******************************************************************************************/
	
	/**
	 * get_dev_id
	 * 获取设备id,伪重载函数
	 *  	重载1:$dev_number
	 *		重载2:$dev_phase,$group_id,$line_id
	 * @param string/string_int_int 设备编号/设备相位_杆塔id_线路id
	 * @return int -1-参数有误;0-无此设备;其他-设备id
	 **/
	public function get_dev_id(){
		$numargs=func_num_args();
		$args=func_get_args();
		if($numargs==1)
			$array=array('dev_number'=>"'$args[0]'");
		elseif($numargs==3)
			$array=array('dev_phase'=>"'$args[0]'",'AND','group_id'=>$args[1],'AND','line_id'=>$args[2]);
		else
			return -1;
		$dev=$this->get_table('devs',null,'dev_id',$array);
		return $dev?$dev[0]['dev_id']:0;
	}

	/**
	 * get_dev
	 * 获取设备数组
	 * @param int $dev_id 设备id
	 * @return null/array null-无此设备,array-设备信息数组
	 **/
	//获取设备信息，组合进杆塔和线路信息
	public function get_dev($dev_id){
		$devs=$this->get_table('devs',null,'*',array('dev_id'=>$dev_id));
		if(!$devs||count($devs)<1)
			return null;
		
		if($devs[0]['group_id']==0){
			$groups[0]['group_loc']='未绑定';
			$groups[0]['group_name']='&nbsp;';
		}
		else
			$groups=$this->get_table('groups',null,'*',array('group_id'=>$devs[0]['group_id']));
		if($devs[0]['line_id']==0)
			$lines[0]['line_name']='未绑定';
		else
			$lines=$this->get_table('liness',null,'*',array('line_id'=>$devs[0]['line_id']));
		$devs[0]['group_loc']=isset($groups[0]['group_loc'])?$groups[0]['group_loc']:'杆塔已删除';
		$devs[0]['group_name']=isset($groups[0]['group_name'])?$groups[0]['group_name']:'&nbsp;';
		$devs[0]['line_name']=isset($lines[0]['line_name'])?$lines[0]['line_name']:'线路已删除';
		unset($groups);
		unset($lines);
		return $devs[0];

	}

	/**
	 * get_all_devs
	 * 获取所有设备的二维数组,分页显示
	 * @param void 
	 * @return null/array null-无设备;array-二维数组存储的设备信息
	 **/
	public function get_all_devs(){
		$devs=$this->get_table('devs','dev_id','*',array(),array('line_id','group_id','dev_phase'),true);
		$groups=$this->get_table('groups','group_id');
		$lines=$this->get_table('liness','line_id');
		$groups[0]['group_loc']=$linesarr[0]['line_name']='未绑定';
		$groups[0]['group_name']='&nbsp;';
		if($devs&&count($devs)>0)
			foreach ($devs as $k => $dev) {
				$devs[$k]['group_loc']=isset($groups[$dev['group_id']]['group_loc'])?$groups[$dev['group_id']]['group_loc']:'杆塔已删除';
				$devs[$k]['group_name']=isset($groups[$dev['group_id']]['group_name'])?$groups[$dev['group_id']]['group_name']:'&nbsp;';
				$devs[$k]['line_name']=isset($lines[$dev['line_id']]['line_name'])?$lines[$dev['line_id']]['line_name']:'线路已删除';

			}
		unset($groups);
		unset($lines);
		return $devs;
	}

	/**
	 * add_dev
	 * 添加新设备
	 * @param string $dev_number 设备编号,长度为10位
	 * @param string $dev_phase 设备相位
	 * @param int $group_id 杆塔id
	 * @param int $line_id 线路id
	 * @return bool true-添加成功,false-添加失败
	 **/
	public function add_dev($dev_number,$dev_phase,$group_id,$line_id){
		$err=null;
		$i=0;
		if(!isset($dev_number)||strlen($dev_number)!=10)
			$err[$i++]='设备编号位数错误';
		if(!in_array($dev_phase, array('A相','B相','C相')))
			$err[$i++]='设备相位错误';
		if(!$this->is_group_on_line($group_id,$line_id))
			$err[$i++]='指定杆塔未绑定相应线路';
		if($this->get_dev_id($dev_number)>0)
			$err[$i++]='设备编号为'.$dev_number.'的设备已存在';
		elseif(($o_dev_id=$this->get_dev_id($dev_phase,$group_id,$line_id))>0){
			$err[$i++]="设备编号为".$this->get_dev($o_dev_id)['dev_number']."的设备已存在于此位置（含有相同的杆塔、线路和相位信息）";
		}
		if($i==0){
			if(!$this->insert_table('devs',array('dev_number'=>"'$dev_number'",'dev_phase'=>"'$dev_phase'",'group_id'=>$group_id,'line_id'=>$line_id)))
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
	/**
	 * update_dev
	 * 编辑设备
	 * @param int $dev_id 设备id
	 * @param string $dev_number 设备编号,长度为10位
	 * @param string $dev_phase 设备相位
	 * @param int $group_id 杆塔id
	 * @param int $line_id 线路id
	 * @return bool true-编辑成功,false-编辑失败
	 **/
	public function update_dev($dev_id,$dev_number,$dev_phase,$group_id,$line_id){
		$err=null;
		$i=0;
		if(!$this->is_group_on_line($group_id,$line_id))
			$err[$i++]="指定杆塔未绑定相应线路";
		if(($flagid=$this->get_dev_id($dev_number))&&$flagid!=$dev_id)
			$err[$i++]="设备编号为".$dev_number."的设备已存在";
		elseif(($o_dev_id=$this->get_dev_id($dev_phase,$group_id,$line_id))>0){
			$err[$i++]="设备编号为".$this->get_dev($o_dev_id)['dev_number']."的设备已存在于此位置（含有相同的杆塔、线路和相位信息）";
		}
		if($i==0){
			$o_dev_name=$this->get_dev($dev_id)['dev_name'];
			$colvs=array('dev_number'=>"'$dev_number'",'dev_phase'=>"'$dev_phase'",'group_id'=>$group_id,'line_id'=>$line_id);
			if(!$this->update_table('devs',$colvs,array('dev_id'=>$dev_id)))
				$err[$i++]="修改设备信息失败";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."修改了设备".$o_dev_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}

	/**
	 * delete_dev
	 * 删除设备
	 * @param int $dev_id 设备id
	 * @return bool true-删除成功,false-删除失败
	 **/
	public function delete_dev($dev_id){
		$err=null;
		$i=0;
		if(empty($this->get_dev($dev_id)))
			$err[$i++]="设备不存在或已删除";
		else{
			$o_dev_number=$this->get_dev($dev_id)['dev_number'];
			if(!$this->delete_table('devs',array('dev_id'=>$dev_id)))
				$err[$i++]="删除失败失败，请稍后再试";
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."删除了设备".$o_dev_number);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}

	/**
	 * get_all_devs_number_index_line_id_group_id_dev_phase
	 * 返回一个一维数组，用【线路ID】-【杆塔ID】-【相位】下标反查设备编号
	 * @param void
	 * @return null/array null-无设备;array-一维数组存储的反查信息
	 **/
	protected function get_all_devs_number_index_line_id_group_id_dev_phase(){
		$rows=$this->get_table('devs',null,'*');
		if(!$rows||sizeof($rows)<1)
			return null;
		$rerows=null;
		foreach ($rows as $row) {
			$rerows[$row['line_id'].'-'.$row['group_id'].'-'.$row['dev_phase']]=$row['dev_number'];
		}
		unset($rows);
		return $rerows;
	}

	/************************************************************************************
	 *group
	 ************************************************************************************/
	
	/**
	 * get_group_id
	 * 获取杆塔id
	 * @param string $group_name 杆塔名
	 * @param string $group_loc 自定杆塔位置
	 * @return int 0-无此杆塔;其他-杆塔id
	 **/
	public function get_group_id($group_name,$group_loc){
		$groups=$this->get_table('groups',null,'*',array('group_name'=>"'$group_name'",'AND','group_loc'=>"'$group_loc'"));
		if($groups&&count($groups)>0)
			return $groups[0]['group_id'];
		return 0;
	}

	/**
	 * get_group
	 * 获取杆塔信息
	 * @param int $group_id 杆塔id
	 * @return null/array null-无此杆塔;array-一维数组存储的杆塔信息
	 **/
	public function get_group($group_id){
		$groups=$this->get_table('groups',null,'*',array('group_id'=>$group_id));
		if(!$groups||count($groups)<1)
			return null;
		if($groups[0]['line_id']==0)
			$groups[0]['line_name']='未绑定';
		else{
			$lines=$this->get_table('lines',null,'line_name',array('line_id'=>$groups[0]['line_id']));
			$groups[0]['line_name']=isset($lines[0]['line_name'])?$lines[0]['line_name']:'已删除';
		}
		if($groups[0]['line_id2']==0)
			$groups[0]['line_name2']='未绑定';
		else{
			$lines=$this->get_table('lines',null,'line_name',array('line_id'=>$groups[0]['line_id2']));
			$groups[0]['line_name2']=isset($lines[0]['line_name'])?$lines[0]['line_name']:'已删除';
		}
		unset($lines);
		return $groups[0];
	}

	/**
	 * get_all_groups
	 * 获取所有杆塔信息的二维数组
	 * @param void
	 * @return null/array null-无杆塔;array-二维数组存储的杆塔信息列表
	 **/
	public function get_all_groups(){
		$groups=$this->get_table('groups','group_id','*',array(),array('group_loc','group_name'),true);
		if(!$groups||count($groups)<1)
			return null;
		$lines=$this->get_table('liness','line_id','line_name');
		$devsnumber=$this->get_all_devs_number_index_line_id_group_id_dev_phase();
		$usergroups=$this->get_table('usergroups','user_gid','*');
		$lines[0]['line_name']='未绑定';
		$usergroups[0]['user_gname']='默认';
		foreach ($groups as $k => $group) {
			$groups[$k]['line_name']=isset($lines[$group['line_id']]['line_name'])?$lines[$group['line_id']]['line_name']:'线路已删除';
			$groups[$k]['line_name2']=isset($lines[$group['line_id2']]['line_name'])?$lines[$group['line_id2']]['line_name']:'线路已删除';
			$groups[$k]['dev_on_A']=isset($devsnumber[$group['line_id'].'-'.$group['group_id'].'-A相'])?$devsnumber[$group['line_id'].'-'.$group['group_id'].'-A相']:'无';
			$groups[$k]['dev_on_B']=isset($devsnumber[$group['line_id'].'-'.$group['group_id'].'-B相'])?$devsnumber[$group['line_id'].'-'.$group['group_id'].'-B相']:'无';
			$groups[$k]['dev_on_C']=isset($devsnumber[$group['line_id'].'-'.$group['group_id'].'-C相'])?$devsnumber[$group['line_id'].'-'.$group['group_id'].'-C相']:'无';
			$groups[$k]['dev_on_2A']=isset($devsnumber[$group['line_id2'].'-'.$group['group_id'].'-A相'])?$devsnumber[$group['line_id2'].'-'.$group['group_id'].'-A相']:'无';
			$groups[$k]['dev_on_2B']=isset($devsnumber[$group['line_id2'].'-'.$group['group_id'].'-B相'])?$devsnumber[$group['line_id2'].'-'.$group['group_id'].'-B相']:'无';
			$groups[$k]['dev_on_2C']=isset($devsnumber[$group['line_id2'].'-'.$group['group_id'].'-C相'])?$devsnumber[$group['line_id2'].'-'.$group['group_id'].'-C相']:'无';
			$groups[$k]['user_gname']=isset($usergroups[$group['user_gid']]['user_gname'])?$usergroups[$group['user_gid']]['user_gname']:'已删除';
		}
		return $groups;
	}

	/**
	 * add_group
	 * 添加新杆塔
	 * @param string $group_name 杆塔名
	 * @param string $group_loc 自定杆塔位置
	 * @param int $line_id 所在线路1id
     * @param int $line_id2 所在线路2id
	 * @param float $coor_long 经度
     * @param float $coor_lat 纬度
     * @param int $user_gid 管理小组id
	 * @return bool false-添加失败;true-添加成功
	 **/
	public function add_group($group_name,$group_loc,$line_id,$line_id2,$coor_long,$coor_lat,$user_gid){
		$err=null;
		$i=0;
		if(strlen($group_name)<1||strlen($group_loc)<1)
			$err[$i++]="杆塔名和杆塔地址不能位空";
		if($this->get_group_id($group_name, $group_loc))
			$err[$i++]="已有相同名字的杆塔名和杆塔地址";
		if($line_id==$line_id2&&$line_id)
			$err[$i++]="一个杆塔的两条线路不能相同";
		if($i==0){
			$colvs=array('group_name'=>"'$group_name'",'group_loc'=>"'$group_loc'",'line_id'=>$line_id,'line_id2'=>$line_id2,'coor_long'=>$coor_long,'coor_lat'=>$coor_lat,'user_gid'=>$user_gid);
			if(!$this->insert_table('groups',$colvs))
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

	/**
	 * add_group
	 * 编辑杆塔
	 * @param int $group_id 杆塔id
	 * @param string $group_name 杆塔名
	 * @param string $group_loc 自定杆塔位置
	 * @param int $line_id 所在线路1id
     * @param int $line_id2 所在线路2id
	 * @param float $coor_long 经度
     * @param float $coor_lat 纬度
     * @param int $user_gid 管理小组id
	 * @return bool false-添加失败;true-添加成功
	 **/
	public function update_group($group_id,$group_name,$group_loc,$line_id,$line_id2,$coor_long,$coor_lat,$user_gid){
		$err=null;
		$i=0;
		if(($o_gid=$this->get_group_id($group_name, $group_loc))&&$o_gid!=$group_id)
			$err[$i++]="已有相同名字的杆塔名和杆塔地址";
		if($line_id==$line_id2&&$line_id)
			$err[$i++]="一个杆塔的两条线路不能相同";
		if($i==0){
			$group=$this->get_group($group_id);
			$colvs=array('group_name'=>"'$group_name'",'group_loc'=>"'$group_loc'",'line_id'=>$line_id,'line_id2'=>$line_id2,'coor_long'=>$coor_long,'coor_lat'=>$coor_lat,'user_gid'=>$user_gid);
			$result=$this->get_result();
			if(!$this->update_table('groups',$colvs,array('group_id'=>$group_id)))
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
	/**
	 * delete_group
	 * 删除杆塔
	 * @param int $group_id 杆塔id
	 * @return bool false-删除失败;true-删除成功
	 **/
	public function delete_group($group_id){
		$err=null;
		$i=0;
		$group=$this->get_group($group_id);
		if(empty($group))
			$err[$i++]="杆塔不存在或已删除";
		else{
			if(!$this->delete_table('groups',array('group_id'=>$group_id)))
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

	/**
	 * get_lines_on_group
	 * 返回一个二维数组,存储一个杆塔上的线路信息数组
	 * @uses newdev-ajax.php
	 * @param int $group_id 杆塔id
	 * @return null/array null-无绑定此杆塔的线路,array-二维数组
	 **/	
	//返回某杆塔绑定的所有线路数组
	public function get_lines_on_group($group_id){
		$this->queries='SELECT '.$this->prefix.'groups.group_id,'.$this->prefix.'liness.line_id,'.$this->prefix.'liness.line_name FROM '.$this->prefix.'groups,'.$this->prefix.'liness WHERE '.$this->prefix.'groups.group_id=".$group_id." AND ('.$this->prefix.'groups.line_id='.$this->prefix.'liness.line_id OR '.$this->prefix.'groups.line_id2='.$this->prefix.'liness.line_id)';
		return $this->get_rows();
	}
	// public function add_group_vi_line_name($group_name,$group_loc,$line_name=null,$line_name2=null,$coor_long,$coor_lat){
		
	// 	$line_id=$this->get_line_id($line_name);
	// 	$line_id2=$this->get_line_id($line_name2);
	// 	$results=add_group($group_name,$group_loc,$line_id,$line_id2,$coor_long,$coor_lat);
	// 	return $results;
	// }

	/**
	 * get_all_groups_coor_index_line_id
	 * 返回一个三维数组,用来保存一条线路上的杆塔坐标信息,一级下标为线路id,耳机下标为杆塔id
	 * @uses mapsinfo-ajax.php
	 * @param void
	 * @return null/array null-无绑定杆塔的线路,array-三维数组
	 **/
	public function get_all_groups_coor_index_line_id(){
		$this->queries='SELECT '.$this->prefix.'groups.group_id,'.$this->prefix.'groups.coor_long,'.$this->prefix.'groups.coor_lat,'.$this->prefix.'liness.line_id FROM '.$this->prefix.'groups,'.$this->prefix.'liness WHERE '.$this->prefix.'groups.line_id='.$this->prefix.'liness.line_id OR '.$this->prefix.'groups.line_id2='.$this->prefix.'liness.line_id ORDER BY '.$this->prefix.'groups.coor_long,'.$this->prefix.'groups.coor_lat';
		$rows=$this->get_table('groups');
		$rerows=null;
		if($rows)
			foreach ($rows as $row) {
				$rerows[$row['line_id']][$row['group_id']]['lng']=$row['coor_long'];
				$rerows[$row['line_id']][$row['group_id']]['lat']=$row['coor_lat'];
			}
		return $rerows;			
	}
	/**
	 * is_group_on_line
	 * 某杆塔上是否绑定了某条线路
	 * @param int $group_id 杆塔id
	 * @param int $line_id 线路id
	 * @return bool true-指定线路上存在此杆塔,false-不存在
	 **/
	public function is_group_on_line($group_id,$line_id){
		$this->queries='SELECT group_id FROM '.$this->prefix.'groups WHERE group_id='.$group_id.' AND (line_id='.$line_id.' OR line_id2='.$line_id.')';
		//$this->queries="SELECT * FROM groups";
		$result=$this->get_result();
		if($this->get_table('groups',null,'group_id',array('group_id'=>$group_id,'AND (','line_id'=>$line_id,'OR','line_id2'=>$line_id,')')))
			return true;
		return false;
	}


	/*******************************************************
	 *line
	 ******************************************************/

	/**
	 * get_line_id
	 * 获去线路id
	 * @param string $line_name 线路名
	 * @return int 0-无此线路名,其他-线路id
	 **/
	public function get_line_id($line_name){
		$lines=$this->get_table('liness',null,'*',array('line_name'=>"'$line_name'"));
		if($lines&&count($lines)>0)
			return $lines[0]['line_id'];
		return 0;
	}
	/**
	 * get_line
	 * 获取线路信息
	 * @param int $line_id 线路id
	 * @return null/array null-无此线路,array-一维数组
	 **/
	public function get_line($line_id){
		$rows=$this->get_table('liness',null,'*',array('line_id'=>$line_id));
		if(!$rows||count($rows)<1)
			return null;
		return $rows[0];
	}

	/**
	 * get_all_lines
	 * 获取所有线路信息列表
	 * @param void
	 * @return null/array null-无线路信息,array-二维数组,线路信息列表
	 **/
	public function get_all_lines(){
		return $this->get_table('liness','line_id','*',array(),'line_name',true);
	}

	/**
	 * add_line
	 * 添加线路
	 * @param string $line_name 线路名
	 * @return bool true-添加成功;flase-添加失败
	 **/
	public function add_line($line_name){
		$err=null;
		$i=0;
		if($line_name==""||!$line_name)
			$err[$i++]="线路名不能为空";
		if($this->get_line_id($line_name)>0)
			$err[$i++]="添加失败，已有相同名字的线路";
		if($i==0){
			if(!$this->insert_table('liness',array('line_name'=>"'$line_name'")))
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

	/**
	 * update_line
	 * 编辑线路
	 * @param int $line_id 线路id
	 * @param string $line_name 线路名
	 * @return bool true-编辑成功;flase-编辑失败
	 **/
	public function update_line($line_id,$line_new_name){
		$err=null;
		$i=0;
		if(!isset($line_new_name)||strlen($line_new_name)<1)
			$err[$i++]="线路名不能为空";
		if(($t=$this->get_line_id($line_new_name))>0&&$t!=$line_id)
			$err[$i++]="修改线路名失败，已有相同名字的线路";
		if($i==0){
			$line=$this->get_line($line_id);
			if(!$this->update_table('liness',array('line_name'=>"'$line_new_name'"),array('line_id'=>$line_id)))
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
	
	/**
	 * delete_line
	 * 删除线路
	 * @param int $line_id 线路id
	 * @return bool true-删除成功;flase-删除失败
	 **/
	public function delete_line($line_id){
		$err=null;
		$i=0;
		if(!$this->has_line($line_id))
			$err[$i++]="线路不存在或已删除";
		else{
			$line=$this->get_line($line_id);
			if(!$this->delete_table('liness',array('line_id'=>$line_id)))
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
	/**
	 * has_line
	 * 是否存在某条线路
	 * @param int $line_id 线路id
	 * @return bool true-存在;flase-不存在
	 **/
	protected function has_line($line_id){
		$line=$this->get_line($line_id);
		return(empty($line))?false:true;
	}

	/*******************************************
	 *USERGROUPS
	 *******************************************/
	/**
	 * get_usergroup
	 * 获取一个用户小组信息
	 * @param int $user_gid 小组id
	 * @return null/array null-无此小组;array-一维数组
	 **/
	public function get_usergroup($user_gid){
		$usergroups=$this->get_table('usergroups',null,'*',array('user_gid'=>$user_gid));
		if($usergroups&&count($usergroups)>0)
			return $usergroups[0];
		return null;
	}
	/**
	 * get_all_usergroups
	 * 获取所有小组信息列表
	 * @param void
	 * @return null/array null-无小组信息,array-二维数组
	 **/
	public function get_all_usergroups(){
		return $this->get_table('usergroups',null,'*',array(),array('user_gname'),true);
	}
	/**
	 * get_user_gid
	 * 获取小组id
	 * @param string $user_gname 小组名
	 * @return int 0-无此小组,其他-小组id
	 **/
	public function get_user_gid($user_gname){
		$rows=$this->get_table('usergroups',null,'*',array('user_gname'=>"'$user_gname'"));
		if(!$rows||sizeof($rows)<1)
			return 0;
		return $rows[0]['user_gid'];
	}
	/**
	 * add_usergroup
	 * 添加用户小组
	 * @param string $user_gname 小组名
	 * @return bool true-添加成功;false-添加失败
	 **/
	public function add_usergroup($user_gname){
		$err=null;
		$i=0;
		if ($this->get_user_gid($user_gname))
			$err[$i++]=("用户小组名已存在");
		if($i==0){
			if(!$this->insert_table('usergroups',array('user_gname'=>"'$user_gname'")))
				$err[$i++]=("添加用户小组失败，请联系管理员");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了用户组".$user_gname);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;	
	}
	/**
	 * update_usergroup
	 * 编辑用户小组
	 * @param int $user_gid 小组id
	 * @param string $user_gname 小组名
	 * @return bool true-编辑成功;false-编辑失败
	 **/
	public function update_usergroup($user_gid,$user_gname){
		$err=null;
		$i=0;
		if(empty($user_gname))
			$err[$i++]='用户小组名不能为空';
		if($this->get_user_gid($user_gname)>0)
			$err[$i++]='修改小组名失败，已有相同名字的小组';
		if($i==0){
			if(!$this->update_table('usergroups',array('user_gname'=>"'$user_gname'"),array('user_gid'=>$user_gid)))
				$err[$i++]=("修改小组失败");
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name'].'修改了小组'.$user_gname);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	/**
	 * delete_usergroup
	 * 删除用户小组
	 * @param int $user_gid 小组id
	 * @return bool true-删除成功;false-删除失败
	 **/
	public function delete_usergroup($user_gid){
		$err=null;
		$i=0;
		if($this->get_usergroup($user_gid)==null)
			$err[$i++]="用户小组不存在或已删除";
		if($i==0){
			$usergroup=$this->get_usergroup($user_gid);
			if(!$this->delete_table('usergroups',array('user_gid'=>$user_gid)))
				$err[$i++]="删除用户小组失败，请联系管理员";
			else{
				global $__USER;
				$this->update_table('users',array('user_gid'=>'0'),array('user_gid'=>$user_gid));
				$this->update_table('groups',array('user_gid'=>'0'),array('user_gid'=>$user_gid));
				$this->add_dolog($__USER['user_name']."删除了用户小组".$usergroup['user_gname']);
				return true;
			}
		}		
		$this->set_errors($i,$err);
		return false;
	}

	/*******************************************
	 *USERS
	 *参数 int $user_role：1-系统管理员，2-超级管理员，3-设备管理员，5-普通用户
	 *参数 int $is_send：0-不转发，1-转发
	 ******************************************/

	/**
	 * get_user_id
	 * 伪重载函数,获取用户id
	 *		重载1:get_user_id($user_name)
	 *		重载2:get_user_id($user_name,$passwd)
	 * @param string/string_string 用户名/用户名_密码
	 * @return int -1-参数错误;0-无此用户;其他-用户id
	 **/
	public function get_user_id(){
		$argsnum=func_num_args();
		$args=func_get_args();
		if($argsnum<1||$argsnum>2)
			return -1;
		if($argsnum==1)
			$users=$this->get_table('users',null,'user_id',array('user_name'=>"'$args[0]'"));
		else{
			$passwdd=md5($args[1]);
			$users=$this->get_table('users',null,'user_id',array('user_name'=>"'$args[0]'",'AND','passwd'=>"'$passwdd'"));
		}
		
		if($users&&count($users)>0)
			return $users[0]['user_id'];
		return 0;
	}

	/**
	 * get_user
	 * 获取用户
	 *@param int $user_id 用户ID
	 *@return null/array null-无此用户;array-用户信息一维数组
	 **/	
	public function get_user($user_id){
		$users=$this->get_table('users',null,'*',array('user_id'=>$user_id));
	
		if($users&&count($users)>0)
			return $users[0];
		return null;

	}

	/**
	 * has_user
	 * 是否存在某用户
	 *@param int $user_id 用户ID
	 *@return bool true-存在;false-不存在
	 **/
	protected function has_user($user_id){
		if($this->get_user($user_id)==null)
			return false;
		return true;
	} 

	/**
	 * get_all_users
	 * 获取用户列表
	 * @param void
	 * @return null/array null-无用户;array-用户信息列表的二维数组
	 **/
	public function get_all_users(){
		$rows=$this->get_table('users','user_id','*','user_role<>1',array('user_gid','user_role'),true);
		if(!$rows||count($rows)<1)
			return null;
		$usergroups=$this->get_table('usergroups','user_gid','*');
		$usergroups[0]['user_gname']='默认';
		foreach ($rows as $key => $row) {
			$rows[$key]['user_gname']=$usergroups[$row['user_gid']]['user_gname'];
		}
		return $rows;
	}

	/**
	 * add_user
	 * 添加新用户
	 * @param string $user_name 用户名
	 * @param string $passwd 未加密密码
	 * @param int $user_role 身份类型
	 * @param int $user_gid 用户小组id
	 * @param string $user_phone 手机
	 * @param string $user_email 邮箱
	 * @param int $is_send 是否转发短信
	 * @return bool true-添加成功;false-添加失败
	 **/
	public function add_user($user_name,$passwd,$user_role=5,$user_gid=0,$user_phone=0,$user_email=null,$is_send=0){
		$err=null;
		$i=0;
		$passwdd=md5($passwd);
		if ($this->get_user_id($user_name)>0)
			$err[$i++]=("用户名已存在");
		if($i==0){
			$colvs=array('user_name'=>"'$user_name'",'passwd'=>"'$passwdd'",'user_role'=>$user_role,'user_gid'=>$user_gid,'user_phone'=>"'$user_phone'",'user_email'=>"'$user_email'",'is_send'=>$is_send,'current_login_time'=>'NOW()','register_time'=>'NOW()');
			if(!$this->insert_table('users',$colvs))
				$err[$i++]='添加用户失败，请联系管理员';
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name']."添加了用户".$user_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;	
	}	
	/**
	 * update_user
	 * 编辑用户
	 * @param int $user_id 用户ID
	 * @param string $user_name 用户名
	 * @param string $passwd 未加密密码
	 * @param int $user_role 身份类型
	 * @param int $user_gid 用户小组id
	 * @param string $user_phone 手机
	 * @param string $user_email 邮箱
	 * @param int $is_send 是否转发短信
	 * @return bool true-编辑成功;false-编辑失败
	 **/
	public function update_user($user_id,$user_name,$passwd,$user_role,$user_gid,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		$passwdd=md5($passwd);
		if (($o_user_id=$this->get_user_id($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($i==0){
			$colvs=array('user_name'=>"'$user_name'",'passwd'=>"'$passwdd'",'user_role'=>$user_role,'user_gid'=>$user_gid,'user_phone'=>"'$user_phone'",'user_email'=>"'$user_email'",'is_send'=>$is_send);
			if(!$this->update_table('users',$colvs,array('user_id'=>$user_id)))
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

	/**
	 * update_user_nopwd
	 * 编辑用户但不修改密码
	 * @param int $user_id 用户ID
	 * @param string $user_name 用户名
	 * @param int $user_role 身份类型
	 * @param int $user_gid 用户小组id
	 * @param string $user_phone 手机
	 * @param string $user_email 邮箱
	 * @param int $is_send 是否转发短信
	 * @return bool true-编辑成功;false-编辑失败
	 **/
	public function update_user_nopwd($user_id,$user_name,$user_role,$user_gid,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		if (($o_user_id=$this->get_user_id($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($i==0){
			$colvs=array('user_name'=>"'$user_name'",'user_role'=>$user_role,'user_gid'=>$user_gid,'user_phone'=>"'$user_phone'",'user_email'=>"'$user_email'",'is_send'=>$is_send);
			if(!$this->update_table('users',$colvs,array('user_id'=>$user_id)))
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

	/**
	 * update_userself
	 * 用户修改资料
	 * @param int $user_id 用户ID
	 * @param string $old_name 旧用户名
	 * @param string $user_name 新用户名
	 * @param string $oldpasswd 旧未加密密码
	 * @param string $passwd 新未加密密码
	 * @param string $user_phone 手机
	 * @param string $user_email 邮箱
	 * @param int $is_send 是否转发短信
	 * @return bool true-编辑成功;false-编辑失败
	 **/
	public function update_userself($user_id,$old_name,$user_name,$oldpasswd,$passwd,$user_phone,$user_email,$is_send){
		$err=null;
		$i=0;
		if($passwd==""||$passwd==null)
			$passwd=$oldpasswd;
		$passwdd=md5($passwd);
		if (($o_user_id=$this->get_user_id($user_name))&&$o_user_id!=$user_id)
			$err[$i++]=("用户名已存在");
		if($this->get_user_id($old_name,$oldpasswd)<1)
			$err[$i++]=("旧密码输入错误");
		if($i==0){
			$colvs=array('user_name'=>"'$user_name'",'passwd'=>"'$passwdd'",'user_phone'=>"'$user_phone'",'user_email'=>"'$user_email'",'is_send'=>$is_send);
			if(!$this->update_table('users',$colvs,array('user_id'=>$user_id)))
				$err[$i++]="修改用户失败，请联系管理员";
			else
				return true;
		}
		$this->set_errors($i,$err);
		return false;	
	}

	/**
	 * update_user_last_login_time
	 * 更新用户最后登陆时间
	 * @param int $user_id 用户ID
	 * @return bool ture-更新成功;false-更新失败
	 **/
	public function update_user_last_login_time($user_id){
		return $this->update_table('users',array('last_login_time'=>'current_login_time','current_login_time'=>'NOW() '),array('user_id'=>$user_id));
	}

	/**
	 * delete_user
	 * 删除用户
	 * @param int $user_id 用户ID
	 * @return bool ture-删除成功;false-删除失败
	 **/
	public function delete_user($user_id){
		$err=null;
		$i=0;
		if(!$this->has_user($user_id))
			$err[$i++]="用户不存在或已删除";
		if($user_id==1)
			$err[$i++]="权限不足，此用户无法删除";
		if($i==0){
			$user=$this->get_user($user_id);
			if(!$this->delete_table('users',array('user_id'=>$user_id)))
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

	/******************
	*ALARMS
	******************/

	/**
	 * get_alarms
	 * 获取报警信息列表
	 * @param void
	 * @return null/array null-无报警信息;array-二维数组
	 **/
	public function get_alarms(){
		$rows=$this->get_table('alarms',null,'*',array(),array('action_time DESC'),true);
		$devs=$this->get_table('devs','dev_id','*');
		$groups=$this->get_table('groups','group_id','*');
		$lines=$this->get_table('liness','line_id','*');
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groups[$devs[$row['dev_id']]['group_id']]))?($groups[$devs[$row['dev_id']]['group_id']]['group_loc'].'-'.$groups[$devs[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($lines[$devs[$row['dev_id']]['line_id']]))?$lines[$devs[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}

	/**
	 * get_last_alarms
	 * 获取最近报警信息列表,默认最近20条
	 * @param void
	 * @return null/array null-无报警信息;array-二维数组
	 **/
	public function get_last_alarms(){
		$this->queries='SELECT * FROM '.$this->prefix.'alarms ORDER BY action_time DESC LIMIT 0,20';
		$rows=$this->get_rows();
		$devs=$this->get_table('devs','dev_id','*');
		$groups=$this->get_table('groups','group_id','*');
		$lines=$this->get_table('liness','line_id','*');
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groups[$devs[$row['dev_id']]['group_id']]))?($groups[$devs[$row['dev_id']]['group_id']]['group_loc'].'-'.$groups[$devs[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($lines[$devs[$row['dev_id']]['line_id']]))?$lines[$devs[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}

	/**
	 * get_last_alarm_id
	 * 获取上一条报警信息id
	 * @param void
	 * @return int 0-获取失败;其他-报警id
	 **/
	public function get_last_alarm_id(){
		$rows=$this->get_table('alarms',null,'max(id)');
		if($rows&&count($rows)>0)
			return $rows[0]['max(id)'];
		return 0;
	}
	/**
	 * get_alarm
	 * 获取上一条报警信息id
	 * @param void
	 * @return int 0-获取失败;其他-报警id
	 **/
	public function get_alarm($id){
		$rows=$this->get_table('alarms',null,'*',array('id'=>$id));
		if($rows&&count($rows)>0)
			return $rows[0];
		return null;
	}

	/**
	 * get_dev_alarms
	 * 获取设备在某时间段内的报警信息列表
	 * @uses alarms
	 * @param int $dev_id 设备id
	 * @param string $date_from 起始日期
	 * @param string $date_to 结束日期
	 * @return null/array null-无报警信息;array-二维数组
	 **/
	public function get_dev_alarms($dev_id,$date_from,$date_to){

		$rows=$this->get_table('alarms',null,'*',"dev_id=$dev_id AND action_time BETWEEN '$date_from' AND '$date_to'",array('action_time'));

		if($rows&&count($rows)>0)
			return $rows;
		return null;
	}

	/**
	 * get_histories
	 * 获取历史信息(例行上传)
	 * @param void
	 * @return null/array null-无历史信息;array-二维数组
	 **/
	public function get_histories(){
		$rows=$this->get_table('histories',null,'*',array(),array('action_time DESC'),true);
		$devs=$this->get_table('devs','dev_id','*');
		$groups=$this->get_table('groups','group_id','*');
		$lines=$this->get_table('liness','line_id','*');
		if($rows){
			foreach ($rows as $key=>$row) {
				$rows[$key]['dev_number']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_number']:'设备已删除';
				$rows[$key]['dev_phase']=(isset($devs[$row['dev_id']]))?$devs[$row['dev_id']]['dev_phase']:'设备已删除';
				$rows[$key]['group_loc_name']=(isset($groups[$devs[$row['dev_id']]['group_id']]))?($groups[$devs[$row['dev_id']]['group_id']]['group_loc'].'-'.$groups[$devs[$row['dev_id']]['group_id']]['group_name']):'杆塔已删除';
				
				$rows[$key]['line_name']=(isset($lines[$devs[$row['dev_id']]['line_id']]))?$lines[$devs[$row['dev_id']]['line_id']]['line_name']:'线路已删除';
			}
		}
		return $rows;
	}

	/*************************************************
	 * MULTISITES
	 *************************************************/
	/**
	 * get_site_id
	 * 获取站点id
	 * @param string $site_name
	 * @return int 0-无此站点;其他-站点id
	 **/
	public function get_site_id($site_name){
		$sites=$this->get_table('multisites',null,'site_id',array('site_name'=>$site_name));
		if($sites&&count($sites)>0)
			return $sites[0]['site_id'];
		return 0;
	}

	/**
	 * get_site
	 * 获取站点信息
	 * @param int $site_id 站点id
	 * @return null/array null-无此站点信息;array-一维数组
	 **/
	public function get_site($site_id){
		$sites=$this->get_table('multisites',null,'*',array('site_id'=>$site_id));
		if($sites&&count($sites)>0)
			return $sites[0];
		return null;
	}

	/**
	 * get_all_sites
	 * 获取站点列表信息
	 * @param void
	 * @return null/array null-无站点;array-二维数组
	 **/
	public function get_all_sites(){
		$sites=$this->get_table('multisites',null,'*',array(),array('site_name'),true);
		if($sites&&count($sites)>0)
			return $sites;
		return null;
	}
	/**
	 * create_site_table
	 * 创建新站点表
	 * @param string 站点名
	 * @return bool true-新建成功;false-新建失败
	 **/
	private function create_site_table($site_name){
		$t=0;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_alarms` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`action_num` tinyint(3) unsigned NOT NULL,`i_num` smallint(5) unsigned NOT NULL,`tem` tinyint(4) NOT NULL,`hum` tinyint(4) NOT NULL,`dev_id` int(10) unsigned NOT NULL,`is_read` tinyint(1) NOT NULL DEFAULT \'0\',PRIMARY KEY (`id`),KEY `action_time` (`action_time`),KEY `FK_DEV_ID` (`dev_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_devs` (`dev_id` int(10) unsigned NOT NULL AUTO_INCREMENT,`dev_number` char(11) NOT NULL,`dev_name` char(30) DEFAULT \'在线监测仪\',`dev_phase` char(4) NOT NULL,`group_id` int(10) unsigned NOT NULL,`line_id` int(10) unsigned NOT NULL,PRIMARY KEY (`dev_id`),UNIQUE KEY `dev_number` (`dev_number`),KEY `dev_id` (`dev_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_dologs` (`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,`do_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`do_msg` varchar(50) NOT NULL,`do_ip` char(40) DEFAULT NULL,PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_groups` (`group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,`group_name` char(30) NOT NULL,`group_loc` char(30) NOT NULL,`line_id` int(10) unsigned DEFAULT \'0\',`line_id2` int(10) unsigned DEFAULT \'0\',`coor_long` double NOT NULL,`coor_lat` double NOT NULL,`user_gid` int(11) NOT NULL DEFAULT \'0\',PRIMARY KEY (`group_id`),KEY `S_GROUP_NAME` (`group_name`)) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_histories` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`action_num` tinyint(3) unsigned NOT NULL,`i_num` smallint(5) unsigned NOT NULL,`tem` tinyint(4) NOT NULL,`hum` tinyint(4) NOT NULL,`dev_id` int(10) unsigned NOT NULL,`is_read` tinyint(1) NOT NULL DEFAULT \'0\',PRIMARY KEY (`id`),KEY `action_time` (`action_time`),KEY `FK_DEV_ID` (`dev_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_liness` (`line_id` int(10) unsigned NOT NULL AUTO_INCREMENT,`line_name` varchar(50) NOT NULL,`add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`line_id`),UNIQUE KEY `line_name` (`line_name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_usergroups` (`user_gid` int(11) NOT NULL AUTO_INCREMENT,`user_gname` char(24) NOT NULL,PRIMARY KEY (`user_gid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		if(!$this->get_result())
			$t++;
		$this->queries='CREATE TABLE IF NOT EXISTS `'.$site_name.'_users` (`user_id` int(11) NOT NULL AUTO_INCREMENT,`user_name` char(18) NOT NULL,`passwd` char(32) NOT NULL,`user_role` tinyint(3) unsigned NOT NULL DEFAULT \'5\',`current_login_time` timestamp NULL DEFAULT NULL,`last_login_time` timestamp NULL DEFAULT NULL,`register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`user_gid` int(11) NOT NULL DEFAULT \'0\',`user_phone` char(15) DEFAULT \'0\',`user_email` varchar(32) DEFAULT NULL,`is_send` tinyint(4) DEFAULT \'0\',PRIMARY KEY (`user_id`),UNIQUE KEY `user_name` (`user_name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC';
		if(!$this->get_result())
			$t++;
		$passwdd=md5('111111');
		$this->queries='INSERT INTO '.$site_name.'_users(user_name,passwd,user_role,user_gid,user_phone,user_email,is_send,current_login_time,register_time) VALUES(\'admin\',\''.$passwdd.'\',1,0,0,\'\',0,NOW(),NOW())';
		$this->get_result();
		return ($t==0)?true:false;

	}
	/**
	 * delete_site_table
	 * 删除站点表
	 * @param string 站点名
	 * @return bool true-删除成功;false-删除失败
	 **/
	private function delete_site_table($site_name){
		$t=0;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_alarms';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_devs';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_dologs';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_groups';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_histories';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_liness';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_usergroups';
		if(!$this->get_result())
			$t++;
		$this->queries='DROP TABLE IF EXISTS '.$site_name.'_users';
		if(!$this->get_result())
			$t++;
		return ($t==0)?true:false;
	}
	/**
	 * add_site
	 * 添加站点
	 * @param string $site_name 站点名
	 * @param string $site_remark 站点id
	 * @param string $dbhost 数据库主机
	 * @param string $dbname 数据表名
	 * @param string $dbuser 数据库用户名
	 * @param string $dbpasswd 数据库密码
	 * @param int $is_use_default 是否使用默认数据库
	 * @return bool true-添加成功;false-添加失败
	 **/
	public function add_site($site_name,$site_remark,$dbhost,$dbname,$dbuser,$dbpasswd,$is_use_default){
		$err=null;
		$i=0;
		if($is_use_default==1){
			$dbhost=DB_HOST;
			$dbname=DB_NAME;
			$dbuser=DB_USER;
			$dbpasswd=DB_PASSWORD;
		}

		if(!preg_match("/^[a-z\s]+$/",$site_name))
			$err[$i++]='站点名格式有误,只能输入小写英文字母';
		elseif(!set_site_conf_file($site_name,$dbhost,$dbname,$dbuser,$dbpasswd))
			$err[$i++]='建立配置文件失败,请确认conf文件夹可写';
		elseif(!$this->create_site_table($site_name)){
			$err[$i++]='建立数据库失败,请确认数据库可以访问';
			delete_site_conf_file($site_name);
		}
		if($i==0){
			if(!$this->insert_table('multisites',array('site_name'=>"'$site_name'",'site_remark'=>"'$site_remark'"))){
				$err[$i++]='新建站点失败,请稍后再试';
				delete_site_conf_file($site_name);
				$this->delete_site_table($site_name);
			}
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name'].'添加了站点'.$site_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	/**
	 * update_site
	 * 编辑站点
	 * @param int $site_id 站点id
	 * @param string $site_name 站点名
	 * @param string $site_remark 站点id
	 * @param string $dbhost 数据库主机
	 * @param string $dbname 数据表名
	 * @param string $dbuser 数据库用户名
	 * @param string $dbpasswd 数据库密码
	 * @param int $is_use_default 是否使用默认数据库
	 * @return bool true-编辑成功;false-编辑失败
	 **/
	public function update_site($site_id,$site_name,$site_remark,$dbhost,$dbname,$dbuser,$dbpasswd,$is_use_default){
		$err=null;
		$i=0;
		if($is_use_default==1){
			$dbhost=DB_HOST;
			$dbname=DB_NAME;
			$dbuser=DB_USER;
			$dbpasswd=DB_PASSWORD;
		}
		if(!preg_match("/^[a-z\s]+$/",$site_name))
			$err[$i++]='站点名格式有误,只能输入小写英文字母';
		elseif(!set_site_conf_file($site_name,$dbhost,$dbname,$dbuser,$dbpasswd))
			$err[$i++]='建立配置文件失败,请确认conf文件夹可写';
		if($i==0){

			if(!$this->update_table('multisites',array('site_name'=>"'$site_name'",'site_remark'=>"'$site_remark'"),array('site_id'=>$site_id)))
				$err[$i++]='更新站点失败,请稍后再试';
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name'].'修改了站点'.$site_name);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	/**
	 * delete_site
	 * 删除站点
	 * @param int $site_id 站点id
	 * @return bool true-删除成功;false-删除失败
	 **/
	public function delete_site($site_id){
		$err=null;
		$i=0;
		$site=$this->get_site($site_id);
		if(empty($site))
			$err[$i++]='站点不存在或已删除';
		elseif(!delete_site_conf_file($site['site_name']))
			$err[$i++]='删除配置文件失败,请检查conf文件夹全写是否可写';
		elseif(!$this->delete_site_table($site['site_name']))
			$err[$i++]='删除数据表失败,请确认数据库可以访问,或表';
		if($i==0){
			if(!$this->delete_table('multisites',array('site_id'=>$site_id)))
				$err[$i++]='删除失败,请稍后再试';
			else{
				global $__USER;
				$this->add_dolog($__USER['user_name'].'删除了站点'.$site['site_name']);
				return true;
			}
		}
		$this->set_errors($i,$err);
		return false;
	}
	/**
	 * output_devs_to_excel
	 * 导出所有设备信息到excel表
	 * @param void
	 * @return null/array null-无设备信息;array-二维数组
	 **/
	public function output_devs_to_excel(){	
		$rows=$this->get_table('devs',null,'*',array(),array('line_id','group_id','dev_phase'));
		if(!$rows||count($rows)<0)
			return null;
		$groups=$this->get_table('groups','group_id','*');
		$lines=$this->get_table('liness','line_id','*');
		foreach ($rows as $key => $row) {
			$rows[$key]['group_name']=(isset($groups[$row['group_id']]['group_name']))?$groups[$row['group_id']]['group_name']:'无';
			$rows[$key]['group_loc']=(isset($groups[$row['group_id']]['group_loc']))?$groups[$row['group_id']]['group_loc']:'无';
			$rows[$key]['coor_long']=(isset($groups[$row['group_id']]['coor_long']))?$groups[$row['group_id']]['coor_long']:0.0;
			$rows[$key]['coor_lat']=(isset($groups[$row['group_id']]['coor_lat']))?$groups[$row['group_id']]['coor_lat']:0.0;
			$rows[$key]['line_name']=(isset($lines[$row['line_id']]['line_name']))?$lines[$row['line_id']]['line_name']:'无';
		}
		return $rows;
	}

	/**
	 * input_devs_from_excel
	 * 导入设备,保守导入
	 * @param array 设备信息的二维数组
	 * @return array 错误信息数组
	 **/
	public function input_devs_from_excel($devs){
		$i=0;
		$err=null;
		foreach ($devs as $dev) {
			$dev_id=$this->get_dev_id($dev['dev_number']);
			if($dev_id>0){
				$err[$i++]="设备[".$dev['dev_number']."]已存在";
				continue;
			}
			$line_id=($dev['line_name']=='无')?0:$this->get_line_id($dev['line_name']);
			if($line_id==0&&$dev['line_name']!='无'){
				$this->add_line($dev['line_name']);
				$line_id=$this->get_line_id($dev['line_name']);
				$err[$i++]="新插入了线路[".$dev['line_name']."]";
			}
			$group_id=($dev['group_name']=='无')?0:$this->get_group_id($dev['group_name'],$dev['group_loc']);
			echo $line_id;
			if($group_id==0&&$dev['group_name']!='无'){
				$this->add_group($dev['group_name'],$dev['group_loc'],$line_id,0,$dev['coor_long'],$dev['coor_lat'],0);
				$group_id=$this->get_group_id($dev['group_name'],$dev['group_loc']);
				$err[$i++]='新插入了杆塔['.$dev['group_loc'].'-'.$dev['group_name'].']';
			}
			elseif(!$this->is_group_on_line($group_id,$line_id)){
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
}
?>