<?php
/**
 * The tests for opt:extend instruction.
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
 * @covers Opt_Instruction_Extend
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_ExtendTest extends Extra_TestFSBase
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
			array('Extend/extend_basic.txt'),
			array('Extend/extend_compound.txt'),
			array('Extend/extend_parent.txt'),
			array('Extend/extend_infinite.txt'),
			array('Extend/extend_branch_not_set.txt'),
			array('Extend/extend_branch_set.txt'),
			array('Extend/extend_branch_set_partially.txt'),
			array('Extend/extend_dynamic.txt'),
			array('Extend/extend_dynamic_not_used.txt'),
			array('Extend/extend_snippets.txt'),
			array('Extend/extend_compound_2.txt'),
			array('Extend/extend_bug61.txt'),
			array('Extend/extend_include.txt'),
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
} // end Package_Instruction_ExtendTest;