<?php
date_default_timezone_set('Asia/Kolkata');

//$current_mode = 'local'; // Local Mode 
//$current_mode = 'production'; // Production Mode 
//$current_mode = 'staging'; // Staging Mode 
$current_mode = 'development'; // development Mode 

$config = array();

$config['production'] = array();
$config['development'] = array();
$config['staging'] = array();
$config['local'] = array();
 
$config['development']['DB'] = array(
     'CMS' => array(
		'db_host' => '192.168.1.174',
		'db_port' => '3306',
		'db_name' => 'icon_cms',
		'db_user' => 'iconadmin',
		'db_pass' => 'redhat'
	  ),
      'SITE_USER' => array(
		'db_host' => '192.168.1.174',
		'db_port' => '3306',
		'db_name' => 'site_user',
		'db_user' => 'iconadmin',
		'db_pass' => 'redhat'
      ),
      'CAMPAIGN' => array(
		'db_host' => '192.168.1.174',
		'db_port' => '3306',
		'db_name' => 'campaign_manager',
		'db_user' => 'iconadmin',
		'db_pass' => 'redhat'
      )
);
$config['staging']['DB'] = array(
     'CMS' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'icon_cms',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
	  ),
      'SITE_USER' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'site_user',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
      ),
      'CAMPAIGN' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'campaign_manager',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
      )
);
$config['local']['DB'] = array(
     'CMS' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'icon_cms',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
	  ),
      'SITE_USER' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'site_user',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
      ),
      'CAMPAIGN' => array(
		'db_host' => '192.168.1.160',
		'db_port' => '3306',
		'db_name' => 'campaign_manager',
		'db_user' => 'iconadmin',
		'db_pass' => 'icon@dm!n'
      )
);

$config['production']['DB'] = array(
     'CMS' => array(
		'db_host' => '10.64.12.136',
		'db_port' => '3306',
		'db_name' => 'icon_cms',
		'db_user' => 'kafka',
		'db_pass' => 'kafka@dm!n'
	  ),
      'SITE_USER' => array(
		'db_host' => '10.64.12.136',
		'db_port' => '3306',
		'db_name' => 'site_user',
		'db_user' => 'kafka',
		'db_pass' => 'kafka@dm!n'
      ),
      'CAMPAIGN' => array(
		'db_host' => '10.64.12.136',
		'db_port' => '3306',
		'db_name' => 'campaign_manager',
		'db_user' => 'kafka',
		'db_pass' => 'kafka@dm!n'
      )
);

//$config['local']['SITE_USER']['db_host'] = 'localhost';
//$config['local']['SITE_USER']['db_port'] = '3306';
//$config['local']['SITE_USER']['db_name'] = 'site_user';
//$config['local']['SITE_USER']['db_username'] = 'root';
//$config['local']['SITE_USER']['db_password'] = 'password';

$config['local']['log_file'] = '/var/www/html/wICONapi/weblogs/log.txt';
$config['local']['vendor_dir'] = '/var/www/html/wICONapi/vendor/';
$config['local']['base_url'] = 'http://localhost/wICONapi/api/v2/';
$config['local']['base_path'] = '/var/www/html/wICONapi/api/v2/';
$config['local']['dbConnect'] = '';
$config['current_mode'] = $current_mode;
$config['local']['image_copy_path'] = '/var/www/html/wICONapi/images/';


//$config['current_mode'] = 'development';

//$config['log_file'] = '/Users/mobisoft/Pritam/ImpFrequent/Projects/Web/voteonpics/weblogs/log.txt';

//$config['fb_app_id'] = '808742129148639';
//$config['fb_app_secret'] = 'ffb19a14c866b991af9a857304a673b1';

//define('VENDOR_DIR', '/Users/mobisoft/Pritam/ImpFrequent/Projects/Web/voteonpics/gitsrc/web/vendor/');
define('VENDOR_DIR', $config[$current_mode]['vendor_dir']);
