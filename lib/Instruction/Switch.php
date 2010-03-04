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
 * Processes the Switch instruction.
 * @package Instruction
 * @subpackage Control
 */
class Opt_Instruction_Switch extends Opt_Compiler_Processor
{
	/**
	 * The processor name.
	 * @var string
	 */
	protected $_name = 'switch';

	/**
	 * Configures the instruction processor.
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:switch'));
	//	$this->_addAttributes(array('opt:switch'));
	} // end configure();

	/**
	 * Migrates the opt:switch node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function _migrateSwitch(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end _migrateSwitch();

	/**
	 * Processes the opt:switch tag.
	 *
	 * @param Opt_Node $node The node.
	 */
	protected function _processSwitch(Opt_Node $node)
	{
		// TODO: Write
	} // end _processSwitch();

} // end Opt_Instruction_Switch;
