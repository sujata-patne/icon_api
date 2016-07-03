<?php
use VOP\Utils\PdoUtils;
require_once(APP."Daos/SubUnsubDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Utils/PdoUtils.php");

class subUnsub extends BaseModel {

    public function __construct($json = NULL) {
        if (is_null($json)) {
            return;
        }
    }
	
	public function validateJson( $jsonObj )
	{
		if (empty($jsonObj)) {
			$res_string = 'Invalid JSON';
			return $res_string;
		}else{
			$MSISDNResponse = '';
			$OperatorResponse = '';
			$Other1Response = '';
			$Other2Response = '';
			$UnitTypeResponse = '';
			
			try {
				if ((isset($jsonObj['msisdn'])) && trim($jsonObj['msisdn']) == '' || ($jsonObj['msisdn']) == null) {
						$PackageIdResponse = Message::ERROR_BLANK_MSISDN;
				}
				if ((isset($jsonObj['operator'])) && trim($jsonObj['operator']) == '' || ($jsonObj['operator']) == null) {
						$PackageIdResponse = Message::ERROR_BLANK_OPERATOR_NAME;
				}
				if ((isset($jsonObj['other1'])) && trim($jsonObj['other1']) == '' || ($jsonObj['other1']) == null) {
						$PackageIdResponse = Message::ERROR_BLANK_OTHER1;
				}
				if ((isset($jsonObj['other2'])) && trim($jsonObj['other2']) == '' || ($jsonObj['other2']) == null) {
						$PackageIdResponse = Message::ERROR_BLANK_OTHER2;
				}
				if ((isset($jsonObj['unitType'])) && trim($jsonObj['unitType']) == '' || ($jsonObj['unitType']) == null) {
						$PackageIdResponse = Message::ERROR_BLANK_UNITTYPE;
				}
				
					
				$res_string = $MSISDNResponse .$OperatorResponse .$Other1Response .$Other2Response .$UnitTypeResponse;
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
	
	public function getSubscriptionDetails( $function,$subUnsubObj ) {
		
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	$dbConnection->beginTransaction();
    	$Contents = array();
		$subUnsub = new SubUnsubDao($dbConnection );
    	try {
			switch($function)
			{
				case 'unsubscribe':
					$Contents = $subUnsub->unsubscribe( $subUnsubObj );
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
    