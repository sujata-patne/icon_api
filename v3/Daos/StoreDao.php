<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 13:23
 */

use VOP\Daos\BaseDao;
require_once(APP."Models/Store.php");
require_once(APP."Daos/BaseDao.php");

class StoreDao extends BaseDao {
    public function __construct($dbConn) {
        parent::__construct($dbConn);
    }
    public function getStoreDetailsByStoreId( $storeId ) {
        $storeDetails = array();

        $query = "SELECT st.st_id, st.st_name, st.st_url
                  FROM icn_store AS st
				  WHERE st.st_id = :storeId
						AND ISNULL( st.st_crud_isactive )
				  ORDER BY st.st_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        while($row = $statement->fetch()) {
            $storeDetails[] = $row;
        }
        return $storeDetails;
    }
    public function getPaymentChannelsByStoreId( $storeId ) {
        $paymentChannel = array();

        $query = "SELECT GROUP_CONCAT(mmd.cmd_entity_detail SEPARATOR ', ') AS paymentChannel
                  FROM icn_store AS st
                  JOIN multiselect_metadata_detail AS mmd ON st.st_payment_channel = mmd.cmd_group_id
				  WHERE st.st_id = :storeId
						AND ISNULL( st.st_crud_isactive )
				  ORDER BY st.st_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        /*while($row = $statement->fetch()) {
            $paymentChannel[] = $row;
        }*/
        return $statement->fetch(); //$paymentChannel;
    }
    public function getPaymentTypeByStoreId( $storeId ) {
        $paymentTypes = array();

        $query = "SELECT GROUP_CONCAT(mmd.cmd_entity_detail SEPARATOR ', ') AS paymentType
                  FROM icn_store AS st
                  left JOIN multiselect_metadata_detail AS mmd ON st.st_payment_type= mmd.cmd_group_id
				  WHERE st.st_id = :storeId
						AND ISNULL( st.st_crud_isactive )
				  ORDER BY st.st_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

       /* while($row = $statement->fetch()) {
            $paymentTypes[] = $row;
        }*/
        return $statement->fetch(); //$paymentTypes;
    }
    public function getContentTypeByStoreId( $storeId ) {
        $contentTypes = array();

        $query = "SELECT group_concat(mmd.cmd_entity_detail SEPARATOR ',') AS contentId, group_concat(ctl.cd_display_name SEPARATOR ',') AS contentType
                  FROM icn_store AS st
                  JOIN multiselect_metadata_detail AS mmd ON st.st_content_type= mmd.cmd_group_id
                  JOIN catalogue_detail AS ctl ON mmd.cmd_entity_detail = ctl.cd_id
				  WHERE st.st_id = :storeId
						AND ISNULL( st.st_crud_isactive )
				  ORDER BY st.st_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        /*while($row = $statement->fetch()) {
            $contentTypes[] = $row;
        }*/
        return $statement->fetch(); //$contentTypes;
    }
    public function getDistributionChannelByStoreId( $storeId ) {
        $distributionChannel = array();

        $query = "SELECT group_concat(mlm.cmd_entity_detail SEPARATOR ', ') AS dChannelId, group_concat(clt.cd_name SEPARATOR ', ') AS dChannelName
                  FROM icn_store st
                  join multiselect_metadata_detail mlm ON mlm.cmd_group_id = st.st_front_type
                  join catalogue_detail clt ON clt.cd_id = mlm.cmd_entity_detail
				  WHERE st.st_id = :storeId
						AND ISNULL( st.st_crud_isactive )
				  ORDER BY st.st_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        /*while($row = $statement->fetch()) {
            $distributionChannel[] = $row;
        }*/
        return $statement->fetch(); //$distributionChannel;
    }
    public function getCGImagesByStoreId( $storeId ) {
        $storeDetails = array();

        $query = "SELECT cg.pci_sp_pkg_id, cg.pci_cg_img_browse as cg_images, cg.pci_image_size
                  FROM icn_store AS st
                  JOIN icn_store_package AS sp ON sp.sp_st_id = st.st_id
                  JOIN icn_package_cg_image AS cg ON sp.sp_pkg_id =cg.pci_sp_pkg_id
				  WHERE st.st_id = :storeId
						AND cg.pci_is_default = 1
						AND ISNULL( st.st_crud_isactive )
						AND ISNULL( sp.sp_crud_isactive )
						AND ISNULL( cg.pci_crud_isactive )
				  ORDER BY sp.sp_pkg_id ";

        $statement = $this->dbConnection->prepare($query);

        $statement->bindParam( ':storeId', $storeId );

        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        while($row = $statement->fetch()) {
            $storeDetails[] = $row;
        }
        return $storeDetails;
    }
    public function getSubscriptionPricePoints( $storeId, $operatorId ) {
        $length = strlen($operatorId);
        $query = "SELECT spl.sp_jed_id, spl.sp_caption, dscl.dcl_disclaimer,ipas.pas_arrange_seq,
spl.sp_st_id as storeId, pss.pss_sp_id, spl.sp_plan_name, spl.sp_description, SUBSTRING(dcl_partner_id, 1, ".$length.") as dcl_partner_id
		          FROM icn_store_package AS sp
                  JOIN icn_disclaimer AS dscl ON ( dscl.dcl_st_id = sp.sp_st_id )
				  JOIN icn_package_subscription_site AS pss ON (pss.pss_sp_pkg_id = sp.sp_pkg_id )
				  JOIN icn_sub_plan AS spl ON (spl.sp_id = pss.pss_sp_id AND dscl.dcl_ref_jed_id = spl.sp_jed_id )
				  JOIN icn_package_arrange_sequence AS ipas ON (ipas.pas_sp_pkg_id = pss.pss_sp_pkg_id and ipas.pas_plan_id = spl.sp_id)
				  WHERE spl.sp_is_active = 1
				  AND pss.pss_is_active = 1
				  AND spl.sp_crud_isactive IS NULL
				  AND sp.sp_crud_isactive IS NULL
				  AND pss.pss_crud_isactive IS NULL
				  AND dscl.dcl_crud_isactive IS NULL
				  AND spl.sp_st_id = :storeId
				  AND sp.sp_pkg_type = 0
				  AND sp.sp_parent_pkg_id = 0
				  AND ipas.pas_plan_type = 'Subscription'
				  GROUP BY dcl_id
				  HAVING BINARY dcl_partner_id = :operatorId
				  ORDER BY ipas.pas_arrange_seq,spl.sp_jed_id ASC";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':storeId', $storeId );
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