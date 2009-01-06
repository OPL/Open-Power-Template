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
 * $Id: Attribute.php 18 2008-10-29 21:23:43Z zyxist $
 */

 /*
  * XML attribute implementation for OPT.
  */

	class Opt_Xml_Attribute extends Opt_Xml_Buffer
	{
		protected $_namespace;
		protected $_name;
		protected $_value;
		
		public function __construct($name, $value)
		{
			$this->setName($name);
			$this->_value = $value;
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
		
		public function setValue($value)
		{
			$this->_value = $value;
		} // end setValue();
		
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
			else
			{
				return $this->_namespace.':'.$this->_name;
			}
		} // end getXmlName();
		
		public function getValue()
		{
			return $this->_value;
		} // end getValue();
		
		public function __toString()
		{
			return $this->_value;
		} // end __toString();		
	} // end Opt_Xml_Attribute;
