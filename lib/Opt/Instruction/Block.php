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
 * The processor for opt:block instruction. Note that compiler
 * DEPENDS on this processor, using its API in order to provide the
 * support for the statically deployed blocks.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 * @subpackage Components
 */
class Opt_Instruction_Block extends Opt_Instruction_Abstract
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'block';
	/**
	 * The opt:block counter used to generate unique variable names.
	 * @internal
	 * @var integer
	 */
	protected $_unique = 0;

	/**
	 * The component call stack used by processSystemVar() to determine which
	 * component the call refers to.
	 * @internal
	 * @var SplStack
	 */
	protected $_stack;

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions('opt:block');
		$this->_stack = new SplStack;
	} // end configure();

	/**
	 * Migrates the opt:block node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

	/**
	 * Processes the opt:block node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$node->set('block', true);
		// Undefined block processing
		$params = array(
			'from' => array(self::REQUIRED, self::EXPRESSION, null),
			'id' => array(self::OPTIONAL, self::STRING, null),
			'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
		);
		$vars = $this->_extractAttributes($node, $params);

		$format = $this->_compiler->getFormat('block#'.$params['id'], false, $this->_tpl->blockFormat);
		$format->assign('variable', $params['from']);
		$this->_stack->push(array($params['from'], $format));

		$mainCode = ' if(is_object('.$params['from'].') && '.$format->get('block:valid').'){ '.$format->get('block:init');
		$mainCode .= $this->_commonProcessing($node, $params['from'], $vars, $format);

		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('block:done').' } ');
		$node->set('postprocess', true);
	} // end processNode();

	/**
	 * Finishes the processing of the opt:block node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function postprocessNode(Opt_Xml_Node $node)
	{
		$this->_stack->pop();
	} // end postprocessNode();

	/**
	 * This method implements the publicly available code that generates
	 * a block support within an XML tag. By default, it is used by
	 * the compiler to support statically deployed blocks.
	 *
	 * @param Opt_Xml_Element $node The component tag
	 */
	public function processBlock(Opt_Xml_Element $node)
	{
		// Defined block processing
		$params = array(
			'id' => array(self::OPTIONAL, self::STRING, null),
			'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
		);

		$vars = $this->_extractAttributes($node, $params);

		// Initialize block structures
		$cn = '$_block_'.($this->_unique++);
		$format = $this->_compiler->getFormat('block#'.$params['id'], false, $this->_tpl->blockFormat);
		$format->assign('variable', $cn);
		$this->_stack->push(array($cn, $format));

		// Generate the initialization code
		$format->assign('className', $this->_compiler->block($node->getXmlName()));
		$mainCode = $format->get('block:build').$format->get('block:init');

		$this->_commonProcessing($node, $cn, $vars, $format);
		$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
		$node->addAfter(Opt_Xml_Buffer::TAG_AFTER,  $format->get('block:done'));
	} // end processBlock();

	/**
	 * Finishes the public processing of the block.
	 *
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function postprocessBlock(Opt_Xml_Node $node)
	{
		$this->_stack->pop();
	} // end postprocessBlock();

	/**
	 * The common processing part of the dynamically and statically
	 * deployed components. Returns the compiled PHP code ready to
	 * be appended to the XML tag. The caller must generate a component
	 * variable name that will be used in the generated code to refer
	 * to the component object. Furthermore, he must pass the returned results
	 * of _extractAttributes() method.
	 *
	 * @internal
	 * @param Opt_Xml_Element $node The node with the component data.
	 * @param string $blockVariable The PHP block variable name.
	 * @param array $args The array of custom block attributes.
	 * @return string
	 */
	private function _commonProcessing(Opt_Xml_Element $node, $blockVariable, array $args, Opt_Format_Abstract $format)
	{
		// Common part of the component processing
		$format->assign('arguments', $args);
		if($node->get('single'))
		{
			$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, $format->get('block:on-single'));
		}
		else
		{
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $format->get('block:on-open'));
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $format->get('block:on-close'));
		}

		$this->_process($node);
	} // end _commonProcessing();

	/**
	 * A hook to the $system special variable. Returns the
	 * compiled PHP code for the call.
	 *
	 * @internal
	 * @param array $namespace The namespace to parse
	 * @return string
	 */
	public function processSystemVar($opt)
	{
		if($this->_stack->count() == 0)
		{
			throw new Opt_Instruction_Exception('opt:block error: cannot process $'.implode('.',$opt).': no blocks active.');
		}
		list($variable, $format) = $this->_stack->top();
		$format->assign('name', $opt[2]);
		return $format->get('block:get');
	} // end processSystemVar();
} // end Opt_Instruction_Block;