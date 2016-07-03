<?php

use VOP\Daos\BaseDao;
require_once(APP."Models/PurchaseHistory.php");
require_once(APP."Daos/BaseDao.php");

class PurchaseHistoryDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}
	
	public function getPurchaseHistory( $data ){
		
		$query = "SELECT DISTINCT bd.subscription_date as SubscriptionDate,bd.content_type as ContentType,cf.cf_cm_id as MetadataId,cf.cf_id as TuneId,SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cd1.cd_name as Language,cmd1.cm_display_title as DialogueTitle,cmd.cm_title as AlbumName,COALESCE(cd2.cd_id,'NA') as CelebId,cd.cd_id as MoodId,cmd1.cm_release_date as ReleaseDate,cd1.cd_id as LanguageId,cft.cft_thumbnail_img_browse as ThumbnailPath,cft.cft_thumbnail_size as ThumbnailSize,vo.vcode as VCODE,COALESCE(ivr_promocode, sms_promocode, 'NA') as PromoCode FROM content_metadata AS cmd
								INNER JOIN content_metadata AS cmd1 ON (cmd1.cm_property_id = cmd.cm_id)
								LEFT JOIN content_files cf ON cf.cf_cm_id = cmd1.cm_id
								LEFT JOIN catalogue_detail cd ON cd.cd_id = cmd1.cm_genre 
								LEFT JOIN multiselect_metadata_detail mmd ON mmd.cmd_group_id = cmd1.cm_language
								LEFT JOIN catalogue_detail cd1 ON cd1.cd_id = mmd.cmd_entity_detail
								INNER JOIN multiselect_metadata_detail mmd1 ON mmd1.cmd_group_id = cmd1.cm_celebrity
								INNER JOIN catalogue_detail cd2 ON cd2.cd_id = mmd1.cmd_entity_detail
								LEFT JOIN vcode_operator vo ON vo.content_file_cf_id = cf.cf_id
								LEFT JOIN operator_country oc ON oc.id = vo.operator_country_id
								LEFT JOIN content_files cf1 on cf1.cf_cm_id = cf.cf_cm_id and cf1.cf_name IS NULL
								LEFT JOIN content_files_thumbnail cft on cft.cft_cm_id = cf.cf_cm_id
								LEFT JOIN content_files_thumbnail cft1 on cft1.cft_cm_id = cf1.cf_cm_id
								INNER JOIN billing_details bd on bd.content_id = cf.cf_id
								WHERE bd.msisdn = :msisdn
								AND cmd1.cm_state = 4
								AND cf.cf_original_processed = 1
								GROUP BY cf.cf_id
								ORDER BY cf.cf_name";

		$PurchaseHistory = array();
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':msisdn',  $data->msisdn );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			
			$thumbnailpath = '';
			
			if($row['ThumbnailPath'] != '' || $row['ThumbnailPath'] != NULL){
				
				$size = explode("*",$row['ThumbnailSize']);
				$ThumbNails = explode(",",$row['ThumbnailPath']);
				$ext = pathinfo($ThumbNails[0], PATHINFO_EXTENSION);
				$thumbUrl = $row['MetadataId'].'_thumb_'.$size[0].'_'.$size[1].'.'.$ext;
				$timestamp = strtotime(date('YmdHis'));
				$ThumbnailWidth = $size[0];
				$ThumbnailHeight = $size[1];
									
				$thumbnailpath = 'http://d85mhbly9q6nd.cloudfront.net/'.$thumbUrl.'?'.$timestamp;
									
			}

			$history['SubscriptionDate']  	= $row['SubscriptionDate'];
			$history['ContentType'] 		= $row['ContentType'];
			$history['MetadataId'] 	        = $row['MetadataId'];
			$history['TuneId'] 				= $row['TuneId'];
			$history['TuneName'] 			= $row['TuneName'];
			$history['Language']		 	= $row['Language'];
			$history['DialogueTitle'] 		= $row['DialogueTitle'];
			$history['AlbumName'] 			= $row['AlbumName'];
			$history['CelebId']	            = $row['CelebId'];
			$history['MoodId']		        = $row['MoodId'];
			$history['ReleaseDate'] 		= $row['ReleaseDate'];
			$history['LanguageId'] 		    = $row['LanguageId'];
			$history['ThumbnailPath'] 		= $thumbnailpath != '' ? $thumbnailpath : 'NA';
			$history['VCODE'] 	            = $row['VCODE'];
			$history['PromoCode'] 	        = $row['PromoCode'];

			$PurchaseHistory[] = $history;
		}
		
		return $PurchaseHistory;	
	}


}


?>