<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

/**
 * This processor implements the opt:tree instruction.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 * @subpackage Sections
 */
class Opt_Instruction_Tree extends Opt_Instruction_Section_Abstract
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var String
	 */
	protected $_name = 'tree';

	/**
	 * Configures the processor.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions('opt:tree');
		$this->_addAmbiguous(array(
			'opt:else' => 'opt:tree',
			'opt:body' => 'opt:tree'
		));
	} // end configure();

	/**
	 * Migrates the opt:tree node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function _migrateTree(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end _migrateTree();

	/**
	 * Processes the opt:tree node.
	 * @internal
	 * @param Opt_Xml_Node $node The instruction node found by the compiler.
	 */
	protected function _processTree(Opt_Xml_Node $node)
	{
		// First, do the section stuff.
		$section = $this->_sectionCreate($node);
		$this->_sectionStart($section);

		if($node->get('ambiguous:opt:body') !== null)
		{
			$treeElse = $node->get('ambiguous:opt:else');
			if($treeElse instanceof Opt_Xml_Element && $treeElse->getParent()->getXmlName() != 'opt:tree')
			{
				throw new Opt_Instruction_Exception('Invalid opt:else location in opt:tree.');
			}

			$this->_process($node);
		}
		else
		{
			$this->_processBody($node);
		}
	} // end _processTree();

	/**
	 * Processes the opt:body node.
	 * @internal
	 * @param Opt_Xml_Node $node The instruction node found by the compiler.
	 */
	protected function _processBody(Opt_Xml_Element $node)
	{
		$section = self::getSection($node->get('priv:section'));

		// Check the tag structure and get the tags.
		$stList = $node->getElementsByTagNameNS('opt', 'list', false);
		$stNode = $node->getElementsByTagNameNS('opt', 'node', false);
		$stTreeElse = $node->getElementsByTagNameNS('opt', 'else', false);
		if(sizeof($stList) != 1)
		{
			throw new Opt_Instruction_Exception('opt:tree error: opt:list missing.');
		}
		if(sizeof($stNode) != 1)
		{
			throw new Opt_Instruction_Exception('opt:tree error: opt:node missing.');
		}
		if($node->getXmlName() == 'opt:body')
		{
			if(sizeof($stTreeElse) != 0)
			{
				throw new Opt_Instruction_Exception('Cannot place opt:else in opt:body.');
			}
		}
		else
		{
			if(sizeof($stTreeElse) > 1)
			{
				throw new Opt_Instruction_Exception('Too many opt:else in opt:tree: zero or one expected.');
			}
		}
		// Show "opt:list" and "opt:node" tags
		$stList = $stList[0];
		$stNode = $stNode[0];
		$stList->set('hidden', false);
		$stNode->set('hidden', false);

		// Reorganize the XML tree structure.
		$node->removeChildren();
		$node->appendChild($stList);
		$node->appendChild($stNode);
		if(isset($stTreeElse[0]))
		{
			$node->appendChild($stTreeElse[0]);
			$stTreeElse[0]->set('hidden', false);
		}

		$stListContent = $stList->getElementsByTagNameNS('opt', 'content');
		$stNodeContent = $stNode->getElementsByTagNameNS('opt', 'content');
		if(sizeof($stListContent) != 1)
		{
			throw new Opt_Instruction_Exception('opt:tree error: opt:content in opt:list missing.');
		}
		if(sizeof($stNodeContent) != 1)
		{
			throw new Opt_Instruction_Exception('opt:tree error: opt:content in opt:node missing.');
		}
		$content = array(
			'list' => $stListContent[0],
			'node' => $stNodeContent[0]
		);

		// Check the PHP buffers. Neither opt:list nor opt:node must have an instruction tag that
		// Is wrapped around opt:content, because this would certainly produce an invalid PHP code.
		// opt:content is used here as a separator in a big "switch" statement, so it must not be
		// enclosed in any PHP curly bracket block.
		$test = array(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_AFTER, Opt_Xml_Buffer::TAG_CONTENT_BEFORE,
			Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CONTENT);
		foreach($content as $id => $tag)
		{
			$tag->set('hidden', false);
			$tag = $tag->getParent();
			while($tag->getName() != 'node' && $tag->getName() != 'list')
			{
				if($tag->getNamespace() == 'opt')
				{
					throw new Opt_Instruction_Exception('opt:tree error: '.$tag->getXmlName().' is a dynamic tag that generates some PHP code.');
				}
				foreach($test as $buffer)
				{
					if($tag->bufferSize($buffer) > 0)
					{
						throw new Opt_Instruction_Exception('opt:tree error: '.$tag->getXmlName().' is a dynamic tag that generates some PHP code.');
					}
				}
				$tag = $tag->getParent();
			}
		}
		// Select the data format
		$format = $this->_compiler->getFormat('tree#'.$section['name'], false, $this->_tpl->treeFormat);
		
		if(!$format->supports('tree'))
		{
			throw new Opt_Format_Exception('The format '.$format->getName().' does not support "tree" type.');
		}

		$format->assign('section', $section);

		// Now, generate the source code.
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $format->get('tree:before'));
		// Add the four case code.
		$stList->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $format->get('tree:case1:before'));
		$content['list']->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $format->get('tree:case1:after'));
		$content['list']->addBefore(Opt_Xml_Buffer::TAG_AFTER, $format->get('tree:case4:before'));
		$stList->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('tree:case4:after'));
		$stNode->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $format->get('tree:case2:before'));
		$content['node']->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $format->get('tree:case2:after'));
		$content['node']->addBefore(Opt_Xml_Buffer::TAG_AFTER, $format->get('tree:case3:before'));
		$stNode->addAfter(Opt_Xml_Buffer::TAG_AFTER, $format->get('tree:case3:after').' '.$format->get('tree:after'));

		$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);

		$node->set('postprocess', true);
		$this->_process($node);
		$this->_process($stList);
		$this->_process($stNode);
	} // end _processBody();

	/**
	 * Postprocesses the opt:body node.
	 * @internal
	 * @param Opt_Xml_Element $node The node found by the compiler.
	 */
	protected function _postprocessBody(Opt_Xml_Element $node)
	{
		$this->_postprocessTree($node);
	} // end _postprocessBody();

	/**
	 * Postprocesses the opt:tree node.
	 * @internal
	 * @param Opt_Xml_Element $node The node found by the compiler.
	 */
	protected function _postprocessTree(Opt_Xml_Element $node)
	{
		$section = $this->getSection($node->get('sectionName'));
		if($node->hasAttributes())
		{
			if(!$node->get('sectionElse'))
			{
				$this->_sortSectionContents($node, 'opt', 'else');
			}
		}
		$this->_sectionEnd($node);
	} // end _postprocessTree();

	/**
	 * Processes opt:treeelse node.
	 * @internal
	 * @param Opt_Xml_Element $node The instruction node found by the compiler.
	 */
	protected function _processElse(Opt_Xml_Element $node)
	{
		$parent = $node->getParent();
		if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:tree')
		{
			$parent->set('sectionElse', true);

			$section = $this->getSection($parent->get('sectionName'));
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
			$this->_process($node);
		}
	} // end _processElse();
} // end Opt_Instruction_Tree;
