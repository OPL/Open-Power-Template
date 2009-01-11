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
 * $Id$
 * 
 */

	class Opt_Xml_Prolog
	{
		private $_attributes;

		public function __construct($attributes = null)
		{
			if(is_null($attributes))
			{
				$this->_attributes = array(
					'version' => '1.0',
					'standalone' => 'yes'
				);
			}
			elseif(is_array($attributes))
			{
				foreach($attributes as $name => $value)
				{
					$this->setAttribute($name, $value);
				}
			}
		} // end __construct();

		public function setAttribute($name, $value)
		{
			if($name == 'version' || $name == 'standalone' || $name = 'encoding')
			{
				$this->_attributes[$name] = $value;
			}
		} // end setAttribute();

		public function getAttribute($name)
		{
			if(!isset($this->_attributes[$name]))
			{
				return NULL;
			}
			return $this->_attributes[$name];
		} // end getAttribute();

		public function getAttributes()
		{
			return $this->_attributes;
		} // end getAttributes();

		public function getProlog()
		{
			$code = '<?xml ';
			foreach($this->_attributes as $name => $value)
			{
				$code .= $name.'="'.$value.'" ';
			}
			return $code.'?>';
		} // end getProlog();
	} // end Opt_Xml_Prolog;