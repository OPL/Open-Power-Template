<?php

	class Form
	{
		const FORM_INITIAL = 0;
		const FORM_INVALID = 1;
		const FORM_VALID = 2;

		private $_fields;
		private $_view;
		private $_action;
		private $_status = Form::FORM_INITIAL;
		private $_validationStatus = array();

		public function __construct(Opt_View $view)
		{
			$this->_view = $view;
			$view->form = $this;

			$this->_fields = array();
		} // end __construct();

		public function setAction($action)
		{
			$this->_action = $action;
		} // end setAction();

		public function __get($name)
		{
			if($name[0] == '_')
			{
				return null;
			}

			switch($name)
			{
				case 'action':
					return helpers::route($this->_action);
				case 'valid':
					return ($this->_status != self::FORM_INVALID ? true : false);
				default:

			}
		} // end __get();

		public function status()
		{
			return $this->_status;
		} // end valid();

		public function addField($name, $rules, $errorMessage = null)
		{
			$this->_fields[$name] = array('rules' => explode(',',$rules), 'error' => $errorMessage);
		} // end addField();

		public function validate()
		{
			if($_SERVER['REQUEST_METHOD'] != 'POST')
			{
				return false;
			}

			$this->_status = self::FORM_VALID;
			foreach($this->_fields as $name => $info)
			{
				if(empty($_POST[$name]))
				{
					if(in_array($info['rules'], 'required'))
					{
						$this->_validationStatus[$name] = $info['error'];
					}
					$_POST[$name] = null;
					continue;
				}
				foreach($info['rules'] as $rule)
				{
					$data = explode('=', $rule);
					$ok = true;
					switch($data)
					{
						case 'integer':
							$ok = ctype_digit($_POST[$name]);
							break;
						case 'url':
							$_POST[$name] = filter_var($_POST[$name], FILTER_VALIDATE_URL, FILTER_SANITIZE_URL);
							if($_POST[$name] === false)
							{
								$ok = false;
							}
							break;
						case 'email':
							$_POST[$name] = filter_var($_POST[$name], FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
							if($_POST[$name] === false)
							{
								$ok = false;
							}
							break;
						case 'max_len':
							$ok = (strlen($_POST[$name]) <= $data[1]);
							break;
						case 'min_len':
							$ok = (strlen($_POST[$name]) >= $data[1]);
							break;
					}
					if(!$ok)
					{
						$_POST[$name] = null;
						$this->_validationStatus[$name] = $info['error'];
						$this->_validationStatus = self::FORM_VALID;
					}
				}
			}

			return ($this->_validationStatus == self::FORM_VALID);
		} // end validate();

		public function getValidationStatus($name)
		{
			return !isset($this->_validationStatus[$name]);
		} // end getValidationStatus();

		public function getErrorMessage($name)
		{
			if(!isset($this->_validationStatus[$name]))
			{
				return null;
			}
			return $this->_validationStatus[$name];
		} // end getErrorMessage();
	} // end Form;
?>
