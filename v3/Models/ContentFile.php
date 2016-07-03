<?php
use VOP\Utils\PdoUtils;

require_once APP."Daos/ContentFileDao.php";
require_once APP.'Models/BaseModel.php';
require_once APP.'Models/Message.php';
require_once APP.'Utils/PdoUtils.php';

/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 12:17
 */
class ContentFile extends BaseModel {
    public $cf_id;
    public $cf_cm_id;
    public $cf_url_base;
    public $cf_url;
    public $cf_absolute_url;
    public $cf_original_processed;
    public $cf_template_id;
    public $cf_name;
    public $cf_name_alias;
    public $cf_created_on;
    public $cf_modified_on;
	public $cf_streaming_url;
    public $cf_downloading_url; 
    public $file_category_id;

    public function __construct() {
        $this->cf_id = '';
        $this->cf_cm_id = '';
        $this->cf_url_base = '';
        $this->cf_url = '';
        $this->cf_absolute_url = '';
        $this->cf_original_processed = '';
        $this->cf_template_id = '';
        $this->cf_name = '';
        $this->cf_name_alias = '';
        $this->cf_created_on = '';
        $this->cf_modified_on = '';
        $this->file_category_id = '';
        $this->cf_downloading_url = '';
        $this->cf_streaming_url = '';
    }

