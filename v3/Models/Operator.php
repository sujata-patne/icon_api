<?php
use VOP\Utils\PdoUtils;

require_once APP."Daos/OperatorsDao.php";
require_once APP.'Models/BaseModel.php';
require_once APP.'Models/Message.php';
require_once APP.'Utils/PdoUtils.php';

/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:17
 */
class Operators extends BaseModel {
    public $vo_id;
    public $vo_cf_id;
    public $vo_operator_id;
    public $vo_vcode;
    public $vo_created_on;
    public $vo_modified_on;

    public function __construct() {
        $this->vo_id = '';
        $this->vo_cf_id = '';
        $this->vo_operator_id = '';
        $this->vo_vcode = '';
        $this->vo_created_on = '';
        $this->vo_modified_on = '';
    }

    public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);
        $this->unsetValues( array( 'created_on', 'created_by', 'updated_on', 'updated_by' ) );

        if (!$result) {
            return $result;
        }
        return true;
    }

    public function validateJsonForInsertVcode($jsonObj) {
        $requiredProps = array( 'vo_id', 'vo_cf_id', 'vo_operator_id', 'vo_vcode' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function insertVcode($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $vcodeDetails = array();
        try {
            $operators = new OperatorsDao($dbConnection);
            $vcodeDetails = $operators->insertVcode( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $vcodeDetails;
    }

    public function getMaxVOId(){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxVOID = array();
        try {
            $operators = new OperatorsDao($dbConnection);
            $maxVOID = $operators->getMaxVOId();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxVOID;
    }

}