<?php
require_once APP.'Models/Message.php';
require_once APP.'Models/Operator.php';
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:12
 */
class OperatorsController extends BaseController {

    public function __construct(){
        parent::__construct();
    }
    public function getAction($request){
        parent::display($request);
    }

    public function insertVcode( $request ) {
        $json = json_encode( $request->parameters );
        $operators = new Operators();
        $jsonObj = json_decode($json);
         $jsonMessage = $operators->validateJsonForInsertVcode($jsonObj);
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
        if (!$operators->setValuesFromJsonObj($jsonObj)) {

            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $operators->vo_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_VO_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $operators->vo_cf_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $operators->vo_operator_id ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $operators->vo_vcode ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_VCODE );
            $this->outputError($response);
            return;
        }

        $vcodes = $operators->insertVcode( $operators );

        if( !empty( $contentFiles ) ){
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'vcodes' => $vcodes );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No vcode available !.' );
            $this->outputError($response);
            return;
        }
    }

    public function getMaxVOId()
    {
         $contents = new Contents();
         $maxVOID = $contents->getMaxVOId();

        if( !empty( $maxVOID ) ){
            //$contentHistoryDetails = $contentDownloadHistory->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'maxVOID' => $maxVOID );
            $this->outputSuccess($response);
            return;
        }else{
            $response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No data available !.' );
            $this->outputError($response);
            return;
        }
    }

}