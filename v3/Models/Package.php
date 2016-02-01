<?php
use VOP\Utils\PdoUtils;
require_once(APP."Daos/PackageDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");

class Package extends BaseModel {
	
    public $portletId;
    public $sp_pkg_id;
    public $timestamp;
    public $promoid;
    public $contentTypeMD5;
    public $contentFileURLMD5;
    public $cft_thumbnail_img_browse;
    public $cft_thumbnail_size;
    public $cm_title;
    public $cm_genre;
    public $cd_id;
    public $cd_name;
    public $cf_url;
    public $cf_template_id;
    public $cf_cm_id;
    public $cm_streaming_url;
    public $cm_downloading_url;
    public $operatorId;
    public $cg_images;
    public $sp_jed_id;

    public function __construct($json = NULL) {
        if (is_null($json)) {
            return;
        }

        //$this->setValuesFromJson($json);
    }
    
    public function validateJson($jsonObj) {
    	$requiredProps = array('pageName', 'storeId');
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }

	public function validateJsonForPricePointByPackageId($jsonObj) {
		$requiredProps = array( 'packageId','operatorId' );
 		//echo "<pre>"; print_r($jsonObj); echo "<pre>"; print_r($requiredProps); 		exit;
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}
    public function validateJsonForPackageContent($jsonObj) {
    	$requiredProps = array( 'operatorId', 'packages' );
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }
    
    public function validateJsonForPackages( $packageData ) {
    	$requiredProps = array( 'packageId', 'contentType', 'limit' );

    	$message = $this->hasRequiredProperties($packageData, $requiredProps);
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
    
    public function getPortletsWithContentsByPackageIds( $packageId, $portletId, $vendorIds ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	
    	$dbConnection->beginTransaction();
    	
    	$packageDetails = array();
    	
    	try {
    		$package = new PackageDao($dbConnection);
    		$packageDetails = $package->getPortletsWithContentsByPackageIds( $packageId,$portletId, $vendorIds );
    	
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	
    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }
    
    public function getAllPacksByPackageIds( $packageIds ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$packageDetails = array();
    	 
    	try {
    		$package = new PackageDao($dbConnection);
    		$packageDetails = $package->getAllPacksByPackageIds( $packageIds );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }

	public function getSubscriptionPricePointsByPackageId( $packageId, $operatorId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');

    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}

    	$dbConnection->beginTransaction();

    	$packageDetails = array();

    	try {
    		$package = new PackageDao($dbConnection);
    		$packageDetails = $package->getSubscriptionPricePointsByPackageId( $packageId, $operatorId );

    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}

    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }
    
    public function getPackageContentsByIdByContentType( $packageId, $contentType, $limit ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$packageContents = array();
    	 
    	try {
    		$package = new PackageDao($dbConnection);
    		$packageContents = $package->getPackageContentsByIdByContentType( $packageId, $contentType, $limit );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $packageContents;
    }
    
    public function getPortletsContentsBySearchKey( $packageIds, $searchKey,$vendorIds ) {

		$dbConnection = PdoUtils::obtainConnection('CMS');
    
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    
    	$dbConnection->beginTransaction();
    
    	$packageContents = array();
    
    	try {
    		$package = new PackageDao($dbConnection);
    		$packageContents = $package->getPortletsContentsBySearchKey( $packageIds, $searchKey, $vendorIds );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    
    	PdoUtils::closeConnection($dbConnection);
    	return $packageContents;
    }
    
    public function getPackageContentsByPackageIdByContentId( $packageObj ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	
    	$dbConnection->beginTransaction();
    	
    	$packageContents = array();
    	
    	try {
    		$package  = new PackageDao($dbConnection );
 
    		$packageContents = $package->getPackageContentsByPackageIdByContentId( $packageObj );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	
    	PdoUtils::closeConnection($dbConnection);
    	return $packageContents;
    }
    
    public function getValuePackPlanDetailsByPackageIdByOperatorId( $packageObj, $operatorId ){
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$valuePackDetails = array();
    	 
    	try {
    		$package  = new PackageDao($dbConnection );
    	
    		$valuePackDetails = $package->getValuePackPlanDetailsByPackageIdByOperatorId( $packageObj, $operatorId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $valuePackDetails;
    }
    
    public function getSubscriptionPlanDetailsByPackageIdByOperatorId( $packageObj, $operatorId ){
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    
    	$dbConnection->beginTransaction();
    
    	$valuePackDetails = array();
    
    	try {
    		$package  = new PackageDao($dbConnection );
    		 
    		$valuePackDetails = $package->getSubscriptionPlanDetailsByPackageIdByOperatorId( $packageObj, $operatorId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    
    	PdoUtils::closeConnection($dbConnection);
    	return $valuePackDetails;
    }
    
    public function getAlacartaPlanDetailsByPackageIdByContentTypeByOperatorId( $packageObj, $operatorId ){
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    
    	$dbConnection->beginTransaction();
    
    	$valuePackDetails = array();
    
    	try {
    		$package  = new PackageDao($dbConnection );
    		 
    		$valuePackDetails = $package->getAlacartaPlanDetailsByPackageIdByContentTypeByOperatorId( $packageObj, $operatorId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    
    	PdoUtils::closeConnection($dbConnection);
    	return $valuePackDetails;
    }
    
    public function getVendorIdsByStoreId( $storeId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$vendors = array();
    	 
    	try {
    		$package = new PackageDao($dbConnection);
    		$vendors = $package->getVendorIdsByStoreId( $storeId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r($e->getMessage());
    		exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $vendors;
    }
}
?>
    