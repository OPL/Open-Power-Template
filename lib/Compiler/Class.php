<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id$
 */

/**
 * The main compiler class that contains the compilation logic
 * and the structure. The compiler is able to compile one result
 * file at the same time, possibly combining it from several
 * templates. The compilation MUST NOT be called from the interior
 * of the compiler code - the compiler prevents such actions.
 *
 * @package Compiler
 */
class Opt_Compiler_Class
{
	const DEFAULT_FORMAT_CLASS = 'Array';

	const ESCAPE_ON = true;
	const ESCAPE_OFF = false;
	const ESCAPE_BOTH = 2;

	// Current compilation
	protected $_template = NULL;
	protected $_attr = array();
	protected $_stack = NULL;
	protected $_node = NULL;

	protected $_output = NULL;
	protected $_newQueue = NULL;

	static protected $_recursionDetector = NULL;

	// Compiler info
	/**
	 * The array of available parsers (Opt_Parser_Interface)
	 * @internal
	 * @var array
	 */
	protected $_parsers = array();
	/**
	 * The array of available expression engines (Opt_Expression_Interface)
	 * @internal
	 * @var array
	 */
	protected $_expressions = array();
	/**
	 * The array of tags mapped to corresponding processors.
	 * @internal
	 * @var array
	 */
	protected $_tags = array();
	/**
	 * The array of attributes mapped to corresponding processors.
	 * @internal
	 * @var array
	 */
	protected $_attributes = array();
	/**
	 * The list of current conversions.
	 * @internal
	 * @var array
	 */
	protected $_conversions = array();
	/**
	 * The array of available processors identified by their names.
	 * @var array
	 */
	protected $_processors = array();
	/**
	 * The list of template dependencies.
	 * @var array
	 */
	protected $_dependencies = array();

	// ** OPT parser info

	/**
	 * The reference to the main OPT class.
	 * @var Opt_Class
	 */
	protected $_tpl;
	/**
	 * The list of instruction processors imported from Opt_Class
	 * @internal
	 * @var array
	 */
	protected $_instructions;
	/**
	 * The list of registered namespaces that need to be parsed by the compiler.
	 * Imported from Opt_Class
	 * @internal
	 * @var array
	 */
	protected $_namespaces;
	/**
	 * The list of registered template functions imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_functions;
	/**
	 * The list of available classes imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_classes;
	/**
	 * The list of registered blocks imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_blocks;
	/**
	 * The list of registered components imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_components;
	/**
	 * The translation inteface imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_tf;
	/**
	 * The list of registered extra entities imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_entities;
	/**
	 * The list of registered expression engines imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_exprEngines;
	/**
	 * The list of registered expression modifiers imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_modifiers;
	/**
	 * The data format information imported from Opt_View.
	 * @internal
	 * @var array
	 */
	protected $_formatInfo;
	/**
	 * The list of registered data formats imported from Opt_Class.
	 * @internal
	 * @var array
	 */
	protected $_formats = array();
	/**
	 * The list of created data format objects.
	 * @internal
	 * @var array
	 */
	protected $_formatObj = array();
	/**
	 * Some inheritance stuff...
	 * @internal
	 * @var array
	 */
	protected $_inheritance;

	/**
	 * The CDF manager instance
	 * @var Opt_Cdf_Manager
	 */
	protected $_cdfManager;

	/**
	 * The CDF loader instance.
	 */
	protected $_cdfLoader;

	/**
	 * The context stack.
	 * @var SplStack
	 */
	protected $_contextStack;

	// Help fields
	private $_charset = null;
	private $_initialMemory = null;
	private $_comments = 0;
	private $_standalone = false;

	static private $_templates = array();

	/**
	 * Creates a new instance of the template compiler. The compiler can
	 * be created, using the settings from the main OPT class or another
	 * compiler.
	 *
	 * @param Opt_Class|Opt_Compiler_Class $tpl The initial object.
	 */
	public function __construct($tpl)
	{
		if($tpl instanceof Opt_Class)
		{
			$this->_tpl = $tpl;
			$this->_namespaces = $tpl->_getList('_namespaces');
			$this->_classes = $tpl->_getList('_classes');
			$this->_functions = $tpl->_getList('_functions');
			$this->_components = $tpl->_getList('_components');
			$this->_blocks = $tpl->_getList('_blocks');
			$this->_phpFunctions = $tpl->_getList('_phpFunctions');
			$this->_formats = $tpl->_getList('_formats');
			$this->_tf = $tpl->_getList('_tf');
			$this->_entities = $tpl->_getList('_entities');
			$this->_exprEngines = $tpl->_getList('_exprEngines');
			$this->_modifiers = $tpl->_getList('_modifiers');
			$this->_charset = strtoupper($tpl->charset);

			// Create the processors and call their configuration method in the constructors.
			$instructions = $tpl->_getList('_instructions');
			foreach($instructions as $instructionClass)
			{
				$obj = new $instructionClass($this, $tpl);
				$this->_processors[$obj->getName()] = $obj;

				// Add the tags and attributes registered by this processor.
				foreach($obj->getInstructions() as $item)
				{
					$this->_instructions[$item] = $obj;
				}
				foreach($obj->getAttributes() as $item)
				{
					$this->_attributes[$item] = $obj;
				}
			}
		}
		elseif($tpl instanceof Opt_Compiler_Class)
		{
			// Simply import the data structures from that compiler.
			$this->_tpl = $tpl->_tpl;
			$this->_namespaces = $tpl->_namespaces;
			$this->_classes = $tpl->_classes;
			$this->_functions = $tpl->_functions;
			$this->_components = $tpl->_components;
			$this->_blocks = $tpl->_blocks;
			$this->_inheritance = $tpl->_inheritance;
			$this->_formatInfo = $tpl->_formatInfo;
			$this->_formats = $tpl->_formats;
			$this->_tf = $tpl->_tf;
			$this->_processor = $tpl->_processors;
			$this->_instructions = $tpl->_instructions;
			$this->_attributes = $tpl->_attributes;
			$this->_charset = $tpl->_charset;
			$this->_entities = $tpl->_entities;
			$this->_exprEngines = $tpl->_exprEngines;
			$this->_modifiers = $tpl->_modifiers;
			$tpl = $this->_tpl;
		}

		// We've just thrown the performance away by loading the compiler, so this won't make things worse
		// but the user may be happy :). However, don't show this message, if we are in the performance mode.
		if(!is_writable($tpl->compileDir) && $tpl->_compileMode != Opt_Class::CM_PERFORMANCE)
		{
			throw new Opt_FilesystemAccess_Exception('compilation', 'writeable');
		}

		// If the debug console is active, preload the XML tree classes.
		// Without it, the debug console would show crazy things about the memory usage.
		if($this->_tpl->debugConsole && !class_exists('Opt_Xml_Root'))
		{
			Opl_Loader::load('Opt_Xml_Root');
			Opl_Loader::load('Opt_Xml_Text');
			Opl_Loader::load('Opt_Xml_Cdata');
			Opl_Loader::load('Opt_Xml_Element');
			Opl_Loader::load('Opt_Xml_Attribute');
			Opl_Loader::load('Opt_Xml_Expression');
			Opl_Loader::load('Opt_Xml_Prolog');
			Opl_Loader::load('Opt_Xml_Dtd');
			Opl_Loader::load('Opt_Format_Array');
		}

		$this->_contextStack = new SplStack;
	} // end __construct();

