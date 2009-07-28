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

	/**
	 * The class represents an XML tag.
	 */
	class Opt_Xml_Element extends Opt_Xml_Scannable
	{
		protected $_name;
		protected $_namespace;
		protected $_attributes;

		/**
		 * Creates a new XML tag with the specified name. The accepted
		 * name format is 'name' or 'namespace:name'.
		 *
		 * @param String $name The tag name.
		 */
		public function __construct($name)
		{
			parent::__construct();
			$this->setName($name);
		} // end __construct();

		/**
		 * Sets the name for the tag. The accepted format is 'name' or
		 * 'namespace:name'.
		 *
		 * @param String $name The tag name.
		 */
		public function setName($name)
		{
			if(strpos($name, ':') !== false)
			{
				$data = explode(':', $name);
				$this->_name = $data[1];
				$this->_namespace = $data[0];
			}
			else
			{
				$this->_name = $name;
			}
		} // end setName();

		/**
		 * Sets the namespace for the tag.
		 *
		 * @param String $namespace The namespace name.
		 */
		public function setNamespace($namespace)
		{
			$this->_namespace = $namespace;
		} // end setNamespace();

		/**
		 * Returns the tag name (without the namespace).
		 * @return String
		 */
		public function getName()
		{
			return $this->_name;
		} // end getName();

		/**
		 * Returns the tag namespace name.
		 * @return String
		 */
		public function getNamespace()
		{
			return $this->_namespace;
		} // end getNamespace();

		/**
		 * Returns the tag name (with the namespace, if possible)
		 *
		 * @return String
		 */
		public function getXmlName()
		{
			if(is_null($this->_namespace))
			{
				return $this->_name;
			}
			return $this->_namespace.':'.$this->_name;
		} // end getXmlName();

		/**
		 * Returns the list of attribute objects.
		 *
		 * @return Array
		 */
		public function getAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return array();
			}
			return $this->_attributes;
		} // end getAttributes();

		/**
		 * Returns the attribute with the specified name.
		 *
		 * @param String $xmlName The XML name of the attribute (with the namespace)
		 * @return Opt_Xml_Attribute
		 */
		public function getAttribute($xmlName)
		{
			if(!is_array($this->_attributes))
			{
				return NULL;
			}
			if(!isset($this->_attributes[$xmlName]))
			{
				return NULL;
			}
			return $this->_attributes[$xmlName];
		} // end getAttribute();

		/**
		 * Adds a new attribute to the tag.
		 *
		 * @param Opt_Xml_Attribute $attribute The new attribute.
		 */
		public function addAttribute(Opt_Xml_Attribute $attribute)
		{
			if(!is_array($this->_attributes))
			{
				$this->_attributes = array();
			}
			$this->_attributes[$attribute->getXmlName()] = $attribute;
		} // end addAttribute();

		/**
		 * Removes the specified attribute identified either by the object
		 * or by the XML name.
		 *
		 * @param String|Opt_Xml_Attribute $refNode The attribute to be removed
		 * @return Boolean
		 */
		public function removeAttribute($refNode)
		{
			if(!is_array($this->_attributes))
			{
				return NULL;
			}
			if(is_object($refNode))
			{
				foreach($this->_attributes as $id => $node)
				{
					if($node === $refNode)
					{
						unset($this->_attributes[$id]);
						return true;
					}
				}
			}
			elseif(is_string($refNode))
			{
				if(isset($this->_attributes[$refNode]))
				{
					unset($this->_attributes[$refNode]);
					return true;
				}
			}
			return false;
		} // end removeAttribute();

		/**
		 * Clears the attribute list.
		 */
		public function removeAttributes()
		{
			$this->_attributes = array();
		} // end removeAttributes();

		/**
		 * Returns 'true', if the tag contains attributes.
		 *
		 * @return Boolean
		 */
		public function hasAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return false;
			}
			return (sizeof($this->_attributes) > 0);
		} // end hasAttributes();

		/**
		 * Returns the XML tag name.
		 * @return String
		 */
		public function __toString()
		{
			return $this->getXmlName();
		} // end __toString();

		/**
		 * The method helps to clone the XML node by cloning
		 * its attributes.
		 *
		 * @internal
		 */
		protected function _cloneHandler()
		{
			if(is_array($this->_attributes))
			{
				foreach($this->_attributes as $name => $attribute)
				{
					$this->_attributes[$name] = clone $attribute;
				}
			}
		} // end _cloneHandler();

		/**
		 * Specifies, what node types can be children of XML tags.
		 *
		 * @internal
		 * @param Opt_Xml_Node $node
		 */
		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() != 'Opt_Xml_Element' && $node->getType() != 'Opt_Xml_Text' && $node->getType() != 'Opt_Xml_Comment')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Element', $node->getType());
			}
		} // end _testNode();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking.
		 */
		public function preLink(Opt_Compiler_Class $compiler)
		{
			if($compiler->isNamespace($this->getNamespace()))
			{
				// This code handles the XML elements that represent the
				// OPT instructions. They have shorter code, because
				// we do not need to display their tags.
				if(!$this->hasChildren() && $this->get('single'))
				{
					$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_SINGLE_BEFORE));
				}
				elseif($this->hasChildren())
				{
					$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_OPENING_BEFORE,
						Opt_Xml_Buffer::TAG_OPENING_AFTER, Opt_Xml_Buffer::TAG_CONTENT_BEFORE));

					$compiler->setChildren($this);
				}
				else
				{
					$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_OPENING_BEFORE,
						Opt_Xml_Buffer::TAG_OPENING_AFTER, Opt_Xml_Buffer::TAG_CONTENT_BEFORE));
				}
			}
			else
			{
				$wasElement = true;
				$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_OPENING_BEFORE));
				if($this->bufferSize(Opt_Xml_Buffer::TAG_NAME) == 0)
				{
					$name = $this->getXmlName();
				}
				elseif($this->bufferSize(Opt_Xml_Buffer::TAG_NAME) == 1)
				{
					$name = $this->buildCode(Opt_Xml_Buffer::TAG_NAME);
				}
				else
				{
					throw new Opt_CompilerCodeBufferConflict_Exception(1, 'TAG_NAME', $this->getXmlName());
				}
				if(!$this->hasChildren() && $this->bufferSize(Opt_Xml_Buffer::TAG_CONTENT) == 0 && $this->get('single'))
				{
					$compiler->appendOutput('<'.$name.$this->_linkAttributes().' />'.$this->buildCode(Opt_Xml_Buffer::TAG_SINGLE_AFTER,Opt_Xml_Buffer::TAG_AFTER));
				}
				else
				{
					$compiler->appendOutput('<'.$name.$this->_linkAttributes().'>'.$this->buildCode(Opt_Xml_Buffer::TAG_OPENING_AFTER));
					$this->set('_name', $name);
					if($this->bufferSize(Opt_Xml_Buffer::TAG_CONTENT) > 0)
					{
						$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, Opt_Xml_Buffer::TAG_CONTENT, Opt_Xml_Buffer::TAG_CONTENT_AFTER));
					}
					elseif($this->hasChildren())
					{
						$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_CONTENT_BEFORE));
						$compiler->setChildren($this);
					}
				}
			}
		} // end preLink();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking, after linking the child nodes.
		 */
		public function postLink(Opt_Compiler_Class $compiler)
		{
			if($compiler->isNamespace($this->getNamespace()))
			{
				if($this->get('single'))
				{
					$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_SINGLE_AFTER, Opt_Xml_Buffer::TAG_AFTER));
				}
				else
				{
					$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CLOSING_BEFORE,
						Opt_Xml_Buffer::TAG_CLOSING_AFTER, Opt_Xml_Buffer::TAG_AFTER));
				}
			}
			elseif($this->hasChildren() || $this->bufferSize(Opt_Xml_Buffer::TAG_CONTENT) != 0 || !$this->get('single'))
			{
				$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CLOSING_BEFORE).'</'.$this->get('_name').'>'.$this->buildCode(Opt_Xml_Buffer::TAG_CLOSING_AFTER, Opt_Xml_Buffer::TAG_AFTER));
				$this->set('_name', NULL);
			}
		} // end postLink();

		/**
		 * Links the element attributes into a valid XML code and returns
		 * the output code.
		 *
		 * @internal
		 * @param Opt_Xml_Element $subitem The XML element.
		 * @return String
		 */
		protected function _linkAttributes()
		{
			// Links the attributes into the PHP code
			if($this->hasAttributes() || $this->bufferSize(Opt_Xml_Buffer::TAG_BEGINNING_ATTRIBUTES) > 0 || $this->bufferSize(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES) > 0)
			{

				$code = $this->buildCode(Opt_Xml_Buffer::TAG_ATTRIBUTES_BEFORE, Opt_Xml_Buffer::TAG_BEGINNING_ATTRIBUTES);
				$attrList = $this->getAttributes();
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
							throw new Opt_CompilerCodeBufferConflict_Exception(1, 'ATTRIBUTE_NAME', $this->getXmlName());
					}

					if($attribute->bufferSize(Opt_Xml_Buffer::ATTRIBUTE_VALUE) == 0)
					{
						// Static value
						$tpl = Opl_Registry::get('opt');
						if(!($tpl->htmlAttributes && $attribute->getValue() == $attribute->getName()))
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
				return $code.$this->buildCode(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES, Opt_Xml_Buffer::TAG_ATTRIBUTES_AFTER);
			}
			return '';
		} // end _linkAttributes();
	} // end Opt_Xml_Element;
