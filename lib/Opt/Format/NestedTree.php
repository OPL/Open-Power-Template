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
 * The alternative tree implementation for OPT that is able to parse
 * nested lists and generate a tree from them.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class Opt_Format_NestedTree extends Opt_Format_Abstract
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

				/**
				 * This is a modification of the algorithm from the DepthTree data format.
				 * It aviods true recursion, too, but since the input data structure is
				 * a bit different, the process of entering a new branch looks a bit
				 * differently.
				 *
				 * Anyway, the algorithm still generates a sequence of control commands that
				 * cause rendering the appropriate parts of the tree layout defined in the
				 * template.
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
}
list($cmd, $_sect'.$section['name'].'_v) = $_'.$section['name'].'_cmd->dequeue();
switch($cmd)
{
';

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
		}
		return null;
	} // end _build();

} // end Opt_Format_NestedTree;