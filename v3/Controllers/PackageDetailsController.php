<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 23-12-2015
 * Time: 18:37
 */

require_once APP.'Models/Message.php';
require_once APP.'Models/Package.php';
class PackageDetailsController extends BaseController{
    private $packageDetails;

    public function __construct(){
        $this->packageDetails = new Package();
    }

    public function getAction($request){
        parent::display($request);
    }

    public function postAction($request){
        $data = $request->parameters;
        $result = $this->packageDetails->find(ICONDB, $data);
        echo json_encode($result);
    }
    public function getSubscriptionPricePointsByPackageId( $request ) {

        $json = json_encode( $request->parameters );

        $package = new Package();
        $jsonObj = json_decode($json);
        $jsonMessage = $package->validateJsonForPricePointByPackageId($jsonObj);

        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }

        if (!$package->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }

        if(trim( $jsonObj->packageId ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_PACKAGE_ID );
            $this->outputError($response);
            return;
        }

        if (trim( $jsonObj->operatorId ) == '') {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
            $this->outputError($response);
            return;
        }

        $pricePointsDetails = $package->getSubscriptionPricePointsByPackageId( $jsonObj->packageId, $jsonObj->operatorId );

        if( empty( $pricePointsDetails ) ){
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_PACKAGE_LOAD );
            $this->outputError($response);
            return;
        }else{
            $response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'subscriptionPricePoints' => $pricePointsDetails );
            $this->outputSuccess($response);
            return;
        }
    }

}
?>