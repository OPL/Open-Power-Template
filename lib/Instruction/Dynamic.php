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
 * The processor for opt:dynamic instruction. It encapsulates
 * a compiler feature in a convenient form. Note that the particular
 * caching system must support the dynamic code snippet feature
 * in order to make this instruction actually useful.
 *
 * @package Instructions
 * @subpackage Cache
 */
class Opt_Instruction_Dynamic extends Opt_Compiler_Processor
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'dynamic';

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:dynamic'));
	} // end configure();

	/**
	 * Processes the opt:dynamic node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		// Add capturing the content for the caching purposes
		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, '$this->_outputBuffer[] = ob_get_contents();');
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' ob_start(); ');

		// Inform the compiler to store this piece of compiled
		// template in an extra file, where it will be accessible
		// to the caching system.
		$node->set('dynamic', true);

		$this->_process($node);
	} // end processNode();
} // end Opt_Instruction_Dynamic;