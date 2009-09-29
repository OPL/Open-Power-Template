<?php
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
  
	class i18n implements ioptI18n{
		private $data;
		private $replacements;
		static $instance;
		private $tpl;
  	
		private function __construct()
		{
			$this -> data = array(
				'global' => 
				array(
					'text1' => 'This is text one',
					'text2' => 'This is text two',
					'text3' => 'This is text three',
					'date' => 'Today is %s, good day for fishing'		
				)
			);
		} // end __construct();
		
		public function setOptInstance(optClass $tpl)
		{
			$this -> tpl = $tpl;
		} // end setOptInstance();
		
		static public function getInstance()
		{
			if(!is_object(self::$instance))
			{
				self::$instance = new i18n;
			}
			return self::$instance;
		} // end getInstance();  
  
	 	public function put($group, $text_id)
	 	{
			if(isset($this->replacements[$group][$text_id]))
			{
				return $this->replacements[$group][$text_id];
			}
			return $this->data[$group][$text_id]; 	
		} // end put();

		public function apply($group, $text_id)
		{
			$args = func_get_args();
			unset($args[0]);
			unset($args[1]);
			$this -> replacements[$group][$text_id] = vsprintf($this -> data[$group][$text_id], $args);
		} // end apply();
		
		public function putApply($group, $text_id)
		{
			$args = func_get_args();
			unset($args[0]);
			unset($args[1]);
			return vsprintf($this -> data[$group][$text_id], $args);
		} // end apply();  
	}
 
	try{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML); 
    

		// create an instance of the i18n system
		$i18n = i18n::getInstance();
		
		// pass it to the parser
		$tpl -> setObjectI18n($i18n);

		$tpl -> assign('current_date', date('d.m.Y')); 
		$tpl -> parse('example5.tpl'); 
	}catch(optException $exception){ 
		optErrorHandler($exception); 
	}
?>
