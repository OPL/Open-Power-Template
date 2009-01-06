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
 * $Id: Grid.php 19 2008-11-20 16:09:45Z zyxist $
 */

	class Opt_Instruction_Grid extends Opt_Instruction_BaseSection
	{
		protected $_name = 'grid';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:grid', 'opt:gridelse', 'opt:item', 'opt:emptyItem'));
		} // end configure();

		public function processNode(Opt_Xml_Node $node)
		{
			$name = '_process'.ucfirst($node->getName());
			$this->$name($node);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$name = '_postprocess'.ucfirst($node->getName());
			$this->$name($node);
		} // end postprocessNode();
		
		private function _processGrid(Opt_Xml_Node $node)
		{
			if(is_null($section = $this->_sectionInitialized($node)))
			{
				$section = $this->_processShow($node, null, array('cols' => array(self::REQUIRED, self::EXPRESSION)));
				
				// We don't want this section to be active yet!
				$this->_deactivateSection($section['name']);
			}
			
			// Error checking
			$itemNode = $node->getElementsExt('opt', 'item');
			$emptyItemNode = $node->getElementsExt('opt', 'emptyItem');

			if(sizeof($itemNode) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:item', 'opt:grid', 'One');
			}
			if(sizeof($emptyItemNode) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:emptyItem', 'opt:grid', 'One');
			}
			
			// Link those nodes to this section
			$itemNode[0]->set('sectionName', $section['name']);
			$emptyItemNode[0]->set('sectionName', $section['name']);
			
			// Code generation			
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, '$_'.$section['name'].'_rows = ceil('.$section['format']->get('sectionCount').' / '.$section['cols'].'); $_'.$section['name'].'_remain = ('.$section['cols'].
			' - ('.$section['format']->get('sectionCount').' % '.$section['cols'].')) % '.$section['cols'].'; '.$section['format']->get('sectionRewind').' '.
			' for($_'.$section['name'].'_j = 0; $_'.$section['name'].'_j < $_'.$section['name'].'_rows; $_'.$section['name'].'_j++){ ');
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			
			$this->_process($node);
		} // end _processGrid();
		
		private function _processItem(Opt_Xml_Node $node)
		{
			if(is_null($node->get('sectionName')))
			{
				throw new Opt_InstructionInvalidLocation_Exception('opt:item', 'opt:grid');
			}
		
			// We're at home. For this particular node we have to activate the section.
			
			$section = $this->getSection($node->get('sectionName'));
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' for($_'.$section['name'].'_k = 0; $_'.$section['name'].'_k < '.$section['cols'].' && '.$section['format']->get('sectionValid').'; $_'.$section['name'].'_k++) { ');
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('sectionNext').' } ');
			
			$this->_activateSection($section['name']);
			$node->set('postprocess', true);
			
			$this->_process($node);
		} // end _processItem();
		
		private function _processEmptyItem(Opt_Xml_Node $node)
		{
			if(is_null($node->get('sectionName')))
			{
				throw new Opt_InstructionInvalidLocation_Exception('opt:item', 'opt:grid');
			}
			$section = $this->getSection($node->get('sectionName'));	
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' if($_'.$section['name'].'_remain > 0 && !'.$section['format']->get('sectionValid').') { for($_'.$section['name'].'_k = 0; $_'.$section['name'].'_k < $_'.$section['name'].'_remain; $_'.$section['name'].'_k++) { ');
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, ' } } ');
				
			$this->_process($node);
		} // end _processItem();
		
		private function _postprocessGrid(Opt_Xml_Element $node)
		{
			$section = $this->getSection($node->get('sectionName'));
			if($node->hasAttributes())
			{
				if(!$node->get('sectionElse'))
				{
					$this->_locateElse($node, 'opt', 'gridelse');
				}
			}
			$this->_finishSection($node);
		} // end _postprocessGrid();
		
		private function _postprocessItem(Opt_Xml_Element $node)
		{
			// Deactivating the section.
			$section = $this->getSection($node->get('sectionName'));
			$this->_deactivateSection($section['name']);
		} // end _postprocessItem();
		
		private function _processGridelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:grid')
			{
				$parent->set('sectionElse', true);
				
				$section = $this->getSection($parent->get('sectionName'));
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
				$this->_deactivateSection($parent->get('sectionName'));
				$this->_process($node);
			}
		} // end _processGridelse();
	} // end Opt_Instruction_Grid;