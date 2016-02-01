<?php
require_once(APP."Models/Campaign.php");
require_once(APP."Models/Message.php");

class CampaignController extends BaseController {

	public function __construct() {
		parent::__construct();
	}
	
    public function getAction($request){
        parent::display($request);
    }

    public function postAction( $request ) {
       echo "coming";exit;
    }

    public function getCampaignDetailsByPromoId( $request ) {
        
        $json = json_encode( $request->parameters );
        $campaign = new Campaign();
      	$jsonObj = json_decode($json);
        $jsonMessage = $campaign->validateJson($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        
        if (!$campaign->setValuesFromJsonObj($jsonObj)) {
        	$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
        	$this->outputError($response);
        	return;
        }
        
        if (trim( $campaign->promoId == '') ) {
        	$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PROMO_ID );
        	$this->outputError($response);
        	return;
        }
        
        $campaignArray = $campaign->getCampaignDetailsByPromoId( $campaign->promoId );
        
        if( empty( $campaignArray ) ){
        	$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_CAMPAIGN_LOAD );
        	$this->outputError($response);
        	return;
        }else{
        	/* foreach( $campaignArray as $campaignObj ) {
        		$campaignObj->unsetValues( array( 'storeId') );
        	} */
        
        	$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'campaignDetails' => $campaignArray );
        	$this->outputSuccess($response);
        	return;
        }
    }
    
    public function getCampaignDetailsByStore( $request ) {
    
    	$json = json_encode( $request->parameters );
        $campaign = new Campaign();
      	$jsonObj = json_decode($json);
      	
        $jsonMessage = $campaign->validateJsonObj($jsonObj);
    
    	if ($jsonMessage != Message::SUCCESS) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
    		$this->outputError($response);
    		return;
    	}
    	
    	if (!$campaign->setValuesFromJsonObj($jsonObj)) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
    		$this->outputError($response);
    		return;
    	}
    	
    	if (trim( $campaign->promoId == '') ) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PROMO_ID );
    		$this->outputError($response);
    		return;
    	}
    	
    	if ( trim( $campaign->storeId == '' ) ) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
    		$this->outputError($response);
    		return;
    	}
    	
    	$campaignObj = $campaign->getCampaignDetailsByPromoIdByStoreId( $campaign->promoId, $campaign->storeId );
    	
    	
    	if( empty( $campaignObj ) ){
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_CAMPAIGN_LOAD );
    		$this->outputError($response);
    		return;
    	}else{
    	
    		//$campaignObj->unsetValues( array( 'storeName', 'promoId', 'created_on', 'created_by', 'updated_on', 'updated_by' ) );
    	
    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'campaignDetails' => $campaignObj );
    		$this->outputSuccess($response);
    		return;
    	}
    }
}
?>