    public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);
        $this->unsetValues( array( 'created_on', 'created_by', 'updated_on', 'updated_by' ) );

        if (!$result) {
            return $result;
        }
        return true;
    }

    public function validateJsonForContentMetadata($jsonObj) {
        $requiredProps = array( 'cf_cm_id' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function validateJsonForUpdateInfo($jsonObj) {
        $requiredProps = array( 'cf_cm_id','cf_template_id', 'cf_name' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

    public function validateJsonForInsertInfo($jsonObj) {
        $requiredProps = array( 'cf_id', 'cf_cm_id', 'cf_url_base', 'cf_url','cf_template_id', 'cf_name', 'cf_name_alias' );

        $message = $this->hasRequiredProperties($jsonObj, $requiredProps);
        return $message;
    }

	public function validateJsonForUpdateURLInfo($jsonObj) {
    	$requiredProps = array( 'cf_id','cf_streaming_url', 'cf_downloading_url' );
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }
    
    public function updateContentFiles($data){
    
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	$dbConnection->beginTransaction();
    	$storeVendorDetails = array();
    	try {
    		$contents = new ContentFileDao($dbConnection);
    		$storeVendorDetails = $contents->updateContentFiles( $data );
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	PdoUtils::closeConnection($dbConnection);
    	return $storeVendorDetails;
    }

    public function validateJsonForTemplateHeightWidth( $jsonObj ){
    
    	if (empty($jsonObj)) {
    		$res_string = 'Invalid JSON';
    		return $res_string;
    	}else{
    		$HeightResponse = '';
    		$WidthResponse = '';
    			
    		try {
    			if ((isset($jsonObj['height'])) && trim($jsonObj['height']) == '' || ($jsonObj['height']) == null) {
    				$HeightResponse = Message::ERROR_BLANK_HEIGHT;
    			}
    
    			if ((isset($jsonObj['width'])) && trim($jsonObj['width']) == '' || ($jsonObj['width']) == null) {
    				$WidthResponse = Message::ERROR_BLANK_WIDTH;
    			}
    
    			$res_string = $HeightResponse .$WidthResponse;
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
    
    public function validateJsonForContentFilesExist( $jsonObj ){
    
    	if (empty($jsonObj)) {
    		$res_string = 'Invalid JSON';
    		return $res_string;
    	}else{
    		$cmdResponse = '';
    		$templateResponse = '';
    		$urlResponse = '';    		
    		$baseurlResponse = '';    		
    		$absoluteurlResponse = '';
    		
    		try {
    			if ((isset($jsonObj['cf_cm_id'])) && trim($jsonObj['cf_cm_id']) == '' || ($jsonObj['cf_cm_id']) == null) {
    				$cmdResponse = Message::ERROR_BLANK_CMD_ID;
    			}
    			if ((isset($jsonObj['cf_url'])) && trim($jsonObj['cf_url']) == '' || ($jsonObj['cf_url']) == null) {
    				$urlResponse = Message::ERROR_BLANK_CF_URL;
    			}
			/*if ((isset($jsonObj['cf_url_base'])) && trim($jsonObj['cf_url_base']) == '' || ($jsonObj['cf_url_base']) == null) {
    				$baseurlResponse = Message::ERROR_BLANK_CF_BASE_URL;
    			}
    			if ((isset($jsonObj['cf_absolute_url'])) && trim($jsonObj['cf_absolute_url']) == '' || ($jsonObj['cf_absolute_url']) == null) {
    				$absoluteurlResponse = Message::ERROR_BLANK_CF_ABSOLUTE_URL;
    			}*/
    			if ((isset($jsonObj['cf_template_id'])) && trim($jsonObj['cf_template_id']) == '' || ($jsonObj['cf_template_id']) == null) {
    				$templateResponse = Message::ERROR_BLANK_CF_TEMPLATE_ID;
    			}
    			$res_string = $cmdResponse .$baseurlResponse.$urlResponse .$absoluteurlResponse .$templateResponse;
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
	
	public function validateJsonForFileType($jsonObj) {
		
    	$requiredProps = array( 'fileType' );
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }
    
    public function isContentFileExist($data){
    
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	$dbConnection->beginTransaction();
    	try {
    		$contents = new ContentFileDao($dbConnection);
    		$isContentFileExist = $contents->isContentFileExist( $data );
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	PdoUtils::closeConnection($dbConnection);
    	return $isContentFileExist;
    }
    
    public function checkContentFileExistForMetadata($data){
    
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	$dbConnection->beginTransaction();
    	try {
    		$contents = new ContentFileDao($dbConnection);
    		$isContentFileExist = $contents->checkContentFileExistForMetadata( $data );
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	PdoUtils::closeConnection($dbConnection);
    	return $isContentFileExist;
    }
    public function insertContentFiles($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeVendorDetails = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $storeVendorDetails = $contents->insertContentFiles( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeVendorDetails;
    }

    public function getTemplateIdForLanguage($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $storeVendorDetails = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $storeVendorDetails = $contents->getTemplateIdForLanguage( $data );
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $storeVendorDetails;
    }

    public function getMaxCFId(){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxCFID = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $maxCFID = $contents->getMaxCFId();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxCFID;
    }

    public function getAllTemplates(){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $templates = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $templates = $contents->getAllTemplates();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $templates;
    }
    public function getTemplateIdForBitrate(){
        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $templates = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $templates = $contents->getTemplateIdForBitrate();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $templates;
    }
    
    public function getTemplateIdForHeightWidth($data){
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	$dbConnection->beginTransaction();
    	$templates = array();
    	try {
    		$contents = new ContentFileDao($dbConnection);
    		$templates = $contents->getTemplateIdForHeightWidth($data);
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	PdoUtils::closeConnection($dbConnection);
    	return $templates;
    }

    public function getMaxMetaContentId($data){

        $dbConnection = PdoUtils::obtainConnection('CMS');
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }
        $dbConnection->beginTransaction();
        $maxMCFID = array();
        try {
            $contents = new ContentFileDao($dbConnection);
            $maxMCFID = $contents->getMaxMetaContentId($data);
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            echo $e->getMessage();
            exit;
        }
        PdoUtils::closeConnection($dbConnection);
        return $maxMCFID;
    }
    public function getContentDeliveryTypesById( $cmdId ){
        $dbConnection = PdoUtils::obtainConnection('CMS');

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $contentMetaData = array();

        try {
            $contents = new ContentFileDao($dbConnection);
            $contentMetaData = $contents->getContentDeliveryTypesById( $cmdId );

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            print_r( $e );exit;
        }

        PdoUtils::closeConnection($dbConnection);
        return $contentMetaData;
    }
	
	public function getContentFiles($fileType){
        $dbConnection = PdoUtils::obtainConnection('CMS');

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $contentMetaData = array();

        try {
            $contents = new ContentFileDao($dbConnection);
            $contentMetaData = $contents->getContentFiles($fileType);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            print_r( $e );exit;
        }

        PdoUtils::closeConnection($dbConnection);
        return $contentMetaData;
    }
	
}