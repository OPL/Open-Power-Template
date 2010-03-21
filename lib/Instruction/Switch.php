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
	 * The handler priority.
	 * @var array
	 */
	private $_priority = array();

	/**
	 * Is the processor initialized?
	 * @var boolean
	 */
	private $_initialized = false;

	/**
	 * The data for the sort() method.
	 * @var array
	 */
	private $_sort = array();

	/**
	 * Reverse group information
	 * @var array
	 */
	private $_reverseGroup = array();

	/**
	 * Configures the instruction processor.
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:switch'));

		$this->addSwitchable('opt:switch');
		$this->addSwitchHandler('opt:equals', $this->_compiler->createFormat(null, 'SwitchEquals'), 500);
		$this->addSwitchHandler('opt:contains', $this->_compiler->createFormat(null, 'SwitchContains'), 1000);
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
	final public function addSwitchable($tagName)
	{
		$this->_switchable[$tagName] = true;
	} // end addSwitchable();

	/**
	 * Registers a new switch handler which is able to process various conditions etc.
	 * The switch cases are handled by ordinary data formats and thus, we must provide
	 * a data format object here. The data format must implement the 'switch' hook type.
	 *
	 * The last argument has two meanings. If it contains an integer number, it defines
	 * the processing priority of this handler, or in other words - whether it will appear
	 * earlier or later in the source code (lower values mean earlier occurence). If it
	 * contains string, the registered tag is grouped with another tag, appearing in its
	 * condition group and following its rules.
	 *
	 * Note that OPT neither checks nor supports grouping with tags that are themselves grouped
	 * to something other.
	 *
	 * Note that the data format must implement the 'switch' hook type.
	 *
	 * @param string $tagName The registered tag name
	 * @param Opt_Compiler_Format $dataFormat The data format to handle these requests.
	 * @param string|integer $groupInfo Group information or the priority.
	 */
	final public function addSwitchHandler($tagName, Opt_Compiler_Format $dataFormat, $groupInfo)
	{
		if(!$dataFormat->supports('switch'))
		{
			// TODO: Exception here.
			die('Error');
		}

		$obj = new SplFixedArray(2);
		$obj[0] = $dataFormat;
		$obj[1] = $groupInfo;

		$this->_handlers[$tagName] = $obj;

		if(is_integer($groupInfo))
		{
			$this->_priority[$groupInfo] = $tagName;
			$this->_reverseGroup[$tagName] = array();
		}
		else
		{
			$this->_reverseGroup[$groupInfo][] = $tagName;
		}
	} // end addSwitchHandler();

	/**
	 * Removes an existing switch handler. If the handler is not found, it throws
	 * an exception.
	 *
	 * @throws Opt_ObjectNotExists_Exception
	 * @param string $tagName The tag name registered for the handler.
	 */
	final public function removeSwitchHandler($tagName)
	{
		if($this->_initialized)
		{
			// TODO: Exception here...
			die('Error');
		}
		if(!isset($this->_handlers[(string)$tagName]))
		{
			throw new Opt_ObjectNotExists_Exception('switch handler', $tagName);
		}
		// Remove the data from some extra arrays...
		if(is_integer($this->_handlers[(string)$tagName][1]))
		{
			unset($this->_priority[$this->_handlers[(string)$tagName][1]]);
			unset($this->_reverseGroup[(string)$tagName]);
		}
		else
		{
			$id = array_search($this->_reverseGroup[$this->_handlers[(string)$tagName][1]]);
			unset($this->_reverseGroup[(string)$tagName][$id]);
		}
		unset($this->_handlers[(string)$tagName]);
	} // end removeSwitchHandler();

	/**
	 * Returns true, if the specified switch handler exists under the specified
	 * tag name.
	 *
	 * @param string $tagName The registered tag name
	 */
	final public function hasSwitchHandler($tagName)
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
	final public function createSwitch(Opt_Xml_Node $node, $test)
	{
		// Initialize the processor, if necessary
		if(!$this->_initialized)
		{
			ksort($this->_priority);

			$i = 0;
			foreach($this->_priority as $num => $handlerName)
			{
				$this->_sort[$handlerName] = $i++;
				foreach($this->_reverseGroup[$handlerName] as $subHandler)
				{
					$this->_sort[$handlerName] = $i++;
				}
			}
			$this->_sort['*'] = $i;
			unset($this->_reverseGroup);	// Won't be necessary anymore.
			$this->_initialized = true;
		}

		// Initialize the containers and sort the subnodes.
		$containers = array();
		$topNodes = array();
		$node->sort($this->_sort);

		// Collect the subnodes with the recursion to detect the opt:equals and opt:contains tags.
		$stack = new SplStack;
		$stack->push($node);
		$node->rewind();
		$nextAction = 0;
		$previous = null;
		$previousMeaningful = null;
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
							if(!isset($container[$type]))
							{
								$container[$type] = array();
								$topNodes[$type] = array();
							}
							($previous !== null && $stack->count() > 2) and $container[$type][] = $this->_constructCb($previousMeaningful);
							$container[$type][] = $this->_constructIb($element);
							$previousMeaningful = $element;
							if($stack->count() == 2)
							{
								$topNodes[$type][] = $element;
							}
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
					if($found === false)
					{
						$nextAction = 2;
					}
					break;
				// Jump out.
				case 2:
					$element = $stack->pop();
					$type = $element->get('priv:switch-type');
					if($type !== null)
					{
						$doExtra = false;
						if($this->_possibleExtraFinalCb($element))
						{
							$doExtra = true;
						}

						if(!isset($container[$type]))
						{
							$container[$type] = array();
							$topNodes[$type] = array();
						}

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
	/*	foreach($container as $type => &$level)
		{
			echo '------level: '.$type.'<br/>';
			foreach($level as $element)
			{
				echo $element[0].' ('.$element[1]->getAttribute('value').')<br/>';
			}
		}*/

		// Remove remainders
		$node->removeChildren();

		// OK, it's time for the finale! Ouuuey!!!!
		// du-du-du dum dum, du-du-du dum dum... xD
		$iteration = 0;
		$previous = null;
		foreach($this->_priority as $handlerName)
		{
			// Skip empty containers
			if(!isset($container[$handlerName]))
			{
				continue;
			}

			$format = $this->_handlers[$handlerName][0];
			$format->assign('test', $test);
			$format->assign('container', $container[$handlerName]);
			$format->action('switch:analyze');

			$typeNode = new Opt_Xml_Element($handlerName.'-type');
			$typeNode->set('hidden', false);
			foreach($topNodes[$handlerName] as $element)
			{
				$typeNode->appendChild($element);
			}

			$node->appendChild($typeNode);

			if($iteration == 0)
			{
				$typeNode->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $format->get('switch:enterTestBegin.first'));
				$typeNode->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('switch:enterTestEnd.first'));
			}
			else
			{
				$typeNode->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $format->get('switch:enterTestBegin.later'));
				$typeNode->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('switch:enterTestEnd.later'));
			}

			
			$nesting = 0;
			foreach($container[$handlerName] as &$elements)
			{
				$elements[0] == 'ib' and $nesting++;
				$elements[0] == 'eb' and $nesting--;

				switch($elements[0])
				{
					case 'ib':
			//			echo 'je visite '.$elements[1].' avec '.$elements[1]->getAttribute('value').' ('.$nesting.')<br/>';
						$params = $format->action('switch:caseAttributes');
						$this->_extractAttributes($elements[1], $params);
						$format->assign('attributes', $params);
						$format->assign('nesting', $nesting);
						$format->assign('element', $elements[1]);

						$elements[1]->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $format->get('switch:caseBefore'));
						$elements[1]->set('hidden', false);
						$this->_process($elements[1]);
						break;
					case 'eb':
			//			echo 'je quitte '.$elements[1].' avec '.$elements[1]->getAttribute('value').' ('.$nesting.')<br/>';
						$format->assign('element', $elements[1]);
						$format->assign('nesting', $nesting);
						$elements[1]->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('switch:caseAfter'));
				}
			}
			$typeNode->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $format->get('switch:testsBefore'));
			$typeNode->addBefore(Opt_Xml_Buffer::TAG_AFTER, $format->get('switch:testsAfter'));
			$format->assign('container', null);
		}
