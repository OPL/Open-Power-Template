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
		
	} // end load();
} // end Opt_Cdf_Loader;