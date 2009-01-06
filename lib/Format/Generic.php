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
 * $Id: Generic.php 19 2008-11-20 16:09:45Z zyxist $
 */

 // A generic format class for OPT
 
	class Opt_Format_Generic extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section', 'variable', 'item'		
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
		/*
		//	'sectionGetSubitem' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i][\'%_sectionItemName%\']',
			'sectionTopRecordInit' => '$_sect%sectionNest%_vals = & %sectionRecordCall%;',
			'sectionSubitemReference' => '&',
			'sectionStartAscLoop' => 'for($_sect%sectionNest%_i = 0; $_sect%sectionNest%_i < $_sect%sectionNest%_cnt; $_sect%sectionNest%_i++){ ',
			'sectionStartDescLoop' => 'for($_sect%sectionNest%_i = $_sect%sectionNest%_cnt-1; $_sect%sectionNest%_i >= 0; $_sect%sectionNest%_i--){ ',
			'sectionEndLoop' => ' } ',
			'sectionRecordCount' => '($_sect%sectionNest%_cnt = sizeof($_sect%sectionNest%_vals)) > 0',
			'sectionOptCount' => '$_sect%sectionNest%_cnt',
			'sectionOptIterator' => '$_sect%sectionNest%_i',
			'sectionItemRead' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i]',

			'sectionOptSize' => 'sizeof($_sect%sectionNest%_vals[$_sect%sectionNest%_i])',
			'sectionOptFirstAsc' => '$_sect%sectionNest%_i == 0',
			'sectionOptFirstDesc' => '$_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
			'sectionOptLastDesc' => '$_sect%sectionNest%_i == 0',
			'sectionOptLastAsc' => '$_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
			'sectionOptFar' => '$_sect%sectionNest%_i == 0 || $_sect%sectionNest%_i == $_sect%sectionNest%_cnt-1',
		
			'sectionInitIterator' => '$_sect%sectionNest%_i = 0; ',
			'sectionNext' => '$_sect%sectionNest%_i++; ',
			'sectionValid' => 'isset($_sect%sectionNest%_vals[$_sect%sectionNest%_i])',		
		*/
			'_itemVariable' => '%sectionItemRead%[\'%_sectionItemName%\']',
			'_itemFullVariable' => '$_sect%sectionNest%_vals[$_sect%sectionNest%_i][\'%_sectionItemName%\']',

			'variableSubitem' => '[\'%item%\']'
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
				case 'sectionRecordCall':
					if($this->_getVar('datasource'))
					{
						return $this->_getVar('parentRecord');
					}
					
					$this->assign('item', $this->_getVar('sectionName'));
					$cnt = $this->_getVar('sectionNest');
					$code = $this->get('variableMain');
					for($i = 1; $i < $cnt; $i++)
					{
						$code .= '[$_sect'.$i.'_i]';
					}
					return $this->_codeBlocks['sectionRecordCall'] = $code;
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

	} // end Opt_Format_Generic;
