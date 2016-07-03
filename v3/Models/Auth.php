<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 03/31/16
 * Time: 09:50 AM
 */
use VOP\Utils\PdoUtils;
require_once(APP."Daos/AuthDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Utils/PdoUtils.php");

class Auth extends BaseModel {

    public function __construct($json = NULL) {
        if (is_null($json)) {
            return;
        }
    }
    public function validateJsonMSISDN($jsonObj) {

        if (empty($jsonObj)) {
            $res_string = 'Invalid JSON';
            return $res_string;
        }elseif(ctype_alpha($jsonObj->msisdn)){
			$res_string = 'Enter Numeric Values only';
            return $res_string;
		}else{
            $MSISDNResponse = '';

            try{
                    if (isset($jsonObj->msisdn) && trim($jsonObj->msisdn) == '' || trim($jsonObj->msisdn) == null) {
                        $MSISDNResponse = Message::ERROR_BLANK_MSISDN;
                    }

                $res_string = $MSISDNResponse;
                return $res_string;
            }
            catch(Exception $e)
            {
                $e->getMessage();
                $error_res_string = 'Exception #' .$e ;
                return $error_res_string;
            }
        }
    }
    public function getAuthDetails( $function,$authObj ) {

        $dbConnection = PdoUtils::obtainConnection('CMS');

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $Contents = array();
        $auth = new AuthDao($dbConnection);

        try {

            switch($function)
            {
                case 'getEligibility':
                    $Contents = $auth->getEligibility( $authObj );
                    break;

                case 'getUserStatusforSubscription':
                    $Contents = $auth->getUserStatusforSubscription( $authObj );
                    break;

                case 'getUserStatusforValuePack':
                    $Contents = $auth->getUserStatusforValuePack( $authObj );
                    break;

                case 'getUserStatusforOffer':
                    $Contents = $auth->getUserStatusforOffer( $authObj );
                    break;

                case 'getUserStatusforAlacart':
                    $Contents = $auth->getUserStatusforAlacart( $authObj );
                    break;

            }

            $dbConnection->commit();

        } catch (\Exception $e) {
            $dbConnection->rollBack();
            print_r($e->getMessage());
            exit;
        }

        PdoUtils::closeConnection($dbConnection);
        return $Contents;
    }


}