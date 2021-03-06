<?php
require_once APP.'Models/Message.php';
require_once APP.'Models/Contents.php';
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:12
 */
class ContentsController extends BaseController {

    public function __construct(){
        parent::__construct();
    }
    public function getAction($request){
        parent::display($request);
    }

    public function updateContentMetadata( $request ) {
        $json = json_encode( $request->parameters );
        $contents = new Contents();
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
        if (trim( $contents->cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cm_state ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STATE );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cm_streaming_url ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_BASE_URL );
            $this->outputError($response);
            return;
        }
        if (trim( $contents->cm_downloading_url ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_URL );
            $this->outputError($response);
            return;
        }

        $contents = $contents->updateContentMetadata( $contents );

        if( !empty( $contentFiles ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contents' => $contents );
            $this->outputSuccess($response);
            return;

        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No upload files available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function getContentDeliveryTypesById( $request ) {
        $json = json_encode( $request->parameters );

        $contents = new Contents();
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

        if (trim( $contents->cm_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CMD_ID );
            $this->outputError($response);
            return;
        }

        $contentMetaDataObj = $contents->getContentDeliveryTypesById($contents->cf_cm_id);
        if(!empty($contentMetaDataObj)){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'contentDeliveryType' => $contentMetaDataObj );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No content metadata information available !.' );
            $this->outputError($response);
            return;
        }
    }
}