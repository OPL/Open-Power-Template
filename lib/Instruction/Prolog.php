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
	 * The XML prolog generator.
	 *
	 * @author Tomasz Jędrzejewski
	 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
	 * @license http://www.invenzzia.org/license/new-bsd New BSD License
	 */
	class Opt_Instruction_Prolog extends Opt_Compiler_Processor
	{
		/**
		 * The instruction processor name - required by the instruction API.
		 * @internal
		 * @var string
		 */
		protected $_name = 'prolog';

		/**
		 * Configures the instruction processor, registering the tags and
		 * attributes.
		 * @internal
		 */
		public function configure()
		{
			$this->_addInstructions(array('opt:prolog'));
		} // end configure();

		/**
		 * Processes the opt:prolog node.
		 * @internal
		 * @param Opt_Xml_Node $node The recognized node.
		 */
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'version' => array(0 => self::OPTIONAL, self::STRING, null),
				'encoding' => array(0 => self::OPTIONAL, self::STRING, null),
				'standalone' => array(0 => self::OPTIONAL, self::STRING, null)
			);
			$this->_extractAttributes($node, $params);
			
			$root = $node;
			while(is_object($tmp = $root->getParent()))
			{
				$root = $tmp;
			}

			// Set the default values.
			if($params['version'] === null)
			{
				$params['version'] = '\'1.0\'';
			}
			if($params['standalone'] === null)
			{
				$params['standalone'] = '\'yes\'';
			}
			if($params['encoding'] === null)
			{
				unset($params['encoding']);
			}

			$root->setProlog($prolog = new Opt_Xml_Prolog($params));
			$prolog->setDynamic('version', true);
			$prolog->setDynamic('standalone', true);
			$prolog->setDynamic('encoding', true);
		} // end processNode();
	} // end Opt_Instruction_Prolog;