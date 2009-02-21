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
 * $Id: BaseSection.php 22 2008-12-03 11:32:29Z zyxist $
 */

	abstract class Opt_Instruction_BaseSection extends Opt_Instruction_Loop
	{
		static private $_sections = array();
		static private $_stack;
		
		public function __construct($compiler)
		{
			parent::__construct($compiler);
			self::$_stack = new SplStack;		
		} // end __construct();
		
		protected function _addSection($name, Array $info)
		{
			if(isset(self::$_sections[$name]))
			{
				throw new Opt_SectionExists_Exception('adding section', $name);
			}
			self::$_sections[$name] = $info;
			self::$_stack->push($name);
		} // end _addSectionInfo();
		
		public function getSection($name)
		{
			if(!isset(self::$_sections[$name]))
			{
				return NULL;
			}
			return self::$_sections[$name];
		} // end getSection();
		
		public function getLastSection()
		{
			return $this->getSection(self::$_stack->top());
		} // end getLastSection();
		
		protected function _removeSection($name)
		{
			if(self::$_stack->count() == 0)
			{
				throw new Opt_ObjectNotExists_Exception('section', $name);
			}
			$name2 = self::$_stack->pop();
			if($name != $name2)
			{
				throw new Opl_Debug_Generic_Exception('OPT: Invalid section name thrown from the stack. Expected: '.$name.'; Actual: '.$name2);
			}
			unset(self::$_sections[$name]);
		} // end _removeSection;
		
		public function countSections()
		{
			return self::$_stack->count();
		} // end countSections();
		
		protected function _activateSection($name)
		{
			if(isset($this->_sections[$name]))
			{
				$this->_sections[$name]['active'] = true;
			}			
		} // end _activateSection();
		
		protected function _deactivateSection($name)
		{
			if(isset($this->_sections[$name]))
			{
				$this->_sections[$name]['active'] = false;
			}			
		} // end _deactivateSection();
		
		protected function _sectionInitialized(Opt_Xml_Element $node, $attr = NULL)
		{
			if($attr instanceof Opt_Xml_Attribute)
			{
				// For attribute sections
				self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
				foreach(self::$_stack as $up)
				{
					if(!self::$_sections[$up]['active'] && $up == $attr->getValue())
					{
						$node->set('sectionName', $up);

						if(!is_null($node->get('call:use')))
						{
							$this->_compiler->setConversion('##simplevar_'.$node->get('call:use'), self::$_sections[$up]['name']);
						}
						
						return self::$_sections[$up];
					}
				}
				return NULL;
			}
			else
			{
				// Only non-attributed tags can be initialized earlier.
				if($node->hasAttributes())
				{
					return NULL;
				}
				
				self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
				foreach(self::$_stack as $up)
				{
					if(!self::$_sections[$up]['active'])
					{
						$node->set('sectionName', $up);
						self::$_sections[$up]['active'] = true;
						
						if(!is_null($node->get('call:use')))
						{
							$this->_compiler->setConversion('##simplevar_'.$node->get('call:use'), self::$_sections[$up]['name']);
						}
						
						return self::$_sections[$up];
					}
				}
				// Don't throw any exception. As there are no parameters, the user
				// still will get it, because the "name" attribute is not set.
				return NULL;
			}		
		} // end _sectionInitialized();
		
		protected function _finishSection(Opt_Xml_Element $node)
		{
			$name = $node->get('sectionName');
			if(!is_null($name))
			{
				$this->_compiler->unsetConversion('##simplevar_'.$node->get('sectionName'));
			
				if(self::$_sections[$name]['show'])
				{
					self::$_sections[$name]['active'] = false;
				}
				else
				{
					$this->_postprocessShow($node);
				}
				return true;
			}
			return false;
		} // end _finishSection();

		protected function _processShow(Opt_Xml_Element $node, $attr = NULL, $additionalArgs = NULL)
		{
			if(!$attr instanceof Opt_Xml_Attribute)
			{
				$params = array(
					'name' => array(0 => self::REQUIRED, self::ID),
					'parent' => array(0 => self::OPTIONAL, self::ID_EMP, NULL),
					'datasource' => array(0 => self::OPTIONAL, self::EXPRESSION, NULL),
					'order' => array(0 => self::OPTIONAL, self::ID, 'asc'),
					'display' => array(0 => self::OPTIONAL, self::EXPRESSION, NULL),
					'separator' => array(0 => self::OPTIONAL, self::EXPRESSION, NULL),
				);
				
				// The instruction may add some extra attributes.
				if(!is_null($additionalArgs))
				{
					$params = array_merge($params, $additionalArgs);
				}
				
				$this->_extractAttributes($node, $params);
				
				/*
				 * Verify the attributes
				 */
				if(is_null($params['datasource']))
				{
					// We have to find if the section has any parent.
					// if the attribute is not set, the default behaviour is to find the nearest
					// top-level and active section and link to it. Otherwise we must check if
					// the chosen section exists and is active.
					// Note that "parent" is ignored when we set "datasource"
					if(is_null($params['parent']))
					{
						self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
						foreach(self::$_stack as $up)
						{
							if(self::$_sections[$up]['active'])
							{
								$params['parent'] = $up;
								break;
							}
						}
					}
					elseif($params['parent'] != '')
					{
						self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
						$ok = false;
						foreach(self::$_stack as $up)
						{
							if($up == $params['parent'] && self::$_sections[$up]['active'])
							{
								$ok = true;
								break;
							}
						}
						if(!$ok)
						{
							throw new Opt_SectionExists_Exception('parent', $params['parent']);
						}
					}
					else
					{
						// For the situation, if we had 'parent=""' in the template.
						$params['parent'] = null;
					}
				}
				else
				{
					$params['parent'] = NULL;
				}
				
				// Verify the value of the "order" attribute.
				if($params['order'] != 'asc' && $params['order'] != 'desc')
				{
					throw new Opt_InvalidAttributeType_Exception('order', $node->getXmlName(), '"asc" or "desc"');
				}
				
				// Check whether the section is active or not
				if($node->getXmlName() != 'opt:show')
				{
					$params['active'] = true;
					$params['show'] = false;
				}
				else
				{
					$params['active'] = false;
					$params['show'] = true;
				}
				$node->set('postprocess', true);
			}
			else
			{
				// Setting up attribute sections is much simpler. All we have to
				// do is to initialize it with default values and find the parent.
				$_params = array(
					'separator' => array(0 => self::OPTIONAL, self::EXPRESSION, NULL),
				);
				$this->_extractAttributes($node, $_params);
			
				$params = array(
					'name' => $attr->getValue(),
					'order' => 'asc',
					'parent' => NULL,
					'datasource' => NULL,
					'display' => NULL,
					'separator' => $_params['separator'],
					'show' => false,
					'active' => true			
				);
				self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
				foreach(self::$_stack as $up)
				{
					if(self::$_sections[$up]['active'])
					{
						$params['parent'] = $up;
						break;
					}
				}
				$attr->set('postprocess', true);	
			}
			
			$params['nesting'] = $this->countSections() + 1;
			
			/*
			 * Choose the hooks
			 */
			$format = $params['format'] = $this->_compiler->getFormat($params['name']);
			if(!$format->supports('section'))
			{
				throw new Opt_FormatNotSupported($format->getName(), 'section');
			}
			$format->assign('sectionName', $params['name']);
			$format->assign('sectionNest', $params['nesting']);
			
			// Now, the parent hook class and determining where to get the section data from.
			if(!is_null($params['parent']))
			{
				$pf = self::$_sections[$params['parent']]['format'];
			}
			
			/*
			 * Register section
			 */
			$this->_addSection($params['name'], $params);
			$node->set('sectionName', $params['name']);
			
			/*
			 * Generate the SHOW code
			 */
			
			$code = '';
		/*	if(isset($ph))
			{
				$code .= $format->get('sectionRecordInit');
			}
			else
			{
				$code .= $format->get('sectionTopRecordInit');
			}*/
			if(isset($params['datasource']))
			{
				$format->assign('datasource', true);
				$format->assign('parentRecord', $params['datasource']);
			}
			elseif(!isset($pf))
			{
				$info = $this->_compiler->createFormat($params['name'], 'Generic');
				$info->assign('item', $params['name']);
				$format->assign('parentRecord', $info->get('variableMain'));
			}
			else
			{
				$pf->assign('_sectionItemName', $params['name']);
				$format->assign('parentRecord', $pf->get('itemVariable'));				
			}
			// TODO: Add support for references, because they are missing now.

			if($format->property('needsAncestors') === true)
			{
				// Some of the formats may need a list of ancestors in order
				// To generate the relationship properly. This code builds
				// such a list for them using the information contained on
				// the stack to enumerate them.
				if(is_null($params['parent']))
				{
					$format->assign('ancestors', array());
				}
				else
				{
					self::$_stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
					$ancestors = array();
					$parent = $params['parent'];
					$iteration = self::$_stack->count();
					foreach(self::$_stack as $up)
					{
						if($up == $parent)
						{
							$parent = self::$_sections[$up]['parent'];
							$ancestors[] = $iteration;
						}
						$iteration--;
					}
					$format->assign('ancestors', array_reverse($ancestors));
				}
			}

			$format->assign('sectionRecordCall', $format->get('sectionRecordCall'));
			$code .= $format->get('sectionInit');
			
			if(!is_null($params['display']))
			{
				$code .= ' if('.$format->get('sectionCondition').' && '.$params['display'].'){ ';
			}
			else
			{
				$code .= ' if('.$format->get('sectionCondition').'){ ';
			}
			
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $code);

			if(!$params['show'])
			{
				if(!is_null($node->get('call:use')))
				{
					$this->_compiler->setConversion('##simplevar_'.$node->get('call:use'), $params['name']);
				}
				return $params;
			}
			else
			{
				$this->_process($node);
			}
		} // end _processShow();
		
		protected function _postprocessShow(Opt_Xml_Element $node)
		{
			// We must get the section information again
			$params = self::$_sections[$node->get('sectionName')];
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			$this->_removeSection($node->get('sectionName'));
		} // end _postprocessShow();
		
		protected function _locateElse(Opt_Xml_Element $node, $ns, $name)
		{
			$else = $node->getElementsByTagNameNS($ns, $name, false);
			
			if(sizeof($else) > 0)
			{
				if($node->hasAttributes())
				{
					$node->sort(array(0=>'*', $name));
				}
				else
				{
					throw new Opt_InstructionTooManyItems_Exception($ns.':'.$name, $node->getXmlName(), 'Zero');
				}
			}
		} // end _locateElse();
		
		public function processSystemVar($opt)
		{
			if(sizeof($opt) < 4)
			{
				throw new Opt_SysVariableLength_Exception('$'.implode('.',$opt), 'short');
			}
			// Determine the section
			$section = $this->getSection($opt[2]);
			if(is_null($section) || !$section['active'])
			{
				throw new Opt_SectionNotFound_Exception('OPT block $'.implode('.',$opt), $opt[2]);
			}
			$section['format']->assign('sectionNest', $section['nesting']);
			switch($opt[3])
			{
				case 'count':					
					return $section['format']->get('sectionCount');
				case 'id':
					return $section['format']->get('sectionIterator');
				case 'size':
					return $section['format']->get('sectionSize');
				case 'first':
					if($section['order'] == 'desc')
					{
						return $section['format']->get('sectionOptFirstDesc');
					}
					return $section['format']->get('sectionOptFirstAsc');
				case 'last':
					if($section['order'] == 'desc')
					{
						return $section['format']->get('sectionOptLastDesc');
					}
					return $section['format']->get('sectionOptLastAsc');
				case 'far':
					return $section['format']->get('sectionOptFar');
				default:
					$result = $this->_processSystemVar($opt);
					if(is_null($result))
					{
						throw new Opt_SysVariableUnknown_Exception('$'.implode('.',$opt));
					}
					return $result;
			}
		} // end processSystemVar();
		
		protected function _processSystemVar($opt)
		{
			return NULL;
		} // end _processSystemVar();
	} // end Opt_Instruction_BaseSection;
