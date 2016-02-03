<?php
require_once(APP."Models/Page.php");
require_once(APP."Models/Message.php");
require_once(APP."Models/Package.php");
require_once(APP."Models/DeviceInfo.php");
require_once(APP."Models/Pack.php");

class PageController extends BaseController {

	public function __construct() {
		parent::__construct();
	}
	
    public function getAction($request){
        parent::display($request);
    }

    public function postAction( $request ) {
       echo "coming";exit;
    }

    public function getPackageDetails( $request ) {
        
        $json = json_encode( $request->parameters );
        $page = new Page();
      	$jsonObj = json_decode($json);
        $jsonMessage = $page->validateJsonObj($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
             
        if (!$page->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        
        if (trim( $jsonObj->pageId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PAGE_ID );
            $this->outputError($response);
            return;
        }
        
        $packageDetails = $page->getPackageIdsByPageId( $jsonObj->pageId );
                  
        if( empty( $packageDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_PACKAGE_LOAD );
            $this->outputError($response);
            return;
        }else{
            foreach( $packageDetails as $packageDetail ) {
                $packageDetail->unsetValues( array('pageName', 'storeId', 'deviceHeight', 'deviceWidth', 'portletMapId', 'searchKey') );
            }
          
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'packageDetails' => $packageDetails );
            $this->outputSuccess($response);
            return;
        }
    }

    //Hari's api
    public function getPackageContents( $request ) {  	   
        $json = json_encode( $request->parameters );

        $package = new Package();
      	$jsonObj = json_decode($json);
      	
        $jsonMessage = $package->validateJsonForPackageContent($jsonObj);
      	
      	if ($jsonMessage != Message::SUCCESS) {
      		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
      		$this->outputError($response);
      		return;
      	}
      	
      	if (trim( $jsonObj->operatorId ) == '') {
      		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
      		$this->outputError($response);
      		return;
      	} 
      	 
      	$packageData = $jsonObj->packages;
     
      	foreach( $packageData as $key => $packageInfo ) {
      		$jsonMessage = $package->validateJsonForPackages( $packageData[$key] );
      		 
      		if($jsonMessage != Message::SUCCESS) {
      			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
      			$this->outputError($response);
      			return;
      		}
      		
      		if(trim( $packageInfo->packageId ) == '') {
      			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PACKAGE_ID );
      			$this->outputError($response);
      			return;
      		}
      		
      		if(trim( $packageInfo->contentType ) == '') {
      			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CONTENT_TYPE );
      			$this->outputError($response);
      			return;
      		}
      	}
      	
