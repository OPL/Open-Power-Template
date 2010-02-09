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
 * The loader loads the data format definitions from CDF files.
 * They resemble CSS files and their syntax is pretty similar.
 */
class Opt_Cdf_Loader
{
	/**
	 * The manager instance.
	 * @var Opt_Cdf_Manager
	 */
	private $_manager;

	/**
	 * Loaded CDF file list.
	 * @var array
	 */
	private $_loaded = array();

	/**
	 * Constructs the loader class.
	 *
	 * @param Opt_Cdf_Manager $manager The manager used to register the loaded definitions.
	 */
	public function __construct(Opt_Cdf_Manager $manager)
	{
		$this->_manager = $manager;
	} // end __construct();

	/**
	 * Loads the specified CDF file.
	 *
	 * @param string $filename The CDF file name.
	 */
	public function load($filename)
	{
		// If the file has already been loaded, skip the parsing process.
		if(in_array($filename, $this->_loaded))
		{
			return;
		}
		$this->_loaded[] = $filename;

		// Initialize the parser and lexer and parse everything.
		$lexer = new Opt_Cdf_Lexer($filename);
		$parser = new Opt_Cdf_Parser($this);

		$this->_definitions = array();

		while($lexer->yylex())
		{
			if($lexer->token != 'w')
			{
				$parser->doParse($lexer->token, $lexer->value);
			}
		}
		$parser->doParse(0, 0);

		// Now register everything in the manager
		foreach($this->_definitions as $definition)
		{
			foreach($definition[0] as $group)
			{
				$last = reset($group);
				array_shift($group);

				// Concatenate the list for the locator
				$fullyQualifiedPath = array();
				foreach($group as $item)
				{
					if($item[0] !== null && $item[1] !== null)
					{
						$fullyQualifiedPath[] = $item[0].'#'.$item[1];
					}
					elseif($item[0] != null)
					{
						$fullyQualifiedPath[] = '#'.$item[1];
					}
					else
					{
						$fullyQualifiedPath[] = $item[0].'#';
					}
				}
				// Add the format definition to the manager
				if(isset($definition[1]['data-format']))
				{
					$this->_manager->addFormat($last[0], $last[1], $definition[1]['data-format'], $fullyQualifiedPath);
				}
			}
		}
	} // end load();

	/**
	 * Adds a definition to the internal buffer from the CDF parser.
	 *
	 * @param array $definition The data format definition
	 */
	public function _addDefinition(array $definition)
	{
		$this->_definitions[] = $definition;
	} // end _addDefinition();
} // end Opt_Cdf_Loader;