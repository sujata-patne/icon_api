<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 04/04/16
 * Time: 10:03 AM
 */

use VOP\Utils\PdoUtils;
require_once(APP."Daos/ContentDownloadDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Utils/PdoUtils.php");

class ContentDownload extends BaseModel {

    public function __construct($json = NULL) {
        if (is_null($json)) {
            return;
        }
    }
    public function validateJsonMSISDNEligibility($jsonObj) {

       if (empty($jsonObj)) {
            $res_string = 'Invalid JSON';
            return $res_string;
        }elseif(ctype_alpha($jsonObj->msisdn)){
			$res_string = 'MSISDN : Enter Numeric Values only';
            return $res_string;
		}elseif($jsonObj->eligibility !=1 && $jsonObj->eligibility !=0){
			$res_string = 'Eligibility : Enter 0 or 1 only';
            return $res_string;
		}else{
            $MSISDNResponse = '';
            $EligibilityResponse = '';

            try {
                    if (isset($jsonObj->msisdn) && trim($jsonObj->msisdn) == '' || trim($jsonObj->msisdn) == null) {
                        $MSISDNResponse = Message::ERROR_BLANK_MSISDN;
                    }

                    if (isset($jsonObj->eligibility) && trim( $jsonObj->eligibility ) == '' || trim( $jsonObj->eligibility ) == null) {
                        $EligibilityResponse = Message::ERROR_ELIGIBILITY_STATUS;
                    }

                    $res_string = $MSISDNResponse.$EligibilityResponse;
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
	
	
	
	public function validateJson($jsonObj){
		
		if (empty($jsonObj)) {
			
            $res_string = 'NOK| Invalid JSON';
            return $res_string;
        }else{
			
            $MSISDNResponse = '';
            $MetadataIdResponse = '';
            $CatalogueDetailIdResponse = '';
            $AppIdResponse = '';
            $PromoIdResponse = '';
            $PackageIdResponse = '';
            $PackIdResponse = '';
            $PlanIdResponse = '';
            $ContentFileIdResponse = '';
            $VendorIdResponse = '';

			 try {
                 	
					if (isset($jsonObj->msisdn) && trim($jsonObj->msisdn) == '' || trim($jsonObj->msisdn) == null) {
                        $MSISDNResponse = Message::ERROR_BLANK_MSISDN;
                    }
					
					if (isset($jsonObj->content_id) && trim($jsonObj->content_id) == '' || trim($jsonObj->content_id) == null) {
                        $CatalogueDetailIdResponse = Message::ERROR_BLANK_CATALOGUE_DETAIL_ID;
                    }
					
					if (isset($jsonObj->app_id) && trim($jsonObj->app_id) == '' || trim($jsonObj->app_id) == null) {
                        $AppIdResponse = Message::ERROR_BLANK_APP_ID;
                    }
					
					if (isset($jsonObj->promo_id) && trim($jsonObj->promo_id) == '' || trim($jsonObj->promo_id	) == null) {
                        $PromoIdResponse = Message::ERROR_BLANK_PROMO_ID;
                    }
					
					if($jsonObj->content_id != 10){
						
						if (isset($jsonObj->package_id) && trim($jsonObj->package_id) == '' || trim($jsonObj->package_id) == null) {
							$PackageIdResponse = Message::ERROR_BLANK_PACKAGE_ID;
						}
						
						if (isset($jsonObj->pack_id) && trim($jsonObj->pack_id) == '' || trim($jsonObj->pack_id) == null) {
							$PackIdResponse = Message::ERROR_BLANK_PACK_ID;
						}
						
						if (isset($jsonObj->plan_id) && trim($jsonObj->plan_id) == '' || trim($jsonObj->plan_id) == null) {
							$PlanIdResponse = Message::ERROR_BLANK_PLAN_ID;
						}	
						
						if (isset($jsonObj->metadata_id) && trim($jsonObj->metadata_id) == '' || trim($jsonObj->metadata_id) == null) {
							$MetadataIdResponse = Message::ERROR_BLANK_METADATA_ID;
						}
					
					}
					
					
					
					
					// if (isset($jsonObj->child_id) && trim($jsonObj->child_id) == '' || trim($jsonObj->child_id) == null) {
                        // $ContentFileIdResponse = Message::ERROR_BLANK_CONTENT_FILE_ID;
                    // }
					
					if (isset($jsonObj->vendor_id) && trim($jsonObj->vendor_id) == '' || trim($jsonObj->vendor_id) == null) {
                        $VendorIdResponse = Message::ERROR_VENDOR_ID;
                    }
					
                    $res_string = $MSISDNResponse.$MetadataIdResponse.$CatalogueDetailIdResponse.$AppIdResponse.$PromoIdResponse.$PackageIdResponse.$PackIdResponse.$PlanIdResponse.$ContentFileIdResponse.$VendorIdResponse;
					return $res_string;
            }
			
            catch(Exception $e)
            {
                $e->getMessage();
                $error_res_string = 'NOK| Exception #' .$e ;
                return $error_res_string;
            }
        }	
		
	}
	
	public function validateJsonForAll($jsonObj){
		
		if (empty($jsonObj)) {
            $res_string = 'NOK| Invalid JSON';
            return $res_string;
        }elseif(strlen($jsonObj->metadata_id) >0 && !is_numeric ($jsonObj->metadata_id)){
			$res_string = 'MetadaId : Enter Numeric Values only';
            return $res_string;
		}else{
			$MetadataIdResponse = '';
			
			try{
				if (isset($jsonObj->metadata_id) && trim($jsonObj->metadata_id) == '' || trim($jsonObj->metadata_id) == null || strlen($jsonObj->metadata_id) == 0) {
					$MetadataIdResponse = Message::ERROR_BLANK_METADATA_ID;
				}	
				
				$res_string = $MetadataIdResponse;
				return $res_string;
				
			}catch(Exception $e)
            {
                $e->getMessage();
                $error_res_string = 'NOK| Exception #' .$e ;
                return $error_res_string;
            }
		}	
	}
	
	
	
	
    public function validateJsonMSISDN($jsonObj) {
	
        if (empty($jsonObj)) {
            $res_string = 'Invalid JSON';
            return $res_string;
        }elseif(ctype_alpha($jsonObj->msisdn)){
			$res_string = 'MSISDN : Enter Numeric Values only';
            return $res_string;
		}else{
            $MSISDNResponse = '';

           try {
                    if (isset($jsonObj->msisdn) && trim($jsonObj->msisdn) == '' || trim($jsonObj->msisdn) == null) {
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
    public function validateJsonMSISDNAppId($jsonObj) {

        if (empty($jsonObj)) {
            $res_string = 'Invalid JSON';
            return $res_string;
        }elseif(ctype_alpha($jsonObj->msisdn)){
			$res_string = 'MSISDN : Enter Numeric Values only';
            return $res_string;
		}elseif(ctype_alpha($jsonObj->appId)){
			$res_string = 'AppId : Enter Numeric Values only';
            return $res_string;
		}else{
            $MSISDNResponse = '';
            $AppIDResponse = '';

			
			 try {
                    if (isset($jsonObj->msisdn) && trim($jsonObj->msisdn) == '' || trim($jsonObj->msisdn) == null) {
                        $MSISDNResponse = Message::ERROR_BLANK_MSISDN;
                    }

                    if (trim( $jsonObj->appId ) == '') {
                        $AppIDResponse = Message::ERROR_BLANK_APP_ID;
                    }

                    $res_string = $MSISDNResponse.$AppIDResponse;
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
	
	
	public function validateJsonCatalogueDetailIDMetadataID( $jsonObj ){
		
		if (empty($jsonObj)) {
            $res_string = 'NOK| Invalid JSON';
            return $res_string;
        }else{
            $ContentIdResponse = '';
            $ContentMetadataIdResponse = '';

            try {
                if (isset($jsonObj->cd_id) && isset($jsonObj->cm_id))
                {
                    if (trim($jsonObj->cd_id) == '' || trim($jsonObj->cd_id) == null) {
                        $ContentIdResponse = Message::ERROR_BLANK_CONTENT_ID;
                    }

                    if (trim( $jsonObj->cm_id ) == '' || trim($jsonObj->cm_id) == null) {
                        $ContentMetadataIdResponse = Message::ERROR_BLANK_CATALOGUE_DETAIL_ID;
                    }

                    $res_string = 'NOK| Parameter values Missing#' . ' # ' . $ContentIdResponse . ' # ' . $ContentMetadataIdResponse . ' # ' ;
                }
                $res_string = 'OK| '. ' # ' . $ContentIdResponse . ' # ' . $ContentMetadataIdResponse . ' # ' ;
                return $res_string;
            }
            catch(Exception $e)
            {
                $e->getMessage();
                $error_res_string = 'NOK| Exception #' .$e ;
                return $error_res_string;
            }
        }

	}
	
    public function getDownloads( $function,$downloadObj ) {

        $dbConnection = PdoUtils::obtainConnection('CMS');

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $Contents = array();
        $contentDownloads = new ContentDownloadDao($dbConnection);

        try {

            switch($function)
            {
                case 'getDownloadsWithUserAuth':
                    $Contents = $contentDownloads->getDownloadsWithUserAuth( $downloadObj );
                    break;

                case 'getPlanHistory':
                    $Contents = $contentDownloads->getPlanHistory( $downloadObj );
                    break;

                case 'getUserDownloadHistoryByMSISDNByAppID':
                    $Contents = $contentDownloads->getUserDownloadHistoryByMSISDNByAppID( $downloadObj );
                    break;
					
				case 'getContentDownloads':
                    $Contents = $contentDownloads->getContentDownloads( $downloadObj );
                    break;	
					
				case 'setContentDownload':
                    $Contents = $contentDownloads->setContentDownload( $downloadObj );
                    break;	
					
				case 'getSmilURL':
                    $Contents = $contentDownloads->getSmilURL( $downloadObj );
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
	
	
	
	public function getContentDownloadPath( $data ){
		
		$dbConnection = PdoUtils::obtainConnection('CMS');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$ContentDownloadPath = null;

		try {
			
			$contentDownloads 		= new ContentDownloadDao($dbConnection);
			$ContentDownloadPath    = $contentDownloads->getContentDownloadPath( $data );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $ContentDownloadPath;
	}
	
	public function getContentDownloadPathForAll( $data ){
		
		$dbConnection = PdoUtils::obtainConnection('CMS');

		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}

		$dbConnection->beginTransaction();

		$ContentDownloadPath = null;

		try {
			
			$contentDownloads 		= new ContentDownloadDao($dbConnection);
			$ContentDownloadPath    = $contentDownloads->getContentDownloadPathForAll( $data );

			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}

		PdoUtils::closeConnection($dbConnection);
		return $ContentDownloadPath;
	}
	
	
	
	
}