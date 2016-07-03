<?php
require_once APP.'Models/Message.php';
require_once APP.'Models/ContentFile.php';
include_once(APP."Controllers/config.class.php");

use Store\Config as Config;
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:12
 */
class ContentFileController extends BaseController {

    public function __construct(){
        parent::__construct();
    }
    public function getAction($request){
        parent::display($request);
    }

    public function isContentFileExist( $request ) {
        $json = json_encode( $request->parameters );
        $contents = new ContentFile();
        $jsonObj = json_decode($json);

        $jsonMessage = $contents->validateJsonForUpdateInfo($jsonObj);
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        if (!$contents->setValuesFromJsonObj($jsonObj)) {

            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if (trim( $contents->cf_cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }

        if (trim( $contents->cf_template_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_TEMPLATE_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_name ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }

        $isContentFileExist = $contents->isContentFileExist( $contents );
        if( !empty( $isContentFileExist ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'isContentFileExist' => $isContentFileExist['cf_id'] );
            $this->outputSuccess($response);
            return;

        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No upload files available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function checkContentFileExistForMetadata( $request ) {
    	$json = json_encode( $request->parameters );
    	$contents = new ContentFile();
    	$jsonObj = json_decode($json);
    
    	$jsonResMessage = $contents->validateJsonForContentFilesExist($request->parameters);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentFileController:checkContentFileExistForMetadata#'.json_encode($response));
			$this->outputError($response);
			return;
		}
    	$isContentFileExist = $contents->checkContentFileExistForMetadata( $jsonObj );
    	if( !empty( $isContentFileExist ) ){
    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'isContentFileExist' => $isContentFileExist['cf_id'] );
    		$this->outputSuccess($response);
    		return;
    
    	}else{
    		$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No upload files available !.' );
    		$this->outputError($response);
    		return;
    	}
    }
    
    public function insertContentFiles( $request ) {
        $json = json_encode( $request->parameters );
  //     echo "<pre>"; print_r($json);
        $contents = new ContentFile();
        $jsonObj = json_decode($json);

        $jsonMessage = $contents->validateJsonForInsertInfo($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        if (!$contents->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        //echo "<pre>insertContentFiles api"; print_r($contents);
        if (trim( $contents->cf_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_url_base ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_BASE_URL );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_url ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_URL );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_template_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_TEMPLATE_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_name ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_name_alias ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_ALIAS );
            $this->outputError($response);
            return;
        }

        $contentFiles = $contents->insertContentFiles( $contents );

        if( !empty( $contentFiles ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentFiles' => $contentFiles );
            $this->outputSuccess($response);
            return;

        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No upload files available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function updateContentFiles( $request ) {
    	$json = json_encode( $request->parameters );
    	$contents = new ContentFile();
    	$jsonObj = json_decode($json);
    //echo "<pre>"; print_r($contents); exit;
    	$jsonMessage = $contents->validateJsonForUpdateURLInfo($jsonObj);
    	if ($jsonMessage != Message::SUCCESS) {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
    		$this->outputError($response);
    		return;
    	}
    	if (!$contents->setValuesFromJsonObj($jsonObj)) {    
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
    		$this->outputError($response);
    		return;
    	}
    
    	if (trim( $contents->cf_id ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_ID );
    		$this->outputError($response);
    		return;
    	}
    
    	if (trim( $contents->cf_streaming_url ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_STREAMING_URL );
    		$this->outputError($response);
    		return;
    	}
    	if (trim( $contents->cf_downloading_url ) == '') {
    		$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_DOUNLOAD_URL );
    		$this->outputError($response);
    		return;
    	}
    
    	$contentFiles = $contents->updateContentFiles( $contents );
//echo "<pre>"; print_r($contentFiles ); 
    	if( $contentFiles ){
    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentFiles' => $contentFiles );
    		$this->outputSuccess($response);
    		return;
    
    	}else{
    		$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No record updated !.' );
    		$this->outputError($response);
    		return;
    	}
    }


    public function getTemplateIdForLanguage( $request ) {
        $json = json_encode( $request->parameters );
        $contents = new ContentFile();
        $jsonObj = json_decode($json);

        $jsonMessage = $contents->validateJsonForContentMetadata($jsonObj);
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        if (!$contents->setValuesFromJsonObj($jsonObj)) {

            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        $templates = $contents->getTemplateIdForLanguage( $contents );
       // echo "<pre>"; print_r($templates);

        if( !empty( $templates ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'templates' => $templates );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No template available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function getMaxCFId() {
         $contents = new ContentFile();
         $maxCFID = $contents->getMaxCFId();

        if( !empty( $maxCFID ) ){
            //$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'maxCFID' => $maxCFID );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No data available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function getAllTemplates() {
        $contents = new ContentFile();
        $templates = $contents->getAllTemplates();

        if( !empty( $templates ) ){
             $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'templates' => $templates );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No template available !.' );
            $this->outputError($response);
            return;
        }
    }
    public function getTemplateIdForBitrate() {
        $contents = new ContentFile();
        $templates = $contents->getTemplateIdForBitrate();

        if( !empty( $templates ) ){
            //$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'templates' => $templates );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No template available !.' );
            $this->outputError($response);
            return;
        }
    }
    
    public function getTemplateIdForHeightWidth($request) {
    	$json = json_encode( $request->parameters );
    	$contents = new ContentFile();
    	$jsonObj = json_decode($json);

    	//echo "<pre>"; print_r($json);    	echo "<pre>"; print_r($jsonObj); exit;
     	$jsonResMessage = $contents->validateJsonForTemplateHeightWidth($request->parameters);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentFileController:getTemplateIdForHeightWidth#'.json_encode($response));
			$this->outputError($response);
			return;
		}
     	$templates = $contents->getTemplateIdForHeightWidth($jsonObj);
    
    	if( !empty( $templates ) ){
    		//$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
    		$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'templates' => $templates );
    		$this->outputSuccess($response);
    		return;
    	}else{
    		$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No template available !.' );
    		$this->outputError($response);
    		return;
    	}
    }

    public function getMaxMetaContentId( $request ) {
        $json = json_encode( $request->parameters );
        $contents = new ContentFile();
        $jsonObj = json_decode($json);
        $jsonMessage = $contents->validateJsonForContentMetadata($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        if (!$contents->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cf_cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        $maxMCFID = $contents->getMaxMetaContentId( $contents );

        if( !empty( $maxMCFID ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'maxMCFID' => $maxMCFID['maxChildId'] );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No Content File available !.' );
            $this->outputError($response);
            return;
        }
    }
	
	public function createDownloadingPath( $fileName,$fileType ){										//create downloading path

		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);	
		$cdn = 'http://d12m6hc8l1otei.cloudfront.net';
		$expires = time() + (60*60);
		
		switch(true)
		{
		  case ($fileType == 2 and $ext == 'mp3'):
			$asset_path = $cdn.'/supporting_files/audio_files/'.$fileName;
			break;
		  case ($fileType == 2 and $ext == 'txt'):
			$asset_path = $cdn.'/supporting_files/text_files/'.$fileName;
			break;
		  case ($fileType == 2 and $ext == '3gp'):
		  case ($fileType == 2 and $ext == 'mp4'):
			$asset_path = $cdn.'/supporting_files/video_files/'.$fileName;
			break;
		  case ($fileType == 2 and $ext == 'jpg'):
		  case ($fileType == 2 and $ext == 'jpeg'):
		  case ($fileType == 2 and $ext == 'gif'):
		  case ($fileType == 2 and $ext == 'png'):
			$asset_path = $cdn.'/supporting_files/image_files/'.$fileName;
			break;
		  case ($fileType == 3 and $ext == 'mp3'):
			$asset_path = $cdn.'/supporting_files/audio_files/'.$fileName;
			break;
		  case ($fileType == 3 and $ext == 'txt'):
			$asset_path = $cdn.'/supporting_files/text_files/'.$fileName;
			break;
		  case ($fileType == 3 and $ext == '3gp'):
		  case ($fileType == 3 and $ext == 'mp4'):
			$asset_path = $cdn.'/supporting_files/video_files/'.$fileName;
			break;
		  case ($fileType == 3 and $ext == 'jpg'):
		  case ($fileType == 3 and $ext == 'jpeg'):
		  case ($fileType == 3 and $ext == 'gif'):
		  case ($fileType == 3 and $ext == 'png'):
			$asset_path = $cdn.'/supporting_files/image_files/'.$fileName;
			break;			
		  default:
			echo 'nothing';
			break;
		}
		
		$signed_url = $this->config->create_signed_url($asset_path, $private_key_filename, $key_pair_id, $expires);
		return $signed_url;
	}
	
	
	public function createNewPaths( $request ){
		
		$json = json_encode( $request->parameters );
        $contents = new ContentFile();
        $jsonObj = json_decode($json);
		$this->config = new Config\Config();
		
        $jsonMessage = $contents->validateJsonForFileType($jsonObj);
	
		if($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
       
        if (trim( $jsonObj->fileType ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
		
		$fileType = $jsonObj->fileType;
		
		if($fileType == 1){		//main files
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => 'Do not play with main files !' );
            $this->outputError($response);
		}else{	
			$contentFiles = $contents->getContentFiles($fileType);	
		}
		
		if(!empty($contentFiles)){
			
				for($i=0;$i<count($contentFiles);$i++){	                                          
					//$contentFiles[$i]['PreviewURL']    = self::createStreamingPath( $contentFiles[$i]['TuneName'] );				
					$contentFiles[$i]['DownloadingURL'] = self::createDownloadingPath( $contentFiles[$i]['FileName'],$fileType );					
					$updatePaths					    = $contents->updateContentFilePaths( $contentFiles );	
					
				}	
			}
			
		 if( $updatePaths ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'msg' => 'Records were updated' );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'Records not updated' );
            $this->outputError($response);
            return;
	
		}	
			
			
	}
	
	
}