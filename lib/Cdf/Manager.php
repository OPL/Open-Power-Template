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
 * $Id: Class.php 269 2009-11-27 10:59:46Z zyxist $
 */

/**
 * A class for managing the CDF documents both by the programmer
 * and the compiler.
 */
class Opt_Cdf_Manager
{
	/**
	 * A search area
	 * @var array
	 */
	private $_information;

	/**
	 * The list of resolved data formats.
	 * @var array
	 */
	private $_resolved;

	/**
	 * Creates a new CDF manager instance.
	 *
	 * @param array $formatList The list of data formats from the public API.
	 */
	public function __construct(array $formatList)
	{
		
	} // end __construct();

	/**
	 * Sets the new default locator.
	 * 
	 * @param Opt_Cdf_Locator_Interface $locator The locator interface
	 */
	public function setLocator(Opt_Cdf_Locator_Interface $locator)
	{

	} // end setLocator();

	/**
	 * Returns the data format for the specified element type. If the
	 * data format for the element is already cached, it returns the
	 * existing object. Otherwise, a new one is created.
	 *
	 * @param <type> $elementType
	 * @param <type> $id
	 * @param <type> $type
	 * @param <type> $locator
	 */
	public function getFormat($elementType, $id, $type, $locator = null)
	{	
		if($locator === null)
		{
			$locator = $this->_locator;
		}

		$code = $elementType.':'.$id;

		// Maybe we have already solved this element?
		if(isset($this->_resolved[$code]))
		{
			if(isset($this->_resolved[$code][$type]))
			{
				return $this->_resolved[$code][$type];
			}
			elseif(isset($this->_resolved[$code]['generic']))
			{
				return $this->_resolved[$code]['generic'];
			}
		}

		$checkIn = array();
		// Determine, what elements to scan and in what order.
		if($elementType !== null && $id !== null)
		{
			$checkIn[] = $elementType.'#'.$id;
		}
		if($id !== null)
		{
			$checkIn[] = '#'.$id;
		}
		if($elementType !== null)
		{
			$checkIn[] = $elementType.'#';
		}

		$location = $locator->getElementLocation($elementType, $id);

		// Now look for the data format definition and process it.
		$match = null;
		foreach($checkIn as $key)
		{
			if(!isset($this->_information[$key]))
			{
				continue;
			}

			// Check each matching definition for the element against
			// the obtained location.
			foreach($this->_information[$key] as $definition)
			{
				$i = 0;
				// The path must match the element location
				// in order to select this definition
				foreach($definition['path'] as $pathItem)
				{
					if($location[$i] != $pathItem)
					{
						break 2;
					}
					$i++;
				}
				$match = $definition;
				break 2;
			}
		}
		if($match === null)
		{
			throw new Opt_NoMatchingFormat_Exception(reset($checkIn));
		}

		return $this->_resolved[$code] = $this->_createFormat(reset($checkIn), $match['format']);
	} // end getFormat();

	/**
	 * Registers a new format in the CDF manager for the specified item.
	 *
	 * @param string $elementType The type of element we want to have
	 * @param string $id The element identifier
	 * @param string $type The type of registered data format
	 * @param string $format The format itself
	 * @param array $fullyQualifiedPath The fully qualified path to the element
	 */
	public function addFormat($elementType, $id, $type, $format, array $fullyQualifiedPath)
	{
		$row = array(
			'type' => $type,
			'format' => $format,
			'path' => $fullyQualifiedPath
		);

		$insertTo = array();
		if($elementType !== null && $id !== null)
		{
			$insertTo[] = $elementType.'#'.$id;
		}
		if($elementType !== null)
		{
			$insertTo[] = $elementType.'#';
		}
		if($id !== null)
		{
			$insertTo[] = '#'.$id;
		}
		foreach($insertTo as $key)
		{
			if(!isset($this->_information[$id]))
			{
				$this->_information[$id] = new SplPriorityQueue;
			}
			$this->_information[$id]->insert(&$row, sizeof($fullyQualifiedPath));
		}
	} // end addFormat();

	/**
	 * Creates a format object for the specified description string.
	 *
	 * @param String $key The element key.
	 * @param String $hc The description string.
	 * @return Opt_Compiler_Format The newly created format object.
	 */
	protected function _createFormat($key, $hc)
	{
		// Decorate the objects, if necessary
		$expanded = explode('/', $hc);
		$obj = null;
		foreach($expanded as $class)
		{
			if(!isset($this->_formats[$class]))
			{
				throw new Opt_FormatNotFound_Exception($key, $class);
			}
			$hcName = $this->_formats[$class];
			if($obj !== null)
			{
				$obj->decorate($obj2 = new $hcName($this->_tpl, $this));
				$obj = $obj2;
			}
			else
			{
				$top = $obj = new $hcName($this->_tpl, $this, $hc);
			}
		}
		return $top;
	} // end _createFormat();
} // end Opt_Cdf_Manager;