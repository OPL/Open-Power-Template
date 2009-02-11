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
 * $Id: Class.php 23 2008-12-03 14:11:58Z extremo $
 */

	class Opt_Compiler_Class
	{
		// Opcodes
		const OP_VARIABLE = 1;
		const OP_LANGUAGE_VAR = 2;
		const OP_STRING = 4;
		const OP_NUMBER = 8;
		const OP_ARRAY = 16;
		const OP_OBJECT = 32;
		const OP_IDENTIFIER = 64;
		const OP_OPERATOR = 128;
		const OP_POST_OPERATOR = 256;
		const OP_PRE_OPERATOR = 512;
		const OP_ASSIGN = 1024;
		const OP_NULL = 2048;
		const OP_SQ_BRACKET = 4096;
		const OP_SQ_BRACKET_E = 8192;
		const OP_FUNCTION = 16384;
		const OP_METHOD = 32768;
		const OP_BRACKET = 65536;
		const OP_CLASS = 131072;
		const OP_CALL = 262144;
		const OP_FIELD = 524288;
		const OP_EXPRESSION = 1048576;
		const OP_OBJMAN = 2097152;
		const OP_BRACKET_E = 4194304;
		const OP_TU = 8388608;
		const OP_CURLY_BRACKET = 16777216;
		
		const DEFAULT_FORMAT_CLASS = 'Generic';
		
		const ESCAPE_ON = true;
		const ESCAPE_OFF = false;
		const ESCAPE_BOTH = 2;
	
	
		// Current compilation
		protected $_template = NULL;
		protected $_attr = array();
		protected $_stack = NULL;
		protected $_node = NULL;
		
		static protected $_recursionDetector = NULL;
		
		// Compiler info
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
		protected $_formnatInfo;
		protected $_formats = array();
		protected $_formatObj = array();
		protected $_inheritance;
		
		// Regular expressions
		private $_rCDataExpression = '/(\<\!\[CDATA\[|\]\]\>)/msi';
		private $_rCommentExpression = '/(\<\!\-\-|\-\-\>)/si';
		private $_rCommentSplitExpression = '/(\<\!\-\-(.*?)\-\-\>)/si';
		private $_rOpeningChar = '[a-zA-Z\:\_]';
		private $_rNameChar = '[a-zA-Z0-9\:\.\_\-]';
		private $_rNameExpression;
		private $_rXmlTagExpression;
		private $_rTagExpandExpression;
		private $_rQuirksTagExpression = '';
		private $_rExpressionTag = '/(\{([^\}]+)\})/msi';
		private $_rAttributeTokens = '/(?:[^\=\"\s]+|\=|\"|\s)/x';
		private $_rPrologTokens = '/(?:[^\=\"\'\s]+|\=|\'|\"|\s)/x';
		private $_rModifiers = 'si';
		private $_rXmlHeader = '/(\<\?xml.+\?\>)/msi';
		private $_rProlog = '/\<\?xml(.+)\?\>|/msi';
		private $_rEncodingName = '/[A-Za-z]([A-Za-z0-9.\_]|\-)*/si';
		
		private $_rBacktickString = '`[^`\\\\]*(?:\\\\.[^`\\\\]*)*`';
		private $_rSingleQuoteString = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
		private $_rHexadecimalNumber = '\-?0[xX][0-9a-fA-F]+';
		private $_rDecimalNumber = '[0-9]+\.?[0-9]*';
		private $_rLanguageVar = '\$[a-zA-Z0-9\_]+@[a-zA-Z0-9\_]+';
		private $_rVariable = '(\$|@)[a-zA-Z0-9\_\.]*';
		private $_rOperators = '\-\>|!==|===|==|!=|\=\>|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\||\%|\+\+|\-\-|\+|\-|\*|\/|\[|\]|\.|\:\:|\{|\}|';
		private $_rIdentifier = '[a-zA-Z\_]{1}[a-zA-Z0-9\_\.]+';
		private $_rLanguageVarExtract = '\$([a-zA-Z0-9\_]+)@([a-zA-Z0-9\_]+)';

		// Help fields
		private $_translationConversion = null;
		private $_initialMemory = null;
		
		static private $_templates = array();

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
				$tpl = $this->_tpl;
			}
			
			if($tpl->unicodeNames)
			{
				// Register unicode name regular expressions
				$this->_rOpeningChar = '(\p{Lu}|\p{Ll}|\p{Ll}|\p{Lt}|\p{Lm}|\p{Nl}|\_|\:)';
				$this->_rNameChar = '(\p{Lu}|\p{Ll}|\p{Ll}|\p{Lt}|\p{Lm}|\p{Nl}|\p{Mc}|\p{Me}|\p{Mn}|\p{Lm}|\p{Nd}|\_|\:|\.|\-)';
				$this->_rModifiers = 'msiu';
			}
			
			// Register the rest of the expressions
			$this->_rNameExpression = '/('.$this->_rOpeningChar.'?'.$this->_rNameChar.'*)/'.$this->_rModifiers;
			$this->_rXmlTagExpression = '/(\<((\/)?('.$this->_rOpeningChar.'?'.$this->_rNameChar.'*)( [^\<\>]+)?(\/)?)\>)/'.$this->_rModifiers;
			$this->_rTagExpandExpression = '/^(\/)?('.$this->_rOpeningChar.'?'.$this->_rNameChar.'*)( [^\<\>]+)?(\/)?$/'.$this->_rModifiers;
			
			
			$this->_rQuirksTagExpression = '/(\<((\/)?(('.implode('|', $this->_namespaces).')\:'.$this->_rNameChar.'*)( [^\<\>]+)?(\/)?)\>)/'.$this->_rModifiers;
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
			}
		} // end __construct();
		
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
		
		static public function getCurrentTemplate()
		{
			return end(self::$_templates);
		} // end getCurrentTemplate();
		
		static public function cleanCompiler()
		{
			self::$_recursionDetector = null;
			self::$_templates = array();
		} // end cleanCompiler();
		
		public function getParser()
		{
			return $this->_tpl;
		} // end getParser();
		
		public function get($name)
		{
			if(!isset($this->_attr[$name]))
			{
				return NULL;
			}
			return $this->_attr[$name];
		} // end get();
		
		public function set($name, $value)
		{
			$this->_attr[$name] = $value;
		} // end set();
	
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
					$top = $obj = new $hcName($this->_tpl, $this); 
				}
			}
			return $top;
		} // end createFormat();
		
		public function setFormatList(Array $list)
		{
			$this->_formatInfo = $list;
		} // end setFormatList();
		
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
		
		public function setConversion($from, $to)
		{
			$this->_conversions[$from] = $to;
		} // end setConversion();
		
		public function unsetConversion($from)
		{
			if(isset($this->_conversions[$from]))
			{
				unset($this->_conversions[$from]);
				return true;
			}
			return false;
		} // end unsetConversion();
		
		public function setInheritance(Array $inheritance)
		{
			$this->_inheritance = $inheritance;
		} // end setInheritance();
		
		public function parseEntities($text)
		{
			return htmlspecialchars_decode(str_replace(array('&lb;', '&rb;'), array('{', '}'), $text));
		} // end parseEntities();
		
		public function parseShortEntities($text)
		{
			return str_replace(array('&lb;', '&rb;'), array('{', '}'), $text);
		} // end parseShortEntities();
		
		public function isIdentifier($id)
		{
			return preg_match($this->_rEncodingName, $id);
		} // end isIdentifier();
		
		public function isInstruction($tag)
		{
			if(isset($this->_instructions[$tag]))
			{
				return $this->_instructions[$tag];
			}
			return NULL;
		} // end isInstruction();

		public function isOptAttribute($tag)
		{
			if(isset($this->_attributes[$tag]))
			{
				return $this->_attributes[$tag];
			}
			return NULL;
		} // end isOptAttribute();
		
		public function isFunction($name)
		{
			if(isset($this->_functions[$name]))
			{
				return $this->_functions[$name];
			}
			return NULL;
		} // end isFunction();
		
		public function isClass($id)
		{
			if(isset($this->_classes[$id]))
			{
				return $this->_classes[$id];
			}
			return NULL;
		} // end isClass();
		
		public function isNamespace($ns)
		{
			return in_array($ns, $this->_namespaces);
		} // end isNamespace();
		
		public function isComponent($component)
		{
			return isset($this->_components[$component]);
		} // end isComponent();
		
		public function isBlock($block)
		{
			return isset($this->_blocks[$block]);
		} // end isComponent();
		
		public function isProcessor($name)
		{
			if(!isset($this->_processors[$name]))
			{
				return NULL;
			}
			return $this->_processors[$name];
		} // end isProcessor();
		
		public function processor($name)
		{
			if(!isset($this->_processors[$name]))
			{
				throw new Opt_ObjectNotExists_Exception('processor', $name);
			}
			return $this->_processors[$name];
		} // end processor();
		
		public function component($name)
		{
			if(!isset($this->_components[$name]))
			{
				throw new Opt_ObjectNotExists_Exception('component', $name);
			}
			return $this->_components[$name];
		} // end component();
		
		public function block($name)
		{
			if(!isset($this->_blocks[$name]))
			{
				throw new Opt_ObjectNotExists_Exception('block', $name);
			}
			return $this->_blocks[$name];
		} // end block();
		
		public function inherits($name)
		{
			if(isset($this->_inheritance[$name]))
			{
				return $this->_inheritance[$name];
			}
			return NULL;
		} // end inherits();
		
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
		
		public function importDependencies(Opt_Compiler_Class $compiler)
		{
			$this->_dependencies = array_merge($this->_dependencies, $compiler->_dependencies);
		} // end importDependencies();

		/*
		 * Internal tools and utilities
		 */
		
		protected function _compileAttributes($attrList)
		{
			// Tokenize the list
			preg_match_all($this->_rAttributeTokens, $attrList, $match, PREG_SET_ORDER);
			
			$size = sizeof($match);
			$result = array();
			for($i = 0; $i < $size; $i++)
			{
				if(!ctype_space($match[$i][0]))
				{
					
					if(!preg_match($this->_rNameExpression, $match[$i][0]))
					{
						return false;
					}
					
					$vret = false;
					$name = $match[$i][0];
					$value = null;
					for($i++; ctype_space($match[$i][0]) && $i < $size; $i++){}
					
					if($match[$i][0] != '=')
					{
						if($this->_tpl->htmlAttributes)
						{
							$result[$name] = $name;
							continue;
						}
						else
						{
							return false;
						}
					}
					for($i++; ctype_space($match[$i][0]) && $i < $size; $i++){}
				
					if($match[$i][0] != '"')
					{
						return false;
					}
					$value = '';
					for($i++; $i < $size; $i++)
					{
						if($match[$i][0] == '"')
						{
							break;
						}
						$value .= $match[$i][0];
					}
					if(!isset($match[$i][0]))
					{
						return false;
					}
					if($match[$i][0] != '"')
					{
						return false;
					}
					$result[$name] = $value;
				}
			}
			return $result;
		} // end _compileAttributes();
		
		protected function _compileProlog($prolog)
		{
			// Tokenize the list
			preg_match_all($this->_rPrologTokens, $prolog, $match, PREG_SET_ORDER);
				
			$size = sizeof($match);
			$result = array();
			for($i = 0; $i < $size; $i++)
			{
				if(!ctype_space($match[$i][0]))
				{
					// Traverse through a single attribute
					if(!preg_match($this->_rNameExpression, $match[$i][0]))
					{
						throw new Opt_XmlInvalidProlog_Exception('invalid attribute format');
					}
						
					$vret = false;
					$name = $match[$i][0];
					$value = null;
					for($i++; $i < $size && ctype_space($match[$i][0]); $i++){}
						
					if($i >= $size || $match[$i][0] != '=')
					{
						throw new Opt_XmlInvalidProlog_Exception('invalid attribute format');
					}
					for($i++; ctype_space($match[$i][0]) && $i < $size; $i++){}
					
					if($match[$i][0] != '"' && $match[$i][0] != '\'')
					{
						throw new Opt_XmlInvalidProlog_Exception('invalid attribute format');
					}
					$opening = $match[$i][0];
					$value = '';
					for($i++; $i < $size; $i++)
					{
						if($match[$i][0] == $opening)
						{
							break;
						}
						$value .= $match[$i][0];
					}
					if(!isset($match[$i][0]) || $match[$i][0] != $opening)
					{
						throw new Opt_XmlInvalidProlog_Exception('invalid attribute format');
					}
					// If we are here, the attribute is correct. No shit on the way detected.
					$result[$name] = $value;
				}
			}
			$returnedResult = $result;
			// Check, whether the arguments are correct.
			if(isset($result['version']))
			{
				// There is no other version so far, so report a warning. For 99,9% this is a mistake.
				if($result['version'] != '1.0')
				{
					$this->_tpl->debugConsole and Opt_Support::warning('OPT', 'XML prolog warning: strange XML version: '.$result['version']);
				}
				unset($result['version']);
			}
			if(isset($result['encoding']))
			{
				if(!preg_match($this->_rEncodingName, $result['encoding']))
				{
					throw new Opt_XmlInvalidProlog_Exception('invalid encoding name format');
				}
				// The encoding should match the value we mentioned in the OPT configuration and sent to the browser.
				$result['encoding'] = strtolower($result['encoding']);
				$charset = is_null($this->_tpl->charset) ? null : strtolower($this->_tpl->charset);
				if($result['encoding'] != $charset && !is_null($charset))
				{
					$this->_tpl->debugConsole and Opt_Support::warning('OPT', 'XML prolog warning: the declared encoding: "'.$result['encoding'].'" differs from setContentType() setting: "'.$charset.'"');
				}
				unset($result['encoding']);
			}
			else
			{
				$this->_tpl->debugConsole and Opt_Support::warning('XML prolog warning: no encoding information. Remember your content must be pure UTF-8 or UTF-16 then.');
			}
			if(isset($result['standalone']))
			{
				if($result['standalone'] != 'yes' && $result['standalone'] != 'no')
				{
					throw new Opt_XmlInvalidProlog_Exception('invalid value for "standalone" attribute: "'.$result['standalone'].'"; expected: "yes", "no".');
				}
				unset($result['standalone']);
			}
			if(sizeof($result) > 0)
			{
				throw new Opt_XmlInvalidProlog_Exception('invalid attributes in prolog.');
			}
			return $returnedResult;
		} // end _compileProlog();
		
		protected function _addDependencies($tree)
		{
			// OK, there is really some info to include!
			$list = '';
			foreach($this->_dependencies as $a)
			{
				$list .= '\''.$a.'\',';
			}
			
			$tree->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'if(!$this->_massPreprocess($this->_template, $compileTime, array('.$list.'))){ ');
			$tree->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' }else{ $compileTime = $this->_compile($this->_template, $mode); require(__FILE__); } ');				
		} // end _addDependencies();
		
		protected function _treeTextCompile($current, $text, $noExpressions = false)
		{
			if($this->_tpl->stripWhitespaces)
			{
				if(ctype_space($text))
				{
					return $current;
				}
			}
			if($noExpressions)
			{
				$current = $this->_treeTextAppend($current, $this->parseShortEntities($text));
			}
			
			preg_match_all($this->_rExpressionTag, $text, $result, PREG_SET_ORDER);
			
			$resultSize = sizeof($result);
			$offset = 0;
			for($i = 0; $i < $resultSize; $i++)
			{
				$id = strpos($text, $result[$i][0], $offset);
				if($id > $offset)
				{
					$current = $this->_treeTextAppend($current, $this->parseShortEntities(substr($text, $offset, $id - $offset)));						
				}
				$offset = $id + strlen($result[$i][0]);
				
				$current = $this->_treeTextAppend($current, new Opt_Xml_Expression($this->parseEntities($result[$i][2])));
			}
			
			$i--;
			// Now the remaining content of the file
			if(strlen($text) > $offset)
			{
				$current = $this->_treeTextAppend($current, $this->parseShortEntities(substr($text, $offset, strlen($text) - $offset)));
			}
			return $current;
		} // end _treeTextCompile();
		
		protected function _treeTextAppend($current, $text, $trim = true)
		{
			$last = $current->getLastChild();
			if(!is_object($last) || !($last instanceof Opt_Xml_Text))
			{
				if(!is_object($text))
				{
					if($this->_tpl->stripWhitespaces && $trim)
					{
						$text = preg_replace('/\s\s+/', ' ', $text);
					}
					$node = new Opt_Xml_Text($text);
				}
				else
				{
					$node = new Opt_Xml_Text();
					$node->appendChild($text);
				}
				$current->appendChild($node);
			}
			else
			{
				if(!is_object($text))
				{
					if($this->_tpl->stripWhitespaces && $trim)
					{
						$text = preg_replace('/\s\s+/', ' ', $text);
					}
					$last->appendData($text);
				}
				else
				{
					$last->appendChild($text);
				}
			}
			return $current;
		} // end _treeTextAppend();
		
		protected function _treeNodeAppend($current, $node, $goInto)
		{
			$current->appendChild($node);
			if($goInto)
			{
				return $node;
			}
			return $current;
		} // end _treeNodeAppend();
		
		protected function _treeJumpOut($current)
		{
			$parent = $current->getParent();
			
			if(!is_null($parent))
			{
				return $parent;
			}
			return $current;
		} // end _treeJumpOut();
		
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
								$result = $this->compileExpression((string)$attr, false);						
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
		
		protected function _postprocessXml(Opt_Xml_Node $node, Array $list)
		{
			$cnt = sizeof($list);
			for($i = 0; $i < $cnt; $i++)
			{
				$list[$i][0]->postprocessAttribute($node, $list[$i][1]);
			}
		} // end _postprocessXml();
		
		protected function _pushQueue($stack, $queue, $item, $pp)
		{
			/*
			 * A support method for non-recursive depth-first search of a tree.
			 * It sends a queue into a stack and initializes a new queue, if
			 * the $item contains subitems.
			 */
		
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

		protected function _doPostlinking($item)
		{
			// Post code
			if(is_null($item))
			{
				return '';
			}
			$output = '';
			switch($item->getType())
			{
				case 'Opt_Xml_Text':
					$output .= $item->buildCode(Opt_Xml_Buffer::TAG_AFTER);
					break;
				case 'Opt_Xml_Element':							
					if($this->isNamespace($item->getNamespace()))
					{
						$output .= $item->buildCode(Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CLOSING_BEFORE,
							Opt_Xml_Buffer::TAG_CLOSING_AFTER, Opt_Xml_Buffer::TAG_AFTER);
					}
					else
					{
						$output .= $item->buildCode(Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CLOSING_BEFORE).'</'.$item->get('_name').'>'.$item->buildCode(Opt_Xml_Buffer::TAG_CLOSING_AFTER, Opt_Xml_Buffer::TAG_AFTER);
						$item->set('_name', NULL);
					}
					break;
				case 'Opt_Xml_Root':
					$output .= $item->buildCode(Opt_Xml_Buffer::TAG_AFTER);
					break;
			}
			return $output;
		} // end _doPostlinking();
		
		protected function _linkAttributes($subitem)
		{
			// Links the attributes into the PHP code
			if($subitem->hasAttributes() || $subitem->bufferSize(Opt_Xml_Buffer::TAG_BEGINNING_ATTRIBUTES) > 0 || $subitem->bufferSize(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES) > 0)
			{
			
				$code = $subitem->buildCode(Opt_Xml_Buffer::TAG_ATTRIBUTES_BEFORE, Opt_Xml_Buffer::TAG_BEGINNING_ATTRIBUTES);
				$attrList = $subitem->getAttributes();
				// Link attributes into a string
				foreach($attrList as $attribute)
				{
					$s = $attribute->bufferSize(Opt_Xml_Buffer::ATTRIBUTE_NAME);
					switch($s)
					{
						case 0:
							$code .= $attribute->buildCode(Opt_Xml_Buffer::ATTRIBUTE_BEGIN).' '.$attribute->getXmlName();
							break;
						case 1:
							$code .= ($attribute->bufferSize(Opt_Xml_Buffer::ATTRIBUTE_BEGIN) == 0 ? ' ' : '').$attribute->buildCode(Opt_Xml_Buffer::ATTRIBUTE_BEGIN, ' ', Opt_Xml_Buffer::ATTRIBUTE_NAME);
							break;
						default:
							throw new Opt_CompilerCodeBufferConflict_Exception(1, 'ATTRIBUTE_NAME', $subitem->getXmlName());
					}

					if($attribute->bufferSize(Opt_Xml_Buffer::ATTRIBUTE_VALUE) == 0)
					{
						// Static value
						if(!($this->_tpl->htmlAttributes && $attribute->getValue() == $attribute->getName()))
						{
							$code .= '="'.htmlspecialchars($attribute->getValue()).'"';
						}
					}
					else
					{
						$code .= '="'.$attribute->buildCode(Opt_Xml_Buffer::ATTRIBUTE_VALUE).'"';
					}
					$code .= $attribute->buildCode(Opt_Xml_Buffer::ATTRIBUTE_END);
				}
				return $code.$subitem->buildCode(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES, Opt_Xml_Buffer::TAG_ATTRIBUTES_AFTER);				
			}
			return '';
		} // end _linkAttributes();
		
		// TODO: Remove in the final version.
		public function _debugPrintNodes($node)
		{
			echo '<ul>';
			
			foreach($node as $id => $subnode)
			{
				if(!is_object($subnode))
				{
					echo '<li><font color="red"><strong>Non-object value detected in the node list! Type: '.gettype($subnode).'</strong></font></li>';
					continue;
				}
				
				$hidden = $subnode->get('hidden') ? ' (HIDDEN)' : '';
				switch($subnode->getType())
				{
					case 'Opt_Xml_Cdata':
						echo '<li>'.$id.': <strong>Character data:</strong> '.htmlspecialchars($subnode).$hidden.'</li>';
						break;
					case 'Opt_Xml_Text':
						echo '<li>'.$id.': <strong>Text:</strong> ';
						$this ->_debugPrintNodes($subnode);
						echo $hidden.'</li>';
						break;
					case 'Opt_Xml_Expression':
						echo '<li>'.$id.': <strong>Expression:</strong> '.$subnode.$hidden.'</li>';
						break;
					case 'Opt_Xml_Element':
						echo '<li>'.$id.': <strong>Element node:</strong> '.$subnode->getXmlName().' (';
						$args = $subnode->getAttributes();
						foreach($args as $name => $value)
						{
							echo $name.'="'.$value.'" ';
						}
						echo ')';
						if($subnode->get('single') === true)
						{
							echo ' single';	
						}
						$this ->_debugPrintNodes($subnode);
						echo $hidden.'</li>';
						break;
				}				
			}
			echo '</ul>';
		} // end _debugPrintNodes();
		
		/*
		 * Main compilation methods
		 */
		
		public function compile($code, $filename, $compiledFilename, $mode)
		{
			try
			{
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
						$tree = $this->_stage1($code, $extend, $mode);
						// Stage 2 - PHP tree processing
						$this->_stack = array();
						$this->_stage2($tree, true);
						$this->set('escape', NULL);
						unset($this->_stack);
						$memory += (memory_get_usage() - $initial);
						unset($code);
					}
					else
					{
						$tree = $this->_stage1($code, $extend, $mode);
						unset($code);
						// Stage 2 - PHP tree processing
						$this->_stack = array();
						$this->_stage2($tree, true);
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
					$output = '';
					$this->_dynamicBlocks = array();

					$this->_stage3($output, $tree);
					$tree->dispose();
					unset($tree);

					$output = str_replace('?><'.'?php', '', $output);

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
					file_put_contents($this->_tpl->compileDir.$compiledFilename, $output);
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
			}
			catch(Exception $e)
			{
				// Clean the compiler state in case of exception
				$this->_dependencies = array();
				self::$_recursionDetector = NULL;
				foreach($this->_processors as $processor)
				{
					$processor->reset();
				}
				// And throw it forward.
				throw $e;
			}
		} // end compile();
		
		protected function _stage1(&$code, $filename, $mode)
		{
			$current = $tree = new Opt_Xml_Root;
			
			// First we have to find the prolog and DTD. Then we will be able to parse tags.

			$codeSize = strlen($code);
			
			if($mode != Opt_Class::QUIRKS_MODE)
			{
				// Find and parse XML prolog
				$endProlog = 0;
				$endDoctype = 0;
				if(substr($code, 0, 5) == '<?xml')
				{
					$endProlog = strpos($code, '?>', 5);

					if($endProlog === false)
					{
						throw new Opt_XmlInvalidProlog_Exception('prolog ending is missing');
					}
					$values = $this->_compileProlog(substr($code, 5, $endProlog - 5));
					$endProlog += 2;
					if(!$this->_tpl->prologRequired)
					{
						// The prolog must be displayed
						$tree->setProlog(new Opt_Xml_Prolog($values));
					}
				}
				// Skip white spaces
				for($i = $endProlog; $i < $codeSize; $i++)
				{
					if($code[$i] != ' ' && $code[$i] != '	' && $code[$i] != "\r" && $code[$i] != "\n")
					{
						break;
					}
				}
				// Try to find doctype at the new position.
				$possibleDoctype = substr($code, $i, 9);
				
				if($possibleDoctype == '<!doctype' || $possibleDoctype == '<!DOCTYPE')
				{
					// OK, we've found it, now determine the doctype end.
					$bracketCounter = 0;
					$doctypeStart = $i;
					for($i += 9; $i < $codeSize; $i++)
					{
						if($code[$i] == '<')
						{
							$bracketCounter++;
						}
						else if($code[$i] == '>')
						{
							if($bracketCounter == 0)
							{
								$endDoctype = $i;
								break;
							}
							$bracketCounter--;
						}
					}
					if($endDoctype == 0)
					{
						throw new Opt_XmlInvalidDoctype_Exception('doctype ending is missing');
					}

					if(!$this->_tpl->prologRequired)
					{
						$tree->setDtd(new Opt_Xml_Dtd(substr($code, $doctypeStart, $i - $doctypeStart + 1)));
					}
					$endDoctype++;
				}
				else
				{
					$endDoctype = $endProlog;
				}
				// OK, now skip that part.
				$code = substr($code, $endDoctype, $codeSize);
				// In the quirks mode, some results from the regular expression parser are
				// moved by one position, so we must add some dynamics here.
				$attributeCell = 5;
				$endingSlashCell = 6;
				$tagExpression = $this->_rXmlTagExpression;
			}
			else
			{
				$tagExpression = $this->_rQuirksTagExpression;
				$attributeCell = 6;
				$endingSlashCell = 7;
			}

			// Split through the general groups (cdata-content)
			$groups = preg_split($this->_rCDataExpression, $code, 0, PREG_SPLIT_DELIM_CAPTURE);
			$groupCnt = sizeof($groups);
			$groupState = 0;
			Opt_Xml_Cdata::$mode = $mode;
			for($k = 0; $k < $groupCnt; $k++)
			{
				// Process CDATA
				if($groupState == 0 && $groups[$k] == '<![CDATA[')
				{
					$cdata = new Opt_Xml_Cdata('');
					$cdata->set('cdata', true);
					$groupState = 1;
					continue;
				}
				if($groupState == 1)
				{
					if($groups[$k] == ']]>')
					{
						$current = $this->_treeTextAppend($current, $cdata, false);
						$groupState = 0;
					}
					else
					{
						$cdata->appendData($groups[$k]);
					}
					continue;
				}
				$subgroups = preg_split($this->_rCommentExpression, $groups[$k], 0, PREG_SPLIT_DELIM_CAPTURE);
				$subgroupCnt = sizeof($subgroups);
				$subgroupState = 0;
				for($i = 0; $i < $subgroupCnt; $i++)
				{
					// Process comments
					if($subgroupState == 0 && $subgroups[$i] == '<!--')
					{
						if($this->_tpl->printComments)
						{
							$current = $this->_treeTextAppend($current, $subgroups[$i], false);
						}
						$subgroupState = 1;
						continue;
					}
					if($subgroupState == 1)
					{
						if($subgroups[$i] == '-->')
						{
							$subgroupState = 0;
						}
						if($this->_tpl->printComments)
						{
							$current = $this->_treeTextAppend($current, $subgroups[$i], false);
						}
						continue;
					}

					// Find XML tags
					preg_match_all($tagExpression, $subgroups[$i], $result, PREG_SET_ORDER);
					/*
					 * Output field description for $result array:
					 *  0 - original content
					 *  1 - tag content (without delimiters)
					 *  2 - /, if enclosing tag
					 *  3 - name
					 *  4 - arguments (5 in quirks mode)
					 *  5 - /, if enclosing tag without subcontent (6 in quirks mode)
					 */
					
					$resultSize = sizeof($result);
					$offset = 0;
					for($j = 0; $j < $resultSize; $j++)
					{
						// Copy the remaining text to the text node
						$id = strpos($subgroups[$i], $result[$j][0], $offset);
						if($id > $offset)
						{
							$current = $this->_treeTextCompile($current, substr($subgroups[$i], $offset, $id - $offset));					
						}
						$offset = $id + strlen($result[$j][0]);
						if(!isset($result[$j][$endingSlashCell]))
						{
							$result[$j][$endingSlashCell] = '';
						}					
						// Process the argument list
						$attributes = array();
						if(!empty($result[$j][$attributeCell]))
						{
							// Just for sure...
							$result[$j][$attributeCell] = trim($result[$j][$attributeCell]);
							$oldLength = strlen($result[$j][$attributeCell]);
							$result[$j][$attributeCell] = rtrim($result[$j][$attributeCell], '/');
							if(strlen($result[$j][$attributeCell]) != $oldLength)
							{
								$result[$j][$endingSlashCell] = '/';
							}
							$attributes = $this->_compileAttributes($result[$j][$attributeCell]);
							if(!is_array($attributes))
							{
								throw new Opt_XmlInvalidAttribute_Exception($result[$j][0]);
							}
						}
						// Recognize the tag type
						if($result[$j][3] != '/')
						{
							// Opening tag
							$node = new Opt_Xml_Element($result[$j][4]);
							$node->set('single', $result[$j][$endingSlashCell] == '/');
							foreach($attributes as $name => $value)
							{
								$node->addAttribute(new Opt_Xml_Attribute($name, $value));
							}
							$current = $this->_treeNodeAppend($current, $node, $result[$j][$endingSlashCell] != '/');
						}
						elseif($result[$j][3] == '/')
						{
							if(sizeof($attributes) > 0)
							{
								throw new Opt_XmlInvalidTagStructure_Exception($result[$j][0]);
							}
							if($current instanceof Opt_Xml_Element)
							{
								if($current->getXmlName() != $result[$j][4])
								{
									throw new Opt_XmlInvalidOrder_Exception($result[$j][4], $current->getXmlName());
								}
							}
							else
							{
								throw new Opt_XmlInvalidOrder_Exception($result[$j][4], 'NULL');	
							}
							$current = $this->_treeJumpOut($current);
						}
						else
						{
							throw new Opt_XmlInvalidTagStructure_Exception($result[$j][0]);
						}
					}
				}
				$i--;
				if(strlen($subgroups[$i]) > $offset)
				{
					$current = $this->_treeTextCompile($current, substr($subgroups[$i], $offset, strlen($subgroups[$i]) - $offset));
				}
			}
			if($mode == Opt_Class::XML_MODE && $this->_tpl->singleRootNode)
			{
				// TODO: The current code does not check the contents of Opt_Text_Nodes and other root elements
				// that may contain invalid and valid XML syntax at the same time.
				// For now, this code is frozen, we'll think a bit about it in the future. Maybe nobody
				// will notice this :)
				$elementFound = false;
				foreach($tree as $item)
				{
					if($item instanceof Opt_Xml_Element)
					{
						if($elementFound)
						{
							// Oops, there is already another root node!
							throw new Opt_XmlRootElement_Exception($item->getXmlName());
						}
						$elementFound = true;
					}
				}
			}
			return $tree;
		} // end _stage1();

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
							$result = $this->compileExpression((string)$item, true);
							// TODO: prevent against generating a code like "echo ;"
							// It happens, when the expression is empty or not properly processed.
							// If there was an assignment, we do not display the result.
							if(!$result[1])
							{
								$item->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'echo '.$result[0].'; ');
							}
							else
							{
								$item->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $result[0].';');
							}
							break;
						case 'Opt_Xml_Root':
							$stateSet and $item->set('hidden', false);
							$queue = $this->_pushQueue($stack, $queue, $item, array());
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
		
		protected function _stage3(&$output, Opt_Xml_Node $node)
		{			
			$queue = new SplQueue;
			$stack = new SplStack;
			$queue->enqueue($node);
			
			// Reset the output
			$output = '';
			
			while(true)
			{
				$item = NULL;
				if($queue->count() > 0)
				{
					$item = $queue->dequeue();
				}
				
				try
				{
					if(is_null($item) || $item->get('hidden'))
					{
						throw new Opl_Goto_Exception;	// Goto postprocess;
					}
					// Pre code
					switch($item->getType())
					{
						case 'Opt_Xml_Cdata':
							if($item->get('cdata'))
							{
								$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE).'<![CDATA['.$item.']]>'.$item->buildCode(Opt_Xml_Buffer::TAG_AFTER);
								break;
							}
							$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE).$item.$item->buildCode(Opt_Xml_Buffer::TAG_AFTER);
							break;
						case 'Opt_Xml_Text':
							$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE);
							$queue = $this->_pushQueue($stack, $queue, $item, NULL);
							// Next part in the post-process section
							break;
						case 'Opt_Xml_Element':
							if($this->isNamespace($item->getNamespace()))
							{								
								if(!$item->hasChildren() && $item->get('single'))
								{
									$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_SINGLE_BEFORE,
										Opt_Xml_Buffer::TAG_SINGLE_AFTER, Opt_Xml_Buffer::TAG_AFTER);
								}
								else
								{
									$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_OPENING_BEFORE,
										Opt_Xml_Buffer::TAG_OPENING_AFTER, Opt_Xml_Buffer::TAG_CONTENT_BEFORE);
									
									$queue = $this->_pushQueue($stack, $queue, $item, NULL);
									// Next part in the post-process section
								}
							}
							else
							{
								// TODO: Rebuild according to the docs.
								$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_OPENING_BEFORE);
								if($item->bufferSize(Opt_Xml_Buffer::TAG_NAME) == 0)
								{
									$name = $item->getXmlName();
								}
								elseif($item->bufferSize(Opt_Xml_Buffer::TAG_NAME) == 1)
								{
									$name = $item->buildCode(Opt_Xml_Buffer::TAG_NAME);
								}
								else
								{
									throw new Opt_CompilerCodeBufferConflict_Exception(1, 'TAG_NAME', $item->getXmlName());
								}
								if(!$item->hasChildren() && $item->bufferSize(Opt_Xml_Buffer::TAG_CONTENT) == 0 && $item->get('single'))
								{
									$output .= '<'.$name.$this->_linkAttributes($item).' />'.$item->buildCode(Opt_Xml_Buffer::TAG_SINGLE_AFTER,Opt_Xml_Buffer::TAG_AFTER);
									$item = null;
								}
								else
								{
									$output .= '<'.$name.$this->_linkAttributes($item).'>'.$item->buildCode(Opt_Xml_Buffer::TAG_OPENING_AFTER);
								
									if($item->bufferSize(Opt_Xml_Buffer::TAG_CONTENT) > 0)
									{
										$output .= $item->buildCode(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, Opt_Xml_Buffer::TAG_CONTENT, Opt_Xml_Buffer::TAG_CONTENT_AFTER);
									}
									elseif($item->hasChildren())
									{
										$output .= $item->buildCode(Opt_Xml_Buffer::TAG_CONTENT_BEFORE);
										$item->set('_name', $name);
										$queue = $this->_pushQueue($stack, $queue, $item, NULL);
										// Next part in the post-process section
										break;
									}
									else
									{
										$output .= $item->buildCode(Opt_Xml_Buffer::TAG_CLOSING_BEFORE).'</'.$name.'>'.$item->buildCode(Opt_Xml_Buffer::TAG_CLOSING_AFTER, Opt_Xml_Buffer::TAG_AFTER);
									}
								}
							}
							break;
						case 'Opt_Xml_Expression':
							$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE);
							break;
						case 'Opt_Xml_Root':
							$output .= $item->buildCode(Opt_Xml_Buffer::TAG_BEFORE);

							if($item->hasProlog())
							{
								$output .= str_replace('<?xml', '<<?php echo \'?\'; ?>xml', $item->getProlog()->getProlog())."\r\n";
							}
							if($item->hasDtd())
							{
								$output .= $item->getDtd()->getDoctype()."\r\n";
							}
							$queue = $this->_pushQueue($stack, $queue, $item, NULL);
							break;
					}
				}
				catch(Opl_Goto_Exception $goto){}	// postprocess:
				
				if($queue->count() == 0)
				{
					if($stack->count() == 0)
					{
						break;
					}
					$output .= $this->_doPostlinking($item);
					
					list($item, $queue, $pp) = $stack->pop();
					
					$output .= $this->_doPostlinking($item);
				}
			}
		} // end _stage3();
		
		/*
		 * Expression compiler
		 */
		
		public function compileExpression($expr, $allowAssignment = false, $escape = self::ESCAPE_ON)
		{
			// cat $expr > /dev/oracle > $result > happy programmer :)
			preg_match_all('/(?:'.
	       			$this->_rSingleQuoteString.'|'.
	       			$this->_rBacktickString.'|'.
					$this->_rHexadecimalNumber.'|'.
					$this->_rDecimalNumber.'|'.
					$this->_rLanguageVar.'|'.
					$this->_rVariable.'|'.
					$this->_rOperators.'|'.
					$this->_rIdentifier.')/x', $expr, $match);
					
			// Skip the whitespaces and create the translation units
			$cnt = sizeof($match[0]);
			$stack = new SplStack;
			$tu = array(0 => array());
			$tuid = 0;
			$maxTuid = 0;
			$prev = '';
			$chr = chr(18);
			
			for($i = 0; $i < $cnt; $i++)
			{
				if(ctype_space($match[0][$i]) || $match[0][$i] == '')
				{
					continue;
				}
				
				// TODO: Add error checking here!
				switch($match[0][$i])
				{
					case ',':
						if($prev == '(' || $prev == ',')
						{
							throw new Opt_Expression_Exception('OP_COMMA', $match[0][$i], $expr);
						}
						$tuid = $stack->pop();
					case '[':
					case '(':
						$tu[$tuid][] = $match[0][$i];
						++$maxTuid;
						$tu[$tuid][] = $chr.$maxTuid;	// A fake token that marks the translation unit which goes here.
						$stack->push($tuid);
						$tuid = $maxTuid;
						$tu[$tuid] = array();
						break;
					case ']':
					case ')':
						// If we have a situation like (), we can remove the TU we've just created,
						// because it's empty and will confuse the expression compiler later.
						if($prev == '(')
						{
							unset($tu[$tuid]);
							--$maxTuid;
						}
						$tuid = $stack->pop();
						if($prev == '(')
						{
							array_pop($tu[$tuid]);
						}
						if($prev == ',')
						{
							throw new Opt_Expression_Exception('OP_BRACKET', $match[0][$i], $expr);
						}
						$tu[$tuid][] = $match[0][$i];
						break;
					default:
						$tu[$tuid][] = $match[0][$i];
				}
				$prev = $match[0][$i];
			}			
			// Process the translation units
			// and avoid recursion!
			foreach($tu as $id => &$tuItem)
			{
				$tuItem = $this->_compileExpression($expr, $allowAssignment, $tuItem, $id);
			}			
			$assign = $tu[0][1];
			$variable = $tu[0][2];
			
			// Link the expression
			$tuid = 0;
			$i = -1;
			$cnt = sizeof($tu[0][0]);
			$stack = new SplStack;
			
			$expression = '';
			
			while(true)
			{
				$i++;
				$token = &$tu[$tuid][0][$i];
				
				// If we've found a translation unit, we must stop for a while the current one
				// and link the new.
				if(strlen($token) > 0 && $token[0] == $chr)
				{
					$stack->push(Array($tuid, $i, $cnt));
					$tuid = (int)ltrim($token, $chr);
					$i = -1;
					$cnt = sizeof($tu[$tuid][0]);
					continue;				
				}
				else
				{
					$expression .= $token;
				}
			
				if($i >= $cnt)
				{
					if($stack->count() == 0)
					{
						break;
					}
					// OK, current TU is ready. Check, whether there are unfinished upper-level TUs
					// on the stack
					unset($tu[$tuid]);
					list($tuid, $i, $cnt) = $stack->pop();
				}
			}
			
			// Escape
			$result = $expression;
			if($escape != self::ESCAPE_OFF && !$assign)
			{
				if($expr[1] == ':' && $expr[2] != ':')
				{
					if($expr[0] != 'e' && $expr[0] != 'u')
					{
						throw new Opt_InvalidExpressionModifier_Exception($expr[0], $expr);
					}
					$result = $this->escape($result, $expr[0] == 'e');
				}
				else
				{
					$result = $this->escape($result);
				}
			}
			// Pack everything
			if($escape != self::ESCAPE_BOTH)
			{
				return array(0 => $result, $assign, $variable, NULL);
			}
			else
			{
				return array(0 => $result, $assign, $variable, $expression);
			}
		} // end compileExpression();
		
		protected function _compileExpression(&$expr, $allowAssignment, Array &$tokens, $tu)
		{
			/* The method processes a single translation unit (TU). For example, in the expression
			 *		$a is ($b + $c) * $d
			 * we have the following translation units:
			 * 1. $a is #TU2 * $d
			 * 2. $b + $c
			 * 
			 * They are compiled separately and automatically, so you do not have to do this on
			 * your own. This has been done to remove the recursion from the source code, and moreover
			 * it allows, for example, to manage the argument order in the functions.
			 */

			// Operator mappings
			$wordOperators = array(
				'eq' => '==',
				'eqt' => '===',
				'ne' => '!=',
				'net' => '!==',
				'neq' => '!=',
				'neqt' => '!==',
				'lt' => '<',
				'le' => '<=',
				'lte' => '<=',
				'gt' => '>',
				'ge' => '>=',
				'gte' => '>=',
				'and' => '&&',
				'or' => '||',
				'xor' => 'xor',
				'not' => '!',
				'mod' => '%',
				'div' => '/',
				'add' => '+',
				'sub' => '-',
				'mul' => '*',
				'shl' => '<<',
				'shr' => '>>'
			);
			
			// Previous token information
			$previous = array(
				'token' => null,
				'source' => null,
				'result' => null
			);
			// Some standard "next token sets"
			$valueSet = self::OP_VARIABLE | self::OP_LANGUAGE_VAR | self::OP_STRING | self::OP_NUMBER |
				self::OP_IDENTIFIER | self::OP_PRE_OPERATOR | self::OP_OBJMAN | self::OP_BRACKET;
			$operatorSet = self::OP_OPERATOR | self::OP_POST_OPERATOR | self::OP_NULL;
			// Initial state
			$state = array(
				'next' => $valueSet | self::OP_NULL,	// What token must occur next.
				'step' => 0,		// This flag helps processing brackets by saving some extra token information.
				'func' => 0,		// The function call type: 0 - OPT function (with "$this" as the first argument); 1 - ordinary function
				'oper' => false,	// The assignment flag. The value must be assigned to a variable, so on the left side there must not be any operator (false).
				'clone' => 0,		// We've already used "clone"
				'preop' => false,	// Prefix operators ++ and -- found. This flag is cancelled by any other operator.
				'rev' => NULL,		// Changing the argument order options
				'assign_func' => false,	// Informing the bracket parser that the first argument must be a language block, which must be processed separately.
				'tu'	=> 0,		// What has opened a translation unit? The field contains the token type.
				'variable' => NULL,	// To detect if the expression is a single variable or not.
				'function' => NULL	// Function name for the argument checker errors
			);
			$chr = chr(18);		// Which ASCII code marks the translation unit
			$result = array();	// Here we put the compilation result
			$void = false;		// This is a fake variable for a recursive call, as a last argument (reference)
			$assign = false;
			$to = sizeof($tokens);
			for($i = 0; $i < $to; $i++)
			{
				// Some initializing stuff.
				$token = &$tokens[$i];
				$parsefunc = false;
				$current = array(
					'token' => null,		// Symbolic token type. Look at the file header to find the token definitions.
					'source' => $token,		// Original form of the token is also remembered.
					'result' => null,		// Here we have to put the result PHP code generated from the token.
				);
				
				// Find out, what it is.
				switch($token)
				{
					case '[':
						// This code checks, whether the token is properly used. We have to assign it to one of the token groups.
						if(!($state['next'] & self::OP_SQ_BRACKET))
						{
							throw new Opt_Expression_Exception('OP_SQ_BRACKET', $token, $expr);
						}
						$result[] = '[';
						$state['tu'] = self::OP_SQ_BRACKET_E;
						$state['next'] = self::OP_TU;
						$state['step'] = self::OP_VARIABLE;
						continue;
					case ']':
						if(!($state['next'] & self::OP_SQ_BRACKET_E))
						{
							throw new Opt_Expression_Exception('OP_SQ_BRACKET_E', $token, $expr);
						}
						$current['token'] = $state['step'];
						$current['result'] = ']';
						$state['step'] = 0;
						// This is the way we mark, what tokens can occur next.
						$state['next'] = self::OP_OPERATOR | self::OP_NULL | self::OP_SQ_BRACKET;
						if($state['clone'] == 1)
						{
							$state['next'] = self::OP_NULL | self::OP_SQ_BRACKET;
						}
						break;
					case '{':
					case '}':
						throw new Opt_Expression_Exception('OP_CURLY_BRACKET', $token, $expr);
						break;
					case 'add':
					case 'sub':
					case 'mul':
					case 'div':
					case 'mod':
					case 'shl':
					case 'shr':
					case 'eq':
					case 'neq':
					case 'eqt':
					case 'neqt':
					case 'ne':
					case 'net':
					case 'lt':
					case 'le':
					case 'lte':
					case 'gt':
					case 'gte':
					case 'ge':
						// These guys can be also method names, if in proper context
						if($previous['token'] == self::OP_CALL)
						{
							$this->_compileIdentifier($token, $previous['token'], $previous['result'], 
								isset($tokens[$i+1]) ? $tokens[$i+1] : null, $operatorSet, $expr, $current, $state);
							break;
						}
					case 'and':
					case 'or':
					case 'xor':
						$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);

						// And these three ones - only strings.		
						if($state['next'] & self::OP_STRING)
						{
							$current['result'] = '\''.$token.'\'';
							$current['token'] = self::OP_STRING;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						else
						{
							if(!($state['next'] & self::OP_OPERATOR))
							{
								throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
							}
							$current['result'] = $wordOperators[$token];
							$current['token'] = self::OP_OPERATOR;
							$state['next'] = $valueSet;
							$state['preop'] = false;
						}
						$state['variable'] = false;
						break;
					case 'not':
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
						}
						$current['token'] = self::OP_PRE_OPERATOR;
						$current['result'] = $wordOperators[$token];
						$state['next'] = $valueSet;
						$state['variable'] = false;					
						break;
					case 'new':
					case 'clone':
						if(!$this->_tpl->advancedOOP)
						{
							throw new Opt_ExpressionOptionDisabled_Exception($token, 'security reasons');
						}
						if(!($state['next'] & self::OP_OBJMAN))
						{
							throw new Opt_Expression_Exception('OP_OBJMAN', $token, $expr);
						}
						$current['result'] = $token.' ';
						$current['token'] = self::OP_OBJMAN;
						$state['next'] = ($token == 'new' ? self::OP_IDENTIFIER : self::OP_BLOCK);
						$state['clone'] = 1;
						$state['variable'] = false;
						break;
					case 'is':
						if($state['next'] & self::OP_STRING)
						{
							$current['result'] = '\''.$token.'\'';
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
							break;
						}
					case '=':
						if(!$allowAssignment)
						{
							throw new Opt_ExpressionOptionDisabled_Exception('Assignments', 'compiler requirements');
						}
						// We have to assign the data to the variable or object field.
						if(($previous['token'] == self::OP_VARIABLE || $previous['token'] == self::OP_FIELD) && !$state['oper'] && $previous['token'] != self::OP_LANGUAGE_VAR)
						{
							$current['result'] = '=';
							$current['token'] = self::OP_ASSIGN;
							$state['variable'] = false;
							$state['next'] = $valueSet ^ self::OP_NULL;
							$assign = true;
						}
						else
						{
							throw new Opt_Expression_Exception('OP_ASSIGN', $token, $expr);
						}
						break;
					case '!==':
					case '==':
					case '===':
					case '!=':
					case '+':
					case '*':
					case '/':
					case '%':						
						if(!($state['next'] & self::OP_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);
						
						$current['result'] = $token;
						$state['next'] = $valueSet;
						$state['oper'] = true;
						$state['preop'] = false;
						$state['variable'] = false;
						break;
					case '-':
						if($state['next'] & self::OP_OPERATOR)
						{
							$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);
							
							$current['result'] = $token;
							$state['oper'] = true;
							$state['next'] = $valueSet;
							$state['preop'] = false;
						}
						elseif($state['next'] & self::OP_NUMBER | self::OP_VARIABLE | self::OP_IDENTIFIER)
						{
							$current['result'] = $token;
							$state['next'] = self::OP_NUMBER | self::OP_VARIABLE | self::OP_IDENTIFIER;
						}
						else
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$state['variable'] = false;
						break;
					case '~':
						if(!($state['next'] & self::OP_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$current['result'] = '.';
						$state['next'] = $valueSet;
						$state['oper'] = true;
						$state['preop'] = false;
						$state['variable'] = false;
						break;
					case '++':
					case '--':
						$current['token'] = self::OP_PRE_OPERATOR;
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							$current['token'] = self::OP_POST_OPERATOR;
							if(!($state['next'] & self::OP_POST_OPERATOR))
							{							
								throw new Opt_Expression_Exception('OP_POST_OPERATOR', $token, $expr);
							}
							else
							{
								$state['next'] = self::OP_OPERATOR | self::OP_NULL;
							}
						}
						else
						{
							$state['next'] = self::OP_VARIABLE | self::OP_LANGUAGE_VAR | self::OP_NUMBER;
							$state['preop'] = true;
						}
						$state['oper'] = true;
						$state['variable'] = false;
						$current['result'] = $token;
						break;
					case '!':
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
						}
						$current['result'] = $token;
						$current['token'] = self::OP_PRE_OPERATOR;
						$state['variable'] = false;
						break;
					case 'null':
					case 'false':
					case 'true':
						if(!($state['next'] & self::OP_NUMBER))
						{
							throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
						}
						$current['token'] = self::OP_NUMBER;
						$current['result'] = $token;
						$state['next'] = $operatorSet;
						break;
					case '.':
						throw new Opt_Expression_Exception('.', $token, $expr);
						break;
					case '::':
						if(!($state['next'] & self::OP_CALL))
						{
							throw new Opt_Expression_Exception('OP_CALL', $token, $expr);
						}
						if($previous['token'] == self::OP_CLASS)
						{
							$current['result'] = '::';
							$state['call'] = 0;
						}
						else
						{
							$current['result'] = '->';
						}
						$current['token'] = self::OP_CALL;
						$state['next'] = self::OP_IDENTIFIER;
						break;
					case '(':
						if($previous['token'] == self::OP_METHOD || $previous['token'] == self::OP_FUNCTION || $previous['token'] == self::OP_CLASS)
						{
							// Find the arguments
							$args = array();
							for($j = $i + 1; $j < $to && $tokens[$j] != ')'; $j++)
							{								
								if($tokens[$j][0] == $chr)
								{
									$args[] = $tokens[$j];
								}
								elseif($tokens[$j] != ',')
								{
									throw new Opt_Expression_Exception('OP_UNKNOWN', $tokens[$j], $expr);	
								}
							}
							$argNum = sizeof($args);
							
							// Optionally, change the argument order
							if(!is_null($state['rev']))
							{
								$this->_reverseArgs($args, $state['rev'], $state['function']);
								$state['rev'] = null;
								$argNum = sizeof($args);
							}
						
							$result[] = '(';
							if($argNum > 0 && $state['assign_func'])
							{
								$this->_translationConversion = (int)trim($args[0], $chr);
							}
							for($k = 0; $k < $argNum; $k++)
							{
								$result[] = $args[$k];
								if($k < $argNum - 1)
								{
									$result[] = ',';
								}
							}
							$i = $j-1;
							$state['next'] = self::OP_BRACKET_E;
							$state['step'] = $previous['token'];
							continue;
						}
						else
						{
							if(!($state['next'] & self::OP_BRACKET))
							{
								throw new Opt_Expression_Exception('OP_BRACKET', $token, $expr);
							}
							$result[] = '(';
							$state['tu'] = self::OP_BRACKET_E;
							$state['next'] = self::OP_TU;
							$state['step'] = self::OP_VARIABLE;
						}
						break;
					case ')':
						if($state['step'] == 0)
						{
							throw new Opt_Expression_Exception('OP_BRACKET', $token, $expr);
						}
						else
						{
							if(!($state['next'] & self::OP_BRACKET_E))
							{
								throw new Opt_Expression_Exception('OP_BRACKET_E', $token, $expr);
							}
							$current['token'] = $state['step'];
							$current['result'] = ')';
							$state['step'] = 0;
							$state['next'] = self::OP_OPERATOR | self::OP_NULL | self::OP_CALL;
							if($state['clone'] == 1)
							{
								$state['next'] = self::OP_NULL | self::OP_CALL;
							}
						}
						break;
					default:	
						if($token[0] == $chr)
						{
							// We've found another translation unit.
							if(!($state['next'] & self::OP_TU))
							{
								throw new Opt_Expression_Exception('OP_TU', 'Translation unit #'.ltrim($token, $chr), $expr);
							}
							$result[] = $token;
							$state['next'] = $state['tu'];
						}
						elseif(preg_match('/^'.$this->_rVariable.'$/', $token))
						{
							if(!($state['next'] & self::OP_VARIABLE))
							{
								throw new Opt_Expression_Exception('OP_VARIABLE', $token, $expr);
							}
							// We do the first character test manually, because
							// in regular expression the parser would receive too much rubbish.
							if(!ctype_alpha($token[1]) && $token[1] != '_')
							{
								throw new Opt_Expression_Exception('OP_VARIABLE', $token, $expr);
							}
							$current['result'] = $this->_compileVariable($token);
							$current['token'] = self::OP_VARIABLE;
							if(is_null($state['variable']))
							{
								$state['variable'] = true;
							}
							// Hmmm... and what is the purpose of this IF? Seriously, I forgot.
							if($state['clone'] == 1)
							{
								$state['next'] = self::OP_SQ_BRACKET | self::OP_CALL | self::OP_NULL;
							}
							else
							{
								$state['next'] = $operatorSet | self::OP_SQ_BRACKET | self::OP_CALL;
							}
						}
						elseif(preg_match('/^'.$this->_rLanguageVarExtract.'$/', $token, $found))
						{
							if(!($state['next'] & self::OP_LANGUAGE_VAR))
							{
								throw new Opt_Expression_Exception('OP_LANGUAGE_VAR', $token, $expr);
							}
							$current['result'] = $this->_compileLanguageVar($found[1], $found[2], $tu);
							$current['token'] = self::OP_LANGUAGE_VAR;
							$state['next'] = $operatorSet;
						}
						elseif(preg_match('/^'.$this->_rDecimalNumber.'$/', $token))
						{
							if(!($state['next'] & self::OP_NUMBER))
							{
								throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
							}
							$current['result'] = $token;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rHexadecimalNumber.'$/', $token))
						{
							if(!($state['next'] & self::OP_NUMBER))
							{
								throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
							}
							$current['result'] = $token;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rSingleQuoteString.'$/', $token))
						{
							if(!($state['next'] & self::OP_STRING))
							{
								throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
							}
							$current['result'] = $this->_compileString($token);
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rBacktickString.'$/', $token))
						{
							if(!($state['next'] & self::OP_STRING))
							{
								throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
							}
							$current['result'] = $this->_compileString($token);
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rIdentifier.'$/', $token))
						{
							$this->_compileIdentifier($token, $previous['token'], $previous['result'], 
								isset($tokens[$i+1]) ? $tokens[$i+1] : null, $operatorSet, $expr, $current, $state);
						}
				}
				$previous = $current;
				$result[] = $current['result'];
			}
			
			$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);
			
			if(!($state['next'] & self::OP_NULL))
			{
				throw new Opt_Expression_Exception('OP_NULL', $token, $expr);
			}
			// TODO: For variable detection: check also class/object fields!
			return array($result, $assign, $state['variable']);
		} // end _compileExpression();
		
		protected function _testPreOperators($previous, $state, &$token, &$expr)
		{
			if(($previous == self::OP_METHOD || $previous == self::OP_FUNCTION || $previous == self::OP_EXPRESSION) && $state)
			{
				// Invalid use of prefix operators!
				throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
			}
		} // end _testPreOperators();
		
		protected function _compileVariable($name)
		{
			$value = substr($name, 1, strlen($name) - 1);
			$result = '';
			if(strpos($value, '.') !== FALSE)
			{
				$ns = explode('.', $value);
			}
			else
			{
				$ns = array(0 => $value);
			}
			
			if($name[0] == '@')
			{
				// The instruction may wish to handle this variable somehow differently.
				if(($to = $this->convert('##var_'.$ns[0])) == '##var_'.$ns[0])
				{
					$result = 'self::$_vars';	// Standard handler
				}
				else
				{
					$result = $to;		// Programmer-defined handler
					unset($ns[0]);		// We assume that the variable name is already included into the handler.
				}
				
				// Link the rest of the array call.
				foreach($ns as $item)
				{
					if(ctype_digit($item))
					{
						$result .= '['.$item.']';
					}
					else
					{
						$result .= '[\''.$item.'\']';
					}
				}
				return $result;
			}
			else
			{
				/*
				 * This is the variable scanner that parses things like "$var.foo.bar.joe".
				 * Each segment of the name can be parsed in different format, depending on
				 * the programmer settings. Moreover, it recognizes the special calls, like "opt"/"system"
				 * or section element calls.
				 */
			
				$path = '';
				$previous = null;
				$code = '';
				$count = sizeof($ns);
				$state = array(
					'access' => $this->_tpl->variableAccess,
					'section' => null,
					'first' => false	
				);

				// Check the first element for special keywords.
				switch($ns[0])
				{
					case 'opt':
					case 'sys':
					case 'system':
						return $this->_compileSys($ns);
					case 'this':
						$state['access'] = Opt_Class::ACCESS_LOCAL;
						unset($ns[0]);
						break;
					case 'global':
						$state['access'] = Opt_Class::ACCESS_GLOBAL;
						unset($ns[0]);
						break;
				}
				// Scan the rest of the name
				foreach($ns as $id => $item)
				{
					$previous = $path;
					if($path == '')
					{
						// Parsing the first element. First, check the conversions.
						if(($to = $this->convert('##simplevar_'.$item)) != '##simplevar_'.$item)
						{
							$item = $to;
						}
						$path = $item;
						$state['first'] = true;
					}
					else
					{
						// Parsing one of the later elements
						$path .= '.'.$item;
						$state['first'] = false;
					}
					
					// Processing sections
					if(!is_null($this->isProcessor('section')))
					{
						if(is_null($state['section']))
						{
							// Check if any section with the specified name exists.
							$proc = $this->processor('section');
							$name = $this->convert($item);
							if(!is_null($section = $proc->getSection($name)))
							{
								$path = $name;
								$state['section'] = $section;

								if($id == $count - 1)
								{
									// This is the last name element.
									return $section['format']->get('sectionCurrent');
								}
								continue;
							}
						}
						else
						{
							// The section has been found, we need to process the item.
							$state['section']['format']->assign('_sectionItemName', $item);
							$code = $state['section']['format']->get('itemVariable');
							
							$state['section'] = null;
							continue;
						}
					}
					
					// Now, the normal variables
					if($state['first'])
					{
						$format = $this->getFormat($path, true);
						if(!$format->supports('variable'))
						{
							throw new Opt_HooksNotSupported_Exception($format->getName(), 'variable');
						}
						$format->assign('access', $state['access']);
						$format->assign('item', $item);
						$code = $format->get('variableMain');
					}
					else
					{
						// The subitems are processed with the upper-item format
						$format = $this->getFormat($previous, true);
						if(!$format->supports('variable'))
						{
							throw new Opt_HooksNotSupported_Exception($format->getName(), 'variable');
						}
						$format->assign('item', $item);
						$code .= $format->get('variableSubitem');
					}
				}
				return $code;
			}
		} // end _compileVariable();
		
		protected function _compileLanguageVar($group, $id, $tu)
		{
			if(is_null($this->_tf))
			{
				throw new Opl_NoTranslationInterface_Exception('OPT template compiler');
			}
			if($tu === $this->_translationConversion)
			{
				$this->_translationConversion = null;
				return '\''.$group.'\',\''.$id.'\'';
			}
			return '$this->_tf->_(\''.$group.'\',\''.$id.'\')';
		} // end _compileLanguageVar();
		
		protected function _compileSys($ns)
		{
			switch($ns[1])
			{
				case 'version':
					return '\''.Opt_Class::VERSION.'\'';
				case 'const':
					return 'constant(\''.$ns[2].'\')';				
				default:
					if(!is_null($this->isProcessor($ns[1])))
					{
						return $this->processor($ns[1])->processSystemVar($ns);
					}
					
					throw new Opt_OptBlockUnknown_Exception('$'.implode('.', $ns));				
			}
		} // end _compileSys();
		
		protected function _compileString($str)
		{
			// TODO: Fix
			switch($str[0])
			{
				case '\'':
					return $str;
				case '`':
					if(is_null($this->_tpl->backticks))
					{
						throw new Opt_NotSupported_Exception('backticks', 'not configured');
					}
					elseif(is_string($this->_tpl->backticks))
					{
						// A redirect to a function		
						return $this->_tpl->backticks.'(\''.str_replace('\'', '\\\'', stripslashes(substr($str, 1, strlen($str) - 2))).'\')';
					}
					elseif(is_array($this->_tpl->backticks) && is_object($this->_tpl->backticks[0]))
					{
						// A redirect to an object method
			
						return '$this->_tpl->backticks[0]->'.$this->_tpl->backticks[1].'(\''.str_replace('\'', '\\\'', stripslashes(substr($str, 1, strlen($str) - 2))).'\')';
					}
					else
					{
						throw new Opt_InvalidCallback_Exception('backticks');
					}
				default:
					return '\''.$str.'\'';
			}
		} // end _compileString();
		
		protected function _compileIdentifier($token, $previous, $pt, $next, $operatorSet, &$expr, &$current, &$state)
		{
			// TODO: Add the ability to disable OOP completely.
			if($previous == self::OP_OBJMAN)
			{
				// Class constructor call
				if(isset($this->_classes[$token]))
				{
					$current['result'] = $this->_classes[$token];
					$current['token'] = self::OP_CLASS;
					$state['next'] = self::OP_BRACKET | self::OP_NULL;
					if($next == '(')
					{
						$state['func'] = 1;
					}
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Class', $token);
				}
			}
			elseif($next == '(')
			{
				// Function/method call
				if($previous == self::OP_CALL)
				{
					$current['result'] = $token;
					$current['token'] = self::OP_METHOD;
					$state['next'] = self::OP_BRACKET;
					$state['func'] = 1;
				}
				elseif(isset($this->_functions[$token]))
				{
					$name = $this->_functions[$token];
					if($name[0] == '#')
					{
						$pos = strpos($name, '#', 1);
						if($pos === false)
						{
							throw new Opt_InvalidArgumentFormat_Exception($name, $token);
						}
						$state['rev'] = substr($name, 1, $pos - 1);
						$name = substr($name, $pos+1, strlen($name));
					}
					$current['result'] = $name;
					$current['token'] = self::OP_FUNCTION;
					$state['next'] = self::OP_BRACKET;
					$state['function'] = $token;
				}
				elseif($token == 'assign')
				{
					$current['result'] = '$this->_tf->assign';
					$current['token'] = self::OP_FUNCTION;
					$state['next'] = self::OP_BRACKET;
					$state['assign_func'] = true;
					$state['function'] = $token;
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Function', $token);
				}
			}
			elseif($previous == self::OP_CALL)
			{
				// Class/object field call, check whether static or not.
				$current['result'] = ($pt == '::' ? '$'.$token : $token);
				$current['token'] = self::OP_FIELD;
				$state['next'] = $operatorSet | self::OP_SQ_BRACKET | self::OP_CALL;
				if($state['clone'] == 1)
				{
					$state['next'] = self::OP_SQ_BRACKET | self::OP_CALL | self::OP_NULL;
				}
			}
			elseif($next == '::')
			{
				// Static class call
				if(isset($this->_classes[$token]))
				{
					$current['result'] = $this->_classes[$token];
					$current['token'] = self::OP_CLASS;
					$state['next'] = self::OP_CALL;
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Class', $token);
				}
			}
			else
			{
				// An ending string.
				if(!$state['next'] & self::OP_STRING)
				{
					throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
				}
				$state['next'] = self::OP_NULL;
				$current['token'] = self::OP_STRING;
				$current['result'] = '\''.$token.'\'';
			}
		} // end _compileIdentifier();
		
		protected function _reverseArgs(&$args, $format, $function)
		{
			$codes = explode(',', $format);
			$newArgs = array();
			$i = 0;
			foreach($codes as $code)
			{
				$data = explode(':', $code);
				if(!isset($args[$i]))
				{
					if(!isset($data[1]))
					{
						throw new Opt_FunctionArgument_Exception($i, $function);
					}
					$newArgs[(int)$data[0]-1] = $data[1];
				}
				else
				{
					$newArgs[(int)$data[0]-1] = $args[$i];
				}
				$i++;
			}
			$args = $newArgs;
		} // end _reverseArgs();
	} // end Opt_Compiler_Class;
