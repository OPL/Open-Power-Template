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
 * $Id$
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
	 * The stored local switch counter.
	 * @var integer
	 */
	private $_cnt = 0;

	/**
	 * The local GOTO label generator.
	 * @var integer
	 */
	private $_label = 0;

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
				self::$_counter++;
				$this->_cnt = self::$_counter;
				return 'if(Opt_Function::isContainer($__test_'.$this->_cnt.' = '.$this->_getVar('test').')){ ';
			case 'switch:enterTestEnd.first':
				return ' } ';
			case 'switch:enterTestBegin.later':
				self::$_counter++;
				$this->_cnt = self::$_counter;
				return 'elseif(Opt_Function::isContainer($__test_'.$this->_cnt.' = '.$this->_getVar('test').')){ ';
			case 'switch:enterTestEnd.later':
				return ' } ';
			case 'switch:testsBefore':
				return 'switch($__test_'.$this->_cnt.') { ';
			case 'switch:testsAfter':
				$code = $this->_finalConditions.' } ';
				$this->_finalConditions = '';
				return $code;
			case 'switch:caseBefore':
				$params = $this->_getVar('attributes');
				$element = $this->_getVar('element');
				$order = $element->get('priv:order');

				$format = $this->_compiler->getFormat('#container', true);
				$format->assign('container', '$__test');
				$format->assign('list', $params['value']);
				$format->assign('optimize', false);
				$condition = $format->get('container:contains');

				if($this->_getVar('nesting') == 1)
				{
					return 'if('.$condition.'){ ';
				}
				else
				{

					return '__switch_'.$this->_cnt.'_'.$this->_label.'e:';

					// This is an element without a tail recursion. PHP does not support such a case
					// so we must emulate it with GOTO by jumping to the appropriate label somewhere
					// deep in the switch.
					if($element->get('priv:common-break') !== null)
					{
						$this->_finalConditions .= ' case '.$params['value'].':'.PHP_EOL.' $__state_'.self::$_counter.' = '.$order.'; goto __switcheq_'.self::$_counter.'_'.$order.';';
						return ' __switcheq_'.self::$_counter.'_'.$order.': ';
					}
					else
					{
						return 'case '.$params['value'].':'.PHP_EOL.' $__state_'.self::$_counter.' = '.$order.'; ';
					}
				}
				return '';
			case 'switch:caseAfter':
				// We render it only if the switch:analyze action reported it as an occurence
				// that should generate something
				$order = $element->get('priv:order');
				if(($orders = $this->_getVar('element')->get('priv:common-break')) !== null)
				{
					if($this->_getVar('nesting') == 0)
					{
						$this->_conditions = '__switchconte_'.self::$_counter.'_'.$order.': ';
						return ' goto _switchconte_'.self::$_counter.'_'.$order.';';
					}
					else
					{
						// This code processes a place without tail recursion. This case
						// is not available in pure PHP, so we have to help us a bit with
						// IF clause and check the state manually once again.
						$code = ' if(';
						$first = true;
						foreach($orders as $order)
						{
							if(!$first)
							{
								$code .= ' || ';
							}
							else
							{
								$first = false;
							}
							$code .= '$__state_'.self::$_counter.' == '.$order;
						}
						return $code.'){ break; }';
					}
				}
				return '';
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
	} // end action();

} // end Opt_Format_SwitchContains;