	/**
	 * Allows to clone the original compiler, creating new instruction
	 * processors for the new instance.
	 */
	public function __clone()
	{
		$this->_processors = array();
		$this->_tags = array();
		$this->_attributes = array();
		$this->_conversions = array();
		$instructions = $this->_tpl->_getList('_instructions');
		$cnt = sizeof($instructions);
		for($i = 0; $i < $cnt; $i++)
		{
			$name = 'Opt_Instruction_'.$instructions[$i];
			$obj = new $name($this, $tpl);
			$this->_processors[$obj->getName()] = $obj;
		}
	} // end __clone();

	/*
	 * General purpose tools and utilities
	 */

	/**
	 * Returns the currently processed template file name.
	 *
	 * @static
	 * @return String The currently processed template name
	 */
	static public function getCurrentTemplate()
	{
		return end(self::$_templates);
	} // end getCurrentTemplate();

	/**
	 * Cleans the compiler state after the template compilation.
	 * It is necessary in the exception processing - if the exception
	 * is thrown in the middle of the compilation, the compiler becomes
	 * useless, because it is locked. The compilation algorithm automatically
	 * filters the exceptions, cleans the compiler state and throws the captured
	 * exceptions again, to the script.
	 *
	 * @static
	 */
	static public function cleanCompiler()
	{
		self::$_recursionDetector = null;
		self::$_templates = array();
	} // end cleanCompiler();

	/**
	 * Returns the value of the compiler state variable or
	 * NULL if the variable is not set.
	 *
	 * @param String $name Compiler variable name
	 * @return Mixed The compiler variable value.
	 */
	public function get($name)
	{
		if(!isset($this->_attr[$name]))
		{
			return NULL;
		}
		return $this->_attr[$name];
	} // end get();

	/**
	 * Creates or modifies the compiler state variable.
	 *
	 * @param String $name The name
	 * @param Mixed $value The value
	 */
	public function set($name, $value)
	{
		$this->_attr[$name] = $value;
	} // end set();

	/**
	 * Adds the escaping formula to the specified expression using the current escaping
	 * rules:
	 *
	 * 1. The $status variable.
	 * 2. The current template settings.
	 * 3. The OPT settings.
	 *
	 * @param char $modifier The modifier used to escape the expression.
	 * @param string $expression The PHP expression to be escaped.
	 * @param string $format The format of the expression.
	 * @param boolean $status The status of escaping for this expression or NULL, if not set.
	 * @return string The expression with the escaping formula added, if necessary.
	 */
	public function escape($modifier, $expression, $format, $status = null)
	{
		// OPT Configuration
		$escape = $this->_tpl->escape;

		// Template configuration
		if($this->get('escaping') !== null)
		{
			$escape = ($this->get('escaping') == true ? true : false);
		}

		// Expression settings
		if($status !== null)
		{
			$escape = ($status == true ? true : false);
		}

		// Apply the escaping subroutine defined by the modifier.
		if(!array_key_exists($modifier, $this->_modifiers))
		{
			throw new Opt_InvalidExpressionModifier_Exception($modifier, $expression);
		}
		if($escape && !empty($this->_modifiers[$modifier]))
		{
			return $this->_modifiers[$modifier].'('.Opt_Compiler_Utils::cast($this->_cdfManager, $expression, $format, 'Scalar').')';
		}
		return $expression;
	} // end escape();

	/**
	 * Returns the CDF manager.
	 *
	 * @return Opt_Cdf_Manager
	 */
	public function getCdfManager()
	{
		if($this->_cdfManager === null)
		{
			$this->_cdfManager = new Opt_Cdf_Manager(
				$this->_tpl,
				$this->_tpl->_getList('_formats')
			);
		}

		return $this->_cdfManager;
	} // end getCdfManager();

	/**
	 * Returns the format object for the specified variable.
	 *
	 * @deprecated
	 * @param String $variable The variable identifier.
	 * @param Boolean $restore optional Whether to load a previously created format object (false) or to create a new one.
	 * @return Opt_Format_Class The format object.
	 */
	public function getFormat($variable, $restore = false)
	{
		return $this->getCdfManager()->getFormat(null, $variable);
	} // end getFormat();

