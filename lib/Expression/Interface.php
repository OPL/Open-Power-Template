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
 * $Id: Class.php 155 2009-07-18 07:25:11Z zyxist $
 */

	interface Opt_Expression_Interface
	{
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