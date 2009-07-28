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
					$this->_treeTextCompile($parent, $element->data);
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