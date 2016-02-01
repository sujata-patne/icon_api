<?php

use VOP\Utils\PdoUtils;
require_once(APP."Daos/SubscriptionDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");


class Subscription extends BaseModel {

	public $storeId;
    public $pss_sp_id;
    public $sp_plan_name;
    public $sp_caption;
	public $sp_description;
	public $sp_jed_id;
	
    public function __construct($json = NULL) {
        if (is_null($json)) {
            return;
        }

        //$this->setValuesFromJson($json);
    }
    
    public function validateJson($jsonObj) {
    	$requiredProps = array ('storeId');
    
    	$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
    	return $message;
    }

    public function setValuesFromJsonObj($jsonObj) {
        $result = $this->setValuesFromJsonObjParent($jsonObj);

        if (!$result) {
            return $result;
        }
        return true;
    }
    
    public function getSubscriptionDetailsPackageIds( $packageIds ) {
    	$dbConnection = PdoUtils::obtainConnection('CMS');
    	
    	if ($dbConnection == null) {
    		return Message::ERROR_NO_DB_CONNECTION;
    	}
    	
    	$dbConnection->beginTransaction();
    	
    	$subscriptions = array();
    	
    	try {
    		$subscriptionDao = new SubscriptionDao($dbConnection);
    		$subscriptions = $subscriptionDao->getSubscriptionDetailsPackageIds( $packageIds );
    	
    		$dbConnection->commit();
    	} catch (\Exception $e) {
    		$dbConnection->rollBack();
    		echo $e->getMessage();
    		exit;
    	}
    	
    	PdoUtils::closeConnection($dbConnection);
    	return $subscriptions;
    }
}
?>
    