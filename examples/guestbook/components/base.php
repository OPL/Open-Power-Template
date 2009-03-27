<?php

	class Opl_NoFormDefined_Exception extends Opl_Exception
	{
		private $_message = 'No form defined for the component %s.';
	} // end Opl_NoFormDefined_Exception;

	abstract class BaseComponent implements Opt_Component_Interface
	{
		protected $_params = array();
		protected $_view;
		protected $_form;

		public function __construct($name = '')
		{
			$this->_params['name'] = $name;
		} // end __construct();

		public function setView(Opt_View $view)
		{
			$this->_view = $view;
			$this->_form = $view->get('form');

			if(!is_object($this->_form))
			{
				throw new Opl_NoFormDefined_Exception(get_class($this).':'.$this->_params['name']);
			}
		} // end setView();

		public function setDatasource(&$data)
		{
			/* null */
		} // end setDatasource();

		public function set($name, $value)
		{
			$this->_params[$name] = $value;
		} // end set();

		public function get($name)
		{
			if($name == 'id')
			{
				return $this->_params['name'].'_id';
			}
			if(!isset($this->_params[$name]))
			{
				return null;
			}
			return $this->_params[$name];
		} // end get();

		public function defined($name)
		{
			return isset($this->_params[$name]);
		} // end defined();

		public function manageAttributes($nodeName, Array $attributes)
		{
			if($this->_form->getStatus() != Form::FORM_INVALID)
			{
				return $attributes;
			}
			if($nodeName == 'div' && $this->_form->getValidationStatus($this->_params['name']))
			{
				$attributes['class'] = $this->_view->getTemplateVar('formInvalidFieldRowClass');
			}
			return $attributes;
		} // end manageAttributes();

		public function processEvent($event)
		{
			$ok = $this->_form->getValidationStatus($this->_params[$name]);
			switch($event)
			{
				case 'isRequired':
					if($ok && $this->_form->getStatus() != Form::FORM_INVALID)
					{
						return true;
					}
					break;
				case 'error':
					if(!$ok)
					{
						$this->_view->errorMessage = $this->_form->getErrorMessage($this->_params[$name]);
						return true;
					}
			}
			return false;
		} // end processEvent();
	} // end BaseComponent;
