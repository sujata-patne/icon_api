<?php
namespace VOP\Utils;
use VOP\Models\Message;
use VOP\Models\Subscription;
use VOP\Models\SubscriptionUsage;

class SubscriptionHelper {

    public function fileUp( $userId = null , $fileSize = null ) {
       
        $subscriptionObj = Subscription::getSubscriptionByUserId( $userId );

        if( empty( $subscriptionObj->id ) ) {
             $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVALID_SUBSCRIPTION );
             $this->outputError($response);
             return false;
        } else {
            $subscriptionUsage = SubscriptionUsage::getSubscriptionUsageByUserId( $userId );
                      
            if( empty( $subscriptionUsage ) ) {
                $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_DB_OPERATION_FAILED );
                $this->outputError($response);
                return false;
            }
           
            $precision = 2;
            
            $totalDiskSpace = floatval ($subscriptionObj->max_disk_space );
            $newDiskSpace = floatval( $subscriptionUsage->used_disk_space ) + floatval( $fileSize );
                    
            $result = SubscriptionHelper::compareValues( $newDiskSpace, $totalDiskSpace, $precision );
    
            if( $result === true ) {
                $response = array("status" => "ERROR-BUSINESS", "status_code" => '401', 'msgs' => 'Subsription exceeded. Please subscribe for more space to upload files.');
                $this->outputError($response);
                return false;
            }
                        
            return true;    
        }
       
    }
    
    public function fileDown( $userId = null , $fileSize = null ) {
        
        $subscriptionObj = Subscription::getSubscriptionByUserId( $userId );

        if( empty( $subscriptionObj->id ) ) {
             $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVALID_SUBSCRIPTION );
             $this->outputError($response);
             return;
        } else {
            $subscriptionUsage = SubscriptionUsage::getSubscriptionUsageByUserId( $userId );
                      
            if( empty( $subscriptionUsage ) ) {
                $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_DB_OPERATION_FAILED );
                $this->outputError($response);
                return;
            }
           
            $newDiskSpace = floatval( $subscriptionUsage->used_disk_space ) - floatval( $fileSize );
                
            if( $newDiskSpace >= 0 ) {
                return true;
            } else {
                $response = array("status" => "ERROR-BUSINESS", "status_code" => '401', 'msgs' => 'Invalid operation.');
                $this->outputError($response);
                return false;
            }
  
        }
    }
    
    public function entityUp( $userId = null ) {
        
        $subscriptionObj = Subscription::getSubscriptionByUserId( $userId );

        if( empty( $subscriptionObj->id ) ) {
             $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVALID_SUBSCRIPTION );
             $this->outputError($response);
             return;
        } else {
            $subscriptionUsage = SubscriptionUsage::getSubscriptionUsageByUserId( $userId );
                      
            if( empty( $subscriptionUsage ) ) {
                $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_DB_OPERATION_FAILED );
                $this->outputError($response);
                return;
            }
           
            $newDiskSpace = intval( $subscriptionUsage->used_entity_count );
            
            if( $newDiskSpace >= $subscriptionObj->total_entity_count ) {
                $response = array("status" => "ERROR-BUSINESS", "status_code" => '401', 'msgs' => 'Max entity count reached. Please subscibe for more entities');
                $this->outputError($response);
                return false;
            } else {
                $newDiskSpace + 1;
                return true;
            }
  
        }
    }
    
    public function enitityDown( $userId = null ) {
        $subscriptionObj = Subscription::getSubscriptionByUserId( $userId );

        if( empty( $subscriptionObj->id ) ) {
             $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVALID_SUBSCRIPTION );
             $this->outputError($response);
             return;
        } else {
            $subscriptionUsage = SubscriptionUsage::getSubscriptionUsageByUserId( $userId );
                      
            if( empty( $subscriptionUsage ) ) {
                $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_DB_OPERATION_FAILED );
                $this->outputError($response);
                return;
            }
           
            $newDiskSpace = intval( $subscriptionUsage->used_entity_count );
            
            if( $newDiskSpace > 0 ) {
                $newDiskSpace - 1;
                return true;
            } else {
                $response = array("status" => "ERROR-BUSINESS", "status_code" => '401', 'msgs' => 'Entity count can not be less than assigned limit' );
                $this->outputError($response);
                return false;
            }
  
        }
    }
    
    function compareValues( $newValue, $oldValue, $precision ) {
        if(bccomp($newValue, $oldValue, $precision)  === 0  || bccomp($newValue, $oldValue, $precision)  >= 0 ) {
            return true;
        }  
    }
    
}

?>