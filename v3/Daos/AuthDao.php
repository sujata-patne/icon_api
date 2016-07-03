<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 03/31/16
 * Time: 10:19 AM
 */

use VOP\Daos\BaseDao;
require_once(APP."Models/Auth.php");
require_once(APP."Daos/BaseDao.php");

class AuthDao extends BaseDao
{

    public function __construct($dbConn){
        parent::__construct($dbConn);
    }

    public function getEligibility($authObj){
        $query_eligibleMSISDN = " SELECT * FROM icon_cms.billing_details                   
                            WHERE msisdn = :msisdn
                            AND status IN ('ACTIVE','PARKING','GRACE')";

        $statement = $this->dbConnection->prepare($query_eligibleMSISDN);
        $statement->bindParam( ':msisdn', $authObj->msisdn );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $eligibleMSISDN = $statement->fetch();

       if(isset($eligibleMSISDN) && $eligibleMSISDN != ''){         // subscribed user

           $query_allowedDownloadsOfPlan = "SELECT vp.vp_download_limit FROM icn_valuepack_plan vp WHERE vp.vp_id = :plan_id";

           $statement = $this->dbConnection->prepare($query_allowedDownloadsOfPlan);
           $statement->bindParam( ':plan_id', $eligibleMSISDN['plan_id'] );
           $statement->execute();
           $statement->setFetchMode(PDO::FETCH_ASSOC);

           while($row = $statement->fetch()) {      //allowed max download contents = 12
               $allowedDownloadsOfPlan = $row['vp_download_limit'];
           }

           $query_downloadsByUserForPlan = "SELECT SUM(cd.cd_download_count) as UserDownloads FROM site_user.content_download cd
                                            INNER JOIN icon_cms.billing_details bd ON bd.msisdn = cd.cd_msisdn
                                            WHERE bd.msisdn = :msisdn
                                            AND cd.cd_plan_id = bd.plan_id
                                            AND cd.cd_download_date BETWEEN bd.subscription_date AND bd.next_renewal_date
                                            GROUP BY cd.cd_id";

           $statement = $this->dbConnection->prepare($query_downloadsByUserForPlan);
           $statement->bindParam( ':msisdn', $eligibleMSISDN['msisdn'] );
           $statement->execute();
           $statement->setFetchMode(PDO::FETCH_ASSOC);

		   $downloadsByUserForPlan = '';
		   
           while($row = $statement->fetch()) {      //allowed max download contents = 3
               $downloadsByUserForPlan = $row['UserDownloads'];
           }

           if($downloadsByUserForPlan == 0){
               $status = 'Eligible';
           }else{
               if($downloadsByUserForPlan < $allowedDownloadsOfPlan){
                   $status = 'Eligible';
               }else{
                   $status = 'Not Eligible';
               }
           }

       }else{                      // visitor = not eligible for any plans
           $status = 'Not Eligible';
       }

        return $status;


    }

    public function getUserStatusforSubscription( $authObj ){

		$query = "SELECT bd.plan_id as Plan_ID,bd.package_id as Package_ID,bd.`status` as User_Status FROM billing_details bd 
				  WHERE bd.msisdn = :msisdn and bd.plan_type = 'Subscription'";
		
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':msisdn', $authObj->msisdn );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
		$userStatusforSubscriptionPlans = $statement->fetchall();

        return $userStatusforSubscriptionPlans;
    }
	

    public function getUserStatusforValuePack( $authObj ){

        $query = "SELECT bd.plan_id as Plan_ID,bd.package_id as Package_ID,bd.`status` as User_Status FROM billing_details bd 
				  WHERE bd.msisdn = :msisdn and bd.plan_type = 'Valuepack'";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':msisdn', $authObj->msisdn );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
		$userStatusforValuePackPlans = $statement->fetchall();
		
        return $userStatusforValuePackPlans;
    }

    public function getUserStatusforOffer( $authObj ){

        $query = "SELECT bd.plan_id as Plan_ID,bd.package_id as Package_ID,bd.`status` as User_status FROM billing_details bd 
				  WHERE bd.msisdn = :msisdn and bd.plan_type = 'Offerplan'";;

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':msisdn', $authObj->msisdn );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $userStatusforOfferPlans = $statement->fetchAll();

        return $userStatusforOfferPlans;
    }

    public function getUserStatusforAlacart( $authObj ){

        $query = "SELECT bd.plan_id as Plan_ID,bd.package_id as Package_ID,bd.`status` as User_Status FROM billing_details bd 
				  WHERE bd.msisdn = :msisdn and bd.plan_type = 'Alacarte'";;

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam( ':msisdn', $authObj->msisdn );
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
		$userStatusforAlacartPlans = $statement->fetchall();
		
        return $userStatusforAlacartPlans;
    }

}