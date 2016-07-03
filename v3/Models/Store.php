<?php
use VOP\Utils\PdoUtils;

require_once APP."Daos/StoreDao.php";
require_once APP.'Models/BaseModel.php';
require_once APP.'Models/Message.php';
require_once APP.'Utils/PdoUtils.php';

/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:17
 */
class Store extends BaseModel {
    public $storeId;
    public $operatorId;
    public $deviceSize;

    public function __construct() {
        $this->storeId = '';
        $this->operatorId = '';
        $this->deviceSize = '';
    }

    public function validateInputParam($jsonObj) {
        $requiredProps = array('storeId');

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }
    public function validateInputParamForCG($jsonObj) {
        $requiredProps = array('storeId','deviceSize');

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }
    public function validateInputJsonObj($jsonObj) {
        $requiredProps = array('storeId','operatorId');

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);

        if (!$result) {
            return $result;
        }
        return true;
    }

    public function getVendorsList(){ //$storeId
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeVendorDetails = array();
        try {
            $store = new StoreDao($dbConnection);
            $storeVendorDetails = $store->getVendorsList(); //$storeId
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeVendorDetails;
    }

    public function getStoreDetailsByStoreId($storeId){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        $this->logCurlAPI($dbConnection);
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeDetails = array();
        try {
            $store = new StoreDao($dbConnection);
            $storeDetails = $store->getStoreDetailsByStoreId( $storeId );
            array_push($storeDetails,$store->getPaymentChannelsByStoreId( $storeId ));
            array_push($storeDetails,$store->getPaymentTypeByStoreId( $storeId ));
            array_push($storeDetails,$store->getContentTypeByStoreId( $storeId ));
            array_push($storeDetails,$store->getDistributionChannelByStoreId( $storeId ));
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeDetails;
    }
    public function getCGImagesByStoreId($storeId,$deviceSize){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeDetails = array();
        try {
            $store = new StoreDao($dbConnection);
            $CGImagesDetails = $store->getCGImagesByStoreId( $storeId, $deviceSize );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $CGImagesDetails;
    }

    public function getSubscriptionPricePoints($storeId,$operatorId){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeDetails = array();
        try {
            $store = new StoreDao($dbConnection);
            $CGImagesDetails = $store->getSubscriptionPricePoints( $storeId, $operatorId );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $CGImagesDetails;
    }

}