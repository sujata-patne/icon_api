<?php
require_once APP.'Models/Message.php';
require_once APP.'Models/ContentFile.php';
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
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'isContentFileExist' => $isContentFileExist );
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
       // echo "<pre>"; print_r($templates['templateId']);

        if( !empty( $templates ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'templates' => $templates['templateId'] );
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
}