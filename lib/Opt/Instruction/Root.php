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
 * The processor for opt:root instruction and its features.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Instructions
 */
class Opt_Instruction_Root extends Opt_Instruction_Abstract
{
	/**
	 * The instruction processor name - required by the instruction API.
	 * @internal
	 * @var string
	 */
	protected $_name = 'root';

	/**
	 * Configures the instruction processor, registering the tags and
	 * attributes.
	 * @internal
	 */
	public function configure()
	{
		$this->_addInstructions(array('opt:root'));
	} // end configure();

	/**
	 * Migrates the opt:root node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function migrateNode(Opt_Xml_Node $node)
	{
		$this->_process($node);
	} // end migrateNode();

	/**
	 * Processes the opt:root node.
	 * @internal
	 * @param Opt_Xml_Node $node The recognized node.
	 */
	public function processNode(Opt_Xml_Node $node)
	{
		$parent = $node->getParent();
		if($parent->getType() != 'Opt_Xml_Root')
		{
			throw new Opt_Instruction_Exception('opt:root must be the root tag in the document.');
		}

		$params = array(
			'escaping' => array(0 => self::OPTIONAL, self::BOOL, NULL),
			'dynamic' => array(0 => self::OPTIONAL, self::BOOL, false),
		);
		$this->_extractAttributes($node, $params);

		// Compile-time inclusion support
		if($parent->get('preprocess') === null)
		{
		//	echo 'FIND LOAD...'.PHP_EOL;
			$loads = $node->getElementsByTagNameNS('opt', 'load', false);
			$templates = array();
			if(sizeof($loads) > 0)
			{
				foreach($loads as $load)
				{
					$loadParams = array(
						'template' => array(0 => self::REQUIRED, self::STRING)
					);
					$this->_extractAttributes($load, $loadParams);
					$templates[] = $loadParams['template'];
				}
			}
			if($params['dynamic'])
			{
				if(null !== ($file = $this->_compiler->inherits($this->_compiler->get('currentTemplate'))))
				{
					$templates[] = $file;
				}
			}
			// If there are some templates to load, break the current execution.
			if(sizeof($templates) > 0)
			{
			//	echo 'STORING...'.PHP_EOL;
				$parent->set('preprocess', $templates);
				return;
			}
		}

		// Escaping control support
		if($params['escaping'] !== null)
		{
			$this->_compiler->set('escaping', $params['escaping']);
		}

		$this->_process($node);
	} // end processNode();
} // end Opt_Instruction_Root;