<?php

use VOP\Utils\PdoUtils;

require_once(APP."Utils/PdoUtils.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Daos/LoggerDao.php");
require_once(APP."Models/Message.php");


class Logger extends BaseModel {
	
	public function __construct($json = NULL) {
		if (is_null($json)) {
			return;
		}

		//$this->setValuesFromJsonObj($json);
	}
	/*public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);

        if (!$result) {
            return $result;
        }
        return true;
    }
	
	
	public function validateJsonObj($jsonObj) {
		$requiredProps = array( 'msisdn');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	
	public function CreateTuneLog($data){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$CreateTuneLog = array();
		
		try {
			$tunelogDao = new TuneLogDao($dbConnection);
			$CreateTuneLog = $tunelogDao->CreateTuneLog($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $CreateTuneLog;
	}*/
	
	
	
	
}