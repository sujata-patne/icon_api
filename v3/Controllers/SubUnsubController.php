<?php
/**
 * Created by PhpStorm.
 * User: Shraddha.Vadnere
 * Date: 04/04/16
 * Time: 02:28 PM
 */

require_once(APP."Models/SubUnsub.php");
require_once(APP."Models/Message.php");
session_start();

class SubUnsubController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

    }
	
	public function executeCurl( $url ){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_POST, count($data));
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch,CURLOPT_TIMEOUT,1);
    // if($isJSON == 1 ){
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         // 'Content-Type: application/json'
        // ));
    // }
    $content = curl_exec($ch);
    $getCurlInfo = curl_getinfo($ch);
    $curlError = curl_error($ch);
    curl_close ($ch); // close curl handle
    return array(
        'Content' => $content,
        'Info' => $getCurlInfo,
        'Error' => $curlError
    );
}

    public function Unsubscribe( $request ){
		
        $json = json_encode( $request->parameters );
        $subUnsub = new SubUnsub();
        $jsonObj = json_decode($json);
		
		$jsonResMessage = $subUnsub->validateJson($request->parameters);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('SubUnsubController:Unsubscribe#'.json_encode($response));
			$this->outputError($response);
			return;
		}
		
		
		$msisdn      = $jsonObj->msisdn;
		$operator    = $jsonObj->operator;
		$other1      = 'http://wakau.in/banner/CG_Banner_128X128.png';
		$other2      = $jsonObj->other2;
		$unitType 	 = $jsonObj->unitType;
		$referer_url = 'http://114.143.181.228/v3/v4/views/response.php';
		$fUrl        = 'http://114.143.181.228/v3/v4/views/response.php';
		
		$msisdn_length = strlen((string)$msisdn);
		if($msisdn_length == 12) {
			$msisdn = substr($msisdn,2);
		}

		$appid   = 123;
		$micro_date   = microtime();
		$date_array   = explode(" ", $micro_date);
		$milliseconds = substr($date_array[0], 2, 3);
		$date    = date('YmdHis');
		
		$tmpTransid = $msisdn . $appid . $date . $milliseconds;
		if(strlen((string)$tmpTransid) == 30){
			$transid = $tmpTransid;			
		}elseif(strlen((string)$tmpTransid) > 30){
			$removeChar = strlen((string)$tmpTransid) - 30;		
            $transid = substr($tmpTransid, $removeChar);	
		}else{
			$transid = $tmpTransid;	
		}
		
		$cpevent = 'WAKAU0003';
		$cmode = 'WAP_3P';
		$uid = 'crbt';
		$pass = 'crbt123';
		$unq_sid = isset($_COOKIE['Unq_Sid']) ? $_COOKIE['Unq_Sid'] : '91'.$msisdn.$date.$milliseconds;	
		$tokenId = $unq_sid.'-CRBT-0-0';
		
		switch($operator)
			{
				case 'reliance':
				
					$unsubUrl='http://103.43.2.5/adpaybilling?MSISDN='.$msisdn.'&TRANSID='.$transid.'&REQUESTTYPE=UNSUB&CPEVENT='.$cpevent.'&OPERATOR='.$operator.'&CMODE='.$cmode.'&UID='.$uid.'&PASS='.$pass.'&APPCONTID=1&OTHER1='.$other1.'&OTHER2='.$other2.'&UNITTYPE='.$unitType.'&RETURL='.$referer_url.'&TOKENCALL='.$tokenId.'&FLRETURL='.$fUrl;
					
				break;
					
				case 'airtel':
				
					$unsubUrl='http://103.43.2.5/airtelbilling?MSISDN='.$msisdn.'&TRANSID='.$transid.'&REQUESTTYPE=UNSUB&CPEVENT='.$cpevent.'&OPERATOR='.$operator.'&CMODE='.$cmode.'&UID='.$uid.'&PASS='.$pass.'&APPCONTID=1&OTHER1='.$other1.'&OTHER2='.$other2.'&UNITTYPE='.$unitType.'&RETURL='.$referer_url.'&TOKENCALL='.$tokenId.'&FLRETURL='.$fUrl;
					
					
				break;
				
				case 'higate':
				
					$unsubUrl='http://103.43.2.5/higate/HigateBilling?MSISDN='.$msisdn.'&TRANSID='.$transid.'&REQUESTTYPE=UNSUB&CPEVENT='.$cpevent.'&OPERATOR='.$operator.'&CMODE='.$cmode.'&UID='.$uid.'&PASS='.$pass.'&APPCONTID=1&OTHER1='.$other1.'&OTHER2='.$other2.'&UNITTYPE='.$unitType.'&RETURL='.$referer_url.'&TOKENCALL='.$tokenId.'&FLRETURL='.$fUrl;
					
				break;
				
				case 'DU':
				
					
					$unsubUrl='http://103.43.2.5/Dubilling?MSISDN='.$msisdn.'&TRANSID='.$transid.'&REQUESTTYPE=UNSUB&CPEVENT='.$cpevent.'&OPERATOR='.$operator.'&CMODE='.$cmode.'&UID='.$uid.'&PASS='.$pass.'&APPCONTID=1&OTHER1='.$other1.'&OTHER2='.$other2.'&UNITTYPE='.$unitType.'&RETURL='.$referer_url.'&TOKENCALL='.$tokenId.'&FLRETURL='.$fUrl;
					
					
				break;
				
				default :
				
					echo "Invalid Parameters/Data";
				
				break;

			}
			
			$url = $unsubUrl;

		header("Location: $url");

			
    }
	
	
	
	
	public function LogUnsubRequest( $request ){
		
		
		echo 'logUnsub';exit;
		
		        $subUnsubDetails = $subUnsub->getSubscriptionDetails('unsubscribe', $jsonObj );
		
		echo '<pre>';print_r($subUnsubDetails);exit;
		
		if(empty($subUnsubDetails)){		//in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'Unsubscription request could not be inserted into the database');
			$this->successLog->LogInfo('SubUnsubController:Unsubscribe#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'UnsubscriptionDetails' => $subUnsubDetails);
			$this->successLog->LogInfo('SubUnsubController:Unsubscribe#'.json_encode($response));
			$this->outputSuccess($response);
			return;	
		
		
	}

}