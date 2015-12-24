<?php
function printData($obj){
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}

require "Config/bootstrap.php";
require "Config/Request.php";

spl_autoload_register('apiAutoload');
function apiAutoload($classname){
    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        include __DIR__ . '/controllers/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        include __DIR__ . '/models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
        include __DIR__ . '/views/' . $classname . '.php';
        return true;
    } 
}

$requestURI = explode('/', $_SERVER['REQUEST_URI']);
$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);

if( stripos( strtolower($requestURI['2']), "index" ) === false ){
	// route the request to the right place
	$controller_name = ucfirst($requestURI['2']) . 'Controller';
	if (class_exists($controller_name)) {		
		$request = new Request();
		$verb = $request->getRequestType();
		$controller = new $controller_name();
		$action_name = strtolower($verb) . 'Action';
		$result = $controller->$action_name($request);
		printData($result);
	}else{
		printData($controller_name." Controller doesn't exists!");
		exit();
	}
}else{
	printData("Invalid API Call!");
	exit();
}

?>