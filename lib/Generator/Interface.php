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
 * $Id: Class.php 294 2010-02-09 17:36:21Z zyxist $
 */

/**
 * The interface for writing data generators for
 * StaticGenerator and RuntimeGenerator data formats.
 *
 * @package Interfaces
 * @subpackage Public
 */
interface Opt_Generator_Interface
{
	public function generate($what);
} // end Opt_Generator_Interface;