<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
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
 */

	class Opt_Instruction_Dynamic extends Opt_Compiler_Processor
	{
		protected $_name = 'dynamic';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:dynamic'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, '$this->_outputBuffer[] = ob_get_contents();');
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' ob_start(); ');
			$node->set('dynamic', true);
			
			$this->_process($node);
		} // end processNode();
	} // end Opt_Instruction_Dynamic;