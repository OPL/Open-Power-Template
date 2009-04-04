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
 * $Id: HEADER 10 2008-08-23 13:38:25Z extremo $
 */

	class Opt_Output_Return implements Opt_Output_Interface
	{
		public function __construct()
		{
			$this->_tpl = Opl_Registry::get('opt');
		} // end __construct();

		public function getName()
		{
			return 'Return';
		} // end getName();

		public function render(Opt_View $view, Opt_Cache_Hook_Interface $cache = null)
		{
			ob_start();
			
			if(!$cache instanceof Opt_Cache_Hook_Interface)
			{
				$view->_parse($this, $this->_tpl->mode);
				return ob_get_clean();
			}
			$cache->cache($this->_tpl, $view, $this->_tpl->mode);

			return ob_get_clean();
		} // end output();
	} // end Opt_Output_Return;