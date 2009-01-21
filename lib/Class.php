<?php
/*
 *  OPEN POWER LIBS <http://libs.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Class.php 22 2008-12-03 11:32:29Z zyxist $
 */

	/*
	 * Interface definitions
	 */
	interface Opt_Component_Interface
	{
		public function __construct($name = '');
		public function setOptInstance(Opt_Class $tpl);
		public function setDatasource(&$data);

		public function set($name, $value);
		public function get($name);
		public function defined($name);

		public function display($attributes = array());
		public function processEvent($name);
		public function createAttribute($tagName);
	} // end Opt_Component_Interface;
	
	interface Opt_Block_Interface
	{
		public function setOptInstance(Opt_Class $tpl);
		public function onOpen(Array $attributes);
		public function onClose();
		public function onSingle(Array $attributes);
	} // end Opt_Block_Interface;
	
	interface Opt_Cache_Hook_Interface
	{
		public function cacheTemplate($tpl, $file, $mode);
	} // end Opt_Cache_Hook;
	
	interface Opt_Output_Interface
	{
		public function getName();
		public function render(Opt_View $view, Opt_Cache_Hook_Interface $cache = null);
	} // end Opt_Output_Interface;

	interface Opt_Generator_Interface
	{
		public function generate($what);
	} // end Opt_Generator_Interface;
	
	/*
	 * Class definitions
	 */

	class Opt_Class extends Opl_Class
	{
		// Constants
		const CM_DEFAULT = 0;
		const CM_REBUILD = 1;
		const CM_PERFORMANCE = 2;
		
		const ACCESS_LOCAL = 0;
		const ACCESS_GLOBAL = 1;
		
		const CHOOSE_MODE = 0;
		const XML_MODE = 1;
		const QUIRKS_MODE = 2;

		const OPT_INSTRUCTION = 1;
		const OPT_NAMESPACE = 2;
		const OPT_FORMAT = 3;
		const OPT_COMPONENT = 4;
		const OPT_BLOCK = 5;
		const PHP_FUNCTION = 6;
		const PHP_CLASS = 7;
	
		const VERSION = '2.0.0-beta1';
		const ERR_STANDARD = 6135; // E_ALL^E_NOTICE
	
		// Directory configuration
		public $sourceDir = NULL;
		public $compileDir = NULL;
		public $cacheDir = NULL;

		// Template configuration
		public $compileId = NULL;

		// Front-end configuration
		public $compileMode = self::CM_DEFAULT;
		public $charset = 'utf-8';
		public $contentType = 0;
		public $gzipCompression = true;
		public $headerBuffering = false;
		public $contentNegotiation = false;
		public $errorReporting = self::ERR_STANDARD;
		public $stdStream = 'file';
		public $debugConsole = false;

		// Function configuration
		public $moneyFormat;
		public $numberDecimals;
		public $numberDecPoint;
		public $numberThousandSep;

		// Compiler configuration
		public $mode = self::XML_MODE;
		public $unicodeNames = false;
		public $htmlAttributes = false;
		public $printComments = false;
		public $prologRequired = true;
		public $stripWhitespaces = true;
		public $singleRootNode = true;
		public $basicOOP = true;
		public $advancedOOP = true;
		public $backticks = null;
		public $translate = null;
		public $strictCallbacks = true;
		public $componentAttributeLevel = 2;
		public $escape = true;
		public $variableAccess = self::ACCESS_LOCAL;

		// Data
		protected $_tf = NULL;	// translation interface
		
		// Add-ons
		protected $_outputs;
		protected $_cache;

		protected $_instructions = array('Section', 'Tree', 'Grid', 'Selector', 'Repeat',
			'Snippet', 'Extend', 'Cycle', 'For', 'Foreach', 'If', 'Put', 'Capture',
			'Attribute', 'Tag', 'Root', 'Prolog', 'Dtd', 'Literal', 'Include',
			'Dynamic', 'Component', 'Block');
		protected $_functions = array(
			'money' => 'Opt_Function::money', 'number' => 'Opt_Function::number', 'spacify' => 'Opt_Function::spacify',
			'firstof' => 'Opt_Function::firstof', 'indent' => 'Opt_Function::indent', 'strip' => 'Opt_Function::strip',
			'stripTags' => 'Opt_Function::stripTags', 'upper' => 'Opt_Function::upper', 'lower' => 'Opt_Function::lower',
			'capitalize' => 'Opt_Function::capitalize', 'countWords' => 'str_word_count', 'countChars' => 'strlen',
			'replace' => '#3,1,2#str_replace', 'repeat' => 'str_repeat', 'nl2br' => 'Opt_Function::nl2br', 'date' => 'date',
			'regexReplace' => '#3,1,2#preg_replace', 'truncate' => 'Opt_Function::truncate', 'wordWrap' => 'Opt_Function::wordwrap',
			'contains' => '#2,1#in_array', 'count' => 'sizeof', 'sum' => 'Opt_Function::sum', 'average' => 'Opt_Function::average',
			'absolute' => 'Opt_Function::absolute', 'stddev' => 'Opt_Function::stddev', 'range' => 'Opt_Function::range',
			'isUrl' => 'Opt_Function::isUrl', 'isImage' => 'Opt_Function::isImage', 'stddev' => 'Opt_Function::stddev',
		);
		protected $_classes = array();
		protected $_components = array();
		protected $_blocks = array();
		protected $_namespaces = array(1 => 'opt', 'com', 'parse');
		protected $_formats = array(1 => 'Generic', 'SingleArray', 'StaticGenerator', 'RuntimeGenerator', 'Objective');

		// Status
		protected $_init = false;

		// Other
		protected $_compiler;
		
		/*
		 * Template parsing
		 */
		
		public function getCompiler()
		{
			if(!is_object($this->_compiler))
			{
				$this->_compiler = new Opt_Compiler_Class($this);
			}
			return $this->_compiler;
		} // end getCompiler();

		/*
		 * Extensions and configuration
		 */

		public function setup($config = null)
		{
			if(is_array($config))
			{
				$this->loadConfig($config);
			}
			if(!is_null($this->pluginDir))
			{
				$this->loadPlugins();
			}

			if(Opl_Registry::exists('opl_translate'))
			{
				$this->setTranslationInterface(Opl_Registry::get('opl_translate'));
			}
			if(Opl_Registry::getState('opl_debug_console') || $this->debugConsole)
			{
				$this->debugConsole = true;				
				Opt_Support::initDebugConsole($this);
			}
			
			// Check paths etc.
			if(is_string($this->sourceDir))
			{
				$this->sourceDir = array('file' => $this->sourceDir);
			}
			foreach($this->sourceDir as &$path)
			{
				$this->_securePath($path);
			}
			$this->_securePath($this->compileDir);
			$this->_init = true;
		} // end setup();
		
		public function register($type, $item, $addon = null)
		{
			if($this->_init)
			{
				throw new Opt_Initialization_Exception($this->_init, 'register an item');
			}
			
			$map = array(1 => '_instructions', '_namespaces', '_formats', '_components', '_blocks', '_functions', '_classes');
			$whereto = $map[$type];
			if(is_array($item))
			{
				$this->$whereto = array_merge($this->$whereto, $item);
				return;
			}
			elseif($type >= self::OPT_COMPONENT)
			{
				$a = &$this->$whereto;
				$a[$item] = $addon;
			}
			else
			{
				$a = &$this->$whereto;
				$a[] = $item;
			}
		} // end register();

		public function setTranslationInterface($tf)
		{
			if(!$tf instanceof Opl_Translation_Interface)
			{
				$this->_tf = null;
				return false;
			}
			$this->_tf = $tf;
			return true;
		} // end setTranslationInterface();

		public function getTranslationInterface()
		{
			return $this->_tf;
		} // end getTranslationInterface();
		/*
		 * Internal use
		 */
		
		public function _getList($name)
		{
			static $list;
			if(is_null($list))
			{
				$list = array('_instructions', '_namespaces', '_formats', '_components', '_blocks', '_functions', '_classes', '_tf');
			}
			if(in_array($name, $list))
			{
				return $this->$name;
			}
			return NULL;
		} // end _getList();
		
		protected function _pluginLoader($directory, SplFileInfo $file)
		{
			$ns = explode('.', $file->getFilename());
			switch($ns[0])
			{
				case 'instruction':
					return 'Opl_Loader::map(\'Opt_Instruction_'.$ns[1].'\', \''.$directory.$file->getFilename().'\'); $this->register(self::OPT_INSTRUCTION, \''.$ns[1].'\'); ';
				case 'format':
					return 'Opl_Loader::map(\'Opt_Format_'.$ns[1].'\', \''.$directory.$file->getFilename().'\'); $this->register(self::OPT_FORMAT, \''.$ns[1].'\'); ';
				default:
					return ' require(\''.$directory.$file->getFilename().'\'); ';
			}
		} // end _pluginLoader();
		
		public function _stream($name)
		{
			if(strpos($name, ':') !== FALSE)
			{
				// We get the stream ID from the given filename.
				$data = explode(':', $name);
				if(!isset($this->sourceDir[$data[0]]))
				{
					throw new Opt_ObjectNotExists_Exception('resource', $data[0]);
				}
				return $this->sourceDir[$data[0]].$data[1];
			}
			// Here, the standard stream is used.
			if(!isset($this->sourceDir[$this->stdStream]))
			{
				throw new Opt_ObjectNotExists_Exception('resource', $this->stdStream);
			}
			return $this->sourceDir[$this->stdStream].$name;
		} // end _stream();
		
		public function _getSource($filename, $exception = true)
		{
			$item = $this->_stream($filename);
			if(!file_exists($item))
			{
				if(!$exception)
				{
					return array(false, false);
				}
				throw new Opt_TemplateNotFound_Exception($item);
			}
			return file_get_contents($item);
		} // end _getSource();
		
		public function __construct()
		{
			Opl_Registry::register('opt', $this);
		} // end __construct();

		public function __destruct()
		{
			if(ob_get_level() > 0)
			{
				while(@ob_end_flush());
			}
			if($this->debugConsole)
			{
				try
				{
					Opt_Support::updateTimers();
					Opl_Debug_Console::display();
				}
				catch(Opl_Exception $e)
				{
					die('<div style="background: #f77777;">Opt_Class destructor exception: '.$e->getMessage().'</div>');
				}
			}
		} // end __destruct();
	} // end Opt_Class;

	class Opt_View
	{
		const VAR_LOCAL = false;
		const VAR_GLOBAL = true;
	
		private $_tpl;
		private $_template;
		private $_formatInfo = array();
		private $_inheritance = array();
		private $_cplInheritance = array();
		private $_data = array();
		private $_tf;
		private $_processingTime = null;
		private $_branch = null;
		
		static private $_vars = array();
		static private $_global = array();
	
		public function __construct($template = '')
		{
			$this->_tpl = Opl_Registry::get('opt');
			$this->_template = $template;
		} // end __construct();
		
		public function setTemplate($file)
		{
			$this->_template = $file;
		} // end setTemplate();
		
		public function getTemplate()
		{
			return $this->_template;
		} // end getTemplate();

		public function setBranch($branch)
		{
			$this->_branch = $branch;
		} // end setBranch();

		public function getBranch()
		{
			return $this->_branch;
		} // end getBranch();
		
		public function getTime()
		{
			return $this->_processingTime;
		} // end getTime();

		/*
		 * Data management
		 */
		
		public function __set($name, $value)
		{
			$this->_data[$name] = $value;
		} // end __set();

		public function assign($name, $value)
		{
			$this->_data[$name] = $value;
		} // end assign();
		
		public function assignGroup($values)
		{
			$this->_data = array_merge($this->_data, $values);
		} // end assignGroup();
		
		public function assignRef($name, &$value)
		{
			$this->_data[$name] = &$value;
		} // end assignRef();
		
		public function defined($name)
		{
			return isset($this->_data[$name]);
		} // end defined();
		
		public function remove($name)
		{
			if(isset($this->_data[$name]))
			{
				unset($this->_data[$name]);
				if(isset($this->_formatInfo[$name]))
				{
					unset($this->_formatInfo[$name]);
				}
				return true;
			}
			return false;
		} // end remove();
		
		static public function assignGlobal($name, $value)
		{
			self::$_global[$name] = $value;
		} // end assignGlobal();
		
		static public function assignGroupGlobal($values)
		{
			self::$_global = array_merge(self::$_global, $values);
		} // end assignGroupGlobal();
		
		static public function assignRefGlobal($name, &$value)
		{
			self::$_global[$name] = &$value;
		} // end assignRefGlobal();
		
		static public function definedGlobal($name)
		{
			return isset(self::$_global[$name]);
		} // end definedGlobal();
		
		static public function removeGlobal($name)
		{
			if(isset(self::$_global[$name]))
			{
				unset(self::$_global[$name]);
				return true;
			}
			return false;
		} // end removeGlobal();
		
		public function setFormat($item, $format)
		{
			$this->_formatInfo[$item] = $format;
		} // end setFormat();
		
		// TODO: What about formats for global data?!
		
		/*
		 * Dynamic inheritance
		 */
		
		public function inherit($source, $destination = null)
		{
			if(is_null($destination))
			{
				$this->_inheritance[$this->_template] = str_replace(array('/', ':', '\\'), '__', $source);
				$this->_cplInheritance[$this->_template] = $source;
				return;
			}
			$this->_inheritance[$source] = str_replace(array('/', ':', '\\'), '__',$destination);
			$this->_cplInheritance[$source] = $destination;			
		} // end inherit();
		
		/*
		 * Internal use
		 */
		public function _parse($output, $mode, $cached = false, $exception = true)
		{
			if($this->_tpl->debugConsole)
			{
				$time = microtime(true);
			}
			$this->_tf = $this->_tpl->getTranslationInterface();
			if($this->_tpl->compileMode != Opt_Class::CM_PERFORMANCE)
			{
				list($compileName, $compileTime) = $this->_preprocess($mode, $exception);	
				if(is_null($compileName))
				{
					return false;
				}
			}
			else
			{
				$compileName = $this->_convert($this->_template);
				$compileTime = null;
				if(!$exception && !file_exists($compileName))
				{
					return false;
				}
			}
			
			$old = error_reporting($this->_tpl->errorReporting);
			require($this->_tpl->compileDir.$compileName);
			error_reporting($old);

			// The counter stops, if the time counting has been enabled for the debug console purposes
			if(isset($time))
			{
				Opt_Support::addView($this->_template, $output->getName(), microtime(true) - $time, $cached);
			}
			return true;
		} // end _parse();

		protected function _preprocess($mode, $exception = true)
		{
			$compiled = $this->_convert($this->_template);
			$item = $this->_tpl->_stream($this->_template);
			$compileTime = @filemtime($this->_tpl->compileDir.$compiled);
			$result = NULL;
			
			// Here the "rebuild" compilation mode is processed
			if($this->_tpl->compileMode == Opt_Class::CM_REBUILD)
			{
				if(!file_exists($item))
				{
					if(!$exception)
					{
						return array(NULL, NULL);
					}
					throw new Opt_TemplateNotFound_Exception($item);
				}
				$result = file_get_contents($item);
			}
			else
			{				
				// Otherwise, we perform a modification test.
				$rootTime = @filemtime($item);
				if($rootTime === false)
				{
					if(!$exception)
					{
						return array(NULL, NULL);
					}
					throw new Opt_TemplateNotFound_Exception($item);
				}
				if($compileTime === false || $compileTime < $rootTime)
				{
					$result = file_get_contents($item);
				}
			}

			if(is_null($result))
			{
				return array($compiled, $compileTime);
			}

			$compiler = $this->_tpl->getCompiler();
			$compiler->setInheritance($this->_cplInheritance);
			$compiler->setFormatList($this->_formatInfo);
			$compiler->set('branch', $this->_branch);
			$compiler->compile($result, $this->_template, $compiled, $mode);
			return array($compiled, $compileTime);
		} // end _preprocess();

		protected function _massPreprocess($filename, $compileTime, $templates)
		{
		/*	if($this->debugConsole)
			{
				$inherited = $templates;
				array_unshift($inherited, $filename);
				Opl_Support::debugAddTemplate(Opl_Support::INHERITED_TPL, $inherited);
			}
		*/
			switch($this->_tpl->compileMode)
			{
				case Opt_Class::CM_PERFORMANCE:
				case Opt_Class::CM_REBUILD:
					return false;	// We return false even here, because the compilation has already been done in _parse()
				case Opt_Class::CM_DEFAULT:
					$cnt = sizeof($templates);
				//	$templates = array();
					
					// TODO: Check whether the object as array key works :P
					for($i = 0; $i < $cnt; $i++)
					{
						$templates[$i] = $this->_tpl->_stream($templates[$i]);
						$time = @filemtime($templates[$i]);
						if(is_null($time))
						{
							throw new Opt_TemplateNotFound_Exception($templates[$i]);
						}
						if($time >= $compileTime)
						{
							return true;
						}
					}
					return false;
			}
		} // end _massPreprocess();

		public function _convert($filename)
		{
			$list = array();
			if(sizeof($this->_inheritance) > 0)
			{
				$list = $this->_inheritance;
				sort($list);
			}
			$list[] = str_replace(array('/', ':', '\\'), '__', $filename);
			if(!is_null($this->_tpl->compileId))
			{
				return $this->_tpl->compileId.'_'.implode('/', $list).'.php';
			}
			return implode('/', $list).'.php';
		} // end _convert();
		
		public function _compile($filename, $mode)
		{
			$compiled = $this->_convert($filename);
			$compiler = $this->_tpl->getCompiler();
			$compiler->set('branch', $this->_branch);
			$compiler->compile($this->_tpl->_getSource($filename), $filename, $compiled, $mode);
			return time();
		} // end _compile();
	} // end Opt_View;
