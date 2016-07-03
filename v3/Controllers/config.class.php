<?php
namespace Store\Config;
use Store\Curl as Curl;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('error_reporting', 327678);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

ini_set("error_log", $_SERVER['DOCUMENT_ROOT']."logs/php_error.log");

putenv('TZ=Asia/Kolkata');
date_default_timezone_set('Asia/Calcutta');
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
 define('SITE_MODE', '1');   // 1: Test 2: Production
}

//define('SVCHOST',(SITE_MODE == 1)? 'http://192.168.1.156/':'http://10.64.11.170/'); //fuse api excluding billing
define('SVCHOST',(SITE_MODE == 1)? 'http://wakau.in/':'http://10.64.11.170/'); //fuse api excluding billing

define('BILLINGHOST',(SITE_MODE == 1)? 'http://192.168.1.156/':'http://10.64.12.132/'); // biling api

define('BILLING', 'http://192.168.1.156/billing/servicereq');
define('USERPROFILE', SVCHOST.'Service1.svc/GetUserProfileSettings');
define('USER_STATUS', SVCHOST.'Service2.svc/GetTelcoInfo');
define('S3STATUS', SVCHOST.'Service3.svc/substatus/');
define('AUTH_SERVICE', SVCHOST.'authService');
define('INSERT_WURFL_DATA', SVCHOST.'telcoService/AddUserAgentInfo'); //INTO DB
define('READ_WURFL_DATA', SVCHOST.'telcoService/GetUserAgentInfo'); //FROM DB
define('IMSI_CIR_DATA', SVCHOST.'telcoService/imsiCir');
define('ADD_BG_BANNER', SVCHOST.'campaign/addbgtidBanner/');

class Config {
	
 const WURFL_API_KEY = "267006:lZPyhxN4jVoXt3fQMGkDuWAc1U8bRE06";

 const STOREID = 'jet';
 const BGWAPPID = 2;
 const UID = 'jet';
 const STORENAME = 'DailyMagic';
 const SubscribeText = 'Thank you for subscribing to DailyMagic. Now, you can download & enjoy unlimited content of your choice.';
 const DEBUG = 1; 
 const CookieTag = 'D2C';
 const Paswd= 'jet@123';

 public $curlMethods;
 public $operatorData;
 public $allowedOperators;
 public $validOperators;

 function __construct (){
	 
  self::startSession();
  // self::showErrors();

  $this->validOperators = array('voda', 'idea', 'airtel', 'aircel');
  $this->allowedOperators = array('voda', 'idea');

  $this->operatorData = array(
   'voda' => array(
    'BillingServiceSub' => 'getvodabilling',
    'BillingServiceUnSub' => 'vodabilling',
    'Cmode' => 'WAP_D2C',
    'DefaultPP' => 'JET0003',
   ),
   'idea' => array(
    'BillingServiceSub' => 'getideabilling',
    'BillingServiceUnSub' => 'ideabilling',
    'Cmode' => 'WAP',
    'DefaultPP' => 'JET0003',

   )
  );
//DB configs : 
  $this->dbData = array(
     'siteuser' => array(
      'name' => 'site_user',
      'username' => 'iconadmin',
      'password' => 'icon@dm!n',
      'host' => '192.168.1.160',
	  //'name' => 'siteuser',
      //'username' => 'root',
      //'password' => '',
      //'host' => 'localhost',
     )
  );
  
 }
 
 // public static function showErrors(){
 //  if(!empty(self::DEBUG) && function_exists('ini_set')) {
 //   ini_set('display_errors', 1);
 //   ini_set('display_startup_errors', 1);
 //   ini_set('error_reporting', 1);
 //   ini_set("error_log", LOGS."php_error.log");
 //  }else{
 //   ini_set('display_errors', 0);
 //  }
 // }
 
 public static function startSession(){
  if (session_status() == PHP_SESSION_NONE) {
   // session_start();
  }else{
   if(session_id() == '') {
    session_start();
   }
  }
 }

 public function setCookie($cookieName, $cookieValue){
  setcookie($cookieName, $cookieValue, strtotime('today 23:59'), '/');
 }

 public function setPersistentCookie($cookieName, $cookieValue){
  setcookie($cookieName, $cookieValue, time() + (10 * 365 * 24 * 60 * 60), '/');
 }
 
 //Create the cloudfront signed URL
public function create_signed_url($asset_path, $private_key_filename, $key_pair_id, $expires){
	
  // Build the policy.
  $canned_policy = '{"Statement":[{"Resource":"' . $asset_path . '","Condition":{"DateLessThan":{"AWS:EpochTime":'. $expires . '}}}]}';

  // Sign the policy.
  $signature = $this->rsa_sha1_sign($canned_policy, $private_key_filename);

  // Make the signature contains only characters that // can be included in a URL.
  $encoded_signature = $this->url_safe_base64_encode($signature);

  // Combine the above into a properly formed URL name
  //echo  $asset_path . '?Expires=' . $expires . '&Signature=' . $encoded_signature . '&Key-Pair-Id=' . $key_pair_id; exit;
  return $asset_path . '?Expires=' . $expires . '&Signature=' . $encoded_signature . '&Key-Pair-Id=' . $key_pair_id;
 }

public function rsa_sha1_sign($policy, $private_key_filename){
  $signature = '';

  // Load the private key.
  $fp = fopen($private_key_filename, 'r');
  $private_key = fread($fp, 8192);
  fclose($fp);

  $private_key_id = openssl_get_privatekey($private_key);

  // Compute the signature.
  openssl_sign($policy, $signature, $private_key_id);

  // Free the key from memory.
  openssl_free_key($private_key_id);

  return $signature;
 }

public function url_safe_base64_encode($value){
  $encoded = base64_encode($value);

  // Replace characters that cannot be included in a URL.
  return str_replace(array('+', '=', '/'), array('-', '_', '~'), $encoded);
 }


}