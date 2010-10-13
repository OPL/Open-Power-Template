<?php
/**
 * The tests for opt:content instruction.
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
 * @covers Opt_Instruction_Grid
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_GridTest extends Extra_TestFSBase
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
	 * @return Array
	 */
	public static function dataProvider()
	{
		return array(0 =>
			array('Grid/grid_basic.txt'),
			array('Grid/grid_basic2.txt'),
			array('Grid/grid_section_nested.txt'),
			array('Grid/grid_section_nested2.txt'),
			array('Grid/grid_section_nested3.txt'),
			array('Grid/grid_snippets.txt'),
			array('Grid/grid_snippets2.txt'),
			array('Grid/grid_html.txt'),
			array('Grid/grid_one_row.txt'),
			array('Grid/grid_missing_item.txt'),
			array('Grid/grid_missing_empty_item.txt'),
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
} // end Package_Instruction_GridTest;