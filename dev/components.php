<?php
	// Component testing utility.

	class selectComponent implements Opt_Component_Interface
	{
		private $tpl;
		private $name = '';
		private $valid = true;
		private $selected = null;
		private $attributes = array();
		private $dataSource = array();

		public function __construct($name = '')
		{
			$this->name = $name;
		} // end __construct();

		public function setOptInstance(Opt_Class $tpl)
		{
			$this->tpl = $tpl;
		} // end setOptInstance();

		public function setDatasource(&$data)
		{
			$this->dataSource = $data;
		} // end setDatasource();

		public function set($name, $value)
		{
			switch($name)
			{
				case 'name':
					$this->name = $value;
					break;
				case 'valid':
					$this->valid = $value;
					break;
				case 'selected':
					$this->selected = $value;
					break;
				default:
					$this->attributes[$name] = $value;
			}
		} // end set();
		
		public function get($name)
		{
			if($name == 'name')
			{
				return $this->name;
			}
			if(isset($this->attributes['_'.$name]))
			{
				return $this->attributes['_'.$name];
			}
			return $this->attributes[$name];
		} // end get();
		
		public function defined($name)
		{
			return isset($this->attributes[$name]);
		} // end defined();

		public function createAttribute($nodeName)
		{
			if($nodeName == 'div' && !$this->valid)
			{
				echo ' class="hello"';
			}
		} // end createAttribute();

		public function display($attributes = array())
		{
			$attributes = $this->attributes;
			$attributes['name'] = $this->name;
		
			$list = array();
			foreach($attributes as $n => $v)
			{
				if($n[0] != '_')
				{
					$list[] = $n.'="'.$v.'"';
				}
			}
			$list = implode(' ', $list);
			echo '<select '.$list.'>';
			$chosen = false;
			foreach($this->dataSource as $name => $list)
			{
				if($this->selected == $name)
				{
					echo '<option value="'.$name.'" selected="selected">'.$list.'</option>';
					$chosen = true;
				}
				else
				{
					echo '<option value="'.$name.'">'.$list.'</option>';
				}
			}
			echo '</select>';
		} // end display();

		public function processEvent($event)
		{
			if(!$this->valid)
			{
				$this->attributes['_msg'] = 'The field is not valid.';
				return true;
			}
			return false;
		} // end processEvent();
	} // end selectComponent;

?>
