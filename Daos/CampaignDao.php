<?php
use VOP\Daos\BaseDao;
require_once(APP."Models/Campaign.php");
require_once(APP."Daos/BaseDao.php");

class CampaignDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}
	
	private function campaignFromRow($row) {
		$campaign = new Campaign();
		
		$campaign->cGFlag 			= $row['cp_cg_direct_flag'];
		$campaign->campaignVendor   = $row['ca_client_name'];
		$campaign->campaignName 	= str_replace(' ', '_', $row['cp_promo_title']);
		
		return $campaign;
	}
	
	public function getCampaignDetailsByPromoId( $promoId ){
		$campaign = array();
		
		if(ctype_digit($promoId)){
				
			$query = "SELECT 
							* 
					   FROM 
							cm_promo_detail as cpd
					   JOIN
							cm_promo_cg_details as cpcd ON ( cpd.cp_banner_id = cpcd.cg_promo_id )
					   JOIN	
							cm_ad_client as cmac ON ( cpd.cp_ad_client_id = cmac.ca_client_id )
						WHERE 
							cpd.cp_promo_id = :promoId ";
	
			
			$statement = $this->dbConnection->prepare($query);
			$statement->bindParam( ':promoId', $promoId );
			$statement->execute();
			$statement->setFetchMode(PDO::FETCH_ASSOC);
			
			if (($row = $statement->fetch()) != FALSE) {
				$campaign = $row;
			}		
		}
		
		return $campaign;

	}
	
	public function getCampaignDetailsByPromoIdByStoreId( $promoId, $storeId ){
		
		$campaign = array();
		
		$row = array(
				'cp_cg_direct_flag' => 0,
				'ca_client_name' 	=> $storeId,
				'cp_promo_title'	=> $storeId
		);
		
		if( ctype_digit( $promoId ) ){
	
			$query = "SELECT 
							*, REPLACE(cp_promo_title,' ', '_') as cp_promo_title
						FROM 
							cm_promo_detail as A, 
							cm_promo_cg_details as B, 
							cm_ad_client as C 
						WHERE 
							A.cp_banner_id = B.cg_promo_id AND 
							A.cp_ad_client_id = C.ca_client_id AND 
							A.cp_app_id = :storeId AND 
							A.cp_promo_id = :promoId ";
			
			$statement = $this->dbConnection->prepare($query);
			$statement->bindParam( ':storeId', $storeId );
			$statement->bindParam( ':promoId', $promoId );
			$statement->execute();
			$statement->setFetchMode(PDO::FETCH_ASSOC);
	
			$rowCount  = $statement->rowCount();
				
			if( $rowCount > 0 ){
				if (($row = $statement->fetch()) != FALSE) {
					//$campaign = $this->campaignFromRow($row);
					$campaign = $row;
				}
			}else{
				//$campaign = $this->campaignFromRow($row);
				$campaign = $row;
			}
		}else{
			//$campaign = $this->campaignFromRow($row);
			$campaign = $row;
		}
		
		return $campaign;
	}
}
?>

