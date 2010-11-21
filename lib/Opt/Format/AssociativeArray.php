<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */

/**
 * This data format allows iteration through associative arrays.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class Opt_Format_AssociativeArray extends Opt_Format_Abstract
{
	/**
	 * The list of supported hook types.
	 * @var array
	 */
	protected $_supports = array(
		'section', 'variable', 'item'
	);

	/**
	 * Data format properties as information for the
	 * caller.
	 *
	 * @var array
	 */
	protected $_properties = array(
		'section:useReference' => true,
		'section:anyRequests' => 'ancestorNumbers',
		'variable:item.assign' => true,
		'variable:item.preincrement' => true,
		'variable:item.postincrement' => true,
		'variable:item.predecrement' => true,
		'variable:item.postdecrement' => true,
		'variable:item.exists' => true,
		'variable:useReference' => true,
		'item:item.assign' => false,
		'item:item.preincrement' => false,
		'item:item.postincrement' => false,
		'item:item.predecrement' => false,
		'item:item.postdecrement' => false,
		'section:item' => true,
		'section:item.assign' => false,
		'section:variable' => true,
		'section:variable.exists' => true
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
		switch($hookName)
		{
			// Initializes the section by obtaining the list of items to display
			case 'section:init':
				$section = $this->_getVar('section');

				if($section['parent'] !== null)
				{
					$parent = Opt_Instruction_Section_Abstract::getSection($section['parent']);
					$parent['format']->assign('item', $section['from']);
					if($parent['format']->property('section:useReference'))
					{
						return '$_sect'.$section['name'].'_vals = &'.$parent['format']->get('section:variable').'; ';
					}
					return '$_sect'.$section['name'].'_vals = '.$parent['format']->get('section:variable').'; ';
				}
				elseif(!is_null($section['datasource']))
				{
					return '$_sect'.$section['name'].'_vals = '.$section['datasource'].'; ';
				}
				else
				{
					$this->assign('item', $section['name']);
					return '$_sect'.$section['name'].'_vals = &'.$this->get('variable:item').'; ';
				}
			// The end of the section loop.
			case 'section:endLoop':
				return ' } ';
			// The condition that should test if the section is not empty.
			case 'section:isNotEmpty':
				$section = $this->_getVar('section');
				return 'is_array($_sect'.$section['name'].'_vals) && ($_sect'.$section['name'].'_cnt = sizeof($_sect'.$section['name'].'_vals)) > 0';
			// The code block after the condition
			case 'section:started':
			// The code block before the end of the conditional block.
			case 'section:finished':
			// The code block after the conditional block
			case 'section:done':
			// The code block before entering the loop.
			case 'section:loopBefore':
				return '';
			// The default loop for the ascending order.
			case 'section:startAscLoop':
				$section = $this->_getVar('section');
				return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';
			// The default loop for the descending order.
			case 'section:startDescLoop':
				$section = $this->_getVar('section');
				return 'for(end($_sect'.$section['name'].'_vals), $_sect'.$section['name'].'_cxx = $_sect'.$section['name'].'_cnt; $_sect'.$section['name'].'_cxx > 0; prev($_sect'.$section['name'].'_vals), $_sect'.$section['name'].'_cxx--){ $_sect'.$section['name'].'_i = key($_sect'.$section['name'].'_vals); $_sect'.$section['name'].'_v = current($_sect'.$section['name'].'_vals);';
			// Retrieving the whole section item.
			case 'section:item':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_v';
			// Retrieving a variable from a section item.
			case 'section:variable':
				$section = $this->_getVar('section');
				if($this->isDecorating())
				{
					return '$_sect'.$section['name'].'_v'.$this->_decorated->get('item:item');
				}
				return '$_sect'.$section['name'].'_v[\''.$this->_getVar('item').'\']';
			// Retrieving a variable from a section item.
			case 'section:variable.assign':
				$section = $this->_getVar('section');
				if($this->isDecorating())
				{
					return '$_sect'.$section['name'].'_v'.$this->_decorated->get('item:assign');
				}
				return '$_sect'.$section['name'].'_v[\''.$this->_getVar('item').'\']='.$this->_getVar('value');
			// Hook for "exists" operator
			case 'section:variable.exists':
				$section = $this->_getVar('section');
				if($this->isDecorating())
				{
					return 'isset($_sect'.$section['name'].'_v'.$this->_decorated->get('item:item').')';
				}
				return 'isset($_sect'.$section['name'].'_v[\''.$this->_getVar('item').'\'])';
			// Resetting the section to the first element.
			case 'section:reset':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return 'reset($_sect'.$section['nesting'].'_vals); $_sect'.$section['nesting'].'_cxx = 1; ';
				}
				else
				{
					return 'end($_sect'.$section['nesting'].'_vals); $_sect'.$section['nesting'].'_cxx = $_sect'.$section['name'].'_cnt; ';
				}
				break;
			// Moving to the next element.
			case 'section:next':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return 'next($_sect'.$section['nesting'].'_vals); ++$_sect'.$section['nesting'].'_cxx; ';
				}
				else
				{
					return 'prev($_sect'.$section['nesting'].'_vals); --$_sect'.$section['nesting'].'_cxx; ';
				}
				break;
			// Checking whether the iterator is valid.
			case 'section:valid':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '$_sect'.$section['nesting'].'_cxx <= $_sect'.$section['nesting'].'_cnt';
				}
				else
				{
					return '$_sect'.$section['nesting'].'_cxx > 0';
				}
			// Populate the current element
			case 'section:populate':
				return ' $_sect'.$section['name'].'_i = key($_sect'.$section['name'].'_vals); $_sect'.$section['name'].'_v = current($_sect'.$section['name'].'_vals); ';
			// The code that returns the number of items in the section;
			case 'section:count':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_cnt';
			// Section item size.
			case 'section:size':
				$section = $this->_getVar('section');
				return 'sizeof($_sect'.$section['name'].'_v)';
			// Section iterator.
			case 'section:iterator':
				$section = $this->_getVar('section');
				return '$_sect'.$section['nesting'].'_i';
			// Testing the first element.
			case 'section:isFirst':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['nesting'].'_cxx == 1)';
				}
				else
				{
					return '($_sect'.$section['nesting'].'_cxx == $_sect'.$section['name'].'_cnt)';
				}
			// Testing the last element.
			case 'section:isLast':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['nesting'].'_cxx == $_sect'.$section['name'].'_cnt)';
				}
				else
				{
					return '($_sect'.$section['nesting'].'_cxx == 1)';
				}
			// Testing the extreme element.
			case 'section:isExtreme':
				$section = $this->_getVar('section');
				return '(($_sect'.$section['nesting'].'_cxx == $_sect'.$section['name'].'_cnt) || ($_sect'.$section['nesting'].'_cxx == 1))';
			// The variable access.
			case 'variable:item':
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return '$ctx->_global[\''.$item.'\']';
				}
				return '$ctx->_data[\''.$item.'\']';
			case 'variable:item.assign':
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return '$ctx->_global[\''.$item.'\']='.$this->_getVar('value');
				}
				return '$ctx->_data[\''.$item.'\']='.$this->_getVar('value');
			// VARIABLE PRE- AND POST INDECREMENTATION
			case 'variable:item.preincrement':
				$pre = '++';
			case 'variable:item.predecrement':
				$pre = (isset($pre) ? '++' : '--');
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return $pre.'$ctx->_global[\''.$item.'\']';
				}
				return $pre.'$ctx->_data[\''.$item.'\']';
			case 'variable:item.postincrement':
				$pre = '++';
			case 'variable:item.postdecrement':
				$pre = (isset($pre) ? $pre : '--');
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return '$ctx->_global[\''.$item.'\']'.$pre;
				}

				return '$ctx->_data[\''.$item.'\']'.$pre;
			case 'variable:item.exists':
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return 'isset($ctx->_global[\''.$item.'\'])';
				}
				return 'isset($ctx->_data[\''.$item.'\'])';
			// ITEM PRE- AND POST INDECREMENTATION
			case 'item:item.preincrement':
				$pre = '++';
			case 'item:item.predecrement':
				$pre = (isset($pre) ? '++' : '--');
				return $pre.$this->_getVar('code').'[\''.$this->_getVar('item').'\']';
			case 'item:item.postincrement':
				$pre = '++';
			case 'item:item.postdecrement':
				$pre = (isset($pre) ? '++' : '--');
				return $this->_getVar('code').'[\''.$this->_getVar('item').'\']'.$pre;
			case 'item:item':
				return '[\''.$this->_getVar('item').'\']';
			case 'item:item.assign':
				return '[\''.$this->_getVar('item').'\']='.$this->_getVar('value');
			default:
				return NULL;
		}
	} // end _build();
} // end Opt_Format_AssociativeArray;
