<?php
/** WordPress数据库的名称 */
/** WordPress数据库的名称 */
define('DB_NAME', 'gis-database');
/** MySQL数据库用户名 */
define('DB_USER', 'root');
/** MySQL数据库密码 */
define('DB_PASSWORD', '');
/** MySQL主机 */
define('DB_HOST', 'localhost');
/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8');
/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE','');
/** 数据库前缀 **/
define('DB_PREFIX', 'h_');
/*分页每页所显示结果数*/
define('PGN_SIZE','50');
/** 域名地址 */
define('SITE_URL', 'http://localhost');

define('H_DEBUG', 'false');
/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__));
?>