	/**
	 * Creates a format object for the specified description string.
	 *
	 * @deprecated
	 * @param String $variable The variable name (for debug purposes)
	 * @param String $hc The description string.
	 * @return Opt_Format_Class The newly created format object.
	 */
	public function createFormat($variable, $hc)
	{
		throw new Opt_NotSupported_Exception('Opt_Compiler_Class::createFormat', 'deprecated');
	} // end createFormat();

	/**
	 * Allows to export the data format configuration to the compiler.
	 *
	 * @param Array $list An associative array of pairs "variable => format description"
	 */
	public function addFormats(array $globalCdf, array $localCdf, array $globalDefs, array $localDefs)
	{
		$manager = $this->getCdfManager();

		if(sizeof($globalCdf) > 0 || sizeof($localCdf) > 0)
		{
			// Initialize the loader
			if($this->_cdfLoader === null)
			{
				$this->_cdfLoader = new Opt_Cdf_Loader($manager);
			}

			$manager->setLocality(Opt_Cdf_Manager::AS_GLOBAL);
			foreach($globalCdf as $cdf)
			{
				$this->_cdfLoader->load($this->_tpl->cdfDir.$cdf);
			}

			foreach($localCdf as $cdf)
			{
				$manager->setLocality($cdf);
				$this->_cdfLoader->load($this->_tpl->cdfDir.$cdf);
			}

			$this->_cdfManager->setLocals($localCdf);
		}

		// Now manual definitions...
		$manager->setLocality(Opt_Cdf_Manager::AS_GLOBAL);
		foreach($globalDefs as $item => &$idList)
		{
			foreach($idList as $id => $format)
			{
				$this->_cdfManager->addFormat($item, $id, $format, array());
			}
		}
		$manager->setLocality(Opt_Cdf_Manager::AS_LOCAL);
		foreach($localDefs as $item => &$idList)
		{
			foreach($idList as $id => $format)
			{
				$this->_cdfManager->addFormat($item, $id, $format, array());
			}
		}
	} // end addFormats();

	/**
	 * Converts the specified item into another string using one of the
	 * registered patterns. If the pattern is not found, the method returns
	 * the original item unmodified.
	 *
	 * @param String $item The item to be converted.
	 * @return String
	 */
	public function convert($item)
	{
		// the converter allows to convert one name into another and keep it, if there is no
		// conversion pattern. Used in connection with sections + snippets.
		if(isset($this->_conversions[$item]))
		{
			return $this->_conversions[$item];
		}
		return $item;
	} // end convert();

	/**
	 * Creates a new conversion pattern. The string $from will be converted
	 * into $to.
	 *
	 * @param String $from The original string
	 * @param String $to The new string
	 */
	public function setConversion($from, $to)
	{
		$this->_conversions[$from] = $to;
	} // end setConversion();

	/**
	 * Removes the conversion pattern from the compiler memory.
	 *
	 * @param String $from The original string.
	 * @return Boolean
	 */
	public function unsetConversion($from)
	{
		if(isset($this->_conversions[$from]))
		{
			unset($this->_conversions[$from]);
			return true;
		}
		return false;
	} // end unsetConversion();

	/**
	 * Registers the dynamic inheritance rules for the templates. The
	 * array taken as a parameter must be an associative array of pairs
	 * 'extending' => 'extended' file names.
	 *
	 * @param Array $inheritance The list of inheritance rules.
	 */
	public function setInheritance(Array $inheritance)
	{
		$this->_inheritance = $inheritance;
	} // end setInheritance();

	/**
	 * Parses the entities in the specified text.
	 *
	 * @param String $text The original text
	 * @return String
	 */
	public function parseEntities($text)
	{
		return preg_replace_callback('/\&(([a-zA-Z\_\:]{1}[a-zA-Z0-9\_\:\-\.]*)|(\#((x[a-fA-F0-9]+)|([0-9]+))))\;/', array($this, '_decodeEntity'), $text);
	//	return htmlspecialchars_decode(str_replace(array_keys($this->_entities), array_values($this->_entities), $text));
	} // end parseEntities();

	/**
	 * Replaces only OPT-specific entities &lb; and &rb; to the corresponding
	 * characters.
	 *
	 * @param String $text Input text
	 * @return String output text
	 */
	public function parseShortEntities($text)
	{
		return str_replace(array('&lb;', '&rb;'), array('{', '}'), $text);
	} // end parseShortEntities();

	/**
	 * Replaces the XML special characters back to entities with smart ommiting of &
	 * that already creates an entity.
	 *
	 * @param String $text Input text.
	 * @return String Output text.
	 */
	public function parseSpecialChars($text)
	{
		return htmlspecialchars($text);
		return preg_replace_callback('/(\&\#?[a-zA-Z0-9]*\;)|\<|\>|\"|\&/', array($this, '_entitize'), $text);
	} // end parseSpecialChars();

	/**
	 * Returns 'true', if the argument is a valid identifier. An identifier
	 * must begin with a letter or underscore, and later, the numbers are also
	 * allowed.
	 *
	 * @param String $id The tested string
	 * @return Boolean
	 */
	public function isIdentifier($id)
	{
		return preg_match('/[A-Za-z]([A-Za-z0-9.\_]|\-)*/si', $id);
	} // end isIdentifier();

	/**
	 * Checks whether the specified tag name is registered as an instruction.
	 * Returns its processor in case of success or NULL.
	 *
	 * @deprecated
	 * @param String $tag The tag name (with the namespace)
	 * @return Opt_Compiler_Processor|NULL The processor that registered this tag.
	 */
	public function isInstruction($tag)
	{
		if(isset($this->_instructions[$tag]))
		{
			return $this->_instructions[$tag];
		}
		return NULL;
	} // end isInstruction();

