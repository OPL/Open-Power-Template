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
 * The class for output system errors.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opt_Output_Exception extends Opt_Exception
{
	/**
	 * The output name.
	 * @var string
	 */
	private $_outputName;

	/**
	 * Creates a new output exception. The output system should specify
	 * its name in the second argument.
	 *
	 * @param string $message The error message.
	 * @param string $output The name of the output system.
	 */
	public function __construct($message, $output)
	{
		$this->message = (string)$message;
		$this->_outputName = (string)$output;
	} // end __construct();

	/**
	 * Returns the name of the output that caused the exception.
	 *
	 * @return string
	 */
	public function getOutputName()
	{
		return $this->_outputName;
	} // end getOutputName();
} // end Opt_Output_Exception;