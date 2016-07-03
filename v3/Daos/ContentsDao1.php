<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 13:23
 */

use VOP\Daos\BaseDao;
require_once(APP."Models/Contents.php");
require_once(APP."Daos/BaseDao.php");

class ContentsDao extends BaseDao {
    public function __construct($dbConn) {
        parent::__construct($dbConn);
    }

    function updateContentMetadata($jsonData) {

        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cm_id'];
        $cm_state = $data['cm_state'];
        $cm_streaming_url = $data['cm_streaming_url'];
        $cm_downloading_url = $data['cm_downloading_url'];

        $query = 'UPDATE content_metadata SET cm_id = :cm_id, cm_state = :cm_state, cm_streaming_url = :cm_streaming_url, cm_downloading_url = :cm_downloading_url WHERE cm_id = :cm_id';
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->bindParam(':cm_state', $cm_state);
        $statement->bindParam(':cm_streaming_url', $cm_streaming_url);
        $statement->bindParam(':cm_downloading_url', $cm_downloading_url);
        $result = $statement->execute();
        return $result;
    }

    public function getContentDeliveryTypesById( $cmdId ){

        $query = "SELECT cm_id, cm_content_type, cd_id, cd_name from
                (SELECT cm_content_type, cm_id FROM content_metadata WHERE cm_id = :cmdId) cm
                INNER JOIN (SELECT mct_delivery_type_id, mct_cnt_type_id FROM icn_manage_content_type) cnt ON (cnt.mct_cnt_type_id = cm.cm_content_type)
                INNER JOIN (SELECT cmd_group_id, cmd_entity_detail FROM multiselect_metadata_detail ) mmd ON (cnt.mct_delivery_type_id = mmd.cmd_group_id)
                INNER JOIN (SELECT cd_id, cd_name FROM catalogue_detail ) cd ON ( cd.cd_id = mmd.cmd_entity_detail)" ;
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':cmdId',  $cmdId );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $contentMetaData = array();
        while ($row = $statement->fetch()) {

        //if (($row = $statement->fetch()) != FALSE) {
            $contentMetaData[] = $row;
        }

        return $contentMetaData;

    }

}