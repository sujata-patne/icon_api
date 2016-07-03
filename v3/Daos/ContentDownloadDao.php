<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 04/04/16
 * Time: 10:18 AM
 */
use VOP\Daos\BaseDao;
require_once(APP."Models/ContentDownload.php");
require_once(APP."Daos/BaseDao.php");


class ContentDownloadDao extends BaseDao
{
    public function __construct($dbConn)
    {
        parent::__construct($dbConn);
    }

    public function getDownloadsWithUserAuth( $downloadObj ){

        if(isset($downloadObj->eligibility) && $downloadObj->eligibility == 1){
            $query = "SELECT cm.cm_streaming_url,cm.cm_downloading_url FROM icn_store_package sp 
                      INNER JOIN icn_packs pk ON sp.sp_pk_id = pk.pk_id
                      INNER JOIN icn_pack_content_type pct ON pct.pct_pk_id = pk.pk_id
                      INNER JOIN content_metadata cm ON cm.cm_content_type = pct.pct_cnt_type
                      INNER JOIN billing_details bd on bd.package_id = sp.sp_pkg_id
                      WHERE bd.msisdn = :msisdn
                      AND cm.cm_streaming_url IS NOT NULL
                      AND cm.cm_downloading_url IS NOT NULL";

            $statement = $this->dbConnection->prepare($query);
            $statement->bindParam( ':msisdn', $downloadObj->msisdn );
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $contents = $statement->fetchall();

        }else if(isset($downloadObj->eligibility) && $downloadObj->eligibility == 0){			//not eligible - show all plans for the store
            $query = "SELECT 'Alacarte' as Type,apl.ap_id as Plan_ID,apl.ap_plan_name as Plan_Name FROM icn_alacart_plan AS apl JOIN billing_details AS bd ON bd.app_id = apl.ap_st_id WHERE bd.msisdn = :msisdn  
                      UNION
                      SELECT 'Subscription' as Type,spl.sp_id as PLAN_ID,spl.sp_plan_name as Plan_Name FROM icn_sub_plan AS spl JOIN billing_details AS bd ON bd.app_id = spl.sp_st_id WHERE bd.msisdn = :msisdn     
                      UNION
                      SELECT 'ValuePack' as Type,vpl.vp_id as Plan_ID,vpl.vp_plan_name as Plan_Name FROM icn_valuepack_plan AS vpl JOIN billing_details AS bd ON bd.app_id = vpl.vp_st_id WHERE bd.msisdn = :msisdn  
                      UNION
                      SELECT 'Offer' as Type,ofl.op_id as Plan_ID,ofl.op_plan_name as Plan_Name FROM icn_offer_plan AS ofl JOIN billing_details AS bd ON bd.app_id = ofl.op_st_id WHERE bd.msisdn = :msisdn";

            $statement = $this->dbConnection->prepare($query);
            $statement->bindParam( ':msisdn', $downloadObj->msisdn );
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $contents = $statement->fetchall();

        }else{
			$contents = "Invalid Eligibility Status";	
		}

        return $contents;


    }


    public function getPlanHistory( $downloadObj ){

            $query = "SELECT bd.app_id,bd.msisdn,bd.package_id,bd.plan_id,bd.action,bd.status,bd.request_type,bd.subscription_date,bd.plan_type FROM 	billing_details bd
                      WHERE bd.msisdn = :msisdn
                      AND bd.subscription_date  > DATE_SUB(now(), INTERVAL 6 MONTH)
                      ORDER BY bd.subscription_date";

            $statement = $this->dbConnection->prepare($query);
            $statement->bindParam( ':msisdn', $downloadObj->msisdn );
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $contents = $statement->fetchall();
        return $contents;

    }

    public function getUserDownloadHistoryByMSISDNByAppID( $downloadObj ){

        $query = "SELECT * FROM site_user.content_download 
				  WHERE cd_msisdn = :msisdn 
				  AND cd_app_id = :appId 
				  ORDER BY cd_download_date DESC";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':msisdn', $downloadObj->msisdn );
        $statement->bindParam( ':appId', $downloadObj->appId );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $contents = $statement->fetchall();

        return $contents;

    }
 
	public function getContentDownloadPath( $downloadObj ){
		
		$subClause = $downloadObj['child_id'] != NULL ? "cf.cf_id = ".$downloadObj['child_id'] : "cm.cm_id = ".$downloadObj['metadata_id'];
			
		$query_download_path =	"SELECT DISTINCT SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cf.cf_id as TuneId,cd.cd_name as contentType,cm.cm_downloading_url AS DownloadingURL FROM content_metadata cm 
								INNER JOIN icn_manage_content_type mct ON cm.cm_content_type = mct.mct_cnt_type_id
								INNER JOIN catalogue_detail cd ON mct.mct_parent_cnt_type_id = cd.cd_id
								INNER JOIN content_files cf on cf.cf_cm_id = cm.cm_id
								WHERE cd.cd_id = :content_id AND ".$subClause."
								GROUP BY cm.cm_id";
						
		$statement = $this->dbConnection->prepare($query_download_path);
        $statement->bindParam( ':content_id', $downloadObj['content_id'] );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
		
        $contents = $statement->fetchall();

        return $contents;	
		
	}
	
	
	public function getContentDownloadPathForAll( $downloadObj ){
		
		$query = "SELECT DISTINCT SUBSTRING_INDEX(cf.cf_url, '/', -1) AS FileName FROM content_files cf 
				  INNER join content_metadata cmd ON cmd.cm_id = cf.cf_cm_id
				  INNER JOIN icn_manage_content_type mct ON mct.mct_cnt_type_id = cmd.cm_content_type
				  INNER join catalogue_detail cd ON cd.cd_id = mct.mct_parent_cnt_type_id
				  WHERE cf.cf_cm_id = :cm_id AND cf.file_category_id IS NULL";
				  
		$statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':cm_id', $downloadObj['metadata_id']);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $contents = $statement->fetchall();

