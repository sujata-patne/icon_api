<?php

include_once(APP."models/Search.php");

class SearchController extends BaseController{
	private $search;
	
	public function __construct(){
		$this->search = new Search();
	}
	
	public function getAction($request){
		parent::display($request);
	}
	
	public function postAction($request){
		$data = $request->parameters;
		$result = $this->search->find(ICONDB, $data);
		echo json_encode($result);
	}
	
}
?>