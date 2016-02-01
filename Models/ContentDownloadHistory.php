<?php

use VOP\Utils\PdoUtils;

require_once(APP."Daos/ContentDownloadHistoryDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");

class ContentDownloadHistory extends BaseModel {

	public $cd_id;
	public $cd_user_id;
	public $cd_msisdn;
	public $cd_cmd_id;
	public $cd_download_count;
	public $cd_cd_id;
	public $cd_app_id;
	public $cd_download_date;
	public $storeId;	

	public function __construct($json = NULL) {
		if (is_null($json)) {
			return;
		}

		$this->setValuesFromJsonObj($json);
	}

	public function validateJsonForUpdateDownloadInfo($jsonObj) {
		$requiredProps = array( 'cd_msisdn', 'cd_user_id', 'cd_app_id', 'cd_cmd_id', 'cd_cd_id','cd_download_count','cd_download_date' );

		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}

	public function validateJsonForCheckDownloadInfo($jsonObj) {
		$requiredProps = array( 'cd_msisdn', 'cd_user_id', 'cd_app_id', 'cd_cmd_id', 'cd_cd_id' );

		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJson($jsonObj) {
		$requiredProps = array( 'cd_msisdn', 'cd_user_id', 'cd_app_id' );
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	
	public function validateJsonObj($jsonObj) {
		$requiredProps = array( 'cd_cmd_id' );
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
	public function validateJsonForCatalogueDetail($jsonObj) {
		$requiredProps = array( 'cd_cd_id' );
	
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
	
	public function getContentDownloadHistoryByMsisdnByUserIdByAppId( $contentDownloadHistoryObj ){
		$dbConnection = PdoUtils::obtainConnection('SITE_USER');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
	
		$dbConnection->beginTransaction();
		
		$contentDownloadHistory = null;
		
		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentDownloadHistory = $contentDownloadHistoryDao->getContentDownloadHistoryByMsisdnByUserIdByAppId( $contentDownloadHistoryObj );
		
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $contentDownloadHistory;
	}
	
	public function getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds ) {
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$contentDownloadHistory = array();
		
		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentDownloadHistory = $contentDownloadHistoryDao->getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds );
			
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $contentDownloadHistory;
	}

	public function getContentMetaDataById( $cmdId ){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$contentMetaData = array();
		
		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentMetaData = $contentDownloadHistoryDao->getContentMetaDataById( $cmdId );
			
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $contentMetaData;
	}

	public function getCatalogueDetailById( $cdId ){
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$catalogueDetail = array();
		
		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$catalogueDetail = $contentDownloadHistoryDao->getCatalogueDetailById( $cdId );
			
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $catalogueDetail;
	}

	public function checkDownloadInfo( $data ){
		$dbConnection = PdoUtils::obtainConnection('SITE_USER');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$contentDownloadHistory = null;

		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentDownloadHistory = $contentDownloadHistoryDao->checkDownloadInfo( $data );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $contentDownloadHistory;
	}
	public function updateDownloadInfo( $data ){
		$dbConnection = PdoUtils::obtainConnection('SITE_USER');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$contentDownloadHistory = null;

		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentDownloadHistory = $contentDownloadHistoryDao->updateDownloadInfo( $data );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $contentDownloadHistory;
	}

	public function insertDownloadInfo( $data ){
		$dbConnection = PdoUtils::obtainConnection('SITE_USER');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$contentDownloadHistory = null;

		try {
			$contentDownloadHistoryDao = new ContentDownloadHistoryDao($dbConnection);
			$contentDownloadHistory = $contentDownloadHistoryDao->insertDownloadInfo( $data );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $contentDownloadHistory;
	}
}
?>