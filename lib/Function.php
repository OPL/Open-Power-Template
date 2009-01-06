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
 * $Id$
 */

	class Opt_Function
	{	
		static public function processContainer($callback, $args)
		{			
			$result = array();
			foreach($args[0] as $idx => $value)
			{
				$args[0] = $value;
				$result[$idx] = call_user_func_array($callback, $args);
			}
			
			return $result;
		} // end processContainer();
		
		static public function isContainer($value)
		{
			return is_array($value) || (is_object($value) && ($value instanceof Iterator || $value instanceof IteratorAggregate));
		} // end isContainer();
	
		static public function firstof()
		{
			$args = func_get_args();
			$cnt = sizeof($args);
			for($i = 0; $i < $cnt; $i++)
			{
				if(!empty($args[$i]))
				{
					return $args[$i];
				}
			}
		} // end firstof();
	
		static public function spacify($string, $delim = ' ')
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'spacify'), array($string, $delim));
			}
		
			$ns = '';
			$len = strlen($string);
			$tpl = Opl_Registry::get('opt');

			for($i = 0; $i < $len; $i++)
			{
				if($tpl->charset == 'utf-8' && ord($string[$i]) > 127)
				{
					break;
				}
				$ns .= $string[$i];
				if($i + 1 < $len)
				{
					$ns .= $delim;
				}
			}
			return $ns;
		} // end spacify();
	
		static public function indent($string, $num, $with = ' ')
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'indent'), array($string, $num, $with));
			}
		
			return preg_replace('/([\\r\\n]{1,2})/', '$1'.str_repeat($with, $num), $string);
		} // end indent();
	
		static public function strip($string)
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'strip'), array($string));
			}

			return preg_replace('/\s\s+/', ' ', $string);
		} // end strip();
	
		static public function truncate($string, $length, $etc = '', $break = true)
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'truncate'), array($string, $length, $etc, $break));
			}

			if($length == 0)
			{
				return '';
			}
			$strlen = strlen($string);
			if($strlen > $length)
			{
				if(!$break)
				{
					$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length));
				}
				return substr($string, 0, $length).$etc;
			}
			return $string;
		} // end truncate();
	
		static public function wordwrap($string, $width, $break = null, $cut = null)
		{
			if(!is_null($break))
			{
				$break = str_replace(array('\\n', '\\r', '\\t', '\\\\'), array("\n", "\r", "\t", '\\'), $break);
			}
			return wordwrap($string, $width, $break, $cut);
		} // end wordwrap();
	
		static public function money($number, $format = null)
		{
			if(self::isContainer($number))
			{
				return self::processContainer(array('Opt_Function', 'money'), array($number, $format));
			}
			$format = (is_null($format) ? $opt->moneyFormat : $format);
			return money_format($format, $number);
		} // end money();
	
		static public function number($number, $d1 = null, $d2 = null, $d3 = null)
		{
			$d1 = (is_null($d1) ? $opt->numberDecimals : $d1);
			$d2 = (is_null($d2) ? $opt->numberDecPoint : $d2);
			$d3 = (is_null($d3) ? $opt->numberThousandSep : $d3);
			return number_format($number, $d1, $d2, $d3);
		} // end number();
		
		static public function absolute($items)
		{
			if(self::isContainer($items))
			{
				return self::processContainer('abs', array($string, $length, $etc, $break));
			}
			
			return abs($items);
		} // end absolute();
		
		static public function sum($items)
		{
			if(self::isContainer($items))
			{				
				$sum = 0;
				foreach($items as $item)
				{
					if(!self::isContainer($item))
					{
						$sum += $item;
					}
				}
				return $sum;
			}
			return null;
		} // end sum();
		
		static public function average($items)
		{
			if(self::isContainer($items))
			{				
				$sum = 0;
				$cnt = 0;
				foreach($items as $item)
				{
					if(!self::isContainer($item) && !is_null($item))
					{
						$sum += $item;
						$cnt++;
					}
				}
				if($cnt > 0)
				{
					return $sum / $cnt;
				}
			}
			return null;
		} // end average();
		
		static public function stddev($items)
		{
			$average = self::average($items);
			
			if(is_null($average))
			{
				return null;
			}
			
			$sum = 0;
			$cnt = 0;
			foreach($items as $item)
			{
				if(!self::isContainer($item) && !is_null($item))
				{
					$sum += pow($item, 2);
					$cnt++;
				}
			}
			return sqrt(($sum / $cnt) - pow($average, 2));
		} // end stddev();
		
		static public function upper($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'upper'), array($item));
			}
			
			return strtoupper($item);
		} // end upper();
		
		static public function lower($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'lower'), array($item));
			}
			
			return strtolower($item);
		} // end lower();
		
		static public function capitalize($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'capitalize'), array($item));
			}
			
			return ucfirst($items);
		} // end capitalize();
		
		static public function nl2br($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'nl2br'), array($item));
			}
			
			return nl2br($items);
		} // end nl2br();
		
		static public function stripTags($item, $what)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'stripTags'), array($item, $what));
			}
			
			return strip_tags($item, $what);
		} // end stripTags();
		
		static public function range($number1, $number2 = null)
		{
			if(is_null($number2))
			{
				$number2 = date('Y');
			}
			if($number2 == $number1)
			{
				return $number1;
			}
			return $number1.' - '.$number2;			
		} // end range();
		
		static public function isUrl($address)
		{
			return filter_var($address, FILTER_VALIDATE_URL) !== false;
		} // end isUrl();
		
		static public function isImage($address)
		{
			$result = @parse_url($address);
			if(is_array($result))
			{
				if(isset($result['path']))
				{
					if(($id = strrpos($result['path'], '.')) !== false)
					{
						if(in_array(substr($result['path'], $id+1, 3), array('jpg', 'png', 'gif', 'svg', 'bmp')))
						{
							return true;
						}
					}
				}
			}
			return false;
		} // end isImage();
	} // end Opt_Function;