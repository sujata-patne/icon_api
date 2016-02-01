<?php
class BaseController {

    protected $app;
    protected $sessionId;
    protected $userId = '';
	protected $response;
	protected $status ;
	protected $body = '';
	protected $length = 0;
	protected $headers;
	
    public function __construct( $body = '', $status = 200, $headers = array() ) {
    		$this->setStatus($status);
    		$this->headers = array('Content-Type' => 'application/json');
    		//$this->headers->replace($headers);
    		$this->write($body);
    }
    public function installPaths() {

    }

    public function display($var) {
        print_r($var);
        exit;
    }
    
    private function setNoCache() {
    	header('Cache-Control', 'no-cache, must-revalidate');
    	header('Pragma', 'no-cache');
    	header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }
    
    private function setContentTypeToJson() {
    	header('Content-Type', 'application/json');
    }
    

    public function output($object, $status = 200) {
        $this->setStatus($status);
        $this->setNoCache();
        $this->setContentTypeToJson();
        $this->setBody(json_encode($object));
    }
    
    public function setStatus($status)
    {
    	$this->status = (int)$status;
    }
    
    public function setBody($content)
    {
    	$response = $this->write($content, true);
    	$this->display($response);
    }
    
    /**
     * DEPRECATION WARNING! use `getBody` or `setBody` instead.
     *
     * Get and set body
     * @param  string|null $body Content of HTTP response body
     * @return string
     */
    public function body($body = null)
    {
    	if (!is_null($body)) {
    		$this->write($body, true);
    	}
    
    	return $this->body;
    }

    public function write($body, $replace = false)
    {
    	if ($replace) {
    		$this->body = $body;
    	} else {
    		$this->body .= (string)$body;
    	}
    	 
    	$this->length = strlen($this->body);
 
    	return $this->body;
    }
    
    public function getLength()
    {
    	return $this->length;
    }
    
    public function finalize()
    {
    	// Prepare response
    	if (in_array($this->status, array(204, 304))) {
    		$this->headers->remove('Content-Type');
    		$this->headers->remove('Content-Length');
    		$this->setBody('');
    	}
    
    	return array($this->status, $this->headers, $this->body);
    }
    

    public function outputSuccess($msg) {
        $obj = Message::successMessage($msg);
        $this->output($obj, 200);
    }

    public function outputError($msg) {
        $obj = Message::errorMessage($msg);
        $this->output($obj, 400);
    }

    public function outputNotFoundError($msg) {
        $obj = Message::errorMessage($msg);
        $this->output($obj, 404);
    }

    public function outputBadRequestError($msg) {
        $obj = Message::errorMessage($msg);
        $this->output($obj, 401);
    }

    public function outputInternalServerError($msg) {
        $obj = Message::errorMessage($msg);
        $this->output($obj, 500);
    }

    public function outputForbidden($msg) {
        $obj = Message::errorMessage($msg);
        $this->output($obj, 403);
    }

    public function generateRandomString($length = 0) {
        $alphabets = range('A', 'Z');
        $numbers = range('0', '9');
        $additional_characters = array('_', '@', '#', '*');
        $final_array = array_merge($alphabets, $numbers, $additional_characters);

        $randomString = '';

        while ($length--) {
            $key = array_rand($final_array);
            $randomString .= $final_array[$key];
        }

        return $randomString;
    }

}
