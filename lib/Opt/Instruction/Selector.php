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
 * The instruction processor for selectors.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 * @subpackage Sections
 */
class Opt_Instruction_Selector extends Opt_Instruction_Section_Abstract
{
	/**
	 * The processor name required by the parent.
	 * @internal
	 * @var string
	 */
	protected $_name = 'section';
	/**
	 * The list of extra opt:selector attributes for the section manager.
	 * @internal
	 * @var array
	 */
	protected $_extraAttributes = array('test' => array(self::OPTIONAL, self::ID, 'item'));

	/**
	 * Array contains deprecated attributes.
	 * @var array
	 */
	protected $_deprecatedAttributes = array();

	/**
	 * Array contains deprecated instructions.
	 * @var array
	 */
	protected $_deprecatedInstructions = array('opt:selectorelse');

	/**
	 * Configures the instruction processor.
	 *
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:selector'));
		$this->_addAttributes('opt:selector');
		$this->_addAmbiguous(array('opt:else' => 'opt:selector'));
		if($this->_tpl->backwardCompatibility)
		{
			$this->_addAttributes($this->_deprecatedAttributes);
			$this->_addInstructions($this->_deprecatedInstructions);
		}
	} // end configure();

	/**
	 * Migrates the opt:selector node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function _migrateSelector(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end _migrateSelector();

	/**
	 * Checks if attribute is deprecated and needs migration.
	 * @param Opt_Xml_Attribute $attr Attribute to migrate
	 * @return boolean If attribute needs migration
	 */
	public function attributeNeedMigration(Opt_Xml_Attribute $attr)
	{
		$name = $attr->getXmlName();
		if(in_array($name, $this->_deprecatedAttributes))
		{
			return true;
		}
		return false;
	} // end attributeNeedMigration();

	/**
	 * Migrates the opt:selector attribute.
	 * @internal
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 * @return Opt_Xml_Attribute Migrated attribute
	 */
	public function migrateAttribute(Opt_Xml_Attribute $attr)
	{
		/*switch($attr->getName())
		{
			// null
		}*/
		return $attr;
	} // end migrateAttribute();

	/**
	 * Processes the opt:selector element using the section API.
	 * @internal
	 * @param Opt_Xml_Node $node The found element.
	 */
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
		$separator = $this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);

		// Before processing, store opt:else in some safe place.
		$results = $node->getElementsByTagNameNS('opt', 'else', false);
		if(sizeof($results) > 0)
		{
			$node->removeChild($results[0]);
		}
		if($separator !== null)
		{
			$this->_enqueue($separator);
			$node->removeChild($separator);
		}

		// Process opt:switch
		$section['format']->assign('item', $section['test']);
		$switchProcessor = $this->_compiler->processor('switch');
		$switchProcessor->attach($this);
		$switchProcessor->createSwitch($node, $section['format']->get('section:variable'), Opt_Instruction_Switch::INGORE_TOP_LEVEL_OPT_TAGS);
		$switchProcessor->detach();

		if(sizeof($results) > 0)
		{
			$node->appendChild($results[0]);
			$this->_enqueue($results[0]);
		}
		if($separator !== null)
		{
			$node->insertBefore($separator, 0);
		}

		$node->set('postprocess', true);
	} // end _processSelector();

	/**
	 * Postprocessing routine for opt:selector.
	 * @internal
	 * @param Opt_Xml_Node $node The found element.
	 */
	protected function _postprocessSelector(Opt_Xml_Node $node)
	{
		$section = self::getSection($node->get('priv:section'));
		if(!$node->get('priv:alternative'))
		{
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
			$this->_sectionEnd($node);
		}
	} // end _postprocessSelector();

	/**
	 * Processes the opt:selectorelse element.
	 * @internal
	 * @param Opt_Xml_Element $node
	 * @throws Opt_InstructionInvalidParent_Exception
	 */
	protected function _processElse(Opt_Xml_Element $node)
	{
		$parent = $node->getParent();
		if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:selector')
		{
			$parent->set('priv:alternative', true);

			$section = self::getSection($parent->get('priv:section'));
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('section:endLoop').' } else { ');

			$this->_sectionEnd($parent);

			$this->_process($node);
		}
		else
		{
			throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:section');
		}
	} // end _processSelectorelse();

	/**
	 * Processes the attribute version of opt:selector
	 * @internal
	 * @param Opt_Xml_Node $node
	 * @param Opt_Xml_Attribute $attr
	 */
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
		$separator = $this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node, Opt_Instruction_Loop_Abstract::ATTRIBUTE_FORM);

		// Before processing, store opt:separator in some warm and dry place.
	/*	if($separator !== null)
		{
			$this->_enqueue($separator);
			$node->removeChild($separator);
		}
	 */

		// Process opt:switch
		$section['format']->assign('item', isset($section['test']) ? $section['test'] : 'item');

		$switchProcessor = $this->_compiler->processor('switch');

		$switchProcessor->attach($this);
		$switchProcessor->createSwitch($node, $section['format']->get('section:variable'), Opt_Instruction_Switch::INGORE_TOP_LEVEL_OPT_TAGS);
		$switchProcessor->detach();

		// Restore separators
/*		if($separator !== null)
		{
			$node->insertBefore($separator, 0);
		}
*/
		$node->set('hidden', false);
		$attr->set('postprocess', true);
	} // end _processAttrSelector();

	/**
	 * A postprocessing routine for attributed opt:selector
	 * @internal
	 * @param Opt_Xml_Node $node
	 * @param Opt_Xml_Attribute $attr
	 */
	protected function _postprocessAttrSelector(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		$section = self::getSection($node->get('priv:section'));
		$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
		$this->_sectionEnd($node);
	} // end _postprocessAttrSelector();
} // end Opt_Instruction_Selector;
