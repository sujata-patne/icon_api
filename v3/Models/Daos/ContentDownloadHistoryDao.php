<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/ContentDownloadHistory.php");
require_once(APP."Daos/BaseDao.php");

class ContentDownloadHistoryDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}
	
	private function contentDownloadHistoryFromRow($row) {
		$contentDlownloadHistory = new ContentDownloadHistory();
		
		$contentDlownloadHistory->cd_id 	   	  = $row['cd_id'];
		$contentDlownloadHistory->cd_user_id   		  = $row['cd_user_id'];
		$contentDlownloadHistory->cd_msisdn    		  = $row['cd_msisdn'];
		$contentDlownloadHistory->cd_cmd_id    		  = $row['cd_cmd_id'];
		$contentDlownloadHistory->cd_download_count       = $row['cd_download_count'];
		$contentDlownloadHistory->cd_cd_id     		  = $row['cd_cd_id'];
		$contentDlownloadHistory->cd_app_id    		  = $row['cd_app_id'];
		$contentDlownloadHistory->cd_download_date        = $row['cd_download_date'];
		
		$contentDlownloadHistory->unsetValues(array( 'created_on', 'created_by', 'updated_on', 'updated_by', 'storeId' ));
		
		return $contentDlownloadHistory;
	}
	
	public function getContentDownloadHistoryByMsisdnByUserIdByAppId( $contentDownloadHistoryObj ){
	   			
		$query = "SELECT 
					  * 
				  FROM 
					  content_download 
				  WHERE 
					  cd_msisdn = :cd_msisdn AND 
					  cd_user_id = :cd_user_id AND 
					  cd_app_id = :cd_app_id 
				  ORDER BY
					  cd_download_date desc";

		$contentDownloadHistory = array();
		
		$statement = $this->dbConnection->prepare($query);
		
		$statement->bindParam( ':cd_msisdn',  $contentDownloadHistoryObj->cd_msisdn );
		$statement->bindParam( ':cd_user_id', $contentDownloadHistoryObj->cd_user_id );
		$statement->bindParam( ':cd_app_id',  $contentDownloadHistoryObj->cd_app_id );
		
		
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$contentDownloadHistory = array();
		
		while($row = $statement->fetch()) {
			$contentDownloadHistory[] = $this->contentDownloadHistoryFromRow($row);
		}
		return $contentDownloadHistory;

	}
	
	public function getContentDetailsByCatelogDetailIdsAndContentMetadataIds( $cdIds, $cmdIds ){
		
		$query = "SELECT 
					 cd.cd_id, 
					 cm.cm_id,
					 cm.cm_vendor,
					 cm.cm_content_type,
					 cd.cd_name, 
					 cd1.cd_name as cm_genre,
					 cm.cm_title,
					 cm.cm_streaming_url,
					 cm.cm_downloading_url
				  FROM 
					content_metadata cm
				  JOIN
					catalogue_detail cd ON ( cm.cm_content_type =  cd.cd_id )
				  JOIN
					catalogue_detail AS cd1 ON ( cd1.cd_id = cm.cm_genre )
				  WHERE
					 FIND_IN_SET( cd.cd_id, :cdIds ) AND
				     FIND_IN_SET( cm.cm_id, :cmdIds ) AND 
				     cm.cm_property_id IS NOT NULL" ;
		
		$contentDownloadHistory = array();
		
		$statement = $this->dbConnection->prepare($query);
		
		$statement->bindParam( ':cdIds',  $cdIds );
		$statement->bindParam( ':cmdIds', $cmdIds ); 
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$contentDownloadHistory = array();
		
		while($row = $statement->fetch()) {
			$contentDownloadHistory[] = $row;
		}
		
		return $contentDownloadHistory;
		 
	}
	public function getContentMetaDataById( $cmdId ){
		
		$query = "SELECT 
					* 
				  FROM 
				  	content_metadata 
				  WHERE 
				  	cm_id = :cmdId AND 
				  	cm_property_id IS NOT NULL" ;
		
		
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':cmdId',  $cmdId ); 
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$contentMetaData = null;
		
		if (($row = $statement->fetch()) != FALSE) {
            $contentMetaData = $row;
        }
		
		return $contentMetaData;
		 
	}
	public function getCatalogueDetailById( $cdId ){
		
		$query = "SELECT 
					cd_name
				  FROM 
				  	catalogue_detail
				  WHERE 
				  	cd_id = :cdId 
				  LIMIT 1" ;
		
		
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':cdId',  $cdId ); 
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$catalogueDetail = null;
		
		if (($row = $statement->fetch()) != FALSE) {
            $catalogueDetail = $row;
        }
		
		return $catalogueDetail;
		 
	}
}
?>

