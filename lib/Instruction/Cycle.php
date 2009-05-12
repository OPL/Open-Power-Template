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

	class Opt_Instruction_Cycle extends Opt_Compiler_Processor
	{
		protected $_name = 'cycle';
		
		public function configure()
		{
			$this->_addInstructions('opt:cycle');
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'name' => array(0 => self::REQUIRED, self::ID),
				'__UNKNOWN__' => array(0 => self::OPTIONAL, self::STRING),
			);
			$values = $this->_extractAttributes($node, $params);
			
			// Error control
			if(sizeof($values) == 0)
			{
				throw new Opt_CycleNoValues_Exception($params['name']);
			}
			
			// Code generation
			$code = '$_cc'.$params['name'].'_values = array(';
			foreach($values as $value)
			{
				$code .= $value.', ';
			}
			$code .= '); $_cc'.$params['name'].'_i = 0; $_cc'.$params['name'].'_s = '.sizeof($values).'; ';
			
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $code);
			$this->_process($node);
		} // end processNode();
		
		public function processSystemVar($opt)
		{
			if(sizeof($opt) != 4)
			{
				throw new Opt_SysVariableLength_Exception('$'.implode('.', $opt), (sizeof($opt) < 4 ? 'short' : 'long'));
			}
			
			switch($opt[3])
			{
				case 'current':
					return '$_cc'.$opt[2].'_values[$_cc'.$opt[2].'_i]';
				case 'next':
					return '$_cc'.$opt[2].'_values[$_cc'.$opt[2].'_i = ($_cc'.$opt[2].'_i + 1) % $_cc'.$opt[2].'_s]';					
				default:
					throw new Opt_SysVariableUnknown_Exception('$'.implode('.', $opt));
			}
		} // end processSystemVar();
	} // end Opt_Instruction_Cycle;