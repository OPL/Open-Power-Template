<?php

	class InputComponent extends BaseComponent
	{
		public function display(Array $attributes)
		{
			$attributes['name'] = $this->_params['name'];
			$attributes['id'] = $this->_params['name'].'_id';

			if($this->_form->getStatus() == Form::FORM_INVALID)
			{
				$attributes['value'] = htmlspecialchars($_POST[$this->_params[$name]]);
			}
			elseif(isset($this->_params['value']))
			{
				$attributes['value'] = htmlspecialchars($this->_params['value']);
			}

			echo '<input';
			foreach($attributes as $name => $value)
			{
				echo ' '.$name.'="'.$value.'"';
			}
			echo ' />';
		} // end display();
	} // end InputComponent;
