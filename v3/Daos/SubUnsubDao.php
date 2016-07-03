<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/SubUnsub.php");
require_once(APP."Daos/BaseDao.php");

class SubUnsubDao extends BaseDao {

	public function __construct($dbConn)
	{
		parent::__construct($dbConn);
	}

	public function unsubscribe( $subUnsubObj ){
		
		echo '<pre>';var_dump($subUnsubObj);exit;
		
		$$query = "INSERT INTO icon_cms.common_consent_gw_log
				  SET msisdn   = :msisdn,
					  operator = :operator,
					  tuneid   = '',
					  returl   = :returl,
					  flreturl = :flreturl,
				      status   = :status";

		$statement = $this->dbConnection->prepare($query);

		$statement->bindParam( ':msisdn',  $data->msisdn );
		$statement->bindParam( ':operator', $data->operator );
		$statement->bindParam( ':returl', $data->refererurl );
		$statement->bindParam( ':flreturl', $data->furl );
		$statement->bindParam( ':status', $data->status );

		$result = $statement->execute();

		return $result;

	}
}
?>