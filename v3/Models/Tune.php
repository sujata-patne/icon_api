<?php

use VOP\Utils\PdoUtils;

require_once(APP."Utils/PdoUtils.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Daos/TuneDao.php");
require_once(APP."Models/Message.php");


class Tune extends BaseModel {
	
	public $UserName;
	public $Operator;
	public $CelebrityName;
	public $cf_id;
	
	public function __construct($json = NULL) {
		if (is_null($json)) {
			return;
		}

		$this->setValuesFromJsonObj($json);
	}
	
	public function validateJsonObj($jsonObj) {
		$requiredProps = array( 'UserName','Operator');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJsonObjForCelebrity($jsonObj) {
		$requiredProps = array( 'UserName','CelebrityName');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJsonObjForName($jsonObj) {
		$requiredProps = array( 'UserName');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJsonObjForAll($jsonObj){
		$requiredProps = array( 'UserName','CelebrityName','Operator');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	
	public function validateJsonObjForTuneID($jsonObj) {
		$requiredProps = array( 'TuneID');
	
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
	
	public function getTunesByOperator($data){
		
		$dbConnection = PdoUtils::obtainConnection('CMS');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$TunesByOperator = array();
		
		try {
			$tuneDao = new TuneDao($dbConnection);
			$TunesByOperator = $tuneDao->getTunesByOperator($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $TunesByOperator;
	}
	
	
	public function getTunesByCelebrity($data){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$TunesByOperator = array();
		
		try {
			$tuneDao = new TuneDao($dbConnection);
			$TunesByCelebrity = $tuneDao->getTunesByCelebrity($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $TunesByCelebrity;
	}
	
	
	public function getTunesByName($data){
		
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$TunesByOperator = array();
		
		try {
			$tuneDao = new TuneDao($dbConnection);
			$TunesByName = $tuneDao->getTunesByName($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $TunesByName;
	}
	
	public function getTunesByUsernameOperatorCelebrity($data){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$TuneDetails = array();
		
		try {
			$tuneDao = new TuneDao($dbConnection);
			$TuneDetails = $tuneDao->getTunesByUsernameOperatorCelebrity($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $TuneDetails;
		
	} 
	
	
	
	public function getTuneID($data){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$TuneID = array();
		
		try {
			$tuneDao = new TuneDao($dbConnection);
			$TuneID = $tuneDao->getTuneID($data);
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $TuneID;
	}
	

}

?>