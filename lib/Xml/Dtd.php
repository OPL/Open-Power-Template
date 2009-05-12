<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
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

	class Opt_Xml_Dtd
	{
		private $_doctype;

		public function __construct($dtd)
		{
			$this->setDoctype($dtd);
		} // end __construct();

		public function setDoctype($doctype)
		{
			$this->_doctype = $doctype;
		} // end setDoctype();

		public function getDoctype()
		{
			return $this->_doctype;
		} // end getDoctype();
	} // end Opt_Xml_Dtd;
