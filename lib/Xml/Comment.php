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

	class Opt_Xml_Comment extends Opt_Xml_Cdata
	{
		public function __construct($cdata = '')
		{
			parent::__construct($cdata);
			$this->set('commented', true);
		} // end __construct();

		protected function _validate(&$text)
		{
			if(strpos($text, '--') !== false)
			{
				throw new Opt_XmlComment_Exception('--');
			}
			return true;
		} // end _validate();
	} // end Opt_Xml_Comment;
