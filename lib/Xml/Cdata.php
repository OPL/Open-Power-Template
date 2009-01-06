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
 * $Id: Cdata.php 18 2008-10-29 21:23:43Z zyxist $
 */

 /*
  * Character data node implementation.
  */

	class Opt_Xml_Cdata extends Opt_Xml_Node
	{
		private $_text = '';
		
		public function __construct($cdata)
		{
			$this->_text = $cdata;
		} // end __construct();
		
		public function appendData($cdata)
		{
			$this->_text .= $cdata;
		} // end appendData();
		
		public function insertData($offset, $cdata)
		{
			// TODO: Write
		} // end insertData();
		
		public function deleteData($offset, $count)
		{
			// TODO: Write
		} // end insertData();
		
		public function replaceData($offset, $count, $text)
		{
			// TODO: Write
		} // end replaceData();
		
		public function substringData($offset, $count)
		{
			// TODO: Write
		} // end substringData();
		
		public function length()
		{
			return strlen($this->_text);		
		} // end length();

		public function __toString()
		{
			return $this->_text;
		} // end __toString();
	} // end Opt_Xml_Cdata;