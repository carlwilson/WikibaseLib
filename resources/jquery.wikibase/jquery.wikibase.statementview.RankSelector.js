/**
 * @licence GNU GPL v2+
 * @author H. Snater < mediawiki@snater.com >
 */
( function( mw, wb, $ ) {
	'use strict';

	var PARENT = $.Widget;

	/**
	 * Selector for choosing a statement rank.
	 * @since 0.5
	 *
	 * @option [rank] {boolean} The rank that shall be selected.
	 *         Default: wikibase.Statement.RANK.NORMAL
	 *
	 * @option [isRTL] {boolean} Defines whether the widget is displayed in right-to-left context.
	 *         If not specified, the context is detected by checking whether the 'rtl' css class is
	 *         set on the HTML body element.
	 *         Default: undefined
	 *
	 * @event afterchange Triggered after the snak type got changed
	 *        (1) {jQuery.Event}
	 */
	$.wikibase.statementview.RankSelector = wb.utilities.inherit( PARENT, {
		widgetName: 'wikibase-rankselector',
		widgetBaseClass: 'wb-rankselector',

		/**
		 * @type {Object}
		 */
		options: {
			rank: wb.Statement.RANK.NORMAL,
			isRtl: undefined
		},

		/**
		 * The node of the menu to select the rank from.
		 * @type {jQuery}
		 */
		$menu: null,

		/**
		 * Icon node.
		 * @type {jQuery}
		 */
		$icon: null,

		/**
		 * @see jQuery.Widget._create
		 */
		_create: function() {
			var self = this;

			this.$menu = this._buildMenu().appendTo( 'body' ).hide();

			this.element
			.addClass( this.widgetBaseClass )
			.on( 'mouseover.' + this.widgetName, function( event ) {
				if( !self.isDisabled() ) {
					self.element.addClass( 'ui-state-hover' );
				}
			} )
			.on( 'mouseout.' + this.widgetName, function( event ) {
				if( !self.isDisabled() ) {
					self.element.removeClass( 'ui-state-hover' );
				}
			} )
			.on( 'click.' + this.widgetName, function( event ) {
				if( self.isDisabled() || self.$menu.is( ':visible' ) ) {
					self.$menu.hide();
					return;
				}

				self.$menu.show();
				self.repositionMenu();

				self.element.addClass( 'ui-state-active' );

				// Close the menu when clicking, regardless of whether the click is performed on the
				// menu itself or outside of it:
				var degrade = function( event ) {
					if ( event.target !== self.element.get( 0 ) ) {
						self.$menu.hide();
						self.element.removeClass( 'ui-state-active' );
					}
					self._unbindGlobalEventListeners();
				};

				$( document ).on( 'mouseup.' + self.widgetName, degrade  );
				$( window ).on( 'resize.' + self.widgetName, degrade );
			} );

			this.$icon = mw.template( 'wb-rankselector', '' ).appendTo( this.element );

			self.$menu.on( 'click.' + this.widgetName, function( event ) {
				var $li = $( event.target ).closest( 'li' ),
					rank = $li.data( self.widgetName + '-menuitem-rank' );

				if( rank !== undefined ) {
					self.rank( rank );
				}
			} );

			this._setRank( this.options.rank );
		},

		/**
		 * @see jQuery.Widget.destroy
		 */
		destroy: function() {
			this.$menu.data( 'menu' ).destroy();
			this.$menu.remove();
			this.$icon.remove();

			this.element.removeClass( 'ui-state-default ui-state-hover ' + this.widgetBaseClass );

			this._unbindGlobalEventListeners();

			PARENT.prototype.destroy.call( this );
		},

		/**
		 * @see jQuery.Widget._setOption
		 * @triggers afterchange
		 */
		_setOption: function( key, value ) {
			PARENT.prototype._setOption.apply( this, arguments );
			if( key === 'rank' ) {
				this._setRank( value );
				this._trigger( 'afterchange' );
			}
		},

		/**
		 * Removes all global event listeners generated by the rank selector.
		 */
		_unbindGlobalEventListeners: function() {
			$( document ).add( $( window ) ).off( '.' + this.widgetName );
		},

		/**
		 * Generates the menu the rank may be chosen from.
		 *
		 * @return {jQuery}
		 */
		_buildMenu: function() {
			var self = this,
				$menu = $( '<ul/>' ).addClass( this.widgetBaseClass + '-menu' );

			$.each( wikibase.Statement.RANK, function( rankId, i ) {
				rankId = rankId.toLowerCase();

				$menu.append(
					$( '<li/>' )
					.addClass( self.widgetBaseClass + '-menuitem-' + rankId )
					.data( self.widgetName + '-menuitem-rank', i )
					.append(
						$( '<a/>' )
						.text( mw.msg( 'wikibase-statementview-rankselector-rank-' + rankId ) )
						.on( 'click.' + self.widgetName, function( event ) {
							event.preventDefault();
						} )
					)
				);
			} );

			return $menu.menu();
		},

		/**
		 * Returns a rank's serialized string.
		 * @see wikibase.Statement.RANK
		 *
		 * @param {number} rank
		 * @return {*}
		 */
		_getRankString: function( rank ) {
			var rankString = null;

			$.each( wikibase.Statement.RANK, function( rankId, i ) {
				if( rank === i ) {
					rankString = rankId.toLowerCase();
					return false;
				}
			} );

			return rankString;
		},

		/**
		 * Sets the rank if a rank is specified or gets the current rank if parameter is omitted.
		 * @since 0.5
		 *
		 * @param {number} [rank]
		 * @return {number|undefined}
		 *
		 * @triggers afterchange
		 */
		rank: function( rank ) {
			if( rank === undefined ) {
				var $activeItem = this.$menu.children( '.ui-state-active' );
				return ( $activeItem.length )
					? $activeItem.data( this.widgetName + '-menuitem-rank' )
					: null;
			}

			this._setRank( rank );

			this._trigger( 'afterchange' );
		},

		/**
		 * Sets the rank activating the menu item representing the specified rank.
		 *
		 * @param {number} rank
		 */
		_setRank: function( rank ) {
			if( rank === this.rank() ) {
				return;
			}

			this.$menu.children().removeClass( 'ui-state-active' );
			this.$menu
				.children( '.' + this.widgetBaseClass + '-menuitem-' + this._getRankString( rank ) )
				.addClass( 'ui-state-active' );

			this._updateIcon();
		},

		/**
		 * Updates the rank icon to reflect the rank currently set.
		 */
		_updateIcon: function() {
			var self = this;

			$.each( wikibase.Statement.RANK, function( rankId, i ) {
				self.$icon.removeClass( 'wb-rankselector-' + rankId.toLowerCase() );
			} );

			this.$icon.addClass( 'wb-rankselector-' + this._getRankString( this.rank() ) );
		},

		/**
		 * Positions the menu.
		 * @since 0.5
		 */
		repositionMenu: function() {
			var isRtl = ( this.options.isRTL )
				? this.options.isRTL
				: $( 'body' ).hasClass( 'rtl' );

			this.$menu.position( {
				of: this.$icon,
				my: ( isRtl ? 'right' : 'left' ) + ' top',
				at: ( isRtl ? 'right' : 'left' ) + ' bottom',
				offset: '0 1',
				collision: 'none'
			} );
		},

		/**
		 * @see jQuery.Widget.disable
		 * @since 0.5
		 */
		disable: function() {
			this.$menu.hide();
			this.element.removeClass( 'ui-state-active ui-state-hover' );
			this.element.addClass( 'ui-state-disabled' );
			return PARENT.prototype.disable.call( this );
		},

		/**
		 * @see jQuery.Widget.enable
		 * @since 0.5
		 */
		enable: function() {
			this.element.removeClass( 'ui-state-disabled' );
			return PARENT.prototype.enable.call( this );
		},

		/**
		 * Returns whether the widget is currently disabled.
		 * @return 0.5
		 */
		isDisabled: function() {
			return this.element.hasClass( 'ui-state-disabled' );
		}

	} );

}( mediaWiki, wikibase, jQuery ) );
