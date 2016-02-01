<?php

namespace VOP\Utils;
require_once( "Log.php");
class Logger {

    public static function logException($e) {
        //$app = \Slim\Slim::getInstance();
        $log = new \Log(3, $e);
        $log = $log->critical($e->getMessage());
        $app->log->critical($e);
    }

    public static function critical($obj) {
        $app = \Slim\Slim::getInstance();
        $app->log->critical($obj);
    }

    public static function debug($obj) {
        $app = \Slim\Slim::getInstance();
        $app->log->debug($obj);
    }

}
