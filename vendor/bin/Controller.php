<?php

abstract class Controller {
	
	public $adapter;
	public $view;
	public $model;
	
	function __construct()
	{
        $this->adapter = new MongoDbAdapter();
		$this->view = new View();
		$this->model = new ParseModel();
	}
}
