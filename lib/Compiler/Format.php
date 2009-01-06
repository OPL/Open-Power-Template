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
 * $Id: Format.php 19 2008-11-20 16:09:45Z zyxist $
 */

	abstract class Opt_Compiler_Format
	{
		private $_vars = array();
		protected $_supports;
		
		protected $_tpl;
		protected $_compiler;
		protected $_applyVars = true;
		protected $_decorated;
		
		public function __construct($tpl, $cpl)
		{
			$this->_tpl = $tpl;
			$this->_compiler = $cpl;
		} // end __construct();
	
		abstract protected function _build($hookName);
		
		final protected function _getVar($name)
		{
			if(!isset($this->_vars[$name]))
			{
				return NULL;
			}
			return $this->_vars[$name];
		} // end _getVar();
		
		final protected function _decorateHook($hook, $with, $alternative)
		{
			if(!$this->isDecorated())
			{
				return $this->_applyVars($alternative);
			}
			$this->_vars[$with] = $this->_decorated->get($with);
			return $this->_applyVars($hook);
		} // end _decorateHook();
		
		public function action($name)
		{
			/* null */
		} // end action();

		public function supports($hookType)
		{
			return in_array($hookType, $this->_supports);
		} // end supports();
		
		final public function getName()
		{
			return substr(get_class($this), 11, strlen(get_class($this)));
		} // end getName();
		
		final public function assign($name, $value)
		{
			$this->_vars[$name] = $value;
		} // end assign();
		
		final public function defined($name)
		{
			return isset($this->_vars[$name]);
		} // end defined();
		
		final public function resetVars()
		{
			$this->_vars = array();
		} // end resetVars();
		
		final public function get($hookName)
		{
			$result = $this->_build($hookName);
			
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
						throw new Opt_APIHookNotDefined_Exception($hookName, get_class($this));
					}
				}
				else
				{
					break;
				}
			}
			while(is_object($obj));
			return $this->_applyVars($result);
		} // end get();
		
		final public function decorate(Opt_Compiler_Format $object)
		{
			$this->_decorated = $object;
			$this->_decorated->_vars = &$this->_vars;
		} // end decorate();
		
		final public function isDecorated()
		{
			return !is_null($this->_decorated);
		} // end isDecorated();
		
		final public function _callback($matches)
		{		
			// Escaping
			if($matches[0][0] == '\\')
			{
				return '%'.$matches[1].'%';
			}
		
			if(isset($this->_vars[$matches[1]]))
			{
				return $this->_vars[$matches[1]];
			}
			return '';
		} // end _callback();
		
		private function _applyVars($result)
		{
			if($this->_applyVars)
			{
				return preg_replace_callback('/\\\\?\%([a-zA-Z0-9\_]+)\%/m', array($this, '_callback'), $result);
			}
			$this->_applyVars = true;
			return $result;
		} // end _applyVars();
	} // end Opt_Compiler_Hook;
