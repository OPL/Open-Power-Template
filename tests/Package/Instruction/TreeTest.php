<?php
/**
 * The tests for opt:tree instruction.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once('./Extra/TestFS.php');
require_once('./Extra/TestFSBase.php');

/**
 * @covers Opt_Compiler_Class
 * @covers Opt_Compiler_Format
 * @covers Opt_Compiler_Processor
 * @covers Opt_Instruction_Section_Abstract
 * @covers Opt_Instruction_Tree
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_TreeTest extends Extra_TestFSBase
{

	/**
	 * Configuration method.
	 * @param Opt_Class $tpl
	 */
	public function configure(Opt_Class $tpl)
	{
		$tpl->parser = 'Opt_Parser_Xml';
		$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:myComponent', 'Extra_Mock_Component');
		$tpl->register(Opt_Class::OPT_BLOCK, 'opt:myBlock', 'Extra_Mock_Block');
	} // end configure();

	/**
	 * Provides the list of test cases.
	 * @return array
	 */
	public static function dataProvider()
	{
		return array(0 =>
			array('Tree/tree_basic.txt'),
			array('Tree/tree_complex.txt'),
			array('Tree/tree_else.txt'),
			array('Tree/tree_body.txt'),
			array('Tree/tree_body_else_correct.txt'),
			array('Tree/tree_body_else_invalid.txt'),
			array('Tree/tree_else_nested.txt'),
			array('Tree/tree_list_loop.txt'),
			array('Tree/tree_node_loop.txt'),
			array('Tree/tree_different_depth.txt'),
			array('Tree/tree_depth_error.txt'),
		);
	} // end dataProvider();

 	/**
 	 * @dataProvider dataProvider
	 * @runInSeparateProcess
 	 */
	public function testInstructions($testCase)
	{
		return $this->_checkTest(dirname(__FILE__).'/Tests/'.$testCase);
	} // end testInstructions();
} // end Package_Instruction_TreeTest;