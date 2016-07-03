<?php
//namespace Store\Curl;
class Curl {
    public function executeCurl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec ($ch);
        $getCurlInfo = curl_getinfo($ch);
        $curlError = curl_error($ch);
        curl_close ($ch); // close curl handle

        return array(
            'Content' => $content,
            'Info' => $getCurlInfo,
            'Error' => $curlError
        );
    }

    public function get_headers_from_curl_response($response){
        $headers = array();

        foreach (explode("\r\n", $response) as $i => $line){
            if ($i === 0 or $i === 1 or $i === 2){
                //$headers['http_code'] = $line;
            }else{
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    public function executePostCurl($url, $data, $isJSON = 1){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if($isJSON == 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json'
            ));
        }
        
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
    
    public function executePostCurlHeader($url,$header,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec ($ch);
        $getCurlInfo = curl_getinfo($ch);
        $curlError = curl_error($ch);
        curl_close ($ch); // close curl handle

        return array(
            'Content' => $content,
            'Info' => $getCurlInfo,
            'Error' => $curlError
        );
    }
}