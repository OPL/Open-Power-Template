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
 */

/**
 * The data format for blocks.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class Opt_Format_Block extends Opt_Format_Abstract
{
	/**
	 * The list of supported hook types.
	 * @var array
	 */
	protected $_supports = array(
		'block'
	);

	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param string $hookName The hook name
	 * @return string The output PHP code
	 */
	protected function _build($hookName)
	{
		switch($hookName)
		{
			case 'block:init':
				return ' '.$this->_getVar('variable').'->setView($this); ';
			case 'block:done':
				return '';
			case 'block:valid':
				return ' '.$this->_getVar('variable').' instanceof Opt_Block_Interface';
			case 'block:build':
				return ' '.$this->_getVar('variable').' = new '.$this->_getVar('className').'; ';
			case 'block:on-single':
				$argList = 'array( ';
				foreach($this->_getVar('arguments') as $name=>$value)
				{
					$argList .= '\''.$name.'\' => '.$value.', ';
				}
				$argList .= ')';
				return ' '.$this->_getVar('variable').'->onSingle('.$argList.'); ';
			case 'block:on-open':
				$argList = 'array( ';
				foreach($this->_getVar('arguments') as $name=>$value)
				{
					$argList .= '\''.$name.'\' => '.$value.', ';
				}
				$argList .= ')';
				return ' if('.$this->_getVar('variable').'->onOpen('.$argList.')){ ';
			case 'block:on-close':
				return ' } '.$this->_getVar('variable').'->onClose(); ';
			case 'block:set':
				return ' '.$this->_getVar('variable').'->__set('.$this->_getVar('name').', '.$this->_getVar('value').') ';
			case 'block:get':
				return ' '.$this->_getVar('variable').'->__get('.$this->_getVar('name').') ';
		}
	} // end _build();

} // end Opt_Format_Block;