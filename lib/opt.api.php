<?php
  //  --------------------------------------------------------------------  //
  //                        Open Power Template                             //
  //         Copyright (c) 2005-2007 Tomasz "Zyx" Jedrzejewski              //
  //     Copyright (c) 2008 Invenzzia Group, http://www.invenzzia.org/      //
  //  --------------------------------------------------------------------  //
  //  This program is free software; you can redistribute it and/or modify  //
  //  it under the terms of the GNU Lesser General Public License as        //
  //  published by the Free Software Foundation; either version 2.1 of the  //
  //  License, or (at your option) any later version.                       //
  //  --------------------------------------------------------------------  //
  //
  // $Id: opt.api.php 59 2006-08-02 11:29:55Z zyxist $

	if(!defined('OPT_SECTION_MULTI'))
	{
		// In order to load both true OPT and API...

		define('OPT_SECTION_MULTI', 0);
		define('OPT_SECTION_SINGLE', 1);
		define('OPT_PRIORITY_NORMAL', 0);
		define('OPT_PRIORITY_HIGH', 1);
		define('OPT_VERSION', '1.1.5');

		define('OPT_E_ARRAY_REQUIRED', 2);
		define('OPT_E_FILE_NOT_FOUND', 6);
		define('OPT_E_ENCLOSING_STATEMENT', 101);
		define('OPT_E_UNKNOWN', 102);
		define('OPT_E_FUNCTION_NOT_FOUND', 103);
		define('OPT_E_CONSTANT_NOT_FOUND', 104);
		define('OPT_E_COMMAND_NOT_FOUND', 105);
		define('OPT_E_EXPRESSION', 106);
		define('OPT_E_REQUIRED_NOT_FOUND', 107);
		define('OPT_E_INVALID_PARAMETER', 108);
		define('OPT_E_DEFAULT_MARKER', 109);
		define('OPT_E_UNKNOWN_PARAM', 110);
		define('OPT_E_PARAM_STYLE', 111);
		define('OPT_W_LANG_NOT_FOUND', 151);
		define('OPT_E_IF_ELSEIF', 201);
		define('OPT_E_IF_ELSE', 202);
		define('OPT_E_IF_END', 203);
		define('OPT_E_BIND_NOT_FOUND', 208);
		define('OPT_W_DYNAMIC_OPENED', 301);
		define('OPT_W_DYNAMIC_CLOSED', 302);
		define('OPT_W_SNIPPETS_NOT_DEF', 303);
		define('OPT_W_SHORT_CYCLE', 304);
		
		# OBJECT_I18N
		interface ioptI18n
		{
			public function setOptInstance(optClass $tpl);
			public function put($group, $id);
			public function putApply($group, $id);
			public function apply($group, $id);
		}
		# /OBJECT_I18N
		
		function optCompileFilename($filename)
		{
			return '%%'.str_replace('/', '_', $filename);
		} // end optCompileFilename();
	}

	if(!defined('OPT_DIR'))
	{
		define('OPT_DIR', './');
	}

	class optApi
	{
		// Configuration
		public $root = NULL;
		public $compile = NULL;
		public $cache = NULL;
		public $plugins = NULL;

		public $gzipCompression = false;
		public $charset = NULL;

		public $alwaysRebuild = false;
		public $showWarnings = true;
		public $debugConsole = false;
		public $performance = false;

		public $xmlsyntaxMode = false;
		public $strictSyntax = false;
		public $entities = false;
		public $sectionStructure = OPT_SECTION_MULTI;
		public $statePriority = OPT_PRIORITY_NORMAL;

		public $parseintDecPoint = '.';
		public $parseintDecimals = 3;
		public $parseintThousands = ',';

		// Parser and compiler data
		protected $init = false;
		protected $outputBufferEnabled = false;
		public $compiler;
		public $data = array();
		public $vars = array();
		public $capture = array();
		// Assotiative array: OPT function name => PHP function name
		public $functions = array();
		// Assotiative array: OPT function name => PHP function name
		public $phpFunctions = array();
		public $control = array();
		# COMPONENTS
		public $components = array();
		# /COMPONENTS
		public $delimiters = array(0 => 
								'\{(\/?)(($$NS$$)\:)?(.*?)(\/?)\}',
								'($$NS$$)(\:)([a-zA-Z0-9\_]*)\=\"(.*?[^\\\\])\"'
							);
		public $filters = array(
								'pre' => array(),
								'preMaster' => array(),
								'post' => array(),
								'output' => array()
							);
		public $instructionFiles = array();
		public $nschange = true;
		public $namespaces = array(0 => 'opt');
		
		// I18n
		public $i18n = NULL;
		public $i18nType = 0;

		
		public function __construct()
		{
			$this -> compileCode = '';		
		} // end __construct();

		public function error($type, $message, $code)
		{
			require_once(OPT_DIR.'opt.error.php');
			require_once(OPT_DIR.'opt.core.php');
			optErrorMessage($this, $type, $message, $code);
		} // end error();

		// Methods		
		public function assign($name, $value)
		{
			$this -> data[$name] = $value;		
		} // end assign();

		public function assignGroup($values)
		{
			if(!is_array($values))
			{
				return false;
			}
		
			foreach($values as $name => &$value)
			{
				$this -> data[$name] = $value;
			}	
		} // end assignGroup();

		public function assignRef($name, &$value)
		{
			$this -> data[$name] = $value;		
		} // end assignRef();

		public function setDefaultI18n(&$lang)
		{
			$this -> i18nType = 0;
			if(is_array($lang))
			{
				$this -> i18n = &$lang;
			}
			else
			{
				$this -> error(E_USER_ERROR, 'First parameter must be an array.', OPT_E_ARRAY_REQUIRED);
			}
		} // end setDefaultI18n();

		# OBJECT_I18N
		public function setObjectI18n(ioptI18n $i18n)
		{
			$this -> i18nType = 1;
			$this -> i18n = $i18n;
		} // end setObjectI18n();
		# /OBJECT_I18N

		public function registerInstruction($class)
		{
			if(is_object($this -> compiler))
			{
				// The compiler is already initialized, we have to translate this call like the compiler does.
				if(!is_array($class))
				{
					$class = array(0 => $class);
				}
				$this -> compiler -> translate($class);
			}
			else
			{
				// OK, the compiler is not used. Just register. If the compiler is needed, it will translate
				// the call on its own.
				if(is_array($class))
				{
					$this -> control = array_merge($this->control, $class);				
				}
				else
				{
					$this -> control[] = $class;
				}
			}
		} // end registerInstruction();
		
		public function registerInstructionFile($file)
		{
			$this -> instructionFiles[] = $file;
		} // end registerInstructionFile();
		
		public function parse($filename)
		{
			$this -> fetch($filename, true);
		} // end parse();

		public function fetch($filename, $display = false)
		{
			// Get the compiled file version name
			$compiled = $this -> needCompile($filename);

			// Only if we want to return the output as a text
			if(!$display)
			{
				ob_start();
			}

			// Disable E_NOTICE and include the compiled version
			$oldErrorReporting = error_reporting(E_ALL ^ E_NOTICE);
			include($this -> compile.$compiled);
			error_reporting($oldErrorReporting);
			
			// Return the output, if needed.
			if(!$display)
			{
				return ob_get_clean();
			}
		} // end fetch();

		protected function doInclude($filename, $default = false)
		{
			// Get the compiled file version name
			$compiled = $this -> needCompile($filename, true);			
			if($compiled === false)
			{
				// Template not found
				return false;
			}

			// Disable E_NOTICE and include the compiled version
			$oldErrorReporting = error_reporting(E_ALL ^ E_NOTICE);
			include($this -> compile.$compiled);
			error_reporting($oldErrorReporting);
		} // end doInclude();
		
		protected function needCompile($filename, $noException = false)
		{
			// This method returns the compiled file name. If the compiled version
			// Does not exist, the template is compiled.
			$compiled = optCompileFilename($filename);
			
			// Both the modification time and the file existence are checked by filemtime() function
			// The fewer disk operation, the better
			$compiledTime = @filemtime($this -> compile.$compiled);
			$result = false;
			$rootTime = @filemtime($this -> root.$filename);
			if($rootTime === false)
			{
				if($noException)
				{
					return NULL;
				}
				$this -> error(E_USER_ERROR, '"'.$filename.'" not found in '.$this->root.' directory.', OPT_E_FILE_NOT_FOUND);
			}
			if($compiledTime === false || $compiledTime < $rootTime || $this -> alwaysRebuild)
			{
				// If it is the time to (re)compilation, read the file content to this variable
				$result = file_get_contents($this -> root.$filename);
			}
			
			if($result === false)
			{
				// The script goes here, if the source template is not loaded. It simply returns the
				// Compiled version filename
				return $compiled;
			}

			// Otherwise, we set up the compiler and parse the template.
			if(!is_object($this -> compiler))
			{
				require_once(OPT_DIR.'opt.compiler.php');
				$this -> compiler = new optCompiler($this);
			}
			$this -> compiler -> parse($this -> compile.$compiled, $result);
			return $compiled;
		} // end needCompile();
		
		public function getTemplate($filename)
		{
			$compiler = new optCompiler($this -> compiler);
			return $compiler -> parse(NULL, file_get_contents($this -> root.$filename));
		} // end getFilename();
	}

?>
