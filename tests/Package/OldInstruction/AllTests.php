<?php
/**
 * The test suite file that configures the execution of the test cases for
 * old instructions in the backward compatibility mode.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

//require_once('AttributeTest.php');
//require_once('AttributesBuildTest.php');
//require_once('BlockTest.php');
//require_once('CaptureTest.php');
//require_once('ComponentTest.php');
//require_once('ContentTest.php');
//require_once('DtdTest.php');
//require_once('ExtendTest.php');
//require_once('ForTest.php');
//require_once('ForeachTest.php');
//require_once('GridTest.php');
//require_once('IfTest.php');
//require_once('IncludeTest.php');
//require_once('InsertTest.php');
//require_once('LiteralTest.php');
//require_once('OnTest.php');
//require_once('PrologTest.php');
//require_once('PutTest.php');
//require_once('RepeatTest.php');
//require_once('RootTest.php');
//require_once('SectionTest.php');
//require_once('SelectorTest.php');
//require_once('ShowTest.php');
//require_once('SingleTest.php');
//require_once('SnippetTest.php');
//require_once('TagTest.php');
//require_once('TreeTest.php');

class Package_OldInstruction_AllTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Configures the suite object.
	 *
	 * @return Suite
	 */
	public static function suite()
	{
		$suite = new Package_OldInstruction_AllTests('Package_OldInstruction');
		//$suite->addTestSuite('Package_OldInstruction_AttributeTest');
		//$suite->addTestSuite('Package_OldInstruction_AttributesBuildTest');
		//$suite->addTestSuite('Package_OldInstruction_BlockTest');
		//$suite->addTestSuite('Package_OldInstruction_CaptureTest');
		//$suite->addTestSuite('Package_OldInstruction_ComponentTest');
		//$suite->addTestSuite('Package_OldInstruction_ContentTest');
		//$suite->addTestSuite('Package_OldInstruction_DtdTest');
		//$suite->addTestSuite('Package_OldInstruction_ExtendTest');
		//$suite->addTestSuite('Package_OldInstruction_ForTest');
		//$suite->addTestSuite('Package_OldInstruction_ForeachTest');
		//$suite->addTestSuite('Package_OldInstruction_GridTest');
		//$suite->addTestSuite('Package_OldInstruction_IfTest');
		//$suite->addTestSuite('Package_OldInstruction_IncludeTest');
		//$suite->addTestSuite('Package_OldInstruction_InsertTest');
		//$suite->addTestSuite('Package_OldInstruction_LiteralTest');
		//$suite->addTestSuite('Package_OldInstruction_OnTest');
		//$suite->addTestSuite('Package_OldInstruction_PrologTest');
		//$suite->addTestSuite('Package_OldInstruction_PutTest');
		//$suite->addTestSuite('Package_OldInstruction_RepeatTest');
		//$suite->addTestSuite('Package_OldInstruction_RootTest');
		//$suite->addTestSuite('Package_OldInstruction_SectionTest');
		//$suite->addTestSuite('Package_OldInstruction_SelectorTest');
		//$suite->addTestSuite('Package_OldInstruction_ShowTest');
		//$suite->addTestSuite('Package_OldInstruction_SingleTest');
		//$suite->addTestSuite('Package_OldInstruction_SnippetTest');
		//$suite->addTestSuite('Package_OldInstruction_TagTest');
		//$suite->addTestSuite('Package_OldInstruction_TreeTest');

		return $suite;
	} // end suite();

	/**
	 * Configures the OPL autoloader and installs the libraries.
	 */
	protected function setUp()
	{
		/* currently null */
	} // end setUp();

	/**
	 * Shuts down the test procedure.
	 */
	protected function tearDown()
	{
		/* currently null */
	} // end tearDown();

} // end Package_OldInstruction_AllTests;