<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
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

 /*
  * A node representing an expression in brackets: { and  }.
  */

	class Opt_Xml_Expression extends Opt_Xml_Node
	{
		private $_expression;
		
		public function __construct($expression)
		{
			parent::__construct();
			$this->_expression = $expression;
		} // end __construct();
		
		public function getExpression()
		{
			return $this->_expression;
		} // end getExpression();
		
		public function setExpression($expression)
		{
			$this->_expression = $expression;
		} // end setExpression();
		
		public function __toString()
		{
			return $this->_expression;
		} // end __toString();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking.
		 */
		public function preLink(Opt_Compiler_Class $compiler)
		{
			$compiler->appendOutput($this->buildCode(Opt_Xml_Buffer::TAG_BEFORE));
		//	$this->_closeComments($item, $output);
		} // end preLink();

		/**
		 * This function is executed by the compiler during the third compilation stage,
		 * linking, after linking the child nodes.
		 */
		public function postLink(Opt_Compiler_Class $compiler)
		{

		} // end postLink();
	} // end Opt_Xml_Expression;
