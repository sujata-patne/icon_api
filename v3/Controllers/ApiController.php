<?php

include_once(APP."models/Model.php");

class ApiController{
	public $model;
	
	public function __construct(){
		$this->model = new Model();
	}
	
	public function invoke(){
		if( !isset($_GET['search']) ){
			$search = $this->model->getSearchResult();
			
		}else{

		}		
	}
	
	public function get(){
		
	}
	
	public function post(){
		
	}

} 


?>
