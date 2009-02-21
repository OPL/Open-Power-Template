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
 * $Id: Node.php 18 2008-10-29 21:23:43Z zyxist $
 */

	abstract class Opt_Xml_Node extends Opt_Xml_Buffer
	{
		protected $_type;
		protected $_parent = null;

		public function setParent($parent)
		{
			$this->_parent = $parent;
		} // end setParent();

		public function getType()
		{
			return get_class($this);
		} // end getType();
		
		public function getParent()
		{
			return $this->_parent;
		} // end getParent();
		
		public function __toString()
		{
			return get_class($this);
		} // end __toString();

		public function dispose()
		{
			$this->_dispose();
		} // end dispose();

		protected function _dispose()
		{
			$this->_parent = null;
			$this->_buffers = null;
			$this->_args = null;
		} // end _dispose();
	} // end Opt_Xml_Node;