/*
		// Attempt to compile it as an ordinary PHP switch()
		if($this->_standardSwitchPossible($container))
		{
			$this->_applyStandardSwitch($node, $test, $container);
		}
		// The statements are too complex to simulate them with switch()
		// We must choose a different approach
		else
		{
			$this->_applySuperSwitch($node, $test, $container);
		}*/
	} // end createSwitch();

	/**
	 * Test if we should add some terminating Code Block to the container
	 * before we push there Ending Block.
	 *
	 * @param Opt_Xml_Element $popped The popped element
	 * @return boolean
	 */
	private function _possibleExtraFinalCb(Opt_Xml_Element $popped)
	{
		$item = $popped->getLastChild();
		if($item === null)
		{
			return false;
		}
		if($item->get('priv:switch-type') !== null)
		{
			return false;
		}
		if($item instanceof Opt_Xml_Text && $item->isWhitespace() && $item->getPrevious() !== null && $item->getPrevious()->get('priv:switch-type') !== null)
		{
			return false;
		}
		return true;
	} // end _possibleExtraFinalCb();

	/**
	 * Detects a switchable tag.
	 *
	 * @internal
	 * @param Opt_Xml_Element $element The tag to test.
	 * @return boolean
	 */
	private function _detectSwitchable(Opt_Xml_Element $element)
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
	private function _detectCase(Opt_Xml_Element $element)
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
	private function _constructIb($tag)
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
	private function _constructCb($tag)
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
	private function _constructEb($tag)
	{
		$object = new SplFixedArray(2);
		$object[0] = 'eb';
		$object[1] = $tag;

		return $object;
	} // end _constructEb();
} // end Opt_Instruction_Switch;
