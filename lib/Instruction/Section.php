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
 * $Id$
 */

/**
 * The processor for the classic sections.
 * @package Instructions
 * @subpackage Sections
 */
class Opt_Instruction_Section extends Opt_Instruction_BaseSection
{
	/**
	 * The processor name - required by the instruction API
	 * @internal
	 * @var string
	 */
	protected $_name = 'section';

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:section', 'opt:sectionelse', 'opt:show', 'opt:showelse'));
		$this->_addAttributes('opt:section');
	} // end configure();

	/**
	 * Migrates the opt:section node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function _migrateSection(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end _migrateSection();

	/**
	 * Processes the opt:section tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized node.
	 */
	protected function _processSection(Opt_Xml_Element $node)
	{
		$section = $this->_sectionCreate($node);
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
		$this->_sortSectionContents($node, 'opt', 'sectionelse');

		$node->set('postprocess', true);
		$this->_process($node);
	} // end _processSection();

	/**
	 * Finishes the processing of the opt:section tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized element.
	 */
	protected function _postprocessSection(Opt_Xml_Element $node)
	{
		$section = self::getSection($node->get('priv:section'));
		if(!$node->get('priv:alternative'))
		{
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
			$this->_sectionEnd($node);
		}
	} // end _postprocessSection();

	/**
	 * Processes the opt:showelse tag.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized element.
	 */
	protected function _processShowelse(Opt_Xml_Element $node)
	{
		$parent = $node->getParent();
		if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:show')
		{
			$parent->set('priv:alternative', true);
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
			$this->_process($node);
		}
		else
		{
			throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:show');
		}
	} // end _processShowelse();

	/**
	 * Processes the opt:sectionelse element.
	 * @internal
	 * @param Opt_Xml_Element $node The recognized element.
	 */
	protected function _processSectionelse(Opt_Xml_Element $node)
	{
		$parent = $node->getParent();
		if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:section')
		{
			$parent->set('priv:alternative', true);

			$section = self::getSection($parent->get('priv:section'));
			if(!is_array($section))
			{
				throw new Opt_APINoDataReturned_Exception('Opt_Instruction_BaseSection::getSection', 'processing opt:sectionelse');
			}
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('section:endLoop').' } else { ');

			$this->_sectionEnd($parent);

			$this->_process($node);
		}
		else
		{
			throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:section');
		}
	} // end _processSectionelse();

	/**
	 * Processes the attribute form of opt:section.
	 * @internal
	 * @param Opt_Xml_Node $node The node the section is appended to
	 * @param Opt_Xml_Attribute $attr The section attribute
	 */
	protected function _processAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		$section = $this->_sectionCreate($node, $attr);
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
		$attr->set('postprocess', true);
	} // end _processAttrSection();

	/**
	 * Finishes the processing of attribute form of opt:section.
	 * @internal
	 * @param Opt_Xml_Node $node The node the section is appended to
	 * @param Opt_Xml_Attribute $attr The section attribute
	 */
	protected function _postprocessAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		$section = self::getSection($node->get('priv:section'));
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
		$this->_sectionEnd($node);
	} // end _postprocessAttrSection();
} // end Opt_Instruction_Section;