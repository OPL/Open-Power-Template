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
 * $Id: Interface.php 290 2010-01-29 09:29:02Z zyxist $
 */

/**
 * An interface for writing expression engines
 * 
 * @package Interfaces
 * @subpackage Compiler
 */
interface Opt_Expression_Interface
{
	// The expression engine control values
	const SINGLE_VAR = 0;
	const ASSIGNMENT = 1;
	const SCALAR = 2;
	const COMPOUND = 3;

	/**
	 * The compiler uses this method to send itself to the expression engine.
	 *
	 * @param Opt_Compiler_Class $compiler The compiler object
	 */
	public function setCompiler(Opt_Compiler_Class $compiler);

	/**
	 * The role of this method is to parse the expression to the
	 * corresponding PHP code.
	 *
	 * @param String $expression The expression source
	 * @return Array
	 */
	public function parse($expression);
} // end Opt_Expression_Interface;