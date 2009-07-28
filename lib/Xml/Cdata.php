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

 /*
  * Character data node implementation.
  */

	class Opt_Xml_Cdata extends Opt_Xml_Node
	{
		private $_text = '';
		static public $mode;

		public function __construct($cdata)
		{
			parent::__construct();
			self::$mode != Opt_Class::QUIRKS_MODE and $this->_validate($cdata);
			$this->_text = $cdata;
		} // end __construct();

		public function appendData($cdata)
		{
			self::$mode != Opt_Class::QUIRKS_MODE and $this->_validate($cdata);
			$this->_text .= $cdata;
		} // end appendData();
		
		public function insertData($offset, $cdata)
		{
			$this->_text = substr($this->_text, 0, $offset).$cdata.substr($this->_text, $offset, strlen($this->_text)-$offset);
		} // end insertData();
		
		public function deleteData($offset, $count)
		{
			$this->_text = substr($this->_text, 0, $offset).substr($this->_text, $offset+$count, strlen($this->_text)-$offset-$count);
		} // end insertData();
		
		public function replaceData($offset, $count, $text)
		{
			$this->_text = substr($this->_text, 0, $offset).substr($text, 0, $count).substr($this->_text, $offset+$count, strlen($this->_text)-$offset-$count);
		} // end replaceData();
		
		public function substringData($offset, $count)
		{
			return substr($this->_text, $offset, $count);
		} // end substringData();
		
		public function length()
		{
			return strlen($this->_text);		
		} // end length();

		public function __toString()
		{
			return $this->_text;
		} // end __toString();

		protected function _validate(&$text)
		{
			return true;
			if($this->get('cdata'))
			{
				return true;
			}
			if(strcspn($text, '<>') != strlen($text))
			{
				throw new Opt_XmlInvalidCharacter_Exception(htmlspecialchars($text));
			}
			return true;
		} // end _validate();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking.
		 */
		public function preLink(Opt_Compiler_Class $compiler)
		{
			if($this->get('cdata'))
			{
				$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE).'<![CDATA['.$this.']]>'.$this->buildCode(Opt_Xml_Buffer::TAG_AFTER));
				return;
			}
			$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE));
			$tpl = Opl_Registry::get('opt');
			// We strip the white spaces at the linking level.
			if($tpl->stripWhitespaces)
			{
				// The CDATA composed of white characters only is reduced to a single space.
				if(ctype_space((string)$this))
				{
					if($wasElement)
					{
						$output .= ' ';
					}
				}
				else
				{
					// In the opposite case reduce all the groups of the white characters
					// to single spaces in the text.
					if($this->get('noEntitize') === true)
					{
						$compiler->appendOutput(preg_replace('/\s\s+/', ' ', (string)$this));
					}
					else
					{
						$compiler->appendOutput($compiler->parseSpecialChars(preg_replace('/(\s){1,}/', ' ', (string)$this)));
					}
				}
			}
			else
			{
				$compiler->appendOutput($this->get('noEntitize') ? (string)$this : $compiler->parseSpecialChars($this));
			}

			$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_AFTER));
		//	$this->_closeComments($item, $output);
		} // end preLink();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking, after linking the child nodes.
		 */
		public function postLink(Opt_Compiler_Class $compiler)
		{

		} // end postLink();
	} // end Opt_Xml_Cdata;