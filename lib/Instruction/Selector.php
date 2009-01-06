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
 * $Id: Selector.php 23 2008-12-03 14:11:58Z extremo $
 */

	class Opt_Instruction_Selector extends Opt_Instruction_BaseSection
	{
		protected $_name = 'section';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:selector', 'opt:selectorelse'));
			$this->_addAttributes('opt:selector');
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			if(is_null($section = $this->_sectionInitialized($node)))
			{
				$section = $this->_processShow($node, NULL, array('test' => array(self::OPTIONAL, self::ID, 'item')));
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
			
			$this->_internalMagic($node, $section, 0);

			$node->set('postprocess', true);			
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{			
			$section = $this->getSection($node->get('sectionName'));
			if(!$node->get('sectionElse'))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('sectionEndLoop'));
			}
			if($node->hasAttributes())
			{
				$this->_postprocessShow($node);
				if(!$node->get('sectionElse'))
				{
					$this->_locateElse($node, 'opt', 'selectorelse');
				}
			}
		} // end postprocessNode();
		
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
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
			
			$this->_internalMagic($node, $section, 1);
			
			$attr->set('postprocess', true);
		} // end processAttribute();
		
		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = $this->getSection($node->get('sectionName'));
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('sectionEndLoop'));
			if(!$section['show'])
			{
				$this->_postprocessShow($node);
			}
		} // end postprocessAttribute();
		
		private function _internalMagic($node, &$section, $type)
		{
			$section['format']->assign('_sectionItemName', (!$type ? $section['test'] : 'item'));
			$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'switch('.$section['format']->get('_itemFullVariable').'){');
			$node->addBefore(Opt_Xml_Buffer::TAG_CONTENT_AFTER, ' } ');
			foreach($node as $subnode)
			{
				if($subnode instanceof Opt_Xml_Element && $this->_compiler->isNamespace($subnode->getNamespace()))
				{
					if($subnode->getName() == 'default')
					{
						$subnode->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' default: ');
					}
					else
					{
						$subnode->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' case \''.$subnode->getName().'\': ');
					}
					$subnode->addBefore(Opt_Xml_Buffer::TAG_CONTENT_AFTER, ' break; ');
					if(!$type)
					{
						$this->_process($subnode);
					}
					else
					{
						$subnode->set('processAll', true);
					}
				}
				else
				{
					$node->removeChild($subnode);
				}
			}
		} // end _internalMagic();
	} // end Opt_Instruction_Selector;
