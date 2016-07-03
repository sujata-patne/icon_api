<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 13:23
 */

use VOP\Daos\BaseDao;
require_once(APP."Models/ContentFile.php");
require_once(APP."Daos/BaseDao.php");

class ContentFileDao extends BaseDao {
    public function __construct($dbConn) {
        parent::__construct($dbConn);
    }

    function isContentFileExist($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cf_cm_id'];
        $templateId = $data['cf_template_id'];
        $username = $data['cf_name'];
		$isContentFileExist = array();
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
        $isContentFileExist['cf_id'] = (!empty($row['cf_id'])) ? $row['cf_id'] : 0;

        return $isContentFileExist;
    }
    
    function checkContentFileExistForMetadata($jsonData) {
    	$data = (array)($jsonData);
        //echo "<pre>";print_r($jsonData);        echo "<pre>";print_r($data);

        $cm_id = $data['cf_cm_id'];
    	$templateId = $data['cf_template_id'];
    	//$cf_url_base = $data['cf_url_base'];
    	$cf_url = $data['cf_url'];
    	//$cf_absolute_url = $data['cf_absolute_url'];
    	$isContentFileExist = array();
    	$query = "select cf_id from content_files as cf
            where cf.cf_cm_id = :cf_cm_id
            and cf.cf_template_id = :cf_template_id
            and cf.cf_url LIKE :cf_url";
           /* -- and cf.cf_url_base LIKE :cf_url_base
    		-- and cf.cf_absolute_url LIKE :cf_absolute_url";*/
    	  
    	$statement = $this->dbConnection->prepare($query);
    	$statement->bindParam(':cf_cm_id', $cm_id);
    	$statement->bindParam(':cf_template_id', $templateId);
    	//$statement->bindParam(':cf_url_base', $cf_url_base);
    	$statement->bindParam(':cf_url', $cf_url);
    	//$statement->bindParam(':cf_absolute_url', $cf_absolute_url);
    	$statement->execute();
    	$statement->setFetchMode(PDO::FETCH_ASSOC);
    	$row = $statement->fetch();
        //echo "<pre>";print_r($row);
    	$isContentFileExist['cf_id'] = (!empty($row['cf_id'])) ? $row['cf_id'] : 0;
    
    	return $isContentFileExist;
    }

