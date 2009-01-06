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

 // The format class, where sub-sections are parts of the upper-level section array.
 
	class Opt_Format_SingleArray extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section', 'item'
		);
		
		private $_codeBlocks = array(
			// Initializes the data for the section.
			'sectionInit' => '$_sect%sectionNest%_vals = %reference% %sectionRecordCall%;',
			// Checks if there are data for this section.
			'sectionCondition' => 'is_array($_sect%sectionNest%_vals) && ($_sect%sectionNest%_cnt = sizeof($_sect%sectionNest%_vals)) > 0',
			// Generates the loop that iterates over the data forward.
			'sectionStartAscLoop' => 'for($_sect%sectionNest%_i = 0; $_sect%sectionNest%_i < $_sect%sectionNest%_cnt; $_sect%sectionNest%_i++){ ',
			// Generates the loop that iterates over the data backward.
			'sectionStartDescLoop' => 'for($_sect%sectionNest%_i = $_sect%sectionNest%_cnt-1; $_sect%sectionNest%_i >= 0; $_sect%sectionNest%_i--){ ',
			// Finishes the loop block.
			'sectionEndLoop' => ' } ',
			// Rewinds the iterator to the first element
			'sectionRewind' => '$_sect%sectionNest%_i = 0; ',
			// Moves the iterator to the next element
			'sectionNext' => '$_sect%sectionNest%_i++; ',
			// Checks if the iterator points to a valid record.
			'sectionValid' => 'isset($_sect%sectionNest%_vals[$_sect%sectionNest%_i])',
			// Retrieves the current record.
			'sectionCurrent' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i]',
			// Retrieves the current record by the explicite call. Here, not used
			'sectionCurrentExp' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i]',
			// Returns the current iterator:
			'sectionIterator' => '$_sect%sectionNest%_i',
			// Counts the section elements
			'sectionCount' => '$_sect%sectionNest%_cnt',
		
			// Tests if the iterator points to the first element:
			'sectionOptFirstAsc' => '$_sect%sectionNest%_i == 0',
			// Tests if the iterator points to the first element, when iterating backwards.
			'sectionOptFirstDesc' => '$_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
			// Tests if the iterator points to the last element, when iterating backwards.
			'sectionOptLastDesc' => '$_sect%sectionNest%_i == 0',
			// Tests if the iterator points to the first element.
			'sectionOptLastAsc' => '$_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
			// Tests if the iterator points to the first or the last element:
			'sectionOptFar' => '$_sect%sectionNest%_i == 0 || $_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
			// Returns the sectionRecordCall for the sectionInit
			'sectionRecordCall' => '%parentRecord%',
			
			'_itemVariable' => '%sectionItemRead%[\'%_sectionItemName%\']',
			'_itemFullVariable' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i][\'%_sectionItemName%\']',
		);

		protected function _build($hookName)
		{
			if(isset($this->_codeBlocks[$hookName]))
			{
				return $this->_codeBlocks[$hookName];
			}
			if($hookName == 'itemVariable')
			{
				return $this->_decorateHook(
					$this->_codeBlocks['_itemVariable'],
					'sectionItemRead',
					$this->_codeBlocks['_itemFullVariable']
				);
			}
			return NULL;
		} // end _build();

	} // end Opt_Format_SingleArray;
