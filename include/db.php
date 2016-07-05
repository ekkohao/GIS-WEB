<?php
//���ݿ�ӿڷ�װ
class db{
	//�����ѯ������ѯ�Ƿ�ɹ��Ĳ���ֵ
	protected $result;
	//�����ѯ������е��У��ֶΣ�����
	protected $col_info;
	//����ִ�еĲ�ѯ���
	var $queries;
	//�������ݿ���������
	protected $reconnect_retries = 5;
	//���ݿ�ǰ׺
	var $prefix = '';
	//���ݿ����Ӿ��
	protected $dbh;
	//�Ƿ����ݿ�������
	protected $has_connected=false;
	protected $ready=false;
	private $is_use_mysqli=false;
	//���캯��
	public function __construct() {
		register_shutdown_function( array( $this, '__destruct' ) );
		//������mysqliģ��������
		if ( function_exists( 'mysqli_connect' ) ) 
			$this->is_use_mysqli = true;
		// wp-config.php creation will manually connect when ready.
		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return;
		}
		$this->db_connect();
	}
	//��������
	public function __destruct() {
		return true;
	}
	/** ��װget **/
	public function __get( $name ) {
		if ( 'col_info' === $name )
			$this->load_col_info();
		return $this->$name;
	}
	/** ��װset **/
	public function __set( $name, $value ) {
		$this->$name = $value;
	}
	/** ��װisset **/
	public function __isset( $name ) {
		return isset( $this->$name );
	}
	/** ��װunset **/
	public function __unset( $name ) {
		unset( $this->$name );
	}
	//�������ݿ�
	public function db_connect() {
		/*
		 * Deprecated in 3.9+ when using MySQLi. No equivalent
		 * $new_link parameter exists for mysqli_* functions.
		 */
		$new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;
		if ( $this->is_use_mysqli ) {
			$this->dbh = mysqli_init();
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
				mysqli_real_connect( $this->dbh, $host, DB_USER, DB_PASSWORD, null, $port, $socket, $client_flags );
			} else {
				@mysqli_real_connect( $this->dbh, $host, DB_USER, DB_PASSWORD, null, $port, $socket, $client_flags );
			}
			if ( $this->dbh->connect_errno ) {
				$this->dbh = null;
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
				if ( $attempt_fallback ) {
					$this->is_use_mysqli = false;
					$this->db_connect();
				}
			}
		} else {
			if ( H_DEBUG ) {
				$this->dbh = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD, $new_link, $client_flags );
			} else {
				$this->dbh = @mysql_connect( DB_HOST, DB_USER, DB_PASSWORD, $new_link, $client_flags );
			}
		}
		if ( ! $this->dbh  ) {
			//δ���
			die("���ݿ�����ʧ��");
			return false;
		} else {
			$this->has_connected = true;
			$this->set_charset( $this->dbh );
			//$this->set_sql_mode();
			$this->ready = true;
			$this->select( DB_NAME, $this->dbh );
			return true;
		}
		return false;
	}
	//�������ݿ����
	public function set_charset( $dbh, $charset = null) {
		if ( ! isset( $charset ) )
			$charset = DB_CHARSET;
		if ( $this->is_use_mysqli&&function_exists( 'mysqli_set_charset' )) {
			mysqli_set_charset( $dbh, $charset );		
		} elseif ( !$this->is_use_mysqli&&function_exists( 'mysql_set_charset' )) {
			mysql_set_charset( $charset, $dbh );	
		}
	}
	/** ѡ�����ݿ� **/
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
			//δ���
			die("���ݿ�ѡ��ʧ��");
			return;
		}
	}
	/**
	 *��ȡ��ǰ��ѯ������������У��ֶΣ��Ķ������飬
	 *ÿ������������ֶ���name������table���ֶ���󳤶�max_length......�ȵ�
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
	/** ���ؽ�����е�ĳ������У��ֶΣ���ĳ������ֵ�������У���$col_offset==-1ʱ����ĳ������ֵ����**/
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
}
?>