    function getBuildValues($data){        //build the values
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

    function insertContentFiles($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
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
    function updateContentFiles($jsonData) {
    
    	$data = (array)($jsonData);
    	$cf_id = $data['cf_id'];
    //echo "<pre>"; print_r($data);
    	if (count($data) > 0) {
            foreach ($data as $key => $value) {
            	if($key != 'cf_id' && $value != ''){
	                $value = "'$value'";
	                $updates[] = "$key = $value";
            	}
            }
        	$setFields = implode(', ', $updates);
        }else{
        	$setFields = '';
        }
        $query = 'UPDATE content_files SET ' . $setFields .' WHERE cf_id = '.$cf_id;
        $statement = $this->dbConnection->prepare($query);
       // $statement->bindParam(':cf_id', $cf_id);

		//$setFields = "cf_streaming_url = '".$data['cf_streaming_url']."', cf_downloading_url = '".$data['cf_downloading_url']."'";
    	//$query = 'UPDATE content_files SET ' . $setFields .' WHERE cf_id = '.$data['cf_id'];
    	//$statement = $this->dbConnection->prepare($query);
    	if (is_array(array_values($data))) {
    		$result = $statement->execute(array_values($data));
    	} else {
    		$result = $statement->execute(array(':value' => array_values($data)));
    	}
    
    	return $result;
    }

    function updateContentMetadata($jsonData) {

        $data = (array)json_decode(json_encode($jsonData));
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

    function getAllTemplates() {
        $templates = array();
        $query = 'Select * from content_template where ct_param_value in ("bitrate","otherimage","othervideo","otheraudio","app","utf 16", "Preview","Supporting","Main")';
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $statement->fetch()) {
            $templates[$row['ct_param_value']] = $row['ct_group_id'];
        }

        return $templates;
    }
    function getTemplateIdForBitrate() {
        $templates = array();
        $query = "select * from content_template where ct_param_value in ('bitrate')";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $statement->fetch()) {
            //$templates[] = $row;
            $templates[$row['ct_param']] = $row['ct_group_id'];

        }

        return $templates;
    }
    
    function getTemplateIdForHeightWidth($data) {
        $data = (array)($data);
       // echo "<pre>"; print_r($data);
    	$templates = array();
    	$height = $data['height'];
    	$width = $data['width']; 
    	
    	$query = "select heightgroupid as ct_group_id  from 
			( select ct_param as width,ct_group_id as widthgroupid from content_template where ct_param_value = 'width' and ct_param = :width )width 
			inner join ( select ct_param as height,ct_group_id as heightgroupid from content_template where ct_param_value = 'height'  and ct_param = :height )height 
			on(width.widthgroupid =height.heightgroupid)";
    	$statement = $this->dbConnection->prepare($query);
    	$statement->bindParam(':height', $height);
    	$statement->bindParam(':width', $width);
    	 
    	$statement->execute();
    	$statement->setFetchMode(PDO::FETCH_ASSOC);
    	$row = $statement->fetch();
     	//$templates[$row['ct_param']] = $row['ct_group_id'];    	
    	$templates = $row['ct_group_id'];
    	 
    	return $templates;
    }

    function getTemplateIdForLanguage($jsonData) {
        $data = (array)json_decode(json_encode($jsonData));
        $cm_id = $data['cf_cm_id'];
        $templates = array();
        $query = "select distinct ct_param_value as language, ct_group_id as templateId from content_metadata as meta
            inner join multiselect_metadata_detail as mlm on(mlm.cmd_group_id = meta.cm_language OR mlm.cmd_group_id = meta.cm_lyrics_languages)
            inner join catalogue_detail as cd on(cd.cd_id = cmd_entity_detail)
            inner join catalogue_master as cm on(cm.cm_id =cd.cd_cm_id  and cm_name in (\"Languages\"))
            inner join content_template as ct on(ct.ct_param =  cd.cd_id and ct.ct_param_value = cd.cd_name)
            where meta.cm_id = ".$cm_id;
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':cm_id', $cm_id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        //$row = $statement->fetch();
        //echo "<pre>"; print_r($row);
        //$templates['templateId'] = (!empty($row['templateId'])) ? $row['templateId'] : 0;
        while ($row = $statement->fetch()) {
            $templates[$row['language']] = $row['templateId']; //[$row['language']]
        }
        //echo "<pre>"; print_r($templates);
        return $templates;
    }

    function getMaxCFId() {
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
        $maxChildId['maxChildId'] = (!empty($row['maxChildId'])) ? $row['maxChildId'] : 0;
        return $maxChildId;
    }

    public function getContentDeliveryTypesById( $cmdId ){
        echo $query = "SELECT cm_id, cm_content_type, cd_id, cd_name from
                (SELECT cm_content_type, cm_id FROM content_metadata WHERE cm_id = ".$cmdId.") cm
                INNER JOIN (SELECT mct_delivery_type_id, mct_cnt_type_id FROM icn_manage_content_type) cnt ON (cnt.mct_cnt_type_id = cm.cm_content_type)
                INNER JOIN (SELECT cmd_group_id, cmd_entity_detail FROM multiselect_metadata_detail ) mmd ON (cnt.mct_delivery_type_id = mmd.cmd_group_id)
                INNER JOIN (SELECT cd_id, cd_name FROM catalogue_detail ) cd ON ( cd.cd_id = mmd.cmd_entity_detail)" ;
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':cmdId',  $cmdId );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $contentMetaData = array();
        while ($row = $statement->fetch()) {
            $contentMetaData[] = $row;
        }

        return $contentMetaData;

    }
	
	function updateContentFilePaths($contentFiles) {
		
		
		for($i=0;$i<count($contentFiles);$i++){	

			$previewURL 	= isset($contentFiles[$i]['PreviewURL']) 	 ? $contentFiles[$i]['PreviewURL']     : '';
			$downloadingURL = isset($contentFiles[$i]['DownloadingURL']) ? $contentFiles[$i]['DownloadingURL'] : '';
			$fileId       = $contentFiles[$i]['FileId'];
					
			$setFields = "cf_streaming_url = '".$previewURL."', cf_downloading_url = '".$downloadingURL."'";
			$query = 'UPDATE content_files SET ' . $setFields .' WHERE cf_id = '.$fileId;
				
			$statement = $this->dbConnection->prepare($query);
			$statement = $this->dbConnection->prepare($query);
			$statement->execute();			
			$count = $statement->rowCount();	
		}	

		return $count; 
		
	}	

}