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
 */

/**
 * The default data format implementation for opt:tree. It generates the
 * nesting level, using the information about the depth of each element.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class Opt_Format_DepthTree extends Opt_Format_Abstract
{
	/**
	 * The list of supported hook types.
	 * @var array
	 */
	protected $_supports = array(
		'tree'
	);

	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param string $hookName The hook name
	 * @return string The output PHP code
	 */
	protected function _build($hookName)
	{
		switch($hookName)
		{
			// The code generated before the tree content.
			case 'tree:before':
				$section = $this->_getVar('section');
				$section['format']->action('section:forceItemVariables');
				$section['format']->assign('item', 'depth');

				/*
				 * Recursion is a native mechanism for tree processing, and in fact - the template syntax suggest we use it here. However, it
				 * would be too expensive (functions, other stupidities). The trees are rendered in the imperative way. Both opt:list and opt:node
				 * are split with the opt:content tag and this way we have four rendering commands: beginning of the list, its end, beginning of the node
				 * and its end. The loop does not iterate through list items, but is a simple automata: if the rendering command queue is empty,
				 * we move to the next list item, decide what it is (leaf, subnode, etc.) and create a command chain that is necessary to render it.
				 *
				 * If the chain is empty, we have to close the nodes that are still open and the main list itself. After processing this chain, we finish the job.
				 */
				return $section['format']->get('section:loopBefore').'
		'.$section['format']->get('section:reset').'
$_'.$section['name'].'_depth = -1;
$_'.$section['name'].'_initDepth = null;
$_'.$section['name'].'_over = 0;
$_'.$section['name'].'_cmd = new SplQueue;
$_'.$section['name'].'_stack = new SplStack;
while(1)
{
if($_'.$section['name'].'_cmd->count() == 0)
{
	switch($_'.$section['name'].'_over)
	{
		case 0:
			$_'.$section['name'].'_over = 1;
			break;
		case 1:
			'.$section['format']->get('section:next').'
			break;
		case 2:
			break 2;
	}
	if(!'.$section['format']->get('section:valid').')
	{
		$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
		for($k = $_'.$section['name'].'_initDepth; $k < $_'.$section['name'].'_depth; $k++)
		{
			$_'.$section['name'].'_cmd->enqueue(array(4, null));
			$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
		}
		$_'.$section['name'].'_cmd->enqueue(array(4, null));
		$_'.$section['name'].'_over = 2;
	}
	else
	{
		'.$section['format']->get('section:populate').'
		if(is_null($_'.$section['name'].'_initDepth))
		{
			$_'.$section['name'].'_initDepth = '.$section['format']->get('section:variable').';
		}
		if($_'.$section['name'].'_initDepth > '.$section['format']->get('section:variable').')
		{
			throw new Opt_Runtime_Exception(\'The tree element depth is too low: \'.'.$section['format']->get('section:variable').'.\'. It must be greater or equal to the initial depth \'.$_'.$section['name'].'_initDepth.\'.\');
		}
		if($_'.$section['name'].'_depth < '.$section['format']->get('section:variable').')
		{
			$_'.$section['name'].'_cmd->enqueue(array(1, null));
			$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
			$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
		}
		elseif($_'.$section['name'].'_depth > '.$section['format']->get('section:variable').')
		{
			$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
			for($k = '.$section['format']->get('section:variable').'; $k < $_'.$section['name'].'_depth; $k++)
			{
				$_'.$section['name'].'_cmd->enqueue(array(4, null));
				$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
			}
			$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
			$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
		}
		else
		{
			$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
			$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
			$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
		}
		$_'.$section['name'].'_depth = '.$section['format']->get('section:variable').';
	}

}
list($cmd, $_sect'.$section['name'].'_v) = $_'.$section['name'].'_cmd->dequeue();
switch($cmd)
{';
		
			// Before case 1...
			case 'tree:case1:before':
				return ' case 1: ';

			// After case 1...
			case 'tree:case1:after':
				return ' break; ';

			// Before case 2...
			case 'tree:case2:before':
				return ' case 2: ';

			// After case 2...
			case 'tree:case2:after':
				return ' break; ';

			// Before case 3...
			case 'tree:case3:before':
				return ' case 3: ';

			// After case 3...
			case 'tree:case3:after':
				return ' break; ';

			// Before case 4...
			case 'tree:case4:before':
				return ' case 4: ';

			// After case 4...
			case 'tree:case4:after':
				return ' break; ';

			// End of the tree rendering.
			case 'tree:after':
				$section = $this->_getVar('section');
				
				return '} } unset($_'.$section['name'].'_stack); unset($_'.$section['name'].'_cmd);';
		}
	} // end _build();
} // end Opt_Format_DepthTree;