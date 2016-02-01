<?php
require_once(APP."Models/Message.php");
require_once(APP."Models/DeviceInfo.php");
require_once(APP."Models/Pack.php");

class DeviceInfoController extends BaseController {

	public function __construct() {
		parent::__construct();
	}
	
	public function getDeviceInfo( $request ) {
		
		$json = json_encode( $request->parameters );
		$device = new DeviceInfo();
		$jsonObj = json_decode($json);
		
		$jsonMessage = $device->validateJson($jsonObj);
		
		if ($jsonMessage != Message::SUCCESS) {
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonMessage);
			$this->outputError($response);
			return;
		}
		 
		if (!$device->setValuesFromJsonObj($jsonObj)) {
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => Message::ERROR_INVAID_REQUEST_BODY);
			$this->outputError($response);
			return;
		}
		
		if (empty($device->dc_device_id) || 0 == strlen( $device->dc_device_id ) ) {
    		$response = array("status" => "ERROR-BUSINESS", "status_code" => '400', 'msgs' => Message::ERROR_EMPTY_DEVICE_ID);
			$this->outputError($response);
			return;
		}else {	
			$deviceObject = $device->getDeviceDetailsByDeviceId( $device->dc_device_id );
			
			if( !empty( $deviceObject ) ){
				$deviceObject->unsetValues(array('created_on', 'updated_on', 'created_by', 'updated_by'));
				$response = array("status" => "SUCCESS-BUSINESS", "status_code" => '200', 'DeviceDetails' => $deviceObject );
				$this->output($response);
			}else {
				$response = array("status" => "ERROR-BUSINESS", "status_code" => '404', 'message' => 'Failed to load device details !.' );
				$this->output($response);
			} 
			 
		}
		 
	}
}
			