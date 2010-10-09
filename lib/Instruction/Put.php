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
 */

	/**
	 * Processes the opt:put instruction.
	 *
	 * @author Tomasz Jędrzejewski
	 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
	 * @license http://www.invenzzia.org/license/new-bsd New BSD License
	 */
	class Opt_Instruction_Put extends Opt_Compiler_Processor
	{
		/**
		 * The instruction processor name - required by the instruction API.
		 * @internal
		 * @var string
		 */
		protected $_name = 'put';
		/**
		 * The opt:content nesting level used to generate unique variable names.
		 * @internal
		 * @var integer
		 */
		protected $_nesting = 0;

		/**
		 * Configures the instruction processor, registering the tags and
		 * attributes.
		 * @internal
		 */
		public function configure()
		{
			$this->_addInstructions(array('opt:put'));
			$this->_addAttributes(array('opt:content'));
		} // end configure();

		/**
		 * Processes the opt:put node.
		 * @internal
		 * @param Opt_Xml_Node $node The recognized node.
		 */
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'value' => array(0 => self::REQUIRED, self::ASSIGN_EXPR)		
			);
			$this->_extractAttributes($node, $params);

			$node->set('single', false);
			$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' echo '.$params['value'].'; ');
		} // end processNode();

		/**
		 * Processes the opt:content attribute.
		 * @internal
		 * @param Opt_Xml_Node $node The node with the attribute
		 * @param Opt_Xml_Attribute $attr The recognized attribute.
		 */
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$result = $this->_compiler->compileExpression($attr->getValue(), false, Opt_Compiler_Class::ESCAPE_BOTH);
			if($result[2] == true)
			{
				// The expression is a single variable that can be handled in a simple way.
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'if(empty('.$result[3].')){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, '} else { echo '.$result[0].'; } ');
			}
			else
			{
				// In more complex expressions, we store the result to a temporary variable.
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' $_cont'.$this->_nesting.' = '.$result[0].'; if(empty($_cont'.$this->_nesting.')){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, '} else { echo $_cont'.$this->_nesting.'; } ');
			}
			$this->_nesting++;
			$attr->set('postprocess', true);
		} // end processAttribute();

		/**
		 * Finishes the processing of the opt:content attribute.
		 * @internal
		 * @param Opt_Xml_Node $node The node with the attribute
		 * @param Opt_Xml_Attribute $attr The recognized attribute.
		 */
		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$this->_nesting--;
		} // end postprocessAttribute();
	} // end Opt_Instruction_Put;