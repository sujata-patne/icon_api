<?php
require_once(APP."Models/Page.php");
require_once(APP."Models/Subscription.php");
require_once(APP."Models/Message.php");

class SubscriptionController extends BaseController {

	public function __construct() {
		parent::__construct();
	}

	/* public function installPaths() {
		$controller = $this;

		$this->app->post('/subscription/subscriptionDetails', function() use ($controller) {
            $controller->getSubscriptionDetails();
        });

        $this->app->options('subscription/subscriptionDetails', function() use ($controller) {
        	//$controller->getDeviceInfo($id);
        }); */
        
       /*  $this->app->notFound(function() {
        	$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => Message::ERROR_PAGE_NOT_FOUND);
        	$this->outputNotFoundError($response);
        	return;
        }); */
/* 	} */	
	public function getSubscriptionDetails( $request ) {
		
		$json = json_encode( $request->parameters );
		$subscription = new Subscription();
		$jsonObj = json_decode($json);
		$jsonMessage = $subscription->validateJson($jsonObj);
			
		if ($jsonMessage != Message::SUCCESS) {
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
			$this->outputError($response);
			return;
		}
		 
		if (!$subscription->setValuesFromJsonObj($jsonObj)) {
		 	$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
		 	$this->outputError($response);
		 	return;
		}
		 	 
		if ($subscription->storeId == '') {
		 	$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID);
		 	$this->outputError($response);
		 	return;
		}
		
		$page = new Page();
		$packageDetails = $page->getMainSitePackageIdsByStoreId( $subscription->storeId );
		 
		if( empty( $packageDetails ) ){
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_PACKAGE_LOAD );
			$this->outputError($response);
			return;
		}else{
			$packageIds = array();
			foreach( $packageDetails as $packageDetail ){
				if( $packageDetail->packageId > 0 ) {
					array_push( $packageIds, $packageDetail->packageId );
				}
			}
			$packageIds = ( implode(",", $packageIds ) );
			
			$subscriptionArray = $subscription->getSubscriptionDetailsPackageIds( $packageIds );
			
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'subscriptionDetails' => $subscriptionArray );
			$this->outputSuccess($response);
			return;
		}
		
	}
}
?>