	/**
	 * Returns true, if the argument is the name of an OPT attribute.
	 *
	 * @deprecated
	 * @param String $tag The attribute name
	 * @return Opt_Compiler_Processor
	 */
	public function isOptAttribute($tag)
	{
		if(isset($this->_attributes[$tag]))
		{
			return $this->_attributes[$tag];
		}
		return NULL;
	} // end isOptAttribute();

	/**
	 * Returns true, if the argument is the OPT function name.
	 *
	 * @deprecated
	 * @param String $name The function name
	 * @return Boolean
	 */
	public function isFunction($name)
	{
		if(isset($this->_functions[$name]))
		{
			return $this->_functions[$name];
		}
		return NULL;
	} // end isFunction();

	/**
	 * Returns true, if the argument is the name of the class
	 * accepted by OPT.
	 *
	 * @deprecated
	 * @param String $id The class name.
	 * @return Boolean
	 */
	public function isClass($id)
	{
		if(isset($this->_classes[$id]))
		{
			return $this->_classes[$id];
		}
		return NULL;
	} // end isClass();

	/**
	 * Returns true, if the argument is the name of the namespace
	 * processed by OPT.
	 *
	 * @param String $ns The namespace name
	 * @return Boolean
	 */
	public function isNamespace($ns)
	{
		return in_array($ns, $this->_namespaces);
	} // end isNamespace();

	/**
	 * Returns true, if the argument is the name of the component tag.
	 *
	 * @deprecated
	 * @param String $component The component tag name
	 * @return Boolean
	 */
	public function isComponent($component)
	{
		return isset($this->_components[$component]);
	} // end isComponent();

	/**
	 * Returns true, if the argument is the name of the block tag.
	 *
	 * @deprecated
	 * @param String $block The block tag name.
	 * @return Boolean
	 */
	public function isBlock($block)
	{
		return isset($this->_blocks[$block]);
	} // end isComponent();

	/**
	 * Returns true, if the argument is the processor name.
	 *
	 * @deprecated
	 * @param String $name The instruction processor name
	 * @return Boolean
	 */
	public function isProcessor($name)
	{
		if(!isset($this->_processors[$name]))
		{
			return NULL;
		}
		return $this->_processors[$name];
	} // end isProcessor();

