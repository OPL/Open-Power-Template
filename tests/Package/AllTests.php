<?php
/**
 * The test suite file that configures the execution of the test cases.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once('ClassTest.php');

class Package_AllTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Configures the suite object.
	 *
	 * @return Suite
	 */
	public static function suite()
	{
		$suite = new Package_AllTests('Package');
		$suite->addTestSuite('Package_ClassTest');

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

} // end Package_AllTests;