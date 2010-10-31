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
 * The processor for opt:grid instruction.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 * @subpackage Sections
 */
class Opt_Instruction_Grid extends Opt_Instruction_Section_Abstract
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'grid';

	/**
	 * The extra instruction attributes - required by the section API.
	 * @internal
	 * @var array
	 */
	protected $_extraAttributes = array('cols' => array(self::REQUIRED, self::EXPRESSION));

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:grid', 'opt:item', 'opt:empty-item'));
		$this->_addAmbiguous(array(
			'opt:else' => 'opt:grid',
			'opt:body' => 'opt:grid'
		));
	} // end configure();

	/**
	 * Migrates the opt:grid node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function _migrateGrid(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end _migrateGrid();

	/**
	 * Processes the opt:grid tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _processGrid(Opt_Xml_Node $node)
	{
		$section = $this->_sectionCreate($node, array(), array('cols' => array(self::REQUIRED, self::EXPRESSION)));

		if($node->hasAmbiguousDescendant('opt:body'))
		{
			$body = $node->getAmbiguousDescendant('opt:body');
			$body->set('priv:grid', $section);

			// This postprocessing is necessary. If there is opt:else, it must be sorted
			// properly.
			$node->set('postprocess', true);
			$this->_process($node);
		}
		else
		{
			$this->_processBody($node, $section);
		}
	} // end _processGrid();

	/**
	 * Processes the opt:body tag for opt:grid.
	 *
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 * @param array $section A workaround for not-registered section when calling from _processGrid().
	 */
	protected function _processBody(Opt_Xml_Element $node, array $section = null)
	{
		if($section === null)
		{
			$section = $node->get('priv:grid');
		}
		// Error checking
		$itemNode = $node->getElementsExt('opt', 'item');
		$emptyItemNode = $node->getElementsExt('opt', 'empty-item');

		if(sizeof($itemNode) != 1)
		{
			throw new Opt_Instruction_Exception('Too many opt:item elements within opt:grid: one required.');
		}
		if(sizeof($emptyItemNode) != 1)
		{
			throw new Opt_Instruction_Exception('Too many opt:empty-item elements within opt:grid: one required.');
		}

		// Link those nodes to this section
		$itemNode[0]->set('priv:section', $section);
		$emptyItemNode[0]->set('priv:section', $section);

		$emptyItemNode[0]->set('priv:valid', $section['format']->get('section:valid'));

		// Code generation
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, '$_'.$section['name'].'_rows = ceil('.$section['format']->get('section:count').' / '.$section['cols'].'); $_'.$section['name'].'_remain = ('.$section['cols'].
		' - ('.$section['format']->get('section:count').' % '.$section['cols'].')) % '.$section['cols'].'; '.$section['format']->get('section:loopBefore').' '.$section['format']->get('section:reset').' '.
		' for($_'.$section['name'].'_j = 0; $_'.$section['name'].'_j < $_'.$section['name'].'_rows; $_'.$section['name'].'_j++){ ');

		$node->set('postprocess', true);
		$this->_process($node);
	} // end _processBody();

	/**
	 * Post-processes the opt:body tag for opt:grid.
	 *
	 * @internal
	 * @param Opt_Xml_Element $node The opt:body tag.
	 */
	protected function _postprocessBody(Opt_Xml_Element $node)
	{
		$section = $node->get('priv:section');
		if(!$node->get('priv:alternative'))
		{
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
		}
	} // end _postprocessBody();

	/**
	 * Processes the opt:item tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _processItem(Opt_Xml_Node $node)
	{
		if(is_null($node->get('priv:section')))
		{
			throw new Opt_Instruction_Exception('opt:item should be located in opt:grid.');
		}

		// We're at home. For this particular node we have to activate the section.

		$section = $node->get('priv:section');
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' for($_'.$section['name'].'_k = 0; $_'.$section['name'].'_k < '.$section['cols'].' && '.$section['format']->get('section:valid').'; $_'.$section['name'].'_k++) { '.$section['format']->get('section:populate'));
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:next').' } ');

		$this->_sectionStart($section);
		$node->set('postprocess', true);

		if(!is_null($node->get('call:use')))
		{
			$this->_compiler->setConversion('##simplevar_'.$node->get('call:use'), $section['name']);
			$node->set('postprocess', true);
		}

		$this->_process($node);
	} // end _processItem();

	/**
	 * Processes the opt:empty-item tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _processEmptyitem(Opt_Xml_Node $node)
	{
		if(is_null($node->get('priv:section')))
		{
			throw new Opt_Instruction_Exception('opt:item should be located in opt:grid.');
		}
		$section = $node->get('priv:section');
		$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' if($_'.$section['name'].'_remain > 0 && !'.$node->get('priv:valid').') { for($_'.$section['name'].'_k = 0; $_'.$section['name'].'_k < $_'.$section['name'].'_remain; $_'.$section['name'].'_k++) { ');
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, ' } } ');

		$this->_process($node);
	} // end _processEmptyitem();

	/**
	 * Finishes the processing of the opt:grid tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _postprocessGrid(Opt_Xml_Element $node)
	{
		$section = $node->get('priv:section');
		if($node->hasAttributes())
		{
			if($node->get('priv:alternative'))
			{
				$this->_sortSectionContents($node);
			}
		}
		if(!$node->get('priv:alternative') && ! $node->hasAmbiguousDescendant('opt:body'))
		{
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
		}
	} // end _postprocessGrid();

	/**
	 * Finishes the processing of the opt:item tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _postprocessItem(Opt_Xml_Element $node)
	{
		if(!is_null($node->get('call:use')))
		{
			$section = $node->get('priv:section');
			$this->_compiler->unsetConversion('##simplevar_'.$section['name']);
		}
		// Deactivating the section.
		$this->_sectionEnd($node);
	} // end _postprocessItem();

	/**
	 * Processes the opt:gridelse tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _processElse(Opt_Xml_Element $node)
	{
		$parent = $node->getParent();
		if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:grid')
		{
			$parent->set('priv:alternative', true);

			$section = $parent->get('priv:section');
			if($parent->hasAmbiguousDescendant('opt:body'))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
			}
			else
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } } else { ');
			}
			$this->_process($node);
		}
	} // end _processElse();
} // end Opt_Instruction_Grid;