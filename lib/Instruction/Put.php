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
 * Processes the opt:put instruction.
 * @package Instructions
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
	 * Array contains deprecated attributes.
	 * @var array
	 */
	protected $_deprecatedAttributes = array();

	/**
	 * Array contains deprecated instructions.
	 * @var array
	 */
	protected $_deprecatedInstructions = array();

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:put'));
		$this->_addAttributes(array('opt:content'));
		if($this->_tpl->backwardCompatibility)
		{
			$this->_addAttributes($this->_deprecatedAttributes);
			$this->_addInstructions($this->_deprecatedInstructions);
		}
	} // end configure();

	/**
	 * Migrates the opt:put node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

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
		/*switch($attr->getName())
		{
			// null
		}*/
		return $attr;
	} // end migrateAttribute();

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