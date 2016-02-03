<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 23-12-2015
 * Time: 18:38
 */
class PackageDetails
{
    private $operatorId;
    private $packages;

    public function find($dbName, $data = array())
    {
        $this->operatorId = $data['operatorId'];
        $this->packages = $data['packages'];
        $package = array();
        $result = array();

        $db = new \mysqli(DBHOST, DBUSER, DBPASSWD, $dbName);

        if ($db->connect_errno > 0) {
            die('Unable to connect to database [' . $db->connect_error . ']');
        }
        foreach ($this->packages as $jsonObj) {
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
						sp.sp_pkg_id = ".$jsonObj->packageId." AND
						cd.cd_id = ".$jsonObj->contentType." AND
						ippp.ppp_crud_isactive IS NULL  AND
						cmd.cm_state = 4 AND
						cmd.cm_starts_from <= NOW() AND
						cmd.cm_expires_on >= NOW()
					GROUP BY
						portletId, cf.cf_cm_id
				    ORDER BY
						portletId, cf.cf_cm_id
					LIMIT ". $jsonObj->limit;
            $packageResult = mysqli_query($db, $query);

            if( mysqli_num_rows($packageResult) > 0 ){
                while($row = mysqli_fetch_assoc($packageResult)){
                    $package[] = $row;
                }
            }
            $valuePackPlanDetials = $this->getValuePackPlanDetailsByPkgIdOperator($db, $jsonObj->packageId);
            array_push( $package, $valuePackPlanDetials );

            $subscriptionPlanDetials = $this->getSubscriptionPlanDetailsByPkgIdOperator($db, $jsonObj->packageId);
            array_push( $package, $subscriptionPlanDetials );

            $alacartPlanDetials = $this->getAlacartaPlanDetailsByPkgIdContentTypeOperator($db,$jsonObj->packageId, $jsonObj->contentType);
            array_push( $package, $alacartPlanDetials );
        }

        return $package;
    }

    public function getValuePackPlanDetailsByPkgIdOperator( $con, $packageId ) {
        $valuePackDetails = array();
        $query = "SELECT
					  vpl.*,
				      dscl.dcl_partner_id,
				      dscl.dcl_disclaimer
				    FROM
				       icn_store_package AS sp
				    JOIN
						icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN
						icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN
						icn_package_value_pack_site AS pvs ON pvs.pvs_sp_pkg_id = sp.sp_pk_id
					JOIN
						icn_valuepack_plan AS vpl ON vpl.vp_id = pvs.pvs_vp_id
					JOIN
						icn_disclaimer AS dscl ON dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = vpl.vp_jed_id
				    WHERE
						sp.sp_pkg_id = ".$packageId." AND
						dscl.dcl_partner_id = ".$this->operatorId." AND
						ippp.ppp_crud_isactive IS NULL
						GROUP BY vpl.vp_id ";

        $result = mysqli_query($con, $query);

        if( mysqli_num_rows($result) > 0 ){
            while($row = mysqli_fetch_assoc($result)){
                $valuePackDetails[] = $row;
            }
        }
        mysqli_free_result($result);
        $data['ValuePackPlan'] = $valuePackDetails;
        return $data;
    }
    public function getSubscriptionPlanDetailsByPkgIdOperator( $con, $packageId ) {
        $subscriptionDetails = array();
        $query = "SELECT
					  spl.*,
				      dscl.dcl_partner_id,
				      dscl.dcl_disclaimer
				    FROM icn_store_package AS sp
				    JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN icn_package_subscription_site AS pss ON pss.pss_sp_pkg_id = sp.sp_pk_id
					JOIN icn_sub_plan AS spl ON spl.sp_id = pss.pss_sp_id
					JOIN icn_disclaimer AS dscl ON dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id
				    WHERE
						sp.sp_pkg_id = ".$packageId." AND
						dscl.dcl_partner_id = ".$this->operatorId." AND
						ippp.ppp_crud_isactive IS NULL
						GROUP BY spl.sp_id ";
        $result = mysqli_query($con, $query);

        if( mysqli_num_rows($result) > 0 ){
            while($row = mysqli_fetch_assoc($result)){
                $subscriptionDetails[] = $row;
            }
        }
        mysqli_free_result($result);

        $data['SubscriptionPlan'] = $subscriptionDetails;
        return $data;
    }
    public function getAlacartaPlanDetailsByPkgIdContentTypeOperator( $con, $packageId, $contentType ) {
        $alacartDetails = array();

        $query = "SELECT
					  apl.*,
				      dscl.dcl_partner_id,
				      dscl.dcl_disclaimer
				    FROM
				       icn_store_package AS sp
				    JOIN icn_pub_map_portlet_pkg AS pub_map  ON( pub_map.pmpp_sp_pkg_id = sp.sp_pkg_id )
				    JOIN
						icn_pub_page_portlet AS ippp ON ippp.ppp_id = pub_map.pmpp_ppp_id
					JOIN
						icn_package_alacart_offer_site AS paos ON paos.paos_sp_pkg_id = sp.sp_pk_id
        			JOIN
        				icn_package_content_type as pkct ON pkct.pct_paos_id = paos.paos_id AND ISNULL(pkct.pct_crud_isactive)
        			JOIN
        				icn_alacart_plan AS apl ON (apl.ap_id = pkct.pct_download_id OR apl.ap_id = pkct.pct_stream_id)
					JOIN
						icn_disclaimer AS dscl ON dscl.dcl_st_id = sp.sp_st_id AND dscl.dcl_ref_jed_id = apl.ap_jed_id
					JOIN
						catalogue_detail AS cd ON apl.ap_content_type = cd.cd_id
				    WHERE
						sp.sp_pkg_id = ".$packageId." AND
						cd.cd_id = ".$contentType." AND
						dscl.dcl_partner_id = ".$this->operatorId." AND
						ippp.ppp_crud_isactive IS NULL
						GROUP BY apl.ap_id ";

        $result = mysqli_query($con, $query);

        if( mysqli_num_rows($result) > 0 ){
            while($row = mysqli_fetch_assoc($result)){
                $alacartDetails[] = $row;
            }
        }
        mysqli_free_result($result);

        $data['AlacartPlan'] = $alacartDetails;
        return $data;
    }

}