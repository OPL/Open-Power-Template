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

	class Opt_Instruction_For extends Opt_Instruction_Loop
	{
		protected $_name = 'for';
		protected $_nesting = 0;
		
		public function configure()
		{
			$this->_addInstructions(array('opt:for'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'begin' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'while' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'iterate' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'separator' => $this->getSeparatorConfig()
			);
			$this->_extractAttributes($node, $params);
			$this->_nesting++;
			
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' for('.$params['begin'].'; '.$params['while'].'; '.$params['iterate'].'){ ');
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			
			$this->processSeparator('$__for'.$this->_nesting, $params['separator'], $node);
			
			$node->set('postprocess', true);
			$this->_process($node);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$this->_nesting--;
		} // end postprocessNode();
	} // end Opt_Instruction_For;