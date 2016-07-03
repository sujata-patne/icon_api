<?php

use VOP\Daos\BaseDao;
require_once(APP."Models/Tune.php");
require_once(APP."Daos/BaseDao.php");

class TuneDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}
	
	public function getTunesByOperator($data){
		
		$query = "select SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cf.cf_id as TuneID,cmd.cm_language as Language,cf.cf_url as SongFullPath,om.om_operator_name as Operator,cd2.cd_id as CelebrityID,cd2.cd_name as CelebrityName
			FROM icon_cms.content_metadata as cmd 
			INNER JOIN icon_cms.catalogue_detail cd1 on cmd.cm_song_type = cd1.cd_id 
			INNER JOIN icon_cms.catalogue_master cm1 on (cd1.cd_cm_id = cm1.cm_id AND cm1.cm_name in ('Song Type'))
			INNER JOIN icon_cms.content_files cf on cf.cf_cm_id = cmd.cm_id
			JOIN icon_cms.multiselect_metadata_detail as mmd on (mmd.cmd_group_id = cmd.cm_celebrity)
			INNER JOIN icon_cms.catalogue_master cm2 
			INNER JOIN icon_cms.catalogue_detail cd2 on (cd2.cd_cm_id = cm2.cm_id AND cd2.cd_id = mmd.cmd_entity_detail)
			INNER JOIN vcode_operator vo on vo.vo_cf_id = cf.cf_id
			INNER JOIN operator_master om on om.om_id = vo.vo_operator_id
			WHERE cm2.cm_name in ('Celebrity') AND
			cf.cf_name LIKE :username AND
			om.om_operator_name LIKE :operator";
		
		$TunesByOperator = array();
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':username',  $data->UserName );
		$statement->bindParam( ':operator', $data->Operator );
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			$TunesByOperator[] = $row;
		}
		
		return $TunesByOperator;	
	}
	
	
	public function getTunesByCelebrity($data){
		
		$query = "select SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cf.cf_id as TuneID,cmd.cm_language as Language,cf.cf_url as SongFullPath,om.om_operator_name as Operator,cd2.cd_id as CelebrityID,cd2.cd_name as CelebrityName
			FROM icon_cms.content_metadata as cmd 
			INNER JOIN icon_cms.catalogue_detail cd1 on cmd.cm_song_type = cd1.cd_id 
			INNER JOIN icon_cms.catalogue_master cm1 on (cd1.cd_cm_id = cm1.cm_id AND cm1.cm_name in ('Song Type'))
			INNER JOIN icon_cms.content_files cf on cf.cf_cm_id = cmd.cm_id
			JOIN icon_cms.multiselect_metadata_detail as mmd on (mmd.cmd_group_id = cmd.cm_celebrity)
			INNER JOIN icon_cms.catalogue_master cm2 
			INNER JOIN icon_cms.catalogue_detail cd2 on (cd2.cd_cm_id = cm2.cm_id AND cd2.cd_id = mmd.cmd_entity_detail)
			INNER JOIN vcode_operator vo on vo.vo_cf_id = cf.cf_id
			INNER JOIN operator_master om on om.om_id = vo.vo_operator_id
			WHERE cm2.cm_name in ('Celebrity') AND
			cf.cf_name LIKE :username AND
			cd2.cd_name LIKE :celebrity";
		
		$TunesByOperator = array();
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':username',  $data->UserName );
		$statement->bindParam( ':celebrity', $data->CelebrityName );
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			$TunesByOperator[] = $row;
		}
		
		return $TunesByOperator;
		
	}
	
	public function getTunesByName($data){
		
		$query = "SELECT DISTINCT SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cf.cf_id as TuneID,cmd.cm_language as Language,cf.cf_url as  				SongFullPath,om.om_operator_name as OperatorName,om.om_id as OperatorID,cd2.cd_id as CelebrityID,cd2.cd_name as CelebrityName FROM icon_cms.content_metadata as cmd 
			INNER JOIN icon_cms.catalogue_detail cd1 on cmd.cm_song_type = cd1.cd_id 
			INNER JOIN icon_cms.catalogue_master cm1 on (cd1.cd_cm_id = cm1.cm_id AND cm1.cm_name in ('Song Type'))
			INNER JOIN icon_cms.content_files cf on cf.cf_cm_id = cmd.cm_id
			JOIN icon_cms.multiselect_metadata_detail as mmd on (mmd.cmd_group_id = cmd.cm_celebrity)
			INNER JOIN icon_cms.catalogue_master cm2 
			INNER JOIN icon_cms.catalogue_detail cd2 on (cd2.cd_cm_id = cm2.cm_id AND cd2.cd_id = mmd.cmd_entity_detail)
			INNER JOIN vcode_operator vo on vo.vo_cf_id = cf.cf_id
			INNER JOIN operator_master om on om.om_id = vo.vo_operator_id
			WHERE cf.cf_name LIKE :username";
				
		$TunesByName = array();
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':username',  $data->UserName );
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			$TunesByName[] = $row;
		}
		
		return $TunesByName;
		
	}
	
	
	public function getTunesByUsernameOperatorCelebrity($data){
		$query = "select SUBSTRING_INDEX(cf.cf_url, '/', -1) AS TuneName,cf.cf_id as TuneID,cmd.cm_language as Language,cf.cf_url as SongFullPath,om.om_id as OperatorID,om.om_operator_name as OperatorName,cd2.cd_id as CelebrityID,cd2.cd_name as CelebrityName
			FROM icon_cms.content_metadata as cmd 
			INNER JOIN icon_cms.catalogue_detail cd1 on cmd.cm_song_type = cd1.cd_id 
			INNER JOIN icon_cms.catalogue_master cm1 on (cd1.cd_cm_id = cm1.cm_id AND cm1.cm_name in ('Song Type'))
			INNER JOIN icon_cms.content_files cf on cf.cf_cm_id = cmd.cm_id
			JOIN icon_cms.multiselect_metadata_detail as mmd on (mmd.cmd_group_id = cmd.cm_celebrity)
			INNER JOIN icon_cms.catalogue_master cm2 
			INNER JOIN icon_cms.catalogue_detail cd2 on (cd2.cd_cm_id = cm2.cm_id AND cd2.cd_id = mmd.cmd_entity_detail)
			INNER JOIN vcode_operator vo on vo.vo_cf_id = cf.cf_id
			INNER JOIN operator_master om on om.om_id = vo.vo_operator_id
			WHERE cm2.cm_name in ('Celebrity') AND
			cf.cf_name LIKE :username AND
			om.om_operator_name LIKE :operator
            and cd2.cd_name like :celebrity";

		$TuneDetails = array();
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':username',  $data->UserName );
		$statement->bindParam( ':operator',  $data->Operator );
		$statement->bindParam( ':celebrity',  $data->CelebrityName );
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			$TuneDetails[] = $row;
		}
		return $TuneDetails;			
		
	}
	
	
	public function getTuneID($data){
		
		$query = "select vo.vo_vcode,vo.vo_operator_id FROM vcode_operator vo 
			INNER JOIN content_files cf on cf.cf_id = vo.vo_cf_id
			WHERE cf.cf_id = :tune_id";
		
		$TuneID = array();
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':tune_id',  $data->TuneID );
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		while($row = $statement->fetch()) {
			$TuneID[] = $row;
		}
		
		return $TuneID;
		
	}
	
}


?>