        return $contents;		  
				 
		
	}
	
	public function setContentDownload( $downloadObj ){
		
		$metadata_id = $downloadObj->metadata_id != NULL ? $downloadObj->metadata_id : '0'; 
		
		$query_insert_content_download_data = "INSERT INTO content_download( cd_user_id, cd_msisdn, cd_cmd_id, cd_download_count, cd_cd_id, cd_app_id, cd_promo_id, cd_package_id, cd_pack_id, cd_plan_id, cd_cf_id, cd_download_date, cd_response_url) VALUES (:cd_user_id,:cd_msisdn,:cd_cmd_id,:cd_download_count,:cd_cd_id,:cd_app_id,:cd_promo_id,:cd_package_id,:cd_pack_id,:cd_plan_id,:cd_plan_id,:cd_download_date,:cd_response_url)";
		
		$statement = $this->dbConnection->prepare($query_insert_content_download_data);
        $statement->bindParam( ':cd_user_id',$downloadObj->cd_user_id );
        $statement->bindParam( ':cd_msisdn', $downloadObj->msisdn );
        $statement->bindParam( ':cd_cmd_id', $metadata_id );
        $statement->bindParam( ':cd_download_count', $downloadObj->cd_download_count );
        $statement->bindParam( ':cd_cd_id', $downloadObj->content_id );
        $statement->bindParam( ':cd_app_id', $downloadObj->app_id );
        $statement->bindParam( ':cd_promo_id', $downloadObj->promo_id );
        $statement->bindParam( ':cd_package_id', $downloadObj->package_id );
        $statement->bindParam( ':cd_pack_id', $downloadObj->pack_id );
        $statement->bindParam( ':cd_plan_id', $downloadObj->plan_id );
        $statement->bindParam( ':cd_download_date', $downloadObj->cd_download_date );
        $statement->bindParam( ':cd_response_url', $downloadObj->cd_response_url );
        $statement->execute();
		
	}
	
	public function getSmilURL( $downloadObj ){
		
		$queryVideo = "select cm_id, cm_modified_on, cm_title, cm_genre, cm_streaming_url, cm_downloading_url, propertyname,vd_name,cm_thumb_url,fileUrl,cm_vendor,cm_property_id,parentid as cd_id,parentname,content_type_id,content_type,group_concat( delivery_type ) as delivery_type from(  select cm_id,cm_title,cm_vendor,cm_content_type,cm_property_id, cm_streaming_url, cm_downloading_url, cm_genre, cm_modified_on from content_metadata where cm_state = 4 and cm_property_id is not null and cm_starts_from <= NOW() and cm_expires_on >= NOW() and cm_vendor in (:vendor_id) and cm_id = :cd_cmd_id )cm inner join(SELECT cm_id as propertyid ,cm_title as propertyname FROM content_metadata where cm_vendor in (:vendor_id) and  cm_property_id is null and cm_starts_from <= NOW() and cm_expires_on >= NOW() and cm_is_active =1 )prop on(cm.cm_property_id =prop.propertyid)inner join(SELECT vd_id,vd_name FROM icn_vendor_detail where vd_id in (:vendor_id) and  vd_is_active  =1 and vd_starts_on <= NOW() and vd_end_on >= NOW())vd on(cm.cm_vendor =vd.vd_id) inner join (SELECT mct_parent_cnt_type_id,mct_cnt_type_id,mct_delivery_type_id FROM icn_manage_content_type)cnt on (cnt.mct_cnt_type_id = cm.cm_content_type) inner join (select cd_id as parentid,cd_name as parentname from catalogue_detail where cd_name = 'Video')parent on(parent.parentid  = cnt.mct_parent_cnt_type_id)inner join (select cd_id as content_type_id ,cd_name as content_type  from catalogue_detail)subcnt on(subcnt.content_type_id  = cnt.mct_cnt_type_id) inner join (select * from multiselect_metadata_detail ) mmd on (cnt.mct_delivery_type_id=mmd.cmd_group_id) inner join(select cd_name as delivery_type,cd_id as delivery_type_id from catalogue_detail )del on (del.delivery_type_id =mmd.cmd_entity_detail) inner join (SELECT group_concat(cf_url) as fileUrl ,cf_cm_id FROM content_files group by cf_cm_id )cm_files on(cm.cm_id = cm_files.cf_cm_id )left  join (SELECT group_concat(cft_thumbnail_img_browse) as cm_thumb_url ,cft_cm_id FROM content_files_thumbnail group by cft_cm_id )cth on(cm.cm_id = cth.cft_cm_id ) group by cm_id,cm_title,propertyname,vd_name, cm_thumb_url, fileUrl, cm_vendor, cm_property_id, parentid, parentname,content_type_id,content_type order by cm_modified_on desc";
		
		$statement = $this->dbConnection->prepare($queryVideo);
        $statement->bindParam( ':vendor_id', $downloadObj['vendor_id'] );
        $statement->bindParam( ':cd_cmd_id', $downloadObj['cd_cmd_id'] );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
		while($row = $statement->fetch()){
			$videos[] = $row;
		}	
		
		$smil_url = $videos[0]['cm_streaming_url'];
		
		return $smil_url;
	}
 
}