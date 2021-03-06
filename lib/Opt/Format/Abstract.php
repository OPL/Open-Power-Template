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
 */

/**
 * The abstract data format class used to create new data formats. Note that
 * this class was named 'Opt_Compiler_Format' in OPT 2.0, but has been renamed
 * because it was not compatible with the naming convention.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @abstract
 * @package Formats
 */
abstract class Opt_Format_Abstract
{
	private $_vars = array();
	private $_name = '';

	/**
	 * The list of supported types of hooks.
	 *
	 * @var Array
	 */
	protected $_supports = array();

	/**
	 * The list of extra format properties.
	 *
	 * @var Array
	 */
	protected $_properties = array();

	/**
	 * The main OPT class
	 *
	 * @var Opt_Class
	 */
	protected $_tpl;

	/**
	 * The used OPT Compiler
	 *
	 * @var Opt_Compiler_Class
	 */
	protected $_compiler;

	/**
	 * The decorated data format object.
	 *
	 * @var Opt_Format_Abstract|NULL
	 */
	protected $_decorated;

	/**
	 * Creates a new data format object.
	 *
	 * @param Opt_Class $tpl The main OPT class.
	 * @param Opt_Compiler_Class $cpl The used template compiler.
	 * @param string $name optional The format description string.
	 */
	public function __construct($tpl, $cpl, $name = '')
	{
		$this->_tpl = $tpl;
		$this->_compiler = $cpl;
		$this->_name = $name;
	} // end __construct();

	/**
	 * Removes the references.
	 */
	public function dispose()
	{
		$item = $this;
		while($item !== null)
		{
			$item->_tpl = null;
			$item->_compiler = null;
			$item->resetVars();
			$tmp = $item;
			$item = $item->_decorated;
			$tmp->_decorated = null;
		}
	} // end dispose();

	/**
	 * Returns the format description string, if it is the top format
	 * object.
	 *
	 * @return String
	 */
	final public function __toString()
	{
		return $this->_name;
	} // end __toString();

	/**
	 * Returns the value of the specified format property.
	 *
	 * @final
	 * @param String $name Property name
	 * @return Mixed Property value
	 */
	final public function property($name)
	{
		$obj = $this;
		do
		{
			if(isset($obj->_properties[$name]))
			{
				return $obj->_properties[$name];
			}
			if(is_object($obj->_decorated))
			{
				$obj = $obj->_decorated;
			}
			else
			{
				return NULL;
			}
		}
		while(is_object($obj));
		return NULL;
	} // end property();

	/**
	 * Returns the format object name.
	 *
	 * @final
	 * @return String
	 */
	final public function getName()
	{
		return substr(get_class($this), 11, strlen(get_class($this)));
	} // end getName();

	/**
	 * Assigns a value to the format. It is used to provide the data
	 * for the code hook generator.
	 *
	 * @final
	 * @param String $name Variable name
	 * @param Mixed $value Variable value
	 */
	final public function assign($name, $value)
	{
		$this->_vars[$name] = $value;
	} // end assign();

	/**
	 * Returns true, if the specified format variable is already defined.
	 *
	 * @final
	 * @param String $name Variable name.
	 * @return Boolean
	 */
	final public function defined($name)
	{
		return isset($this->_vars[$name]);
	} // end defined();

	/**
	 * Cleans the list of format variables.
	 *
	 * @final
	 */
	final public function resetVars()
	{
		$this->_vars = array();
	} // end resetVars();

	/**
	 * Returns the PHP code hook from the data format.
	 * If the current object is not able to generate the hook,
	 * and the decorated format is available, the method tries
	 * to obtain the code from the decorated object.
	 *
	 * Note that due to PHP bug #40479 this method uses die()
	 * to raise an error instead of throwing an exception.
	 *
	 * @final
	 * @param String $hookName The hook name
	 * @return String The output PHP code.
	 */
	final public function get($hookName)
	{
	//	$result = $this->_build($hookName);

		$obj = $this;
		do
		{
			$result = $obj->_build($hookName);
			if(is_null($result))
			{
				if(is_object($obj->_decorated))
				{
					$obj = $obj->_decorated;
				}
				else
				{
					throw new Opt_Format_Exception('Invalid hook name: '.$hookName.' in '.get_class($this));
				}
			}
			else
			{
				break;
			}
		}
		while(is_object($obj));
		return $result;
	} // end get();

	/**
	 * Decorates the specified format object with the current object.
	 *
	 * @final
	 * @param Opt_Format_Abstract $object Format object.
	 */
	public function decorate(Opt_Format_Abstract $object)
	{
		$this->_decorated = $object;
		$this->_decorated->_vars = &$this->_vars;
	} // end decorate();

	/**
	 * Returns true, if this format object decorates another format object.
	 *
	 * @final
	 * @return Boolean
	 */
	final public function isDecorating()
	{
		return $this->_decorated !== null;
	} // end isDecorated();

	/**
	 * Returns the format variable value or NULL, if it does not exist.
	 *
	 * @final
	 * @param String $name Variable name
	 * @return Mixed Variable value
	 */
	final protected function _getVar($name)
	{
		if(!isset($this->_vars[$name]))
		{
			return null;
		}
		return $this->_vars[$name];
	} // end _getVar();

	/**
	 * Allows the script to force the data format to perform an action.
	 * Currently, OPT does not support any actions.
	 *
	 * @param String $name The action name.
	 */
	public function action($name)
	{
		/* null */
	} // end action();

	/**
	 * Returns true, if this format supports a particular hook type.
	 *
	 * @param String $hookType The hook type name
	 * @return Boolean
	 */
	public function supports($hookType)
	{
		if($this->_decorated !== null)
		{
			if(in_array($hookType, $this->_decorated->_supports))
			{
				return true;
			}
		}
		return in_array($hookType, $this->_supports);
	} // end supports();

	/**
	 * This method must build a PHP code for the specified hook.
	 *
	 * @abstract
	 * @param String $hookName Hook name
	 * @return String The PHP code
	 */
	abstract protected function _build($hookName);
} // end Opt_Format_Abstract;
