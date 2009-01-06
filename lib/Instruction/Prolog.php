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
			
			$root = $node;
			while(is_object($tmp = $root->getParent()))
			{
				$root = $tmp;
			}

			$root->setProlog(new Opt_Xml_Prolog($params));
		} // end processNode();
	} // end Opt_Instruction_Prolog;