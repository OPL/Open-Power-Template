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
 * $Id: Processor.php 23 2008-12-03 14:11:58Z extremo $
 */

	class Opt_Compiler_Processor
	{
		// Attribute types
		const STRING = 1;
		const HARD_STRING = 2;
		const NUMBER = 3;
		const EXPRESSION = 4;
		const ASSIGN_EXPR = 5;
		const ID = 6;
		const BOOL = 7;
		const ID_EMP = 8; // Same as "ID", but allows empty content.
		
		const REQUIRED = 1;
		const OPTIONAL = 2;
	
		// Class fields
		/**
		 * The compiler object.
		 *
		 * @var Opt_Compiler_Class
		 */
		protected $_compiler;
		/**
		 * The main class object.
		 *
		 * @var Opt_Class
		 */
		protected $_tpl;
		
		protected $_name;
		private $_queue = NULL;
		private $_instructions = array();
		private $_attributes = array();
		
		public function __construct(Opt_Compiler_Class $compiler)
		{
			$this->_compiler = $compiler;
			$this->_tpl = $compiler->getParser();
			
			$this->configure();
		} // end __construct();
		
		public function configure()
		{
			/* null */
		} // end configure();
		
		public function reset()
		{
			/* null */
		} // end reset();

		public function processNode(Opt_Xml_Node $node)
		{
			$name = '_process'.ucfirst($node->getName());
			$this->$name($node);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$name = '_postprocess'.ucfirst($node->getName());
			$this->$name($node);
		} // end postprocessNode();
		
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_processAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end processAttribute();

		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_postprocessAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end postprocessAttribute();
		
		public function processSystemVar($opt)
		{
			/* null */
		} // end processSystemVar();
		
		final public function getName()
		{
			return $this->_name;
		} // end getName();
		
		final public function getQueue()
		{
			$q = $this->_queue;
			$this->_queue = NULL;
			return $q;
		} // end getQueue();
		
		final public function getInstructions()
		{
			return $this->_instructions;		
		} // end getInstructions();
		
		final public function getAttributes()
		{
			return $this->_attributes;
		} // end getAttributes();
		
		final protected function _process(Opt_Xml_Node $node)
		{
			if(is_null($this->_queue))
			{
				$this->_queue = new SplQueue;
			}
			if($node->hasChildren())
			{
				foreach($node as $child)
				{
					$this->_queue->enqueue($child);		
				}
			}
		} // end _process();
		
		final protected function _debugPrintQueue()
		{
			var_dump($this->_queue);
		} // end _debugPrintQueue();
		
		final protected function _addInstructions($list)
		{
			if(is_array($list))
			{
				$this->_instructions = array_merge($this->_instructions, $list);
			}
			else
			{
				$this->_instructions[] = $list;
			}
		} // end _addInstructions();
		
		final protected function _addAttributes($list)
		{
			if(is_array($list))
			{
				$this->_attributes = array_merge($this->_attributes, $list);
			}
			else
			{
				$this->_attributes[] = $list;
			}
		} // end _addAttributes();
		
		final protected function _extractAttributes(Opt_Xml_Element $subitem, Array &$config)
		{
			$required = array();
			$optional = array();
			$unknown = null;
			// Decide, what is what.
			foreach($config as $name => &$data)
			{
				if($name == '__UNKNOWN__')
				{
					$unknown = &$data;					
				}
				elseif($data[0] == self::REQUIRED)
				{
					$required[$name] = &$data;
				}
				elseif($data[0] == self::OPTIONAL)
				{
					$optional[$name] = &$data;
				}
			}
			$config = array();
			$return = array();

			// Parse required attributes
			$attrList = $subitem->getAttributes(false);
			foreach($required as $name => &$data)
			{
				if(isset($attrList[$name]))
				{
					$aname = $name;
				}
				elseif(isset($attrList['str:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					$data[1] = self::STRING;
					$aname = 'str:'.$name;
				}
				elseif(isset($attrList['parse:'.$name]))
				{
					if($data[1] == self::STRING)
					{
						$data[1] = self::EXPRESSION;
					}
					$aname = 'parse:'.$name;
				}
				else
				{
					throw new Opt_AttributeNotDefined_Exception($name, $subitem->getXmlName());
				}

				$config[$name] = $this->_extractAttribute($subitem, $attrList[$aname], $data[1]);
				unset($attrList[$aname]);
			}

			// Parse optional attributes
			foreach($optional as $name => &$data)
			{
				if(isset($attrList[$name]))
				{
					$aname = $name;
				}
				elseif(isset($attrList['str:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					$data[1] = self::STRING;
					$aname = 'str:'.$name;
				}
				elseif(isset($attrList['parse:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					if($data[1] == self::STRING)
					{
						$data[1] = self::EXPRESSION;
					}
					$aname = 'parse:'.$name;
				}
				else
				{
					// We can't use isset() because the default data might be "NULL"
					if(!array_key_exists(2, $data))
					{
						throw new Opt_APIMissingDefaultValue_Exception($name, $subitem->getXmlName());
					}
					$config[$name] = $data[2];
					continue;
				}
			
				$config[$name] = $this->_extractAttribute($subitem, $attrList[$aname], $data[1]);
				unset($attrList[$aname]);
			}
			// The remaining tags must be processed using $unknown rule, however it
			// must be defined.
			if(!is_null($unknown))
			{
				// TODO: Add here namespace check!
				$type = $unknown[1];
				foreach($attrList as $name => $attr)
				{					
					if(strpos($name, 'str:') === 0 && ($type == self::STRING || $type == self::EXPRESSION || $type == self::ASSIGN_EXPR))
					{
						$type = self::STRING;
						$name = substr($name, 4, strlen($name) - 4);
					}
					elseif(strpos($name, 'parse:') === 0 && ($type == self::EXPRESSION || $type == self::ASSIGN_EXPR || $type == self::STRING))
					{
						if($type == self::STRING)
						{
							$type = self::EXPRESSION;
						}
						$name = substr($name, 6, strlen($name) - 6);
					}
					$return[$name] = $this->_extractAttribute($subitem, $attr, $type);
				}
			}
			return $return;
		} // end _extractAttributes();
		
		final private function _extractAttribute(Opt_Xml_Element $item, Opt_Xml_Attribute $attr, $type)
		{
			$value = (string)$attr;
			switch($type)
			{
				case self::ID_EMP:
					if($value == '')
					{
						return $value;
					}
				case self::ID:
					if(!preg_match('/^[a-zA-Z0-9\_\.]+$/', $value))
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), 'identifier');
					}
					return $value;
				
				case self::NUMBER:
					if(!preg_match('/^\-?([0-9]+\.?[0-9]*)|(0[xX][0-9a-fA-F]+)$/', $value))
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), 'number');
					}
					return $value;
				case self::BOOL:
					if($value != 'yes' && $value != 'no')
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), '"yes" or "no"');
					}
					return ($value == 'yes');
				case self::STRING:
					if($attr->getNamespace() == 'parse')
					{
						$result = $this->_compiler->compileExpression($value, false, false);
						return $result[0];
					}
					else
					{
						return '\''.$value.'\'';
					}
					break;
				case self::EXPRESSION:
					if($attr->getNamespace() == 'str')
					{
						return '\''.$value.'\'';
					}
					else
					{
						// Do not allow the empty strings to be evaluated!
						if(strlen(trim($value)) == 0)
						{
							throw new Opt_AttributeEmpty_Exception($attr->getXmlName(), $item->getXmlName());
						}
						$result = $this->_compiler->compileExpression($value, false, false);
						return $result[0];
					}
					break;
				case self::ASSIGN_EXPR:
					if($attr->getNamespace() == 'str')
					{
						return '\''.$value.'\'';
					}
					else
					{
						// Do not allow the empty strings to be evaluated!
						if(strlen(trim($value)) == 0)
						{
							throw new Opt_AttributeEmpty_Exception($attr->getXmlName(), $item->getXmlName());
						}
						$result = $this->_compiler->compileExpression($value, true, false);
						return $result[0];
					}
					break;
				case self::HARD_STRING:
					return $value;
					break;
			}
		} // end _extractAttribute();
	} // end Opt_Compiler_Processor;
