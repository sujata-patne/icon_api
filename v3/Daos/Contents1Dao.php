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

    function isContentFileExist($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cf_cm_id'];
        $templateId = $data['cf_template_id'];
        $username = $data['cf_name'];

        $query = "select count(cf_id) as cf_id from content_files as cf
            where cf.cf_cm_id = :cf_cm_id
            and cf.cf_template_id = :cf_template_id
            and cf.cf_name LIKE :cf_name";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cf_cm_id', $cm_id);
        $statement->bindParam(':cf_template_id', $templateId);
        $statement->bindParam(':cf_name', $username);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $isContentFileExist = (!empty($row['cf_id'])) ? $row['cf_id'] : 0;

        return $isContentFileExist;
    }

    function getBuildValues($data){
        //build the values
        $buildValues = '';
        if (is_array(array_values($data))) {            //loop through all the fields
            foreach (array_values($data) as $key => $value) {
                if ($key == 0) {                 //first item
                    $buildValues .= '?';
                } else {                 //every other item follows with a ","
                    $buildValues .= ', ?';
                }
            }
        } else {            //we are only inserting one field
            $buildValues .= ':value';
        }
        return $buildValues;
    }

    function getBuildFields($data){
        $buildFields = '';       //build the fields
        if (is_array(array_keys($data))) {            //loop through all the fields
            foreach (array_keys($data) as $key => $field) {
                if ($key == 0) {
                    //first item
                    $buildFields .= $field;
                } else {                    //every other item follows with a ","
                    $buildFields .= ', ' . $field;
                }
            }
        } else {
            //we are only inserting one field
            $buildFields .= $data;
        }
        return $buildFields;
    }

    function bindFields($fields){
        end($fields); $lastField = key($fields);
        $bindString = ' ';
        foreach($fields as $field => $data){
            $bindString .= $field . '=:' . $field;
            $bindString .= ($field === $lastField ? ' ' : ',');
        }
        return $bindString;
    }

    function insertContentFiles($jsonData)
    {
        $data = (array)json_decode(json_encode($jsonData));
        //echo "<pre>"; print_r($this->bindFields($data));
        $buildFields = $this->getBuildFields($data);
        $buildValues = $this->getBuildValues($data);
        $query = 'INSERT INTO content_files (' . $buildFields . ') VALUES (' . $buildValues . ')';
        $statement = $this->dbConnection->prepare($query);
        if (is_array(array_values($data))) {
            $result = $statement->execute(array_values($data));
        } else {
            $result = $statement->execute(array(':value' => array_values($data)));
        }

        return $result;
    }

    function getTemplateIdForLanguage($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cf_cm_id'];
        $templates = array();
        $query = "select ct_param_value as language, ct_group_id as templateId from content_metadata as meta
            inner join multiselect_metadata_detail as mlm on(mlm.cmd_group_id = meta.cm_language)
            inner join catalogue_detail as cd on(cd.cd_id = cmd_entity_detail)
            inner join catalogue_master as cm on(cm.cm_id =cd.cd_cm_id  and cm_name in (\"Languages\"))
            inner join content_template as ct on(ct.ct_param =  cd.cd_id and ct.ct_param_value = cd.cd_name)
            where meta.cm_id = ".$cm_id;
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        //echo "<pre>"; print_r($row);
        $templates['templateId'] = (!empty($row['templateId'])) ? $row['templateId'] : 0;
        /* while ($row = $statement->fetch()) {
            $templates = $row['templateId']; //[$row['language']]
        }*/
        return $templates;
    }

    function getMaxCFId()
    {
        $query = 'SELECT MAX(cf_id) as cf_id FROM content_files';
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $cfId = (!empty($row)) ? $row['cf_id'] : 0;
        return $cfId;
    }

    function getMaxMetaContentId($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cmId = $data['cf_cm_id'];
        $maxChildId = array();
        //$templateId = $data['cf_template_id'];
        $query = 'SELECT MAX(cf_name_alias) AS maxChildId FROM content_files where cf_cm_id = :cmId ';
        //AND cf_template_id = :templateId ';
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cmId', $cmId);
        //$statement->bindParam(':templateId', $templateId);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
       // echo "<pre>"; print_r($row); exit;
        $maxChildId['maxChildId'] = (!empty($row['maxChildId'])) ? $row['maxChildId'] : 0;
        return $maxChildId;
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