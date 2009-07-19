<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
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

	class Opt_Instruction_Attribute extends Opt_Compiler_Processor
	{
		// Display the attribute values
		const ATTR_DISPLAY = 0;
		// Keep raw expressions, because they will be processed later.
		const ATTR_RAW = 1;

		protected $_name = 'attribute';

		// A counter to create unique fake attribute names
		static private $_cnt = 0;

		/**
		 * Registers opt:attribute tag and opt:single attribute.
		 */
		public function configure()
		{
			$this->_addInstructions(array('opt:attribute'));
			$this->_addAttributes(array('opt:attributes-build', 'opt:attributes-ignore'));
		} // end configure();

		/**
		 * Processes the opt:attribute instruction tag.
		 *
		 * @param Opt_Xml_Node $node XML node.
		 */
		public function processNode(Opt_Xml_Node $node)
		{			
			$params = array(
				'name' => array(0 => self::REQUIRED, self::EXPRESSION),
				'value' => array(0 => self::REQUIRED, self::EXPRESSION),
				'ns' => array(0 => self::OPTIONAL, self::EXPRESSION, null),
			);
			$this->_extractAttributes($node, $params);

			$parent = $node->getParent();
			$returnStyle = $node->get('attributeValueStyle');
			$returnStyle = (is_null($returnStyle) ? self::ATTR_DISPLAY : $returnStyle);

			if($returnStyle == self::ATTR_DISPLAY)
			{
				if(!$node->getParent() instanceof Opt_Xml_Element)
				{
					throw new Opt_InstructionInvalidParent_Exception('opt:attribute', 'printable tag');
				}
				$parentName = $node->getParent()->getXmlName();
				if(($this->_compiler->isInstruction($parentName) || $this->_compiler->isComponent($parentName) || $this->_compiler->isBlock($parentName)) && $node->getParent()->get('call:attribute-friendly') === null)
				{
					throw new Opt_InstructionInvalidParent_Exception('opt:attribute', 'printable tag');
				}

				// This is a bit tricky optimization. If the name is constant, there is no need to process it as a variable name.
				// If the name is constant, the result must contain only a string
				if($params['ns'] !== null)
				{
					$trNamespace = trim($params['ns'], '\' ');
					if(!(substr_count($params['ns'], '\'') == 2 && substr_count($trNamespace, '\'') == 0 && $this->_compiler->isIdentifier($trNamespace)))
					{
						unset($trNamespace);
					}
				}

				// Using the same tricky optimization
				$trName = trim($params['name'], '\' ');
				if(!(substr_count($params['name'], '\'') == 2 && substr_count($trName, '\'') == 0 && $this->_compiler->isIdentifier($trName)))
				{
					unset($trName);
				}

				if((isset($trName) && $params['ns'] === null) || (isset($trName) && isset($trNamespace)))
				{
					$attribute = new Opt_Xml_Attribute($trName, $params['value']);
					$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$params['value'].'; ');

					if(isset($trNamespace))
					{
						$attribute->setNamespace($trNamespace);
					}
				}
				else
				{
					$attribute = new Opt_Xml_Attribute('__xattr_'.self::$_cnt++, $params['value']);
					if(isset($trNamespace))
					{
						$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_NAME, 'echo \''.$trNamespace.':\'.'.$params['name'].'; ');
					}
					elseif($params['ns'] !== null)
					{
						$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_NAME, ' $_ns = '.$params['ns'].'; echo (!empty($_ns) ? $_ns.\':\' : \'\').'.$params['name'].'; ');
					}
					else
					{
						$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_NAME, 'echo '.$params['name'].'; ');
					}
					$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, 'echo '.$params['value'].'; ');
				}
			}
			else
			{
				// In the raw mode, we simply put the raw expressions, because they will be processed
				// later by another instruction processor.
				$attribute = new Opt_Xml_Attribute('__xattr_'.self::$_cnt++, $params['value']);
				$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_NAME, $params['name']);
				$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, $params['value']);

				if($params['ns'] !== null)
				{
					$attribute->set('priv:namespace', $params['ns']);
				}
			}
			$node->set('priv:attr', $attribute);
			$node->set('postprocess', true);

			// Add the newly created attribute to the list of dynamic attributes in the parent tag.
			// If the list does not exist, then create it.
			if(!is_null($list = $parent->get('call:attribute')))
			{
				array_push($list, $attribute);
				$parent->set('call:attribute', $list);
			}
			else
			{
				$parent->set('call:attribute', array(0 => $attribute));
			}

			$parent->addAttribute($attribute);
			$parent->removeChild($node);
		} // end processNode();

		/**
		 * Postprocesses the opt:attribute instruction tag.
		 *
		 * @param Opt_Xml_Node $node XML node.
		 */
		public function postprocessNode(Opt_Xml_Node $node)
		{
			// We must copy the buffers here, because the instruction might have some attributes
			// which may also use "postprocess" to generate their code. Here we are sure they've completed
			// their work.

			$attribute = $node->get('priv:attr');
			$attribute->copyBuffer($node, Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::ATTRIBUTE_BEGIN);
			$attribute->copyBuffer($node, Opt_Xml_Buffer::TAG_AFTER, Opt_Xml_Buffer::ATTRIBUTE_END);
			$node->set('priv:attr', null);
		} // end postprocessNode();

		/**
		 * Processes the opt:attributes-build and opt:attributes-ignore attributes.
		 * @param Opt_Xml_Element $node The node
		 * @param Opt_Xml_Attribute $attr The attribute to process
		 */
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			if($attr->getName() == 'attributes-build')
			{
				$ignoreList = $node->getAttribute('opt:attributes-ignore');
				if($ignoreList instanceof Opt_Xml_Attribute)
				{
					$ignore = $this->_compiler->compileExpression($ignoreList->getValue(), false, Opt_Compiler_Class::ESCAPE_OFF);
					$ignore = $ignore[0];
				}
				else
				{
					$ignore = 'array()';
				}
				$expression = $this->_compiler->compileExpression($attr->getValue(), false, Opt_Compiler_Class::ESCAPE_OFF);

				$node->addAfter(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES, 'echo Opt_Function::buildAttributes('.$expression[0].', '.$ignore.', \' \'); ');
			}
		} // end processAttribute();
	} // end Opt_Instruction_Attribute;
