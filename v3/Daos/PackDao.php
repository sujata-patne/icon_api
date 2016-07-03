<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/Pack.php");
require_once(APP."Daos/BaseDao.php");

class PackDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}

	private function packageDetailsFromRow($row) {
		$pack = new Pack();
		
		$pack->sp_pkg_id 				= $row['sp_pkg_id'];
		$pack->sp_st_id					= $row['sp_st_id'];
		$pack->sp_dc_id 				= $row['sp_dc_id'];
		$pack->sp_pkg_type 				= $row['sp_pkg_type'];
		$pack->sp_package_name 			= $row['sp_package_name'];
		$pack->sp_package_desc 			= $row['sp_package_desc'];		
		$pack->sp_parent_pkg_id 		= $row['sp_parent_pkg_id'];
		$pack->sp_pk_id 				= $row['sp_pk_id'];
		$pack->pk_name 					= $row['pk_name'];
		$pack->pk_desc			 		= $row['pk_desc'];
		$pack->pk_cnt_display_opt		= $row['pk_cnt_display_opt'];
		$pack->cm_id			 		= $row['cm_id'];
		$pack->cm_content_type			= $row['cm_content_type'];
		$pack->cm_title					= $row['cm_title'];
		$pack->cm_vendor			 	= $row['cm_vendor'];
		$pack->cm_property_id			= $row['cm_property_id'];
		$pack->cd_name			 		= $row['cd_name'];
		$pack->cft_thumbnail_img_browse	= $row['cft_thumbnail_img_browse'];
		$pack->cft_thumbnail_size		= $row['cft_thumbnail_size'];
		$pack->cf_template_id			= $row['cf_template_id'];
		$pack->cm_streaming_url			= $row['cm_streaming_url'];
		$pack->cm_downloading_url		= $row['cm_downloading_url'];
		
		$pack->unsetValues(array('created_on', 'updated_on', 'created_by', 'updated_by'));
		
		return $pack;
	}
	
public function getAllPacksByPackageIds( $packageId, $portletId, $storeId, $templateId ) {
		$query = "SELECT 
					isp.sp_pkg_id, 
					isp.sp_st_id,
					isp.sp_dc_id,
					isp.sp_pkg_type,
					isp.sp_package_name,
				    isp.sp_package_desc,
					isp.sp_parent_pkg_id,
					isp.sp_pk_id,
				    icn_pk.pk_name,
					icn_pk.pk_desc,
					icn_pk.pk_cnt_display_opt,
					cmd.cm_id,
					cmd.cm_content_type,
				    cmd.cm_title,
					cmd.cm_vendor,
				    cmd.cm_property_id,
					cmd.cm_streaming_url,
					cmd.cm_downloading_url,
					cd.cd_name,
					cft.cft_thumbnail_img_browse,
					cft.cft_thumbnail_size,
					cf.cf_template_id,
					cf.cf_url,
					cf.cf_url_base,
					ipas.pas_arrange_seq

				FROM 
					icn_store_package as isp
				JOIN
					icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = isp.sp_pkg_id )
				JOIN
					icn_packs AS icn_pk ON( icn_pk.pk_id = isp.sp_pk_id AND icn_pk.pk_st_id = isp.sp_st_id AND icn_pk.pk_is_active = 1 AND (icn_pk.pk_crud_isactive) IS NULL )
				JOIN
					icn_pack_content_type AS icn_pct ON( icn_pct.pct_pk_id = icn_pk.pk_id AND icn_pct.pct_is_active = 1 AND ( icn_pct.pct_crud_isactive ) IS NULL )
				JOIN
					icn_pack_content AS icn_pc ON( icn_pc.pc_pct_id = icn_pct.pct_id AND icn_pc.pc_ispublished = 1 AND ( icn_pc.pc_crud_isactive ) IS NULL )
				JOIN
					content_metadata cmd ON( icn_pc.pc_cm_id = cmd.cm_id AND icn_pct.pct_cnt_type = cmd.cm_content_type AND cmd.cm_crud_isactive IS NULL AND cmd.cm_state = 4 )
				JOIN 
					catalogue_detail AS cd ON cd.cd_id = cmd.cm_content_type
				LEFT OUTER JOIN 
					content_files_thumbnail cft ON ( cft.cft_cm_id = cmd.cm_id AND cft.cft_crud_isactive IS NULL )
				LEFT OUTER JOIN 
					content_files cf ON ( cf.cf_cm_id = cmd.cm_id AND cf.cf_crud_isactive IS NULL )
				LEFT OUTER JOIN icn_package_arrange_sequence AS ipas ON ipas.pas_sp_pkg_id = isp.sp_pkg_id

				WHERE
					isp.sp_pkg_id = :packageId AND 
					pub_map.pmpp_ppp_id = :portletId AND 
					isp.sp_st_id = :storeId AND
					isp.sp_crud_isactive IS NULL AND
					pub_map.pmpp_crud_isactive IS NULL AND
					isp.sp_is_active = 1 AND
					cf.cf_template_id = :templateId  AND
					( cft.cft_thumbnail_size = '125*125' OR cft.cft_thumbnail_size = '125X125' )
				ORDER BY icn_pc.pc_arrange_seq, pas_arrange_seq ";
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':packageId', $packageId );
		$statement->bindParam( ':portletId', $portletId );
		$statement->bindParam( ':storeId', $storeId );
		$statement->bindParam( ':templateId', $templateId );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$package = array();
		
		while($row = $statement->fetch()) {
			$package[] = $this->packageDetailsFromRow($row);
		}
		
		return $package;
	}
}
?>