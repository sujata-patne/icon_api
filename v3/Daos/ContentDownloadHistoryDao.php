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
		if( !empty( $row['cd_id'] ) ) {
			$contentDlownloadHistory->cd_id = $row['cd_id'];
		}
		if( !empty( $row['cd_user_id'] ) ) {
			$contentDlownloadHistory->cd_user_id = $row['cd_user_id'];
		}
		if( !empty( $row['cd_msisdn'] ) ) {
			$contentDlownloadHistory->cd_msisdn = $row['cd_msisdn'];
		}
		if( !empty( $row['cd_cmd_id'] ) ) {
			$contentDlownloadHistory->cd_cmd_id = $row['cd_cmd_id'];
		}
		if( !empty( $row['cd_download_count'] ) ) {
			$contentDlownloadHistory->cd_download_count = $row['cd_download_count'];
		}
		if( !empty( $row['cd_cd_id'] ) ) {
			$contentDlownloadHistory->cd_cd_id = $row['cd_cd_id'];
		}
		if( !empty( $row['$row'] ) ) {
			$contentDlownloadHistory->cd_app_id = $row['cd_app_id'];
		}
		if( !empty( $row['downloadCount'] ) ) {
			$contentDlownloadHistory->downloadCount	= $row['downloadCount'];
		}
		if( !empty( $row['sub_start_date'] ) ) {
			$contentDlownloadHistory->sub_start_date	= $row['sub_start_date'];
		}
		if( !empty( $row['singleDayLimit'] ) ) {
			$contentDlownloadHistory->singleDayLimit	= $row['singleDayLimit'];
		}
		$contentDlownloadHistory->unsetValues(array( 'created_on', 'created_by', 'updated_on', 'updated_by', 'storeId' ));
		
		return $contentDlownloadHistory;
	}

	public function checkDownloadInfo( $contentDownloadHistoryObj ){
		/*if($contentDownloadHistoryObj->singleDayLimit == 1){
			$where = " AND DATE(cd_download_date) = DATE(NOW()) AND cd_download_date > '".$contentDownloadHistoryObj->sub_start_date."'";
		}else{
			$where = " AND cd_download_date BETWEEN '".$contentDownloadHistoryObj->sub_start_date."' AND NOW() ";
		}*/

		$query1 = "SELECT sum(cd_download_count) AS downloadCount FROM content_download
			  WHERE cd_msisdn = ".$contentDownloadHistoryObj->cd_msisdn." AND
				  cd_user_id = ".$contentDownloadHistoryObj->cd_user_id." AND
				  cd_app_id = ".$contentDownloadHistoryObj->cd_app_id ." AND
				  cd_download_date BETWEEN '".$contentDownloadHistoryObj->sub_start_date."' AND NOW()
			  ORDER BY cd_download_date desc";

		$statement = $this->dbConnection->prepare($query1);

		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);

		$contentDownloadHistory = array();
		while($row = $statement->fetch()) {
			$contentDownloadHistory['fullSubscriptionDownloadLimit'] = $row['downloadCount'];
		}
		$query2 = "SELECT sum(cd_download_count) AS downloadCount FROM content_download
			  WHERE cd_msisdn = ".$contentDownloadHistoryObj->cd_msisdn." AND
				  cd_user_id = ".$contentDownloadHistoryObj->cd_user_id." AND
				  cd_app_id = ".$contentDownloadHistoryObj->cd_app_id ." AND
				  DATE(cd_download_date) = DATE(NOW()) AND cd_download_date > '".$contentDownloadHistoryObj->sub_start_date."'
			  ORDER BY cd_download_date desc";

		$statement = $this->dbConnection->prepare($query2);

		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);

 		while($row = $statement->fetch()) {
			$contentDownloadHistory['singleDayDownloadLimit'] = $row['downloadCount'];
		}
 		return $contentDownloadHistory;
	}

	public function updateDownloadInfo( $contentDownloadHistoryObj ){
		$query = "update content_download
				  SET cd_download_count = :cd_download_count,
				  cd_download_date = :cd_download_date
				  WHERE
					  cd_msisdn = :cd_msisdn AND
					  cd_user_id = :cd_user_id AND
					  cd_app_id = :cd_app_id AND
					  cd_cmd_id = :cd_cmd_id AND
					  cd_cd_id = :cd_cd_id ";

		$statement = $this->dbConnection->prepare($query);

		$statement->bindParam( ':cd_msisdn',  $contentDownloadHistoryObj->cd_msisdn );
		$statement->bindParam( ':cd_user_id', $contentDownloadHistoryObj->cd_user_id );
		$statement->bindParam( ':cd_app_id',  $contentDownloadHistoryObj->cd_app_id );
		$statement->bindParam( ':cd_cmd_id',  $contentDownloadHistoryObj->cd_cmd_id );
		$statement->bindParam( ':cd_cd_id',  $contentDownloadHistoryObj->cd_cd_id );
		$statement->bindParam( ':cd_download_date',  $contentDownloadHistoryObj->cd_download_date );
		$statement->bindParam( ':cd_download_count',  $contentDownloadHistoryObj->cd_download_count );

		$result = $statement->execute();

		return $result;
	}

	public function insertDownloadInfo( $contentDownloadHistoryObj ){
		$query = "insert into content_download
				  SET cd_msisdn = :cd_msisdn,
					  cd_user_id = :cd_user_id,
					  cd_app_id = :cd_app_id,
					  cd_cmd_id = :cd_cmd_id,
					  cd_cd_id = :cd_cd_id,
				  cd_download_count = :cd_download_count,
				  cd_download_date = :cd_download_date";

		$statement = $this->dbConnection->prepare($query);

		$statement->bindParam( ':cd_msisdn',  $contentDownloadHistoryObj->cd_msisdn );
		$statement->bindParam( ':cd_user_id', $contentDownloadHistoryObj->cd_user_id );
		$statement->bindParam( ':cd_app_id',  $contentDownloadHistoryObj->cd_app_id );
		$statement->bindParam( ':cd_cmd_id',  $contentDownloadHistoryObj->cd_cmd_id );
		$statement->bindParam( ':cd_cd_id',  $contentDownloadHistoryObj->cd_cd_id );
		$statement->bindParam( ':cd_download_date',  $contentDownloadHistoryObj->cd_download_date );
		$statement->bindParam( ':cd_download_count',  $contentDownloadHistoryObj->cd_download_count );

		$result = $statement->execute();

		return $result;
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
					 parent.cd_name as parent_content_type,
					 cm.cm_id,
					 cm.cm_vendor,
					 cm.cm_content_type,
					 cd.cd_name, 
					 cd1.cd_name as cm_genre,
					 cm.cm_title,
					 cm.cm_streaming_url,
					 cm.cm_downloading_url,
					 cft.cft_thumbnail_img_browse
				  FROM content_metadata AS cm
				  JOIN catalogue_detail AS cd ON ( cm.cm_content_type =  cd.cd_id )
				  JOIN icn_manage_content_type AS mct ON ( mct.mct_cnt_type_id =  cd.cd_id )
				  JOIN catalogue_detail AS parent ON ( mct.mct_parent_cnt_type_id =  parent.cd_id )
				  JOIN catalogue_detail AS cd1 ON ( cd1.cd_id = cm.cm_genre )
				  JOIN content_files_thumbnail AS cft ON ( cft.cft_cm_id = cm.cm_id )
				  WHERE
					 FIND_IN_SET( cd.cd_id, :cdIds ) AND
				     FIND_IN_SET( cm.cm_id, :cmdIds ) AND
				     cft.cft_thumbnail_size = '125*125' AND
				     cm.cm_property_id IS NOT NULL" ;
		
		$contentDownloadHistory = array();
		
		$statement = $this->dbConnection->prepare($query);
		
		$statement->bindParam( ':cdIds',  $cdIds );
		$statement->bindParam( ':cmdIds', $cmdIds ); 
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);

		while($row = $statement->fetch()) {
			$contentDownloadHistory[] = $row;
		}
		
		return $contentDownloadHistory;
		 
	}

	public function getContentMetaDataById( $cmdId ){
		
		$query = "SELECT mct.mct_parent_cnt_type_id as parentId, parent.cd_name as parent_content_type, cm.*
				FROM content_metadata AS cm
				  	JOIN catalogue_detail AS cd ON ( cd.cd_id = cm.cm_content_type )
					JOIN icn_manage_content_type mct ON ( mct.mct_cnt_type_id = cd.cd_id )
					JOIN catalogue_detail AS parent ON ( mct.mct_parent_cnt_type_id =  parent.cd_id )
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
} ?>

