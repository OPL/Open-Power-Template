<?php

	class BasicAction
	{
		public $view;
		public $controller;

	} // end BasicAction();

	class Config
	{
		private $_config;

		public function __construct($config)
		{
			if(!file_exists($config))
			{
				die('The configuration file does not exist.');
			}
			$this->_config = parse_ini_file($config, true);
		} // end __construct();

		public function get($group, $name)
		{
			if(!isset($this->_config[$group]))
			{
				return null;
			}
			if(!isset($this->_config[$group][$name]))
			{
				return null;
			}
			return $this->_config[$group][$name];
		} // end get();

		public function getGroup($group)
		{
			if(!isset($this->_config[$group]))
			{
				return null;
			}
			return $this->_config[$group];
		} // end getGroup();
	} // end Config;

	class FrontController
	{
		public $defaultController;
		public $defaultAction;

		public $config;

		static private $_instance;
		
		private function __construct()
		{
			$this->defaultController = 'index';
			$this->defaultAction = 'index';
			
			self::$_instance = $this;
		} // end __construct();

		static public function getInstance()
		{
			if(!is_object(self::$_instance))
			{
				self::$_instance = new FrontController;
			}
			return self::$_instance;
		} // end getInstance();

		public function dispatch()
		{
			if(!isset($_GET['controller']))
			{
				$_GET['controller'] = $this->defaultController;
			}
			if(!isset($_GET['action']))
			{
				$_GET['action'] = $this->defaultAction;
			}


			if(!file_exists('./controllers/'.ucfirst($_GET['controller']).'Controller.php'))
			{
				die('Error 404: '.$_GET['Controller'].' has not been found.');
			}

			require_once('./controllers/'.ucfirst($_GET['controller']).'Controller.php');
			$ctlName = ucfirst($_GET['controller']).'Controller';

			if(!class_exists($ctlName))
			{
				die('Invalid class file.');
			}
			$ctl = new $ctlName;
			if(!method_exists($ctl, $_GET['action']))
			{
				die('Error 404: '.$_GET['action'].' has not been found.');
			}
			$method = $_GET['action'];

			$ctl->view = new Opt_View(ucfirst($_GET['controller']).'/'.$_GET['action'].'.tpl');
			$ctl->$method($this);
		} // end dispatch();

		public function display(Opt_Output_Interface $out, Opt_View $view)
		{
			$layout = new Opt_View('layout.tpl');
			$layout->websiteTitle = $this->config->get('website', 'title');
			$layout->view = $view;

			$out->render($layout);
		} // end display();

	} // end FrontController;
