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
 */

/**
 * The objects of this class are used by opt:switch implementations to manage
 * the opt:prepend and opt:append runtime snippets. They collect information
 * about passed switch cases, and are able to activate the snippet on certain
 * conditions.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Runtime
 */
class Opt_Runtime_Prepender
{
	/**
	 * The internal context that should be passed to the anonymous functions.
	 * @var Opt_InternalContext
	 */
	private $_ctx;

	/**
	 * The switch case results.
	 * @var array
	 */
	private $_cases;

	/**
	 * Current passed iteration.
	 * @var integer
	 */
	private $_i = 0;

	/**
	 * Case index.
	 * @var integer
	 */
	private $_case = 1;
	
	/**
	 * The list of prepend functions.
	 * @var array
	 */
	private $_prepends = array();
	
	/**
	 * The list of append functions.
	 * @var array
	 */
	private $_appends = array();

	/**
	 * Last valid case.
	 * @var integer
	 */
	private $_lastValid = null;

	/**
	 * Constructs the prepender.
	 *
	 * @param Opt_InternalContext $ctx The internal execution context.
	 */
	public function __construct($ctx)
	{
		$this->_ctx = $ctx;
	} // end __construct();

	/**
	 * Registers a new opt:append function for the specified case.
	 *
	 * @param integer|string $case The number of the case, or 'first', or 'last'
	 * @param Closure $function The anonymous function to run.
	 */
	public function registerAppend($case, $function)
	{
		if(isset($this->_appends[$case]))
		{
			throw new Opt_Runtime_Exception('Cannot register another opt:append block for #'.$case.' case.');
		}
		$this->_appends[$case] = $function;
	} // end registerAppend();

	/**
	 * Registers a new opt:prepend function for the specified case.
	 *
	 * @param integer|string $case The number of the case, or 'first', or 'last'
	 * @param Closure $function The anonymous function to run.
	 */
	public function registerPrepend($case, $function)
	{
		if(isset($this->_prepends[$case]))
		{
			throw new Opt_Runtime_Exception('Cannot register another opt:prepend block for #'.$case.' case.');
		}
		$this->_prepends[$case] = $function;
	} // end registerPrepend();

	/**
	 * Registers a new case result. The cases should be provided in the order they
	 * will be tested, as the method enumerates them automatically.
	 *
	 * @param boolean $result The case result.
	 */
	public function setCaseResult($result)
	{
		$this->_cases[$this->_case] = (boolean)$result;

		if($result == true)
		{
			$this->_lastValid = $this->_case;
		}

		$this->_case++;
	} // end setCaseResult();

	/**
	 * Returns true, if the specified case had a satisfactory condition.
	 * In addition, runs all the anonymous function registered for this
	 * position. The result of this method can be used to determine whether
	 * to enter the code block or not.
	 *
	 * @return boolean
	 */
	public function isPassed($caseNum)
	{
		if($this->_cases[$caseNum])
		{
			$this->_i++;
			if($this->_i == 1 && isset($this->_prepends['first']))
			{
				$this->_run($this->_prepends['first']);
			}
			if($caseNum == $this->_lastValid && isset($this->_prepends['last']))
			{
				$this->_run($this->_prepends['last']);
			}
			if(isset($this->_prepends[$this->_i]))
			{
				$this->_run($this->_prepends[$this->_i]);
			}
			return true;
		}
		return false;
	} // end isPassed();

	/**
	 * This method should be called at the end of the specified case together with
	 * the case number. It allows to run the functions whose content should be
	 * appended to the output content of the case.
	 *
	 * @param integer $caseNum The case number
	 */
	public function endPassing($caseNum)
	{
		if($this->_cases[$caseNum])
		{
			$this->_i++;
			if($this->_i == 0 && isset($this->_appends['first']))
			{
				$this->_run($this->_appends['first']);
			}
			if($caseNum == $this->_lastValid && isset($this->_appends['last']))
			{
				$this->_run($this->_appends['last']);
			}
			if(isset($this->_appends[$this->_i]))
			{
				$this->_run($this->_appends[$this->_i]);
			}
		}
	} // end endPassing();

	/**
	 * Executes an anonymous function.
	 *
	 * @param Closure $function The anonymous function to run.
	 */
	private function _run($function)
	{
		$function($this->_ctx);
	} // end _run();
} // end Opt_Runtime_Prepender;