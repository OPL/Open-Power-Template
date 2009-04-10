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
 * $Id: Dtd.php 19 2008-11-20 16:09:45Z zyxist $
 */

	class Opt_Instruction_Dtd extends Opt_Compiler_Processor
	{
		protected $_name = 'dtd';
		
		public function configure()
		{
			$this->_addInstructions(array(0 => 'opt:dtd'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'template' => array(0 => self::OPTIONAL, self::ID, null)
			);
			$this->_extractAttributes($node, $params);
			// TODO: Hmmm... now we have to invent, how to deal with THAT!
			if(is_null($params['template']))
			{
				$this->_compiler->processor('literal')->disableCDATA($node, true);
				$this->_process($node);
			}
			else
			{
				$node->set('nophp', true);
				$node->set('hidden', false);
				switch($params['template'])
				{
					case 'xhtml10strict':
						$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
						break;
					case 'xhtml10transitional':
						$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
						break;
					case 'xhtml10frameset':
						$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">');
						break;
					case 'xhtml11':
						$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">');
						break;
					case 'html40':
						$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
				}
			}
		} // end processNode();
	} // end Opt_Instruction_Dtd;