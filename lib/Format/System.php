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
 * The class implementing the $system special variable calls.
 *
 * @package Formats
 */
class Opt_Format_System extends Opt_Compiler_Format
{
	/**
	 * The list of supported types.
	 * @var array
	 */
	protected $_supports = array(
		'variable'
	);

	/**
	 * The data format properties.
	 * @var array
	 */
	protected $_properties = array(
		'variable:capture' => true,
		'variable:capture.dynamic' => true,
		'variable:capture.assign' => true,
		'variable:capture.preincrement' => true,
		'variable:capture.postincrement' => true,
		'variable:capture.predecrement' => true,
		'variable:capture.postdecrement' => true
	);

	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param String $hookName The hook name
	 * @return String The output PHP code
	 */
	protected function _build($hookName)
	{
		// Extract the extra name.
		$pos = strpos($hookName, '.');

		if($pos === false)
		{
			$type = 'default';
			$extra = null;
		}
		else
		{
			$type = substr($hookName, $pos, strlen($hookName) - $pos);
			$extra = $this->_getVar('code');
		}

		// Select the processor.
		$namespace = $this->_getVar('items');

		if(!is_string($namespace[1]))
		{
			// TODO: Error reporting!
			throw new Exception('Boo boo');
		}

		$processor = $this->_compiler->getProcessor($namespace[1]);
		return $processor->processSystemVar($namespace, $this->_getVar('dynamic'), $type, $extra);
	} // end _build();
} // end Opt_Format_System;
