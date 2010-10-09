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
	 * @abstract
	 */
	abstract class Opt_Instruction_Loop extends Opt_Compiler_Processor
	{
		const ATTRIBUTE_FORM = true;

		/**
		 * Adds the "separator" tag and attribute support to the loop instructions.
		 * It finds the necessary items in the XML tree and compiles them properly.
		 * Note that the algorithm used by "separator", does not require any special
		 * information and stuff from the instruction.
		 *
		 * @param String $varname The variable name used by the separator PHP code to detect the first element
		 * @param String $arg If this argument is not NULL, it should contain the value from the "separator" attribute in the instruction tag.
		 * @param Opt_Xml_Scannable $node The loop node.
		 * @param boolean $attributeForm Optimize for the attribute form?
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
					throw new Opt_InstructionTooManyItems_Exception('opt:separator', $node->getXmlName(), 'Zero or one');
			}
		} // end processSeparator();

		/**
		 * Returns the "separator" attribute configuration for _extractAttributes()
		 * @return Array
		 */
		public function getSeparatorConfig()
		{
			return array(self::OPTIONAL, self::EXPRESSION, NULL);
		} // end getSeparatorConfig();
		
	} // end Opt_Instruction_Loop;