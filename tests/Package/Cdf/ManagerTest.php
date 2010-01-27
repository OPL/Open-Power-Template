<?php
/**
 * The tests for Opt_Cdf_Manager
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers Opt_Cdf_Manager
 */
class Package_Cdf_ManagerTest extends Extra_Testcase
{
	/**
	 * The tested object.
	 * @var Opt_Cdf_Manager
	 */
	private $_obj;

	/**
	 * Sets up the object to test. We use Opt_Xml_Element to test
	 * the basic interface.
	 */
	public function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->_obj = new Opt_Cdf_Manager('class', array(
			'Array' => 'Opt_Format_Array',
			'Objective' => 'Opt_Format_Objective'
		));
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
			$this->_obj = null;
		}
		// @codeCoverageIgnoreStop
	} // end tearDown();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreation()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreation();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreationElementOnly()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', null, 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreationElementOnly();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreationIdOnly()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat(null, 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreationIdOnly();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFullyQualifiedPathThatMatches()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('goo', 'hoo')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathThatMatches();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFullyQualifiedPathThatDoesNotMatch()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathThatMatches();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testMultiplePathsSelection()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testMultiplePathsSelection();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFormatCaching()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);
		$formatB = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
		$this->assertSame($formatA, $formatB);
	} // end testFormatCaching();
} // end Package_Cdf_ManagerTest;