<?php

use VOP\Utils\PdoUtils;

require_once(APP."Daos/CampaignDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");

class Campaign extends BaseModel {

	public $promoId;
	public $storeId;
	public $cGFlag;
	public $campaignVendor;
	public $campaignName;
	

	public function __construct($json = NULL) {
		if (is_null($json)) {
			return;
		}

		$this->setValuesFromJsonObj($json);
	}
	
	public function validateJson($jsonObj) {
		$requiredProps = array('promoId' );
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJsonObj($jsonObj) {
		$requiredProps = array('promoId', 'storeId' );
	
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
	
	public function getCampaignDetailsByPromoId( $promoId ){
		$dbConnection = PdoUtils::obtainConnection('CAMPAIGN');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
	
		$dbConnection->beginTransaction();
		
		$campaign = null;
		
		try {
			$campaignDao = new CampaignDao($dbConnection);
			$campaign = $campaignDao->getCampaignDetailsByPromoId( $promoId );
		
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $campaign;
	}
	
	public function getCampaignDetailsByPromoIdByStoreId( $promoId, $storeId ) {
		$dbConnection = PdoUtils::obtainConnection('CAMPAIGN');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$campaign = null;
		
		try {
			$campaignDao = new CampaignDao($dbConnection);
			$campaign = $campaignDao->getCampaignDetailsByPromoIdByStoreId( $promoId, $storeId );
		
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $campaign;
	}
}
?>