<?php

use VOP\Utils\PdoUtils;
require_once(APP."Daos/PageDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");

class Page extends BaseModel {

	public $pageName;
	public $storeId;
    public $packageId;
    public $vendorIds;
    public $portletId;
    public $portletMapId;
    public $token;
	public $deviceHeight;
	public $deviceWidth;
	public $searchKey;
	public $pageId;
	public $pageTitle;
	public $subscriptionPlan;
	public $pas_arrange_seq;
	public $pricePoint;
	public $singleDayLimit;
	public $fullSubDownloadLimit;
	public $fullSubStreamContentLimit;
	public $fullSubStreamDurationLimit;
	public $fullSubStreamDurationTypeId;
	public $fullSubStreamDurationTypeName;

    public function __construct($json = NULL) {
    	$this->pageName = '';
    	$this->storeId = '';
    	$this->packageId = '';
    	$this->portletId = '';
    	$this->portletMapId = '';
    	$this->token = '';
    	$this->deviceHeight = '';
    	$this->deviceWidth = '';
    	$this->searchKey = '';
    	$this->pageTitle = '';
    	$this->pageId = '';
    	$this->pricePoint = '';
    	$this->subscriptionPlan = '';
    	$this->pas_arrange_seq = '';
		$this->singleDayLimit  = '';
		$this->fullSubDownloadLimit = '';
		$this->fullSubStreamContentLimit = '';
		$this->fullSubStreamDurationLimit = '';
		$this->fullSubStreamDurationTypeId = '';
		$this->fullSubStreamDurationTypeName = '';

        if (is_null($json)) {
            return;
        }

        //$this->setValuesFromJson($json);
    }
    
    public function validateJson($jsonObj) {
    	$requiredProps = array('pageName', 'storeId', 'deviceHeight', 'deviceWidth' );

    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }

	public function validateJsonPageObj($jsonObj) {
		$requiredProps = array( 'pageTitle' );

		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}

    public function validateJsonObj($jsonObj) {
    	$requiredProps = array( 'pageId' );
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }
    
    public function validateJsonForSearch($jsonObj) {
    	$requiredProps = array('storeId', 'searchKey' );
    
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

    public  function generateUserId() {

        $udid = UuidUtils::uuid();
        $newUuid = str_replace('-', '', $udid);

        return $newUuid;
    }
    
    public function getPackageIdsByPageName( $pageName, $storeId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	
    	$dbConnection->beginTransaction();
    	
    	$pageDetails = array();
    	
    	try {
    		$page = new PageDao($dbConnection);
    		$pageDetails = $page->getPackageIdsByPageName( $pageName, $storeId );
    	
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	
    	PdoUtils::closeConnection($dbConnection);
    	return $pageDetails;
    }
	public function getPageContents( $pageTitle ) {
		$dbConnection = PdoUtils::obtainConnection('CMS');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$pageDetails = array();

		try {
			$page = new PageDao($dbConnection);
			$pageDetails = $page->getPageContents( $pageTitle );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			echo $e->getMessage();
			exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $pageDetails;
	}

    public function getPackageIdsByPageId( $pageId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$pageDetails = array();
    	 
    	try {
    		$page = new PageDao($dbConnection);
    		$pageDetails = $page->getPackageIdsByPageId( $pageId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $pageDetails;
    }
    
    public function getPackageIdsByStoreId( $storeId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	
    	$dbConnection->beginTransaction();
    	
    	$packageDetails = array();
    	
    	try {
    		$page = new PageDao($dbConnection);
    		$packageDetails = $page->getPackageIdsByStoreId( $storeId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	
    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }
    
    public function getMainSitePackageIdsByStoreId( $storeId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$packageDetails = array();
    	 
    	try {
    		$page = new PageDao($dbConnection);
    		$packageDetails = $page->getMainSitePackageIdsByStoreId( $storeId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }
    
}
?>
    
