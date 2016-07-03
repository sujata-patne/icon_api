<?php

use VOP\Utils\PdoUtils;

require_once(APP."Utils/PdoUtils.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Daos/PurchaseHistoryDao.php");
require_once(APP."Models/Message.php");


class PurchaseHistory extends BaseModel {
	
	public $UserName;
	public $Operator;
	public $CelebrityName;
	public $cf_id;
	
	public function __construct($json = NULL) {
		
		//parent::__construct();
   		if (is_null($json)) {
			return;
		}

		$this->setValuesFromJsonObj($json);
	}
	
	
	 public function validateJsonObjforMSISDN($jsonObj) {
		 
        if (empty((array) $jsonObj)) {
            $res_string = 'Invalid JSON';
            return $res_string;
        }elseif(ctype_alpha($jsonObj['msisdn'])){
			$res_string = 'MSISDN : Enter Numeric Values only';
            return $res_string;
		}else{
            $MSISDNResponse = '';

           try {
                    if (isset($jsonObj['msisdn']) && trim($jsonObj['msisdn']) == '' || trim($jsonObj['msisdn']) == null) {
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

	public function getDetails( $function,$packageObj ) {
		
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
			$response = Message::ERROR_NO_DB_CONNECTION;
			$this->errorLog->LogError('PurchaseHistoryController'.json_encode($response));
    		return $response;
    	}
    	$dbConnection->beginTransaction();
    	$Contents = array();
		$purchaseHistoryDao = new PurchaseHistoryDao($dbConnection);
    	try {
			switch($function)
			{
				case 'getPurchaseHistory':
					$Contents = $purchaseHistoryDao->getPurchaseHistory( $packageObj );
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

?>