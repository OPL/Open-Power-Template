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

	class Opt_Instruction_Selector extends Opt_Instruction_BaseSection
	{
		protected $_name = 'section';
		protected $_extraAttributes = array('test' => array(self::OPTIONAL, self::ID, 'item'));
		
		public function configure()
		{
			$this->_addInstructions(array('opt:selector', 'opt:selectorelse'));
			$this->_addAttributes('opt:selector');
		} // end configure();
	
		protected function _processSelector(Opt_Xml_Node $node)
		{
			$section = $this->_sectionCreate($node, null, array('test' => array(self::OPTIONAL, self::ID, 'item')));
			$this->_sectionStart($section);

			if($section['order'] == 'asc')
			{
				$code = $section['format']->get('section:startAscLoop');
			}
			else
			{
				$code = $section['format']->get('section:startDescLoop');
			}
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $code);
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);
			
			$this->_internalMagic($node, $section, 0);

		//	$this->_sortSectionContents($node, 'opt', 'selectorelse');

			$node->set('postprocess', true);
			$this->_process($node);
		} // end _processSelector();
		
		protected function _postprocessSelector(Opt_Xml_Node $node)
		{			
			$section = self::getSection($node->get('priv:section'));
			if(!$node->get('priv:alternative'))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
				$this->_sectionEnd($node);
			}
		} // end _postprocessSelector();

		protected function _processSelectorelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:selector')
			{
				$parent->set('priv:alternative', true);

				$section = self::getSection($parent->get('priv:section'));
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } '.$section['format']->get('section:endLoop').' } else { ');

				$this->_sectionEnd($parent);

				$this->_process($node);
			}
			else
			{
				throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:section');
			}
		} // end _processSelectorelse();
		
		protected function _processAttrSelector(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = $this->_sectionCreate($node, $attr, array('test' => array(self::OPTIONAL, self::ID, 'item')));
			$this->_sectionStart($section);
			$code = '';
			if($section['order'] == 'asc')
			{
				$code .= $section['format']->get('section:startAscLoop');
			}
			else
			{
				$code .= $section['format']->get('section:startDescLoop');
			}
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $code);
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);
			
			$this->_internalMagic($node, $section, 1);
			
			$attr->set('postprocess', true);
		} // end _processAttrSelector();
		
		protected function _postprocessAttrSelector(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = self::getSection($node->get('priv:section'));
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
			$this->_sectionEnd($node);
		} // end _postprocessAttrSelector();
		
		private function _internalMagic($node, &$section, $type)
		{
			$section['format']->assign('item', (!$type ? $section['test'] : 'item'));

			// Check, if there are no instruction tags in the children list.
			$instructions = array();
			$cases = array();
			$alternative = null;
			foreach($node as $subnode)
			{
				if($subnode instanceof Opt_Xml_Element && $this->_compiler->isNamespace($subnode->getNamespace()))
				{
					if($this->_compiler->isInstruction($subnode->getXmlName()) || $subnode->getXmlName() == 'opt:separator')
					{
						if($subnode != 'opt:selectorelse')
						{
							$instructions[] = $subnode;
						}
						else
						{
							if(!is_null($alternative))
							{
								throw new Opt_InstructionTooManyItems_Exception('opt:selectorelse', $node->getXmlName(), 'Zero or one');
							}
							$alternative = $subnode;
						}
					}
					else
					{
						$cases[] = $subnode;
					}
				}
				else
				{
					$node->removeChild($subnode);
				}
			}
			if(sizeof($instructions) > 0)
			{
				// There are instructions in opt:selector. We have to move the
				// cases to a fake node in order to sanitize them.

				$node->removeChildren();
				foreach($instructions as $instruction)
				{
					$node->appendChild($instruction);
				}

				$fake = new Opt_Xml_Element('opt:_');
				foreach($cases as $case)
				{
					$fake->appendChild($case);
				}
				$fake->set('processAll', true);
				$fake->set('hidden', false);
				$node->appendChild($fake);
				if(!is_null($alternative))
				{
					$node->appendChild($alternative);
				}
			}
			else
			{
				$fake = $node;
			}

			$fake->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'switch('.$section['format']->get('section:variable').'){');
			// If opt:selectorelse is used, the ending curly bracket is created by
			// _processSelectorelse().
			if(is_null($alternative))
			{
				$fake->addBefore(Opt_Xml_Buffer::TAG_CONTENT_AFTER, ' } ');
			}
			foreach($cases as $case)
			{
				if($case->getXmlName() == 'opt:separator')
				{
					Opl_Debug::write('Print shit');
				}
				if($case->getName() == 'default')
				{
					$case->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' default: ');
				}
				else
				{
					$case->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' case \''.$case->getName().'\': ');
				}
				$case->addBefore(Opt_Xml_Buffer::TAG_CONTENT_AFTER, ' break; ');
				$case->set('processAll', true);
				$case->set('hidden', false);
			}
		} // end _internalMagic();
	} // end Opt_Instruction_Selector;
