<?php


use VOP\Daos\BaseDao;
require_once(APP."Models/Logger.php");
require_once(APP."Daos/BaseDao.php");

class LoggerDao extends BaseDao
{

	/*public function CreateTuneLog($data){
		
		$query = "INSERT INTO icon_cms.common_consent_gw_log
				  SET msisdn = :msisdn,
					  operator = :operator,
					  tuneid = :tuneid,
					  returl = :returl,
					  flreturl = :flreturl,
				      status = :status";

		$statement = $this->dbConnection->prepare($query);

		$statement->bindParam( ':msisdn',  $data->msisdn );
		$statement->bindParam( ':operator', $data->operator );
		$statement->bindParam( ':tuneid', $data->tuneid );
		$statement->bindParam( ':returl', $data->refererurl );
		$statement->bindParam( ':flreturl', $data->furl );
		$statement->bindParam( ':status', $data->status );

		$result = $statement->execute();

		return $result;
		
	}*/
}