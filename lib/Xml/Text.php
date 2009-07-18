<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id$
 */

 /*
  * A container for Opt_Xml_Cdata and Opt_Xml_Expression objects.
  */

	class Opt_Xml_Text extends Opt_Xml_Scannable
	{
		public function __construct($cdata = null)
		{
			parent::__construct();
			if(!is_null($cdata))
			{
				$this->appendData($cdata);
			}
		} // end __construct();
		
		public function appendData($cdata)
		{
			$node = $this->getLastChild();
			if(is_null($node) || $node->getType() != 'Opt_Xml_Cdata' || $node->get('cdata') == true)
			{
				$node = new Opt_Xml_Cdata($cdata);
				$this->appendChild($node);
			}
			else
			{
				$node->appendData($cdata);
			}
		} // end appendData();
		
		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() != 'Opt_Xml_Expression' && $node->getType() != 'Opt_Xml_Cdata')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Text', $node->getType());
			}
		} // end _testNode();
	} // end Opt_Xml_Text;
