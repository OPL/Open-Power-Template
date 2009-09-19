<?php
/**
 * The tests for Opt_Xml_Scannable
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers Opt_Xml_Scannable
 */
class Package_Xml_ScannableTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The tested object.
	 * @var Opt_Xml_Scannable
	 */
	private $_obj;

	/**
	 * Sets up the object to test. We use Extra_Wrapper_ScannableTester to test
	 * the basic interface.
	 */
	public function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->_obj = new Extra_Wrapper_ScannableTester('opt:foo');
		// @codeCoverageIgnoreStop
	} // end setUp();

	/**
	 * Removes the tested object.
	 */
	public function tearDown()
	{
		// @codeCoverageIgnoreStart
		if($this->_obj !== null)
		{
			$this->_obj->dispose();
			$this->_obj = null;
		}
		// @codeCoverageIgnoreStop
	} // end tearDown();

	/**
	 * @covers Opt_Xml_Scannable::appendChild
	 * @covers Opt_Xml_Scannable::getLastChild
	 */
	public function testAppendChild()
	{
		$mock = $this->getMock('Opt_Xml_Node');
		$mock->expects($this->once())
			->method('setParent')
			->with($this->isInstanceOf('Opt_Xml_Scannable'));

		$this->_obj->appendChild($mock);
		$this->assertSame($mock, $this->_obj->getLastChild());
	} // end testAppendChild();

	/**
	 * Skip this test due to a bug in PHPUnit.
	 *
	 * @skip
	 * @covers Opt_Xml_Scannable::insertBefore
	 */
	public function testInsertBefore()
	{
		$mock = array(0 => $this->getMock('Opt_Xml_Node'));
		$mock[0]->expects($this->exactly(1))
			->method('setParent')
			->with($this->isInstanceOf('Opt_Xml_Scannable'));
		$mock[1] = $this->getMock('Opt_Xml_Node');
		$mock[1]->expects($this->exactly(1))
			->method('setParent')
			->with($this->isInstanceOf('Opt_Xml_Scannable'));
		$mock[2] = $this->getMock('Opt_Xml_Node');
		$mock[2]->expects($this->exactly(1))
			->method('setParent')
			->with($this->isInstanceOf('Opt_Xml_Scannable'));

		$this->_obj->appendChild($mock[0]);
		$this->_obj->appendChild($mock[1]);
		$this->_obj->insertBefore($mock[2], 1);
		$suggestedOutput = array(0 => $mock[0], $mock[2], $mock[1]);

		$i = 0;
		foreach($this->_obj as $obj)
		{
			$this->assertSame($suggestedOutput[$i], $obj);
			$i++;
		}
	} // end testInsertBefore();
} // end Package_Xml_ScannableTest;