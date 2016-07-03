<?php

use VOP\Daos\BaseDao;
require_once(APP."Daos/BaseDao.php");
require_once APP.'Models/Tune.php';
require_once(APP."Models/Message.php");

class TunesController extends BaseController{
	
	public function getTunesByUserOperator($request){
		
		$json = json_encode( $request->parameters );
		$tune = new Tune();
		$jsonObj = json_decode($json);
		
		$jsonMessage = $tune->validateJsonObj($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
		
		if (!$tune->setValuesFromJsonObj($jsonObj)) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->UserName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->Operator == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
            $this->outputError($response);
            return;
        }
		
        
		$OperatorwiseTunes = $tune->getTunesByOperator($tune);
		
		if( !empty( $OperatorwiseTunes ) ){
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'Tunes' => $OperatorwiseTunes );
    		$this->outputSuccess($response);
    		return;	
		}else{
			$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No Operator Wise Tunes available !.' );
    		$this->outputError($response);
    		return;
		}		
		
	}
	
	public function getTunesByUserCelebrity($request){
		
		$json = json_encode( $request->parameters );
        $tune = new Tune();
        $jsonObj = json_decode($json);
		
		$jsonMessage = $tune->validateJsonObjForCelebrity($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->UserName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->CelebrityName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CELEBRITY_NAME );
            $this->outputError($response);
            return;
        }
		
		$CelebritywiseTunes = $tune->getTunesByCelebrity($jsonObj);
		
		if( !empty( $CelebritywiseTunes ) ){
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'Tunes' => $CelebritywiseTunes );
    		$this->outputSuccess($response);
    		return;	
		}else{
			$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No Celebrity Wise Tunes available !.' );
    		$this->outputError($response);
    		return;
		}

	}
	
	public function getTunesByUsername($request){
		
		$json = json_encode( $request->parameters );
        $tune = new Tune();
        $jsonObj = json_decode($json);
		
		$jsonMessage = $tune->validateJsonObjForName($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
		
		
		if (trim( $jsonObj->UserName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }
		
		$NamewiseTunes = $tune->getTunesByName($jsonObj);
		
		if( !empty( $NamewiseTunes ) ){
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'Tunes' => $NamewiseTunes );
    		$this->outputSuccess($response);
    		return;	
		}else{
			$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No Name Wise Tunes available !.' );
    		$this->outputError($response);
    		return;
		}
	}
	
	public function getTunesByUsernameOperatorCelebrity($request){
		$json =json_encode( $request->parameters );
		$tune = new Tune();
		$jsonObj = json_decode($json);
		
		$jsonMessage = $tune->validateJsonObjForAll($jsonObj);
		
		if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->UserName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_USER_NAME );
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->CelebrityName == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CELEBRITY_NAME );
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->Operator == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_OPERATOR_ID );
            $this->outputError($response);
            return;
        }
		
		
		$TunesDetails = $tune->getTunesByUsernameOperatorCelebrity($jsonObj);
		
		if( !empty( $TunesDetails ) ){
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'Tunes' => $TunesDetails );
    		$this->outputSuccess($response);
    		return;	
		}else{
			$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No Tunes available !.' );
    		$this->outputError($response);
    		return;
		}
		
		
	}
	
	public function getTuneID($request){
		$json = json_encode( $request->parameters );
        $tune = new Tune();
        $jsonObj = json_decode($json);
		
		$jsonMessage = $tune->validateJsonObjForTuneID($jsonObj);
            
        if ($jsonMessage != Message::SUCCESS) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
            $this->outputError($response);
            return;
        }
		
		if (trim( $jsonObj->TuneID == '' )) {
            $response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_BLANK_CF_ID );
            $this->outputError($response);
            return;
        }
		
		$TuneID = $tune->getTuneID($jsonObj);
		
		if( !empty( $TuneID ) ){
			$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'TuneID' => $TuneID );
    		$this->outputSuccess($response);
    		return;	
		}else{
			$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'msgs' => 'No TuneIDs available !.' );
    		$this->outputError($response);
    		return;
		}
	}
	
}

?>