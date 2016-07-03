<?php
require_once(APP."Models/ContentDownloadHistory.php");
require_once(APP."Models/Message.php");

class ContentDownloadHistoryController extends BaseController {

	public function __construct() {
		parent::__construct();
	}
	
    public function getAction($request){
        parent::display($request);
    }

    public function postAction( $request ) {
       echo "coming";exit;
    }

    public function getContentDownloadHistory( $request ) {
    	$json = json_encode( $request->parameters );
    	
    	$contentDownloadHistory = new ContentDownloadHistory();
    	$jsonObj = json_decode($json);
    	
    	
    	$jsonMessage = $contentDownloadHistory->validateJson($jsonObj);
    	
    	if ($jsonMessage != Message::SUCCESS) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
    		$this->outputError($response);
    		return;
    	}
    	
    	if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
    		$this->outputError($response);
    		return;
    	}
    	 
    	if (trim( $contentDownloadHistory->cd_msisdn ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_MSISDN );
    		$this->outputError($response);
    		return;
    	}
    	
    	if (trim( $contentDownloadHistory->cd_user_id ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_USER_ID );
    		$this->outputError($response);
    		return;
    	}
    	
    	if (trim( $contentDownloadHistory->cd_app_id ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_APP_ID );
    		$this->outputError($response);
    		return;
    	}
    	

    	$downloadHistoryArray = $contentDownloadHistory->getContentDownloadHistoryByMsisdnByUserIdByAppId( $contentDownloadHistory );
        // echo "<pre>";
        // print_r($downloadHistoryArray);
        // exit;
    	
    	if( !empty( $downloadHistoryArray ) ){
    		$cdIds = array();
    		$cmdIds = array();
    		foreach( $downloadHistoryArray as $downloadHistory ) {
    			$cdIds[] = $downloadHistory->cd_cd_id;
    			$cmdIds[] = $downloadHistory->cd_cmd_id;
    		}
    		if( !empty( $cdIds ) ){
    			$cdIds = implode( ",", array_unique($cdIds) );
    		}
    		
    		if( !empty( $cmdIds ) ){
    			$cmdIds = implode( ",", array_unique($cmdIds) );
    		}
    		
    		$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );

    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentHistoryDetails' => $contentHistoryDetails );
    		$this->outputSuccess($response);
    		return;
    		 
    	}else{
    		$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No download history available !.' );
    		$this->outputError($response);
    		return;
    	}
    }
    
    public function getContentMetaData( $request ) {
        $json = json_encode( $request->parameters );
        
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);
        
        
        $jsonMessage = $contentDownloadHistory->validateJsonObj($jsonObj);
        
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        
        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_cmd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }

        $contentMetaDataObj = $contentDownloadHistory->getContentMetaDataById($contentDownloadHistory->cd_cmd_id);
        if(!empty($contentMetaDataObj)){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentMetaDataDetail' => $contentMetaDataObj );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No content metadata information available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function getCatalogueDetail( $request ) {
        $json = json_encode( $request->parameters );
        
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);
        
        
        $jsonMessage = $contentDownloadHistory->validateJsonForCatalogueDetail($jsonObj);
        
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        
        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_cd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_ID );
            $this->outputError($response);
            return;
        }

        $catalogueDetailObj = $contentDownloadHistory->getCatalogueDetailById($contentDownloadHistory->cd_cd_id);
        if(!empty($catalogueDetailObj)){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'catalogueDetail' => $catalogueDetailObj );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No catalogue detail information available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function checkDownloadInfo( $request ) {
        $json = json_encode( $request->parameters );
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);

        $jsonMessage = $contentDownloadHistory->validateJsonForCheckDownloadInfo($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }

        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_msisdn ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_MSISDN );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_user_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_USER_ID );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_app_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_APP_ID );
            $this->outputError($response);
            return;
        }
       /* if (trim( $contentDownloadHistory->singleDayLimit ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_SINGLE_DAY_LIMIT );
            $this->outputError($response);
            return;
        }*/
        if (trim( $contentDownloadHistory->sub_start_date ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_SUB_START_DATE );
            $this->outputError($response);
            return;
        }
        $downloadHistoryArray = $contentDownloadHistory->checkDownloadInfo( $contentDownloadHistory );

        if( !empty( $downloadHistoryArray ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentHistoryDetails' => $downloadHistoryArray );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No download history available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function updateDownloadInfo( $request ) {
        $json = json_encode( $request->parameters );
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);

        $jsonMessage = $contentDownloadHistory->validateJsonForUpdateDownloadInfo($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }

        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_msisdn ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_MSISDN );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_user_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_USER_ID );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_app_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_APP_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_cd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_cmd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_download_count ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_DOWNLOAD_COUNT );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_download_date ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_DOWNLOAD_DATE );
            $this->outputError($response);
            return;
        }
        $downloadHistoryArray = $contentDownloadHistory->updateDownloadInfo( $contentDownloadHistory );

        if( !empty( $downloadHistoryArray ) ){
            //$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentHistoryDetails' => $downloadHistoryArray );
            $this->outputSuccess($response);
            return;

        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No download history available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function insertDownloadInfo( $request ) {
        $json = json_encode( $request->parameters );
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);

        $jsonMessage = $contentDownloadHistory->validateJsonForUpdateDownloadInfo($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }

        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_msisdn ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_MSISDN );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_user_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_USER_ID );
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_app_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_APP_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_cd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_cmd_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_download_count ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_DOWNLOAD_COUNT );
            $this->outputError($response);
            return;
        }
        if (trim( $contentDownloadHistory->cd_download_date ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CD_DOWNLOAD_DATE );
            $this->outputError($response);
            return;
        }
        $downloadHistoryArray = $contentDownloadHistory->insertDownloadInfo( $contentDownloadHistory );
        /*echo "<pre>";
        print_r($downloadHistoryArray);
        exit;*/

        if( !empty( $downloadHistoryArray ) ){
            //$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentHistoryDetails' => $downloadHistoryArray );
            $this->outputSuccess($response);
            return;

        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No download history available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function checkDownloadLimit( $request ){
        $json = json_encode( $request->parameters );
        $contentDownloadHistory = new ContentDownloadHistory();
        $jsonObj = json_decode($json);

        $jsonMessage = $contentDownloadHistory->validateJsonForCheckDownloadLimit($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }

        if (!$contentDownloadHistory->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contentDownloadHistory->cd_msisdn ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_MSISDN );
            $this->outputError($response);
            return;
        }
    }
}

?>