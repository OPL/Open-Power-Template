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
	 * Array contains deprecated attributes.
	 * @var array
	 */
	protected $_deprecatedAttributes = array('opt:on');

	/**
	 * Array contains deprecated instructions.
	 * @var array
	 */
	protected $_deprecatedInstructions = array('opt:elseif');

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:if', 'opt:else-if', 'opt:else'));
		$this->_addAttributes(array('opt:if', 'opt:omit-tag'));
		if($this->_tpl->backwardCompatibility)
		{
			$this->_addAttributes($this->_deprecatedAttributes);
			$this->_addInstructions($this->_deprecatedInstructions);
		}
	} // end configure();

	/**
	 * Migrates the opt:if node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		switch($node->getName())
		{
			case 'elseif':
				$node->setName('else-if');
				break;
		}
		$this->_process($node);
	} // end migrateNode();

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
			case 'else-if':
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
	 * Checks if attribute is deprecated and needs migration.
	 * @param Opt_Xml_Attribute $attr Attribute to migrate
	 * @return boolean If attribute needs migration
	 */
	public function attributeNeedMigration(Opt_Xml_Attribute $attr)
	{
		$name = $attr->getXmlName();
		if(in_array($name, $this->_deprecatedAttributes))
		{
			return true;
		}
		return false;
	} // end attributeNeedMigration();

	/**
	 * Migrates the opt:if (and its derivatives) attributes.
	 * @internal
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 * @return Opt_Xml_Attribute Migrated attribute
	 */
	public function migrateAttribute(Opt_Xml_Attribute $attr)
	{
		switch($attr->getName())
		{
			case 'on':
				$attr->setName('omit-tag');
				break;
		}
		return $attr;
	} // end migrateAttribute();

	/**
	 * Processes the opt:if and opt:omit-tag attributes.
	 * @internal
	 * @param Opt_Xml_Node $node The node with the attribute
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 */
	public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		// TODO: Add opt:omit-tag implementation, changed opt:on->opt:omit-tag, it should work as before
		switch($attr->getName())
		{
			case 'omit-tag':
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
