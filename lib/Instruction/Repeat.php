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
 * Processes the opt:repeat instruction.
 * @package Instructions
 * @subpackage Control
 */
class Opt_Instruction_Repeat extends Opt_Instruction_Loop
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'repeat';

	/**
	 * The opt:repeat nesting level used to generate unique variable names.
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
		$this->_addInstructions('opt:repeat');
	} // end configure();

	/**
	 * Migrates the opt:repeat node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

	/**
	 * Processes the opt:root node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$params = array(
			'times' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
			'separator' => $this->getSeparatorConfig()
		);
		$this->_extractAttributes($node, $params);
		$this->_nesting++;

		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' for($__r'.$this->_nesting.' = 0; $__r'.$this->_nesting.' < '.$params['times'].'; $__r'.$this->_nesting.'++){ ');
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');

		$this->processSeparator('$__rs'.$this->_nesting, $params['separator'], $node);

		$node->set('postprocess', true);
		$this->_process($node);
	} // end processNode();

	/**
	 * Finishes the processing of the opt:root node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function postprocessNode(Opt_Xml_Node $node)
	{
		$this->_nesting--;
	} // end postprocessNode();

	/**
	 * A hook to the $system special variable. Returns the
	 * compiled PHP code for the call.
	 * 
	 * @internal
	 * @param array $namespace The namespace to parse
	 * @return string
	 */
	public function processSystemVar($namespace)
	{
		if(!isset($namespace[2]))
		{
			$namespace[2] = 'counter';
		}
		switch($namespace[2])
		{
			case 'counter':
				return '$__r'.$this->_nesting;
			case 'order':
				return '$__r'.$this->_nesting.'+1';
		}
	} // end processSystemVar();
} // end Opt_Instruction_Repeat;