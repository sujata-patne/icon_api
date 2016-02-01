<?php
function printData($obj){
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}

require_once('config.php');
require_once "Config/bootstrap.php";
require_once "Config/Request.php";
header("Access-Control-Allow-Origin: *");

spl_autoload_register('apiAutoload');
function apiAutoload($classname){
	if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
		include __DIR__ . '/Controllers/' . $classname . '.php';
		return true;
	} elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
		include __DIR__ . '/Models/' . $classname . '.php';
		return true;
	} elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
		include __DIR__ . '/views/' . $classname . '.php';
		return true;
	}
}

$requestURI = explode('/', $_SERVER['REQUEST_URI']);
$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
$index = 3;
//echo "<pre>"; print_r($requestURI); exit;
if( stripos( strtolower($requestURI[$index]), "index" ) === false ){
	// route the request to the right place
	$controller_name = ucfirst($requestURI[$index]) . 'Controller';
	if (class_exists($controller_name)) {
		$request = new Request();
		$verb = $request->getRequestType();
		$_SERVER['REQUEST_METHOD'] = $verb;
		$controller = new $controller_name();
		//echo "<pre>"; print_r( $request->url_elements); exit;
		if( isset( $request->url_elements[2] ) ) {
			$action_name = $request->url_elements[2];
		} else{
			$action_name = strtolower($verb) . 'Action';
		}

		$result = $controller->$action_name($request);
		printData($result);
	}else{
		header('HTTP/1.0 404 Not Found');
		$errorPage = "<html>
					    <head>
					        <title>404 Not Found</title>
					    </head>
					    <body>
					        <h1>Not Found</h1>
					        <p>The requested URL" .$_SERVER['REQUEST_URI']. " was not found on this server.</p>
					        <hr>
					            <address>Apache/2.4.7 (Ubuntu) Server at localhost Port 80</address>
					        </body>
					    </html>";

		print_r($errorPage);
		exit;
	}
}else{
	printData("Invalid API Call!");
	exit;
}

?>
