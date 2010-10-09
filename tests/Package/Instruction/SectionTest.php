<?php
/**
 * The tests for opt:section instruction.
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
 * @covers Opt_Instruction_Section
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_SectionTest extends Extra_TestFSBase
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
			array('Section/section_basic.txt'),
			array('Section/section_else.txt'),
			array('Section/section_else_body.txt'),
			array('Section/section_else_multi.txt'),
			array('Section/section_order.txt'),
			array('Section/section_auto_relation.txt'),
			array('Section/section_parent.txt'),
			array('Section/section_parent_complex.txt'),
			array('Section/section_parent_complex2.txt'),
			array('Section/section_from.txt'),
			array('Section/section_from_dataformat.txt'),
			array('Section/section_datasource.txt'),
			array('Section/section_datasource_nested.txt'),
			array('Section/section_if_coop.txt'),
			array('Section/section_display.txt'),
			array('Section/section_special_var.txt'),
			array('Section/section_attribute_separators.txt'),
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
} // end Package_Instruction_SectionTest;