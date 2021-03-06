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
 *
 */

/**
 * A compiler for `contains` statements in opt:switch instruction.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class Opt_Format_SwitchContains extends Opt_Format_Abstract
{
	/**
	 * The list of supported hook types.
	 * @var array
	 */
	protected $_supports = array(
		'switch'
	);

	/**
	 * Data format properties as information for the
	 * caller.
	 *
	 * @var array
	 */
	protected $_properties = array(
		'switch:longCase' => true
	);

	/**
	 * The things that need to be done before we enter the tests.
	 * @var string
	 */
	private $_testsBefore = '';

	/**
	 * The list of conditions that need to be tested
	 * at the end.
	 *
	 * @var string
	 */
	private $_conditions = '';

	/**
	 * The list of conditions that need to be tested
	 * at the end of the current top-level case.
	 *
	 * @var SplStack
	 */
	private $_finalConditions;

	/**
	 * The switch counter to generate unique variable names.
	 * @static
	 * @var integer
	 */
	static private $_counter = 0;

	/**
	 * The local GOTO label generator.
	 * @var integer
	 */
	private $_label = 0;

	/**
	 * The previous nesting
	 * @var integer
	 */
	private $_previous = 0;

	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param string $hookName The hook name
	 * @return string The output PHP code
	 */
	protected function _build($hookName)
	{
		switch($hookName)
		{
			case 'switch:enterTestBegin.first':
				return 'if(Opt_Function::isContainer($__test_'.self::$_counter.' = '.$this->_getVar('test').')){ ';
			case 'switch:enterTestEnd.first':
				return '__switch_'.(self::$_counter++).'_end: } ';
			case 'switch:enterTestBegin.later':
				return 'elseif(Opt_Function::isContainer($__test_'.self::$_counter.' = '.$this->_getVar('test').')){ ';
			case 'switch:enterTestEnd.later':
				return '__switch_'.(self::$_counter++).'_end:  } ';
			case 'switch:testsBefore':
				return $this->_testsBefore;
			case 'switch:testsAfter':
				return '';
			case 'switch:caseBefore':
				$params = $this->_getVar('attributes');
				$element = $this->_getVar('element');
				$order = $element->get('priv:order');

				$format = $this->_compiler->getFormat('#container', true);
				$format->assign('container', '$__test_'.self::$_counter);
				$format->assign('values', $params['value']);
				$format->assign('optimize', false);

				$prepender = $this->_getVar('prepender');
				if($prepender !== null)
				{
					$this->_testsBefore .= ' '.$prepender.'->setCaseResult('.($this->_getVar('order')+1).','.$format->get('container:contains').');'.PHP_EOL;
				
					$condition = $prepender.'->isPassed('.($this->_getVar('order')+1).')';
				}
				else
				{
					$condition = $format->get('container:contains');
				}

				if($this->_getVar('nesting') == 0)
				{
					$this->_previous = $this->_getVar('nesting');
					return 'if('.$condition.'){ '.PHP_EOL.($prepender !== null ? $prepender.'->startPassing('.($this->_getVar('order')+1).')' : '');
				}
				else
				{
					if($this->_previous != $this->_getVar('nesting'))
					{
						$this->_conditions = '';
					}

					$conditionCode = ' if('.$condition.'){ $__ctrl_'.self::$_counter.' = '.$this->_getVar('order').'; goto __switch_'.self::$_counter.'_'.$this->_getVar('order').'c; }'.PHP_EOL;
					
					if(($informed = $this->_getVar('informed')) !== null)
					{
						$conditionCode .= ' else { '.$informed.' } __switch_'.self::$_counter.'_'.$this->_getVar('order').'ce: ';
					}
					else
					{
						$conditionCode .= '__switch_'.self::$_counter.'_'.$this->_getVar('order').'ce: ';
					}

					$this->_conditions = $conditionCode.$this->_conditions;

					$this->_previous = $this->_getVar('nesting');
					return '__switch_'.self::$_counter.'_'.$this->_getVar('order').'c:'.PHP_EOL.($prepender !== null ? $prepender.'->startPassing('.($this->_getVar('order')+1).')' : '');
				}
			case 'switch:caseAfter':
				$prepender = $this->_getVar('prepender');
				if($this->_getVar('nesting') == 0)
				{
					$result = ($prepender !== null ? $prepender.'->endPassing('.($this->_getVar('order')+1).');' : '').' }'.PHP_EOL;
					if(($informed = $this->_getVar('informed')) !== null)
					{
						$result .= ' else { '.$informed.' } '.PHP_EOL;
					}
					return $result;
				}
				else
				{
					return ($prepender !== null ? $prepender.'->endPassing('.($this->_getVar('order')+1).');' : '').' if($__ctrl_'.self::$_counter.' == '.$this->_getVar('order').'){ goto __switch_'.self::$_counter.'_'.$this->_getVar('order').'ce; }';
				}
		}
	} // end _build();

	/**
	 * The format actions.
	 *
	 * @param string $name The action name
	 * @return mixed
	 */
	public function action($name)
	{
		if($name == 'switch:caseAttributes')
		{
			return array(
				'value' => array(0 => Opt_Instruction_Abstract::REQUIRED, Opt_Instruction_Abstract::EXPRESSION, null, 'parse')
			);
		}
		elseif($name == 'switch:processAttribute')
		{
			return 'value';
		}
		else
		{
			return $this->_conditions;
		}
	} // end action();

} // end Opt_Format_SwitchContains;