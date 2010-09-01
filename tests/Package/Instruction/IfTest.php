<?php
/**
 * The tests for opt:if instruction.
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
 * @covers Opt_Instruction_If
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_IfTest extends Extra_TestFSBase
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
			array('If/if_basic.txt'),
			array('If/if_basic_failure.txt'),
			array('If/if_basic_else.txt'),
			array('If/if_basic_else_failure.txt'),
			array('If/if_basic_elseif_first.txt'),
			array('If/if_basic_elseif.txt'),
			array('If/if_basic_elseif_failure.txt'),
			array('If/if_basic_sorting.txt'),
			array('If/if_basic_sorting_2.txt'),
			array('If/if_basic_sorting_3.txt'),
			array('If/if_new_one_condition.txt'),
			array('If/if_new_more_conditions.txt'),
			array('If/if_new_no_matching.txt'),
			array('If/if_new_else.txt'),
			array('If/if_new_long.txt'),
			array('If/if_new_long_else.txt'),
			array('If/if_new_attribute.txt'),
			array('If/if_new_attribute_nocondition.txt'),
			array('If/if_new_condition_without_if.txt'),
			array('If/if_err_parent_elseif.txt'),
			array('If/if_err_parent_else.txt'),
			array('If/if_err_else_first.txt'),
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
} // end Package_Instruction_IfTest;