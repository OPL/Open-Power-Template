<?php

	class TextareaComponent extends BaseComponent
	{
		public function display(Array $attributes)
		{
			$attributes['name'] = $this->_params['name'];
			$attributes['id'] = $this->_params['name'].'_id';

			echo '<textarea';
			foreach($attributes as $name => $value)
			{
				echo ' '.$name.'="'.$value.'"';
			}
			echo '>';

			if($this->_form->getStatus() == Form::FORM_INVALID)
			{
				echo htmlspecialchars($_POST[$this->_params[$name]]);
			}
			elseif(isset($this->_params['value']))
			{
				echo htmlspecialchars($this->_params['value']);
			}
			echo '</textarea>';
		} // end display();
	} // end TextareaComponent;