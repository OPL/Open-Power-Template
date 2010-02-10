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
 * $Id: Class.php 294 2010-02-09 17:36:21Z zyxist $
 */

/**
 * A helper structure representing a single context. It is implemented
 * primarily for the data formats and variables which must be calculated
 * using dynamic, strong typing.
 */
class Opt_Compiler_Context implements Opt_Cdf_Locator_Interface
{
	const TEMPLATE_CTX = 'template';
	const PROCEDURE_CTX = 'procedure';

	/**
	 * The compiler object.
	 * @var Opt_Compiler_Class
	 */
	private $_compiler;

	/**
	 * The context name for the data format locators.
	 * @var string
	 */
	private $_contextName;

	/**
	 * The context type for the data format locators.
	 * @var integer
	 */
	private $_contextType;

	/**
	 * The list of variables currently used in this context.
	 * @var array
	 */
	private $_variables;

	/**
	 * Cached context buffer.
	 * @var array
	 */
	private $_buffer;

	/**
	 * Creates a new context object.
	 *
	 * @param Opt_Compiler_Class $compiler The compiler.
	 * @param integer $contextType The context type.
	 * @param string $contextName The context name.
	 */
	public function __construct(Opt_Compiler_Class $compiler, $contextType, $contextName)
	{
		$this->_compiler = $compiler;
		$this->_contextType = $contextType;
		$this->_contextName = $contextName;
	} // end __construct();

	/**
	 * Returns the location of the specified element within the template
	 * context for the data formats.
	 *
	 * @param string $elementType The element type the location we want to know.
	 * @param string $id The element ID the location we want to know.
	 * @return array
	 */
	public function getElementLocation($elementType, $id)
	{
		if($this->_buffer === null)
		{
			$stack = $this->_compiler->getContextStack();
			$stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
			$this->_buffer = array();
			foreach($stack as $ctx)
			{
				assert($ctx instanceof Opt_Compiler_Context);
				$this->_buffer[] = $ctx->_contextType.'#'.$ctx->_contextName;
			}
		}
		return $this->_buffer;
	} // end getElementLocation();

	/**
	 * Uses the specified variable within the context.
	 *
	 * @param string $variable The variable name.
	 * @param boolean $isGlobal Is the variable global or local?
	 * @param string $contextFormat The format enforced by the occurence context.
	 * @return array
	 */
	public function useVariable($variable, $isGlobal, $contextFormat = null)
	{
		if(!isset($this->_variables[$variable]))
		{
			// In this case the variable has not been used yet. We must check
			// if the user have not selected any format for it, calculate the
			// default format and modify the context format.
			$manager = $this->_compiler->getCdfManager();

			try
			{
				$this->_variables[$variable] = $manager->getFormat('variable', $variable, $this);
				return array(
					'format' => $this->_variables[$variable],
					'replacement' => null,
					'cast' => null
				);
			}
			catch(Opt_NoMatchingFormat_Exception $exception)
			{
				if($contextFormat === null)
				{
					// This is very strange. It seems that someone has used
					// an uninitialized variable, so we can set it to null and
					// report it as an unused variable.

					Opt_Support::warning('Uninitialized variable '.$variable.' - casting to NULL');

					return array(
						'format' => null,
						'replacement' => 'null',
					);
				}
				else
				{
					$manager->addFormat('variable', $variable, $contextFormat, $this->getElementLocation('variable', $variable));
					$this->_variables[$variable] = $manager->getFormat('variable', $variable, $this);

					return array(
						'format' => $this->_variables[$variable],
						'replacement' => null,
						'cast' => null
					);
				}
			}
		}
		else
		{
			// The variable has already been used. We must match the
			// previously selected format.
			if($contextFormat !== null)
			{
				return array(
					'format' => $this->_variables[$variable],
					'replacement' => null,
					'cast' => $this->_variables[$variable]->getName()
				);
			}
			else
			{
				return array('format' => $this->_variables[$variable], 'replacement' => null);
			}
		}
	} // end useVariable();

	/**
	 * Ensures that the garbage collector will eat this object. We assume
	 * that the object will not be used anymore.
	 */
	public function dispose()
	{
		$this->_compiler = null;
		$this->_variables = null;
		$this->_contextType = null;
		$this->_contextName = null;
	} // end dispose();
} // end Opt_Compiler_Context;