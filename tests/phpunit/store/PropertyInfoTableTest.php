<?php

namespace Wikibase\Test;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\PropertyInfoTable;
use Wikibase\Settings;

/**
 * @covers Wikibase\PropertyInfoTable
 *
 * @group Wikibase
 * @group WikibaseLib
 * @group WikibaseStore
 * @group WikibasePropertyInfo
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class PropertyInfoTableTest extends \MediaWikiTestCase {

	/**
	 * @var PropertyInfoStoreTestHelper
	 */
	public $helper;

	public function __construct( $name = null, $data = array(), $dataName = '' ) {
		parent::__construct( $name, $data, $dataName );

		$this->helper = new PropertyInfoStoreTestHelper( $this, array( $this, 'newPropertyInfoTable' ) );
	}

	public function setUp() {
		parent::setUp();

		if ( !defined( 'WB_VERSION' ) ) {
			$this->markTestSkipped( "Skipping because WikibaseClient doesn't have a local wb_property_info table." );
		}

		if ( !Settings::get( 'usePropertyInfoTable' ) ) {
			$this->markTestSkipped( "Skipping because wb_property_info isn't configured." );
		}

		$this->tablesUsed[] = 'wb_property_info';
	}

	public function newPropertyInfoTable() {
		return new PropertyInfoTable( false );
	}

	public function provideSetPropertyInfo() {
		return $this->helper->provideSetPropertyInfo();
	}

	/**
	 * @dataProvider provideSetPropertyInfo
	 */
	public function testSetPropertyInfo( PropertyId $id, array $info, $expectedException ) {
		$this->helper->testSetPropertyInfo( $id, $info, $expectedException );
	}

	public function testGetPropertyInfo() {
		$this->helper->testGetPropertyInfo();
	}

	public function testGetAllPropertyInfo() {
		$this->helper->testGetAllPropertyInfo();
	}

	public function testRemovePropertyInfo() {
		$this->helper->testRemovePropertyInfo();
	}

	public function testPropertyInfoPersistance() {
		$this->helper->testPropertyInfoPersistance();
	}

}
