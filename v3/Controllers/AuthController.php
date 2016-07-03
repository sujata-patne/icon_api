<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 03/31/16
 * Time: 09:50 AM
 */
require_once(APP."Models/Auth.php");
require_once(APP."Models/Message.php");

class AuthController extends BaseController {

    public function __construct() {
        parent::__construct();
    }
    public function getAction($request){
        parent::display($request);
    }
    public function postAction( $request ) {
        echo "coming";exit;
    }
    public function getEligibility( $request ){
		
		//$log = new KLogger ('/var/www/api/v3/v4/logs/'.date('m-d-Y').'.log' , KLogger::INFO );

        $json = json_encode( $request->parameters );
        $auth = new Auth();
        $jsonObj = json_decode($json);

        $jsonResMessage = $auth->validateJsonMSISDN($jsonObj);
	
        if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('AuthController:getEligibility#'.json_encode($response));
			$this->outputError($response);
			return;
		}

        $eligibilityDetails = $auth->getAuthDetails( 'getEligibility',$jsonObj );
        
		if(empty($eligibilityDetails) || strlen($eligibilityDetails) == 0 || $eligibilityDetails == ''){ //in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'Invalid MSISDN');
			$this->successLog->LogInfo('AuthController:getEligibility#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'EligibilityDetails' => $eligibilityDetails);
			$this->successLog->LogInfo('AuthController:getEligibility#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		
    }
	
    public function getStatus( $request ){
	
        $json = json_encode( $request->parameters );
        $auth = new Auth();
        $jsonObj = json_decode($json);

        $jsonResMessage = $auth->validateJsonMSISDN($jsonObj);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('AuthController:getStatus#'.json_encode($response));
			$this->outputError($response);
			return;
		}

        $userStatus['SubscriptionPlans'] = $auth->getAuthDetails( 'getUserStatusforSubscription',$jsonObj );
        $userStatus['ValuePackPlans']    = $auth->getAuthDetails( 'getUserStatusforValuePack',$jsonObj );
        $userStatus['OfferPlans']        = $auth->getAuthDetails( 'getUserStatusforOffer',$jsonObj );
        $userStatus['AlacartPlans']      = $auth->getAuthDetails( 'getUserStatusforAlacart',$jsonObj );
        
		if(empty($userStatus['SubscriptionPlans']) && empty($userStatus['ValuePackPlans']) && empty($userStatus['OfferPlans']) || empty($userStatus['AlacartPlans'])){		//in case invalid msisdn
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'No Plans found for this MSISDN');
			$this->successLog->LogInfo('AuthController:getStatus#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
		
			$response = array("status" => "SUCCESS", "status_code" => '200', 'UserStatus' => $userStatus);
			$this->successLog->LogInfo('AuthController:getStatus#'.json_encode($response));
			$this->outputSuccess($response);
			return;	 

    }
	
	public function getMSISDNOperator(){
		
		$msisdn = '';
		
		if (isset($_SERVER['X-MSISDN'])){
			$msisdn =  $_SERVER['X-MSISDN'];
		}elseif (isset($_SERVER['X_MSISDN'])){
			$msisdn =  $_SERVER['X_MSISDN'];
		}elseif (isset($_SERVER['HTTP_X_MSISDN'])){
			$msisdn =  $_SERVER['HTTP_X_MSISDN'];
		}elseif (isset($_SERVER['X-UP-CALLING-LINE-ID'])){
			$msisdn =  $_SERVER['X-UP-CALLING-LINE-ID'];
		}elseif (isset($_SERVER['X_UP_CALLING_LINE_ID'])){
			$msisdn = $_SERVER['X_UP_CALLING_LINE_ID'];
		}elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])){
			$msisdn = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
		}elseif (isset($_SERVER['X_WAP_NETWORK_CLIENT_MSISDN'])){
			$msisdn =  $_SERVER['X_WAP_NETWORK_CLIENT_MSISDN'];
		}elseif (isset($_SERVER['HTTP_MSISDN'])){
			$msisdn = $_SERVER['HTTP_MSISDN'];
		}elseif (isset($_SERVER['HTTP-X-MSISDN'])){
			$msisdn =  $_SERVER['HTTP-X-MSISDN'];
		}elseif (isset($_SERVER['MSISDN'])){
			$msisdn =  $_SERVER['MSISDN'];
		}elseif (isset($_SERVER['HTTP_X_NOKIA_MSISDN'])){
			$msisdn =  $_SERVER['HTTP_X_NOKIA_MSISDN'];
		}else{
			$msisdn = 'UNKNOWN';
		}
		
		
		$appId = 12;
		$netIpAddress = $_SEREVER['HTTP_CLIENT_IP'];
		$imsi = 
		
		$url = 'http://wakau.in/authService/?AppId='.$appId.';MSISDN='.$msisdn.';NET_IP_ADDRESS='.$netIpAddress.';IMSI='.$imsi.'';
		
		
		
		
		
		
		
	}

}



?>