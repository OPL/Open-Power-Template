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
 * The class provides various utility functions to perform
 * some operations on OPT-XML tree and other elements.
 *
 * @package Compiler
 */
class Opt_Compiler_Utils
{
	/**
	 * This utility function helps removing the CDATA state from the
	 * specified node and their descendants. If the extra attribute is
	 * set to false, the compiler does not replace to entities the special
	 * symbols. By default, they are entitized.
	 *
	 * @static
	 * @param Opt_Xml_Node $node The starting node.
	 * @param boolean $entitize Replace the special symbols to entities?
	 */
	static public function removeCdata(Opt_Xml_Node $node, $entitize = true)
	{
		// Do not use true recursion.
		$queue = new SplQueue;
		$queue->enqueue($node);
		do
		{
			$current = $queue->dequeue();

			if($current instanceof Opt_Xml_Cdata)
			{
				if($current->get('cdata') === true)
				{
					$current->set('cdata', false);
				}
				if(!$entitize)
				{
					$current->set('noEntitize', true);
				}
			}
			// Add the children of the node to the queue for furhter processing
			foreach($current as $subnode)
			{
				$queue->enqueue($subnode);
			}
		}
		while($queue->count() > 0);
	} // end removeCdata();

	/**
	 * This utility function helps removing the COMMENT state from the
	 * specified node and their descendants. If the extra attribute is
	 * set to false, the compiler does not replace to entities the special
	 * symbols. By default, they are entitized.
	 *
	 * @static
	 * @param Opt_Xml_Node $node The starting node.
	 * @param boolean $entitize Replace the special symbols to entities?
	 */
	static public function removeComments(Opt_Xml_Node $node, $entitize = true)
	{
		// Do not use true recursion.
		$queue = new SplQueue;
		$queue->enqueue($node);
		do
		{
			$current = $queue->dequeue();

			if($current instanceof Opt_Xml_Cdata)
			{
				if($current->get('commented') === true)
				{
					$current->set('commented', false);
				}
				if(!$entitize)
				{
					$current->set('noEntitize', true);
				}
			}
			// Add the children of the node to the queue for furhter processing
			foreach($current as $subnode)
			{
				$queue->enqueue($subnode);
			}
		}
		while($queue->count() > 0);
	} // end removeComments();

	/**
	 * Performs a data format type casting. If the data format does not match
	 * the suggested one, it locates the data format class and executes the
	 * conversion action. Returns the modified code.
	 *
	 * @param Opt_Cdf_Manager $manager The CDF manager object.
	 * @param string $code The code that may be casted
	 * @param string $actual The actual data format
	 * @param string $suggested The suggested data format
	 * @return string
	 */
	static public function cast(Opt_Cdf_Manager $manager, $code, $actual, $suggested)
	{
		if($actual == $suggested)
		{
			return $code;
		}

		// Type casting goes here.
		if($actual instanceof Opt_Format_Class)
		{
			$className = get_class($actual);
			$modified = $className::cast($suggested, $code, $actual);
		}
		else
		{
			$className = $manager->getFormatClass($actual);
			$modified = $className::cast($suggested, $code);
		}		

		if($modified === null)
		{
			throw new Opt_FormatCasting_Exception($actual, $suggested);
		}

		return $modified;
	} // end cast();
} // end Opt_Compiler_Utils;