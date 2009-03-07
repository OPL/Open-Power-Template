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
 * $Id: Snippet.php 22 2008-12-03 11:32:29Z zyxist $
 */

	class Opt_Instruction_Snippet extends Opt_Compiler_Processor
	{
		protected $_name = 'snippet';
		protected $_snippets = array();	// Snippet database
		protected $_current = array();	// Currently processed snippets (infinite recursion detection)
		
		public function configure()
		{
			$this->_addInstructions(array('opt:snippet', 'opt:insert', 'opt:parent'));
			$this->_addAttributes(array('opt:use'));
		} // end configure();
		
		public function reset()
		{
			foreach($this->_snippets as &$snippetList)
			{
				foreach($snippetList as $snippet)
				{
					$snippet->dispose();
				}
			}
			$this->_snippets = array();
			$this->_current = array();
		} // end reset();
	
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
			if(isset($this->_snippets[$attr->getValue()]))
			{
				array_push($this->_current, $attr->getValue());
				$snippet = &$this->_snippets[$attr->getValue()];
				
				// Move all the stuff to the fake node.
				if($node->hasChildren())
				{
					$newNode = new Opt_Xml_Element('opt:_');
					$newNode->moveChildren($node);				
					
					$size = sizeof($snippet);
					$snippet[$size] = $newNode;

					$attr->set('snippetObj', $snippet);
					$attr->set('size', $size);
				}
				$node->removeChildren();
				
				// Process the snippets
				$node->set('escaping', $this->_compiler->get('escaping'));
				$this->_compiler->set('escaping', $snippet[0]->get('escaping'));
				foreach($snippet[0] as $subnode)
				{
					$node->appendChild(clone $subnode);
				}

				$node->set('call:use', $attr->getValue());
				$attr->set('postprocess', true);				
			}
		} // end processAttribute();
		
		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			if(!is_null($attr->get('size')))
			{
				$snippet = $attr->get('snippetObj');
				unset($snippet[$attr->get('size')]);
			}
			// Restore the original escaping state
			$this->_compiler->set('escaping', $node->get('escaping'));
			array_pop($this->_current);
		} // end postprocessAttribute();

		public function _processSnippet(Opt_Xml_Element $node)
		{
			$params = array(
				'name' => array(0 => self::REQUIRED, self::ID)		
			);
			$this->_extractAttributes($node, $params);
			
			// Assign this snippet					
			if(!isset($this->_snippets[$params['name']]))
			{
				$this->_snippets[$params['name']] = array(0 => $node);
				$current = 0;
			}
			else
			{
				$current = sizeof($this->_snippets[$params['name']]);

				$this->_snippets[$params['name']][] = $node;
			}
			if($node->getParent()->removeChild($node) == 0)
			{
				throw new Opl_Debug_Exception();
			}
			// Remember the template state of escaping for this snippet.
			// This is necessary to make per-template escaping work with
			// the inheritance.
			$node->set('escaping', $this->_compiler->get('escaping'));
				
			// Link "opt:parent" with the parent
			$parentTags = $node->getElementsByTagNameNS('opt', 'parent');
			foreach($parentTags as $parent)
			{
				$parent->set('snippetName', $params['name']);
				$parent->set('snippetId', $current + 1);
			}
		} // end _processSnippet();

		public function _processParent(Opt_Xml_Element $node)
		{
			$n = $node->get('snippetName');
			$i = $node->get('snippetId');
			// If there is a parent, append it here and execute.
			if(isset($this->_snippets[$n][$i]))
			{
				$node->set('escaping', $this->_compiler->get('escaping'));
				$node->set('single', false);
				$this->_compiler->set('escaping', $this->_snippets[$n][$i]->get('escaping'));
				foreach($this->_snippets[$n][$i] as $subnode)
				{
					$node->appendChild(clone $subnode);
				}
				$node->set('postprocess', true);
				$this->_process($node);
			}
		} // end _processParent();
		
		public function _postprocessParent(Opt_Xml_Element $node)
		{
			$this->_compiler->set('escaping', $node->get('escaping'));
		} // end _postprocessParent();

		public function _processInsert(Opt_Xml_Element $node)
		{
			$params = array(
				'snippet' => array(0 => self::REQUIRED, self::ID),		
				'ignoredefault' => array(0 => self::OPTIONAL, self::BOOL, false)
			);
			$this->_extractAttributes($node, $params);

			if(in_array($params['snippet'], $this->_current))
			{
				array_push($this->_current, $params['snippet']);
				$err = new Opt_SnippetRecursion_Exception($params['snippet']);
				throw $err->setData($this->_current);
			}
			if(isset($this->_snippets[$params['snippet']]))
			{			
				array_push($this->_current, $params['snippet']);
				$snippet = &$this->_snippets[$params['snippet']];
				
				// Move all the stuff to the fake node.
				if($node->hasChildren() && $params['ignoredefault'] == false)
				{
					$newNode = new Opt_Xml_Element('opt:_');
					$newNode->set('escaping', $this->_compiler->get('escaping'));
					$newNode->moveChildren($node);				
					$size = sizeof($snippet);
					$snippet[$size] = $newNode;
					$node->set('insertSize', $size);
				}
				// We must do the cleaning for the inserted node.
				$node->removeChildren();
				
				// Process the snippets
				$node->set('escaping', $this->_compiler->get('escaping'));
				$this->_compiler->set('escaping', $snippet[0]->get('escaping'));

				foreach($snippet[0] as $subnode)
				{
					$node->appendChild(clone $subnode);
				}
				$this->_process($node);
				$node->set('insertSnippet', $params['snippet']);
				$node->set('postprocess', true);
			}
			else
			{
				// Processing the default content - snippet not found.
				$this->_process($node);
			}
		} // end _processInsert();
		
		public function _postprocessInsert(Opt_Xml_Element $node)
		{
			// Freeing the fake node, if necessary.
			if(!is_null($node->get('insertSize')))
			{
				$this->_snippets[$node->get('insertSnippet')][$node->get('insertSize')]->dispose();
				unset($this->_snippets[$node->get('insertSnippet')][$node->get('insertSize')]);
			}

			// Restore the original escaping state
			$this->_compiler->set('escaping', $node->get('escaping'));
			
			array_pop($this->_current);
		} // end _postprocessInsert();
		
		public function isSnippet($name)
		{
			return isset($this->_snippets[$name]);
		} // end isSnippet();		
	} // end Opt_Instruction_Snippet;