      	$packageDetails = array();
      	$valuePackPlanDetials = array();
      	$subscriptionPlanDetails = array();
      	$alacartPlanDetails = array();
      	$packageArray = array();
      	if( !empty( $packageData ) ) {
      		foreach( $packageData as $packageObj ){
      			$packageArray[$packageObj->packageId]['packageDetails'][] = $package->getPackageContentsByPackageIdByContentId( $packageObj );
      			$packageArray[$packageObj->packageId]['valuePackPlans'][] = $package->getValuePackPlanDetailsByPackageIdByOperatorId( $packageObj, $jsonObj->operatorId  );
      			$packageArray[$packageObj->packageId]['subscriptionPlans'][] = $package->getSubscriptionPlanDetailsByPackageIdByOperatorId( $packageObj, $jsonObj->operatorId  );
      			$packageArray[$packageObj->packageId]['alacartPlans'][] = $package->getAlacartaPlanDetailsByPackageIdByContentTypeByOperatorId($packageObj, $jsonObj->operatorId );
      		}
      		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'packageContents' => $packageArray );
      		$this->outputSuccess($response);
      		return;
      	}else {
      		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => 'Invalid package details!.');
      		$this->outputError($response);
      		return;
      	}
    }

    public function getPageDetails( $request ) {
            
        $json = json_encode( $request->parameters );
        $page = new Page();
        $jsonObj = json_decode($json);
        $jsonMessage = $page->validateJson($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
            
        if (!$page->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
         
        if (trim( $page->pageName == '') ) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PAGENAME);
            $this->outputError($response);
            return;
        }
            
        if (trim( $page->storeId == '') ) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID);
            $this->outputError($response);
            return;
        }
            
        if( trim( $page->deviceHeight == '' ) ) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_DEVICE_HEIGHT );
            $this->outputError($response);
            return;
        }
    
        if (trim( $page->deviceWidth == '') ) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_DEVICE_WIDTH );
            $this->outputError($response);
            return;
        }
            
        $device = new DeviceInfo();
        //$device->dc_height = $page->deviceHeight;
        //$device->dc_width = $page->deviceWidth;
            
        $templateObj = $device->getTemplateDetailsByDeviceHeightAndWidth( $page->deviceHeight, $page->deviceWidth );
        
        $pageDetails = $page->getPackageIdsByPageName( $page->pageName, $page->storeId );
            
        if( empty( $pageDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_PACKAGE_LOAD );
            $this->outputError($response);
            return;
        }else{
            
            $portletArray = array();
            $package = new Package();
            $pack = new Pack();
            
            $vendorsArray = $package->getVendorIdsByStoreId( $page->storeId );
            $vendorIds = array();
            if( !empty( $vendorsArray ) ){
            	foreach( $vendorsArray as $vendor ){
            		$vendorIds[] = $vendor['vendor_id'];
            	}
            	if( !empty( $vendorIds ) ){
            		$vendorIds = implode(",", $vendorIds);
            	}
            }

            foreach( $pageDetails as $pageDetail ){  
                if( $pageDetail->packageId > 0  && $pageDetail->portletId > 0 ) {
                    $portletArray[$pageDetail->portletId]['packageDetails'] = $package->getPortletsWithContentsByPackageIds($pageDetail->packageId, $pageDetail->portletId, $vendorIds );
                    $portletArray[$pageDetail->portletId]['packDetails']    = $pack->getAllPacksByPackageIds( $pageDetail->packageId , $pageDetail->portletId, $page->storeId, $templateObj['ct_group_id']);
                }
            }

            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200','potletMapDetails' => $pageDetails, 'portletDetails' => $portletArray );
   
            $this->outputSuccess($response);
            return;     
        }   
    }
    
    public function searchPageContents( $request ) {
    	
    	$json = json_encode( $request->parameters );
    	$page = new Page();
    	$jsonObj = json_decode($json);
    	$jsonMessage = $page->validateJsonForSearch($jsonObj);
    	
    		
    	if ($jsonMessage != Message::SUCCESS) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
    		$this->outputError($response);
    		return;
    	}
    
    	if (!$page->setValuesFromJsonObj($jsonObj)) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
    		$this->outputError($response);
    		return;
    	}
    		
    	if (trim( $page->storeId == '') ) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
    		$this->outputError($response);
    		return;
    	}
    		
    	if (trim( $page->searchKey == '') ) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CONTENT_TYPE);
    		$this->outputError($response);
    		return;
    	}
    
    	$packageContents = array();
    
    	$packageDetails = $page->getPackageIdsByStoreId( $page->storeId );
    		
    	if( empty( $packageDetails ) ){
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_PACKAGE_LOAD );
    		$this->outputError($response);
    		return;
    	}else{
            //echo "<pre>"; print_r($packageDetails);
    		$packageIds = array();
    		foreach( $packageDetails as $packageDetail ){
    			if( $packageDetail->packageId > 0 ) {
    				array_push( $packageIds, $packageDetail->packageId );
    			}
    		}
    		$packageIds = ( implode(",", $packageIds ) );
    		 
    		$searchContents = array();
    	    
    		$package = new Package();
    		$pack = new Pack();
            //echo "<pre>"; print_r($packageDetails);
            // echo $packageIds; echo $page->searchKey; echo $packageDetails[0]->vendorIds;
    		$searchContents = $package->getPortletsContentsBySearchKey( $packageIds, $page->searchKey, $packageDetails[0]->vendorIds );
    		
    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'searchContents' => $searchContents );
   
    		$this->outputSuccess($response);
    		return;	
    	}
    }
}
?>