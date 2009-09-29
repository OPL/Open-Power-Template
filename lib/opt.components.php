<?php
  //  --------------------------------------------------------------------  //
  //                        Open Power Template                             //
  //         Copyright (c) 2005-2007 Tomasz "Zyx" Jedrzejewski              //
  //     Copyright (c) 2008 Invenzzia Group, http://www.invenzzia.org/      //
  //  --------------------------------------------------------------------  //
  //  This program is free software; you can redistribute it and/or modify  //
  //  it under the terms of the GNU Lesser General Public License as        //
  //  published by the Free Software Foundation; either version 2.1 of the  //
  //  License, or (at your option) any later version.                       //
  //  --------------------------------------------------------------------  //
  //
  // $Id: opt.components.php 59 2006-08-02 11:29:55Z zyxist $
	
	function generateTagElementList($list)
	{
		$code = '';
		foreach($list as $name => $value)
		{
			$code .= ' '.$name.'="'.htmlspecialchars($value).'"';			
		}
		return $code;
	} // end generateTagElementList();

	class selectComponent implements ioptComponent
	{
		protected $_list = array();
		protected $message = NULL;
		protected $tagParameters = array();
		
		protected $tpl;

		public function __construct($name = '')
		{
			$this -> _list = array();
			$this -> message = NULL;
			$this -> tagParameters = array();		
		} // end __construct();
		
		public function setOptInstance(optClass $tpl)
		{
			$this -> tpl = $tpl;
		} // end setOptInstance();
		
		public function set($name, $value)
		{
			switch($name)
			{
				case 'message':
					$this -> message = $value;
					break;
				case 'selected':
					foreach($this -> _list as $i => &$item)
					{
						if($item['value'] == $value)
						{
							$item['selected'] = true;
						}				
					}
					break;
				default:
					$this -> tagParameters[$name] = $value;		
			}
		} // end set();
		
		public function push($value, $desc, $selected = false)
		{
			$this -> _list[] = array(
				'value' => $value,
				'desc' => $desc,
				'selected' => $selected		
			);
		} // end push();

		public function setDatasource(&$source)
		{
			$this -> _list = $source;		
		} // end setDatasource();

		public function begin()
		{	
			$code = '<select'.generateTagElementList($this->tagParameters).'>';
			$selected = 0;
			foreach($this -> _list as $item)
			{
				if($item['selected'] == 1 && $selected == 0)
				{
					$code .= '<option value="'.$item['value'].'" selected="selected">'.$item['desc'].'</option>';
					$selected = 1;
				}
				else
				{
					$code .= '<option value="'.$item['value'].'">'.$item['desc'].'</option>';
				}		
			}
			$code .= '</select>';
			echo $code;
		} // end begin();

		public function onmessage($pass_to)
		{
			if($this -> message == NULL)
			{
				return 0;
			}
			$this -> tpl -> vars[$pass_to] = $this -> message;
			return 1;		
		} // end onmessage();

		public function end()
		{
			echo '';		
		} // end end();
	}

	class textInputComponent implements ioptComponent
	{
		protected $message = NULL;
		protected $tagParameters = array();
		protected $tpl;

		public function __construct($name = '')
		{
			$this -> message = NULL;		
		} // end __construct();
		
		public function setOptInstance(optClass $tpl)
		{
			$this -> tpl = $tpl;
		} // end setOptInstance();

		public function set($name, $value)
		{
			switch($name)
			{
				case 'message':
					$this -> message = $value;
					break;
				default:
					$this -> tagParameters[$name] = $value;		
			}
		} // end set();
		
		public function push($value, $desc, $selected = false)
		{
			$this -> set($value, $desc);
		} // end push();

		public function setDatasource(&$source)
		{
			if(is_array($source))
			{
				if(isset($source['name']))
				{
					$this -> tagParameters['name'] = $source['name'];
				}
				if(isset($source['value']))
				{
					$this -> tagParameters['value'] = $source['value'];
				}
				if(isset($source['message']))
				{
					$this -> message = $source['message'];
				}
			}
		} // end setDatasource();

		public function begin()
		{
			echo '<input type="text"'.generateTagElementList($this->tagParameters).' />';
		} // end begin();

		public function onmessage($pass_to)
		{
			if($this -> message == NULL)
			{
				return 0;
			}
			$this -> tpl -> vars[$pass_to] = $this -> message;
			return 1;
		} // end onmessage();

		public function end()
		{
			echo '';		
		} // end end();
	}

	class textLabelComponent extends textinputComponent implements ioptComponent
	{
		public function begin()
		{
			$code = '<input type="hidden"'.generateTagElementList($this->tagParameters).' />';
			if($this -> tagParameters['value'] != NULL)
			{
				echo $code.'<span class="label">'.htmlspecialchars($this -> tagParameters['value']).'</span>';
			}
			echo $code;
		} // end begin();
	}

	class formActionsComponent implements ioptComponent
	{
		private $buttons;
		protected $tpl;

		public function __construct($name = '')
		{
			$this -> buttons = array();
		} // end __construct();
		
		public function setOptInstance(optClass $tpl)
		{
			$this -> tpl = $tpl;
		} // end setOptInstance();

		public function set($name, $value)
		{
			$this -> push($name, $value);
		} // end set();
		
		public function push($name, $value, $type = 'submit')
		{
			$this -> buttons[] = array(
				'name' => $name,
				'value' => $value,
				'type' => $type
			);		
		} // end push();

		public function setDatasource(&$source)
		{
			$this -> buttons = $source;
		} // end setDatasource();

		public function begin()
		{
			$code = '';
			foreach($this -> buttons as $button)
			{
				$code .= '<input type="'.$button['type'].'"'.($button['name'] != NULL ? ' name="'.$button['name'].'"' : '').' value="'.$button['value'].'"/>';			
			}
			echo $code;
		} // end begin();

		public function end()
		{
			echo '';		
		} // end end();
	}
?>
