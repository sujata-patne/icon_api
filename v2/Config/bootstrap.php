<?php

putenv('TZ=Asia/Kolkata');

/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 * result: /var/www
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}

/**
 * The actual directory name for the "app".
 * result: directoryName
 */
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}

/**
 * Path to the lib's directory.
 */
if (!defined('LIB')) {
	define('LIB', dirname(__FILE__) . DS);
}

/**
 * Path to the application's directory.
 */
if (!defined('APP')) {
	define('APP', ROOT . DS . APP_DIR . DS);
}

/**
 * Document Root.
 */
if (!defined('DOC_ROOT')) {
	define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
}

/**
 * Path to the logs directory.
 */
if (!defined('LOGS')) {
	define('LOGS', APP . 'logs' . DS);
}

if (!defined('SITE_MODE')) {
	define('SITE_MODE', '1');			// 1: Test 2: Production
}

if (!defined('DEBUG')) {
	define('DEBUG', '1');			// 0: Off 1: On
}

if( DEBUG == 1 && function_exists('ini_set')) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	ini_set('error_reporting', 32767);
	ini_set("error_log", LOGS."php_error.log");
}else{
	ini_set('display_errors', 0);
}

if(SITE_MODE == 1 ){
	define('DBHOST', '192.168.1.160');
}else{
	define('DBHOST', '10.64.12.136');
}
// Icon DB Credentials
define("DBUSER_ICON", "icon_cms");
define("DBPASSWD_ICON", "iconcms@123");
define("ICONDB", "icon_cms1");
// Campaign DB Credentials
define("DBUSER_CAMPAIGN", "cmadmin");
define("DBPASSWD_CAMPAIGN", "cm@dm!n");
define("CAMPAIGNDB", "campaign_manager");

define("DBUSER", "misadmin");
define("DBPASSWD", "m!$@dm!n");
?>