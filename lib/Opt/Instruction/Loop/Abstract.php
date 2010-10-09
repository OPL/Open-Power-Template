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
 * This abstract class contains various tools useful for loop instructions.
 * Currently it supports separators.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 * @subpackage API
 * @abstract
 */
abstract class Opt_Instruction_Loop_Abstract extends Opt_Instruction_Abstract
{
	const ATTRIBUTE_FORM = true;

	/**
	 * Processes the loop separator. The programmer must provide the
	 * variable name that will be used to check if we need to apply
	 * the separator, and the optional value of "separator" attribute
	 * in the node attributes. The separator is added to the specified
	 * XML node.
	 *
	 * If the node contains too many opt:separator tags, an exception
	 * is thrown. The method returns the found separator element.
	 *
	 * If the method is used with an attribute form of the loop, the fourth
	 * argument should be set to Opt_Instruction_Loop_Abstract::ATTRIBUTE_FORM.
	 *
	 * @throws Opt_Instruction_Exception
	 * @param string $varname The internal variable name
	 * @param string $arg The value of "separator" attribute
	 * @param Opt_Xml_Scannable $node The node the separator will be added to.
	 * @param boolean $attributeForm Optimize for the attribute form?
	 * @return Opt_Xml_Element
	 */
	public function processSeparator($varname, $arg, Opt_Xml_Scannable $node, $attributeForm = false)
	{
		$items = $node->getElementsByTagNameNS('opt', 'separator', false);

		switch(sizeof($items))
		{
			case 1:
				// Move this node to the beginning
				if($attributeForm)
				{
					$node->removeChild($items[0]);
					$node->getParent()->insertBefore($items[0], $node);
					$items[0]->set('hidden', false);
					$this->_process($items[0]);
					$items[0]->copyBuffer($node, Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_BEFORE);
					$node->clear(Opt_Xml_Buffer::TAG_BEFORE);

					// Add PHP code
					$items[0]->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' '.$varname.' = 0;');
					$items[0]->addBefore(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'if('.$varname.' == 1){');
					$items[0]->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, '}else{ '.$varname.' = 1; }');
				}
				else
				{
					$node->removeChild($items[0]);
					$node->insertBefore($items[0], 0);
					$this->_process($items[0]);
					$items[0]->set('hidden', false);

					// Add PHP code
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' '.$varname.' = 0;');
					$items[0]->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'if('.$varname.' == 1){');
					$items[0]->addAfter(Opt_Xml_Buffer::TAG_AFTER, '}else{ '.$varname.' = 1; }');
				}
				return $items[0];
			case 0:
				if($arg !== null)
				{
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $varname.' = 0;');
					if($attributeForm)
					{
						$node->addAfter(Opt_Xml_Buffer::TAG_OPENING_BEFORE, 'if('.$varname.' == 1){ echo '.$arg.'; }else{ '.$varname.' = 1; }');
					}
					else
					{
						$node->addBefore(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'if('.$varname.' == 1){ echo '.$arg.'; }else{ '.$varname.' = 1; }');
					}
				}
				return null;
			default:
				throw new Opt_Instruction_Exception('Too many "opt:separator" elements: zero or one expected.');
		}
	} // end processSeparator();

	/**
	 * Returns the configuration of the "separator" attribute
	 * for Opt_Compiler_Instruction::_extractAttributes()
	 * @return array
	 */
	public function getSeparatorConfig()
	{
		return array(self::OPTIONAL, self::EXPRESSION, NULL);
	} // end getSeparatorConfig();
} // end Opt_Instruction_Loop_Abstract;