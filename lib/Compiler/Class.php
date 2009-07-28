<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
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
		protected $_parsers = array();
		protected $_expressions = array();
		protected $_tags = array();
		protected $_attributes = array();
		protected $_conversions = array();
		protected $_processors = array();
		protected $_dependencies = array();

		// OPT parser info
		protected $_tpl;
		protected $_instructions;
		protected $_namespaces;
		protected $_functions;
		protected $_classes;
		protected $_blocks;
		protected $_components;
		protected $_tf;
		protected $_entities;
		protected $_formnatInfo;
		protected $_formats = array();
		protected $_formatObj = array();
		protected $_inheritance;

		// Regular expressions

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
				$this->_charset = strtoupper($tpl->charset);

				// Create the processors and call their configuration method in the constructors.
				$instructions = $tpl->_getList('_instructions');
				$cnt = sizeof($instructions);
				for($i = 0; $i < $cnt; $i++)
				{
					$name = 'Opt_Instruction_'.$instructions[$i];
					$obj = new $name($this, $tpl);
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
		 * @param String $expression The PHP expression to be escaped.
		 * @param Boolean $status The status of escaping for this expression or NULL, if not set.
		 * @return String The expression with the escaping formula added, if necessary.
		 */
		public function escape($expression, $status = null)
		{
			// OPT Configuration
			$escape = $this->_tpl->escape;

			// Template configuration
			if(!is_null($this->get('escaping')))
			{
				$escape = ($this->get('escaping') == true ? true : false);
			}

			// Expression settings
			if(!is_null($status))
			{
				$escape = ($status == true ? true : false);
			}

			if($escape)
			{
				// The user may define a custom escaping function
				if($this->isFunction('escape'))
				{
					if(strpos($this->_functions['escape'], '#', 0) !== false)
					{
						throw new Opt_InvalidArgumentFormat_Exception('escape', 'escape');
					}
					return $this->_functions['escape'].'('.$expression.')';
				}
				return 'htmlspecialchars('.$expression.')';
			}
			return $expression;
		} // end escape();

		/**
		 * Returns the format object for the specified variable.
		 *
		 * @param String $variable The variable identifier.
		 * @param Boolean $restore optional Whether to load a previously created format object (false) or to create a new one.
		 * @return Opt_Compiler_Format The format object.
		 */
		public function getFormat($variable, $restore = false)
		{
			$hc = self::DEFAULT_FORMAT_CLASS;
			if(isset($this->_formatInfo[$variable]))
			{
				$hc = $this->_formatInfo[$variable];
			}
			if($restore && isset($this->_formatObj[$hc]))
			{
				return $this->_formatObj[$hc];
			}

			$top = $this->createFormat($variable, $hc);
			if($restore)
			{
				$this->_formatObj[$hc] = $top;
			}
			return $top;
		} // end getFormat();

		/**
		 * Creates a format object for the specified description string.
		 *
		 * @param String $variable The variable name (for debug purposes)
		 * @param String $hc The description string.
		 * @return Opt_Compiler_Format The newly created format object.
		 */
		public function createFormat($variable, $hc)
		{
			// Decorate the objects, if necessary
			$expanded = explode('/', $hc);
			$obj = null;
			foreach($expanded as $class)
			{
				if(!in_array($class, $this->_formats))
				{
					throw new Opt_FormatNotFound_Exception($variable, $class);
				}
				$hcName = 'Opt_Format_'.$class;
				if(!is_null($obj))
				{
					$obj->decorate($obj2 = new $hcName($this->_tpl, $this));
					$obj = $obj2;
				}
				else
				{
					$top = $obj = new $hcName($this->_tpl, $this, $hc);
				}
			}
			return $top;
		} // end createFormat();

		/**
		 * Allows to export the list of variables and their data formats to
		 * the template compiler.
		 *
		 * @param Array $list An associative array of pairs "variable => format description"
		 */
		public function setFormatList(Array $list)
		{
			$this->_formatInfo = $list;
		} // end setFormatList();

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
			return preg_match($this->_rEncodingName, $id);
		} // end isIdentifier();

		/**
		 * Checks whether the specified tag name is registered as an instruction.
		 * Returns its processor in case of success or NULL.
		 *
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
		 * @param String $tag The attribute name
		 * @return Boolean
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
		 * @param String $component The component tag name
		 * @return Boolean
		 */
		public function isComponent($component)
		{
			return isset($this->_components[$component]);
		} // end isComponent();

		/**
		 * Returns true, if the argument is the name of the block tag.
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

		public function appendOutput($text)
		{
			$this->_output .= $text;
		} // end appendOutput();

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
		 * Looks for special OPT attributes in the element attribute list and
		 * processes them. Returns the list of nodes that need to be postprocessed.
		 *
		 * @internal
		 * @param Opt_Xml_Element $node The scanned element.
		 * @param Boolean $specialNs Do we recognize "parse" and "str" namespaces?
		 * @return Array
		 */
		protected function _processXml(Opt_Xml_Element $node, $specialNs = true)
		{
			if(!$node->hasAttributes())
			{
				return array();
			}
			$attributes = $node->getAttributes();
			$pp = array();

			// Look for special OPT attributes
			foreach($attributes as $attr)
			{
				if($this->isNamespace($attr->getNamespace()))
				{
					$xml = $attr->getXmlName();
					// Check the namespace we found
					switch($attr->getNamespace())
					{
						case 'parse':
							if($specialNs)
							{
								$result = $this->compileExpression((string)$attr, false, Opt_Compiler_Class::ESCAPE_BOTH);
								$attr->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, ' echo '.$result[0].'; ');
								$attr->setNamespace(null);
							}
							break;
						case 'str':
							if($specialNs)
							{
								$attr->setNamespace(null);
							}
							break;
						default:
							if(isset($this->_attributes[$xml]))
							{
								$this->_attributes[$xml]->processAttribute($node, $attr);
								if($attr->get('postprocess'))
								{
									$pp[] = array($this->_attributes[$xml], $attr);
								}
							}
							$node->removeAttribute($xml);
					}
				}
			}
			return $pp;
		} // end _processXml();

		/**
		 * Runs the postprocessors for the specified attributes.
		 *
		 * @internal
		 * @param Opt_Xml_Node $node The scanned node.
		 * @param Array $list The list of XML attribute processors that need to be postprocessed.
		 */
		protected function _postprocessXml(Opt_Xml_Node $node, Array $list)
		{
			$cnt = sizeof($list);
			for($i = 0; $i < $cnt; $i++)
			{
				$list[$i][0]->postprocessAttribute($node, $list[$i][1]);
			}
		} // end _postprocessXml();

		/**
		 * An utility method for the stage 2 and 3 of the compilation. It is
		 * used to create a non-recursive depth-first search algorithm. The
		 * current queue is sent to a stack, and the new queue if initialized,
		 * if $item contains children.
		 *
		 * @internal
		 * @param SplStack $stack The processing stack.
		 * @param SplQueue $queue The processing queue.
		 * @param Opt_Xml_Scannable $item The item, where to import the nodes from.
		 * @param Boolean $pp The postprocess flag.
		 * @return SplQueue The new queue (or the old one, if none has been created).
		 */
		protected function _pushQueue($stack, $queue, $item, $pp)
		{
			if($item->hasChildren())
			{
				$stack->push(array($item, $queue, $pp));
				$pp = NULL;
				$queue = new SplQueue;
				foreach($item as $child)
				{
					$queue->enqueue($child);
				}
			}

			return $queue;
		} // end _pushQueue();

		/**
		 * Does the postprocessing in the second stage of compilation.
		 *
		 * @internal
		 * @param Opt_Xml_Node|Null $item The postprocessed node.
		 * @param Array $pp The list of postprocessed attributes.
		 */
		protected function _doPostprocess($item, $pp)
		{
			// Postprocess code for the compilation stage 2
			// Packed into a method, because it is used twice.
			if(is_null($item))
			{
				return;
			}
			if(sizeof($pp) > 0)
			{
				$this->_postprocessXml($item, $pp);
			}
			if($item->get('postprocess'))
			{
				if(!is_null($processor = $this->isInstruction($item->getXmlName())))
				{
					$processor->postprocessNode($item);
				}
				elseif($this->isComponent($item->getXmlName()))
				{
					$processor = $this->processor('component');
					$processor->postprocessComponent($item);
				}
				elseif($this->isBlock($item->getXmlName()))
				{
					$processor = $this->processor('block');
					$processor->postprocessBlock($item);
				}
				else
				{
					throw new Opt_UnknownProcessor_Exception($item->getXmlName());
				}
			}
		} // end _doPostprocess();

		/**
		 * Does the post-linking for the third stage of the compilation and returns
		 * the linked code.
		 *
		 * @internal
		 * @param Opt_Xml_Node $item The linked item.
		 * @return String
		 */
		protected function _doPostlinking($item)
		{
			// Post code
			if(is_null($item))
			{
				return '';
			}

			// This prevents from displaying </> if the HTML node was hidden.
			if($item->get('hidden') !== false)
			{
				return '';
			}
			if($item->get('_skip_postlinking') == true)
			{
				return '';
			}

			$output = '';
			switch($item->getType())
			{
				case 'Opt_Xml_Text':
					
					break;
				case 'Opt_Xml_Element':

					break;
				case 'Opt_Xml_Root':
					
					break;
			}
			$this->_closeComments($item, $output);
			return $output;
		} // end _doPostlinking();

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

				// Run the new garbage collector, if it is available.
			/*	if(version_compare(PHP_VERSION, '5.3.0', '>='))
				{
					gc_collect_cycles();
				}*/
			}
			catch(Exception $e)
			{
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
				// Run the new garbage collector, if it is available.
			/*	if(version_compare(PHP_VERSION, '5.3.0', '>='))
				{
					gc_collect_cycles();
				}*/
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
				$item = NULL;
				if($queue->count() > 0)
				{
					$item = $queue->dequeue();
				}
				$pp = array();

				// We set the "hidden" state unless it is set.

				try
				{
					if(is_null($item))
					{
						throw new Opl_Goto_Exception;
					}

					$stateSet = false;
					if(is_null($item->get('hidden')))
					{
						$item->set('hidden', true);
						$stateSet = true;
					}

					// Proper processing
					switch($item->getType())
					{
						case 'Opt_Xml_Cdata':
							$stateSet and $item->set('hidden', false);
							break;
						case 'Opt_Xml_Text':
							$stateSet and $item->set('hidden', false);
							if($item->hasChildren())
							{
								$stack->push(array($item, $queue, $pp));
								$pp = NULL;
								$queue = new SplQueue;
								foreach($item as $child)
								{
									$queue->enqueue($child);
								}
								continue 2;
							}
							break;
						case 'Opt_Xml_Element':
							if($this->isNamespace($item->getNamespace()))
							{
								$name = $item->getXmlName();
								$pp = $this->_processXml($item, false);

								// Look for the processor
								if(!is_null($processor = $this->isInstruction($name)))
								{
									$processor->processNode($item);
								}
								elseif($this->isComponent($name))
								{
									$processor = $this->processor('component');
									$processor->processComponent($item);
								}
								elseif($this->isBlock($name))
								{
									$processor = $this->processor('block');
									$processor->processBlock($item);
								}

								if(is_object($processor))
								{
									$stateSet and $item->set('hidden', false);
									$result = $processor->getQueue();
									if(!is_null($result))
									{
										$stack->push(array($item, $queue, $pp));
										$queue = $result;
										continue 2;
									}
								}
								elseif($item->get('processAll'))
								{
									$stateSet and $item->set('hidden', false);
									$stack->push(array($item, $queue, $pp));
									$pp = NULL;
									$queue = new SplQueue;
									foreach($item as $child)
									{
										$queue->enqueue($child);
									}
									continue 2;
								}
								unset($processor);
							}
							else
							{
								$pp = $this->_processXml($item, true);
								$stateSet and $item->set('hidden', false);
								if($item->hasChildren())
								{
									$stack->push(array($item, $queue, $pp));
									$pp = NULL;
									$queue = new SplQueue;
									foreach($item as $child)
									{
										$queue->enqueue($child);
									}
									continue 2;
								}
							}
							break;
						case 'Opt_Xml_Expression':
							$stateSet and $item->set('hidden', false);
							// Empty expressions will be caught by the try... catch.
							try
							{
								$result = $this->compileExpression((string)$item, true);
								if(!$result[1])
								{
									$item->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'echo '.$result[0].'; ');
								}
								else
								{
									$item->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $result[0].';');
								}
							}
							catch(Opt_EmptyExpression_Exception $e){}
							break;
						case 'Opt_Xml_Root':
							$stateSet and $item->set('hidden', false);
							$queue = $this->_pushQueue($stack, $queue, $item, array());
							break;
						case 'Opt_Xml_Comment':
							if($this->_tpl->printComments)
							{
								$stateSet and $item->set('hidden', false);
							}
							break;
					}

				}
				catch(Opl_Goto_Exception $e){}
				$this->_doPostprocess($item, $pp);
				if($queue->count() == 0)
				{
					unset($queue);
					if($stack->count() == 0)
					{
						break;
					}
					list($item, $queue, $pp) = $stack->pop();
					$this->_doPostprocess($item, $pp);
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
				//	echo str_repeat('  ', $stack->count())."Start: ".$item."\n";
					if($this->_newQueue !== null)
					{
					//	echo str_repeat('  ', $stack->count())."Sub: ".$item."\n";
						// Starting next level.
						$stack->push(array($item, $queue));
						$queue = $this->_newQueue;
						$this->_newQueue = null;
					}
					else
					{
					//	echo str_repeat('  ', $stack->count())."Nope: ".$item."\n";
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
				//	echo str_repeat('  ', $stack->count())."Pop: ".$item."\n";
					$item->postLink($this);
					$this->_closeComments($item);
				}
			}

			if($this->_tpl->stripWhitespaces)
			{
				$output = rtrim($output);
			}
		} // end _stage3();

		/*
		 * Expression compiler
		 */

		/**
		 * Compiles the template expression to the PHP code and checks the syntax
		 * errors. The method is recursion-free.
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
			// The expression modifier must not be tokenized, so we
			// capture it before doing anything with the expression.
			$modifier = '';
			if(preg_match('/^([^\'])\:[^\:]/', $expr, $found))
			{
				$modifier = $found[1];

				if($modifier != 'e' && $modifier != 'u')
				{
					throw new Opt_InvalidExpressionModifier_Exception($modifier, $expr);
				}

				$expr = substr($expr, 2, strlen($expr) - 2);
			}

			// First, we select a parser.
			// TODO: Add parser recognition
			$mode = 'Opt_Expression_Standard';
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

			$expression = $exprEngine->parse($expr, $allowAssignment);


			/*
			 * Now it's time to apply the escaping policy to this expression. We check
			 * the expression for the "e:" and "u:" modifiers and redirect the task to
			 * the escape() method.
			 */
			$expression[3] = $expression[0];
			if($escape != self::ESCAPE_OFF && !$expression[1])
			{
				if($modifier != '')
				{
					$expression[0] = $this->escape($expression[0], $modifier == 'e');
				}
				else
				{
					$expression[0] = $this->escape($expression[0]);
				}
			}
			// Pack everything
			if($escape != self::ESCAPE_BOTH)
			{
				$expression[3] = null;
			}
			return $expression;
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
