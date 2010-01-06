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
 * Processes the opt:capture instruction.
 * @package Instructions
 */
class Opt_Instruction_Capture extends Opt_Compiler_Processor
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'capture';

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions('opt:capture');
		$this->_addAttributes('opt:capture');
	} // end configure();

	/**
	 * Migrates the opt:capture node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

	/**
	 * Processes the opt:capture node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$params = array(
			'as' => array(0 => self::REQUIRED, self::ID)
		);
		$this->_extractAttributes($node, $params);
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'ob_start(); ');
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, 'self::$_capture[\''.$params['as'].'\'] = ob_get_clean();');
		$this->_process($node);
	} // end processNode();

	/**
	 * Processes the opt:capture attribute.
	 * @internal
	 * @param Opt_Xml_Node $node The node with the attribute
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 */
	public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		if($this->_compiler->isIdentifier($attr->getValue()))
		{
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'ob_start(); ');
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, 'self::$_capture[\''.$attr->getValue().'\'] = ob_get_clean();');
			$this->_process($node);
		}
		else
		{
			throw new Opt_InvalidAttributeType_Exception('opt:capture', 'identifier');
		}
	} // end processAttribute();

	/**
	 * A hook to the $system special variable. Returns the
	 * compiled PHP code for the call. In this case, it
	 * allows a simple access to the captured codes.
	 *
	 * @internal
	 * @param array $namespace The namespace to parse
	 * @return string
	 */
	public function processSystemVar($opt)
	{
		return 'self::$_capture[\''.$opt[2].'\']';
	} // end processSystemVar();
} // end Opt_Instruction_Capture;
