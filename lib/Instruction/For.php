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
 * The processor for opt:for instruction.
 *
 * @package Instructions
 * @subpackage Control
 */
class Opt_Instruction_For extends Opt_Instruction_Loop
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'for';
	/**
	 * The opt:for nesting level used to generate unique variable names.
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
		$this->_addInstructions(array('opt:for'));
	} // end configure();

	/**
	 * Migrates the opt:for node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

	/**
	 * Processes the opt:for node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$params = array(
			'begin' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
			'while' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
			'iterate' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
			'separator' => $this->getSeparatorConfig()
		);
		$this->_extractAttributes($node, $params);
		$this->_nesting++;

		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' for('.$params['begin'].'; '.$params['while'].'; '.$params['iterate'].'){ ');
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');

		$this->processSeparator('$__for'.$this->_nesting, $params['separator'], $node);

		$node->set('postprocess', true);
		$this->_process($node);
	} // end processNode();

	/**
	 * Finishes the processing of the opt:for node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function postprocessNode(Opt_Xml_Node $node)
	{
		$this->_nesting--;
	} // end postprocessNode();
} // end Opt_Instruction_For;