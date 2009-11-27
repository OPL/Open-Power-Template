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
 * The processor for opt:foreach instruction.
 *
 * @package Instructions
 * @subpackage Control
 */
class Opt_Instruction_Foreach extends Opt_Instruction_Loop
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'foreach';
	/**
	 * The opt:foreach nesting level used to generate unique variable names.
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
		$this->_addInstructions(array('opt:foreach', 'opt:foreachelse'));
	} // end configure();

	/**
	 * Processes the opt:foreach node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		switch($node->getName())
		{
			case 'foreach':
				$params = array(
					'array' => array(0 => self::REQUIRED, self::EXPRESSION),
					'value' => array(0 => self::REQUIRED, self::ID),
					'index' => array(0 => self::OPTIONAL, self::ID, null),
					'separator' => array(0 => self::OPTIONAL, self::EXPRESSION, null)
				);

				$this->_extractAttributes($node, $params);
				$this->_nesting++;

				$node->sort(array('*' => 0, 'opt:foreachelse' => 1));
				$list = $node->getElementsByTagNameNS('opt', 'foreachelse', false);

				$codeBegin = ' foreach('.$params['array'].' as '.(!is_null($params['index']) ? '$__fe'.$this->_nesting.'_idx => ' : '').'$__fe'.$this->_nesting.'_val){ ';
				switch(sizeof($list))
				{
					case 0:
						break;
					case 1:
						$codeBegin = 'if(sizeof('.$params['array'].') > 0){ '.$codeBegin;
						break;
					default:
						throw new Opt_InstructionTooManyItems_Exception('opt:foreachelse', $node->getXmlName());
				}

				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $codeBegin);
				$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
				$this->_compiler->setConversion('##var_'.$params['value'], '$__fe'.$this->_nesting.'_val');
				if(!is_null($params['index']))
				{
					$this->_compiler->setConversion('##var_'.$params['index'], '$__fe'.$this->_nesting.'_idx');
				}
				$this->processSeparator('$__foreach_'.$this->_nesting, $params['separator'], $node);

				$node->set('postprocess', true);
				$this->_process($node);
				$node->set('params', $params);

				break;
			case 'foreachelse':
				if($node->getParent()->getName() != 'foreach')
				{
					throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:foreach');
				}
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, '} } else { ');
				$this->_process($node);
				break;
		}
	} // end processNode();

	/**
	 * Finishes the processing of the opt:foreach node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function postprocessNode(Opt_Xml_Node $node)
	{
		$params = $node->get('params');
		$this->_compiler->unsetConversion('##var_'.$params['value']);
		$this->_compiler->unsetConversion('##var_'.$params['index']);
		$this->_nesting--;
	} // end postprocessNode();
} // end Opt_Instruction_Foreach;