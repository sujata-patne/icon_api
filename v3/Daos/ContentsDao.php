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
        $data = (array)$jsonData;
        $cm_id = $data['cm_id'];
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                if($key != 'cm_id' && $value != ''){
                    $value = "'$value'";
                    $updates[] = "$key = $value";
                }
            }
            $setFields = implode(', ', $updates);
        }else{
            $setFields = '';
        }
        /*if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $value = "'$value'";
                $updates[] = "$key = $value";
            }
        }
        $setFields = implode(', ', $updates);*/

        $query = 'UPDATE content_metadata SET ' . $setFields .' WHERE cm_id = '.$cm_id;
        $statement = $this->dbConnection->prepare($query);
        // $statement->bindParam(':cm_id', $cm_id);
        if (is_array(array_values($data))) {
            $result = $statement->execute(array_values($data));
        } else {
            $result = $statement->execute(array(':value' => array_values($data)));
        }

        return $result;
    }

    function updateContentMetadata123($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cm_id'];
        $cm_state = $data['cm_state'];
        $cm_streaming_url = ($data['cm_streaming_url'] != '')? $data['cm_streaming_url']:'';
        $cm_downloading_url = ($data['cm_downloading_url'] != '')? $data['cm_downloading_url']:'';

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
        /*echo $query = "SELECT cm_id, cm_content_type, cd_id, cd_name from
                (SELECT cm_content_type, cm_id FROM content_metadata WHERE cm_id = ".$cmdId.") cm
                INNER JOIN (SELECT mct_delivery_type_id, mct_cnt_type_id FROM icn_manage_content_type) cnt ON (cnt.mct_cnt_type_id = cm.cm_content_type)
                INNER JOIN (SELECT cmd_group_id, cmd_entity_detail FROM multiselect_metadata_detail ) mmd ON (cnt.mct_delivery_type_id = mmd.cmd_group_id)
                INNER JOIN (SELECT cd_id, cd_name FROM catalogue_detail ) cd ON ( cd.cd_id = mmd.cmd_entity_detail)" ;*/
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
        //echo "<pre>"; print_r(count($statement->fetch())); exit;
        while ($row = $statement->fetch()) {
            //if ($row['cm_id'] != null){
                $contentMetaData[] = $row;
            //}else {}
        }

        return $contentMetaData;

    }
    public function getContentMetadataBycmId( $cmdId ){
    	$query = "select * from content_metadata where cm_id = :cmdId";
    	     	
    	$statement = $this->dbConnection->prepare($query);
    	$statement->bindParam( ':cmdId',  $cmdId );
    	$statement->execute();
    	$statement->setFetchMode(PDO::FETCH_ASSOC);
    
    	$contentMetadata = array();
        $contentMetadata = $statement->fetch();
    	//$contentMetadata = (!empty($row['cm_id'])) ? $row : null;
    	 
    	return $contentMetadata;
    
    }
    function isContentMetadataExist($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cm_id'];

         $query = "select count(cm_id) as cm_id from content_metadata
            where cm_id = :cm_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $isContentMetadataExist['cm_id'] = (!empty($row['cm_id'])) ? $row['cm_id'] : 0;

        return $isContentMetadataExist;
    }
    function checkIsBulkUploadAllowed($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cm_id'];

         $query = "select cm_ispersonalized from content_metadata
            where cm_id = :cm_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $checkIsBulkUploadAllowed['cm_ispersonalized'] = (!empty($row['cm_ispersonalized'])) ? $row['cm_ispersonalized'] : 0;

        return $checkIsBulkUploadAllowed;
    }

    function getMetadataStatus($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cm_id'];

        $query = "select cm_state from content_metadata where cm_id = :cm_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $metadataStatus['cm_state'] = (!empty($row['cm_state'])) ? $row['cm_state'] : 0;

        return $metadataStatus;
    }

}