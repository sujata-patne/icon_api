<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/Subscription.php");
require_once(APP."Daos/BaseDao.php");

class SubscriptionDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}

	private function subscriptionDetailsFromRow($row) {
		$subscription = new Subscription();
		$subscription->storeId 			= $row['storeId'];
		$subscription->pss_sp_id 		= $row['pss_sp_id'];
		$subscription->sp_plan_name 	= $row['sp_plan_name'];
		$subscription->sp_caption 		= $row['sp_caption'];
		$subscription->sp_description   = $row['sp_description'];
		$subscription->sp_jed_id 		= $row['sp_jed_id'];
		
		$subscription->unsetValues(array( 'storeId', 'created_on', 'updated_on', 'created_by', 'updated_by'));
		
		return $subscription;
	}
	
	public function getSubscriptionDetailsPackageIds( $packageIds ) {
	 
		$query = "SELECT 
						sp.sp_st_id as storeId,
						pss.pss_sp_id,
						sp.sp_plan_name,
						sp.sp_caption,
						sp.sp_description,
						sp.sp_jed_id
				   FROM  
				   		icn_package_subscription_site pss, 
				   		icn_sub_plan sp
					WHERE 
						FIND_IN_SET(pss.pss_sp_pkg_id, :packageIds ) AND
						ISNULL( pss.pss_crud_isactive ) AND
						sp.sp_is_active = 1 AND
						pss.pss_sp_id = sp.sp_id
					GROUP BY
						pss.pss_sp_id";
						
			
		$statement = $this->dbConnection->prepare($query);	
		$statement->bindParam( ':packageIds', $packageIds );
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$subscription = array();
		
		while($row = $statement->fetch()) {
			$subscription[] = $this->subscriptionDetailsFromRow($row);
		}
		
		return $subscription;
	}
}
?>