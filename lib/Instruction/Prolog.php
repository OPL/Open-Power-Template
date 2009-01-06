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
 * $Id: Prolog.php 20 2008-11-22 09:37:35Z zyxist $
 */

	class Opt_Instruction_Prolog extends Opt_Compiler_Processor
	{
		protected $_name = 'prolog';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:prolog'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'version' => array(0 => self::OPTIONAL, self::STRING, null),
				'encoding' => array(0 => self::OPTIONAL, self::STRING, null),
				'standalone' => array(0 => self::OPTIONAL, self::STRING, null)
			);
			$this->_extractAttributes($node, $params);	
			
			$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, ' echo \'<\'.\'?xml version="'.
				(is_null($params['version']) ? '1.0' : '\'.'.$params['version'].'.\'').
			'" encoding="\'.'.
				(is_null($params['encoding']) ? 'strtoupper($this->_tpl->charset)' : $params['encoding']).
			'.\'" standalone="'.
				(is_null($params['standalone']) ? 'no' : '\'.'.$params['standalone'].'.\'').
			'" ?\'.\'>\'."\r\n"; ');
		} // end processNode();
	} // end Opt_Instruction_Prolog;