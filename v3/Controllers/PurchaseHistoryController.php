<?php

use VOP\Daos\BaseDao;
require_once(APP."Daos/BaseDao.php");
require_once APP.'Models/PurchaseHistory.php';
require_once(APP."Models/Message.php");
require_once(APP."Controllers/DeviceController.php");
include_once(APP."Controllers/config.class.php");
use Store\Config as Config;

class PurchaseHistoryController extends BaseController{
	
	public function __construct(){
		parent::__construct();
		$this->purchaseHistory = new PurchaseHistory();
	}
	
	public function createJsonObj( $request ){
		$json = json_encode( $request->parameters );   
      	return json_decode( $json );
	}
	
	public function createSuccessLogs( $request,$history,$controllerName ){
			$response = array("status" => "SUCCESS", "status_code" => '200', 'PurchaseHistory' => $history);
			$this->successLog->LogInfo('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
			$this->outputSuccess($response);
			return;
	}
	
	public function createErrorLogs( $jsonResMessage,$controllerName ){
		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
			$this->outputError($response);
			return;
		}
	}
	
	public function createEmptyLogs( $history,$emptyMsg,$controllerName,$request  ){
		if(empty($history)){		//in case of no history
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => $emptyMsg );
			$this->successLog->LogInfo('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
			$this->outputSuccess($response);
			return;
		}
	}
	
	public function createStreamingPath( $fileName ){

		$ext = pathinfo($fileName, PATHINFO_EXTENSION);

		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$domain = 'http://d12m6hc8l1otei.cloudfront.net/';
		$expires = time() + (60*60);
		$this->config = new Config\Config();
		
		switch($ext)
		{
			case 'mp3':
				$asset_path = $domain.'audio/'.$fileName;
				break;
					
			case 'txt':
				$asset_path = $domain.'text/'.$fileName;
				break;		
			
		}	
		
		$streamingURL = $this->config->create_signed_url($asset_path, $private_key_filename, $key_pair_id, $expires);			
		return $streamingURL;
	}
	
	public function getPurchaseHistory ( $request ){
		
		$emptyMsg 		= 'No Purchase History available for this User'; 
		$controllerName = 'getPurchaseHistory';
		$jsonObj 		= self::createJsonObj( $request );
		$jsonResMessage = $this->purchaseHistory->validateJsonObjforMSISDN( $request->parameters );
		self::createErrorLogs( $jsonResMessage,$controllerName );
		$purchaseHistory 	= $this->purchaseHistory->getDetails( 'getPurchaseHistory',$jsonObj );
	
		// if(!empty($purchaseHistory)){
			// for($i=0;$i<count($purchaseHistory);$i++){ //create streaming URL
				// $purchaseHistory[$i]['PreviewURL'] = self::createStreamingPath( $purchaseHistory[$i]['TuneName'] );		
			// }	
		// }
		
		self::createEmptyLogs( $purchaseHistory,$emptyMsg,$controllerName,$request );
		self::createSuccessLogs( $request,$purchaseHistory,$controllerName );
			
	}	
	
	
	
}

?>