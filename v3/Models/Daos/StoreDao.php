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
                  FROM icon_cms.icn_store AS st
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
                  FROM icon_cms.icn_store AS st
                  JOIN icon_cms.multiselect_metadata_detail AS mmd ON st.st_payment_channel = mmd.cmd_group_id
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
                  FROM icon_cms.icn_store AS st
                  left JOIN icon_cms.multiselect_metadata_detail AS mmd ON st.st_payment_type= mmd.cmd_group_id
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
                  FROM icon_cms.icn_store AS st
                  JOIN icon_cms.multiselect_metadata_detail AS mmd ON st.st_content_type= mmd.cmd_group_id
                  JOIN icon_cms.catalogue_detail AS ctl ON mmd.cmd_entity_detail = ctl.cd_id
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
                  FROM icon_cms.icn_store st
                  join icon_cms.multiselect_metadata_detail mlm ON mlm.cmd_group_id = st.st_front_type
                  join icon_cms.catalogue_detail clt ON clt.cd_id = mlm.cmd_entity_detail
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
}