<?php
/** WordPress数据库的名称 */
define('DB_NAME', 'hfkeding_gis');
/** MySQL数据库用户名 */
define('DB_USER', 'hfkeding');
/** MySQL数据库密码 */
define('DB_PASSWORD', 'hfkeding1994');
/** MySQL主机 */
define('DB_HOST', 'localhost');
/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8');
/** 数据库整理类型。如不确定请勿更改 */
//define('DB_COLLATE', '');
define('H_DEBUG', 'false');
/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__));
?>