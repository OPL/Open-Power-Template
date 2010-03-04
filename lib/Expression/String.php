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
 * $Id: String.php 290 2010-01-29 09:29:02Z zyxist $
 */

/**
 * A simple expression engine that treats the input as a string.
 *
 * @package Expressions
 */
class Opt_Expression_String implements Opt_Expression_Interface
{
	/**
	 * The compiler instance.
	 *
	 * @var Opt_Compiler_Class
	 */
	protected $_compiler;

	/**
	 * Sets the compiler instance in the expression parser.
	 *
	 * @param Opt_Compiler_Class $compiler The compiler object
	 */
	public function setCompiler(Opt_Compiler_Class $compiler)
	{
		$this->_compiler = $compiler;
	} // end setCompiler();

	/**
	 * Parses the source expressions to the PHP code.
	 *
	 * @param String $expression The expression source
	 * @return Array
	 */
	public function parse($expression)
	{
		$expression = '\''.addslashes($expression).'\'';
		return array('bare' => $expression, 'escaped' => $expression, 'type' => Opt_Expression_Interface::SCALAR);
	} // end parse();
} // end Opt_Expression_String;