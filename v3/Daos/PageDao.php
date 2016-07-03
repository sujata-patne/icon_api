<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/Page.php");
require_once(APP."Daos/BaseDao.php");

class PageDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}

	private function packageDetailsFromRow($row) {
		$page = new Page();
		if( !empty( $row['pageName'] ) ) {
			$page->pageName = $row['pageName'];
		}
		if( !empty( $row['portletId'] ) ) {
			$page->portletId = $row['portletId'];
		}
		
		if( !empty( $row['portletMapId'] ) ) {
			$page->portletMapId = $row['portletMapId'];
		}
		
		$page->storeId 			= $row['storeId'];
		$page->packageId 		= $row['packageId'];
		
		if( !empty( $row['token'] ) ) {
			$page->token 			= $row['token'];
		}
		
		if( !empty( $row['pageId'] ) ) {
			$page->pageId			= $row['pageId'];
		}
		if( !empty( $row['vendorIds'] ) ) {
			$page->vendorIds			= $row['vendorIds'];
		}
		if( !empty( $row['subscriptionPlan'] ) ) {
			$page->subscriptionPlan			= $row['subscriptionPlan'];
		}
		if( !empty( $row['pas_arrange_seq'] ) ) {
			$page->pas_arrange_seq			= $row['pas_arrange_seq'];
		}
		if( !empty( $row['pricePoint'] ) ) {
			$page->pricePoint			= $row['pricePoint'];
		}
		if( !empty( $row['singleDayLimit'] ) ) {
			$page->singleDayLimit			= $row['singleDayLimit'];
		}
		if( !empty( $row['fullSubDownloadLimit'] ) ) {
			$page->fullSubDownloadLimit			= $row['fullSubDownloadLimit'];
		}
		if( !empty( $row['fullSubStreamContentLimit'] ) ) {
			$page->fullSubStreamContentLimit			= $row['fullSubStreamContentLimit'];
		}
		if( !empty( $row['fullSubStreamDurationLimit'] ) ) {
			$page->fullSubStreamDurationLimit			= $row['fullSubStreamDurationLimit'];
		}
		if( !empty( $row['fullSubStreamDurationTypeId'] ) ) {
			$page->fullSubStreamDurationTypeId			= $row['fullSubStreamDurationTypeId'];
		}
		if( !empty( $row['fullSubStreamDurationTypeName'] ) ) {
			$page->fullSubStreamDurationTypeName			= $row['fullSubStreamDurationTypeName'];
		}
		
		//$page->unsetValues(array('created_on', 'updated_on', 'created_by', 'updated_by', 'deviceHeight', 'deviceWidth'));
		$page->unsetValues(array('created_on', 'updated_on', 'created_by', 'updated_by' ));
		return $page;
	}

	public function getPackageIdsByPageName( $pageName, $storeId ) {
	    $whereCondtion = "";
	    if( $storeId !=  "" ) {
	    	$whereCondtion = " AND pub.pp_sp_st_id = :storeId ";
	    }
		$query = "SELECT
						pub.pp_page_file as pageName,
						pub.pp_sp_st_id as storeId,
						pub_map.pmpp_sp_pkg_id as packageId ,
						portlet.ppp_id as portletId, 
						pub_map.pmpp_id as portletMapId,
						spl.sp_jed_id AS pricePoint,
						spl.sp_single_day_cnt_limit AS singleDayLimit,
						spl.sp_full_sub_cnt_limit AS fullSubDownloadLimit,
						spl.sp_full_sub_stream_limit AS fullSubStreamContentLimit,
						spl.sp_full_sub_stream_duration AS fullSubStreamDurationLimit,
						spl.sp_full_sub_stream_dur_type AS fullSubStreamDurationTypeId,
						cd3.cd_name AS fullSubStreamDurationTypeName,
						spl.sp_id AS subscriptionPlan,
						ipas.pas_arrange_seq
					FROM icn_pub_map_portlet_pkg  AS pub_map
				    Right OUTER JOIN icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )
					LEFT OUTER JOIN icn_store_package AS sp ON ( (pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id OR pub_map.pmpp_sp_pkg_id = sp.sp_parent_pkg_id AND sp.sp_pkg_type = 0) OR (pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id AND sp.sp_pkg_type = 1))
					LEFT OUTER JOIN icn_package_subscription_site AS pss ON (pss.pss_sp_pkg_id = sp.sp_pkg_id )
					LEFT OUTER JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					LEFT OUTER JOIN icn_package_arrange_sequence AS ipas ON ipas.pas_sp_pkg_id = pss.pss_sp_pkg_id
					LEFT OUTER JOIN catalogue_detail AS cd3 ON ( cd3.cd_id = spl.sp_full_sub_stream_dur_type )
				   	WHERE 
						pub.pp_page_file = :pageName ". $whereCondtion . "
						AND portlet.ppp_is_active = 1
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive )
						AND ISNULL( pub_map.pmpp_crud_isactive )
						AND portlet.ppp_pkg_allow >= 0
						AND sp.sp_crud_isactive IS NULL
						AND spl.sp_crud_isactive IS NULL
						AND pub_map.pmpp_crud_isactive IS NULL
						AND pss.pss_crud_isactive IS NULL
					ORDER BY 
						portlet.ppp_id,ipas.pas_arrange_seq,pub_map.pmpp_id ";
		/*GROUP BY sp.sp_pkg_id,
		   CASE WHEN sp.sp_pkg_id IS NULL
			  THEN portlet.ppp_id
			  ELSE 0
		 END */
		$statement = $this->dbConnection->prepare($query);
		
		$statement->bindParam( ':pageName', $pageName );
		if( $storeId !=  "" ) {
			$statement->bindParam( ':storeId',  $storeId );
		}
		 
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$page = array();
		
		while($row = $statement->fetch()) {
			$page[] = $this->packageDetailsFromRow($row);
		}
		
		return $page;
	}
	public function getPageContents( $pageTitle ) {
 		$query = "SELECT
						pub.pp_page_file as pageName,
						pub.pp_sp_st_id as storeId,
						pub_map.pmpp_sp_pkg_id as packageId ,
						portlet.ppp_id as portletId,
						portlet.ppp_pp_id as pageId,
						pub_map.pmpp_id as portletMapId,
						SHA1(pkg.sp_modified_on) as token
					FROM
						icn_pub_map_portlet_pkg  AS pub_map
				    Right OUTER JOIN
						icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN
						icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )
					Right OUTER JOIN icn_store_package AS pkg ON pkg.sp_pkg_id = pub_map.pmpp_sp_pkg_id
				   	WHERE
						pub.pp_page_title = :pageTitle
				   		AND portlet.ppp_is_active = 1
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive )
						AND ISNULL( pub_map.pmpp_crud_isactive )
						AND portlet.ppp_pkg_allow >= 0
					ORDER BY
						portlet.ppp_id,pub_map.pmpp_id ";

		$statement = $this->dbConnection->prepare($query);

		$statement->bindParam( ':pageTitle', $pageTitle );

		$statement->execute();

		$statement->setFetchMode(PDO::FETCH_ASSOC);

		$page = array();

		while($row = $statement->fetch()) {
			$page[] = $this->packageDetailsFromRow($row);
		}
		return $page;
	}

	public function getPackageIdsByPageId( $pageId ) {
	
		$query = "SELECT
						pub.pp_page_file as pageName,
						pub.pp_sp_st_id as storeId,
						pub_map.pmpp_sp_pkg_id as packageId ,
						portlet.ppp_id as portletId,
						portlet.ppp_pp_id as pageId,
						pub_map.pmpp_id as portletMapId,
						SHA1(pkg.sp_modified_on) as token
					FROM
						icn_pub_map_portlet_pkg  AS pub_map
				    Right OUTER JOIN
						icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN
						icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )
					Right OUTER JOIN icn_store_package AS pkg ON pkg.sp_pkg_id = pub_map.pmpp_sp_pkg_id
				   	WHERE
						pub.pp_id = :pageId
				   		AND portlet.ppp_is_active = 1
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive )
						AND ISNULL( pub_map.pmpp_crud_isactive )
						AND portlet.ppp_pkg_allow >= 0
					ORDER BY
						portlet.ppp_id,pub_map.pmpp_id ";
			
		$statement = $this->dbConnection->prepare($query);
		
		$statement->bindParam( ':pageId', $pageId );
			
		$statement->execute();
	
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$page = array();
	
		while($row = $statement->fetch()) {
			$page[] = $this->packageDetailsFromRow($row);
		}
		return $page;
	}
	
	public function getPackageIdsByStoreId( $storeId ) {
		/*LEFT OUTER JOIN icn_pub_map_portlet_pkg  AS pub_map ON ( (pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id OR pub_map.pmpp_sp_pkg_id = sp.sp_parent_pkg_id AND sp.sp_pkg_type = 0) OR (pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id AND sp.sp_pkg_type = 1))
				    Right OUTER JOIN icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )

		AND portlet.ppp_is_active = 1
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive )
						AND ISNULL( pub_map.pmpp_crud_isactive )
						AND portlet.ppp_pkg_allow >= 0
						AND pub_map.pmpp_crud_isactive IS NULL

		CASE WHEN sp.sp_pkg_id IS NULL
						  THEN portlet.ppp_id
						  ELSE 0
					END*/
		$query = "SELECT sp.sp_st_id AS storeId,
						sp.sp_pkg_id as packageId,
						GROUP_CONCAT(distinct(mlm.cmd_entity_detail)) as vendorIds
				FROM icn_store_package  AS sp
				JOIN icn_store AS st ON ( sp.sp_st_id = st.st_id )
				JOIN multiselect_metadata_detail AS mlm ON ( mlm.cmd_group_id = st.st_vendor )
				WHERE sp.sp_st_id = :storeId
					AND sp.sp_is_active = 1
					AND ISNULL( sp.sp_crud_isactive )
					AND ISNULL( st.st_crud_isactive )
				GROUP BY sp.sp_pkg_id
				ORDER BY sp.sp_pkg_id ";

		$statement = $this->dbConnection->prepare($query);
	
		$statement->bindParam( ':storeId', $storeId );
					
		$statement->execute();
	
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$page = array();
	
		while($row = $statement->fetch()) {
			$page[] = $this->packageDetailsFromRow($row);
		}
	
		return $page;
	}
	
	public function getMainSitePackageIdsByStoreId( $storeId ) {
		$query = "SELECT
						sp.sp_st_id AS storeId,
						sp.sp_pkg_id as packageId
					FROM
						icn_store_package  AS sp
				    JOIN
						icn_store AS st ON ( sp.sp_st_id = st.st_id )
				   	WHERE
						sp.sp_st_id = :storeId AND
						sp.sp_is_active = 1 AND
						sp.sp_pkg_type = 0 AND
						 sp.sp_parent_pkg_id = 0 AND
						ISNULL( sp.sp_crud_isactive )
					ORDER BY
						sp.sp_pkg_id ";
			
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':storeId', $storeId );
			
		$statement->execute();
	
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$page = array();
	
		while($row = $statement->fetch()) {
			$page[] = $this->packageDetailsFromRow($row);
		}
	
		return $page;
	}
}
?>
