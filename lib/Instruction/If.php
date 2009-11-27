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

/**
 * The processor for opt:if instruction.
 *
 * @package Instructions
 * @subpackage Control
 */
class Opt_Instruction_If extends Opt_Compiler_Processor
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'if';
	/**
	 * The opt:if occurence counter used to generate unique variable names.
	 * @internal
	 * @var integer
	 */
	protected $_cnt = 0;

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:if', 'opt:elseif', 'opt:else'));
		$this->_addAttributes(array('opt:if', 'opt:on'));
	} // end configure();

	/**
	 * Processes the opt:if node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$params = array(
			'test' => array(0 => self::REQUIRED, self::EXPRESSION)
		);

		switch($node->getName())
		{
			case 'if':
				$this->_extractAttributes($node, $params);
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' if('.$params['test'].'){ ');
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, ' } ');
				$node->sort(array('*' => 0, 'opt:elseif' => 1, 'opt:else' => 2));
				$this->_process($node);
				break;
			case 'elseif':
				if($node->getParent()->getName() == 'if')
				{
					$this->_extractAttributes($node, $params);
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } elseif('.$params['test'].'){ ');
					$this->_process($node);
				}
				else
				{
					throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:if');
				}
				break;
			case 'else':
				if($node->getParent()->getName() == 'if')
				{
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, '}else{ ');
					$this->_process($node);
				}
				else
				{
					throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:if');
				}
				break;
		}
	} // end processNode();

	/**
	 * Processes the opt:if and opt:omit-tag attributes.
	 * @internal
	 * @param Opt_Xml_Node $node The node with the attribute
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 */
	public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		// TODO: Add opt:omit-tag implementation
		switch($attr->getName())
		{
			case 'on':
				if(!$this->_compiler->isNamespace($node->getNamespace()))
				{
					$expr = $this->_compiler->compileExpression((string)$attr, false, Opt_Compiler_Class::ESCAPE_OFF);

					$node->addBefore(Opt_Xml_Buffer::TAG_OPENING_BEFORE, ' $_tag_'.$this->_cnt.' = false; if('.$expr[0].'){ $_tag_'.$this->_cnt.' = true; ');
					$node->addAfter(Opt_Xml_Buffer::TAG_OPENING_AFTER, ' } ');
					$node->addBefore(Opt_Xml_Buffer::TAG_CLOSING_BEFORE, ' if($_tag_'.$this->_cnt.' === true){ ');
					$node->addAfter(Opt_Xml_Buffer::TAG_CLOSING_AFTER, ' } ');
					$this->_cnt++;
					break;
				}
			case 'if':
				// opt:if added to an section must be handled differently.
				// Wait for the section processor and add the condition in the postprocessing.
				if($this->_compiler->isInstruction($node->getXmlName()) instanceof Opt_Instruction_BaseSection)
				{
					$attr->set('postprocess', true);
					return;
				}

				$expr = $this->_compiler->compileExpression((string)$attr, false, Opt_Compiler_Class::ESCAPE_OFF);

				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' if('.$expr[0].'){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
		}
	} // end processAttribute();

	/**
	 * Finalizes the processing of the opt:if and opt:omit-tag attributes.
	 * @internal
	 * @param Opt_Xml_Node $node The node with the attribute
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 */
	public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		$expr = $this->_compiler->compileExpression((string)$attr, false, Opt_Compiler_Class::ESCAPE_OFF);

		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' if('.$expr[0].'){ ');
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
	} // end postprocessAttribute();
} // end Opt_Instruction_If;
