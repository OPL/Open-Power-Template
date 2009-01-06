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
 * $Id: Repeat.php 18 2008-10-29 21:23:43Z zyxist $
 */

	class Opt_Instruction_Repeat extends Opt_Instruction_Loop
	{
		protected $_name = 'repeat';
		protected $_nesting = 0;
		
		public function configure()
		{
			$this->_addInstructions('opt:repeat');
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'times' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'separator' => $this->getSeparatorConfig()
			);
			$this->_extractAttributes($node, $params);
			$this->_nesting++;
			
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' for($__r'.$this->_nesting.' = 0; $__r'.$this->_nesting.' < '.$params['times'].'; $__r'.$this->_nesting.'++){ ');
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			
			$this->processSeparator('$__rs'.$this->_nesting, $params['separator'], $node);
			
			$node->set('postprocess', true);
			$this->_process($node);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$this->_nesting--;
		} // end postprocessNode();
		
		public function processOpt($namespace)
		{
			if(!isset($namespace[2]))
			{
				$namespace[2] = 'counter';
			}
			switch($namespace[2])
			{
				case 'counter':
					return '$__r'.$this->_nesting;
				case 'order':
					return '$__r'.$this->_nesting.'+1';
			}
		} // end processOpt();
	} // end Opt_Instruction_Repeat;