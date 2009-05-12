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

	/**
	 * The class represents the simplest output which returns the results
	 * of the executed template back to the script.
	 */
	class Opt_Output_Return implements Opt_Output_Interface
	{

		/**
		 * Creates the output.
		 */
		public function __construct()
		{
			$this->_tpl = Opl_Registry::get('opt');
		} // end __construct();

		/**
		 * Returns the output name.
		 *
		 * @return String
		 */
		public function getName()
		{
			return 'Return';
		} // end getName();

		/**
		 * Executes the specified view and return the results back
		 * to the script.
		 *
		 * @param Opt_View $view The rendered view
		 * @return String
		 */
		public function render(Opt_View $view)
		{
			ob_start();			
			$view->_parse($this, true);
			return ob_get_clean();
		} // end render();
	} // end Opt_Output_Return;