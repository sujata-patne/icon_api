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
    public $cf_id;
    public $cf_cm_id;
    public $cf_url_base;
    public $cf_url;
    public $cf_absolute_url;
    public $cf_original_processed;
    public $cf_template_id;
    public $cf_name;
    public $cf_name_alias;
    public $cf_created_on;
    public $cf_modified_on;

    public function __construct() {
        $this->cf_id = '';
        $this->cf_cm_id = '';
        $this->cf_url_base = '';
        $this->cf_url = '';
        $this->cf_absolute_url = '';
        $this->cf_original_processed = '';
        $this->cf_template_id = '';
        $this->cf_name = '';
        $this->cf_name_alias = '';
        $this->cf_created_on = '';
        $this->cf_modified_on = '';
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
        $requiredProps = array( 'cf_cm_id' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function validateJsonForUpdateInfo($jsonObj) {
        $requiredProps = array( 'cf_cm_id','cf_template_id', 'cf_name' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function validateJsonForInsertInfo($jsonObj) {
        $requiredProps = array( 'cf_id', 'cf_cm_id', 'cf_url_base', 'cf_url','cf_template_id', 'cf_name', 'cf_name_alias' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function isContentFileExist($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        try {
            $contents = new ContentsDao($dbConnection);
            $isContentFileExist = $contents->isContentFileExist( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $isContentFileExist;
    }

    public function insertContentFiles($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeVendorDetails = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $storeVendorDetails = $contents->insertContentFiles( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeVendorDetails;
    }

    public function getTemplateIdForLanguage($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeVendorDetails = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $storeVendorDetails = $contents->getTemplateIdForLanguage( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeVendorDetails;
    }
    public function getMaxVOId(){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxVOID = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $maxVOID = $contents->getMaxVOId();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxVOID;
    }

    public function getMaxCFId(){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxCFID = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $maxCFID = $contents->getMaxCFId();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxCFID;
    }

    public function getMaxMetaContentId($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxMCFID = array();
        try {
            $contents = new ContentsDao($dbConnection);
            $maxMCFID = $contents->getMaxMetaContentId($data);
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxMCFID;
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
}