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
 * $Id: Class.php 155 2009-07-18 07:25:11Z zyxist $
 */

	/**
	 * This class uses XMLReader to generate the XML tree which is
	 * later converted to OPT nodes.
	 */
	class Opt_Parser_Xml implements Opt_Parser_Interface
	{
		/**
		 * The expression finding regular expression.
		 * @var String 
		 */
		private $_rExpressionTag = '/(\{([^\}]*)\})/msi';

		/**
		 * The compiler class
		 * @var Opt_Compiler_Class
		 */
		private $_compiler;

		/**
		 * Sets the compiler instance.
		 * @param Opt_Compiler_Class $compiler The compiler object
		 */
		public function setCompiler(Opt_Compiler_Class $compiler)
		{
			if(!extension_loaded('XMLReader'))
			{
				throw new Opt_NotSupported_Exception('XML parser', 'XMLReader extension is not loaded');
			}
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
			$reader = new XMLReader;
		//	$reader->setParserProperty(XMLReader::LOADDTD, false);
			$reader->xml($code);

			$root = $current = new Opt_Xml_Root;
			$firstElementMatched = false;
			$depth = 0;
			while($reader->read())
			{
				if($reader->depth < $depth)
				{
					$current = $current->getParent();
				}
				elseif($reader->depth > $depth)
				{
					$current = $optNode;
				}
				switch($reader->nodeType)
				{
					// XML elements
					case XMLReader::ELEMENT:
						$optNode = new Opt_Xml_Element($reader->name);
						// Parse element attributes, if you manage to get there
						if($reader->moveToFirstAttribute())
						{
							do
							{
								// "xmlns" special namespace must be handler somehow differently.
								if($reader->prefix == 'xmlns')
								{
									$ns = str_replace('xmlns:', '', $reader->name);
									$root->addNamespace($ns, $reader->value);

									// Let this attribute to appear, if it does not represent an OPT special
									// namespace
									if(!$this->_compiler->isNamespace($ns))
									{
										$optAttribute = new Opt_Xml_Attribute($reader->name, $reader->value);
										$optNode->addAttribute($optAttribute);
									}
								}
								else
								{
									$optAttribute = new Opt_Xml_Attribute($reader->name, $reader->value);
									$optNode->addAttribute($optAttribute);
								}
							}
							while($reader->moveToNextAttribute());
							$reader->moveToElement();
						}
						// Set "rootNode" flag
						if(!$firstElementMatched)
						{
							$optNode->set('rootNode', true);
							$firstElementMatched = true;
						}
						// Set "single" flag
						if($reader->isEmptyElement)
						{
							$optNode->set('single', true);
						}
						$current->appendChild($optNode);
						
						break;
					case XMLReader::TEXT:
						$this->_treeTextCompile($current, $reader->value);
						break;
					case XMLReader::COMMENT:
						$optNode = new Opt_Xml_Comment($reader->value);
						$current->appendChild($optNode);
						break;
				}
				$depth = $reader->depth;
			}
			return $root;
		} // end parse();

		/**
		 * Compiles the current text block between two XML tags, creating a
		 * complete Opt_Xml_Text node. It looks for the expressions in the
		 * curly brackets, extracts them and packs as separate nodes.
		 *
		 * Moreover, it replaces the entities with the corresponding characters.
		 *
		 * @internal
		 * @param Opt_Xml_Node $current The current XML node.
		 * @param String $text The text block between two tags.
		 * @param Boolean $noExpressions=false If true, do not look for the expressions.
		 * @return Opt_Xml_Node The current XML node.
		 */
		protected function _treeTextCompile($current, $text, $noExpressions = false)
		{
			if($noExpressions)
			{
				$current = $this->_treeTextAppend($current, $text);
			}

			preg_match_all($this->_rExpressionTag, $text, $result, PREG_SET_ORDER);

			$resultSize = sizeof($result);
			$offset = 0;
			for($i = 0; $i < $resultSize; $i++)
			{
				$id = strpos($text, $result[$i][0], $offset);
				if($id > $offset)
				{
					$current = $this->_treeTextAppend($current, substr($text, $offset, $id - $offset));
				}
				$offset = $id + strlen($result[$i][0]);

				$current = $this->_treeTextAppend($current, new Opt_Xml_Expression($result[$i][2]));
			}

			$i--;
			// Now the remaining content of the file
			if(strlen($text) > $offset)
			{
				$current = $this->_treeTextAppend($current, substr($text, $offset, strlen($text) - $offset));
			}
			return $current;
		} // end _treeTextCompile();

		/**
		 * An utility method that simplifies inserting the text to the XML
		 * tree. Depending on the last child type, it can create a new text
		 * node or add the text to the existing one.
		 *
		 * @internal
		 * @param Opt_Xml_Node $current The currently built XML node.
		 * @param String|Opt_Xml_Node $text The text or the expression node.
		 * @return Opt_Xml_Node The current XML node.
		 */
		protected function _treeTextAppend($current, $text)
		{
			$last = $current->getLastChild();
			if(!is_object($last) || !($last instanceof Opt_Xml_Text))
			{
				if(!is_object($text))
				{
					$node = new Opt_Xml_Text($text);
				}
				else
				{
					$node = new Opt_Xml_Text();
					$node->appendChild($text);
				}
				$current->appendChild($node);
			}
			else
			{
				if(!is_object($text))
				{
					$last->appendData($text);
				}
				else
				{
					$last->appendChild($text);
				}
			}
			return $current;
		} // end _treeTextAppend();
	} // end Opt_Parser_SimpleXml;