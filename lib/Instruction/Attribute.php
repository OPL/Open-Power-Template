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
 * $Id: Attribute.php 19 2008-11-20 16:09:45Z zyxist $
 */

	class Opt_Instruction_Attribute extends Opt_Compiler_Processor
	{
		protected $_name = 'attribute';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:attribute'));
			$this->_addAttributes(array('opt:single'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			static $cnt;
			
			if(!isset($cnt))
			{
				$cnt = 0;
			}
			
			$params = array(
				'name' => array(0 => self::REQUIRED, self::EXPRESSION),
				'value' => array(0 => self::REQUIRED, self::EXPRESSION)		
			);
			$this->_extractAttributes($node, $params);

			$parent = $node->getParent();

			// This is a bit tricky optimization. If the name is constant, there is no need to process it as a variable name.
			// If the name is constant, the result must contain only a string
			$trName = trim($params['name'], '\' ');
			if(substr_count($params['name'], '\'') == 2 && substr_count($trName, '\'') == 0 && $this->_compiler->isIdentifier($trName))
			{
				$attribute = new Opt_Xml_Attribute($trName, $params['value']);
				$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$params['value'].'; ');
				
			/*	// Integration with "opt:tag"
				if($parent->getXmlName() == 'opt:tag' && ($trName == 'name' || $trName == 'single' || $trName == 'ns'))
				{
					$attribute->setName('__xattr_'.$trName);
				}*/
			}
			else
			{
				$attribute = new Opt_Xml_Attribute('__xattr_'.$cnt, $params['value']);
				$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_NAME, 'echo '.$params['name'].'; ');
				$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$params['value'].'; ');
			}
			$node->set('attr', $attribute);
			$node->set('postprocess', true);
			
			$parent->addAttribute($attribute);
			$parent->removeChild($node);
		} // end processNode();

		public function postprocessNode(Opt_Xml_Node $node)
		{
			// We must copy the buffers here, because the instruction might have some attributes
			// which may also use "postprocess" to generate their code. Here we are sure they've completed
			// their work.

			$attribute = $node->get('attr');
			$attribute->copyBuffer($node, Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::ATTRIBUTE_BEGIN);
			$attribute->copyBuffer($node, Opt_Xml_Buffer::TAG_AFTER, Opt_Xml_Buffer::ATTRIBUTE_END);
		} // end postprocessNode();

		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			if($this->_compiler->isNamespace($node->getNamespace()))
			{
				throw new Opt_AttributeInvalidNamespace_Exception($node->getXmlName());
			}
			if($attr->getValue() == 'yes')
			{
				$attr->set('postprocess', true);
			}
		} // end processAttribute();

		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			if($attr->getValue() == 'yes')
			{
				$node->set('single', true);
				$node->removeChildren();
			}
		} // end processAttribute();
	} // end Opt_Instruction_Attribute;