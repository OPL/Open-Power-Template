<?php
/*
 *  OPEN POWER LIBS <http://libs.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Scannable.php 19 2008-11-20 16:09:45Z zyxist $
 */

	/*
	 * Function definitions
	 */
	function Opt_Array_Push(&$array, $from, $cnt = null)
	{
		if(!isset($array[$from]))
		{
			return;
		}
		$i = 0;
		if(is_null($cnt))
		{
			$cnt = sizeof($array);
			while($cnt > 0 && $from != $i)
			{
				if(isset($array[$i]))
				{
					$cnt--;
				}
				$i++;
			}
		}
		
		$tmp = $tmp2 = null;
		while($cnt > 0)
		{
			if(isset($array[$i]))
			{
				if(is_null($tmp))
				{
					$tmp = $array[$i];
				}
				else
				{
					$tmp2 = $array[$i];
					$array[$i] = $tmp;
					$tmp = $tmp2;
				}
				$cnt--;
			}
			$i++;
		}
		if(!is_null($tmp))
		{
			$array[$i] = $tmp;
		}
	} // end Opt_Array_Push();

	/*
	 * Class definitions
	 */
	class Opt_Xml_Scannable extends Opt_Xml_Node implements Iterator
	{
		protected $_subnodes;
		protected $_i;
		protected $_size;
		protected $_copy;
		
		private $_prototypes;
		
		/*
		 * Public DOM-like API
		 */
		
		public function appendChild(Opt_Xml_Node $child)
		{
			if(!is_array($this->_subnodes))
			{
				$this->_subnodes = array();
			}
			$child->setParent($this);
			$this->_subnodes[] = $child;
		} // end appendChild();
		
		public function insertBefore(Opt_Xml_Node $newnode, $refnode = null, $appendOnError = true)
		{
			if(!is_array($this->_subnodes))
			{
				$this->_subnodes = array();
			}
			$newnode->setParent($this);
			if(is_null($refnode))
			{
				$this->_appendChild($newnode, $appendOnError);
			}
			if(is_object($refnode))
			{
				$i = 0;
				$cnt = sizeof($this->_subnodes);
				while($cnt > 0)
				{
					if(isset($this->_subnodes[$i]))
					{
						$cnt--;
						if($this->_subnodes[$i] === $refnode)
						{
							Opt_Array_Push($this->_subnodes, $i, $cnt);
							$this->_subnodes[$i] = $newnode;
						}
					}
					$i++;
				}
			}
			elseif(is_integer($refnode))
			{
				end($this->_subnodes);
				if($refnode <= key($this->_subnodes) && $refnode >= 0)
				{
					Opt_Array_Push($this->_subnodes, $refnode);
					$this->_subnodes[$refnode] = $newnode;
				}
				else
				{
					$this->_appendChild($newnode, $appendOnError);
				}
			}
		} // end insertBefore();
		
		public function removeChild($node)
		{
			if(!is_array($this->_subnodes))
			{
				return false;
			}
			if(is_object($node))
			{
				$cnt = sizeof($this->_subnodes);
				$found = 0;
				for($i = 0; $i < $cnt; $i++)
				{
					if(isset($this->_subnodes[$i]))
					{
						if($this->_subnodes[$i] === $node)
						{
							$node->setParent(null);
							unset($this->_subnodes[$i]);
							$found++;
						}
					}
				}
				return $found > 0;
			}
			elseif(is_integer($node) && isset($this->_subnodes[$node]))
			{
				$this->_subnodes[$node]->setParent(null);
				unset($this->_subnodes[$node]);
				return true;
			}
			return false;
		} // end removeChild();
		
		public function removeChildren()
		{
			unset($this->_subnodes);
			$this->_subnodes = null;
		} // end removeChildren();
		
		public function moveChildren(Opt_Xml_Scannable $node)
		{
			$this->_subnodes = $node->_subnodes;
			$this->_i = $node->_i;
			$this->_size = $node->_size;
			$this->_copy = $node->_copy;
		} // end moveChildren();
		
		public function replaceChild(Opt_Xml_Node $newnode, $refnode)
		{
			if(!is_array($this->_subnodes))
			{
				return false;
			}
			$newnode->setParent($this);
			if(is_object($refnode))
			{
				$i = 0;
				$cnt = sizeof($this->_subnodes);
				while($cnt > 0)
				{
					if(isset($this->_subnodes[$i]))
					{
						// TODO: see note about comparing" in bringToEnd()
						if($refnode == $this->_subnodes[$i])
						{
							$this->_subnodes[$i] = $newnode;
							return true;
						}
					}
					$i++;
				}
			}
			elseif(is_integer($refnode) && isset($this->_subnodes[$refnode]))
			{
				$this->_subnodes[$refnode] = $newnode;
				return true;
			}
			return false;
		} // end replaceChild();
		
		public function hasChildren()
		{
			if(!is_array($this->_subnodes))
			{
				return false;
			}
			return sizeof($this->_subnodes) > 0;
		} // end hasChildren();
		
		public function countChildren()
		{
			if(!is_array($this->_subnodes))
			{
				return 0;
			}
			return sizeof($this->_subnodes);
		} // end countChildren();
		
		public function getLastChild()
		{
			if(!is_array($this->_subnodes))
			{
				return NULL;
			}
			if(sizeof($this->_subnodes) > 0)
			{
				return end($this->_subnodes);
			}
			return NULL;
		} // end getLastChild();
		
		public function getChildren()
		{
			$cnt = sizeof($this->_subnodes);
			$result = array();
			for($i = 0; $i < $cnt; $i++)
			{
				if(is_object($this->_subnodes[$i]))
				{
					$result[] = $this->_subnodes[$i];
				}			
			}
			return $result;
		} // end getChildren();
		
		public function getElements()
		{
			$queue = array();
			foreach($this->_subnodes as $item)
			{
				if(is_object($item))
				{
					$queue[] = $item;
				}
			}
			$result = array();
			while(sizeof($queue) > 0)
			{
				$current = array_shift($queue);
				if($current instanceof Opt_Xml_Node)
				{
					$result[] = $current;
				}
				if($current instanceof Opt_Xml_Scannable)
				{
					foreach($current as $subnode)
					{
						$queue[] = $subnode;
					}
				}
			}
			return $result;
		} // end getElements();
		
		public function getElementsByTagName($name, $recursive = true)
		{
			if($recursive)
			{
				return $this->_getElementsByTagName($name, '*');
			}
			
			if(!is_array($this->_subnodes))
			{
				return array();
			}
			$result = array();
			foreach($this->_subnodes as $subnode)
			{
				if(!$subnode instanceof Opt_Xml_Element)
				{
					continue;
				}
				
				if($subnode->getName() == $name || $name == '*')
				{
					$result[] = $subnode;
				}
			}
			return $result;			
		} // end getElementsByTagName();
		
		public function getElementsByTagNameNS($namespace, $name, $recursive = true)
		{
			if($recursive)
			{
				return $this->_getElementsByTagName($name, $namespace);
			}

			if(!is_array($this->_subnodes))
			{
				return array();
			}
			$result = array();
			foreach($this->_subnodes as $subnode)
			{
				if(!$subnode instanceof Opt_Xml_Element)
				{
					continue;
				}

				if(($subnode->getName() == $name || $name == '*') && ($subnode->getNamespace() == $namespace || $namespace == '*') )
				{
					$result[] = $subnode;
				}
			}
			return $result;
		} // end getElementsByTagNameNS();
		
		public function getElementsExt($ns, $name)
		{
			if(!is_array($this->_subnodes))
			{
				return array();
			}
			// Use the queue to avoid recusive calls in this place.
			$queue = new SplQueue;
			foreach($this->_subnodes as $item)
			{
				if(is_object($item))
				{
					$queue->enqueue($item);
				}
			}
			$result = array();
			while($queue->count() > 0)
			{
				$current = $queue->dequeue();
				if($current instanceof Opt_Xml_Element)
				{
					if(is_null($ns))
					{
						if($current->getName() == $name || $name == '*')
						{
							$result[] = $current;
							continue;	// Do not visit the children of the matching node.
						}
					}
					else
					{
						if(($current->getName() == $name || $name == '*') && ($current->getNamespace() == $ns || $ns == '*') )
						{
							$result[] = $current;
							continue;	// Do not visit the children of the matching node.
						}
					}
				}
				if($current instanceof Opt_Xml_Scannable)
				{
					foreach($current as $subnode)
					{
						$queue->enqueue($subnode);
					}
				}
			}
			return $result;
		} // end getElementsExt();
		
		public function sort(Array $prototypes)
		{
			$this->_prototypes = $prototypes;

			if(!isset($prototypes['*']))
			{
				throw new Opt_APINoWildcard_Exception;
			}
			// To create a stable sort.
			$i = 0;
			foreach($this->_subnodes as &$node)
			{
				if($node instanceof Opt_Xml_Buffer)
				{
					$node->set('_ssort', $i++);
				}				
			}
			// Sort!
			usort($this->_subnodes, array($this, 'sortCmp'));
			unset($this->_prototypes);
		} // end sort();
		
		public function bringToEnd($node)
		{
			if(!is_array($this->_subnodes))
			{
				return false;			
			}
			if(is_object($node))
			{
				$i = 0;
				$cnt = sizeof($this->_subnodes);
				while($cnt > 0)
				{
					if(isset($this->_subnodes[$i]))
					{
						// Information: NEVER compare two nodes using "=="!
						if($this->_subnodes[$i] === $node)
						{
							$this->_subnodes[] = $node;
							unset($this->_subnodes[$i]);
							return true;
						}
					}
					$i++;
				}
			}
			else
			{
				if(isset($this->_subnodes[$node]))
				{
					$this->_subnodes[] = $this->_subnodes[$node];
					unset($this->_subnodes[$node]);
					return true;
				}
			}
			return false;
		} // end bringToEnd();
		
		public function getSubnodeArray()
		{
			return $this->_subnodes;
		} // end getSubnodeArray();
		
		public function clearNode()
		{
			$queue = new SplQueue;
			foreach($this->_subnodes as $subitem)
			{
				$queue->enqueue($subitem);
			}
			$this->_subnodes = NULL;
			$buffer = array();
			while($queue->count() > 0)
			{
				$item = $queue->dequeue();
				
				foreach($item as $subitem)
				{
					$queue->enqueue($subitem);
					$buffer[] = $subitem;
				}
				$item->_subnodes = NULL;
			}
			unset($buffer);
		} // end clearNode();
		
		final public function __clone()
		{
			if($this->get('__nrc') === true)
			{
				// In this state, we do not clone the subnodes, because some else node takes
				// care of it
				$this->set('__nrc', NULL);
				$this->_subnodes = NULL;
				$this->_prototypes = NULL;
				$this->_i = NULL;
				$this->_size = 0;
				$this->_copy = NULL;
			}
			else
			{
				// The main instance of cloning, it makes copies of all the subnodes.
				$queue = new SplQueue;
				foreach($this->_subnodes as $subitem)
				{
					if(is_object($subitem))
					{
						$queue->enqueue(array($subitem, $this));
					}
				}
				$this->_subnodes = NULL;
				$this->_prototypes = NULL;
				$this->_i = NULL;
				$this->_size = 0;
				$this->_copy = NULL;
				while($queue->count() > 0)
				{
					$pair = $queue->dequeue();
					$pair[0]->set('__nrc', true);
					$pair[1]->appendChild($clone = clone $pair[0]);
					
					// Only Opt_Xml_Scannable instances must go deeper.
					if($pair[0] instanceof Opt_Xml_Scannable)
					{
						foreach($pair[0] as $subitem)
						{
							if(is_object($subitem))
							{
								$queue->enqueue(array($subitem, $clone));
							}
						}
					}		
				}
			}
		} // end __clone();

		public function dispose()
		{
			// The main instance of cloning, it makes copies of all the subnodes.
			$queue = new SplQueue;
			if(is_array($this->_subnodes))
			{
				foreach($this->_subnodes as $subitem)
				{
					if(is_object($subitem))
					{
						$queue->enqueue($subitem);
					}
				}
			}
			while($queue->count() > 0)
			{
				$item = $queue->dequeue();
				if($item instanceof Opt_Xml_Scannable)
				{
					foreach($item as $subitem)
					{
						if(is_object($subitem))
						{
							$queue->enqueue($subitem);
						}
					}
				}
				$item->_dispose();
			}
			$this->_dispose();
		} // end dispose();

		protected function _dispose()
		{
			parent::_dispose();
			$this->_subnodes = NULL;
			$this->_prototypes = NULL;
			$this->_i = NULL;
			$this->_size = 0;
			$this->_copy = NULL;
		} // end _dispose();
		/*
		 * Iterator interface implementation
		 */
		
		public function rewind()
		{
			if(!is_array($this->_subnodes))
			{
				$this->_subnodes = array();
			}
			ksort($this->_subnodes);
			$this->_i = 0;
			end($this->_subnodes);
			$this->_size = key($this->_subnodes);
			
			while(!isset($this->_subnodes[$this->_i]) && $this->_i <= $this->_size)
			{
				$this->_i++;
			}
			
			$this->_copy = $this->_subnodes;
		} // end rewind();
		
		public function valid()
		{
			if($this->_i <= $this->_size)
			{
				return true;
			}
			$this->_copy = null;
			return false;
		} // end valid();
		
		public function current()
		{
			return $this->_copy[$this->_i];
		} // end current();
		
		public function next()
		{
			do
			{
				$this->_i++;
			}
			while(!isset($this->_copy[$this->_i]) && $this->_i <= $this->_size);
		} // end next();
		
		public function key()
		{
			return $this->_i;
		} // end key();
		
		/*
		 * Internal methods
		 */
		
		final public function sortCmp($node1, $node2)
		{
			$name1 = (string)$node1;
			$name2 = (string)$node2;
			if(!isset($this->_prototypes[$name1]))
			{
				$name1 = '*';
			}
			if(!isset($this->_prototypes[$name2]))
			{
				$name2 = '*';
			}
			if($this->_prototypes[$name1] == $this->_prototypes[$name2])
			{
				if($node1->get('_ssort') < $node2->get('_ssort'))
				{
					return -1;
				}
				return 1;
			}
			elseif($this->_prototypes[$name1] < $this->_prototypes[$name2])
			{
				return -1;
			}
			return +1;
		} // end sortCmp();
		
		final protected function _getElementsByTagName($name, $ns)
		{
			if(!is_array($this->_subnodes))
			{
				return array();
			}
			// Use the queue to avoid recusive calls in this place.
			$queue = new SplQueue;
			foreach($this->_subnodes as $item)
			{
				if(is_object($item))
				{
					$queue->enqueue($item);
				}
			}
			$result = array();
			while($queue->count() > 0)
			{
				$current = $queue->dequeue();
				if($current instanceof Opt_Xml_Element)
				{
					if(is_null($ns))
					{
						if($current->getName() == $name || $name == '*')
						{
							$result[] = $current;
						}
					}
					else
					{
						
						if(($current->getName() == $name || $name == '*') && ($current->getNamespace() == $ns || $ns == '*') )
						{
							$result[] = $current;
						}
					}
				}
				if($current instanceof Opt_Xml_Scannable)
				{
					foreach($current as $subnode)
					{
						$queue->enqueue($subnode);
					}
				}
			}
			return $result;
		} // end _getElementsByTagName();
		
		final protected function _appendChild(optNode $child, $appendOnError)
		{
			if($appendOnError)
			{
				if(!is_array($this->_subnodes))
				{
					return false;
				}
				$this->_subnodes[] = $child;
			}
			else
			{
				throw new Opt_APIInvalidBorders_Exception;
			}
		} // end _appendChild();
		
		protected function _testNode(Opt_Xml_Node $node)
		{
			// This method tests, whether the specified node can be a child
			// of the current note. By overwriting this method, we can change
			// this behavior.
			return true;
		} // end _testNode();
	} // end Opt_Xml_Scannable;