	/**
	 * Returns the processor object with the specified name. If
	 * the processor does not exist, it generates an exception.
	 *
	 * @deprecated
	 * @param String $name The processor name
	 * @return Opt_Compiler_Processor
	 */
	public function processor($name)
	{
		if(!isset($this->_processors[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('processor', $name);
		}
		return $this->_processors[$name];
	} // end processor();

	/**
	 * Returns the component class name assigned to the specified
	 * XML tag. If the component class is not registered, it throws
	 * an exception.
	 *
	 * @deprecated
	 * @param String $name The component XML tag name.
	 * @return Opt_Component_Interface
	 */
	public function component($name)
	{
		if(!isset($this->_components[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('component', $name);
		}
		return $this->_components[$name];
	} // end component();

	/**
	 * Returns the block class name assigned to the specified
	 * XML tag. If the block class is not registered, it throws
	 * an exception.
	 *
	 * @deprecated
	 * @param String $name The block XML tag name.
	 * @return Opt_Block_Interface
	 */
	public function block($name)
	{
		if(!isset($this->_blocks[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('block', $name);
		}
		return $this->_blocks[$name];
	} // end block();

	/**
	 * Returns the list of instruction attribute processors in format
	 * instruction attribute XML name => processor object
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	} // end getAttributes();

	/**
	 * Returns the processor that registered the specified
	 * instruction attribute.
	 *
	 * @param string $name The instruction XML name
	 * @return Opt_Compiler_Processor
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getAttribute($name)
	{
		if(!isset($this->_attributes[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('instruction attribute', $name);
		}
		return $this->_attributes[$name];
	} // end getAttribute();

	/**
	 * Returns true, if the specified instruction attribute exists.
	 *
	 * @param string $name The instruction XML name
	 * @return boolean
	 */
	public function hasAttribute($name)
	{
		return isset($this->_attributes[$name]);
	} // end hasAttribute();

	/**
	 * Returns the list of instruction processors in format
	 * instruction XML name => processor object
	 *
	 * @return array
	 */
	public function getInstructions()
	{
		return $this->_instructions;
	} // end getInstructions();

	/**
	 * Returns the processor that registered the specified
	 * instruction.
	 *
	 * @param string $name The instruction XML name
	 * @return Opt_Compiler_Processor
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getInstruction($name)
	{
		if(!isset($this->_instructions[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('instruction', $name);
		}
		return $this->_instructions[$name];
	} // end getInstruction();

	/**
	 * Returns true, if the specified instruction exists.
	 *
	 * @param string $name The instruction XML name
	 * @return boolean
	 */
	public function hasInstruction($name)
	{
		return isset($this->_instructions[$name]);
	} // end hasInstruction();

	/**
	 * Returns the list of processors in format
	 * name => object
	 *
	 * @return array
	 */
	public function getProcessors()
	{
		return $this->_processors;
	} // end getProcessors();

	/**
	 * Returns the processor object.
	 *
	 * @param string $name The processor name
	 * @return Opt_Compiler_Processor
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getProcessor($name)
	{
		if(!isset($this->_processors[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('processor', $name);
		}
		return $this->_processors[$name];
	} // end getProcessor();

	/**
	 * Returns true, if the specified processor exists.
	 *
	 * @param string $name The processor name
	 * @return boolean
	 */
	public function hasProcessor($name)
	{
		return isset($this->_processors[$name]);
	} // end hasProcessor();

	/**
	 * Returns the list of blocks in format
	 * XML name => class name
	 *
	 * @return array
	 */
	public function getBlocks()
	{
		return $this->_blocks;
	} // end getBlocks();

	/**
	 * Returns the class name of the specified block.
	 *
	 * @param string $name The block XML name
	 * @return string
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getBlock($name)
	{
		if(!isset($this->_blocks[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('block', $name);
		}
		return $this->_blocks[$name];
	} // end getBlock();

	/**
	 * Returns true, if the specified block exists.
	 *
	 * @param string $name The block XML name
	 * @return boolean
	 */
	public function hasBlock($name)
	{
		return isset($this->_blocks[$name]);
	} // end hasBlock();

	/**
	 * Returns the list of components in format
	 * XML name => class name
	 *
	 * @return array
	 */
	public function getComponents()
	{
		return $this->_components;
	} // end getComponents();

	/**
	 * Returns the class name of the specified component.
	 *
	 * @param string $name The component XML name
	 * @return string
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getComponent($name)
	{
		if(!isset($this->_components[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('component', $name);
		}
		return $this->_components[$name];
	} // end getComponent();

	/**
	 * Returns true, if the specified component exists.
	 *
	 * @param string $name The component XML name
	 * @return boolean
	 */
	public function hasComponent($name)
	{
		return isset($this->_components[$name]);
	} // end hasComponent();

	/**
	 * Returns the list of expression engines in format
	 * name => class name
	 *
	 * @return array
	 */
	public function getExpressionEngines()
	{
		return $this->_exprEngines;
	} // end getExpressionEngines();

	/**
	 * Returns the class name of the specified expression engine.
	 *
	 * @param string $name The expression engine name
	 * @return string
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getExpressionEngine($name)
	{
		if(!isset($this->_exprEngines[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('expression enigne', $name);
		}
		return $this->_exprEngines[$name];
	} // end getExpressionEngine();

	/**
	 * Returns true, if the specified expression engine exists.
	 *
	 * @param string $name The expression engine name
	 * @return boolean
	 */
	public function hasExpressionEngine($name)
	{
		return isset($this->_exprEngines[$name]);
	} // end hasExpressionEngine();

	/**
	 * Returns the list of expression modifiers in format
	 * modifier => function
	 *
	 * @return array
	 */
	public function getModifiers()
	{
		return $this->_modifiers;
	} // end getModifiers();

	/**
	 * Returns the specified modifier data. If the modifier
	 * does not exist, it throws the exception.
	 *
	 * @param char $name The modifier name
	 * @return string
	 * @throws Opt_ObjectNotExists_Exception
	 */
	public function getModifier($name)
	{
		if(!isset($this->_modifiers[$name]))
		{
			throw new Opt_ObjectNotExists_Exception('modifier', $name);
		}
		return $this->_modifiers[$name];
	} // end getModifier();

	/**
	 * Returns true, if the specified modifier exists.
	 *
	 * @param char $name The modifier name
	 * @return boolean
	 */
	public function hasModifier($name)
	{
		return isset($this->_modifiers[$name]);
	} // end hasModifier();

	/**
	 * Returns the context stack object.
	 * @return SplStack
	 */
	public function getContextStack()
	{
		return $this->_contextStack;
	} // end getContextStack();

	/**
	 * Returns the template name that is inherited by the template '$name'
	 *
	 * @param String $name The "current" template file name
	 * @return String
	 */
	public function inherits($name)
	{
		if(isset($this->_inheritance[$name]))
		{
			return $this->_inheritance[$name];
		}
		return NULL;
	} // end inherits();

	/**
	 * Adds the template file name to the dependency list of the currently
	 * compiled file, so that it could be checked for modifications during
	 * the execution.
	 *
	 * @param String $template The template file name.
	 */
	public function addDependantTemplate($template)
	{
		if(in_array($template, $this->_dependencies))
		{
			$exception = new Opt_InheritanceRecursion_Exception($template);
			$exception->setData($this->_dependencies);
			throw $exception;
		}

		$this->_dependencies[] = $template;
	} // end addDependantTemplate();

	/**
	 * Imports the dependencies from another compiler object and adds them
	 * to the actual dependency list.
	 *
	 * @param Opt_Compiler_Class $compiler Another compiler object.
	 */
	public function importDependencies(Opt_Compiler_Class $compiler)
	{
		$this->_dependencies = array_merge($this->_dependencies, $compiler->_dependencies);
	} // end importDependencies();

	/*
	 * Node management tools.
	 */

	 /**
	  * Appends the text to the output.
	  * @param String $text The text to append.
	  */
	public function appendOutput($text)
	{
		$this->_output .= $text;
	} // end appendOutput();

	/**
	 * Sets the new node children queue used in stages 2 and 3 of the compilation.
	 *
	 * @param SplQueue|Opt_Xml_Scannable $children The children list.
	 */
	public function setChildren($children)
	{
		if($children instanceof SplQueue)
		{
			if($children->count() > 0)
			{
				$this->_newQueue = $children;
			}
		}
		else if($children instanceof Opt_Xml_Scannable)
		{
			if($children->hasChildren() > 0)
			{
				$this->_newQueue = new SplQueue;
				foreach($children as $child)
				{
					$this->_newQueue->enqueue($child);
				}
			}
		}
	} // end setChildren();

	/*
	 * Internal tools and utilities
	 */

	/**
	 * Adds the PHP code with dependencies to the code buffers in the tree
	 * root node.
	 *
	 * @internal
	 * @param Opt_Xml_Node $tree The tree root node.
	 */
	protected function _addDependencies($tree)
	{
		// OK, there is really some info to include!
		$list = '';
		foreach($this->_dependencies as $a)
		{
			$list .= '\''.$a.'\',';
		}

		$tree->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'if(!$this->_massPreprocess($compileTime, array('.$list.'))){ ');
		$tree->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' }else{ $compileTime = $this->_compile($this->_template, $mode); require(__FILE__); } ');
	} // end _addDependencies();

	/**
	 * Closes the XML comment for the commented item.
	 *
	 * @internal
	 * @param Opt_Xml_Node $item The commented item.
	 * @param String &$output The reference to the output buffer.
	 */
	protected function _closeComments($item)
	{
		if($item->get('commented'))
		{
			$this->_comments--;
			if($this->_comments == 0)
			{
				// According to the XML grammar, the construct "--->" is not allowed.
				if(strlen($this->_output) > 0 && $this->_output[strlen($this->_output)-1] == '-')
				{
					throw new Opt_XmlComment_Exception('--->');
				}

				$this->_output .= '-->';
			}
		}
	} // end _closeComments();

	/*
	 * Main migration methods
	 */

	/**
	 * The migration launcher. Executes template parsing and template migration
	 * process, then it saves migrated template.
	 *
	 * @param Opt_Xml_Node $node Node to be processed
	 * @return Opt_
	 */
	public function migrate()
	{
		// Implement
	} //end migrate();

	protected function _migrate(Opt_Xml_Node $node)
	{
		//$this->_debugPrintNodes($node);
		$queue = new SplQueue;
		$stack = new SplStack;

		$queue->enqueue($node);
		while(true)
		{
			$item = $queue->dequeue();
			// Now process the node.
			$item->preMigrate($this);
			if($this->_newQueue !== null)
			{
				// Starting next level.
				$stack->push(array($item, $queue));
				$queue = $this->_newQueue;
				$this->_newQueue = null;
			}
			else
			{
				$item->postProcess($this);
			}

			// Closing the current level.
			while($queue->count() == 0)
			{
				if($stack->count() == 0)
				{
					break 2;
				}
				unset($queue);
				list($item, $queue) = $stack->pop();
				$item->postProcess($this);
			}
		}
	} // end _migrate();

	/*
	 * Main compilation methods
	 */

	/**
	 * The compilation launcher. It executes the proper compilation steps
	 * according to the inheritance rules etc.
	 *
	 * @param String $code The source code to be compiled.
	 * @param String $filename The source template filename.
	 * @param String $compiledFilename The output template filename.
	 * @param Int $mode The compilation mode.
	 */
	public function compile($code, $filename, $compiledFilename, $mode)
	{
		$manager = $this->getCdfManager();

		// Initialize the context.
		$this->_contextStack->push(
			new Opt_Compiler_Context($this, Opt_Compiler_Context::TEMPLATE_CTX, $filename)
		);

		try
		{
			// First, we select a parser.
			if(!isset($this->_parsers[$mode]))
			{
				$this->_parsers[$mode] = new $mode;
				if(!$this->_parsers[$mode] instanceof Opt_Parser_Interface)
				{
					throw new Opt_InvalidParser_Exception($mode);
				}
			}
			$parser = $this->_parsers[$mode];
			$parser->setCompiler($this);

			// We cannot compile two templates at the same time
			if(!is_null($this->_template))
			{
				throw new Opt_CompilerLocked_Exception($filename, $this->_template);
			}

			// Detecting recursive inclusion
			if(is_null(self::$_recursionDetector))
			{
				self::$_recursionDetector = array(0 => $filename);
				$weFree = true;
			}
			else
			{
				if(in_array($filename, self::$_recursionDetector))
				{
					$exception = new Opt_CompilerRecursion_Exception($filename);
					$exception->setData(self::$_recursionDetector);
					throw $exception;
				}
				self::$_recursionDetector[] = $filename;
				$weFree = false;
			}
			// Cleaning up the processors
			foreach($this->_processors as $proc)
			{
				$proc->reset();
			}
			// Initializing the template launcher
			$this->set('template', $this->_template = $filename);
			$this->set('mode', $mode);
			$this->set('currentTemplate', $this->_template);
			array_push(self::$_templates, $filename);
			$this->_stack = new SplStack;
			$i = 0;
			$extend = $filename;

			$memory = 0;

			// The inheritance loop
			do
			{
				// Stage 1 - code compilation
				if($this->_tpl->debugConsole)
				{
					$initial = memory_get_usage();
					$tree = $parser->parse($extend, $code);
					// Migration stage - only if backwards compatibility is on
					if($this->_tpl->backwardCompatibility)
					{
						$tree = $this->_migrate($tree);
					}
					// Stage 2 - PHP tree processing
					$this->_stack = null;
					$this->_stage2($tree);
					$this->set('escape', NULL);
					unset($this->_stack);
					$memory += (memory_get_usage() - $initial);
					unset($code);
				}
				else
				{
					$tree = $parser->parse($extend, $code);
					unset($code);
					// Migration stage - only if backward compatibility is on
					if($this->_tpl->backwardCompatibility)
					{
						$this->_migrate($tree);
					}
					// Stage 2 - PHP tree processing
					$this->_stack = array();
					$this->_stage2($tree);
					$this->set('escape', NULL);
					unset($this->_stack);
				}


				// if the template extends something, load it and also process
				if(isset($extend) && $extend != $filename)
				{
					$this->addDependantTemplate($extend);
				}

				if(!is_null($snippet = $tree->get('snippet')))
				{
					$tree->dispose();
					unset($tree);

					// Change the specified snippet into a root node.
					$tree = new Opt_Xml_Root;
					$attribute = new Opt_Xml_Attribute('opt:use', $snippet);
					$this->processor('snippet')->processAttribute($tree, $attribute);
					$this->processor('snippet')->postprocessAttribute($tree, $attribute);

					$this->_stage2($tree, true);
					break;
				}
				if(!is_null($extend = $tree->get('extend')))
				{
					$tree->dispose();
					unset($tree);

					$this->set('currentTemplate', $extend);
					array_pop(self::$_templates);
					array_push(self::$_templates, $extend);
					$code = $this->_tpl->_getSource($extend);
				}
				$i++;
			}
			while(!is_null($extend));
			// There are some dependant templates. We must add a suitable PHP code
			// to the output.

			if(sizeof($this->_dependencies) > 0)
			{
				$this->_addDependencies($tree);
			}

			if($this->_tpl->debugConsole)
			{
				Opt_Support::addCompiledTemplate($this->_template, $memory);
			}

			// Stage 3 - linking the last tree
			if(!is_null($compiledFilename))
			{
				$this->_output = '';
				$this->_newQueue = null;
				$this->_dynamicBlocks = array();

				$this->_stage3($output, $tree);
				$tree->dispose();
				unset($tree);

				$this->_output = str_replace('?><'.'?php', '', $this->_output);

				// Build the directories, if needed.
				if(($pos = strrpos($compiledFilename, '/')) !== false)
				{
					$path = $this->_tpl->compileDir.substr($compiledFilename, 0, $pos);
					if(!is_dir($path))
					{
						mkdir($path, 0750, true);
					}
				}

				// Save the file
				if(sizeof($this->_dynamicBlocks) > 0)
				{
					file_put_contents($this->_tpl->compileDir.$compiledFilename.'.dyn', serialize($this->_dynamicBlocks));
				}
				file_put_contents($this->_tpl->compileDir.$compiledFilename, $this->_output);
				$this->_output = '';
				$this->_dynamicBlocks = null;
			}
			else
			{
				$tree->dispose();
			}
			array_pop(self::$_templates);
			$this->_inheritance = array();
			if($weFree)
			{
				// Do the cleanup.
				$this->_dependencies = array();
				self::$_recursionDetector = NULL;
				foreach($this->_processors as $processor)
				{
					$processor->reset();
				}
			}
			$this->_template = NULL;
			$manager->clearLocals();

			// Free the context.
			while($this->_contextStack->count() > 0)
			{
				$ctx = $this->_contextStack->pop();
				$ctx->dispose();
			}

			// Run the new garbage collector, if it is available.
		/*	if(version_compare(PHP_VERSION, '5.3.0', '>='))
			{
				gc_collect_cycles();
			}*/
		}
		catch(Exception $e)
		{
			// Free the context
			while($this->_contextStack->count() > 0)
			{
				$ctx = $this->_contextStack->pop();
				$ctx->dispose();
			}

			// Free the memory
			if(isset($tree))
			{
				$tree->dispose();
			}
			// Clean the compiler state in case of exception
			$this->_template = NULL;
			$this->_dependencies = array();
			self::$_recursionDetector = NULL;
			foreach($this->_processors as $processor)
			{
				$processor->reset();
			}
			$manager->clearLocals();

			// And throw it forward.
			throw $e;
		}
	} // end compile();

	/**
	 * Compilation - stage 2. Traversing through the tree and doing something
	 * with the tree and the nodes. The method is recursion-safe.
	 *
	 * @internal
	 * @param Opt_Xml_Node $node The initial node.
	 */
	protected function _stage2(Opt_Xml_Node $node)
	{
		$queue = new SplQueue;
		$stack = new SplStack;

		$queue->enqueue($node);
		while(true)
		{
			$item = $queue->dequeue();
			// Now process the node.
			$item->preProcess($this);
			if($this->_newQueue !== null)
			{
				// Starting next level.
				$stack->push(array($item, $queue));
				$queue = $this->_newQueue;
				$this->_newQueue = null;
			}
			else
			{
				$item->postProcess($this);
			}

			// Closing the current level.
			while($queue->count() == 0)
			{
				if($stack->count() == 0)
				{
					break 2;
				}
				unset($queue);
				list($item, $queue) = $stack->pop();
				$item->postProcess($this);
			}
		}
	} // end _stage2();

	/**
	 * Links the tree into a valid XML file with embedded PHP commands
	 * from the tag buffers. The method is recursion-free.
	 *
	 * @internal
	 * @param String &$output Where to store the output.
	 * @param Opt_Xml_Node $node The initial node.
	 */
	protected function _stage3(&$output, Opt_Xml_Node $node)
	{
		$queue = new SplQueue;
		$stack = new SplStack;

		$queue->enqueue($node);
		while(true)
		{
			$item = $queue->dequeue();
			if(!$item->get('hidden'))
			{
				// If the tag has the "commented" flag, we comment it
				// and its content.
				if($item->get('commented'))
				{
					$this->_comments++;
					if($this->_comments == 1)
					{
						$this->_output .= '<!--';
					}
				}
				// Now link the node.
				$item->preLink($this);
				if($this->_newQueue !== null)
				{
					// Starting next level.
					$stack->push(array($item, $queue));
					$queue = $this->_newQueue;
					$this->_newQueue = null;
				}
				else
				{
					$item->postLink($this);
					$this->_closeComments($item);
				}
			}
			// Closing the current level.
			while($queue->count() == 0)
			{
				if($stack->count() == 0)
				{
					break 2;
				}
				unset($queue);
				list($item, $queue) = $stack->pop();
				$item->postLink($this);
				$this->_closeComments($item);
			}
		}

		if($this->_tpl->stripWhitespaces)
		{
			$output = rtrim($output);
		}
	} // end _stage3();

	/**
	 * Compiles the XML attribute which may contain some dynamic data.
	 * The method automatically recognizes the registered expression
	 * engine and launches it.
	 *
	 * @param Opt_Xml_Attribute $attr The attribute to parse.
	 */
	public function compileAttribute(Opt_Xml_Attribute $attr)
	{
		$value = $attr->getValue();

		if(preg_match('/^([a-zA-Z0-9\_]+)\:([^\:].*)$/', $value, $found))
		{
			switch($found[1])
			{
				case 'parse':
						$result = $this->parseExpression($found[2], $found[1], self::ESCAPE_ON, $this->_tpl->attributeModifier);
						$attr->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$result['expression'].'; ');
					break;
				case 'str':
						$result = $this->parseExpression($found[2], $found[1], self::ESCAPE_ON, $this->_tpl->attributeModifier);
						$attr->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$result['escaped'].'; ');
					break;
				case null:
						$attr->setValue($found[2]);
					break;
			}
		}
	} // end compileAttribute();

	/**
	 * Executes the expression parsing and applies extra stuff, such as escaping on it.
	 * Returns the array containing the information on the compiled expression. The array
	 * fields are:
	 *  - bare - the compiled expression
	 *  - escaped - the escaped expression (no escaping - the same, as bare)
	 *
	 * @param string $expr The expression to parse
	 * @param string $ee The name of the expression engine
	 * @param int $escape Whether to use escaping or not.
	 * @param char $defaultModifier The default escaper for this expression.
	 * @return array
	 */
	public function parseExpression($expr, $ee = null, $escape = self::ESCAPE_BOTH, $defaultModifier = false)
	{
		// Autodetection of the expression engine
		if($ee === null)
		{
			if(preg_match('/^([a-zA-Z0-9\_]{2,})\:([^\:].*)$/', $expr, $found))
			{
				$expr = $found[2];
				$ee = $found[1];
			}
			else
			{
				$ee = $this->_tpl->expressionEngine;
			}
		}

		if(!isset($this->_exprEngines[$ee]))
		{
			throw new Opt_EngineNotExists_Exception($ee);
		}

		// The expression modifier must not be tokenized, so we
		// capture it before doing anything with the expression.
		$modifier = ($defaultModifier == false ? $this->_tpl->defaultModifier : $defaultModifier);
		$modifierSelected = false;
		if(preg_match('/^([^\'])\:[^\:]/', $expr, $found))
		{
			$modifierSelected = true;
			$modifier = $found[1];
			$expr = substr($expr, 2, strlen($expr) - 2);
		}

		// First, we select a parser.
		$mode = $this->_exprEngines[$ee];
		if(!isset($this->_expressions[$mode]))
		{
			$this->_expressions[$mode] = new $mode;
			if(!$this->_expressions[$mode] instanceof Opt_Expression_Interface)
			{
				throw new Opt_InvalidExpressionEngine_Exception($mode);
			}
		}
		$exprEngine = $this->_expressions[$mode];
		$exprEngine->setCompiler($this);

		$expression = $exprEngine->parse($expr, true);

		/*
		 * Now it's time to apply the escaping policy to this expression. We check
		 * the expression for the "e:" and "u:" modifiers and redirect the task to
		 * the escape() method.
		 */
		$expression['escaping'] = true;
		if($escape !== self::ESCAPE_OFF)
		{
			if($modifierSelected || $escape === self::ESCAPE_ON)
			{
				$expression['escaped'] = $this->escape($modifier, $expression['bare'], $expression['format'], !empty($this->_modifiers[$modifier]));
			}
			else
			{
				$expression['escaped'] = $this->escape($modifier, $expression['bare'], $expression['format']);
			}
		}
		else
		{
			$expression['escaped'] = $expression['bare'];
		}
		if($expression['escaped'] == $expression['bare'])
		{
			$expression['escaping'] = false;
		}
		return $expression;
	} // end parseExpression();

	/**
	 * An alias for parseExpression() left for backward compatibility with OPT 2.0.
	 *
	 * @param String $expr The expression
	 * @param Boolean $allowAssignment=false True, if the assignments are allowed.
	 * @param Int $escape=self::ESCAPE_ON The HTML escaping policy for this expression.
	 * @return Array An array consisting of four elements: the compiled expression,
	 *   the assignment status and the variable status (if the expression is in fact
	 *   a single variable). If the escaping is controlled by the template or the
	 *   script, the fourth element contains also an unescaped PHP expression.
	 */
	public function compileExpression($expr, $allowAssignment = false, $escape = self::ESCAPE_ON)
	{
		$expression = $this->parseExpression($expr, 'parse', $escape, 'e');

		return array(0 =>
			$expression['escaped'],
			$expression['type'] == Opt_Expression_Interface::ASSIGNMENT,
			$expression['type'] == Opt_Expression_Interface::SINGLE_VAR,
			$expression['bare']
		);
	} // end compileExpression();



	/**
	 * Smart special character replacement that leaves entities
	 * unmodified. Used by parseSpecialChars().
	 *
	 * @internal
	 * @param Array $text Matching string
	 * @return String Modified text
	 */
	protected function _entitize($text)
	{
		switch($text[0])
		{
			case '&':	return '&amp;';
			case '>':	return '&gt;';
			case '<':	return '&lt;';
			case '"':	return '&quot;';
			default:	return $text[0];
		}
	} // end _entitize();

	/**
	 * Smart entity replacement that makes use of
	 *
	 * @internal
	 * @param Array $text Matching string
	 * @return String Modified text
	 */
	protected function _decodeEntity($text)
	{
		switch($text[1])
		{
			case 'amp':	return '&';
			case 'quot':	return '"';
			case 'lt':	return '<';
			case 'gt':	return '>';
			case 'apos': return "'";
			default:

				if(isset($this->_entities[$text[1]]))
				{
					return $this->_entities[$text[1]];
				}
				if($text[1][0] == '#')
				{
					return html_entity_decode($text[0], ENT_COMPAT, $this->_tpl->charset);
				}
				elseif($this->_tpl->htmlEntities && $text[0] != ($result = html_entity_decode($text[0], ENT_COMPAT, $this->_tpl->charset)))
				{
					return $result;
				}
				throw new Opt_UnknownEntity_Exception(htmlspecialchars($text[0]));
		}
	} // end _entitize();
} // end Opt_Compiler_Class;
