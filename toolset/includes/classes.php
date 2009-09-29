<?php

	class view
	{
		private $title;
		private $template;

		public function setTitle($title)
		{
			$this -> title = $title;
		} // end setTitle();

		public function setTemplate($template)
		{
			$this -> template = $template;
		} // end setTemplate();

		public function message($message, $redirect = 'index.php')
		{
			global $tpl;

			$tpl -> assign('message', $message);
			$tpl -> assign('redirect', $redirect);
			$this -> setTitle('Message');
			$this -> setTemplate('message.tpl');
			$this -> display();
			die();
		} // end message();

		public function display()
		{
			global $tpl, $config;

			$tpl -> assign('title', $this -> title);
			$tpl -> parse('overall_header.tpl');
			$tpl -> parse($this -> template);

			if(!$config -> save())
			{
				$tpl -> assign('config_ns', 1);
			}

			$tpl -> parse('overall_footer.tpl');
		} // end display();

	}

	class config
	{
		private $options = array();
		private $directives = array();
		private $modified = false;

		public function __construct()
		{
			$this -> options = @parse_ini_file(DIR_DATA.'%%config.php', true);
			$this -> directives = &$this -> options['directives'];
		} // end __construct();	

		public function save()
		{
			if($this -> modified)
			{
				$code = "; <?"."php die(); ?".">\r\n";

				foreach($this -> options as $name => $value)
				{
					if(!is_array($value))
					{
						$code .= $name.' = "'.$value."\"\r\n";
					}
				}
				$code .= "[directives]\r\n";

				foreach($this -> directives as $name => $value)
				{
					$code .= $name.' = "'.$value."\"\r\n";
				}
				if(!@file_put_contents(DIR_DATA.'%%config.php', $code))
				{
					return false;
				}
			}
			return true;
		} // end save();

		public function __get($name)
		{
			if(isset($this -> options[$name]))
			{
				return $this -> options[$name];
			}
			return '';
		} // end __get();

		public function __set($name, $value)
		{
			$this -> modified = true;
			$this -> options[$name] = $value;
		} // end __set();

		public function enabled($name)
		{
			if(isset($this -> directives[$name]))
			{
				return $this -> directives[$name];
			}
			return NULL;
		} // end __get();

		public function enable($name, $value)
		{
			$this -> modified = true;
			$this -> directives[$name] = $value;
		} // end __set();

	}

?>
