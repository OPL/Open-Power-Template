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
 * $Id: Tag.php 18 2008-10-29 21:23:43Z zyxist $
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
				'single' => array(0 => self::OPTIONAL, self::BOOL, false)		
			);
			$this->_extractAttributes($node, $params);
		
			// Remove these nodes
			$node->removeAttribute('name');
			$node->removeAttribute('single');
			
			// Check if "opt:attribute" tried to define us something special
			if(!is_null($attr = $node->getAttribute('__xattr_name')))
			{
				$attr->setName('name');
			}
			if(!is_null($attr = $node->getAttribute('__xattr_single')))
			{
				$attr->setName('single');
			}
			
			$node->setNamespace(NULL);
			$node->setName('__default__');
			$node->addBefore(Opt_Xml_Buffer::TAG_NAME, ' echo '.$params['name'].'; ');
			
			// TODO: Add "single" support
			
			$this->_process($node);
		} // end processNode();
	} // end Opt_Instruction_Tag;