<?php

namespace Wikibase\Test;
use Diff\Diff;
use Diff\DiffOpChange;
use Exception;
use Wikibase\EntityChange;
use Wikibase\Item;
use Wikibase\ItemChange;
use Wikibase\Entity;
use Wikibase\ItemDiff;

/**
 * Tests for the Wikibase\ItemChange class.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.3
*
 * @ingroup WikibaseLib
 * @ingroup Test
 *
 * @group Database
 * @group Wikibase
 * @group WikibaseLib
 * @group WikibaseChange
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class ItemChangeTest extends EntityChangeTest {

	/**
	 * @see ORMRowTest::getRowClass
	 * @since 0.4
	 * @return string
	 */
	protected function getRowClass() {
		return 'Wikibase\ItemChange';
	}

	public function entityProvider() {
		$entities = array_filter(
			TestChanges::getEntities(),
			function( Entity $entity ) {
				return ( $entity instanceof Item );
			}
		);

		$cases = array_map(
			function( Entity $entity ) {
				return array( $entity );
			},
			$entities
		);

		return $cases;
	}

	public function itemChangeProvider() {
		$changes = array_filter(
			TestChanges::getChanges(),
			function( EntityChange $change ) {
				return ( $change instanceof ItemChange );
			}
		);

		$cases = array_map( function( ItemChange $change ) {
			return array( $change );
		},
		$changes );

		return $cases;
	}

	/**
	 * @dataProvider changeProvider
	 *
	 * @param \Wikibase\ItemChange $change
	 */
	public function testGetSiteLinkDiff( ItemChange $change ) {
		$siteLinkDiff = $change->getSiteLinkDiff();
		$this->assertInstanceOf( 'Diff\Diff', $siteLinkDiff,
			"getSiteLinkDiff must return a Diff" );
	}

	public function changeBackwardsCompatProvider() {
		//NOTE: Disable developer warnings that may get triggered by
		//      the B/C code path.
		$this->setMwGlobals( 'wgDevelopmentWarnings', false );
		wfSuppressWarnings();

		$cases = array();

		// --------
		// We may hit a plain diff generated by old code.
		// Make sure we can deal with that.

		$diff = new Diff();

		$change = ItemChange::newFromDiff( $diff, array( 'type' => 'test' ) );
		$cases['plain-diff'] = array( $change );

		// --------
		// Bug 51363: As of commit ff65735a125e, MapDiffer may generate atomic diffs for
		// substructures even in recursive mode. Make sure we can handle them
		// if we happen to load them from the database or such.

		$diff = new ItemDiff( array(
			'links' => new DiffOpChange(
				array( 'foowiki' => 'X', 'barwiki' => 'Y' ),
				array( 'barwiki' => 'Y', 'foowiki' => 'X' )
			)
		) );

		// make sure we got the right key for sitelinks
		assert( $diff->getSiteLinkDiff() !== null );

		//NOTE: ItemChange's constructor may or may not already fix the bad diff.

		$change = ItemChange::newFromDiff( $diff, array( 'type' => 'test' ) );
		$cases['atomic-sitelink-diff'] = array( $change );

		wfRestoreWarnings();
		return $cases;
	}

	/**
	 * @dataProvider changeBackwardsCompatProvider
	 *
	 * @param \Wikibase\ItemChange $change
	 * @throws Exception
	 */
	public function testGetSiteLinkDiffBackwardsCompat( ItemChange $change ) {
		//NOTE: Disable developer warnings that may get triggered by
		//      the B/C code path.
		$this->setMwGlobals( 'wgDevelopmentWarnings', false );

		// Also suppress notices that may be triggered by wfLogWarning
		wfSuppressWarnings();
		$exception = null;

		try {
			$siteLinkDiff = $change->getSiteLinkDiff();
			$this->assertInstanceOf( 'Diff\Diff', $siteLinkDiff,
				"getSiteLinkDiff must return a Diff" );
		} catch ( Exception $ex ) {
			// PHP 5.3 doesn't have `finally`, so we use a hacky emulation
			$exception = $ex;
		}

		// this is our make-shift `finally` section.
		wfRestoreWarnings();

		if ( $exception ) {
			throw $exception;
		}
	}
}
