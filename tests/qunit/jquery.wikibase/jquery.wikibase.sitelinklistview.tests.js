/**
 * @licence GNU GPL v2+
 * @author H. Snater < mediawiki@snater.com >
 */

( function( $, wb, QUnit ) {
	'use strict';

	/**
	 * @param {Object} [options]
	 * @return {jQuery}
	 */
	function createSitelinklistview( options ) {
		options = $.extend( {
			entityId: 'i am an entity id',
			api: 'i am an api',
			entityStore: new wb.store.EntityStore( null ),
			allowedSiteIds: ['aawiki', 'enwiki']
		}, options );

		return $( '<div/>' )
			.addClass( 'test_sitelinklistview')
			.appendTo( $( 'body' ) )
			.sitelinklistview( options );
	}

	QUnit.module( 'jquery.wikibase.sitelinklistview', QUnit.newWbEnvironment( {
		config: {
			'wbSiteDetails': {
				aawiki: {
					apiUrl: 'http://aa.wikipedia.org/w/api.php',
					name: 'Qafár af',
					pageUrl: 'http://aa.wikipedia.org/wiki/$1',
					shortName: 'Qafár af',
					languageCode: 'aa',
					id: 'aawiki',
					group: 'wikipedia'
				},
				enwiki: {
					apiUrl: 'http://en.wikipedia.org/w/api.php',
					name: 'English Wikipedia',
					pageUrl: 'http://en.wikipedia.org/wiki/$1',
					shortName: 'English',
					languageCode: 'en',
					id: 'enwiki',
					group: 'wikipedia'
				},
				dewiki: {
					apiUrl: 'http://de.wikipedia.org/w/api.php',
					name: 'Deutsche Wikipedia',
					pageUrl: 'http://de.wikipedia.org/wiki/$1',
					shortName: 'Deutsch',
					languageCode: 'de',
					id: 'dewiki',
					group: 'wikipedia'
				}
			}
		},
		teardown: function() {
			$( '.test_sitelinklistview' ).each( function() {
				var $sitelinklistview = $( this ),
					sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

				if( sitelinklistview ) {
					sitelinklistview.destroy();
				}

				$sitelinklistview.remove();
			} );
		}
	} ) );

	QUnit.test( 'Create and destroy', function( assert ) {
		var $sitelinklistview = createSitelinklistview(),
			sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

		assert.ok(
			sitelinklistview !== 'undefined',
			'Created widget'
		);

		sitelinklistview.destroy();

		assert.ok(
			$sitelinklistview.data( 'sitelinklistview' ) === undefined,
			'Destroyed widget.'
		);
	} );

	QUnit.test( 'Create and destroy with initial value', function( assert ) {
		var siteLink = new wikibase.datamodel.SiteLink( 'enwiki', 'Main Page' ),
			$sitelinklistview = createSitelinklistview( {
				value: [siteLink]
			} ),
			sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

		assert.ok(
			sitelinklistview !== 'undefined',
			'Created widget'
		);

		sitelinklistview.destroy();

		assert.ok(
			$sitelinklistview.data( 'sitelinkview' ) === undefined,
			'Destroyed widget.'
		);
	} );

	QUnit.test( 'enterNewItem()', 2, function( assert ) {
		var $sitelinklistview = createSitelinklistview(),
			sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

		$sitelinklistview
		.on( 'listviewenternewitem', function( event, $sitelinkview ) {
			assert.ok(
				true,
				'Added listview item.'
			);
		} )
		.on( 'sitelinkviewafterstartediting', function() {
			assert.ok(
				true,
				'Started sitelinkview edit mode.'
			);
		} );

		sitelinklistview.enterNewItem();
	} );

	QUnit.test( 'isFull()', function( assert ) {
		var $sitelinklistview = createSitelinklistview(),
			sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

		assert.ok(
			!sitelinklistview.isFull(),
			'Returning false.'
		);

		$sitelinklistview = createSitelinklistview( {
			value: [
				new wikibase.datamodel.SiteLink( 'aawiki', 'Main Page' ),
				new wikibase.datamodel.SiteLink( 'enwiki', 'Main Page' )
			]
		} );
		sitelinklistview = $sitelinklistview.data( 'sitelinklistview' );

		assert.ok(
			sitelinklistview.isFull(),
			'Retuning true.'
		);
	} );

}( jQuery, wikibase, QUnit ) );