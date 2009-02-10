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
 * $Id: Element.php 18 2008-10-29 21:23:43Z zyxist $
 */

	class Opt_Xml_Element extends Opt_Xml_Scannable
	{
		protected $_name;
		protected $_namespace;
		protected $_attributes;
		
		public function __construct($name)
		{
			$this->setName($name);
		} // end __construct();
		
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
		
		public function setNamespace($namespace)
		{
			$this->_namespace = $namespace;
		} // end setNamespace();
		
		public function getName()
		{
			return $this->_name;
		} // end getName();
		
		public function getNamespace()
		{
			return $this->_namespace;
		} // end getNamespace();
		
		public function getXmlName()
		{
			if(is_null($this->_namespace))
			{
				return $this->_name;
			}
			return $this->_namespace.':'.$this->_name;
		} // end getXmlName();
		
		public function getAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return array();
			}
			return $this->_attributes;
		} // end getAttributes();

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
		
		public function addAttribute(Opt_Xml_Attribute $attribute)
		{
			if(!is_array($this->_attributes))
			{
				$this->_attributes = array();
			}
			$this->_attributes[$attribute->getXmlName()] = $attribute;
		} // end addAttribute();
		
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

		public function removeAttributes()
		{
			$this->_attributes = array();
		} // end removeAttributes();
		
		public function hasAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return false;
			}
			return (sizeof($this->_attributes) > 0);
		} // end hasAttributes();
		
		public function __toString()
		{
			return $this->getXmlName();
		} // end __toString();

		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() != 'Opt_Xml_Element' && $node->getType() != 'Opt_Xml_Text')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Element', $node->getType());
			}
		} // end _testNode();
	} // end Opt_Xml_Element;
