<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
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

	class Opt_Instruction_Tag extends Opt_Compiler_Processor
	{
		protected $_name = 'tag';
		
		public function configure()
		{
			$this->_addInstructions('opt:tag');
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'name' => array(0 => self::REQUIRED, self::EXPRESSION),
				'ns' => array(0 => self::OPTIONAL, self::EXPRESSION, null),
				'single' => array(0 => self::OPTIONAL, self::BOOL, false)		
			);
			$this->_extractAttributes($node, $params);
		
			// Remove these nodes
			$node->removeAttribute('name');
			$node->removeAttribute('ns');
			$node->removeAttribute('single');

			if(is_null($params['ns']))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_NAME, ' echo '.$params['name'].'; ');
			}
			else
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_NAME, ' $_ns = '.$params['ns'].'; echo (!empty($_ns) ? $_ns.\':\' : \'\').'.$params['name'].'; ');
			}
			
			if($params['single'] == true)
			{
				$node->set('single', true);
				
			}
			$node->set('postprocess', true);
			$this->_process($node);
		} // end processNode();

		public function postprocessNode(Opt_Xml_Node $node)
		{
			if($node->get('single'))
			{
				$node->removeChildren();
			}
			$node->setNamespace(null);
			$node->setName('__default__');
		} // end postprocessNode();
	} // end Opt_Instruction_Tag;