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
 * $Id: Section.php 19 2008-11-20 16:09:45Z zyxist $
 */

	class Opt_Instruction_Section extends Opt_Instruction_BaseSection
	{
		protected $_name = 'section';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:section', 'opt:sectionelse', 'opt:show', 'opt:showelse'));
			$this->_addAttributes('opt:section');
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
		
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_processAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end processAttribute();
		
		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_postprocessAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end postprocessAttribute();

		private function _processSection(Opt_Xml_Element $node)
		{
			if(is_null($section = $this->_sectionInitialized($node)))
			{
				$section = $this->_processShow($node);
				$initialized = true;
			}
			if($section['order'] == 'asc')
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('sectionStartAscLoop'));
			}
			else
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('sectionStartDescLoop'));
			}
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);

			$node->set('postprocess', true);
			$this->_process($node);
		} // end _processSection();
		
		private function _postprocessSection(Opt_Xml_Element $node)
		{			
			$section = $this->getSection($node->get('sectionName'));
			if(!$node->get('sectionElse'))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('sectionEndLoop'));
			}
			if($node->hasAttributes())
			{
				if(!$node->get('sectionElse'))
				{
					$this->_locateElse($node, 'opt', 'sectionelse');
				}
			}
			$this->_finishSection($node);
		} // end _postprocessSection();
		
		private function _processShowelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:show')
			{
				$parent->set('sectionElse', true);
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
				$this->_process($node);
			}
		} // end _processShowelse();

		private function _processSectionelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:section')
			{
				$parent->set('sectionElse', true);
				
				$section = $this->getSection($parent->get('sectionName'));
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('sectionEndLoop').' } else { ');
				$this->_deactivateSection($parent->get('sectionName'));
				$this->_process($node);
			}
		} // end _processSectionelse();
		
		private function _processAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			// TODO: Check attributed sections and single tags. In opt:put this did not work.
			if(is_null($section = $this->_sectionInitialized($node, $attr)))
			{
				$section = $this->_processShow($node, $attr);
				$initialized = true;
			}
			if($section['order'] == 'asc')
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('sectionStartAscLoop'));
			}
			else
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('sectionStartDescLoop'));
			}
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);
			$attr->set('postprocess', true);
		} // end _processAttrSection();
		
		private function _postprocessAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = $this->getSection($node->get('sectionName'));
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('sectionEndLoop'));
			$this->_finishSection($node);
		} // end _postprocessAttrSection();
	} // end Opt_Instruction_Section;