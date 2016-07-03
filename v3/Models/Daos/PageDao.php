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
						pub_map.pmpp_id as portletMapId 
					FROM 
						icn_pub_map_portlet_pkg  AS pub_map
				    Right OUTER JOIN 
						icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN 
						icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )
				   	WHERE 
						pub.pp_page_file = :pageName ". $whereCondtion .
				   		" AND portlet.ppp_is_active = 1 
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive ) 
						AND ISNULL( pub_map.pmpp_crud_isactive ) 
						AND portlet.ppp_pkg_allow >= 0 
					ORDER BY 
						portlet.ppp_id,pub_map.pmpp_id ";
			
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
					JOIN icn_store_package AS pkg ON pkg.sp_pkg_id = pub_map.pmpp_sp_pkg_id
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
		$query = "SELECT
						sp.sp_st_id AS storeId,
						sp.sp_pkg_id as packageId
					FROM
						icn_store_package  AS sp
				    JOIN
						icn_store AS st ON ( sp.sp_st_id = st.st_id )
				   	WHERE
						sp.sp_st_id = :storeId 
						AND sp.sp_is_active = 1
						AND ISNULL( sp.sp_crud_isactive )
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
