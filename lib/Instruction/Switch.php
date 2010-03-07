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
	 * The registered switch handlers
	 * @var array
	 */
	private $_handlers = array();

	/**
	 * The switchable tags
	 * @var array
	 */
	private $_switchable = array('opt:switch' => true);

	/**
	 * Configures the instruction processor.
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:switch'));

		$this->_handlers['opt:equals'] = array($this, '_handleEquals');
		$this->_handlers['opt:contains'] = array($this, '_handleContains');
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
	 * @param Opt_Xml_Node $node The node.
	 */
	protected function _processSwitch(Opt_Xml_Node $node)
	{
		$params = array(
			'test' => array(0 => self::REQUIRED, self::EXPRESSION, null, 'parse'),
		);
		$this->_extractAttributes($node, $params);

		$this->createSwitch($node, $params['test']);
	} // end _processSwitch();

	/**
	 * Adds a tag that is recognized as a beginning of a new switch.
	 * 
	 * @param string $tagName The switch tag name
	 */
	public function addSwitchable($tagName)
	{
		$this->_switchable[$tagName] = true;
	} // end addSwitchable();

	/**
	 * Registers a new switch handler which is able to process various conditions etc.
	 *
	 * @param string $tagName The registered tag name
	 * @param callback $handler The tag name handler callback
	 */
	public function addSwitchHandler($tagName, $handler)
	{

	} // end addSwitchHandler();

	/**
	 * Removes an existing switch handler. If the handler is not found, it throws
	 * an exception.
	 *
	 * @throws Opt_ObjectNotExists_Exception
	 * @param string $tagName The tag name registered for the handler.
	 */
	public function removeSwitchHandler($tagName)
	{
		if(!isset($this->_handlers[(string)$tagName]))
		{
			throw new Opt_ObjectNotExists_Exception('switch handler', $tagName);
		}
		unset($this->_handlers[(string)$tagName]);
	} // end removeSwitchHandler();

	/**
	 * Returns true, if the specified switch handler exists under the specified
	 * tag name.
	 *
	 * @param string $tagName The registered tag name
	 */
	public function hasSwitchHandler($tagName)
	{
		return isset($this->_handlers[(string)$tagName]);
	} // end hasSwitchHandler();

	/**
	 * Compiles the specified tag contents as a programmable switch statement.
	 * The programmer may define his own actions and requirements for node
	 * compilation.
	 *
	 * @param Opt_Xml_Node $node The root node that acts as a switch.
	 * @param string $test The test condition.
	 */
	public function createSwitch(Opt_Xml_Node $node, $test)
	{
		// Initialize the containers
		$containers = array();
		foreach($this->_handlers as $handler => $callback)
		{
			$containers[$handler] = array();
		}

		// Collect the subnodes with the recursion to detect the opt:equals and opt:contains tags.
		$stack = new SplStack;
		$stack->push($node);
		$node->rewind();
		$nextAction = 0;
		$previous = null;
		$cbConstruct = array();
		while($stack->count() != 0)
		{
			// This is a finite state machine
			switch($nextAction)
			{
				// Process the element
				case 0:
					$element = $stack->top();

					// Top-level element
					if($stack->count() == 1)
					{
						$nextAction = 3;
					}
					// Nested elements
					elseif($this->_detectSwitchable($element))
					{
						$nextAction = 2;
					}
					// Switch cases
					elseif(($type = $element->get('priv:switch-type')) !== null)
					{
						$nextAction = 2;
						if($element->hasChildren())
						{
							($previous !== null && $stack->count() > 2) and $container[$type][] = $this->_constructCb($element);
							$container[$type][] = $this->_constructIb($element);
							$lastContainer = $type;
							$nextAction = 3;
						}						
					}
					// Others
					else
					{
						$nextAction = 2;
						if($element->hasChildren())
						{
							$nextAction = 3;
						}
					}
					break;
				// Move to the next element
				case 1:
					$element = $stack->top();
					if(!$jumpIn)
					{
						$element->next();
					}
					$jumpIn = false;
					$found = false;
					while($element->valid())
					{
						$item = $element->current();
						if($item instanceof Opt_Xml_Element)
						{
							$type = $this->_detectCase($item);
							if($stack->count() == 1 && $type === null)
							{
								// TODO: Exception here!
								die('Error');
							}
							$item->set('priv:switch-type', $type);
							$stack->push($item);

							$nextAction = 0;
							$found = true;
							break;
						}
						else
						{
							$previous = $item;
						}
						$element->next();
					}
					$found === false and $nextAction = 2;
					break;
				// Jump out.
				case 2:
					$element = $stack->pop();
					$type = $element->get('priv:switch-type');
					$doExtra = false;
					if($element->hasChildren() && (!($__tmp = $element->getLastChild()) instanceof Opt_Xml_Element || $__tmp->get('priv:switch-type') !== null))
					{
						$doExtra = true;
					}
					if($type !== null)
					{
						$doExtra and $container[$type][] = $this->_constructCb($element);
						$container[$type][] = $this->_constructEb($element);
					}
					$previous = $element;
					$nextAction = 1;
					break;
				// Jump in to the first element
				case 3:
					$element = $stack->top();
					$element->rewind();
					$nextAction = 1;
					$previous = null;
					$jumpIn = true;
					break;
			}
		}
		foreach($container as $type => &$level)
		{
			echo '------level: '.$type.'<br/>';
			foreach($level as $element)
			{
				echo $element[0].'<br/>';
			}
		}

		// Attempt to compile it as an ordinary PHP switch()
		if($this->_standardSwitchPossible($container))
		{
			$this->_applyStandardSwitch($node, $container);
		}
		// The statements are too complex to simulate them with switch()
		// We must choose a different approach
		else
		{

		}
	} // end createSwitch();

	/**
	 * Detects a switchable tag.
	 *
	 * @internal
	 * @param Opt_Xml_Element $element The tag to test.
	 * @return boolean
	 */
	protected function _detectSwitchable(Opt_Xml_Element $element)
	{
		if(isset($this->_switchable[$element->getXmlName()]))
		{
			return true;
		}
		return false;
	} // end _detectSwitchable();

	/**
	 * Recognizes the case tag in the switch. Returns the recognized tag name
	 * or NULL. Please note that checking the attributed forms is quite slow,
	 * so please do not use it too often, but rather cache the results for
	 * a particular tag.
	 *
	 * @internal
	 * @param Opt_Xml_Element $element The element to test
	 * @return string|null
	 */
	protected function _detectCase(Opt_Xml_Element $element)
	{
		if(isset($this->_handlers[$element->getXmlName()]))
		{
			return $element->getXmlName();
		}
		// Look for an attribute.
		else
		{
			foreach($element->getAttributes() as $attribute)
			{
				if(isset($this->_handlers[$attribute->getXmlName()]))
				{
					return $attribute->getXmlName();
				}
			}

		}
		return null;
	} // end _detectCase();

	/**
	 * Constructs the "Initialization block" for the containers.
	 *
	 * @internal
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
	 * Constructs the "Code block" for the containers.
	 *
	 * @internal
	 * @param Opt_Xml_Element $tag The element.
	 * @return SplFixedArray
	 */
	protected function _constructCb($tag)
	{
		$object = new SplFixedArray(2);
		$object[0] = 'cb';
		$object[1] = $tag;

		return $object;
	} // end _constructCb();

	/**
	 * Constructs the "Finalization block" for the containers.
	 *
	 * @internal
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

	/**
	 * Analyzes the containers to check if we can compile it as an ordinary
	 * PHP switch() statement.
	 *
	 * @internal
	 * @param array $container The reference to a container with the blocks
	 */
	protected function _standardSwitchPossible(array &$container)
	{
		// 1st condition - only opt:equals possible
		foreach($container as $name => &$elements)
		{
			if($name != 'opt:equals' && sizeof($elements) > 0)
			{
				echo 'failure on '.$name.'<br/>';
				return false;
			}
		}

		// 2nd condition - only tail nesting possible
		$state = 0;
		$nesting = 0;
		foreach($container['opt:equals'] as &$elements)
		{
			$elements[0] == 'ib' and $nesting++;
			$elements[0] == 'eb' and $nesting--;

			echo 'haben '.$elements[0].' with '.$state.': ';
			switch($state)
			{
				case 0:
					if($elements[0] == 'eb' && $nesting > 0)
					{
						echo 'state 1';
						$state = 1;
					}
					break;
				case 1:
					if($elements[0] == 'eb' || ($elements[0] == 'cb' && $elements[1] instanceof Opt_Xml_Text && $elements[1]->isWhitespace()))
					{
						echo 'state 2';
						$state = 2;
					}
					else
					{
						echo 'epic fail';
						return false;
					}
					break;
				case 2:
					if($elements[0] == 'eb' && $nesting > 0)
					{
						echo 'state 1';
						$state = 1;
					}
					elseif($nesting == 0)
					{
						echo 'state 0';
						$state = 0;
					}
					else
					{
						echo 'state epic fail';
						return false;
					}
					break;
			}
			echo '<br/>';
		}
		return true;
	} // end _standardSwitchPossible();

	/**
	 * Applies the standard switch to the instruction.
	 *
	 * @param Opt_Xml_Node $node The root node
	 * @param string $test The tested condition
	 * @param array $container The container with the code blocks
	 */
	protected function _applyStandardSwitch(Opt_Xml_Node $node, $test, array &$container)
	{
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' switch('.$test.'){ ');
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, ' } ');

		$nesting = 0;
		foreach($container['opt:equals'] as &$elements)
		{
			$elements[0] == 'ib' and $nesting++;
			$elements[0] == 'eb' and $nesting--;

			switch($elements[0])
			{
				case 'ib':
					if(!$elements[1]->hasAttribute('value'))
					{
						throw new Opt_AttributeNotDefined_Exception('value', 'opt:equals');
					}
					$result = $this->_compiler->parseExpression((string)$elements[1]->getAttribute('value'), null, Opt_Compiler_Class::ESCAPE_OFF);
					$elements[1]->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' case '.$result['bare'].': ');
					$this->_process($elements[1]);
					break;
				case 'eb':
					if($nesting == 0)
					{
						$elements[1]->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' break; ');
					}
			}
		}
	} // end _applyStandardSwitch();

} // end Opt_Instruction_Switch;
