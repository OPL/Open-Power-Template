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
 * $Id: Include.php 22 2008-12-03 11:32:29Z zyxist $
 */

	class Opt_Instruction_Include extends Opt_Compiler_Processor
	{
		protected $_name = 'include';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:include'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'file' => array(0 => self::OPTIONAL, self::STRING, null),
				'view' => array(0 => self::OPTIONAL, self::EXPRESSION, NULL),
				'from' => array(0 => self::OPTIONAL, self::ID, NULL),

				'default' => array(0 => self::OPTIONAL, self::STRING, NULL),
				'import' => array(0 => self::OPTIONAL, self::BOOL, NULL),
				'branch' => array(0 => self::OPTIONAL, self::STRING, NULL),
				'__UNKNOWN__' => array(0 => self::OPTIONAL, self::EXPRESSION)
			);
			$vars = $this->_extractAttributes($node, $params);

			// Conditional attribute control.
			if(!isset($params['from']) && !isset($params['file']) && !isset($params['view']))
			{
				throw new Opt_IncludeNoAttributes($node->getXmlName());
			}
			// Possible section integration
			$codeBegin = '';
			$codeEnd = '';
			
			if(isset($params['from']))
			{
				$section = Opt_Instruction_BaseSection::getSection($params['from']);

				if(is_null($section))
				{
					throw new Opt_SectionNotFound_Exception('opt:include', $params['from']);
				}
				$section['format']->assign('item', 'view');
				$view = $section['format']->get('section:variable');
				
				// TODO: File-specific blocks and variables!!!
			}
			
			if(isset($params['view']))
			{
				$view = $params['view'];
			}
			elseif(isset($params['file']))
			{
				$codeBegin = '$view = new Opt_View('.$params['file'].');';
				$view = '$view';
				$codeEnd = ' unset($view); ';
			}
			// Compile the import
			if($params['import'] == 'yes')
			{
				if(isset($params['file']))
				{
					$codeBegin .= $view.'->_data = $this->_data; ';
				}
				else
				{
					$codeBegin .= $view.'->_data = array_merge('.$view.'->_data, $this->_data); ';
				}
			}
			foreach($vars as $name => $value)
			{
				$codeBegin .= $view.'->'.$name.' = '.$value.'; ';
			}
			
			if(isset($params['branch']))
			{
				$codeBegin .= $view.'->setBranch('.$params['branch'].'); ';
			}
			
			if(!is_null($params['default']))
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $codeBegin.' if(!'.$view.'->_parse($output, false)){ '.$view.'->_template = '.$params['default'].'; '.$view.'->_parse($output, true); } '.$codeEnd);
			}
			elseif($node->hasChildren())
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, $codeBegin.' if(!'.$view.'->_parse($output, false)){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, ' } '.$codeEnd);
				$this->_process($node);
			}
			else
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $codeBegin.'  '.$view.'->_parse($output, $exception); '.$codeEnd);
			}
		} // end processNode();
	} // end Opt_Instruction_Include;