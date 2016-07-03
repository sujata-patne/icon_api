<?php
require_once APP.'Models/Message.php';
require_once APP.'Models/Store.php';
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:12
 */
class StoreController extends BaseController {

    public function __construct(){
        parent::__construct();
    }
    public function getAction($request){
        parent::display($request);
    }
   /* public function postAction($request){
        parent::display($request);
    }*/
    public function getStoreDetailsByStoreId($request){
        $json = json_encode( $request->parameters );
        $store = new Store();
        $jsonObj = json_decode($json);

        $validationMessage = $store->validateInputParam($jsonObj);
        if ($validationMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $validationMessage);
            $this->outputError($response);
            return;
        }
        if (!$store->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $jsonObj->storeId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
            $this->outputError($response);
            return;
        }
        $storeDetails = $store->getStoreDetailsByStoreId( $jsonObj->storeId );
        $this->logCurlAPI($storeDetails);

        if( empty( $storeDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_STORE_LOAD );
            $this->outputError($response);
            return;
        }else{
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'storeDetails' => $storeDetails );
            $this->outputSuccess($response);
            return;
        }

    }
    public function getVendorsList($request){
        $json = json_encode( $request->parameters );
        $store = new Store();
        $jsonObj = json_decode($json);

        //$validationMessage = $store->validateInputParam($jsonObj);
        /*if ($validationMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $validationMessage);
            $this->outputError($response);
            return;
        }*/
        if (!$store->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        /*if (trim( $jsonObj->storeId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
            $this->outputError($response);
            return;
        }*/
        $storeVendorDetails = $store->getVendorsList();    //$jsonObj->storeId
         if( empty( $storeVendorDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_STORE_LOAD );
            $this->outputError($response);
            return;
        }else{
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'storeVendorDetails' => $storeVendorDetails );
            $this->outputSuccess($response);
            return;
        }

    }

    public function getCGImagesByStoreId($request){
        $json = json_encode( $request->parameters );
        $store = new Store();
        $jsonObj = json_decode($json);

        $validationMessage = $store->validateInputParamForCG($jsonObj);
        if ($validationMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $validationMessage);
            $this->outputError($response);
            return;
        }
        if (!$store->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $jsonObj->storeId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $jsonObj->deviceSize == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_DEVICE_SIZE_ID );
            $this->outputError($response);
            return;
        }
        $storeCGImages = $store->getCGImagesByStoreId( $jsonObj->storeId, $jsonObj->deviceSize );

        if( empty( $storeCGImages ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_STORE_LOAD );
            $this->outputError($response);
            return;
        }else{
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'storeCGImages' => $storeCGImages );
            $this->outputSuccess($response);
            return;
        }

    }
    public function getSubscriptionPricePoints($request){
        $json = json_encode( $request->parameters );
        $store = new Store();
        $jsonObj = json_decode($json);

        $validationMessage = $store->validateInputParam($jsonObj);
        if ($validationMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $validationMessage);
            $this->outputError($response);
            return;
        }
        if (!$store->validateInputJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
        if (trim( $jsonObj->storeId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_STORE_ID );
            $this->outputError($response);
            return;
        }
        if (trim( $jsonObj->operatorId == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
            $this->outputError($response);
            return;
        }
        $storeDetails = $store->getSubscriptionPricePoints( $jsonObj->storeId,$jsonObj->operatorId );

        if( empty( $storeDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_STORE_LOAD );
            $this->outputError($response);
            return;
        }else{
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'subscriptionPricePoints' => $storeDetails );
            $this->outputSuccess($response);
            return;
        }

    }

}