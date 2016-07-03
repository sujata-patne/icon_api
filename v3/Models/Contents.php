<?php
use VOP\Utils\PdoUtils;

require_once APP."Daos/ContentsDao.php";
require_once APP.'Models/BaseModel.php';
require_once APP.'Models/Message.php';
require_once APP.'Utils/PdoUtils.php';

/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:17
 */
class Contents extends BaseModel {
    public $cm_id ;
    public $cm_state ;
    public $cm_streaming_url ;
    public $cm_downloading_url;
    public $cm_created_on;
    public $cm_modified_on;

    public function __construct() {
        $this->cm_id = '';
        $this->cm_state = '';
        $this->cm_streaming_url = '';
        $this->cm_downloading_url = '';
        $this->cm_created_on = '';
        $this->cm_modified_on = '';
    }

    public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);
        $this->unsetValues( array( 'created_on', 'created_by', 'updated_on', 'updated_by' ) );

        if (!$result) {
            return $result;
        }
        return true;
    }

    public function validateJsonForContentMetadata($jsonObj) {
        $requiredProps = array( 'cm_id' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function validateJsonForUpdateInfo($jsonObj) {
        $requiredProps = array( 'cm_id','cm_state' );
        //$requiredProps = array( 'cm_id','cm_state', 'cm_streaming_url','cm_downloading_url' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function getContentDeliveryTypesById( $cmdId ){
        $dbConnection = PdoUtils::obtainConnection('CMS');

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();
        $contentMetaData = array();

        try {
            $contents = new ContentsDao($dbConnection);
            $contentMetaData = $contents->getContentDeliveryTypesById( $cmdId );

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            print_r( $e );exit;
        }

        PdoUtils::closeConnection($dbConnection);
        return $contentMetaData;
    }

    public function updateContentMetadata($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $contentDetails = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $contentDetails = $contents->updateContentMetadata( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $contentDetails;
    }
    public function isContentMetadataExist($data){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        try {
            $contents = new ContentsDao($dbConnection);
            $isContentMetadataExist = $contents->isContentMetadataExist( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $isContentMetadataExist;
    }
    public function checkIsBulkUploadAllowed($data){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        try {
            $contents = new ContentsDao($dbConnection);
            $isContentMetadataExist = $contents->checkIsBulkUploadAllowed( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $isContentMetadataExist;
    }

    public function getMetadataStatus($data){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        try {
            $contents = new ContentsDao($dbConnection);
            $isContentMetadataExist = $contents->getMetadataStatus( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $isContentMetadataExist;
    }
}