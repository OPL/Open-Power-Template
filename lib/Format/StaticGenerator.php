<?php
/*
 *  OPEN POWER LIBS <http://libs.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: generic.php 15 2008-10-05 19:11:42Z zyxist $
 */

 // A format class that generates the necessary section content on the fly
 // The generation is obligatory, contrary to "RuntimeGenerator". 
 
	class Opt_Format_StaticGenerator extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section'		
		);
		
		private $_codeBlocks = array(
			'sectionInit' => ' $_sect%sectionNest%_vals = array(); if(%sectionRecordCall% instanceof Opt_Generator_Interface){ $_sect%sectionNest%_vals = %sectionRecordCall%->generate(\'%sectionName%\'); }',
		);

		protected function _build($hookName)
		{
			if(isset($this->_codeBlocks[$hookName]))
			{
				return $this->_codeBlocks[$hookName];
			}
		} // end _build();

	} // end Opt_Format_StaticGenerator;
