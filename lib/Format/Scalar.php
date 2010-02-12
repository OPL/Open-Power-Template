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
 * $Id$
 */

/**
 * The class implements the "Scalar" data format, useful for scalar
 * data in the template.
 */
class Opt_Format_Scalar extends Opt_Format_Class
{
	/**
	 * The list of supported types.
	 * @var array
	 */
	static protected $_supports = array(
		'variable', 'cast'
	);

	/**
	 * The data format properties.
	 * @var array
	 */
	static protected $_properties = array(
		'variable:item.assign' => true,
		'variable:item.preincrement' => true,
		'variable:item.postincrement' => true,
		'variable:item.predecrement' => true,
		'variable:item.postdecrement' => true,
		'variable:useReference' => true,
	);


	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @param string $hookName The hook name
	 * @return string The output PHP code
	 */
	protected function _build($hookName)
	{
		switch($hookName)
		{
			case 'variable:item':
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return 'self::$_global[\''.$item.'\']';
				}
				return '$this->_data[\''.$item.'\']';
			case 'variable:item.assign':
				$this->_applyVars = false;
				$item = $this->_getVar('item');
				if($this->_getVar('global') === true)
				{
					return 'self::$_global[\''.$item.'\']='.$this->_getVar('value');
				}
				return '$this->_data[\''.$item.'\']='.$this->_getVar('value');
			case 'variable:item.preincrement':
				return '++'.$this->_getVar('code');
			case 'variable:item.postincrement':
				return $this->_getVar('code').'++';
			case 'variable:item.predecrement':
				return '--'.$this->_getVar('code');
			case 'variable:item.postdecrement':
				return $this->_getVar('code').'--';
		}
	} // end _build();

	/**
	 * A type casting utility.
	 *
	 * @param string $format The required format name.
	 * @param string $code The code to cast.
	 * @param Opt_Format_Class $casted The casted data format object
	 * @return string|null
	 */
	static public function cast($format, $code, $casted = null)
	{
		switch($format)
		{
			case 'Array':
				return '(array)'.$code;
		}
		return NULL;
	} // end cast();

} // end Opt_Format_Scalar;