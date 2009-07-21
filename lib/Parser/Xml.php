<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Class.php 155 2009-07-18 07:25:11Z zyxist $
 */

	/**
	 * This class uses DOM XML to generate the XML tree which is
	 * later converted to OPT nodes.
	 */
	class Opt_Parser_Xml implements Opt_Parser_Interface
	{
		/**
		 * The compiler class
		 * 
		 * @var Opt_Compiler_Class
		 */
		private $_compiler;

		/**
		 * Sets the compiler instance.
		 *
		 * @param Opt_Compiler_Class $compiler The compiler object
		 */
		public function setCompiler(Opt_Compiler_Class $compiler)
		{
			$this->_compiler = $compiler;
		} // end setCompiler();

		/**
		 * Parses the input code and returns the OPT XML tree.
		 *
		 * @param String $filename The file name (for debug purposes)
		 * @param String &$code The code to parse
		 * @return Opt_Xml_Root
		 */
		public function parse($filename, &$code)
		{
			$dom = new DOMDocument;
			$dom->strictErrorChecking = false;
			$dom->loadXml($code);

			// Create the OPT XML root node
			$root = $current = new Opt_Xml_Root;

			if(!$dom->hasChildNodes())
			{
				return $root;
			}

			$queue = new SplQueue;
			$queue->enqueue(array($root, $dom->childNodes->item(0)));

			while($queue->count() > 0)
			{
				list($parent, $element) = $queue->dequeue();

				// Parse the element
				if($element instanceof DOMElement)
				{
					$optNode = new Opt_Xml_Element($element->tagName);
					if(strlen($element->prefix) > 0)
					{
						$optNode->setNamespace($element->prefix);
					}
					if($element->hasAttributes())
					{
						foreach($element->attributes as $attribute)
						{
							$optAttribute = new Opt_Xml_Attribute($attribute->name, $attribute->value);
							if(strlen($attribute->prefix) > 0)
							{
								$optAttribute->setNamespace($attribute->prefix);
							}
							$optNode->addAttribute($optAttribute);
						}
					}
					$parent->appendChild($optNode);
				}
				elseif($element instanceof DOMText)
				{
					$optNode = new Opt_Xml_Text($element->data);
					$parent->appendChild($optNode);
				}
				elseif($element instanceof DOMComment)
				{
					$optNode = new Opt_Xml_Comment($element->data);
					$parent->appendChild($optNode);
				}

				// Add its children
				if($element->hasChildNodes())
				{
					foreach($element->childNodes as $node)
					{
						$queue->enqueue(array(0 => $optNode, $node));
					}
				}
			}
			return $root;
		} // end parse();
	} // end Opt_Parser_SimpleXml;