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
	 * @internal
	 * @param Opt_Node $node The node.
	 */
	protected function _processSwitch(Opt_Node $node)
	{
		$equalsContainer = array();
		$containsContainer = array();

		// Collect the subnodes with the recursion to detect the opt:equals and opt:contains tags.
		$stack = new SplStack;
		$stack->push($node);
		$node->rewind();
		$nextAction = 0;
		while($stack->size() != 0)
		{
			// This is a finite state machine
			switch($nextAction)
			{
				// Process the element
				case 0:
					$element = $stack->top();

					// Top-level element
					if($stack->size() == 1)
					{
						// TODO: Only "opt:equals" and "opt:contains" possible.
						$nextAction = 3;
					}
					// Nested elements
					else
					{
						switch($this->_detectCase($element))
						{
							// Ignore the deeper content
							case 'opt:switch':
								$nextAction = 2;
								break;
							// Push some blocks to the "equals" container
							case 'opt:equals':
								$nextAction = 2;
								if($element->hasChildren())
								{
									$equalsContainer[] = $this->_constructIb($element);
									$lastContainer = &$equalsContainer;
									$nextAction = 3;
								}				
								break;

							// Push some blocks to the "contains" container.
							case 'opt:contains':
								$nextAction = 2;
								if($element->hasChildren())
								{
									$containsContainer[] = $this->_constructIb($element);
									$lastContainer = &$containsContainer;
									$nextAction = 3;
								}
								break;
							default:
								$nextAction = 2;
								if($element->hasChildren())
								{
									$nextAction = 3;
								}
						}
					}
					break;
				// Move to the next element
				case 1:
					$element = $stack->top();
					$found = false;
					while($element->valid())
					{
						$element->next();
						$item = $element->current();
						if($item instanceof Opt_Xml_Element)
						{
							if(($type = $this->_detectCase($item)) === null)
							{
								// TODO: Exception here!
								die('Error');
							}

							$element->push($item);

							$nextAction = 0;
							$found = true;
							break;
						}
					}
					if($found === false)
					{
						$nextAction = 2;
					}
					break;
				// Jump out.
				case 2:
					$element = $stack->pop();
					$type = $this->_detectCase($element);
					if($type == 'opt:equals')
					{
						$equalsContainer[] = $this->_constructEb($element);
					}
					elseif($type == 'opt:contains')
					{
						$containsContainer[] = $this->_constructEb($element);
					}
					$nextAction = 1;
					break;
				// Jump in to the first element
				case 3:
					$element = $stack->top();
					$element->rewind();
					$nextAction = 1;
					$jumpIn = true;
					break;
			}
		}

		foreach($equalsContainer as $element)
		{
			echo $element[0].'<br/>';
		}
		foreach($containsContainer as $element)
		{
			echo $element[0].'<br/>';
		}
	} // end _processSwitch();

	/**
	 * Recognizes the case tag in the switch. Returns the recognized tag name
	 * or NULL.
	 *
	 * @param Opt_Xml_Element $element The element to test
	 * @return string|null
	 */
	protected function _detectCase(Opt_Xml_Element $element)
	{
		switch($element->getXmlName())
		{
			case 'opt:equals':
				return 'opt:equals';
			case 'opt:contains':
				return 'opt:contains';
			default:
				return null;
		}
	} // end _detectCase();

	/**
	 * Constructs the "Initialization block" for the containers.
	 *
	 * @param Opt_Xml_Element $tag The element.
	 * @return SplFixedArray
	 */
	protected function _constructIb($tag)
	{
		$object = new SplFixedArray(2);
		$object[0] = 'ib';
		$object[1] = $tag;

		return $object;
	} // end _constructIb();

	/**
	 * Constructs the "Initialization block" for the containers.
	 *
	 * @param Opt_Xml_Element $tag The element.
	 * @return SplFixedArray
	 */
	protected function _constructEb($tag)
	{
		$object = new SplFixedArray(2);
		$object[0] = 'eb';
		$object[1] = $tag;

		return $object;
	} // end _constructEb();

} // end Opt_Instruction_Switch;
