<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/Package.php");
require_once(APP."Daos/BaseDao.php");

class PackageDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}

	private function packageDetailsFromRow($row) {
		$package = new Package();
		
		$package->portletId 				= $row['portletId'];
		$package->sp_pkg_id			 		= $row['sp_pkg_id'];
		$package->timestamp 				= $row['timestamp'];
		$package->promoid 					= $row['promoid'];
		$package->contentTypeMD5 			= $row['contentTypeMD5'];
		if( !empty( $row['contentFileURLMD5'] ) ) {
			$package->contentFileURLMD5	= $row['contentFileURLMD5'];
		}	
		$package->cft_thumbnail_img_browse 	= $row['cft_thumbnail_img_browse'];
		$package->cft_thumbnail_size 		= $row['cft_thumbnail_size'];
		$package->cm_title 					= $row['cm_title'];
		
		$package->cm_genre			 		= $row['cm_genre'];
		if( !empty( $row['genre'] ) ) {
			$package->genre	= $row['genre'];
		}
		
		if( !empty( $row['searchKey'] ) ) {
			$package->searchKey	= $row['searchKey'];
		}
		
		if( !empty( $row['cd_id'] ) ) {
			$package->cd_id	= $row['cd_id'];
		}
		
		if( !empty( $row['cd_name'] ) ) {
			$package->cd_name	= $row['cd_name'];
		}
		
		if( !empty( $row['parentId'] ) ) {
			$package->parentId	= $row['parentId'];
		}
		
		if( !empty( $row['cf_cm_id'] ) ) {
			$package->cf_cm_id	= $row['cf_cm_id'];
		}
		
		if( !empty( $row['cft_cm_id'] ) ) {
			$package->cft_cm_id	= $row['cft_cm_id'];
		}
		if( !empty( $row['cg_images'] ) ) {
			$package->cg_images	= $row['cg_images'];
		}
		if( !empty( $row['sp_jed_id'] ) ) {
			$package->sp_jed_id	= $row['sp_jed_id'];
		}
		
		
		$package->cm_streaming_url			= $row['cm_streaming_url'];
		$package->cm_downloading_url		= $row['cm_downloading_url'];
		$package->cd_id			 			= $row['cd_id'];
		$package->cd_name			 		= $row['cd_name'];
		
		if( !empty( $row['cf_url'] ) ) {
			$package->cf_url	= $row['cf_url'];
		}
		
		$package->unsetValues(array('created_on', 'updated_on', 'created_by', 'updated_by'));
		
		return $package;
	}
	
	public function getVendorIdsByStoreId( $storeId ){
		$query = "SELECT 
						ivd.vd_id as vendor_id,
						ivd.vd_name as vendor_name,
						( SELECT 
							cm.cm_id 
						  FROM 
							catalogue_master AS cm 
						  WHERE 
							cm.cm_name in ('Vendor') )as cm_id 
					FROM 
						icn_store st
					INNER  JOIN
						multiselect_metadata_detail mmd on (mmd.cmd_group_id = st.st_vendor)
					JOIN 
						icn_vendor_detail ivd ON ivd.vd_id =  mmd.cmd_entity_detail
					WHERE 
						st.st_id = :storeId AND 
						ivd.vd_is_active = 1 ";
	
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':storeId', $storeId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
			
		$vendors = array();
		 	
		while($row = $statement->fetch()) {
			$vendors[] = $row;
		}
			
		return $vendors;
	}

	public function getPortletsWithContentsByPackageIds( $packageId, $portletId, $vendorIds ) {
		//echo "packageId:".$packageId; echo "portletId:".$portletId; echo "vendorIds:". $vendorIds;
		$query = "SELECT
					  DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') as timestamp, 
				      SUBSTR(CONCAT('z_',MD5(RAND()),MD5(RAND())),1,32) as promoid,
				   	  md5(cd.cd_name) as contentTypeMD5, 
					  md5(cf.cf_url_base) as contentFileURLMD5 ,
				      pub_map.pmpp_ppp_id as  portletId,
				      cft.cft_thumbnail_img_browse,
				      cft.cft_thumbnail_size,
				      cmd.cm_title,
				      cmd.cm_genre,
					  cmd.cm_streaming_url,
				      cmd.cm_downloading_url,
				      cmd.cm_vendor,
                      vd.vd_name,
				      cd.cd_id,
				      cd.cd_name,
				      cd1.cd_name as genre,
				      cf.cf_url ,
					  cf.cf_template_id,
				      cmd.cm_id as cf_cm_id,
				      sp.sp_pkg_id
				    -- , cg.pci_cg_img_browse as cg_images
				    FROM icn_store_package AS sp
				    JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
				    JOIN icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
				    JOIN icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
				    JOIN content_files AS cf ON cf.cf_cm_id = pc.pc_cm_id
				    JOIN content_files_thumbnail AS cft ON cft.cft_cm_id = pc.pc_cm_id
				    JOIN content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
				    JOIN icn_vendor_detail AS vd ON cmd.cm_vendor = vd.vd_id
				    JOIN catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
					JOIN catalogue_detail AS cd1 ON cd1.cd_id = cmd.cm_genre
					-- JOIN icn_package_cg_image AS cg ON cg.pci_sp_pkg_id = sp.sp_pkg_id
				    WHERE sp.sp_pkg_id = :packageId  AND
						pub_map.pmpp_ppp_id = :portletId AND 
						ippp.ppp_crud_isactive IS NULL  AND 
						sp.sp_is_active = 1 AND 
						sp.sp_crud_isactive IS NULL AND
						pct.pct_crud_isactive IS NULL AND
						pct.pct_is_active = 1 AND 
						pc.pc_crud_isactive IS NULL AND
						pc.pc_ispublished = 1 AND
						cmd.cm_state = 4  AND 
						FIND_IN_SET(cmd.cm_vendor, :vendorIds ) AND
						cmd.cm_starts_from <= NOW() AND 
						cmd.cm_expires_on >= NOW()
						-- AND cg.pci_crud_isactive IS NULL AND
                        -- cg.pci_crud_isactive IS NULL AND
                        -- cg.pci_is_default = 1
					GROUP BY 
						portletId, cf.cf_cm_id
						-- , cg.pci_sp_pkg_id
				    ORDER BY 
						portletId, cf.cf_cm_id
						-- , cg.pci_sp_pkg_id ";
		//sp.sp_pkg_id IN (".$packageIds.") AND
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageId );
		$statement->bindParam( ':portletId', $portletId );
		$statement->bindParam( ':vendorIds', $vendorIds );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$package = array();
		
		while($row = $statement->fetch()) {
			$package[] = $row ;//$this->packageDetailsFromRow($row);
		}
		
		return $package;
	}
	
	public function getPackageContentsByIdByContentType( $packageId, $contentType ) {
		
		$query = "SELECT
					  pub_map.pmpp_ppp_id as  portletId,
				      cft.cft_thumbnail_img_browse,
				      cft.cft_thumbnail_size,
				      cmd.cm_title,
				      cmd.cm_genre,
					  cmd.cm_streaming_url,
				      cmd.cm_downloading_url,
				      cd.cd_id,
				      cd.cd_name,
				      cf.cf_url ,
					  cf.cf_template_id,
				      cmd.cm_id as cf_cm_id,
				      sp.sp_pkg_id
				    FROM
				       icn_store_package AS sp
				    JOIN
						icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN
						icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
				    JOIN
						icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
				    JOIN
						icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
				    JOIN
						content_files AS cf ON cf.cf_cm_id = pc.pc_cm_id
				    JOIN
						content_files_thumbnail AS cft ON cft.cft_cm_id = pc.pc_cm_id
				    JOIN
						content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
				    JOIN
						catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
				    WHERE
						sp.sp_pkg_id = :packageId AND
						cd.cd_name = :contentType AND 
						ippp.ppp_crud_isactive IS NULL  AND
						cmd.cm_state = 4  ANDcd2.cd_name as genre_name,
						cmd.cm_starts_from <= NOW() AND
						cmd.cm_expires_on >= NOW() AND
						pct.pct_crud_isactive IS NULL AND 
						pct.pct_is_active = 1 AND 
						pc.pc_crud_isactive IS NULL 
						-- pc.pc_is_active = 1 AND 
					GROUP BY
						portletId, cf.cf_cm_id
				    ORDER BY
						portletId, cf.cf_cm_id ";
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageId );
		$statement->bindParam( ':contentType', $contentType );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$package = array();
		
		while($row = $statement->fetch()) {
			$package[] = $this->packageDetailsFromRow($row);
		}
		
		return $package;
	}
	
	public function getPortletsContentsBySearchKey( $packageIds,  $searchKey, $vendorIds ) {
		//echo $packageIds; echo $searchKey; echo $vendorIds;

		$query = "SELECT
					  DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') as timestamp,
				      SUBSTR(CONCAT('z_',MD5(RAND()),MD5(RAND())),1,32) as promoid,
				   	  md5(cd.cd_name) as contentTypeMD5,
				      cft.cft_thumbnail_img_browse,
				      cft.cft_thumbnail_size,
				      cmd.cm_title,
				      cmd.cm_genre,
					  cd2.cd_name as genre,
					  cmd.cm_key_words,
					  cmd.cm_streaming_url,
				      cmd.cm_downloading_url,
				      cd.cd_id,
				      cd.cd_name,
				      cmd.cm_id as cft_cm_id,
				      sp.sp_pkg_id,
					  ( SELECT 
						    group_concat( cd1.cd_name ) 
					    FROM 
						    content_metadata cm
					    JOIN
						    multiselect_metadata_detail mmd ON ( cm.cm_key_words = mmd.cmd_group_id )
					    JOIN
						    catalogue_detail cd1 ON (cd1.cd_id = mmd.cmd_entity_detail) 
					    WHERE 
							cm.cm_id = cft.cft_cm_id ) searchKey,
					  mct.mct_parent_cnt_type_id as parentId
				    FROM
				       icn_store_package AS sp
				    JOIN
						icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
				    JOIN
						icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
				    JOIN
						content_files_thumbnail AS cft ON cft.cft_cm_id = pc.pc_cm_id
				    JOIN
						content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
					JOIN 
				       icn_vendor_detail AS vd ON cmd.cm_vendor = vd.vd_id
					JOIN
						multiselect_metadata_detail mmd ON ( mmd.cmd_group_id = cmd.cm_key_words )
					JOIN
						catalogue_detail AS cd ON ( cd.cd_id = cmd.cm_content_type )
					JOIN
						catalogue_detail cd1 ON ( cd1.cd_id = mmd.cmd_entity_detail )
					JOIN
						icn_manage_content_type mct ON ( mct.mct_cnt_type_id = cd.cd_id )
					JOIN
						catalogue_detail AS cd2 ON ( cd2.cd_id = cmd.cm_genre )
				    WHERE
						FIND_IN_SET(sp.sp_pkg_id, :packageIds ) AND
						FIND_IN_SET(cmd.cm_vendor, :vendorIds ) AND
						cmd.cm_state = 4  AND
						cmd.cm_starts_from <= NOW() AND
						cmd.cm_expires_on >= NOW()
					GROUP BY
						cft.cft_cm_id
				    ORDER BY
						cft.cft_cm_id ";
		//FIND_IN_SET(pub_map.pmpp_ppp_id, :portletIds ) AND
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageIds', $packageIds );
		$statement->bindParam( ':vendorIds', $vendorIds );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$package = array();
	
		while($row = $statement->fetch()) {
			$package[] = $row ;//$this->packageDetailsFromRow($row);
		}

		$contentArray = array_filter($package, function ($var) use ($searchKey) {
			return (stripos($var['searchKey'], $searchKey) !== false );
		});

		$package = $contentArray;
	
		return $package;
	}
	
	public function getPackageContentsByPackageIdByContentId( $packageObj ) {
		$limitQuery = "";
		if( trim( $packageObj->limit) != "" && trim( $packageObj->limit) > 0 ) {
			$limitQuery = "LIMIT ". trim( $packageObj->limit );
		}
		
		$query = "SELECT
					  pub_map.pmpp_ppp_id as  portletId,
				      cft.cft_thumbnail_img_browse,
				      cft.cft_thumbnail_size,
				      cmd.cm_title,
				      cmd.cm_genre,
					  cmd.cm_streaming_url,
				      cmd.cm_downloading_url,
				      cd.cd_id,
				      cd.cd_name,
				      cmd.cm_id,
				      sp.sp_pkg_id
				    FROM
				       icn_store_package AS sp
				    JOIN
						icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN
						icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
				    JOIN
						icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
				    JOIN
						icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
				    JOIN
						content_files AS cf ON cf.cf_cm_id = pc.pc_cm_id
				    JOIN
						content_files_thumbnail AS cft ON cft.cft_cm_id = pc.pc_cm_id
				    JOIN
						content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
				    JOIN
						catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
				    WHERE
						sp.sp_pkg_id = :packageId AND
						sp.sp_is_active = 1 AND 
						sp.sp_crud_isactive IS NULL AND
						cd.cd_id = :contentType AND
						ippp.ppp_crud_isactive IS NULL  AND
						pub_map.pmpp_crud_isactive IS NULL AND
						cmd.cm_state = 4 AND
						cmd.cm_starts_from <= NOW() AND
						cmd.cm_expires_on >= NOW() AND
						pct.pct_crud_isactive IS NULL AND 
						pct.pct_is_active = 1 AND 
						pc.pc_crud_isactive IS NULL
						-- pc.pc_is_active = 1 
					GROUP BY
						portletId, cmd.cm_id, cf.cf_cm_id
				    ORDER BY
						portletId, cmd.cm_id, cf.cf_cm_id " . $limitQuery; 
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageObj->packageId );
		$statement->bindParam( ':contentType', $packageObj->contentType );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$packageDetails = array();
		
		while($row = $statement->fetch()) {
			$package['portletId'] 				 = $row['portletId'];
			$package['cft_thumbnail_img_browse'] = $row['cft_thumbnail_img_browse'];
			$package['cft_thumbnail_size'] 		 = $row['cft_thumbnail_size'];
			$package['cm_title'] 				 = $row['cm_title'];
			$package['cm_genre'] 				 = $row['cm_genre'];
			$package['cm_streaming_url'] 		 = $row['cm_streaming_url'];
			$package['cm_downloading_url'] 		 = $row['cm_downloading_url'];
			$package['cd_id'] 					 = $row['cd_id'];
			$package['cd_name'] 				 = $row['cd_name'];
			$package['cm_id'] 					 = $row['cm_id'];
			$package['sp_pkg_id'] 			     = $row['sp_pkg_id'];
			
			$packageDetails[] = $package;
		}
		 
		return $packageDetails;
	}
	
	public function getValuePackPlanDetailsByPackageIdByOperatorId( $packageObj, $operatorId ) {
		
		$limitQuery = "";
		
		if( trim( $packageObj->limit) != "" && trim( $packageObj->limit) > 0 ) {
			$limitQuery = "LIMIT ". trim( $packageObj->limit );
		}
		$length = strlen($operatorId);

		$query = "SELECT sp.sp_pkg_id, cmd.cm_id, cd.cd_id, cd.cd_name, cmd.cm_content_type, vpl.*, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
				  	FROM icn_store_package AS sp
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_value_pack_site AS pvs ON pvs.pvs_sp_pkg_id = sp.sp_pkg_id
					JOIN icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
					JOIN icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
					JOIN content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
					JOIN catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
					JOIN icn_valuepack_plan AS vpl ON vpl.vp_id = pvs.pvs_vp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = vpl.vp_jed_id )
					WHERE sp.sp_pkg_id = :packageId AND
						sp.sp_is_active = 1 AND 
						sp.sp_crud_isactive IS NULL AND
						pvs.pvs_crud_isactive IS NULL AND 
						pvs.pvs_is_active = 1 AND 
						cd.cd_id = :contentType AND 
						dscl.dcl_partner_id = :operatorId AND
						ippp.ppp_crud_isactive IS NULL AND
						pct.pct_crud_isactive IS NULL AND 
						pct.pct_is_active = 1 AND 
						pc.pc_crud_isactive IS NULL 
						-- pc.pc_is_active = 1 
					GROUP BY vpl.vp_id
					HAVING BINARY dcl_partner_id = :operatorId "
					. $limitQuery ;
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageObj->packageId );
		$statement->bindParam( ':contentType', $packageObj->contentType );
		$statement->bindParam( ':operatorId', $operatorId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$valuePackDetails = array();
		while($row = $statement->fetch()) {
			$valuePackDetails[] = $row;
		}
		
		return $valuePackDetails;
	}
	
	public function getSubscriptionPlanDetailsByPackageIdByOperatorId( $packageObj,$operatorId ) {
		
		$limitQuery = "";
		$length = strlen($operatorId);

		if( trim( $packageObj->limit) != "" && trim( $packageObj->limit) > 0 ) {
			$limitQuery = "LIMIT ". trim( $packageObj->limit );
		}

		$stmt1 = $this->dbConnection->prepare("SELECT sp_pkg_id FROM icn_store_package
												WHERE sp_parent_pkg_id = 0 AND sp_pkg_type = 0 AND sp_pkg_id = :packageId");
		$stmt1->bindParam( ':packageId', $packageObj->packageId );
		$stmt1->execute();
		$stmt1->setFetchMode(PDO::FETCH_ASSOC);

		$stmt = $this->dbConnection->prepare("SELECT sp_parent_pkg_id FROM icn_store_package
												WHERE sp_parent_pkg_id != 0 AND sp_pkg_id = :packageId");
		$stmt->bindParam( ':packageId', $packageObj->packageId );
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		if($stmt1->rowCount() > 0) { //mainsite packages
			//echo "mainsite"; exit;
			$query = "SELECT sp.sp_pkg_id, spl.*,  SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
					FROM icn_store_package AS sp
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp.sp_pkg_id = :packageId
						AND sp.sp_is_active = 1
						AND sp.sp_crud_isactive IS NULL
						AND sp.sp_pkg_type = 0
					    AND sp.sp_parent_pkg_id = 0
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						-- cd.cd_id = :contentType AND
						-- AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId " . $limitQuery;
		}else if($stmt->rowCount() > 0){ //Mapped with mainsite packages

			$query = "SELECT pct.pct_is_active, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer, pct.pct_cnt_type, spl.*
					FROM icn_store_package AS sp
					inner join icn_store_package AS sp1 on sp.sp_pkg_id = sp1.sp_parent_pkg_id
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp1.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					LEFT JOIN icn_pack_content_type AS pct ON pct.pct_pk_id = sp1.sp_pk_id
					LEFT JOIN icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
					LEFT JOIN content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
					LEFT JOIN catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
					LEFT JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp1.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp1.sp_pkg_id  = :packageId
						AND sp1.sp_pkg_type = 0
						AND sp1.sp_parent_pkg_id != 0
						AND sp1.sp_is_active = 1
						AND sp1.sp_crud_isactive IS NULL
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						AND pct.pct_cnt_type = :contentType
						-- AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pct.pct_crud_isactive IS NULL
						AND pct.pct_is_active = 1
						AND pc.pc_crud_isactive IS NULL
						AND pc.pc_ispublished = 1
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId " .$limitQuery;
		}else { //pack site packages

			$query = "SELECT sp.sp_pkg_id, cmd.cm_id, cd.cd_id, cd.cd_name, cmd.cm_content_type, spl.*,  SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
					FROM icn_store_package AS sp
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					JOIN icn_pack_content_type AS pct ON pct.pct_pk_id = sp.sp_pk_id
					JOIN icn_pack_content AS pc ON pc.pc_pct_id = pct.pct_id
					JOIN content_metadata AS cmd ON cmd.cm_id = pc.pc_cm_id
					JOIN catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
					JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp.sp_pkg_id = :packageId
						AND sp.sp_is_active = 1
						AND sp.sp_crud_isactive IS NULL
						AND sp.sp_pkg_type = 1
					    AND sp.sp_parent_pkg_id = 0
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						AND cd.cd_id = :contentType
						-- AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pct.pct_crud_isactive IS NULL
						AND pct.pct_is_active = 1
						AND pc.pc_crud_isactive IS NULL
						AND pc.pc_ispublished = 1
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId " . $limitQuery;
		}
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageObj->packageId );
		$statement->bindParam( ':contentType', $packageObj->contentType );
		$statement->bindParam( ':operatorId', $operatorId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$subscriptionDetails = array();
		while($row = $statement->fetch()) {
			$subscriptionDetails[] = $row;
		}
		
		return $subscriptionDetails;
		
	}
	public function getAlacartaPlanDetailsByPackageIdByContentTypeByOperatorId( $packageObj,$operatorId ) {
		
		$limitQuery = "";
		
		if( trim( $packageObj->limit) != "" && trim( $packageObj->limit) > 0 ) {
			$limitQuery = "LIMIT ". trim( $packageObj->limit );
		}
		$length = strlen($operatorId);

		$query = "SELECT apl.*, apl.ap_description, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
				    FROM icn_store_package AS sp
				    JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_alacart_offer_site AS paos ON paos.paos_sp_pkg_id = sp.sp_pkg_id
        			JOIN icn_package_content_type as pkct ON pkct.pct_paos_id = paos.paos_id AND ISNULL(pkct.pct_crud_isactive)
        			JOIN icn_alacart_plan AS apl ON (apl.ap_id = pkct.pct_download_id OR apl.ap_id = pkct.pct_stream_id)
					JOIN icn_disclaimer AS dscl ON dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = apl.ap_jed_id
					JOIN catalogue_detail AS cd ON apl.ap_content_type = cd.cd_id
				    WHERE sp.sp_pkg_id = :packageId AND
						sp.sp_is_active = 1 AND 
						sp.sp_crud_isactive IS NULL AND
						paos.paos_crud_isactive IS NULL AND 
						paos.paos_is_active = 1 AND 
						cd.cd_id = :contentType AND
						-- dscl.dcl_partner_id = :operatorId AND
						ippp.ppp_crud_isactive IS NULL AND
						pkct.pct_is_active = 1 
					GROUP
						BY apl.ap_id
						HAVING BINARY dcl_partner_id = :operatorId " . $limitQuery;
	
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageObj->packageId );
		$statement->bindParam( ':contentType', $packageObj->contentType );
		$statement->bindParam( ':operatorId', $operatorId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$alacartPlanDetails = array();
		while($row = $statement->fetch()) {
			$alacartPlanDetails[] = $row;
		}
		
		return $alacartPlanDetails;
	}

	public function getSubscriptionPricePointsByPackageId( $packageId,$operatorId ) {
		$length = strlen($operatorId);

		$stmt1 = $this->dbConnection->prepare("SELECT sp_pkg_id FROM icn_store_package
												WHERE sp_parent_pkg_id = 0 AND sp_pkg_type = 0 AND sp_pkg_id = :packageId");
		$stmt1->bindParam( ':packageId', $packageId);
		$stmt1->execute();
		$stmt1->setFetchMode(PDO::FETCH_ASSOC);

		$stmt = $this->dbConnection->prepare("SELECT sp_parent_pkg_id FROM icn_store_package
												WHERE sp_parent_pkg_id != 0 AND sp_pkg_id = :packageId");
		$stmt->bindParam( ':packageId', $packageId );
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		if($stmt1->rowCount() > 0) { //mainsite packages
			$query = "SELECT sp.sp_pkg_id, spl.*, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
					FROM icn_store_package AS sp
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp.sp_pkg_id = :packageId
						AND sp.sp_is_active = 1
						AND sp.sp_crud_isactive IS NULL
						AND sp.sp_pkg_type = 0
					    AND sp.sp_parent_pkg_id = 0
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId ";
		}else if($stmt->rowCount() > 0){ //Mapped with mainsite packages
			//echo "mapped mainsite"; exit;

			$query = "SELECT SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer, spl.*
					FROM icn_store_package AS sp
					inner join icn_store_package AS sp1 on sp.sp_pkg_id = sp1.sp_parent_pkg_id
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp1.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					LEFT JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp1.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp1.sp_pkg_id  = :packageId
						AND sp1.sp_pkg_type = 0
						AND sp1.sp_parent_pkg_id != 0
						AND sp1.sp_is_active = 1
						AND sp1.sp_crud_isactive IS NULL
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId ";
		}else { //pack site packages
			//echo "packsite"; exit;

			 $query = "SELECT sp.sp_pkg_id, spl.*, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id, dscl.dcl_disclaimer
					FROM icn_store_package AS sp
					JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
					JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pkg_id
					JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
					WHERE sp.sp_pkg_id = :packageId
						AND sp.sp_is_active = 1
						AND sp.sp_crud_isactive IS NULL
						AND sp.sp_pkg_type = 1
					    AND sp.sp_parent_pkg_id = 0
						AND pss.pss_crud_isactive IS NULL
						AND pss.pss_is_active = 1
						AND dscl.dcl_partner_id = :operatorId
						AND ippp.ppp_crud_isactive IS NULL
						AND pub_map.pmpp_crud_isactive IS NULL
					GROUP BY spl.sp_id
					HAVING BINARY dcl_partner_id = :operatorId ";
		}
 		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageId );
		$statement->bindParam( ':operatorId', $operatorId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);

		$subscriptionDetails = array();
		while($row = $statement->fetch()) {
			$subscriptionDetails[] = $row;
		}

		return $subscriptionDetails;

	}
}
?>