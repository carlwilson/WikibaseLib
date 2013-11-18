<?php

namespace Wikibase\Lib\Test\Serializers;

use Wikibase\Claim;
use Wikibase\Item;
use Wikibase\Lib\Serializers\SerializationOptions;
use Wikibase\Lib\Serializers\SerializerFactory;
use Wikibase\PropertyNoValueSnak;
use Wikibase\Reference;

/**
 * @covers Wikibase\Lib\Serializers\SerializerFactory
 *
 * @since 0.4
 *
 * @group WikibaseLib
 * @group Wikibase
 * @group WikibaseSerialization
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class SerializerFactoryTest extends \MediaWikiTestCase {

	public function testConstructor() {
		new SerializerFactory();
		$this->assertTrue( true );
	}

	public function objectProvider() {
		$argLists = array();

		$argLists[] = array( new PropertyNoValueSnak( 42 ) );
		$argLists[] = array( new Reference() );
		$argLists[] = array( new Claim( new PropertyNoValueSnak( 42 ) ) );

		$argLists[] = array( Item::newEmpty(), new SerializationOptions() );

		return $argLists;
	}

	/**
	 * @dataProvider objectProvider
	 */
	public function testNewSerializerForObject( $object, $options = null ) {
		$factory = new SerializerFactory();

		$serializer = $factory->newSerializerForObject( $object, $options );

		$this->assertInstanceOf( 'Wikibase\Lib\Serializers\Serializer', $serializer );

		$serializer->getSerialized( $object );
	}

	public function serializationProvider() {
		$argLists = array();

		$snak = new PropertyNoValueSnak( 42 );

		$factory = new SerializerFactory();
		$serializer = $factory->newSerializerForObject( $snak );

		$argLists[] = array( 'Wikibase\Snak', $serializer->getSerialized( $snak ) );

		return $argLists;
	}

	/**
	 * @dataProvider serializationProvider
	 *
	 * @param string $className
	 * @param array $serialization
	 */
	public function testNewUnserializerForClass( $className, array $serialization ) {
		$factory = new SerializerFactory();

		$unserializer = $factory->newUnserializerForClass( $className );

		$this->assertInstanceOf( 'Wikibase\Lib\Serializers\Unserializer', $unserializer );

		$unserializer->newFromSerialization( $serialization );
	}

	public function newUnserializerProvider() {
		$names = array(
			'SnakUnserializer',
			'ReferenceUnserializer',
			'ClaimUnserializer',
			'ClaimsUnserializer',
			'PropertyUnserializer',
			'ItemUnserializer',
			'LabelUnserializer',
			'DescriptionUnserializer',
			'AliasUnserializer',
		);

		return array_map( function( $name ) {
			return array( $name );
		}, $names );
	}

	/**
	 * @dataProvider newUnserializerProvider
	 */
	public function testNewUnserializer( $serializerName ) {
		$factory = new SerializerFactory();
		$options = new SerializationOPtions();

		$method = "new$serializerName";
		$unserializer = $factory->$method( $options );

		$this->assertInstanceOf( 'Wikibase\Lib\Serializers\Unserializer', $unserializer );
	}

	public function newSerializerProvider() {
		$names = array(
			'SnakSerializer',
			'ReferenceSerializer',
			'ClaimSerializer',
			'ClaimsSerializer',
			'PropertySerializer',
			'ItemSerializer',
			'LabelSerializer',
			'DescriptionSerializer',
			'AliasSerializer',

			'SiteLinkSerializer',
		);

		return array_map( function( $name ) {
			return array( $name );
		}, $names );
	}

	/**
	 * @dataProvider newSerializerProvider
	 */
	public function testNewSerializer( $serializerName ) {
		$factory = new SerializerFactory();
		$options = new SerializationOPtions();

		$method = "new$serializerName";
		$unserializer = $factory->$method( $options );

		$this->assertInstanceOf( 'Wikibase\Lib\Serializers\Serializer', $unserializer );
	}

}
