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
 * $Id: Objective.php 295 2010-02-10 15:54:16Z zyxist $
 */

/**
 * This data format informs the decorated formats that they
 * should refer to the global template data, shared across
 * the views.
 *
 * @package Formats
 */
class Opt_Format_Global extends Opt_Format_Class
{
	/**
	 * Notify the decorated format that it should refer
	 * to the global data.
	 */
	protected function _onDecorate()
	{
		$this->_decorated->assign('global', true);
	} // end _onDecorate();

	/**
	 * In this particular format, the method does nothing.
	 *
	 * @param string $hookName The hook name
	 * @return NULL
	 */
	protected function _build($hookName)
	{
		if(!$this->isDecorating())
		{
			throw new Opt_FormatNotDecorated_Exception('Global');
		}
		return NULL;
	} // end _build();

	/**
	 * A type casting utility.
	 *
	 * @param string $format The required format name.
	 * @param string $code The code to cast.
	 * @param Opt_Format_Class $casted The casted data format object
	 * @return string|null
	 */
	static public function cast($format, $code, $casted = null)
	{
		assert($casted instanceof Opt_Format_Global);

		$className = get_class($casted->_decorated);

		if($casted->_decorated->getName() == $format)
		{
			return $code;
		}
		return $className::cast($format, $code, $casted);
	} // end cast();
} // end Opt_Format_Global;