<?php
use VOP\Utils\PdoUtils;
require_once(APP."Daos/PackDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");


class Pack extends BaseModel {

    public $sp_pkg_id;
    public $sp_st_id;
    public $sp_dc_id;
    public $sp_pkg_type;
    public $sp_package_name;
    public $sp_package_desc;
    public $sp_parent_pkg_id;
    public $sp_pk_id;
    public $pk_name;
    public $pk_desc;
    public $pk_cnt_display_opt;
    public $cm_id;
    public $cm_content_type;
    public $cm_title;
    public $cm_vendor;
    public $cm_property_id;
    public $cd_name;
    public $cft_thumbnail_img_browse;
    public $cft_thumbnail_size;
    public $cf_url;
    public $cf_url_base;
    public $cf_template_id;
    public $cm_streaming_url;
    public $cm_downloading_url;

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
    
    public function getAllPacksByPackageIds( $packageIds, $portletIds, $storeId, $templateId ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	 
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	 
    	$dbConnection->beginTransaction();
    	 
    	$packageDetails = array();
    	 
    	try {
    		$pack = new PackDao($dbConnection);
    		$packageDetails = $pack->getAllPacksByPackageIds( $packageIds, $portletIds, $storeId, $templateId );
    		 
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		print_r( $e );exit;
    	}
    	 
    	PdoUtils::closeConnection($dbConnection);
    	return $packageDetails;
    }
}
?>
    