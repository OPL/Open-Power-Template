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
 * $Id: HEADER 10 2008-08-23 13:38:25Z extremo $
 */

// Objective format for OPT

	class Opt_Format_Objective extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section', 'variable'		
		);
		
		private $_codeBlocks = array(
			// Initializes the data for the section.
			'sectionInit' => '$_sect%sectionNest%_vals = %reference% %sectionRecordCall%;',
			// Checks if there are data for this section.
			'sectionCondition' => 'is_object($_sect%sectionNest%_vals) && $_sect%sectionNest%_vals instanceof Countable && $_sect%sectionNest%_vals instanceof Iterator && $_sect%sectionNest%_vals->count() > 0',
			// Generates the loop that iterates over the data forward.
			'sectionStartAscLoop' => 'foreach($_sect%sectionNest%_vals as $_sect%sectionNest%_i => $_sect%sectionNest%_val){ ',
			// Generates the loop that iterates over the data backward.
			// TODO: Add descending order!
			'sectionStartDescLoop' => 'foreach($_sect%sectionNest%_vals as $_sect%sectionNest%_i => $_sect%sectionNest%_val){ ',
			// Finishes the loop block.
			'sectionEndLoop' => ' } ',
			// Rewinds the iterator to the first element
			'sectionRewind' => '$_sect%sectionNest%_vals->rewind(); ',
			// Moves the iterator to the next element
			'sectionNext' => '$_sect%sectionNest%_vals->next(); ',
			// Checks if the iterator points to a valid record.
			'sectionValid' => '$_sect%sectionNest%_vals->valid()',
			// Retrieves the current record.
			'sectionCurrent' => '$_sect%sectionNest%_val',
			// Retrieves the current record by the explicite call.
			'sectionCurrentExp' => '$_sect%sectionNest%_vals->current()',
			// Returns the current iterator:
			'sectionIterator' => '$_sect%sectionNest%_i',
			// Counts the section elements
			'sectionCount' => '$_sect%sectionNest%_vals->count()',
			// Counts the section elements
			'sectionSize' => '$_sect%sectionNest%_val->count()',

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
		
			'_itemVariable' => '%sectionItemRead%->%_sectionItemName%',
			'_itemFullVariable' => '$_sect%sectionNest%_val->%_sectionItemName%',
		
			'variableSubitem' => '->%item%'
		);

		protected function _build($hookName)
		{
			if(isset($this->_codeBlocks[$hookName]))
			{
				return $this->_codeBlocks[$hookName];
			}
			switch($hookName)
			{
				case 'itemVariable':
					return $this->_decorateHook(
						$this->_codeBlocks['_itemVariable'],
						'sectionItemRead',
						$this->_codeBlocks['_itemFullVariable']
					);
				case 'variableMain':
					$this->_applyVars = false;
					$item = $this->_getVar('item');
					if($this->_getVar('access') == Opt_Class::ACCESS_LOCAL)
					{
						return '$this->_data[\''.$item.'\']';
					}
					else
					{
						return 'self::$_global[\''.$item.'\']';
					}
				default:
					return NULL;
			}
		} // end _build();
	} // end Opt_Format_Objective;
