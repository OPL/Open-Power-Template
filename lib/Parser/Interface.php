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
 * The interface for writing parsers.
 *
 * @package Interfaces
 * @subpackage Compiler
 */
interface Opt_Parser_Interface
{

	/**
	 * The compiler uses this method to send itself to the parser.
	 *
	 * @param Opt_Compiler_Class $compiler The compiler object
	 */
	public function setCompiler(Opt_Compiler_Class $compiler);

	/**
	 * The role of this method is to parse the specified code and
	 * return the XML tree to the compiler.
	 *
	 * @param String $filename The file name (for debug purposes)
	 * @param String &$code The code to parse
	 * @return Opt_Xml_Root
	 */
	public function parse($filename, &$code);
} // end Opt_Parser_Interface;