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
 * $Id: Quirks.php 290 2010-01-29 09:29:02Z zyxist $
 */

/**
 * The quirks-mode parser. Actually, all the functionality
 * is implemented in the HTML parser, we just need to change
 * one flag to 1.
 *
 * @package Parsers
 */
class Opt_Parser_Quirks extends Opt_Parser_Html
{
	protected $_mode = 1;
} // end Opt_Parser_Quirks;